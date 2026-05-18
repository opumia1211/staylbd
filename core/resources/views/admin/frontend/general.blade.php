@extends('admin.layouts.app')
@section('panel')
<div class="general-settings-wrapper animate__animated animate__fadeIn">
    {{-- Page Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded bg-label-primary shadow-sm"><i class="las la-cog fs-3"></i></span>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">@lang('General Matrix')</h5>
                    <p class="text-muted small mb-0">@lang('Governing site identity, monetary protocols, and architectural appearance.')</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Intelligence Shortcuts --}}
    <div class="row mb-4 g-3">
        @php
            $shortcuts = [
                ['route' => 'admin.report.revenue_profit', 'icon' => 'la-coins', 'label' => 'Revenue Hub', 'color' => 'primary'],
                ['route' => 'admin.report.employee_performance', 'icon' => 'la-user-tie', 'label' => 'Performance', 'color' => 'success'],
                ['route' => 'admin.report.ad_source', 'icon' => 'la-chart-pie', 'label' => 'Ad Analytics', 'color' => 'info'],
                ['route' => 'admin.report.product', 'icon' => 'la-box', 'label' => 'Inventory', 'color' => 'warning'],
                ['route' => 'admin.orders.index', 'icon' => 'la-shopping-cart', 'label' => 'Order Flow', 'color' => 'secondary'],
            ];
        @endphp
        @foreach($shortcuts as $shortcut)
            @if(Route::has($shortcut['route']))
            <div class="col-6 col-sm-4 col-md-2">
                <a href="{{ route($shortcut['route']) }}" class="card border-0 shadow-sm text-center h-100 transition-all shortcut-card rounded-4">
                    <div class="card-body p-3">
                        <div class="badge bg-label-{{ $shortcut['color'] }} p-2 rounded-3 mb-2">
                            <i class="las {{ $shortcut['icon'] }} fs-4"></i>
                        </div>
                        <span class="d-block tiny fw-bold text-dark">@lang($shortcut['label'])</span>
                    </div>
                </a>
            </div>
            @endif
        @endforeach
    </div>

    <form action="{{ route('admin.frontend.sections.general.update') }}" method="POST" id="generalSettingsForm">
        @csrf
        <div class="row g-4">
            {{-- Primary Core Settings --}}
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold d-flex align-items-center">
                            <i class="las la-fingerprint me-2 text-primary fs-4"></i>
                            @lang('Core Identity')
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">@lang('Platform Title')</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0"><i class="las la-globe"></i></span>
                                    <input class="form-control rounded-3" type="text" name="site_name" required value="{{ $general->site_name ?? '' }}" placeholder="@lang('StayLBD')">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">@lang('Temporal Zone')</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0"><i class="las la-clock"></i></span>
                                    <select class="form-select select2-basic rounded-3" name="timezone">
                                        @foreach ($timezones ?? [] as $timezone)
                                            <option value="'{{ @$timezone }}'">{{ __($timezone) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">@lang('Currency Identifier')</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0"><i class="las la-dollar-sign"></i></span>
                                    <input class="form-control rounded-3" type="text" name="cur_text" required value="{{ __($general->cur_text ?? 'BDT') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">@lang('Currency Symbol')</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0"><i class="las la-receipt"></i></span>
                                    <input class="form-control rounded-3" type="text" name="cur_sym" required value="{{ $general->cur_sym ?? '৳' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold d-flex align-items-center">
                            <i class="las la-palette me-2 text-primary fs-4"></i>
                            @lang('Aesthetic Architecture')
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">@lang('Primary Base Color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border-0 bg-transparent overflow-hidden rounded-start-3" style="width: 45px;">
                                        <input type="text" class="form-control colorPicker h-100 w-100 border-0" value="{{ $general->base_color ?? '4634ff' }}" />
                                    </span>
                                    <input type="text" class="form-control colorCode rounded-end-3" name="base_color" value="{{ $general->base_color ?? '4634ff' }}" placeholder="4634ff">
                                </div>
                            </div>
                            @if(\Schema::hasColumn('general_settings', 'product_card_color'))
                            <div class="col-12 mt-4">
                                <div class="p-3 bg-label-primary rounded-4 border border-primary border-opacity-10">
                                    <h6 class="small fw-bold text-primary mb-3">@lang('Dynamic UI Tokens (Storefront)')</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="tiny fw-bold text-muted">@lang('Card Frame')</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text p-0 border-0 overflow-hidden" style="width: 30px;">
                                                    <input type="text" class="form-control colorPicker h-100 w-100 border-0" data-name="product_card_color" value="{{ $general->product_card_color ?? '#ffffff' }}" />
                                                </span>
                                                <input type="text" class="form-control ui-color-code" name="product_card_color" value="{{ $general->product_card_color ?? '#ffffff' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="tiny fw-bold text-muted">@lang('Interaction Btn')</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text p-0 border-0 overflow-hidden" style="width: 30px;">
                                                    <input type="text" class="form-control colorPicker h-100 w-100 border-0" data-name="button_color" value="{{ $general->button_color ?? '#1f2937' }}" />
                                                </span>
                                                <input type="text" class="form-control ui-color-code" name="button_color" value="{{ $general->button_color ?? '#1f2937' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="tiny fw-bold text-muted">@lang('Rating Signal')</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text p-0 border-0 overflow-hidden" style="width: 30px;">
                                                    <input type="text" class="form-control colorPicker h-100 w-100 border-0" data-name="rating_star_color" value="{{ $general->rating_star_color ?? '#f59e0b' }}" />
                                                </span>
                                                <input type="text" class="form-control ui-color-code" name="rating_star_color" value="{{ $general->rating_star_color ?? '#f59e0b' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Settings --}}
            <div class="col-xl-4">
                {{-- Promotion Matrix --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold d-flex align-items-center">
                            <i class="las la-tags me-2 text-warning fs-4"></i>
                            @lang('Promotion Matrix')
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <label class="form-label fw-bold text-dark small">@lang('Universal Discount Token')</label>
                        <div class="input-group">
                            <input type="number" class="form-control rounded-start-3" name="discount" value="{{ getAmount($general->discount ?? 0) }}" required min="0" step="0.01">
                            <select name="discount_type" class="form-select rounded-end-3" style="max-width: 100px;">
                                <option value="1" @selected(($general->discount_type ?? 1) == 1)>{{ __($general->cur_text ?? 'BDT') }}</option>
                                <option value="2" @selected(($general->discount_type ?? 1) == 2)>%</option>
                            </select>
                        </div>
                        <p class="tiny text-muted mt-2 mb-0">@lang('Governs global "Today\'s Deal" and promotional discount calculations.')</p>
                    </div>
                </div>

                {{-- Tracking Intelligence --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold d-flex align-items-center">
                            <i class="las la-bullhorn me-2 text-info fs-4"></i>
                            @lang('Tracking Intelligence')
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        @if(!$trackingReady)
                            <div class="p-3 bg-label-warning rounded-4 border border-warning border-opacity-10 text-center">
                                <i class="las la-exclamation-triangle fs-3 d-block mb-2"></i>
                                <p class="tiny fw-bold text-warning mb-0">@lang('Migration required for tracking fields.')</p>
                            </div>
                        @else
                            <div class="row g-3">
                                @if($hasMetaPixel)
                                <div class="col-12">
                                    <label class="tiny fw-bold text-dark">@lang('Meta Pixel ID')</label>
                                    <input class="form-control form-control-sm rounded-3" type="text" name="meta_pixel_id" value="{{ $general->meta_pixel_id ?? '' }}" placeholder="123456789">
                                </div>
                                @endif
                                @if($hasGoogleAds)
                                <div class="col-12">
                                    <label class="tiny fw-bold text-dark">@lang('Google Ads Node')</label>
                                    <input class="form-control form-control-sm rounded-3" type="text" name="google_ads_id" value="{{ $general->google_ads_id ?? '' }}" placeholder="AW-12345">
                                </div>
                                @endif
                                @if($hasTiktokPixel)
                                <div class="col-12">
                                    <label class="tiny fw-bold text-dark">@lang('TikTok Events Node')</label>
                                    <input class="form-control form-control-sm rounded-3" type="text" name="tiktok_pixel_id" value="{{ $general->tiktok_pixel_id ?? '' }}">
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px; z-index: 10;">
                    <div class="card-body p-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm mb-3">
                            <i class="las la-save me-2"></i>@lang('Commit Matrix')
                        </button>
                        <button type="reset" class="btn btn-outline-secondary w-100 rounded-pill">@lang('Revert Changes')</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style')
<link rel="stylesheet" href="{{ asset('assets/admin/css/spectrum.css') }}">
<style>
    .general-settings-wrapper { font-family: 'Public Sans', sans-serif; }
    .avatar-md { width: 48px; height: 48px; }
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .bg-label-warning { background-color: #fff2e6 !important; color: #ff9f43 !important; }
    .bg-label-secondary { background-color: #f5f5f9 !important; color: #8592a3 !important; }
    
    .shortcut-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important; color: #696cff; }
    .tiny { font-size: 0.75rem; }
    .form-label { margin-bottom: 0.4rem; }
    .rounded-4 { border-radius: 1rem !important; }
    
    .select2-container--default .select2-selection--single {
        border: 1px solid #d9dee3 !important;
        border-radius: 8px !important;
        height: 38px !important;
        padding-top: 4px !important;
    }
    .input-group-text { border-color: #d9dee3; }
    .form-control, .form-select { border-color: #d9dee3; padding: 0.6rem 0.9rem; }
    .form-control:focus, .form-select:focus { border-color: #696cff; box-shadow: 0 0 0 0.15rem rgba(105, 108, 255, 0.1); }
</style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.colorPicker').spectrum({
                color: $(this).val() || $(this).data('color'),
                preferredFormat: 'hex',
                showInput: true,
                change: function(color) {
                    var $p = $(this);
                    var target = $p.data('name') ? $p.closest('.input-group').find('input[name="' + $p.data('name') + '"]') : $p.closest('.input-group').find('.colorCode');
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
            $('.select2-basic').select2({ dropdownParent: $('#generalSettingsForm').closest('.general-settings-wrapper') });
        })(jQuery);
    </script>
@endpush

