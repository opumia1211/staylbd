@php
    $stats = $stats ?? ['total_count' => 0, 'total_value' => 0, 'today_count' => 0, 'today_value' => 0];
    $scope = $scope ?? 'all';
    $emptyMessage = $emptyMessage ?? __('No orders found');
@endphp
@extends('admin.layouts.app')

@section('panel')
    {{-- Modern Status Cards --}}
    <div class="row g-6 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="small fw-medium text-muted mb-1 d-block">@lang('Total Orders')</span>
                            <div class="d-flex align-items-end mt-1">
                                <h4 class="mb-0 me-2 fw-bold text-heading">{{ number_format($stats['total_count']) }}</h4>
                                <small class="text-primary fw-medium">@lang('All Time')</small>
                            </div>
                        </div>
                        <div class="avatar avatar-md">
                            <span class="avatar-initial rounded bg-label-primary d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-shopping-bag fs-3"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="small fw-medium text-muted mb-1 d-block">@lang('Net Revenue')</span>
                            <div class="d-flex align-items-end mt-1">
                                <h4 class="mb-0 me-2 fw-bold text-heading text-nowrap">{{ showAmount($stats['total_value']) }}</h4>
                                <small class="text-success fw-medium">{{ $general->cur_text }}</small>
                            </div>
                        </div>
                        <div class="avatar avatar-md">
                            <span class="avatar-initial rounded bg-label-success d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-dollar fs-3"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="small fw-medium text-muted mb-1 d-block">@lang("Today's Volume")</span>
                            <div class="d-flex align-items-end mt-1">
                                <h4 class="mb-0 me-2 fw-bold text-heading">{{ number_format($stats['today_count']) }}</h4>
                                <small class="text-info fw-medium">@lang('Orders')</small>
                            </div>
                        </div>
                        <div class="avatar avatar-md">
                            <span class="avatar-initial rounded bg-label-info d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-calendar-event fs-3"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="small fw-medium text-muted mb-1 d-block">@lang("Today's Earnings")</span>
                            <div class="d-flex align-items-end mt-1">
                                <h4 class="mb-0 me-2 fw-bold text-heading text-nowrap">{{ showAmount($stats['today_value']) }}</h4>
                                <small class="text-warning fw-medium">{{ $general->cur_text }}</small>
                            </div>
                        </div>
                        <div class="avatar avatar-md">
                            <span class="avatar-initial rounded bg-label-warning d-flex align-items-center justify-content-center">
                                <i class="icon-base bx bx-wallet fs-3"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Status Navigation --}}
    <div class="nav-align-top mb-6 shadow-sm rounded bg-white overflow-hidden">
        <ul class="nav nav-tabs nav-fill border-0" role="tablist">
            @php
                $scopes = [
                    ['key' => 'all', 'label' => __('All Jobs'), 'route' => 'admin.orders.index', 'icon' => 'bx-list-ul'],
                    ['key' => 'pending', 'label' => __('Pending'), 'route' => 'admin.orders.pending', 'icon' => 'bx-time'],
                    ['key' => 'confirmed', 'label' => __('Confirmed'), 'route' => 'admin.orders.confirmed', 'icon' => 'bx-check-double'],
                    ['key' => 'processing', 'label' => __('Processing'), 'route' => 'admin.orders.processing', 'icon' => 'bx-sync'],
                    ['key' => 'packaging', 'label' => __('Packaging'), 'route' => 'admin.orders.packaging', 'icon' => 'bx-package'],
                    ['key' => 'shipped', 'label' => __('Shipped'), 'route' => 'admin.orders.shipped', 'icon' => 'bx-truck'],
                    ['key' => 'delivered', 'label' => __('Delivered'), 'route' => 'admin.orders.delivered', 'icon' => 'bx-task'],
                    ['key' => 'cancel', 'label' => __('Cancelled'), 'route' => 'admin.orders.cancel', 'icon' => 'bx-x-circle'],
                ];
            @endphp
            @foreach($scopes as $s)
                <li class="nav-item">
                    <a href="{{ route($s['route']) }}" class="nav-link py-3 {{ $scope === $s['key'] ? 'active border-bottom border-primary border-3' : '' }}">
                        <i class="icon-base {{ $s['icon'] }} me-1 fs-5"></i> {{ $s['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Orders Table Card --}}
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header border-bottom py-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                        <span class="avatar-initial rounded bg-label-secondary d-flex align-items-center justify-content-center">
                            <i class="icon-base bx bx-data fs-4"></i>
                        </span>
                    </div>
                    <h6 class="m-0">@lang('Order Management')</h6>
                </div>
                
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <form method="GET" action="{{ request()->url() }}" class="d-flex flex-wrap align-items-center gap-2">
                        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                        
                        <div class="input-group input-group-sm w-auto">
                            <span class="input-group-text bg-label-secondary border-end-0"><i class="icon-base bx bx-calendar"></i></span>
                            <input type="date" class="form-control border-start-0 ps-1" name="date_from" value="{{ request('date_from') }}" title="@lang('From Date')">
                            <span class="input-group-text bg-label-secondary border-x-0">-</span>
                            <input type="date" class="form-control border-start-0 ps-1" name="date_to" value="{{ request('date_to') }}" title="@lang('To Date')">
                        </div>

                        <div class="input-group input-group-sm w-auto">
                            <span class="input-group-text bg-label-secondary border-end-0"><i class="icon-base bx bx-credit-card"></i></span>
                            <select class="form-select border-start-0 ps-1" name="payment_type" onchange="this.form.submit()">
                                <option value="">@lang('Payment')</option>
                                <option value="{{ Status::PAYMENT_ONLINE }}" {{ request('payment_type') == Status::PAYMENT_ONLINE ? 'selected' : '' }}>@lang('Online')</option>
                                <option value="{{ Status::PAYMENT_OFFLINE }}" {{ request('payment_type') == Status::PAYMENT_OFFLINE ? 'selected' : '' }}>@lang('COD')</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-sm btn-label-primary px-3">@lang('Filter')</button>
                        @if(request()->anyFilled(['date_from', 'date_to', 'payment_type']))
                            <a href="{{ request()->url() }}" class="btn btn-sm btn-label-secondary btn-icon"><i class="icon-base bx bx-refresh"></i></a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-label-secondary border-top-0">
                        <tr>
                            <th class="ps-5" style="width: 36px;"><input type="checkbox" id="selectAllOrders" class="form-check-input"></th>
                            <th>@lang('Order No')</th>
                            <th>@lang('Customer')</th>
                            <th>@lang('Logistics')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Payment Status')</th>
                            <th>@lang('Timeline')</th>
                            <th class="text-center">@lang('Status')</th>
                            <th class="text-end pe-5">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-5"><input type="checkbox" class="order-checkbox form-check-input" value="{{ $order->id }}"></td>
                                <td>
                                    <span class="fw-bold text-heading">#{{ $order->order_no }}</span>
                                    @if(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'order_source') && ($order->order_source ?? '') === 'quick_order')
                                        <span class="badge bg-label-success border rounded-pill px-2 ms-1 extra-small">@lang('Quick')</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->isGuest())
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-secondary d-flex align-items-center justify-content-center">
                                                    <i class="icon-base bx bx-user"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="fw-medium d-block text-truncate" style="max-width: 150px;">{{ $order->guest_name ?? __('Guest') }}</span>
                                                <small class="text-muted">{{ $order->guest_phone ?? '—' }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                @if($order->user && $order->user->image)
                                                    <img src="{{ getImage(getFilePath('userProfile') . '/' . $order->user->image, getFileSize('userProfile')) }}" alt="" class="rounded-circle shadow-xs">
                                                @else
                                                    <span class="avatar-initial rounded-circle bg-label-primary shadow-xs d-flex align-items-center justify-content-center fw-bold">
                                                        {{ strtoupper(substr($order->user->username ?? 'U', 0, 1)) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                @if($order->user)
                                                <a href="{{ route('admin.users.detail', $order->user->id) }}" class="fw-bold text-heading d-block text-truncate" style="max-width: 150px;">{{ $order->user->username }}</a>
                                                <small class="text-muted">{{ $order->user->email }}</small>
                                                @else
                                                <span class="text-muted">—</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">
                                        <span class="text-body fw-medium d-block">{{ $order->orderDetail->count() }} @lang('Items')</span>
                                        <span class="text-muted"><i class="icon-base bx bx-package me-1"></i>{{ $order->shipping_method_name ?? __('Standard') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-heading">{{ showAmount($order->total) }}</div>
                                    <small class="text-muted">{{ $general->cur_text }}</small>
                                    @if(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'advance_payment') && (float) ($order->advance_payment ?? 0) > 0)
                                        <span class="badge bg-label-success d-block mt-1 extra-small" title="@lang('Advance received')">
                                            @lang('Adv'): {{ showAmount($order->advance_payment) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->payment_type == Status::PAYMENT_ONLINE)
                                        <span class="badge bg-label-info mb-1">@lang('Online')</span>
                                        @if($order->payment_status == Status::ORDER_PAYMENT_SUCCESS)
                                            <span class="badge bg-success rounded-circle p-0" title="@lang('Paid')" style="width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center;"><i class="icon-base bx bx-check fs-small"></i></span>
                                        @else
                                            <span class="badge bg-warning rounded-circle p-0" title="@lang('Pending')" style="width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center;"><i class="icon-base bx bx-time fs-small"></i></span>
                                        @endif
                                    @else
                                        <span class="badge bg-label-secondary">@lang('COD')</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">
                                        <span class="text-heading fw-medium d-block">{{ $order->created_at->format('d M, Y') }}</span>
                                        <span class="text-muted">{{ $order->created_at->format('H:i A') }}</span>
                                    </div>
                                </td>
                                <td class="text-center">@php echo $order->ordersBadge; @endphp</td>
                                <td class="text-end pe-5">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.orders.detail', $order->id) }}" class="btn btn-sm btn-icon btn-label-primary shadow-none d-flex align-items-center justify-content-center" title="@lang('Details')">
                                            <i class="icon-base bx bx-show text-primary"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-sm btn-icon btn-label-secondary shadow-none d-flex align-items-center justify-content-center" target="_blank" title="@lang('Invoice')">
                                            <i class="icon-base bx bx-printer"></i>
                                        </a>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon btn-label-secondary shadow-none d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown">
                                                <i class="icon-base bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if(in_array($order->order_status, [Status::ORDER_PENDING, Status::ORDER_CONFIRMED, Status::ORDER_PROCESSING, Status::ORDER_PACKAGING, Status::ORDER_SHIPPED]))
                                                    <li>
                                                        <button type="button" class="dropdown-item d-flex align-items-center orderStatusModal"
                                                            data-url="{{ route('admin.orders.status', $order->id) }}"
                                                            data-order_status="{{ $order->order_status }}">
                                                            @if($order->order_status == Status::ORDER_PENDING)<i class="icon-base bx bx-check-circle me-2 text-success"></i>@lang('Confirm Order')
                                                            @elseif($order->order_status == Status::ORDER_CONFIRMED)<i class="icon-base bx bx-sync me-2 text-info"></i>@lang('Start Processing')
                                                            @elseif($order->order_status == Status::ORDER_PROCESSING)<i class="icon-base bx bx-package me-2 text-warning"></i>@lang('Mark Packaging')
                                                            @elseif($order->order_status == Status::ORDER_PACKAGING)<i class="icon-base bx bx-truck me-2 text-primary"></i>@lang('Product Shipped')
                                                            @else<i class="icon-base bx bx-task me-2 text-success"></i>@lang('Mark Delivered') @endif
                                                        </button>
                                                    </li>
                                                @endif
                                                @if($order->order_status == Status::ORDER_PENDING)
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button" class="dropdown-item d-flex align-items-center text-danger cancelOrderModal" data-url="{{ route('admin.orders.status', $order->id) }}">
                                                            <i class="icon-base bx bx-x-circle me-2"></i>@lang('Cancel Order')
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center py-12" colspan="100%">
                                    <div class="avatar avatar-xl bg-label-secondary mx-auto mb-4">
                                        <span class="avatar-initial rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="icon-base bx bx-shopping-bag fs-1"></i>
                                        </span>
                                    </div>
                                    <h5 class="text-muted">{{ __($emptyMessage) }}</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
            <div class="card-footer py-4 border-top">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <p class="mb-0 small text-muted">@lang('Displaying') {{ $orders->firstItem() }} - {{ $orders->lastItem() }} @lang('of') {{ $orders->total() }} @lang('records')</p>
                    {{ paginateLinks($orders) }}
                </div>
            </div>
        @endif
    </div>

    @include('admin.order.partials.modals')

    <form id="bulkStatusForm" action="{{ route('admin.orders.bulk.status') }}" method="post" class="d-none">
        @csrf
        <input type="hidden" name="order_status" id="bulkStatusValue">
    </form>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap align-items-center gap-2">
        <form action="{{ request()->url() }}" method="GET" class="d-inline-flex">
            <x-search-key-field placeholder="{{ __('Order ID / Customer') }}" />
        </form>
        
        <div class="btn-group">
            <button type="button" class="btn btn-label-secondary btn-sm" id="bulkActionBtn" disabled>
                <i class="icon-base bx bx-cog me-1"></i> @lang('Bulk Actions')
            </button>
            <button type="button" class="btn btn-label-secondary btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">@lang('Toggle')</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><h6 class="dropdown-header small text-muted">@lang('Courier Logistics')</h6></li>
                <li><button class="dropdown-item d-flex align-items-center" type="button" id="sendToPathaoBtn"><i class="icon-base bx bxs-truck me-2 text-danger"></i> @lang('Send to Pathao')</button></li>
                <li><button class="dropdown-item d-flex align-items-center" type="button" id="sendToSteadfastBtn"><i class="icon-base bx bxs-ship me-2 text-info"></i> @lang('Send to Steadfast')</button></li>
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header small text-muted">@lang('Bulk status')</h6></li>
                <li><button type="button" class="dropdown-item bulk-status-btn" data-status="1">@lang('Mark Confirmed')</button></li>
                <li><button type="button" class="dropdown-item bulk-status-btn" data-status="7">@lang('Mark Processing')</button></li>
                <li><button type="button" class="dropdown-item bulk-status-btn" data-status="8">@lang('Mark Packaging')</button></li>
                <li><button type="button" class="dropdown-item bulk-status-btn" data-status="2">@lang('Mark Shipped')</button></li>
                <li><button type="button" class="dropdown-item bulk-status-btn" data-status="3">@lang('Mark Delivered')</button></li>
                <li><button type="button" class="dropdown-item text-danger bulk-status-btn" data-status="9">@lang('Mark Canceled')</button></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('admin.orders.export', array_merge(request()->only(['search','date_from','date_to','payment_type']), ['scope' => $scope])) }}"><i class="icon-base bx bx-export me-2"></i> @lang('Export as CSV')</a></li>
                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('admin.api.courier.logs') }}"><i class="icon-base bx bx-list-ul me-2"></i> @lang('View Courier Logs')</a></li>
            </ul>
        </div>
    </div>
@endpush

@push('script')
<script>
(function($) {
    "use strict";
    // Move modals to body so they appear above Bootstrap backdrop
    function moveOrderModalsToBody() {
        ['orderStatusModal', 'pathaoModal', 'steadfastModal'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el && el.parentNode && el.parentNode !== document.body) {
                document.body.appendChild(el);
            }
        });
    }
    
    $(document).ready(moveOrderModalsToBody);

    function showBootstrapModal(id) {
        var el = document.getElementById(id);
        if (!el) return;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var m = bootstrap.Modal.getOrCreateInstance(el);
            m.show();
        } else {
            $(el).modal('show');
        }
    }

    $(document).on('click', '.orderStatusModal', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var modal = $('#orderStatusModal');
        var url = $btn.data('url');
        var orderStatus = parseInt($btn.data('order_status'), 10);
        var status = 1;
        var msg = '';
        if (orderStatus === 0) { status = 1; msg = "{{ __('Confirm this order? User will be notified.') }}"; }
        else if (orderStatus === 1) { status = 7; msg = "{{ __('Start processing this order?') }}"; }
        else if (orderStatus === 7) { status = 8; msg = "{{ __('Mark this order as packaging?') }}"; }
        else if (orderStatus === 8) { status = 2; msg = "{{ __('Mark this order as shipped?') }}"; }
        else if (orderStatus === 2) { status = 3; msg = "{{ __('Mark this order as delivered?') }}"; }
        
        modal.find('.modal-detail').text(msg);
        modal.find('form').attr('action', url);
        modal.find('[name=order_status]').val(status);
        showBootstrapModal('orderStatusModal');
    });

    $(document).on('click', '.cancelOrderModal', function(e) {
        e.preventDefault();
        var modal = $('#orderStatusModal');
        modal.find('form').attr('action', $(this).data('url'));
        modal.find('[name=order_status]').val(9);
        modal.find('.modal-detail').text("{{ __('Are you sure to cancel this order permanently?') }}");
        showBootstrapModal('orderStatusModal');
    });

    $('#selectAllOrders').change(function() { $('.order-checkbox').prop('checked', this.checked); updateBulkBtn(); });
    $('.order-checkbox').change(function() { updateBulkBtn(); $('#selectAllOrders').prop('checked', $('.order-checkbox:checked').length === $('.order-checkbox').length); });
    
    function updateBulkBtn() {
        var n = $('.order-checkbox:checked').length;
        $('#bulkActionBtn').prop('disabled', n === 0).html('<i class="icon-base bx bx-cog me-1"></i> {{ __("Bulk Actions") }}' + (n ? ' (' + n + ')' : ''));
    }

    function getSelectedOrderIds() {
        var ids = [];
        $('.order-checkbox:checked').each(function() { ids.push($(this).val()); });
        return ids;
    }

    $(document).on('click', '#sendToPathaoBtn', function() {
        var ids = getSelectedOrderIds();
        if (!ids.length) { notify('error', '{{ __("Please select at least one order") }}'); return; }
        
        $('#pathaoModal form input[name="order_ids[]"]').remove();
        ids.forEach(function(id) {
            $('<input>').attr({ type: 'hidden', name: 'order_ids[]', value: id }).appendTo('#pathaoModal form');
        });
        $('#pathaoModal #selectedOrders').html('<span class="badge bg-label-primary">' + ids.length + ' {{ __("Orders Selected") }}</span>');
        showBootstrapModal('pathaoModal');
    });

    $(document).on('click', '#sendToSteadfastBtn', function() {
        var ids = getSelectedOrderIds();
        if (!ids.length) { notify('error', '{{ __("Please select at least one order") }}'); return; }
        
        $('#steadfastModal form input[name="order_ids[]"]').remove();
        ids.forEach(function(id) {
            $('#steadfastModal form').append($('<input>').attr({ type: 'hidden', name: 'order_ids[]', value: id }));
        });
        $('#selectedOrdersSteadfast').html('<span class="badge bg-label-info">' + ids.length + ' {{ __("Orders Selected") }}</span>');
        showBootstrapModal('steadfastModal');
    });

    $(document).on('click', '.bulk-status-btn', function() {
        var ids = getSelectedOrderIds();
        var status = $(this).data('status');
        if (!ids.length) { notify('error', '{{ __("Please select at least one order") }}'); return; }
        if (!confirm('{{ __("Update status for selected orders?") }}')) return;
        var $form = $('#bulkStatusForm');
        $form.find('input[name="order_ids[]"]').remove();
        ids.forEach(function(id) {
            $form.append($('<input>').attr({ type: 'hidden', name: 'order_ids[]', value: id }));
        });
        $('#bulkStatusValue').val(status);
        $form.submit();
    });

    $('#pathaocity').change(function() {
        var cityId = $(this).val();
        var $zone = $('#pathaozone');
        if (!cityId) { $zone.html('<option value="">{{ __("Select Zone") }}</option>'); return; }
        $zone.html('<option value="">{{ __("Loading...") }}</option>');
        $.get('{{ route("admin.orders.pathao.zone") }}', { city_id: cityId }).done(function(data) {
            $zone.html('<option value="">{{ __("Select Zone") }}</option>');
            if (data.data && data.data.length) data.data.forEach(function(z) {
                $zone.append($('<option></option>').val(z.zone_id).text(z.zone_name));
            });
        }).fail(function() { $zone.html('<option value="">{{ __("Error loading zones") }}</option>'); });
    });
})(jQuery);
</script>
@endpush
