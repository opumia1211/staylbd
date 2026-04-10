@extends($activeTemplate . 'layouts.frontend')

@push('style')
    
{{-- inline style moved to critical-storefront.css --}}

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
