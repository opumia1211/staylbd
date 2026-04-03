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
<style>
/* Track Order – professional, no overlap/jump, layout isolated */
.track-order-page {
    --track-base: var(--base, #6366f1);
    --track-muted: #6c757d;
    --track-border: rgba(0,0,0,0.1);
    --track-radius: 8px;
    --track-gap: 16px;
    box-sizing: border-box;
    overflow-x: hidden;
    width: 100%;
    max-width: 100%;
    padding: 24px 16px 32px;
}
.track-order-page *,
.track-order-page *::before,
.track-order-page *::after { box-sizing: border-box; }

.track-order-container {
    max-width: 560px;
    margin: 0 auto;
    width: 100%;
    min-width: 0;
    overflow: hidden;
}

.track-order-hero {
    text-align: center;
    margin-bottom: 24px;
}
.track-order-hero__icon {
    display: inline-block;
    font-size: 2.5rem;
    line-height: 1;
    color: var(--track-base);
    margin-bottom: 8px;
}
.track-order-hero__title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 6px 0;
    line-height: 1.3;
    color: #1a1a1a;
}
.track-order-hero__subtitle {
    font-size: 0.875rem;
    color: var(--track-muted);
    margin: 0;
    line-height: 1.45;
    max-width: 100%;
}

.track-order-search-card {
    background: #fff;
    border: 1px solid var(--track-border);
    border-radius: var(--track-radius);
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    padding: 20px;
    margin-bottom: var(--track-gap);
    overflow: hidden;
}
.track-search-row { }
.track-search-label {
    display: block;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}
.track-search-input-wrap {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 10px;
    align-items: stretch;
    width: 100%;
    min-width: 0;
}
.track-search-input {
    width: 100%;
    min-width: 0;
    height: 44px;
    padding: 0 14px;
    font-size: 1rem;
    border: 1px solid var(--track-border);
    border-radius: var(--track-radius);
}
.track-search-input:focus {
    outline: none;
    border-color: var(--track-base);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}
.track-search-btn {
    height: 44px;
    min-width: 100px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 0 18px;
    font-size: 0.9375rem;
    font-weight: 600;
    color: #fff;
    background: var(--track-base);
    border: none;
    border-radius: var(--track-radius);
    cursor: pointer;
    white-space: nowrap;
}
.track-search-btn:hover { opacity: 0.95; }

/* Recent orders – one card, fixed grid: no overlap, no jump */
.track-order-recent {
    margin-bottom: var(--track-gap);
}
.track-order-recent-card {
    background: #fff;
    border: 1px solid var(--track-border);
    border-radius: var(--track-radius);
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    padding: 20px;
    overflow: hidden;
}
.track-order-recent__title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 4px 0;
    color: #1a1a1a;
}
.track-order-recent__hint {
    font-size: 0.8125rem;
    color: var(--track-muted);
    margin: 0 0 16px 0;
}
.track-order-recent__list {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    list-style: none;
    margin: 0;
    padding: 0;
    width: 100%;
}
.track-order-recent__item {
    min-width: 0;
    margin: 0;
    padding: 0;
}
.track-quick-btn {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    width: 100%;
    height: 100%;
    min-height: 88px;
    padding: 14px;
    font-size: 0.8125rem;
    text-align: left;
    background: #fafafa;
    border: 1px solid var(--track-border);
    border-radius: var(--track-radius);
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    overflow: hidden;
}
.track-quick-btn:hover {
    border-color: var(--track-base);
    background: #fff;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.12);
}
.track-quick-btn__no {
    display: block;
    font-weight: 600;
    color: #1a1a1a;
    font-size: 0.875rem;
    word-break: break-all;
    line-height: 1.3;
}
.track-quick-btn__meta {
    display: block;
    font-size: 0.75rem;
    color: var(--track-muted);
    margin-top: 4px;
}
.track-quick-btn__amount {
    display: block;
    font-size: 0.875rem;
    margin-top: 6px;
    font-weight: 600;
    color: #111827;
}
.track-quick-btn__action {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 10px;
    font-size: 0.75rem;
    color: var(--track-base);
    font-weight: 600;
}
.track-quick-btn:hover .track-quick-btn__action { text-decoration: underline; }

.track-result-container {
    min-height: 24px;
    width: 100%;
    min-width: 0;
    overflow: hidden;
}
.track-result-container .row,
.track-result-container .d-flex { min-width: 0; }
.track-result-container .card-body { overflow-wrap: break-word; }

