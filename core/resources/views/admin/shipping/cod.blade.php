@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a href="{{ route('admin.shipping.index') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-th-large"></i> @lang('Hub')</a>
                <a href="{{ route('admin.shipping.rules.index') }}" class="btn btn-sm btn-outline-info text-dark"><i class="las la-cog"></i> @lang('Rules')</a>
                <span class="btn btn-sm btn-warning text-dark"><i class="las la-money-bill-wave"></i> @lang('COD Settings')</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10">
            <div class="card b-radius--10 shadow-sm border">
                <div class="card-header border-bottom bg-white">
                    <h5 class="mb-0 text-dark fw-semibold">@lang('Cash on Delivery — Eligibility & Charge')</h5>
                    <p class="text-muted small mb-0 mt-1">@lang('Min/max order, charge type, free COD above, zone & product control')</p>
                </div>
                <div class="card-body bg-white">
                    <form action="{{ route('admin.shipping.cod.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="cod_enabled" value="0">
                                    <input type="checkbox" class="form-check-input" name="cod_enabled" value="1" id="cod_enabled" @checked($cod->cod_enabled ?? true)>
                                    <label class="form-check-label text-dark fw-medium" for="cod_enabled">@lang('Enable COD globally')</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-medium">@lang('Minimum order for COD') ({{ __($general->cur_text) }})</label>
                                <input type="number" step="0.01" class="form-control" name="cod_min_order" value="{{ old('cod_min_order', $cod->cod_min_order ?? 0) }}" min="0" placeholder="0 = no min">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-medium">@lang('Maximum order for COD') ({{ __($general->cur_text) }})</label>
                                <input type="number" step="0.01" class="form-control" name="cod_max_order" value="{{ old('cod_max_order', $cod->cod_max_order ?? 0) }}" min="0" placeholder="0 = no max (e.g. 20000)">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-dark fw-medium">@lang('COD charge type')</label>
                                <select class="form-select" name="cod_charge_type">
                                    <option value="1" @selected(($cod->cod_charge_type ?? 1) == 1)>@lang('Flat amount')</option>
                                    <option value="2" @selected(($cod->cod_charge_type ?? 1) == 2)>@lang('Percentage of order')</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-medium">@lang('COD charge value') ({{ __($general->cur_text) }} or %)</label>
                                <input type="number" step="0.01" class="form-control" name="cod_charge_value" value="{{ old('cod_charge_value', $cod->cod_charge_value ?? 0) }}" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-medium">@lang('Free COD above order') ({{ __($general->cur_text) }})</label>
                                <input type="number" step="0.01" class="form-control" name="cod_free_above" value="{{ old('cod_free_above', $cod->cod_free_above ?? 0) }}" min="0" placeholder="0 = disabled">
                            </div>
                            <hr class="my-3">
                            <div class="col-12">
                                <h6 class="text-dark fw-semibold mb-2">@lang('Fraud & verification')</h6>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="cod_otp_required" value="0">
                                    <input type="checkbox" class="form-check-input" name="cod_otp_required" value="1" id="cod_otp" @checked($cod->cod_otp_required ?? false)>
                                    <label class="form-check-label text-dark" for="cod_otp">@lang('Require OTP verification at checkout for COD')</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark">@lang('OTP expire (minutes)')</label>
                                <input type="number" class="form-control" name="cod_otp_expire_minutes" value="{{ old('cod_otp_expire_minutes', $cod->cod_otp_expire_minutes ?? 10) }}" min="5" max="60">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark">@lang('Auto-cancel unverified COD (hours)')</label>
                                <input type="number" class="form-control" name="cod_auto_cancel_hours" value="{{ old('cod_auto_cancel_hours', $cod->cod_auto_cancel_hours ?? 24) }}" min="1" max="168">
                            </div>
                            <hr class="my-3">
                            <div class="col-12">
                                <h6 class="text-dark fw-semibold mb-2">@lang('Smart restriction')</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark">@lang('Disable COD after N failed deliveries')</label>
                                <input type="number" class="form-control" name="cod_failed_disable_count" value="{{ old('cod_failed_disable_count', $cod->cod_failed_disable_count ?? 2) }}" min="0" max="10">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark">@lang('New customer max COD order') ({{ __($general->cur_text) }})</label>
                                <input type="number" step="0.01" class="form-control" name="cod_new_customer_max" value="{{ old('cod_new_customer_max', $cod->cod_new_customer_max ?? 0) }}" min="0" placeholder="0 = use max above">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg text-white"><i class="las la-save"></i> @lang('Save COD Settings')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border bg-white shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-dark fw-semibold mb-2">@lang('What you can do')</h6>
                    <ul class="mb-0 ps-3 text-secondary small" style="line-height:1.6;">
                        <li>@lang('Enable/disable COD globally; set min & max order value.')</li>
                        <li>@lang('Flat or % COD charge; free COD above a certain order amount.')</li>
                        <li>@lang('Per-zone COD: disable in Shipping Zones (edit zone → COD enabled).')</li>
                        <li>@lang('Per-product: disable COD on product edit (COD disabled).')</li>
                        <li>@lang('OTP at checkout and auto-cancel unverified orders.')</li>
                        <li>@lang('Blacklist: Admin → COD Blacklist (mobile / address / IP).')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
