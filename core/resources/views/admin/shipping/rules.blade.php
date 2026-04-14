@extends('admin.layouts.app')

@section('panel')
    {{-- Nav --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a href="{{ route('admin.shipping.index') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-th-large"></i> @lang('Hub')</a>
                <a href="{{ route('admin.shipping.zones.index') }}" class="btn btn-sm btn-outline-primary"><i class="las la-map-marked-alt"></i> @lang('Zones')</a>
                <a href="{{ route('admin.shipping.methods.index') }}" class="btn btn-sm btn-outline-primary"><i class="las la-shipping-fast"></i> @lang('Methods')</a>
                <span class="btn btn-sm btn-info text-white"><i class="las la-cog"></i> @lang('Rules')</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card b-radius--10 shadow-sm border">
                <div class="card-header border-bottom bg-white">
                    <h5 class="mb-0 text-dark fw-semibold">@lang('Shipping Rules & Charges')</h5>
                    <p class="text-muted small mb-0 mt-1">@lang('Free shipping min, COD & express charges')</p>
                </div>
                <div class="card-body bg-white">
                    <form action="{{ route('admin.shipping.rules.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-dark fw-medium">@lang('Free shipping minimum amount') ({{ __($general->cur_text) }})</label>
                                <input type="number" step="0.01" class="form-control" name="free_shipping_min_amount" value="{{ old('free_shipping_min_amount', $rule->free_shipping_min_amount) }}" min="0" placeholder="@lang('Leave empty to disable free shipping')">
                                <small class="text-muted">@lang('Orders above this amount get free shipping.')</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-dark fw-medium">@lang('Header top notice text')</label>
                                <input type="text" class="form-control" name="header_notice_text" value="{{ old('header_notice_text', $rule->header_notice_text ?? 'Cash on Delivery available nationwide') }}" maxlength="255" placeholder="@lang('e.g. Cash on Delivery available nationwide')">
                                <small class="text-muted">@lang('Shown on the storefront top header bar.')</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-medium">@lang('COD extra charge') ({{ __($general->cur_text) }})</label>
                                <input type="number" step="0.01" class="form-control" name="cod_extra_charge" value="{{ old('cod_extra_charge', $rule->cod_extra_charge ?? 0) }}" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-medium">@lang('Express delivery extra charge') ({{ __($general->cur_text) }})</label>
                                <input type="number" step="0.01" class="form-control" name="express_extra_charge" value="{{ old('express_extra_charge', $rule->express_extra_charge ?? 0) }}" min="0" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="international_enabled" value="0">
                                    <input type="checkbox" class="form-check-input" name="international_enabled" value="1" id="intl" @checked($rule->international_enabled ?? true)>
                                    <label class="form-check-label text-dark" for="intl">@lang('Enable international shipping')</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg text-white"><i class="las la-save"></i> @lang('Save Rules')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- What you can do --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border bg-white shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-dark fw-semibold mb-2">@lang('What you can do')</h6>
                    <ul class="mb-0 ps-3 text-secondary small" style="line-height:1.6;">
                        <li>Free shipping above order amount. Zone/area free shipping in Zones.</li>
                        <li>COD & express extra charges. International on/off.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
