@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a href="{{ route('admin.payment.gateways.hub') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-arrow-left"></i> @lang('Payment Gateways')</a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-0 text-dark fw-bold">@lang('Payment Analytics')</h5>
            <p class="text-muted small mb-0 mt-1">@lang('Total online payments, success rate, gateway-wise revenue.')</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Total Online Payments')</span>
                    <span class="fw-bold fs-4 text--primary">{{ $general->cur_sym }}{{ number_format($totalOnline ?? 0, 2) }}</span>
                    <small class="d-block text-muted mt-1">{{ $totalCount ?? 0 }} @lang('transactions')</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Success Rate')</span>
                    <span class="fw-bold fs-4 text-success">{{ $successRate ?? 0 }}%</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Failed Rate')</span>
                    <span class="fw-bold fs-4 text-danger">{{ $failedRate ?? 0 }}%</span>
                    <small class="d-block text-muted mt-1">{{ $failedCount ?? 0 }} @lang('failed')</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Pending')</span>
                    <span class="fw-bold fs-4 text-warning">{{ $pendingCount ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 text-dark fw-semibold">@lang('Revenue by Gateway')</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Gateway')</th>
                                    <th>@lang('Transactions')</th>
                                    <th>@lang('Total Amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gatewayWise ?? [] as $row)
                                    <tr>
                                        <td>{{ $gatewayNames[$row->method_code] ?? 'Code ' . $row->method_code }}</td>
                                        <td>{{ $row->count }}</td>
                                        <td>{{ $general->cur_sym }}{{ number_format($row->total ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">@lang('No data yet.')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
