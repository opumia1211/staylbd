@extends('admin.layouts.app')

@section('panel')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-1">@lang('Product Report')</h4>
        <p class="text-muted mb-0">@lang('Summary, best sellers and stock report.')</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.report.product') }}" method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="col-md-2">
                <label class="form-label">@lang('Date From')</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">@lang('Date To')</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn--primary"><i class="las la-filter"></i> @lang('Filter')</button>
                <a href="{{ route('admin.report.product.export') }}?date_from={{ $dateFrom->format('Y-m-d') }}&date_to={{ $dateTo->format('Y-m-d') }}" class="btn btn-outline--info"><i class="las la-file-csv"></i> @lang('Export CSV')</a>
            </div>
        </form>
    </div>
</div>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'summary' ? 'active' : '' }}" href="{{ route('admin.report.product', ['tab' => 'summary', 'date_from' => $dateFrom->format('Y-m-d'), 'date_to' => $dateTo->format('Y-m-d')]) }}">@lang('Summary')</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'best' ? 'active' : '' }}" href="{{ route('admin.report.product', ['tab' => 'best', 'date_from' => $dateFrom->format('Y-m-d'), 'date_to' => $dateTo->format('Y-m-d')]) }}">@lang('Best Seller')</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'stock' ? 'active' : '' }}" href="{{ route('admin.report.product', ['tab' => 'stock']) }}">@lang('Stock Report')</a>
    </li>
</ul>

@if($tab === 'summary')
<div class="row">
    <div class="col-md-3 col-6 mb-3">
        <div class="card b-radius--10"><div class="card-body py-3"><span class="text-muted small">@lang('Total Products')</span><h4 class="mb-0">{{ $summary['total_products'] }}</h4></div></div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card b-radius--10 border--success"><div class="card-body py-3"><span class="text-muted small">@lang('Active')</span><h4 class="mb-0">{{ $summary['active_products'] }}</h4></div></div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card b-radius--10 border--warning"><div class="card-body py-3"><span class="text-muted small">@lang('Low Stock')</span><h4 class="mb-0">{{ $summary['low_stock'] }}</h4></div></div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card b-radius--10 border--danger"><div class="card-body py-3"><span class="text-muted small">@lang('Out of Stock')</span><h4 class="mb-0">{{ $summary['out_of_stock'] }}</h4></div></div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card b-radius--10"><div class="card-body py-3"><span class="text-muted small">@lang('Orders in period')</span><h4 class="mb-0">{{ $summary['total_orders'] }}</h4></div></div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card b-radius--10 border--success"><div class="card-body py-3"><span class="text-muted small">@lang('Delivered')</span><h4 class="mb-0">{{ $summary['delivered_orders'] }}</h4></div></div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card b-radius--10 border--primary"><div class="card-body py-3"><span class="text-muted small">@lang('Revenue')</span><h4 class="mb-0">{{ $general->cur_sym }}{{ number_format($summary['revenue'], 2) }}</h4></div></div>
    </div>
</div>
@endif

@if($tab === 'best')
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table--light style--two mb-0">
                <thead><tr><th>#</th><th>@lang('Product')</th><th>@lang('Quantity Sold')</th><th>@lang('Total Sales')</th></tr></thead>
                <tbody>
                    @forelse($bestSellers as $idx => $row)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $row->product ? $row->product->name : '—' }}</td>
                        <td>{{ $row->total_qty ?? 0 }}</td>
                        <td>{{ $general->cur_sym }}{{ number_format($row->total_sales ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">{{ $emptyMessage }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@if($tab === 'stock')
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table--light style--two mb-0">
                <thead><tr><th>@lang('SKU')</th><th>@lang('Product')</th><th>@lang('Quantity')</th><th>@lang('Price')</th></tr></thead>
                <tbody>
                    @forelse($stockReport as $p)
                    <tr class="{{ $p->quantity <= 0 ? 'table-danger' : ($p->quantity <= 5 ? 'table-warning' : '') }}">
                        <td>{{ $p->product_sku ?? '—' }}</td>
                        <td>{{ $p->name }}</td>
                        <td><strong>{{ $p->quantity }}</strong></td>
                        <td>{{ $general->cur_sym }}{{ number_format($p->price ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">{{ $emptyMessage }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
