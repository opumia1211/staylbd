@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12 mb-3">
            <div class="alert alert-light border d-flex align-items-start gap-2">
                <i class="las la-external-link-alt fs-4 text--primary mt-1"></i>
                <div>
                    <strong>@lang('External payment website')</strong><br>
                    <span class="small text-muted">@lang('User will pay on this site; after payment the external site should redirect back to your success URL. Use placeholders in Redirect URL: {amount}, {order_id}, {trx}, {user_id}.')</span>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="las la-plus me-2"></i>@lang('Add External Gateway')</h6>
                </div>
                <form action="{{ route('admin.gateway.autopay.external.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <h6 class="text-muted mb-3">@lang('Basic & Redirect')</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">@lang('Gateway Name')</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required placeholder="e.g. PayNow, ExternalPay"/>
                            </div>
                            <div class="col-12">
                                <label class="form-label">@lang('Redirect URL')</label>
                                <input type="url" class="form-control" name="redirect_url" value="{{ old('redirect_url') }}" required placeholder="https://other-payment-site.com/pay?amount={amount}&order={order_id}&trx={trx}"/>
                                <small class="text-muted">@lang('Placeholders: {amount}, {order_id}, {trx}, {user_id}')</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Success return path')</label>
                                <input type="text" class="form-control" name="success_path" value="{{ old('success_path', '/user/deposit/autopay-return') }}" placeholder="/user/deposit/autopay-return"/>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Cancel return path')</label>
                                <input type="text" class="form-control" name="cancel_path" value="{{ old('cancel_path', '/user/orders') }}" placeholder="/user/orders"/>
                            </div>
                        </div>
                        <h6 class="text-muted mb-3">@lang('Currency & limits')</h6>
                        <div class="row g-3">
                            <div class="col-md-3"><label class="form-label">@lang('Currency')</label><input type="text" class="form-control" name="currency" value="{{ old('currency', 'USD') }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Symbol')</label><input type="text" class="form-control" name="symbol" value="{{ old('symbol', '$') }}"/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Rate')</label><input type="number" step="any" class="form-control" name="rate" value="{{ old('rate') }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Min')</label><input type="number" step="any" class="form-control" name="min_limit" value="{{ old('min_limit') }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Max')</label><input type="number" step="any" class="form-control" name="max_limit" value="{{ old('max_limit') }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Fixed charge')</label><input type="number" step="any" class="form-control" name="fixed_charge" value="{{ old('fixed_charge', 0) }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Percent charge')</label><input type="number" step="any" class="form-control" name="percent_charge" value="{{ old('percent_charge', 0) }}" required/></div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 py-3">
                        <button type="submit" class="btn btn--primary">@lang('Add Gateway')</button>
                        <a href="{{ route('admin.gateway.autopay.index') }}" class="btn btn-outline--secondary ms-2">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.gateway.autopay.index') }}" />
@endpush
