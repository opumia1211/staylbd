@extends('admin.layouts.app')

@section('panel')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-1">@lang('Ad Source Report')</h4>
        <p class="text-muted mb-0">@lang('See which orders came from Facebook, Google, TikTok or direct.')</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.report.ad_source') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">@lang('Date From')</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">@lang('Date To')</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn--primary"><i class="las la-filter"></i> @lang('Filter')</button>
                <a href="{{ route('admin.report.ad_source.export') }}?date_from={{ $dateFrom->format('Y-m-d') }}&date_to={{ $dateTo->format('Y-m-d') }}" class="btn btn-outline--info"><i class="las la-file-csv"></i> @lang('Export CSV')</a>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card b-radius--10 border--primary">
            <div class="card-body py-3">
                <span class="text-muted small">@lang('Total Orders')</span>
                <h4 class="mb-0">{{ $totalOrders }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card b-radius--10 border--success">
            <div class="card-body py-3">
                <span class="text-muted small">@lang('Delivered')</span>
                <h4 class="mb-0">{{ $deliveredOrders }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card b-radius--10 border--info">
            <div class="card-body py-3">
                <span class="text-muted small">@lang('Total Revenue')</span>
                <h4 class="mb-0">{{ $general->cur_sym }}{{ number_format($totalRevenue, 2) }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">@lang('Orders by Ad Source')</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table--light style--two mb-0">
                <thead>
                    <tr>
                        <th>@lang('Source')</th>
                        <th>@lang('Orders')</th>
                        <th>@lang('Delivered')</th>
                        <th>@lang('Revenue')</th>
                        <th>@lang('Success %')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bySource as $row)
                    <tr>
                        <td><strong>{{ ucfirst($row->source ?? 'direct') }}</strong></td>
                        <td>{{ $row->order_count ?? 0 }}</td>
                        <td>{{ $row->delivered_count ?? 0 }}</td>
                        <td>{{ $general->cur_sym }}{{ number_format($row->revenue ?? 0, 2) }}</td>
                        <td>{{ $row->order_count > 0 ? number_format(($row->delivered_count / $row->order_count) * 100, 1) : 0 }}%</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">{{ $emptyMessage }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5 class="mb-0">@lang('Orders in date range')</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table--light style--two mb-0">
                <thead>
                    <tr>
                        <th>@lang('Order')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Ad Source')</th>
                        <th>@lang('Customer')</th>
                        <th>@lang('Total')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ordersList as $o)
                    <tr>
                        <td><strong>{{ $o->order_no }}</strong></td>
                        <td>{{ $o->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $o->ad_source ?? 'direct' }}</td>
                        <td>{{ $o->user ? $o->user->username : '—' }}<br><small>{{ $o->user->mobile ?? '—' }}</small></td>
                        <td>{{ $general->cur_sym }}{{ number_format($o->total, 2) }}</td>
                        <td><span class="badge badge--{{ $o->order_status == 4 ? 'success' : 'warning' }}">{{ $o->order_status == 4 ? __('Delivered') : __('Other') }}</span></td>
                        <td><a href="{{ route('admin.orders.detail', $o->id) }}" class="btn btn-sm btn-outline--primary">@lang('View')</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">{{ $emptyMessage }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ordersList->hasPages())
        <div class="card-footer py-3">{{ paginateLinks($ordersList) }}</div>
        @endif
    </div>
</div>
@endsection
