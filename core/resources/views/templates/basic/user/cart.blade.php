@php
    $carts = $carts ?? [];
    if (!is_countable($carts)) {
        $carts = is_array($carts) ? $carts : (is_object($carts) ? array_values((array) $carts) : []);
    }
@endphp
@extends($activeTemplate . 'layouts.master')
@section('dashboard_page_title')
    @php
        $cartHeaderActions = '<a href="' . e(route('products')) . '" class="btn btn-sm btn-outline-primary">' . view($activeTemplate . 'partials.icon', ['name' => 'shopping-bag', 'class' => 'me-1'])->render() . e(__('Continue Shopping')) . '</a>';
    @endphp
    @include($activeTemplate . 'partials.dashboard_page_header', [
        'title' => __('My Cart'),
        'subtitle' => __('Review your items and proceed to checkout'),
        'actions' => $cartHeaderActions,
    ])
@endsection
@section('content')
    @include($activeTemplate . 'partials.cart_page_content', ['cartPageContext' => 'user_dashboard'])
@endsection

@push('script')
<script>
(function initUserCart() {
    if (typeof jQuery === 'undefined') { setTimeout(initUserCart, 25); return; }
    (function($) {
        "use strict";
        /* Qty +/- and Totals are handled in master layout so they work for both full page and AJAX-loaded cart. */
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
                        if (res && res.success) {
                            row.fadeOut(280, function() { $(this).remove(); if (typeof window.cartSubTotal === 'function') window.cartSubTotal(); if (typeof getCartCount === 'function') getCartCount(); });
                            if (typeof notify === 'function') notify('success', res.success);
                        }
                    });
                } else if (typeof notify === 'function') notify('error', (r && r.error) || '{{ __("Could not add to wishlist.") }}');
            });
        });
        /* Mobile: sync "Select all" with item checkboxes */
        $(document).on('change', '#cartSelectAllMobile, .cart-select-all-mobile', function() {
            var checked = $(this).prop('checked');
            $('.cart-select-item').prop('checked', checked);
            $('#cartSelectAll').prop('checked', checked);
            if (typeof window.cartSubTotal === 'function') window.cartSubTotal();
        });
        $(document).on('change', '.cart-select-item', function() {
            var total = $('.cart-select-item').length;
            var checked = $('.cart-select-item:checked').length;
            $('#cartSelectAll').prop('checked', total > 0 && checked === total).prop('indeterminate', checked > 0 && checked < total);
            $('#cartSelectAllMobile').prop('checked', total > 0 && checked === total).prop('indeterminate', checked > 0 && checked < total);
        });
        $(function() { if (typeof window.cartSubTotal === 'function') window.cartSubTotal(); });
    })(jQuery);
})();
</script>
@endpush
