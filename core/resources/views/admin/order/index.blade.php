@php
    $stats = $stats ?? ['total_count' => 0, 'total_value' => 0, 'today_count' => 0, 'today_value' => 0];
    $scope = $scope ?? 'all';
    $emptyMessage = $emptyMessage ?? __('Data not found');
@endphp
@extends('admin.layouts.app')
@section('panel')
    {{-- Summary cards --}}
    <div class="row mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm bg-white rounded-3 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 rounded-3 bg--primary bg-opacity-10 p-2">
                            <i class="las la-shopping-cart text--primary fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted small mb-0">@lang('Total Orders')</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['total_count']) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm bg-white rounded-3 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 rounded-3 bg-success bg-opacity-10 p-2">
                            <i class="las la-coins text-success fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted small mb-0">@lang('Total Value')</p>
                            <h5 class="mb-0 fw-bold">{{ showAmount($stats['total_value']) }} {{ __($general->cur_text) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm bg-white rounded-3 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 rounded-3 bg-info bg-opacity-10 p-2">
                            <i class="las la-calendar-day text-info fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted small mb-0">@lang("Today's Orders")</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['today_count']) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm bg-white rounded-3 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 rounded-3 bg-warning bg-opacity-10 p-2">
                            <i class="las la-wallet text-warning fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted small mb-0">@lang("Today's Value")</p>
                            <h5 class="mb-0 fw-bold">{{ showAmount($stats['today_value']) }} {{ __($general->cur_text) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Order status filter tabs: clear, high-contrast, professional --}}
    <div class="order-status-tabs-wrapper mb-4">
        <div class="order-status-tabs" role="tablist">
            <a href="{{ route('admin.orders.index') }}" class="order-status-tab {{ $scope === 'all' ? 'order-status-tab--active' : '' }}" role="tab">@lang('All')</a>
            <a href="{{ route('admin.orders.pending') }}" class="order-status-tab order-status-tab--pending {{ $scope === 'pending' ? 'order-status-tab--active' : '' }}" role="tab">@lang('Pending')</a>
            <a href="{{ route('admin.orders.confirmed') }}" class="order-status-tab order-status-tab--confirmed {{ $scope === 'confirmed' ? 'order-status-tab--active' : '' }}" role="tab">@lang('Confirmed')</a>
            <a href="{{ route('admin.orders.processing') }}" class="order-status-tab order-status-tab--processing {{ $scope === 'processing' ? 'order-status-tab--active' : '' }}" role="tab">@lang('Processing')</a>
            <a href="{{ route('admin.orders.packaging') }}" class="order-status-tab order-status-tab--packaging {{ $scope === 'packaging' ? 'order-status-tab--active' : '' }}" role="tab">@lang('Packaging')</a>
            <a href="{{ route('admin.orders.shipped') }}" class="order-status-tab order-status-tab--shipped {{ $scope === 'shipped' ? 'order-status-tab--active' : '' }}" role="tab">@lang('Shipped')</a>
            <a href="{{ route('admin.orders.delivered') }}" class="order-status-tab order-status-tab--delivered {{ $scope === 'delivered' ? 'order-status-tab--active' : '' }}" role="tab">@lang('Delivered')</a>
            <a href="{{ route('admin.orders.cancel') }}" class="order-status-tab order-status-tab--cancel {{ $scope === 'cancel' ? 'order-status-tab--active' : '' }}" role="tab">@lang('Cancelled')</a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ request()->url() }}" class="row g-3 align-items-end flex-wrap" id="orderFilterForm">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small mb-0">@lang('Date from')</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small mb-0">@lang('Date to')</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small mb-0">@lang('Payment')</label>
                    <select class="form-select form-select-sm" name="payment_type">
                        <option value="">@lang('All')</option>
                        <option value="{{ Status::PAYMENT_ONLINE }}" {{ request('payment_type') == Status::PAYMENT_ONLINE ? 'selected' : '' }}>@lang('Online')</option>
                        <option value="{{ Status::PAYMENT_OFFLINE }}" {{ request('payment_type') == Status::PAYMENT_OFFLINE ? 'selected' : '' }}>@lang('COD')</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small mb-0">@lang('Per page')</label>
                    <select class="form-select form-select-sm" name="per_page">
                        @foreach([10, 20, 50, 100, 200] as $n)
                            <option value="{{ $n }}" {{ request('per_page', getPaginate()) == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn--primary btn-sm w-100">@lang('Apply')</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--light style--two table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 36px;"><input type="checkbox" id="selectAllOrders" aria-label="@lang('Select all')"></th>
                            <th>@lang('Order No')</th>
                            <th>@lang('Customer')</th>
                            <th>@lang('Items')</th>
                            <th>@lang('Total')</th>
                            <th>@lang('Payment')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Status')</th>
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'order_source'))<th>@lang('Order Source')</th>@endif
                            <th class="text-end" style="min-width: 180px;">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><input type="checkbox" class="order-checkbox" value="{{ $order->id }}" aria-label="@lang('Select order')"></td>
                                <td><span class="fw-medium">{{ $order->order_no }}</span></td>
                                <td>
                                    @if($order->isGuest())
                                        <div>
                                            <span class="badge bg-secondary me-1">@lang('Guest')</span>
                                            <span class="fw-medium">{{ $order->guest_name ?? '—' }}</span>
                                            <br><small class="text-muted">{{ $order->guest_phone ?? '—' }}</small>
                                            @if($order->guest_email)<br><small class="text-muted">{{ $order->guest_email }}</small>@endif
                                        </div>
                                    @else
                                        <div>
                                            @if($order->user)
                                            <a href="{{ route('admin.users.detail', $order->user->id) }}" class="text--primary">{{ $order->user->username }}</a>
                                            <br><small class="text-muted">{{ $order->user->email }}</small>
                                            @else
                                            <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $order->orderDetail->count() }} @lang('item(s)')</td>
                                <td><strong>{{ showAmount($order->total) }} {{ __($general->cur_text) }}</strong></td>
                                <td>
                                    @if($order->payment_type == Status::PAYMENT_ONLINE)
                                        <span class="badge bg-info">@lang('Online')</span>
                                        @if($order->payment_status == Status::ORDER_PAYMENT_SUCCESS)
                                            <span class="badge bg-success small">@lang('Paid')</span>
                                        @else
                                            <span class="badge bg-warning small">@lang('Pending')</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">@lang('COD')</span>
                                    @endif
                                </td>
                                <td><span class="text-muted">{{ $order->created_at->format('M d, Y H:i') }}</span></td>
                                <td>@php echo $order->ordersBadge; @endphp</td>
                                @if(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'order_source'))
                                <td>
                                    @if(($order->order_source ?? '') === 'quick_order')
                                        <span class="badge bg-success">@lang('Quick Order')</span>
                                    @else
                                        <span class="badge bg-secondary">@lang('Checkout')</span>
                                    @endif
                                </td>
                                @endif
                                <td class="text-end align-middle">
                                    <div class="order-list-actions d-flex flex-wrap gap-2 justify-content-end">
                                        <a href="{{ route('admin.orders.detail', $order->id) }}" class="order-action-btn order-action-btn--detail" title="@lang('View full order details')">
                                            <i class="las la-desktop"></i><span>@lang('Details')</span>
                                        </a>
                                        <a href="{{ route('admin.orders.invoice', $order->id) }}" class="order-action-btn order-action-btn--invoice" target="_blank" rel="noopener" title="@lang('Print invoice')">
                                            <i class="las la-print"></i><span>@lang('Invoice')</span>
                                        </a>
                                        @if(in_array($order->order_status, [Status::ORDER_PENDING, Status::ORDER_CONFIRMED, Status::ORDER_PROCESSING, Status::ORDER_PACKAGING, Status::ORDER_SHIPPED]))
                                            <button type="button" class="order-action-btn order-action-btn--next orderStatusModal"
                                                data-url="{{ route('admin.orders.status', $order->id) }}"
                                                data-order_status="{{ $order->order_status }}"
                                                title="@lang('Next step - change status')">
                                                @if($order->order_status == Status::ORDER_PENDING)<i class="las la-check-circle"></i><span>@lang('Confirm')</span>
                                                @elseif($order->order_status == Status::ORDER_CONFIRMED)<i class="las la-cog"></i><span>@lang('Processing')</span>
                                                @elseif($order->order_status == Status::ORDER_PROCESSING)<i class="las la-box"></i><span>@lang('Packaging')</span>
                                                @elseif($order->order_status == Status::ORDER_PACKAGING)<i class="las la-truck"></i><span>@lang('Shipped')</span>
                                                @else<i class="las la-check-double"></i><span>@lang('Delivered')</span> @endif
                                            </button>
                                        @endif
                                        @if($order->order_status == Status::ORDER_PENDING)
                                            <button type="button" class="order-action-btn order-action-btn--cancel cancelOrderModal" data-url="{{ route('admin.orders.status', $order->id) }}" title="@lang('Cancel this order')">
                                                <i class="las la-times-circle"></i><span>@lang('Cancel')</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-muted py-5" colspan="9">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
            <div class="card-footer py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted">@lang('Showing') {{ $orders->firstItem() }} - {{ $orders->lastItem() }} @lang('of') {{ $orders->total() }}</small>
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    @include('admin.order.partials.modals')
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline--primary me-2"><i class="las la-arrow-left"></i> @lang('Back to Dashboard')</a>
    <form action="{{ request()->url() }}" method="GET" class="d-inline-flex flex-wrap gap-2 align-items-center">
        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
        <input type="hidden" name="payment_type" value="{{ request('payment_type') }}">
        <input type="hidden" name="per_page" value="{{ request('per_page', getPaginate()) }}">
        <x-search-key-field placeholder="{{ __('Search by Order No / Customer') }}" />
    </form>
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <a href="{{ route('admin.orders.export', array_merge(request()->only(['search','date_from','date_to','payment_type']), ['scope' => $scope])) }}" class="btn btn-sm btn-outline--primary">
            <i class="las la-file-export"></i> @lang('Export CSV')
        </a>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline--primary" id="bulkActionBtn" disabled><i class="las la-truck"></i> @lang('Bulk Actions')</button>
            <button type="button" class="btn btn-sm btn-outline--primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false"><span class="visually-hidden">@lang('Toggle')</span></button>
            <div class="dropdown-menu dropdown-menu-end">
                <button class="dropdown-item" type="button" id="sendToPathaoBtn"><i class="las la-truck"></i> @lang('Send to Pathao')</button>
                <button class="dropdown-item" type="button" id="sendToSteadfastBtn"><i class="las la-shipping-fast"></i> @lang('Send to Steadfast')</button>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('admin.api.courier.logs') }}"><i class="las la-list-alt"></i> @lang('Courier Logs')</a>
            </div>
        </div>
    </div>
