@extends($activeTemplate . 'layouts.master')
@section('dashboard_page_title')
    @include($activeTemplate . 'partials.dashboard_page_header', ['title' => __('My Orders'), 'subtitle' => __('View and track your orders')])
@endsection
@section('content')
    @if($orders->isNotEmpty())
            <div class="row g-4">
                @foreach($orders as $order)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm order-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-3 col-6 mb-3 mb-md-0">
                                        <span class="text-muted small d-block">@lang('Order No')</span>
                                        <strong class="text--base">{{ $order->order_no }}</strong>
                                    </div>
                                    <div class="col-md-2 col-6 mb-3 mb-md-0">
                                        <span class="text-muted small d-block">@lang('Date')</span>
                                        <span>{{ $order->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="col-md-2 col-6 mb-3 mb-md-0">
                                        <span class="text-muted small d-block">@lang('Payment')</span>
                                        @if ($order->payment_type == Status::PAYMENT_ONLINE)
                                            <span>@lang('Online')</span>
                                        @else
                                            <span>@lang('Cash on Delivery')</span>
                                        @endif
                                    </div>
                                    <div class="col-md-2 col-6 mb-3 mb-md-0">
                                        <span class="text-muted small d-block">@lang('Amount')</span>
                                        <strong>{{ showAmount($order->total) }} {{ __($general->cur_text) }}</strong>
                                    </div>
                                    <div class="col-md-2 col-6 mb-3 mb-md-0">
                                        <span class="text-muted small d-block">@lang('Status')</span>
                                        @php echo $order->ordersBadge; @endphp
                                        @if (@$order->deposit->admin_feedback != null)
                                            <span class="badge badge--info status-info detailBtn ms-1" data-admin_feedback="{{ __(@$order->deposit->admin_feedback) }}" title="@lang('Info')">@include($activeTemplate . 'partials.icon', ['name' => 'info-circle'])</span>
                                        @endif
                                    </div>
                                    <div class="col-md-1 text-md-end mt-2 mt-md-0">
                                        <a href="{{ route('user.order.detail', $order->id) }}" class="btn btn--base btn-sm" title="@lang('View details')">
                                            @include($activeTemplate . 'partials.icon', ['name' => 'eye'])
                                        </a>
                                        <a href="{{ route('track.order') }}?order={{ $order->order_no }}" class="btn btn-outline-primary btn-sm mt-1 mt-md-0 d-inline-block" title="@lang('Track order')">
                                            @include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast'])
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ paginateLinks($orders) }}</div>
    @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <span class="text-muted" style="font-size: 4rem;">@include($activeTemplate . 'partials.icon', ['name' => 'shopping-bag'])</span>
                    <h5 class="mt-3 text-muted">@lang('No orders yet')</h5>
                    <p class="text-muted mb-4">{{ __($emptyMessage) }}</p>
                    <a href="{{ route('products') }}" class="btn btn--base">@lang('Browse Products')</a>
                </div>
            </div>
    @endif

    <div id="detailModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg--base">
                    <h5 class="modal-title text-white">@lang('Details')</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"><div class="payment-detail"></div></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">@lang('Close')</button></div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>.status-info { cursor: pointer; } .order-card { transition: box-shadow 0.2s; } .order-card:hover { box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; }</style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var feedback = $(this).data('admin_feedback');
                $('#detailModal .payment-detail').html('<p class="mb-0">' + feedback + '</p>');
                $('#detailModal').modal('show');
            });
        })(jQuery);
    </script>
@endpush
