@extends($activeTemplate . 'layouts.master')

@push('head-meta')
    @include('partials.storefront_deferred_bundle', ['bundle' => 'tailwind-storefront-deferred-compare'])
@endpush

@section('dashboard_page_title')
    @php
        $wishlistHeaderActions = $headerActions ?? '';
    @endphp
    @include($activeTemplate . 'partials.dashboard_page_header', [
        'title' => __('My Wishlist'),
        'subtitle' => __('Products you saved for later'),
        'actions' => $wishlistHeaderActions
    ])
@endsection
@section('content')
    @include($activeTemplate . 'partials.wishlist_page_content')
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            if (typeof getWishlistCount === 'function') getWishlistCount();
            else {
                $.ajax({ type: "GET", url: "{{ route('wish.list.count') }}", success: function(response) {
                    var total = Object.keys(response).length;
                    $.each(response, function(i, value) {
                        $('.add-wishlist[data-product_id="' + value.product_id + '"]').addClass('active added');
                    });
                    $('.show-wishlist-count').text(total);
                }});
            }
        })(jQuery);
    </script>
@endpush
