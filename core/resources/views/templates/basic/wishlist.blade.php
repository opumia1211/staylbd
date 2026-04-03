@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'partials.wishlist_page_content')
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            getWishlistCount();
            function getWishlistCount() {
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
