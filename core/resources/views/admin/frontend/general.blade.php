@extends('admin.layouts.app')
@section('panel')
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">@lang('General Settings')</h4>
            <p class="text-muted mb-0">@lang('Site identity, currency, timezone, theme and E-commerce & Ads tracking.')</p>
        </div>
    </div>

    {{-- Quick links (Reports & Tracking) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0"><i class="las la-link me-2"></i>@lang('Reports & Automation')</h6>
                </div>
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap gap-2">
                        @if(Route::has('admin.report.revenue_profit'))
                            <a href="{{ route('admin.report.revenue_profit') }}" class="btn btn-sm btn-outline-primary"><i class="las la-coins me-1"></i>@lang('Revenue & Profit')</a>
                        @endif
                        @if(Route::has('admin.report.employee_performance'))
                            <a href="{{ route('admin.report.employee_performance') }}" class="btn btn-sm btn-outline-primary"><i class="las la-user-tie me-1"></i>@lang('Employee Performance')</a>
                        @endif
                        <a href="{{ route('admin.report.ad_source') }}" class="btn btn-sm btn-outline-primary"><i class="las la-chart-pie me-1"></i>@lang('Ad Source Report')</a>
                        <a href="{{ route('admin.report.product') }}" class="btn btn-sm btn-outline-primary"><i class="las la-box me-1"></i>@lang('Product Report')</a>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-shopping-cart me-1"></i>@lang('Orders')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Site & Store Settings --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg--primary py-3">
                    <h5 class="mb-0 text-white d-flex align-items-center">
                        <i class="las la-cog me-2"></i>
                        @lang('Site & Store Settings')
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.frontend.sections.general.update') }}" method="POST" id="generalSettingsForm">
                        @csrf
                        {{-- Site Identity --}}
                        <div class="mb-4">
                            <h6 class="text-uppercase mb-3 text-muted border-bottom pb-2">
                                <i class="las la-globe me-1"></i> @lang('Site Identity')
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold">@lang('Site Title') <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="las la-heading text--primary"></i></span>
                                        <input class="form-control" type="text" name="site_name" required value="{{ $general->site_name ?? '' }}" placeholder="@lang('Your site name')" maxlength="40">
                                    </div>
                                    <small class="text-muted">@lang('Shown in browser tab and across the site')</small>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold">@lang('Timezone')</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="las la-clock text--primary"></i></span>
                                        <select class="form-select select2-basic" name="timezone">
                                            @foreach ($timezones ?? [] as $timezone)
                                                <option value="'{{ @$timezone }}'">{{ __($timezone) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Currency & Pricing --}}
                        <div class="mb-4">
                            <h6 class="text-uppercase mb-3 text-muted border-bottom pb-2">
                                <i class="las la-coins me-1"></i> @lang('Currency & Pricing')
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold">@lang('Currency Name') <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="las la-dollar-sign text--primary"></i></span>
                                        <input class="form-control" type="text" name="cur_text" required value="{{ __($general->cur_text ?? 'BDT') }}" placeholder="e.g. US Dollar" maxlength="40">
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold">@lang('Currency Symbol') <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="las la-receipt text--primary"></i></span>
                                        <input class="form-control" type="text" name="cur_sym" required value="{{ $general->cur_sym ?? '৳' }}" placeholder="e.g. $" maxlength="10">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Appearance --}}
                        <div class="mb-4">
                            <h6 class="text-uppercase mb-3 text-muted border-bottom pb-2">
                                <i class="las la-palette me-1"></i> @lang('Appearance')
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold">@lang('Site Base Color')</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                            <input type="text" class="form-control colorPicker border-0 shadow-none" value="{{ $general->base_color ?? '4634ff' }}" style="width:38px;height:38px;cursor:pointer;" />
                                        </span>
                                        <input type="text" class="form-control colorCode" name="base_color" value="{{ $general->base_color ?? '4634ff' }}" placeholder="Hex e.g. 4634ff" maxlength="6">
                                    </div>
                                    <small class="text-muted">@lang('Primary theme color (buttons, links)')</small>
                                </div>
                            </div>
                        </div>

                        {{-- Product Card UI (dynamic colors) --}}
                        @if(\Schema::hasColumn('general_settings', 'product_card_color'))
                        <div class="mb-4">
                            <h6 class="text-uppercase mb-3 text-muted border-bottom pb-2">
                                <i class="las la-th-large me-1"></i> @lang('Product Card UI')
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold">@lang('Card frame color')</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                            <input type="text" class="form-control colorPicker border-0 shadow-none" data-name="product_card_color" value="{{ $general->product_card_color ?? '#ffffff' }}" style="width:38px;height:38px;cursor:pointer;" />
                                        </span>
                                        <input type="text" class="form-control ui-color-code" name="product_card_color" value="{{ $general->product_card_color ?? '#ffffff' }}" placeholder="#ffffff" maxlength="30">
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold">@lang('Button color')</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                            <input type="text" class="form-control colorPicker border-0 shadow-none" data-name="button_color" value="{{ $general->button_color ?? '#1f2937' }}" style="width:38px;height:38px;cursor:pointer;" />
                                        </span>
                                        <input type="text" class="form-control ui-color-code" name="button_color" value="{{ $general->button_color ?? '#1f2937' }}" placeholder="#1f2937" maxlength="30">
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold">@lang('Button hover color')</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                            <input type="text" class="form-control colorPicker border-0 shadow-none" data-name="button_hover_color" value="{{ $general->button_hover_color ?? '#374151' }}" style="width:38px;height:38px;cursor:pointer;" />
                                        </span>
                                        <input type="text" class="form-control ui-color-code" name="button_hover_color" value="{{ $general->button_hover_color ?? '#374151' }}" placeholder="#374151" maxlength="30">
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold">@lang('Rating star color')</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                            <input type="text" class="form-control colorPicker border-0 shadow-none" data-name="rating_star_color" value="{{ $general->rating_star_color ?? '#f59e0b' }}" style="width:38px;height:38px;cursor:pointer;" />
                                        </span>
                                        <input type="text" class="form-control ui-color-code" name="rating_star_color" value="{{ $general->rating_star_color ?? '#f59e0b' }}" placeholder="#f59e0b" maxlength="30">
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold">@lang('Discount badge color')</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                            <input type="text" class="form-control colorPicker border-0 shadow-none" data-name="discount_badge_color" value="{{ $general->discount_badge_color ?? '#dc2626' }}" style="width:38px;height:38px;cursor:pointer;" />
                                        </span>
                                        <input type="text" class="form-control ui-color-code" name="discount_badge_color" value="{{ $general->discount_badge_color ?? '#dc2626' }}" placeholder="#dc2626" maxlength="30">
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">@lang('Applied to product cards on storefront. Use hex e.g. #ffffff.')</small>
                        </div>
                        @endif

                        {{-- Today's Deal --}}
                        <div class="mb-4">
                            <h6 class="text-uppercase mb-3 text-muted border-bottom pb-2">
                                <i class="las la-tags me-1"></i> @lang('Today\'s Deal')
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label fw-semibold">@lang('Default Discount') <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="discount" value="{{ getAmount($general->discount ?? 0) }}" required min="0" step="0.01" placeholder="0">
                                        <select name="discount_type" class="form-select" style="max-width: 80px;">
                                            <option value="1" @selected(($general->discount_type ?? 1) == 1)>{{ __($general->cur_text ?? 'BDT') }}</option>
                                            <option value="2" @selected(($general->discount_type ?? 1) == 2)>%</option>
                                        </select>
                                    </div>
                                    <small class="text-muted">@lang('Used for today\'s deal promotions')</small>
                                </div>
                            </div>
                        </div>

                        {{-- E-commerce & Ads Tracking – always show this card --}}
                        <div class="mb-4">
                            <h6 class="text-uppercase mb-3 text-muted border-bottom pb-2">
                                <i class="las la-bullhorn me-1"></i> @lang('E-commerce & Ads Tracking')
                            </h6>
                            @if(!$trackingReady)
                                <div class="alert alert-warning mb-3 mb-0">
                                    <i class="las la-info-circle me-2"></i>
                                    @lang('To enable Ads Tracking fields, run'): <code>php artisan migrate</code>
                                </div>
                            @else
                                <div class="row g-3">
                                    @if($hasMetaPixel)
                                    <div class="col-md-6 col-lg-5">
                                        <label class="form-label fw-semibold">@lang('Meta Pixel ID')</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="las la-fingerprint text--primary"></i></span>
                                            <input class="form-control" type="text" name="meta_pixel_id" value="{{ $general->meta_pixel_id ?? '' }}" placeholder="e.g. 123456789" maxlength="100">
                                        </div>
                                        <small class="text-muted">@lang('Event Manager → Dataset → Settings. For order/conversion tracking.')</small>
                                    </div>
                                    @endif
                                    @if($hasFbToken)
                                    <div class="col-md-6 col-lg-5">
                                        <label class="form-label fw-semibold">@lang('Facebook Access Token')</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="las la-key text--primary"></i></span>
                                            <input class="form-control" type="text" name="facebook_access_token" value="{{ $general->facebook_access_token ?? '' }}" placeholder="@lang('Access token for Conversions API')" maxlength="500">
                                        </div>
                                        <small class="text-muted">@lang('Event Manager → Settings. Used for server-side Conversions API.')</small>
                                    </div>
                                    @endif
                                    @if($hasGoogleAds)
                                    <div class="col-md-6 col-lg-5">
                                        <label class="form-label fw-semibold">@lang('Google Ads ID')</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="lab la-google text--primary"></i></span>
                                            <input class="form-control" type="text" name="google_ads_id" value="{{ $general->google_ads_id ?? '' }}" placeholder="e.g. AW-123456789" maxlength="100">
                                        </div>
                                        <small class="text-muted">@lang('Google Ads conversion / gtag ID.')</small>
                                    </div>
                                    @endif
                                    @if($hasTiktokPixel)
                                    <div class="col-md-6 col-lg-5">
                                        <label class="form-label fw-semibold">@lang('TikTok Pixel ID')</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="lab la-tiktok text--primary"></i></span>
                                            <input class="form-control" type="text" name="tiktok_pixel_id" value="{{ $general->tiktok_pixel_id ?? '' }}" placeholder="@lang('From TikTok Events Manager')" maxlength="100">
                                        </div>
                                        <small class="text-muted">@lang('TikTok Events Manager → Pixel ID.')</small>
                                    </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2 pt-2">
                            <button type="submit" class="btn btn--primary btn-lg px-4">
                                <i class="las la-save me-2"></i>@lang('Save Settings')
                            </button>
                            <button type="reset" class="btn btn-outline-secondary btn-lg">@lang('Reset')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/spectrum.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.colorPicker').spectrum({
                color: $(this).val() || $(this).data('color'),
                preferredFormat: 'hex',
                change: function(color) {
                    var $p = $(this);
                    var target = $p.data('name') ? $p.closest('.input-group').find('input[name="' + $p.data('name') + '"]') : $p.parent().siblings('.colorCode');
                    if (target.length) target.val(color.toHexString());
                }
            });
            $('.colorCode, .ui-color-code').on('input', function() {
                var clr = $(this).val();
                $(this).closest('.input-group').find('.colorPicker').spectrum('set', clr);
            });
            var tz = "{{ config('app.timezone', 'UTC') }}";
            if (tz && $('select[name=timezone]').length) {
                $('select[name=timezone]').val("'" + tz + "'").trigger('change');
            }
            $('.select2-basic').select2({ dropdownParent: $('#generalSettingsForm').closest('.card-body') });
        })(jQuery);
    </script>
@endpush
