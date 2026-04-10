@extends($activeTemplate . 'layouts.master')

@push('head-meta')
    @include('partials.storefront_deferred_bundle', ['bundle' => 'tailwind-storefront-deferred-compare'])
@endpush

@section('dashboard_page_title')
    @include($activeTemplate . 'partials.dashboard_page_header', ['title' => __('Product Comparison'), 'subtitle' => __('Compare products side by side')])
@endsection
@section('content')
    @include($activeTemplate . 'partials.compare_page_content')
@endsection
@push('script')
<script>
(function($) {
    "use strict";
    var contentEl = document.getElementById('dashboard-ajax-content');
    function reloadCompare() {
        if (contentEl) {
            fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }, credentials: 'same-origin' })
                .then(function(r) { return r.text(); })
                .then(function(html) {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(html, 'text/html');
                    var newContent = doc.getElementById('dashboard-ajax-content');
                    if (newContent) contentEl.innerHTML = newContent.innerHTML;
                });
        } else {
            window.location.reload();
        }
    }
    $(document).on('click', '.remove-compare-btn', function() {
        var productId = $(this).data('product_id');
        $.ajax({
            type: 'POST',
            url: '{{ route("compare.remove") }}',
            data: { product_id: productId, _token: '{{ csrf_token() }}' },
            success: function(r) {
                if (r.success) {
                    if (typeof notify === 'function') notify('success', r.message);
                    reloadCompare();
                    if (typeof getCompareCount === 'function') getCompareCount();
                }
            }
        });
    });
    $(document).on('click', '.clear-compare-btn', function() {
        if (!confirm('{{ __("Clear all products from comparison?") }}')) return;
        $.ajax({
            type: 'POST',
            url: '{{ route("compare.clear") }}',
            data: { _token: '{{ csrf_token() }}' },
            success: function(r) {
                if (r.success) {
                    if (typeof notify === 'function') notify('success', r.message);
                    reloadCompare();
                    if (typeof getCompareCount === 'function') getCompareCount();
                }
            }
        });
    });
    $(document).on('click', '.compare-page .btn-print', function() { window.print(); });

    /* Add to Cart on compare page */
    $(document).on('click', '.compare-page .compare-btn--cart.add-to-cart', function(e) {
        e.preventDefault();
        var productId = parseInt($(this).data('product_id'), 10);
        if (!productId) return;
        var $btn = $(this);
        $.ajax({
            method: 'POST',
            url: '{{ route("cart.list.add") }}',
            data: { product_id: productId, quantity: 1, _token: '{{ csrf_token() }}' },
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
            dataType: 'json'
        }).done(function(r) {
            if (r && r.success) { if (typeof notify === 'function') notify('success', r.success); if (typeof getCartCount === 'function') getCartCount(); }
            else { if (typeof notify === 'function') notify('error', (r && r.error) || 'Could not add to cart.'); }
        }).fail(function(xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.error) || 'Could not add to cart.';
            if (typeof notify === 'function') notify('error', msg);
        });
    });
})(jQuery);
</script>
@endpush