/* Injected result card – professional, status & location clear */
.track-order-page .track-result-card {
    border: 1px solid var(--track-border);
    border-radius: var(--track-radius);
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    overflow: hidden;
    margin: 0;
    width: 100%;
}
.track-order-page .track-result-header {
    padding: 16px 18px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 1px solid var(--track-border);
}
.track-order-page .track-result-header__top {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}
.track-order-page .track-result-header__info {
    min-width: 0;
}
.track-order-page .track-result-header__label {
    display: block;
    font-size: 0.75rem;
    color: var(--track-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.track-order-page .track-result-header__no {
    display: block;
    font-size: 1.125rem;
    color: var(--track-base);
    margin-top: 2px;
}
.track-order-page .track-result-header__date {
    display: block;
    font-size: 0.8125rem;
    color: var(--track-muted);
    margin-top: 4px;
}
.track-order-page .track-result-header__badges {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}
.track-order-page .track-result-status-badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.8125rem;
    font-weight: 600;
}
.track-order-page .track-result-status-badge--0 { background: #fef3c7; color: #92400e; }
.track-order-page .track-result-status-badge--1 { background: #dbeafe; color: #1e40af; }
.track-order-page .track-result-status-badge--2 { background: #e0e7ff; color: #3730a3; }
.track-order-page .track-result-status-badge--3 { background: #d1fae5; color: #065f46; }
.track-order-page .track-result-header__total {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
}
.track-order-page .track-result-body {
    padding: 18px;
}
.track-order-page .track-where-box {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 16px;
    margin-bottom: 20px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.06) 0%, rgba(99, 102, 241, 0.02) 100%);
    border: 1px solid rgba(99, 102, 241, 0.15);
    border-radius: var(--track-radius);
}
.track-order-page .track-where-box__icon {
    width: 44px;
    height: 44px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--track-base);
    color: #fff;
    border-radius: 10px;
    font-size: 1.25rem;
}
.track-order-page .track-where-box__title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 6px 0;
    color: #1a1a1a;
}
.track-order-page .track-where-box__text {
    font-size: 0.9375rem;
    color: #374151;
    margin: 0;
    line-height: 1.5;
}
.track-order-page .track-result-card .card-header { padding: 14px 16px; }
.track-order-page .track-result-card .card-body { padding: 14px 16px; }
.track-order-page .track-result-card--pro .card-body { padding: 18px; }
.track-order-page .track-section-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--track-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin: 0 0 12px 0;
}
.track-order-page .track-delivery-address__text {
    font-size: 0.9375rem;
    color: #374151;
    margin: 0;
    line-height: 1.6;
}
.track-order-page .track-timeline {
    position: relative;
    padding-left: 1.75rem;
}
.track-order-page .track-timeline__item {
    position: relative;
    padding-bottom: 0.75rem;
}
.track-order-page .track-timeline__item:last-child { padding-bottom: 0; }
.track-order-page .track-timeline__marker {
    position: absolute;
    left: -1.75rem;
    top: 0;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    background: #e5e7eb;
    color: var(--track-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    z-index: 1;
}
.track-order-page .track-timeline__item.done .track-timeline__marker {
    background: var(--track-base);
    color: #fff;
}
.track-order-page .track-timeline__item.active .track-timeline__marker {
    background: var(--track-base);
    color: #fff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}
.track-order-page .track-timeline__line {
    position: absolute;
    left: calc(-1.75rem + 0.4rem);
    top: 1.5rem;
    bottom: 0;
    width: 2px;
    background: #e5e7eb;
    z-index: 0;
}
.track-order-page .track-timeline__item.done .track-timeline__line { background: var(--track-base); }
.track-order-page .track-timeline__content { min-width: 0; }
.track-order-page .track-timeline__content h6 { font-size: 0.9375rem; margin: 0; }
.track-order-page .track-timeline__content small { font-size: 0.8125rem; }

.track-order-page .alert {
    border-radius: var(--track-radius);
    padding: 12px 16px;
    font-size: 0.875rem;
    width: 100%;
    min-width: 0;
}

@media (max-width: 520px) {
    .track-order-recent__list {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 480px) {
    .track-order-page { padding: 20px 12px 28px; }
    .track-order-search-card { padding: 16px; }
    .track-search-input-wrap { grid-template-columns: 1fr; }
    .track-search-btn { width: 100%; min-width: 0; }
    .track-order-recent-card { padding: 16px; }
}
</style>
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
