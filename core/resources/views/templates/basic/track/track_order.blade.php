@extends($activeTemplate . 'layouts.frontend')
@section('content')
<div class="track-order-page" role="region" aria-label="@lang('Track Your Order')">
    <div class="track-order-container">
        {{-- Compact hero --}}
        <header class="track-order-hero">
            <span class="track-order-hero__icon" aria-hidden="true">@include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast'])</span>
            <h1 class="track-order-hero__title">{{ __($pageTitle) }}</h1>
            <p class="track-order-hero__subtitle">@lang('Enter your order number below to see current status and where your product is')</p>
        </header>

        {{-- Single compact search card --}}
        <div class="track-order-search-card">
            <form class="track-search-form" id="trackOrderForm" method="post" action="javascript:void(0);">
                @csrf
                <div class="track-search-row">
                    <label for="track_order_no" class="track-search-label">@lang('Order Number')</label>
                    <div class="track-search-input-wrap">
                        <input type="text" id="track_order_no" class="track-search-input" name="order_no" placeholder="@lang('e.g.') ORD-123456" autocomplete="off" required>
                        <button type="submit" class="track-search-btn">
                            @include($activeTemplate . 'partials.icon', ['name' => 'search'])<span>@lang('Track')</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @auth
        @if($recentOrders->isNotEmpty())
        <section class="track-order-recent" aria-label="@lang('Your recent orders')">
            <div class="track-order-recent-card">
                <h2 class="track-order-recent__title">@include($activeTemplate . 'partials.icon', ['name' => 'history']) @lang('Your recent orders')</h2>
                <p class="track-order-recent__hint">@lang('Click an order to track it quickly')</p>
                <ul class="track-order-recent__list" role="list">
                    @foreach($recentOrders as $ord)
                    <li class="track-order-recent__item">
                        <button type="button" class="track-quick-btn" data-order-no="{{ $ord->order_no }}">
                            <span class="track-quick-btn__no">{{ $ord->order_no }}</span>
                            <span class="track-quick-btn__meta">{{ $ord->created_at->format('d M Y') }}</span>
                            <span class="track-quick-btn__amount">{{ $general->cur_sym }}{{ showAmount($ord->total) }}</span>
                            <span class="track-quick-btn__action">@include($activeTemplate . 'partials.icon', ['name' => 'search-plus']) @lang('Track')</span>
                        </button>
                    </li>
                    @endforeach
                </ul>
            </div>
        </section>
        @endif
        @endauth

        <div id="show_track" class="track-result-container"></div>
    </div>
</div>
@endsection

@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush

@push('script')
<script>
(function($) {
    "use strict";
    var trackUrl = "{{ route('get.track.order') }}";
    var token = "{{ csrf_token() }}";

    function doTrack(orderNo) {
        orderNo = (orderNo || '').toString().trim();
        if (!orderNo) {
            var emptyMsg = '{{ __("Please enter order number.") }}';
            if (typeof notify === 'function') notify('error', emptyMsg);
            else $('#show_track').html('<div class="alert alert-danger">' + emptyMsg + '</div>');
            return;
        }
        $('#show_track').html('<div class="text-center py-4"><div class="spinner-border text--base" role="status"></div><p class="mt-2 small">@lang("Loading...")</p></div>');
        $.ajax({
            headers: { "X-CSRF-TOKEN": token },
            url: trackUrl,
            data: { orderNo: orderNo },
            method: "POST",
            success: function(response) {
                if (typeof response === 'object' && response !== null && response.error) {
                    var msg = (response.error.order_number && response.error.order_number[0]) ? response.error.order_number[0] : (typeof response.error === 'string' ? response.error : '{{ __("Order not found.") }}');
                    $('#show_track').html('<div class="alert alert-danger">' + msg + '</div>');
                    if (typeof notify === 'function') notify('error', msg);
                } else {
                    $('#show_track').html(response);
                }
            },
            error: function(xhr) {
                var msg = '{{ __("Something went wrong. Try again.") }}';
                if (xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
                else if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                else if (xhr.responseText) { try { var d = JSON.parse(xhr.responseText); if (d && d.error) msg = d.error; } catch (e) {} }
                $('#show_track').html('<div class="alert alert-danger">' + msg + '</div>');
                if (typeof notify === 'function') notify('error', msg);
            }
        });
    }

    $('#trackOrderForm').on('submit', function(e) {
        e.preventDefault();
        doTrack($('#track_order_no').val());
    });

    $(document).on('click', '.track-quick-btn', function() {
        var orderNo = $(this).data('order-no');
        $('#track_order_no').val(orderNo);
        doTrack(orderNo);
    });
    var urlOrder = (function() { var m = window.location.search.match(/[?&]order=([^&]+)/); return m ? decodeURIComponent(m[1]) : ''; })();
    if (urlOrder) { $('#track_order_no').val(urlOrder); doTrack(urlOrder); }
})(jQuery);
</script>
@endpush
