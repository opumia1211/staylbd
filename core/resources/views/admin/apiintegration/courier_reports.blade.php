@extends('admin.layouts.app')

@section('panel')
{{-- Date filter --}}
<div class="card b-radius--10 border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="get" action="{{ route('admin.api.courier.reports') }}" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-0">@lang('From Date')</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? now()->subDays(30)->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-0">@lang('To Date')</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? now()->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn--primary btn-sm"><i class="las la-filter"></i> @lang('Apply')</button>
            </div>
            <div class="col-md-2 ms-md-auto">
                <a href="{{ route('admin.api.courier.reports.export') }}?date_from={{ urlencode($dateFrom ?? '') }}&date_to={{ urlencode($dateTo ?? '') }}" class="btn btn--success btn-sm"><i class="las la-file-csv"></i> @lang('Export CSV')</a>
            </div>
        </form>
    </div>
</div>

{{-- Stats: Total, Success, Failed, Pending, Returns, Paid, Unpaid --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--primary rounded me-2 p-2"><i class="las la-shipping-fast text-white"></i></div>
                <div><h6 class="text-muted mb-0 small">@lang('Total')</h6><h5 class="mb-0">{{ (string)($totalOrders ?? 0) }}</h5></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--success rounded me-2 p-2"><i class="las la-check-circle text-white"></i></div>
                <div><h6 class="text-muted mb-0 small">@lang('Success')</h6><h5 class="mb-0">{{ (string)($successfulOrders ?? 0) }}</h5></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--danger rounded me-2 p-2"><i class="las la-times-circle text-white"></i></div>
                <div><h6 class="text-muted mb-0 small">@lang('Failed')</h6><h5 class="mb-0">{{ (string)($failedOrders ?? 0) }}</h5></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--warning rounded me-2 p-2"><i class="las la-clock text-white"></i></div>
                <div><h6 class="text-muted mb-0 small">@lang('Pending')</h6><h5 class="mb-0">{{ (string)($pendingOrders ?? 0) }}</h5></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--dark rounded me-2 p-2"><i class="las la-undo text-white"></i></div>
                <div><h6 class="text-muted mb-0 small">@lang('Returns')</h6><h5 class="mb-0">{{ (string)($returnCount ?? 0) }}</h5></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--info rounded me-2 p-2"><i class="las la-percentage text-white"></i></div>
                <div><h6 class="text-muted mb-0 small">@lang('Success Rate')</h6><h5 class="mb-0">{{ ($totalOrders ?? 0) > 0 ? round((($successfulOrders ?? 0) / ($totalOrders ?: 1)) * 100, 1) : 0 }}%</h5></div>
            </div>
        </div>
    </div>
</div>

{{-- Payment summary (Paid / Unpaid orders in shipped) --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--success rounded me-3 p-2"><i class="las la-wallet text-white"></i></div>
                <div>
                    <h6 class="text-muted mb-0 small">@lang('Paid (Prepaid)')</h6>
                    <h4 class="mb-0">{{ (string)($paidCount ?? 0) }}</h4>
                    <span class="small text-muted">@lang('Shipments with order paid')</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--warning rounded me-3 p-2"><i class="las la-money-bill text-white"></i></div>
                <div>
                    <h6 class="text-muted mb-0 small">@lang('Unpaid (COD)')</h6>
                    <h4 class="mb-0">{{ (string)($unpaidCount ?? 0) }}</h4>
                    <span class="small text-muted">@lang('Shipments with order unpaid')</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- By Courier Type --}}
    <div class="col-lg-6">
        <div class="card b-radius--10 border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">@lang('By Courier Type')</h5>
                <span class="badge badge--primary">{{ $dateFrom ?? '' }} — {{ $dateTo ?? '' }}</span>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @forelse($byCourierType ?? [] as $cType => $count)
                    <div class="col-6">
                        <div class="d-flex justify-content-between align-items-center p-3 rounded border">
                            <span class="fw-bold text--primary">{{ ucfirst($cType) }}</span>
                            <span class="badge badge--primary">{{ (string)$count }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="col-12"><p class="text-muted mb-0 small">@lang('No data in this date range.')</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    {{-- Daily Stats --}}
    <div class="col-lg-6">
        <div class="card b-radius--10 border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h5 class="card-title mb-0">@lang('Daily Shipments')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                    <table class="table table--light table-sm mb-0">
                        <thead><tr><th>@lang('Date')</th><th class="text-end">@lang('Orders')</th></tr></thead>
                        <tbody>
                            @forelse($dailyStats as $stat)
                            <tr>
                                <td>{{ $stat->date ? \Carbon\Carbon::parse($stat->date)->format('M d, Y') : '—' }}</td>
                                <td class="text-end"><span class="badge badge--primary">{{ (string)($stat->total ?? 0) }}</span></td>
                            </tr>
                            @empty
                            <tr><td class="text-muted text-center" colspan="2">@lang('No data')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.api.courier.manage') }}" class="btn btn-sm btn-outline--primary"><i class="las la-cog"></i> @lang('Settings')</a>
    <a href="{{ route('admin.api.courier.logs') }}" class="btn btn-sm btn-outline--info"><i class="las la-list-alt"></i> @lang('Logs')</a>
@endpush
