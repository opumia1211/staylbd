@extends('admin.layouts.app')

@section('panel')
{{-- Quick stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--primary rounded me-2 p-2"><i class="las la-shipping-fast text-white"></i></div>
                <div><h6 class="text-muted mb-0 small">@lang('Total')</h6><h5 class="mb-0">{{ number_format($logStats['total'] ?? 0) }}</h5></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--success rounded me-2 p-2"><i class="las la-check-circle text-white"></i></div>
                <div><h6 class="text-muted mb-0 small">@lang('Success')</h6><h5 class="mb-0">{{ number_format($logStats['success'] ?? 0) }}</h5></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--danger rounded me-2 p-2"><i class="las la-times-circle text-white"></i></div>
                <div><h6 class="text-muted mb-0 small">@lang('Failed')</h6><h5 class="mb-0">{{ number_format($logStats['failed'] ?? 0) }}</h5></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--warning rounded me-2 p-2"><i class="las la-clock text-white"></i></div>
                <div><h6 class="text-muted mb-0 small">@lang('Pending')</h6><h5 class="mb-0">{{ number_format($logStats['pending'] ?? 0) }}</h5></div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card b-radius--10 border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="get" action="{{ route('admin.api.courier.logs') }}" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-0">@lang('Courier')</label>
                <select name="courier_type" class="form-control form-control-sm">
                    <option value="">@lang('All')</option>
                    @foreach($courierTypes ?? [] as $ct)
                    <option value="{{ $ct }}" {{ request('courier_type') === $ct ? 'selected' : '' }}>{{ ucfirst($ct) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0">@lang('Status')</label>
                <select name="status" class="form-control form-control-sm">
                    <option value="">@lang('All')</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>@lang('Success')</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>@lang('Failed')</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>@lang('Pending')</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0">@lang('From')</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0">@lang('To')</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0">@lang('Search')</label>
                <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Order, ID">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn--primary btn-sm w-100"><i class="las la-filter"></i> @lang('Filter')</button>
            </div>
            <div class="col-md-12 col-lg-auto ms-lg-auto">
                <a href="{{ route('admin.api.courier.logs') }}" class="btn btn--dark btn-sm">@lang('Reset')</a>
                <form method="get" action="{{ route('admin.api.courier.logs.export') }}" class="d-inline">
                    @foreach(request()->only(['courier_type','status','date_from','date_to','search']) as $k => $v)
                        @if($v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
                    @endforeach
                    <button type="submit" class="btn btn-sm btn-outline--success"><i class="las la-file-csv"></i> @lang('Export')</button>
                </form>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card b-radius--10 border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 py-3">
        <h5 class="card-title mb-0">@lang('Activity Logs')</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table--light style--two mb-0">
                <thead>
                    <tr>
                        <th>@lang('Order')</th>
                        <th>@lang('Customer')</th>
                        <th>@lang('Courier')</th>
                        <th>@lang('Courier ID')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Date')</th>
                        <th width="100">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td><span class="fw-bold">#{{ $log->order_no ?? 'N/A' }}</span></td>
                        <td>{{ $log->username ?? 'N/A' }}</td>
                        <td><span class="badge badge--primary">{{ ucfirst($log->courier_type ?? 'N/A') }}</span></td>
                        <td><span class="text-muted small">{{ $log->courier_order_id ?? '—' }}</span></td>
                        <td>
                            @if(($log->status ?? '') == 'success')
                                <span class="badge badge--success">@lang('Success')</span>
                            @elseif(($log->status ?? '') == 'failed')
                                <span class="badge badge--danger">@lang('Failed')</span>
                            @else
                                <span class="badge badge--warning">@lang('Pending')</span>
                            @endif
                        </td>
                        <td>{{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('M d, H:i') : '—' }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline--primary viewBtn"
                                data-order-no="{{ $log->order_no ?? 'N/A' }}"
                                data-username="{{ $log->username ?? 'N/A' }}"
                                data-courier-type="{{ $log->courier_type ?? 'N/A' }}"
                                data-courier-order-id="{{ $log->courier_order_id ?? 'N/A' }}"
                                data-status="{{ $log->status ?? 'N/A' }}"
                                data-created-at="{{ $log->created_at ?? '' }}"
                                data-request-data="{{ e($log->request_data ?? 'N/A') }}"
                                data-response-data="{{ e($log->response_data ?? 'N/A') }}"
                                data-error-message="{{ e($log->error_message ?? 'N/A') }}"><i class="las la-eye"></i></button>
                            @if(($log->status ?? '') == 'failed')
                            <a href="{{ route('admin.api.courier.log.retry', $log->id) }}" class="btn btn-sm btn-outline--success" onclick="return confirm('@lang('Go to Bulk Courier to resend?')');"><i class="las la-redo"></i></a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="text-muted text-center py-5" colspan="7">@lang('No logs found')</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($logs, 'hasPages') && $logs->hasPages())
    <div class="card-footer py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <span class="small text-muted">@lang('Showing') {{ $logs->firstItem() }} - {{ $logs->lastItem() }} @lang('of') {{ $logs->total() }}</span>
            {{ paginateLinks($logs) }}
        </div>
    </div>
    @endif
</div>

{{-- Log detail modal --}}
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Log Details')</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 form-group"><label class="small text-muted">@lang('Order No')</label><input type="text" class="form-control form-control-sm" id="logOrderNo" readonly></div>
                    <div class="col-md-6 form-group"><label class="small text-muted">@lang('Customer')</label><input type="text" class="form-control form-control-sm" id="logCustomer" readonly></div>
                    <div class="col-md-6 form-group"><label class="small text-muted">@lang('Courier Type')</label><input type="text" class="form-control form-control-sm" id="logCourierType" readonly></div>
                    <div class="col-md-6 form-group"><label class="small text-muted">@lang('Courier Order ID')</label><input type="text" class="form-control form-control-sm" id="logCourierOrderId" readonly></div>
                    <div class="col-md-6 form-group"><label class="small text-muted">@lang('Status')</label><input type="text" class="form-control form-control-sm" id="logStatus" readonly></div>
                    <div class="col-md-6 form-group"><label class="small text-muted">@lang('Date')</label><input type="text" class="form-control form-control-sm" id="logDate" readonly></div>
                    <div class="col-12 form-group"><label class="small text-muted">@lang('Error Message')</label><textarea class="form-control form-control-sm" id="logErrorMessage" rows="2" readonly></textarea></div>
                    <div class="col-12 form-group"><label class="small text-muted">@lang('Request')</label><textarea class="form-control form-control-sm" id="logRequestData" rows="2" readonly></textarea></div>
                    <div class="col-12 form-group"><label class="small text-muted">@lang('Response')</label><textarea class="form-control form-control-sm" id="logResponseData" rows="3" readonly></textarea></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.api.courier.manage') }}" class="btn btn-sm btn-outline--primary"><i class="las la-cog"></i> @lang('Settings')</a>
    <a href="{{ route('admin.api.courier.reports') }}" class="btn btn-sm btn-outline--dark"><i class="las la-chart-bar"></i> @lang('Reports')</a>
@endpush

@push('script')
<script>
$(function() {
    $('.viewBtn').on('click', function() {
        var d = $(this).data();
        $('#logOrderNo').val(d.orderNo || 'N/A');
        $('#logCustomer').val(d.username || 'N/A');
        $('#logCourierType').val(d.courierType || 'N/A');
        $('#logCourierOrderId').val(d.courierOrderId || 'N/A');
        $('#logStatus').val(d.status || 'N/A');
        $('#logDate').val(d.createdAt ? new Date(d.createdAt).toLocaleString() : 'N/A');
        $('#logRequestData').val(d.requestData || 'N/A');
        $('#logResponseData').val(d.responseData || 'N/A');
        $('#logErrorMessage').val(d.errorMessage || 'N/A');
        $('#logDetailsModal').modal('show');
    });
});
</script>
@endpush