@endpush

@push('style')
<style>
/* Order management: flexible, professional CSS – change variables to theme without breaking layout */
:root {
    --order-tab-bg: #f1f3f5;
    --order-tab-color: #495057;
    --order-tab-active-bg: var(--primary, #0d6efd);
    --order-tab-active-color: #fff;
    --order-tab-hover-bg: #e9ecef;
    --order-tab-pending: #fd7e14;
    --order-tab-confirmed: #198754;
    --order-tab-processing: #0dcaf0;
    --order-tab-packaging: #6f42c1;
    --order-tab-shipped: #212529;
    --order-tab-delivered: #0d6efd;
    --order-tab-cancel: #dc3545;
    --order-tab-radius: 8px;
    --order-tab-padding-x: 1rem;
    --order-tab-padding-y: 0.6rem;
    --order-tab-font-weight: 600;
    --order-tab-font-size: 0.9375rem;
}
.order-status-tabs-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.order-status-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    min-height: 2.75rem;
}
.order-status-tab {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: var(--order-tab-padding-y) var(--order-tab-padding-x);
    border-radius: var(--order-tab-radius);
    font-weight: var(--order-tab-font-weight);
    font-size: var(--order-tab-font-size);
    text-decoration: none;
    color: var(--order-tab-color);
    background: var(--order-tab-bg);
    border: 1px solid rgba(0,0,0,0.08);
    white-space: nowrap;
    transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease, transform 0.1s ease;
}
.order-status-tab:hover { background: var(--order-tab-hover-bg); color: var(--order-tab-color); border-color: rgba(0,0,0,0.12); }
.order-status-tab--active {
    background: var(--order-tab-active-bg) !important;
    color: var(--order-tab-active-color) !important;
    border-color: var(--order-tab-active-bg) !important;
}
.order-status-tab--pending:not(.order-status-tab--active) { border-left: 3px solid var(--order-tab-pending); }
.order-status-tab--confirmed:not(.order-status-tab--active) { border-left: 3px solid var(--order-tab-confirmed); }
.order-status-tab--processing:not(.order-status-tab--active) { border-left: 3px solid var(--order-tab-processing); }
.order-status-tab--packaging:not(.order-status-tab--active) { border-left: 3px solid var(--order-tab-packaging); }
.order-status-tab--shipped:not(.order-status-tab--active) { border-left: 3px solid var(--order-tab-shipped); }
.order-status-tab--delivered:not(.order-status-tab--active) { border-left: 3px solid var(--order-tab-delivered); }
.order-status-tab--cancel:not(.order-status-tab--active) { border-left: 3px solid var(--order-tab-cancel); }
.table td, .table th { vertical-align: middle; }

