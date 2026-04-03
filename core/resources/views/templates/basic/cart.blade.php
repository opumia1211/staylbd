@php
    $carts = $carts ?? [];
    if (!is_countable($carts)) {
        $carts = is_array($carts) ? $carts : (is_object($carts) ? array_values((array) $carts) : []);
    }
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'partials.cart_page_content')
@endsection

@push('script')
    <script>
        (function initCartPage() {
            if (typeof jQuery === 'undefined') { setTimeout(initCartPage, 25); return; }
            (function($) {
            "use strict";
            var removeableItem = null, modal = $('#removeCartModal');
            $(document).on('click', '.cart-decrease', function() {
                var row = $(this).closest("tr").length ? $(this).closest("tr") : $(this).closest(".cart-row-mobile");
                var inp = row.find('input[name="quantity"]');
                var q = parseInt(inp.val(), 10) || 1;
                if (q > 1) { inp.val(q - 1); syncQtyReadout(row); CartCalculation(row); } else { inp.val(1); syncQtyReadout(row); if (typeof notify === 'function') notify('error', 'Minimum quantity is 1.'); }
            });
            $(document).on('click', '.cart-increase', function() {
                var row = $(this).closest("tr").length ? $(this).closest("tr") : $(this).closest(".cart-row-mobile");
                var inp = row.find('input[name="quantity"]');
                inp.val(parseInt(inp.val(), 10) + 1);
                syncQtyReadout(row);
                CartCalculation(row);
            });
            function syncQtyReadout(row) {
                var v = row.find('input[name="quantity"]').val();
                row.find('.cart-qty-readout').text(v);
            }
            $(document).on('change', 'input[name="quantity"]', function() {
                var row = $(this).closest("tr").length ? $(this).closest("tr") : $(this).closest(".cart-row-mobile");
                var q = parseInt($(this).val(), 10);
                if (q < 1) { $(this).val(1); q = 1; }
                syncQtyReadout(row);
                CartCalculation(row);
            });
            $(document).on('click', '.remove-btn', function() {
                removeableItem = $(this).closest("tr").length ? $(this).closest("tr") : $(this).closest(".cart-row-mobile");
                modal.modal('show');
            });
            $(document).on('click', '.remove-product', function() {
                var row = removeableItem;
                var product_id = row.find('.productName').data('product_id'), variant_id = row.find('.productName').data('variant_id') || null;
                var variant_details = row.attr('data-variant-details') || row.data('variant-details') || '';
                var data = { product_id: product_id }; if (variant_id) data.variant_id = variant_id; if (variant_details) data.variant_details = variant_details;
                $('.coupon-show, .total-show').addClass('d-none'); $('.coupon').val('');
                $.ajax({ method: "POST", headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }, url: "{{ route('cart.list.remove') }}", data: data, success: function(response) {
                    if (response.success) { removeableItem.remove(); subTotal(); updateCartItemCount(); $.get("{{ route('cart.list.count') }}", function(r) { var c = (r && r.count != null) ? r.count : r; $('.show-cart-count').text(c); }); if (typeof notify === 'function') notify('success', response.success); } else if (typeof notify === 'function') notify('error', response.error);
                }});
                modal.modal('hide');
            });
            $(document).on('click', '.cart-move-wishlist', function() {
                var btn = $(this);
                var product_id = btn.data('product_id');
                if (!product_id) return;
                $.post("{{ route('wish.list.add') }}", { product_id: product_id, _token: "{{ csrf_token() }}" }).done(function(r) {
                    if (r && r.success) {
                        if (typeof notify === 'function') notify('success', r.success);
                        if (typeof getWishlistCount === 'function') getWishlistCount();
                        var row = btn.closest('tr').length ? btn.closest('tr') : btn.closest('.cart-row-mobile');
                        var variant_id = row.find('.productName').data('variant_id') || null;
                        var variant_details = row.attr('data-variant-details') || '';
                        $.post("{{ route('cart.list.remove') }}", { product_id: product_id, variant_id: variant_id || '', variant_details: variant_details, _token: "{{ csrf_token() }}" }).done(function(res) {
                            if (res && res.success) { row.fadeOut(280, function() { $(this).remove(); subTotal(); updateCartItemCount(); toggleRemoveSelected(); if (typeof getCartCount === 'function') getCartCount(); }); }
                        });
                    } else if (typeof notify === 'function') notify('error', (r && r.error) || '{{ __("Could not add to wishlist.") }}');
                });
            });
            function subTotal() {
                var subtotal = 0;
                $('.cart-row, .cart-row-mobile').each(function() {
                    var $row = $(this);
                    var chk = $row.find('input.cart-select-item:checked');
                    if (chk.length) {
                        var t = $row.find('.subtotal').text().split("{{ $general->cur_sym }}");
                        if (t[1]) subtotal += parseFloat(t[1]);
                    }
                });
                $('.subtotal-price').text("{{ $general->cur_sym }}" + subtotal.toFixed(2));
                $('.total-price').text("{{ $general->cur_sym }}" + subtotal.toFixed(2));
            }
            function toggleRemoveSelected() {
                var total = $('.cart-select-item').length, checked = $('.cart-select-item:checked').length;
                $('.cart-remove-selected').toggleClass('d-none', !checked);
            }
            function syncSelectAllStates() {
                var total = $('.cart-select-item').length, checked = $('.cart-select-item:checked').length;
                var allChecked = total > 0 && total === checked;
                $('#cartSelectAll').prop('checked', allChecked);
                $('#cartSelectAllMobile, .cart-select-all-mobile').prop('checked', allChecked);
            }
            $(document).on('change', '.cart-select-item', function() {
                subTotal();
                syncSelectAllStates();
                toggleRemoveSelected();
            });
            $(document).on('change', '#cartSelectAll, #cartSelectAllMobile, .cart-select-all-mobile', function() {
                var checked = $(this).prop('checked');
                $('.cart-select-item').prop('checked', checked);
                subTotal();
                syncSelectAllStates();
                toggleRemoveSelected();
            });
            $(document).on('click', '.cart-remove-selected', function() {
                var rows = $('.cart-page__table .cart-row:has(input.cart-select-item:checked), .cart-row-mobile:has(input.cart-select-item:checked)');
                if (!rows.length) return;
                var btn = $(this);
                btn.prop('disabled', true);
                var done = 0, total = rows.length;
                function removeNext() {
                    if (done >= total) {
                        btn.prop('disabled', false);
                        updateCartItemCount();
                        if (typeof getCartCount === 'function') getCartCount();
                        return;
                    }
                    var row = rows.eq(done);
                    var product_id = row.find('.productName').data('product_id'), variant_id = row.find('.productName').data('variant_id') || null;
                    var variant_details = row.attr('data-variant-details') || row.data('variant-details') || '';
                    var data = { product_id: product_id, _token: "{{ csrf_token() }}" };
                    if (variant_id) data.variant_id = variant_id;
                    if (variant_details) data.variant_details = variant_details;
                    $.post("{{ route('cart.list.remove') }}", data).done(function(r) {
                        if (r && r.success) { row.fadeOut(280, function() { $(this).remove(); subTotal(); updateCartItemCount(); toggleRemoveSelected(); }); }
                    }).always(function() { done++; removeNext(); });
                }
                removeNext();
            });
            $('#checkoutSelectionForm').on('submit', function(e) {
                var checked = $('.cart-page__table input.cart-select-item:checked[name="cart_ids[]"], .cart-row-mobile input.cart-select-item:checked[name="cart_ids[]"]');
                if (!checked.length) {
                    e.preventDefault();
                    if (typeof notify === 'function') notify('error', '{{ __("Please select at least one item to checkout.") }}');
                    return false;
                }
                var container = $('#checkout-cart-ids-container');
                container.empty();
                checked.each(function() { container.append($('<input type="hidden" name="cart_ids[]">').val($(this).val())); });
            });
            function updateCartItemCount() {
                var rows = $('.cart-page__tbody tr.cart-row').length + $('.cart-row-mobile').length;
                $('.cart-item-count').text(rows + (rows === 1 ? ' {{ __("item") }}' : ' {{ __("items") }}'));
            }
            function CartCalculation(currentRow) {
                var product_id = currentRow.find('.productName').data('product_id'), variant_id = currentRow.find('.productName').data('variant_id') || null;
                var variant_details = currentRow.attr('data-variant-details') || currentRow.data('variant-details') || '';
                var quantity = currentRow.find('input[name="quantity"]').val();
                var priceText = currentRow.find('.price').text();
                var price = parseFloat(priceText.split("{{ $general->cur_sym }}")[1] || 0);
                var totalPrice = quantity * price;
                currentRow.find('.subtotal').text("{{ $general->cur_sym }}" + totalPrice.toFixed(2));
                syncQtyReadout(currentRow);
                $('.coupon-show, .total-show').addClass('d-none'); $('.coupon').val('');
                subTotal();
                var data = { product_id: product_id, quantity: quantity }; if (variant_id) data.variant_id = variant_id; if (variant_details) data.variant_details = variant_details;
                $.ajax({ headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }, method: "POST", url: "{{ route('cart.list.update') }}", data: data, success: function(response) { if (typeof notify === 'function') notify(response.success ? 'success' : 'error', response.success || response.error); }});
            }
            $('.coupon-apply').on('click', function(e) {
                e.preventDefault();
                var coupon = $('.coupon').val();
                $.ajax({ method: "POST", headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }, url: "{{ route('cart.list.apply.coupon') }}", data: { coupon: coupon }, success: function(response) {
                    if (response.success) { if (typeof notify === 'function') notify('success', response.success); $('.coupon-show, .total-show').removeClass('d-none'); $('.discount-price').text("-{{ $general->cur_sym }}" + parseFloat(response.discount).toFixed(2)); $('.subtotal-price').text("{{ $general->cur_sym }}" + parseFloat(response.subtotal).toFixed(2)); $('.total-price').text("{{ $general->cur_sym }}" + parseFloat(response.totalAmount).toFixed(2)); } else if (typeof notify === 'function') notify('error', response.error);
                }});
            });
            subTotal();
            toggleRemoveSelected();
            $.get("{{ route('cart.list.count') }}", function(r) { var c = (r && r.count != null) ? r.count : r; $('.show-cart-count').text(c); });
            function updateOfferTimerBars() {
                document.querySelectorAll('.offer-timer-bar[data-end-ts]').forEach(function(bar) {
                    var endTs = parseInt(bar.getAttribute('data-end-ts'), 10);
                    var d = endTs - Date.now();
                    var wrap = bar.querySelector('.offer-timer-bar__countdown');
                    if (!wrap) return;
                    var hEl = wrap.querySelector('.countdown-hours'), mEl = wrap.querySelector('.countdown-mins'), sEl = wrap.querySelector('.countdown-secs');
                    if (d <= 0) { if (hEl) hEl.textContent = '00'; if (mEl) mEl.textContent = '00'; if (sEl) sEl.textContent = '00'; return; }
                    var h = Math.floor(d / 3600000), m = Math.floor((d % 3600000) / 60000), s = Math.floor((d % 60000) / 1000);
                    if (hEl) hEl.textContent = ('0' + h).slice(-2); if (mEl) mEl.textContent = ('0' + m).slice(-2); if (sEl) sEl.textContent = ('0' + s).slice(-2);
                });
            }
            updateOfferTimerBars();
            setInterval(updateOfferTimerBars, 1000);
            })(jQuery);
        })();
    </script>
@endpush
