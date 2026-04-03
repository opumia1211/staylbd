@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12 mb-3">
            <div class="alert alert-light border d-flex align-items-start gap-2">
                <i class="las la-mobile-alt fs-4 text--primary mt-1"></i>
                <div>
                    <strong>@lang('App / SMS message gateway')</strong><br>
                    <span class="small text-muted">@lang('When user pays via bKash/Nagad etc., your Android (or other) app can capture the payment SMS and send it to this website. Once the message is received and matched to a pending payment, the user will see success automatically. Set the API URL and key in your app.')</span>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="las la-plus me-2"></i>@lang('Add Message Gateway')</h6>
                </div>
                <form action="{{ route('admin.gateway.autopay.message.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <h6 class="text-muted mb-3">@lang('Basic')</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">@lang('Gateway Name')</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required placeholder="e.g. bKash SMS, Nagad App"/>
                            </div>
                            <div class="col-md-3"><label class="form-label">@lang('Currency')</label><input type="text" class="form-control" name="currency" value="{{ old('currency', 'BDT') }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Symbol')</label><input type="text" class="form-control" name="symbol" value="{{ old('symbol', '৳') }}"/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Rate')</label><input type="number" step="any" class="form-control" name="rate" value="{{ old('rate', 1) }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Min')</label><input type="number" step="any" class="form-control" name="min_limit" value="{{ old('min_limit') }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Max')</label><input type="number" step="any" class="form-control" name="max_limit" value="{{ old('max_limit') }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Fixed charge')</label><input type="number" step="any" class="form-control" name="fixed_charge" value="{{ old('fixed_charge', 0) }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Percent charge')</label><input type="number" step="any" class="form-control" name="percent_charge" value="{{ old('percent_charge', 0) }}" required/></div>
                        </div>
                        <h6 class="text-muted mb-3">@lang('Instructions & parsing (optional)')</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label">@lang('Deposit instruction (shown to user)')</label>
                                <textarea class="form-control" name="instructions" rows="3" placeholder="@lang('Send money to this number. Payment will be confirmed automatically when we receive the transaction.')">{{ old('instructions') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Amount regex')</label>
                                <input type="text" class="form-control" name="amount_regex" value="{{ old('amount_regex') }}" placeholder="e.g. /(\d+\.?\d*)\s*BDT/"/>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Transaction ID regex')</label>
                                <input type="text" class="form-control" name="trx_regex" value="{{ old('trx_regex') }}" placeholder="e.g. /TrxID[:\s]*(\w+)/"/>
                            </div>
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