/* Order action buttons: professional, clear, every button works */
.order-list-actions { gap: 0.5rem; }
.order-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.45rem 0.65rem;
    font-size: 0.8125rem;
    font-weight: 500;
    border-radius: 6px;
    border: 1px solid transparent;
    text-decoration: none;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s, color 0.2s, border-color 0.2s, transform 0.1s;
    min-height: 36px;
    line-height: 1.2;
}
.order-action-btn:hover { transform: translateY(-1px); }
.order-action-btn:focus { outline: 2px solid var(--primary, #0d6efd); outline-offset: 2px; }
.order-action-btn--detail {
    background: rgba(13, 110, 253, 0.08);
    color: var(--primary, #0d6efd);
    border-color: rgba(13, 110, 253, 0.35);
}
.order-action-btn--detail:hover { background: rgba(13, 110, 253, 0.15); color: var(--primary); }
.order-action-btn--invoice {
    background: rgba(108, 117, 125, 0.1);
    color: #495057;
    border-color: rgba(108, 117, 125, 0.3);
}
.order-action-btn--invoice:hover { background: rgba(108, 117, 125, 0.2); color: #212529; }
.order-action-btn--next {
    background: rgba(253, 126, 20, 0.12);
    color: #c45a0a;
    border-color: rgba(253, 126, 20, 0.4);
}
.order-action-btn--next:hover { background: rgba(253, 126, 20, 0.2); color: #a84808; }
.order-action-btn--cancel {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border-color: rgba(220, 53, 69, 0.35);
}
.order-action-btn--cancel:hover { background: rgba(220, 53, 69, 0.2); color: #bb2d3b; }
@media (max-width: 767.98px) {
    .order-action-btn span { display: none; }
    .order-action-btn { padding: 0.5rem 0.6rem; min-width: 38px; justify-content: center; }
}
@media (max-width: 991.98px) {
    .order-status-tab { padding: 0.5rem 0.75rem; font-size: 0.875rem; }
}
/* Ensure order modals appear above backdrop (fix black screen on Confirm/Cancel) */
#orderStatusModal.modal,
#pathaoModal.modal,
#steadfastModal.modal {
    z-index: 1060 !important;
}
body .modal-backdrop {
    z-index: 1050 !important;
}
</style>
@endpush

@push('script')
<script>
(function($) {
    "use strict";
    // Move modals to body so they appear above Bootstrap backdrop (fixes black screen)
    function moveOrderModalsToBody() {
        ['orderStatusModal', 'pathaoModal', 'steadfastModal'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el && el.parentNode && el.parentNode !== document.body) {
                document.body.appendChild(el);
            }
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', moveOrderModalsToBody);
    } else {
        moveOrderModalsToBody();
    }
    function showOrderStatusModal() {
        var el = document.getElementById('orderStatusModal');
        if (!el) return;
        moveOrderModalsToBody();
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
        if (orderStatus === 0) { status = 1; msg = "{{ __('Are you sure to confirm this order? User will be notified.') }}"; }
        else if (orderStatus === 1) { status = 7; msg = "{{ __('Mark as Processing? User will be notified.') }}"; }
        else if (orderStatus === 7) { status = 8; msg = "{{ __('Mark as Packaging? User will be notified.') }}"; }
        else if (orderStatus === 8) { status = 2; msg = "{{ __('Mark as Shipped? User will be notified.') }}"; }
        else if (orderStatus === 2) { status = 3; msg = "{{ __('Mark as Delivered? User will be notified.') }}"; }
        modal.find('.modal-detail').text(msg);
        modal.find('form').attr('action', url);
        modal.find('[name=order_status]').val(status);
        showOrderStatusModal();
    });
    $(document).on('click', '.cancelOrderModal', function(e) {
        e.preventDefault();
        var modal = $('#orderStatusModal');
        modal.find('form').attr('action', $(this).data('url'));
        modal.find('[name=order_status]').val(9);
        modal.find('.modal-detail').text("{{ __('Are you sure to cancel this order?') }}");
        showOrderStatusModal();
    });
    $('#selectAllOrders').change(function() { $('.order-checkbox').prop('checked', this.checked); updateBulkBtn(); });
    $('.order-checkbox').change(function() { updateBulkBtn(); $('#selectAllOrders').prop('checked', $('.order-checkbox:checked').length === $('.order-checkbox').length); });
    function updateBulkBtn() {
        var n = $('.order-checkbox:checked').length;
        $('#bulkActionBtn').prop('disabled', n === 0).html('<i class="las la-truck"></i> {{ __("Bulk Actions") }}' + (n ? ' (' + n + ')' : ''));
    }
    function getSelectedOrderIds() {
        var ids = [];
        $('.order-checkbox:checked').each(function() { ids.push($(this).val()); });
        return ids;
    }
    function setOrderIdsInForm(ids) {
        $('#pathaoModal form input[name="order_ids[]"]').remove();
        ids.forEach(function(id) {
            $('<input>').attr({ type: 'hidden', name: 'order_ids[]', value: id }).appendTo('#pathaoModal form');
        });
        $('#pathaoModal #selectedOrders').html(ids.length ? '<p class="mb-0">' + ids.length + ' {{ __("orders selected") }}</p>' : '<p class="text-muted mb-0">{{ __("No orders selected") }}</p>');
    }
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
    $(document).on('click', '#sendToPathaoBtn', function() {
        var ids = getSelectedOrderIds();
        if (!ids.length) { alert('{{ __("Please select at least one order") }}'); return; }
        setOrderIdsInForm(ids);
        showBootstrapModal('pathaoModal');
    });
    $(document).on('click', '#sendToSteadfastBtn', function() {
        var ids = getSelectedOrderIds();
        if (!ids.length) { alert('{{ __("Please select at least one order") }}'); return; }
        $('#steadfastModal form input[name="order_ids[]"]').remove();
        ids.forEach(function(id) {
            $('#steadfastModal form').append($('<input>').attr({ type: 'hidden', name: 'order_ids[]', value: id }));
        });
        $('#selectedOrdersSteadfast').html(ids.length ? '<p class="mb-0">' + ids.length + ' {{ __("orders selected") }}</p>' : '<p class="text-muted mb-0">{{ __("No orders selected") }}</p>');
        showBootstrapModal('steadfastModal');
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
