@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <a href="{{ route('admin.orders.hub') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="las la-arrow-left"></i> @lang('Order Center')</a>
                <h5 class="mb-0 text-dark fw-bold">@lang('Fulfillment Queue')</h5>
                <p class="text-muted small mb-0 mt-1">@lang('Actionable orders, SLA overdue, returns — process faster.')</p>
            </div>
            <form action="{{ request()->url() }}" method="get" class="d-flex gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="@lang('Order # / customer')" value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-primary">@lang('Search')</button>
            </form>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'queue' ? 'active' : '' }}" href="{{ route('admin.orders.fulfillment', ['tab' => 'queue']) }}">
                @lang('To Process') <span class="badge bg-primary ms-1">{{ $counts['queue'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'sla' ? 'active' : '' }}" href="{{ route('admin.orders.fulfillment', ['tab' => 'sla']) }}">
                @lang('SLA Overdue') <span class="badge bg-danger ms-1">{{ $counts['sla'] }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'returns' ? 'active' : '' }}" href="{{ route('admin.orders.fulfillment', ['tab' => 'returns']) }}">
                @lang('Returns') <span class="badge bg-secondary ms-1">{{ $counts['returns'] }}</span>
            </a>
        </li>
    </ul>

    @if($tab === 'sla' && $sla['enabled'])
    <div class="alert alert-warning border-0 shadow-sm small mb-3">
        @lang('Pending > :h1 hours or unshipped confirmed/processing > :h2 hours.', ['h1' => $sla['pending_hours'], 'h2' => $sla['fulfillment_hours']])
        <a href="{{ route('admin.orders.automation.index') }}" class="ms-2">@lang('Adjust SLA')</a>
    </div>
    @endif

    <div class="card border shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>@lang('Order')</th>
                        <th>@lang('Customer')</th>
                        <th>@lang('Total')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Date')</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><code>{{ $order->order_no }}</code></td>
                        <td class="small">{{ $order->customer_display }}</td>
                        <td class="fw-semibold">{{ showAmount($order->total) }}</td>
                        <td>{!! $order->ordersBadge !!}</td>
                        <td class="small text-muted">{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.detail', $order->id) }}" class="btn btn-sm btn-outline-primary">@lang('Manage')</a>
                            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank">@lang('Invoice')</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">@lang('No orders in this queue.')</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="card-footer">{{ paginateLinks($orders) }}</div>
        @endif
    </div>
@endsection
