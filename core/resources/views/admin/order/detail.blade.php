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

{{-- inline style moved to critical-admin.css --}}

@endpush
