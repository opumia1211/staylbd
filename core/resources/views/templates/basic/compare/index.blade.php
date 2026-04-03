@extends($activeTemplate . 'layouts.frontend')

@push('style')
    <style>
        .compare-page { padding-top: 1rem !important; padding-bottom: 1.25rem !important; }
        .compare-top-line { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.5rem 0 0.75rem; margin-bottom: 0.75rem; border-bottom: 1px solid rgba(0,0,0,.08); }
        .compare-hero { padding: 0 !important; margin: 0 !important; display: flex; align-items: center; gap: 0.5rem; }
        .compare-hero__icon { width: 2.25rem; height: 2.25rem; font-size: 1.2rem; margin: 0 !important; flex-shrink: 0; }
        .compare-hero__title { font-size: 1.2rem !important; margin: 0 !important; font-weight: 600; }
        .compare-hero__sub { font-size: 0.8rem !important; margin: 0 !important; opacity: .85; }
        .compare-toolbar { padding: 0 !important; margin: 0 !important; display: flex; align-items: center; flex-wrap: wrap; gap: 0.4rem; }
        .compare-toolbar .compare-slots { font-size: 0.85rem; }
        .compare-toolbar .btn-add-more, .compare-toolbar .btn-print, .compare-toolbar .btn-clear { padding: 0.3rem 0.6rem; font-size: 0.85rem; }
        .compare-row { display: flex; flex-wrap: nowrap; gap: 1rem; overflow-x: auto; padding-bottom: 0.5rem; margin-bottom: 0.5rem; }
        .compare-product-col { flex: 1 1 0; min-width: 200px; max-width: 280px; }
        .compare-desktop.compare-card { padding: 0.75rem !important; margin-bottom: 0.75rem !important; }
        .compare-scroll-hint { padding: 0.2rem 0 !important; font-size: 0.75rem !important; margin-bottom: 0.5rem !important; }
        .compare-product-card { position: relative; border: 1px solid rgba(0,0,0,.08); border-radius: 8px; padding: 0.75rem; height: 100%; display: flex; flex-direction: column; }
        .compare-img-wrap { height: 200px !important; width: 100%; margin: 0 0 0.5rem !important; background: #f8f9fa; border-radius: 6px; overflow: hidden; }
        .compare-img-wrap img { object-fit: contain; height: 100%; width: 100%; }
        .compare-name { font-size: 0.95rem !important; margin: 0 0 0.5rem !important; line-height: 1.3; font-weight: 600; min-height: 2.6em; }
        .compare-remove { top: 6px; right: 6px; width: 28px; height: 28px; font-size: 0.9rem; padding: 0; z-index: 2; }
        .compare-info-block { font-size: 0.8rem; display: flex; flex-wrap: wrap; gap: 0.35rem 0.75rem; margin-bottom: 0.5rem; }
        .compare-info-item { display: inline-flex; align-items: center; gap: 0.25rem; }
        .compare-info-item .label { color: #6c757d; }
        .compare-info-item .val { font-weight: 500; }
        .compare-price-cell .price-main { font-size: 1rem; font-weight: 600; }
        .compare-best-value { font-size: 0.7rem; margin-top: 0.2rem; }
        .compare-actions { display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.5rem; }
        .compare-actions .btn-view, .compare-actions .btn-cart { padding: 0.35rem 0.6rem; font-size: 0.8rem; }
        .compare-footer-cta { margin-top: 0.75rem !important; }
        .compare-table-wrap { display: none !important; }
        .compare-mobile-list { gap: 0.75rem; }
        .compare-mobile-card { padding: 0.75rem; display: flex; flex-wrap: nowrap; align-items: flex-start; gap: 0.75rem; border: 1px solid rgba(0,0,0,.08); border-radius: 8px; }
        .compare-mobile-card-link { flex: 0 0 auto; }
        .compare-mobile-img { height: 120px !important; width: 120px; flex-shrink: 0; border-radius: 6px; overflow: hidden; background: #f8f9fa; }
        .compare-mobile-img img { object-fit: contain; height: 100%; width: 100%; }
        .compare-mobile-body { flex: 1; min-width: 0; }
        .compare-mobile-name { font-size: 0.95rem !important; margin: 0 0 0.35rem !important; line-height: 1.25; }
        .compare-mobile-info-inline { display: flex; flex-wrap: wrap; gap: 0.35rem 0.75rem; font-size: 0.8rem; margin-bottom: 0.4rem; color: #495057; }
        .compare-mobile-actions { margin-top: 0.4rem; gap: 0.35rem; }
        .compare-empty { padding: 1.5rem 1rem !important; }
        .compare-empty__icon { font-size: 2rem; margin-bottom: 0.5rem !important; }
        .compare-empty__title { font-size: 1.1rem !important; }
        .compare-empty__text { font-size: 0.8rem !important; }
    </style>
@endpush

@section('content')
    @include($activeTemplate . 'partials.compare_page_content')
@endsection

@push('script')
<script>
(function($) {
    "use strict";

    $(document).on('click', '.remove-compare-btn', function() {
        var productId = $(this).data('product_id');
        $.ajax({
            type: 'POST',
            url: '{{ route("compare.remove") }}',
            data: { product_id: productId, _token: '{{ csrf_token() }}' },
            success: function(r) {
                if (r.success) {
                    if (typeof notify === 'function') notify('success', r.message);
                    location.reload();
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
                    location.reload();
                }
            }
        });
    });

    $('.btn-print').on('click', function() {
        window.print();
    });
})(jQuery);
</script>
@endpush
