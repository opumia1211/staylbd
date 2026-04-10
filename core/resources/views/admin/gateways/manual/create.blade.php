@extends('admin.layouts.app')

@section('panel')
    <div class="manual-create-page">
        {{-- Hero --}}
        <div class="card border-0 shadow-sm mb-4 manual-create-hero">
            <div class="card-body p-4 p-md-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="manual-hero-icon rounded-3 d-flex align-items-center justify-content-center bg--primary bg-opacity-10 flex-shrink-0">
                        <i class="las la-globe fs-2 text--primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold">@lang('Add payment from any country')</h6>
                        <p class="text-muted small mb-0">@lang('Set up any manual payment method with any currency. Accept deposits from Bangladesh, India, USA, UK, or any country—use any currency code (USD, EUR, GBP, BDT, INR, JPY, etc.) and symbol.')</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm manual-create-card">
            <div class="card-header bg-light border-0 py-3 px-4">
                <h5 class="mb-0 fw-bold"><i class="las la-plus-circle me-2 text--primary"></i>@lang('New Manual Gateway')</h5>
            </div>
            <form action="{{ route('admin.gateway.manual.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body px-4">
                    {{-- Basic Info --}}
                    <div class="manual-section mb-4">
                        <h6 class="manual-section-title text-muted mb-3"><i class="las la-info-circle me-1"></i>@lang('Basic Info')</h6>
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label fw-medium">@lang('Gateway Name')</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="@lang('e.g. Bank Transfer, bKash, Nagad, PayPal')" required/>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label fw-medium">@lang('Region / Country')</label>
                                <input type="text" class="form-control" name="region" value="{{ old('region') }}" placeholder="@lang('e.g. Bangladesh, India, Global')"/>
                                <small class="text-muted">@lang('Optional — for your reference')</small>
                            </div>
                            <div class="col-12 col-lg-4"></div>
                        </div>
                    </div>

                    {{-- Currency (any country) --}}
                    <div class="manual-section mb-4">
                        <h6 class="manual-section-title text-muted mb-3"><i class="las la-coins me-1"></i>@lang('Currency') <span class="fw-normal small">(@lang('Any country'))</span></h6>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-medium">@lang('Currency Code')</label>
                                <input type="text" name="currency" id="currencyCode" class="form-control" required value="{{ old('currency') }}" placeholder="USD, EUR, BDT, INR..." list="worldCurrencies"/>
                                <datalist id="worldCurrencies">
                                    <option value="USD"><option value="EUR"><option value="GBP"><option value="BDT"><option value="INR">
                                    <option value="JPY"><option value="CAD"><option value="AUD"><option value="SAR"><option value="AED">
                                    <option value="PKR"><option value="MYR"><option value="SGD"><option value="CNY"><option value="CHF">
                                </datalist>
                                <small class="text-muted">@lang('ISO or custom code')</small>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-medium">@lang('Symbol')</label>
                                <input type="text" name="symbol" id="currencySymbol" class="form-control" value="{{ old('symbol') }}" placeholder="$ € ৳ £ ¥"/>
                            </div>
                            <div class="col-12 col-md-5">
                                <label class="form-label fw-medium d-block">@lang('Quick select')</label>
                                <div class="manual-currency-chips d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary manual-currency-chip" data-currency="USD" data-symbol="$">USD $</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary manual-currency-chip" data-currency="EUR" data-symbol="€">EUR €</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary manual-currency-chip" data-currency="GBP" data-symbol="£">GBP £</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary manual-currency-chip" data-currency="BDT" data-symbol="৳">BDT ৳</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary manual-currency-chip" data-currency="INR" data-symbol="₹">INR ₹</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary manual-currency-chip" data-currency="JPY" data-symbol="¥">JPY ¥</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary manual-currency-chip" data-currency="CAD" data-symbol="C$">CAD</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary manual-currency-chip" data-currency="AUD" data-symbol="A$">AUD</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary manual-currency-chip" data-currency="SAR" data-symbol="﷼">SAR</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary manual-currency-chip" data-currency="AED" data-symbol="د.إ">AED</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary manual-currency-chip" data-currency="PKR" data-symbol="₨">PKR</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary manual-currency-chip" data-currency="CNY" data-symbol="¥">CNY</button>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-lg-4">
                                <label class="form-label fw-medium">@lang('Rate')</label>
                                <div class="input-group">
                                    <span class="input-group-text">1 {{ __($general->cur_text) }} =</span>
                                    <input type="number" step="any" class="form-control" name="rate" required value="{{ old('rate') }}"/>
                                    <span class="input-group-text currency_symbol_display">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Range & Charge --}}
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-lg-6">
                            <div class="card border manual-range-card h-100">
                                <div class="card-header bg-light border-0 py-2">
                                    <h6 class="mb-0 fw-semibold"><i class="las la-sliders-h me-1"></i>@lang('Amount Range')</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">@lang('Minimum Amount')</label>
                                        <div class="input-group">
                                            <input type="number" step="any" class="form-control" name="min_limit" required value="{{ old('min_limit') }}"/>
                                            <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label">@lang('Maximum Amount')</label>
                                        <div class="input-group">
                                            <input type="number" step="any" class="form-control" name="max_limit" required value="{{ old('max_limit') }}"/>
                                            <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="card border manual-charge-card h-100">
                                <div class="card-header bg-light border-0 py-2">
                                    <h6 class="mb-0 fw-semibold"><i class="las la-percent me-1"></i>@lang('Charge')</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">@lang('Fixed Charge')</label>
                                        <div class="input-group">
                                            <input type="number" step="any" class="form-control" name="fixed_charge" required value="{{ old('fixed_charge') }}"/>
                                            <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label">@lang('Percent Charge')</label>
                                        <div class="input-group">
                                            <input type="number" step="any" class="form-control" name="percent_charge" required value="{{ old('percent_charge', 0) }}"/>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Instruction --}}
                    <div class="manual-section mb-4">
                        <h6 class="manual-section-title text-muted mb-3"><i class="las la-info-circle me-1"></i>@lang('Deposit Instruction')</h6>
                        <textarea rows="6" class="form-control nicEdit" name="instruction" placeholder="@lang('Step-by-step instructions for the user on how to pay (account details, reference, etc.)...')">{{ old('instruction') }}</textarea>
                    </div>

                    {{-- User Data --}}
                    <div class="manual-section">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="manual-section-title text-muted mb-0"><i class="las la-th-list me-1"></i>@lang('User Data')</h6>
                            <button type="button" class="btn btn-sm btn--primary form-generate-btn"><i class="las la-plus me-1"></i>@lang('Add Field')</button>
                        </div>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row addedField g-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3 px-4">
                    <button type="submit" class="btn btn--primary px-4"><i class="las la-check me-1"></i>@lang('Create Gateway')</button>
                    <a href="{{ route('admin.gateway.manual.index') }}" class="btn btn-outline-secondary ms-2">@lang('Cancel')</a>
                </div>
            </form>
        </div>
    </div>

    <x-form-generator />
@endsection

@push('script')
<script>
    "use strict";
    var formGenerator = new FormGenerator();
</script>
<script src="{{ asset('assets/global/js/form_actions.js') }}"></script>
@endpush

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.gateway.manual.index') }}" />
@endpush

@push('script')
<script>
(function($) {
    "use strict";
    function updateSymbol() {
        var sym = $('#currencySymbol').val() || $('#currencyCode').val();
        $('.currency_symbol_display').text(sym || '—');
    }
    $('#currencyCode').on('input', updateSymbol);
    $('#currencySymbol').on('input', updateSymbol);
    $('.manual-currency-chip').on('click', function() {
        var c = $(this).data('currency'), s = $(this).data('symbol');
        $('#currencyCode').val(c);
        $('#currencySymbol').val(s || c);
        updateSymbol();
        $('.manual-currency-chip').removeClass('active btn--primary').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn--primary active');
    });
    @if(old('currency'))
        $('#currencyCode').trigger('input');
        @if(old('symbol'))
            $('#currencySymbol').trigger('input');
        @endif
    @endif
})(jQuery);
</script>
@endpush

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush
