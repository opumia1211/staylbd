@extends('admin.layouts.master')

@section('content')
    <div class="page-wrapper default-version">
        @include('admin.partials.sidenav')
        <div class="admin-sidebar-overlay" id="adminSidebarOverlay" aria-hidden="true" title="@lang('Close menu')"></div>
        @include('admin.partials.topnav')

        <div class="body-wrapper">
            <div class="bodywrapper__inner">
                @include('admin.partials.breadcrumb')

                @php
                    $orderCategory = $orderCategory ?? ['route' => 'admin.orders.index', 'label' => __('All Orders')];
                    $canAdvance = in_array($order->order_status, [
                        \App\Constants\Status::ORDER_PENDING,
                        \App\Constants\Status::ORDER_CONFIRMED,
                        \App\Constants\Status::ORDER_PROCESSING,
                        \App\Constants\Status::ORDER_PACKAGING,
                        \App\Constants\Status::ORDER_SHIPPED
                    ], true);
                    $nextStatus = $order->order_status == \App\Constants\Status::ORDER_PENDING ? \App\Constants\Status::ORDER_CONFIRMED
                        : ($order->order_status == \App\Constants\Status::ORDER_CONFIRMED ? \App\Constants\Status::ORDER_PROCESSING
                        : ($order->order_status == \App\Constants\Status::ORDER_PROCESSING ? \App\Constants\Status::ORDER_PACKAGING
                        : ($order->order_status == \App\Constants\Status::ORDER_PACKAGING ? \App\Constants\Status::ORDER_SHIPPED
                        : \App\Constants\Status::ORDER_DELIVERED)));
                @endphp

                {{-- Single compact bar: Back + Category + Order# + Status + Total + Actions --}}
                <div class="order-detail-hero card border-0 shadow-sm rounded-3 mb-3">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex flex-wrap align-items-center gap-2 gap-md-3">
                            <a href="{{ route($orderCategory['route']) }}" class="btn btn-sm btn--primary order-detail-back-btn"><i class="las la-arrow-left"></i></a>
                            <a href="{{ route($orderCategory['route']) }}" class="badge order-detail-category-badge bg-primary text-decoration-none text-uppercase">{{ $orderCategory['label'] }}</a>
                            <span class="order-detail-hero__sep d-none d-md-inline text-muted">|</span>
                            <span class="fw-semibold">{{ $order->order_no }}</span>
                            <span class="text-muted small d-none d-lg-inline">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            @php echo $order->ordersBadge; @endphp
                            @php echo $order->paymentBadge; @endphp
                            <span class="text--primary fw-semibold">{{ showAmount($order->total) }} {{ __($general->cur_text) }}</span>
                            <div class="d-flex flex-wrap align-items-center gap-1 ms-md-auto order-detail-status-actions">
                                @if($canAdvance)
                                    <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __("Change status? User will be notified.") }}');">
                                        @csrf
                                        <input type="hidden" name="order_status" value="{{ $nextStatus }}">
                                        <button type="submit" class="btn btn-sm btn--primary">
                                            @if($order->order_status == \App\Constants\Status::ORDER_PENDING)<i class="las la-check-circle"></i> @lang('Confirm')
                                            @elseif($order->order_status == \App\Constants\Status::ORDER_CONFIRMED)<i class="las la-cog"></i> @lang('Processing')
                                            @elseif($order->order_status == \App\Constants\Status::ORDER_PROCESSING)<i class="las la-box"></i> @lang('Packaging')
                                            @elseif($order->order_status == \App\Constants\Status::ORDER_PACKAGING)<i class="las la-truck"></i> @lang('Shipped')
                                            @else<i class="las la-check-double"></i> @lang('Delivered') @endif
                                        </button>
                                    </form>
                                @endif
                                @if($order->order_status == \App\Constants\Status::ORDER_PENDING)
                                    <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __("Cancel this order?") }}');">
                                        @csrf
                                        <input type="hidden" name="order_status" value="{{ \App\Constants\Status::ORDER_CANCEL }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">@lang('Cancel')</button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="@lang('Print Invoice')"><i class="las la-print"></i></a>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary" title="@lang('All Orders')"><i class="las la-list"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-xl-3 col-lg-4 col-md-5">
                        <div class="card order-detail-customer-card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-body py-2 px-3">
                                @if($order->isGuest())
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="order-detail-avatar rounded-2 bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center"><i class="las la-user text-secondary"></i></span>
                                        <div class="min-w-0 flex-grow-1">
                                            <span class="badge bg-secondary mb-1">@lang('Guest')</span>
                                            <h6 class="mb-0 text-truncate">{{ $order->guest_name ?? '—' }}</h6>
                                            <p class="text-muted small mb-0 text-truncate">{{ $order->guest_email ?? '—' }}</p>
                                            @if($order->guest_phone)<a href="tel:{{ $order->guest_phone }}" class="small text-decoration-none">{{ $order->guest_phone }}</a>@endif
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <img src="{{ getImage(getFilePath('userProfile') . '/' . @$order->user->image, getFileSize('userProfile')) }}" alt="" class="order-detail-avatar rounded-2">
                                        <div class="min-w-0 flex-grow-1">
                                            <h6 class="mb-0 text-truncate">{{ @$order->user->fullname }}</h6>
                                            <p class="text-muted small mb-0 text-truncate">{{ @$order->user->email }}</p>
                                            @if(@$order->user->mobile)<a href="tel:{{ $order->user->mobile }}" class="small text-decoration-none">{{ $order->user->mobile }}</a>@endif
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('admin.users.detail', $order->user->id) }}" class="btn btn-sm btn-outline-primary">@lang('Profile')</a>
                                        <a href="{{ route('admin.users.notification.single', $order->user->id) }}" class="btn btn-sm btn-outline-primary"><i class="las la-paper-plane"></i></a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-8 col-md-7">
                        @include('admin.partials.order_details')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
