@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-0 text-dark fw-bold">@lang('Payment Gateways')</h5>
            <p class="text-muted small mb-0 mt-1">@lang('Manage gateways, deposits and view payment analytics.')</p>
            <p class="text-info small mb-0 mt-2">@lang('This is the hub: click a card below to open Automatic Gateways, Manual, Autopay, Deposits list, or Payment Analytics.')</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="{{ route('admin.gateway.automatic.index') }}" class="text-decoration-none d-block h-100">
                <div class="card border shadow-sm h-100 gateway-hub-card bg-white">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 p-3 bg-primary text-white"><i class="las la-credit-card fs-2"></i></div>
                            <span class="badge bg-primary">{{ $automaticCount ?? 0 }}</span>
                        </div>
                        <h6 class="card-title mb-2 text-dark fw-semibold">@lang('Automatic Gateways')</h6>
                        <p class="text-secondary small mb-0 flex-grow-1">@lang('bKash, Nagad, Stripe, PayPal — API keys, logo, fee, enable/disable')</p>
                        <span class="mt-3 text-primary small fw-semibold">@lang('Open') <i class="las la-arrow-right ms-1"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.gateway.manual.index') }}" class="text-decoration-none d-block h-100">
                <div class="card border shadow-sm h-100 gateway-hub-card bg-white">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 p-3 bg-success text-white"><i class="las la-university fs-2"></i></div>
                            <span class="badge bg-success">{{ $manualCount ?? 0 }}</span>
                        </div>
                        <h6 class="card-title mb-2 text-dark fw-semibold">@lang('Manual / Bank Transfer')</h6>
                        <p class="text-secondary small mb-0 flex-grow-1">@lang('Bank details, instructions — manual confirm')</p>
                        <span class="mt-3 small fw-semibold text-success">@lang('Open') <i class="las la-arrow-right ms-1"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.gateway.autopay.index') }}" class="text-decoration-none d-block h-100">
                <div class="card border shadow-sm h-100 gateway-hub-card bg-white">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 p-3 bg-info text-dark"><i class="las la-external-link-alt fs-2"></i></div>
                            <span class="badge bg-info text-dark">{{ $autopayCount ?? 0 }}</span>
                        </div>
                        <h6 class="card-title mb-2 text-dark fw-semibold">@lang('Autopay (External / Message)')</h6>
                        <p class="text-secondary small mb-0 flex-grow-1">@lang('Redirect or app/SMS confirmation')</p>
                        <span class="mt-3 small fw-semibold text-info">@lang('Open') <i class="las la-arrow-right ms-1"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.deposit.list') }}" class="text-decoration-none d-block h-100">
                <div class="card border shadow-sm h-100 gateway-hub-card bg-white">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 p-3 bg-warning text-dark"><i class="las la-list fs-2"></i></div>
                            <span class="badge bg-warning text-dark">{{ $totalPayments ?? 0 }} @lang('paid')</span>
                        </div>
                        <h6 class="card-title mb-2 text-dark fw-semibold">@lang('Deposits & Payments')</h6>
                        <p class="text-secondary small mb-0 flex-grow-1">@lang('View all deposits, pending, approved, rejected')</p>
                        <span class="mt-3 small fw-semibold text-warning">@lang('Open') <i class="las la-arrow-right ms-1"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.payment.analytics') }}" class="text-decoration-none d-block h-100">
                <div class="card border shadow-sm h-100 gateway-hub-card bg-white">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 p-3 bg-secondary text-white"><i class="las la-chart-line fs-2"></i></div>
                            <span class="badge bg-secondary">@lang('Analytics')</span>
                        </div>
                        <h6 class="card-title mb-2 text-dark fw-semibold">@lang('Payment Analytics')</h6>
                        <p class="text-secondary small mb-0 flex-grow-1">@lang('Success rate, gateway-wise revenue, failed rate')</p>
                        <span class="mt-3 small fw-semibold text-secondary">@lang('Open') <i class="las la-arrow-right ms-1"></i></span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border bg-light">
                <div class="card-body py-3 d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('admin.gateway.automatic.index') }}" class="btn btn-sm btn-outline-primary"><i class="las la-credit-card"></i> @lang('Automatic')</a>
                    <a href="{{ route('admin.gateway.manual.index') }}" class="btn btn-sm btn-outline-success"><i class="las la-university"></i> @lang('Manual')</a>
                    <a href="{{ route('admin.gateway.autopay.index') }}" class="btn btn-sm btn-outline-info text-dark"><i class="las la-external-link-alt"></i> @lang('Autopay')</a>
                    <a href="{{ route('admin.deposit.list') }}" class="btn btn-sm btn-outline-warning text-dark"><i class="las la-list"></i> @lang('Deposits')</a>
                    <a href="{{ route('admin.payment.analytics') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-chart-line"></i> @lang('Analytics')</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush
