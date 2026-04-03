@php
    $gatewayParams = json_decode($method->gateway_parameters ?? '{}', true);
    $region = $gatewayParams['region'] ?? '';
@endphp
@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12 mb-3">
            <div class="alert alert-light border d-flex align-items-start gap-2">
                <i class="las la-globe fs-4 text--primary mt-1"></i>
                <div>
                    <strong>@lang('Global payment method')</strong><br>
                    <span class="small text-muted">@lang('You can use any currency code and symbol. Accept payment from any country. Edit details below.')</span>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="las la-pen me-2"></i>@lang('Edit Manual Gateway')</h6>
                </div>
                <form action="{{ route('admin.gateway.manual.update', $method->code) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="payment-method-item">
                            <div class="payment-method-body">
                                <h6 class="mb-3 text-muted">@lang('Basic Info')</h6>
                                <div class="row g-3">
                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label class="fw-medium">@lang('Gateway Name')</label>
                                            <input type="text" class="form-control" name="name" value="{{ $method->name }}" required/>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label class="fw-medium">@lang('Region / Country')</label>
                                            <input type="text" class="form-control" name="region" value="{{ $region }}" placeholder="@lang('e.g. Bangladesh, India, Global')"/>
                                            <small class="text-muted">@lang('Optional')</small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="fw-medium">@lang('Official Logo')</label>
                                            <p class="small text-muted mb-2">@lang('Upload the payment method official logo for the deposit page.')</p>
                                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                                @if($method->logo ?? null)
                                                    <div class="border rounded p-2 bg-light">
                                                        <img src="{{ getGatewayLogoUrl($method->alias, $method->logo) }}" alt="{{ __($method->name) }}" style="max-height: 48px; width: auto;" class="img-fluid"/>
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control form-control-sm w-auto" name="logo" accept="image/png,image/jpeg,image/jpg,image/webp"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12"></div>
                                    <div class="col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label class="fw-medium">@lang('Currency Code')</label>
                                            <input type="text" name="currency" id="currencyCode" class="form-control" value="{{ @$method->singleCurrency->currency }}" required placeholder="USD, EUR, BDT..."/>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-3">
                                        <div class="form-group">
                                            <label class="fw-medium">@lang('Symbol')</label>
                                            <input type="text" name="symbol" id="currencySymbol" class="form-control" value="{{ @$method->singleCurrency->symbol }}" placeholder="$ € ৳"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-5">
                                        <label class="fw-medium d-block">@lang('Quick select')</label>
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
                                    <div class="col-sm-12 col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label class="fw-medium">@lang('Rate')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">1 {{ __($general->cur_text) }} =</span>
                                                <input type="number" step="any" class="form-control" name="rate" value="{{ getAmount(@$method->singleCurrency->rate) }}" required/>
                                                <span class="currency_symbol input-group-text"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card border--primary mt-3">
                                            <h5 class="card-header bg--primary"><i class="las la-sliders-h me-1"></i>@lang('Range')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>@lang('Minimum Amount')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="form-control" name="min_limit" value="{{ getAmount(@$method->singleCurrency->min_amount) }}" required/>
                                                        <div class="input-group-text">{{ __($general->cur_text) }}</div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>@lang('Maximum Amount')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="form-control" name="max_limit" value="{{ getAmount(@$method->singleCurrency->max_amount) }}" required/>
                                                        <div class="input-group-text">{{ __($general->cur_text) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="card border--primary mt-3">
                                            <h5 class="card-header bg--primary"><i class="las la-percent me-1"></i>@lang('Charge')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>@lang('Fixed Charge')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="form-control" name="fixed_charge" value="{{ getAmount(@$method->singleCurrency->fixed_charge) }}" required/>
                                                        <div class="input-group-text">{{ __($general->cur_text) }}</div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>@lang('Percent Charge')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="form-control" name="percent_charge" value="{{ getAmount(@$method->singleCurrency->percent_charge) }}" required>
                                                        <div class="input-group-text">%</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="card border--primary mt-3">
                                            <h5 class="card-header bg--primary"><i class="las la-info-circle me-1"></i>@lang('Deposit Instruction')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <textarea rows="8" class="form-control border-radius-5 nicEdit" name="instruction">{{ __(@$method->description)  }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="card border--primary mt-3">
                                            <div class="card-header bg--primary d-flex justify-content-between align-items-center">
                                                <h5 class="text-white mb-0"><i class="las la-th-list me-1"></i>@lang('User Data')</h5>
                                                <button type="button" class="btn btn-sm btn-outline-light form-generate-btn"><i class="las la-plus"></i> @lang('Add New')</button>
                                            </div>
                                            <div class="card-body">
                                                <div class="row addedField">
                                                    @if($form)
                                                        @foreach($form->form_data as $formData)
                                                            <div class="col-md-4">
                                                                <div class="card border mb-3" id="{{ $loop->index }}">
                                                                    <input type="hidden" name="form_generator[is_required][]" value="{{ $formData->is_required }}">
                                                                    <input type="hidden" name="form_generator[extensions][]" value="{{ $formData->extensions }}">
                                                                    <input type="hidden" name="form_generator[options][]" value="{{ implode(',',$formData->options) }}">

                                                                    <div class="card-body">
                                                                        <div class="form-group">
                                                                            <label>@lang('Label')</label>
                                                                            <input type="text" name="form_generator[form_label][]" class="form-control" value="{{ $formData->name }}" readonly>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>@lang('Type')</label>
                                                                            <input type="text" name="form_generator[form_type][]" class="form-control" value="{{ $formData->type }}" readonly>
                                                                        </div>
                                                                        @php
                                                                            $jsonData = json_encode([
                                                                                'type'=>$formData->type,
                                                                                'is_required'=>$formData->is_required,
                                                                                'label'=>$formData->name,
                                                                                'extensions'=>explode(',',$formData->extensions) ?? 'null',
                                                                                'options'=>$formData->options,
                                                                                'old_id'=>'',
                                                                            ]);
                                                                        @endphp
                                                                        <div class="btn-group w-100">
                                                                            <button type="button" class="btn btn--primary editFormData" data-form_item="{{ $jsonData }}" data-update_id="{{ $loop->index }}"><i class="las la-pen"></i></button>
                                                                            <button type="button" class="btn btn--danger removeFormData"><i class="las la-times"></i></button>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 py-3">
                        <button type="submit" class="btn btn--primary px-4"><i class="las la-check me-1"></i>@lang('Update Gateway')</button>
                        <a href="{{ route('admin.gateway.manual.index') }}" class="btn btn-outline--secondary ms-2">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-form-generator />
@endsection

@push('script')
    <script>
        "use strict"
        var formGenerator = new FormGenerator();
        formGenerator.totalField = {{ $form ? count((array) $form->form_data) : 0 }}
    </script>

    <script src="{{ asset('assets/global/js/form_actions.js') }}"></script>
@endpush



@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.gateway.manual.index') }}" />
@endpush

@push('script')
    <script>

        (function ($) {
            "use strict";
            function updateSymbol() {
                var sym = $('#currencySymbol').val() || $('#currencyCode').val();
                $('.currency_symbol').text(sym || '—');
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
            updateSymbol();
        })(jQuery);

    </script>
@endpush