:root {
    --order-hero-py: 0.5rem;
    --order-avatar-size: 48px;
    --order-card-radius: 0.5rem;
    --order-card-body-py: 0.75rem;
    --order-card-body-px: 1rem;
    --order-list-row-py: 0.4rem;
}
.order-detail-hero .order-detail-back-btn { font-weight: 500; }
.order-detail-hero .order-detail-category-badge { font-size: 0.7rem; padding: 0.25rem 0.5rem; }
.order-detail-hero__sep { font-size: 0.875rem; }
.order-detail-avatar { width: var(--order-avatar-size); height: var(--order-avatar-size); object-fit: cover; }
.order-detail-customer-card .card-body { padding: var(--order-card-body-py) var(--order-card-body-px); }
.order-detail-card .card-header { flex-wrap: wrap; padding: var(--order-card-body-py) var(--order-card-body-px); }
.order-detail-card .card-body { padding: var(--order-card-body-py) var(--order-card-body-px); }
.order-delivery-address-block { font-size: 0.9375rem; line-height: 1.5; color: #023047; }
.order-address-line { margin-bottom: 0.2rem; }
.order-address-line:last-child { margin-bottom: 0; }
.order-detail-card .list-group-item { padding: var(--order-list-row-py) 0; border-width: 0 0 1px; }
.order-detail-card .list-group-flush .list-group-item:last-child { border-bottom: 0 !important; }
.table-align-middle td, .table-align-middle th { vertical-align: middle !important; }
.order-detail-card .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.order-detail-card .table { margin-bottom: 0; min-width: 560px; font-size: 0.9375rem; }
.order-detail-card .table td, .order-detail-card .table th { padding: 0.5rem 0.75rem; }
@media (max-width: 767.98px) {
    .table-align-middle .d-inline-flex { width: 48px !important; height: 48px !important; }
    .table-align-middle img[style*="56px"] { width: 48px !important; height: 48px !important; max-width: 48px !important; }
    .order-detail-card .table { min-width: 480px; }
}
@media (max-width: 575.98px) {
    .order-detail-hero .order-detail-status-actions { width: 100%; }
}
/* Order tracking section – flexible, future-proof */
.order-tracking-section .card { transition: box-shadow 0.2s ease; }
.order-tracking-section .card:hover { box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.08) !important; }
.tracking-timeline-vertical { display: flex; flex-direction: column; gap: 0; }
.tracking-timeline-item { display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.5rem 0; border-bottom: 1px solid rgba(0,0,0,0.06); flex-wrap: wrap; }
.tracking-timeline-item:last-child { border-bottom: 0; }
.tracking-timeline-icon { flex-shrink: 0; width: 2rem; height: 2rem; border-radius: 50%; background: var(--primary, #219ebc); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; }
.tracking-timeline-body { min-width: 0; word-break: break-word; }
.tracking-timeline-actions { flex-shrink: 0; }
@media (max-width: 575.98px) {
    .tracking-timeline-item { flex-direction: column; gap: 0.35rem; }
}
.order-address-edit-form .order-address-edit-label { color: #212529; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.25rem; }
.order-address-edit-form .form-control { font-size: 0.875rem; }
.order-address-edit-form .bg-light { background-color: #f8f9fa !important; }
</style>
@endpush
