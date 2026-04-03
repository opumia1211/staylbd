@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="las la-pen me-2"></i>@lang('Edit External Gateway')</h6>
                </div>
                <form action="{{ route('admin.gateway.autopay.external.update', $method->code) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">@lang('Gateway Name')</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $method->name) }}" required/>
                            </div>
                            <div class="col-12">
                                <label class="form-label">@lang('Official Logo')</label>
                                <p class="small text-muted mb-2">@lang('Upload the payment method official logo for the deposit page.')</p>
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    @if($method->logo ?? null)
                                        <div class="border rounded p-2 bg-light">
                                            <img src="{{ getGatewayLogoUrl($method->alias, $method->logo) }}" alt="{{ $method->name }}" style="max-height: 48px; width: auto;" class="img-fluid"/>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control form-control-sm w-auto" name="logo" accept="image/png,image/jpeg,image/jpg,image/webp"/>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">@lang('Redirect URL')</label>
                                <input type="url" class="form-control" name="redirect_url" value="{{ old('redirect_url', $params['redirect_url'] ?? '') }}" required/>
                                <small class="text-muted">@lang('Placeholders: {amount}, {order_id}, {trx}, {user_id}')</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Success return path')</label>
                                <input type="text" class="form-control" name="success_path" value="{{ old('success_path', $params['success_path'] ?? '/user/deposit/autopay-return') }}"/>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Cancel return path')</label>
                                <input type="text" class="form-control" name="cancel_path" value="{{ old('cancel_path', $params['cancel_path'] ?? '/user/orders') }}"/>
                            </div>
                        </div>
                        @php $c = $method->singleCurrency; @endphp
                        <div class="row g-3">
                            <div class="col-md-3"><label class="form-label">@lang('Currency')</label><input type="text" class="form-control" name="currency" value="{{ old('currency', $c->currency ?? 'USD') }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Symbol')</label><input type="text" class="form-control" name="symbol" value="{{ old('symbol', $c->symbol ?? '$') }}"/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Rate')</label><input type="number" step="any" class="form-control" name="rate" value="{{ old('rate', $c->rate ?? 1) }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Min')</label><input type="number" step="any" class="form-control" name="min_limit" value="{{ old('min_limit', $c->min_amount ?? 0) }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Max')</label><input type="number" step="any" class="form-control" name="max_limit" value="{{ old('max_limit', $c->max_amount ?? 0) }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Fixed charge')</label><input type="number" step="any" class="form-control" name="fixed_charge" value="{{ old('fixed_charge', $c->fixed_charge ?? 0) }}" required/></div>
                            <div class="col-md-2"><label class="form-label">@lang('Percent charge')</label><input type="number" step="any" class="form-control" name="percent_charge" value="{{ old('percent_charge', $c->percent_charge ?? 0) }}" required/></div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 py-3">
                        <button type="submit" class="btn btn--primary">@lang('Update')</button>
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
