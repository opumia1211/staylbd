@extends('admin.layouts.app')
@section('panel')
@push('style')
<style>
    .ui-settings-compact .table > :not(caption) > * > * {
        padding-top: 0.42rem;
        padding-bottom: 0.42rem;
        vertical-align: middle;
    }
    .ui-settings-compact .ui-color-bar {
        width: 100%;
        min-width: 220px;
        height: 40px;
        border: 1px solid #cfd4dc;
        border-radius: 8px;
        background: #fff;
        padding: 2px;
        cursor: pointer;
    }
    .ui-settings-compact .ui-color-bar::-webkit-color-swatch-wrapper {
        padding: 0;
    }
    .ui-settings-compact .ui-color-bar::-webkit-color-swatch {
        border: 1px solid rgba(0, 0, 0, 0.12);
        border-radius: 6px;
    }
    .ui-settings-compact .ui-color-bar::-moz-color-swatch {
        border: 1px solid rgba(0, 0, 0, 0.12);
        border-radius: 6px;
    }
    .ui-settings-compact .ui-color-code {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.2px;
        text-transform: lowercase;
    }
    .ui-settings-compact .ui-field-reset {
        width: 34px;
        height: 30px;
        padding: 0;
        font-weight: 700;
    }
    .ui-settings-compact .preset-chip {
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        padding: 0.3rem 0.7rem;
    }
</style>
@endpush
@php
    $defaults = [
        'product_card_bg' => '#ffffff',
        'product_button_color' => '#1f2937',
        'product_buy_now_color' => '#0e9f90',
        'product_buy_now_hover' => '#0c8a7d',
        'product_price_color' => '#0e9f90',
        'rating_color' => '#f59e0b',
        'discount_badge_color' => '#dc2626',
        'stock_color' => '#16a34a',
        'shipping_badge_color' => '#2563eb',
        'header_top_bg' => '#0f172a',
        'header_bg' => '#ffffff',
        'footer_bg' => '#0f172a',
    ];
@endphp
<div class="row g-3 mb-4">
    <div class="col-12">
        <h4 class="mb-1">@lang('UI & Theme Settings')</h4>
        <p class="text-muted mb-0">@lang('Professional storefront color control with compact digital bars and live preview before publish.')</p>
    </div>
</div>
<div class="row g-4">
    <div class="col-xl-7">
        @if(isset($uiTableReady) && !$uiTableReady)
            <div class="alert alert-warning border-0 shadow-sm">
                <i class="las la-exclamation-triangle me-1"></i>
                @lang('ui_settings table is missing/broken in database engine. You can view defaults here, but save will fail until table is repaired or migrated.')
            </div>
        @endif
        <div class="card border-0 shadow-sm rounded-3 ui-settings-compact">
            <div class="card-header bg--primary py-3">
                <h5 class="mb-0 text-white"><i class="las la-palette me-2"></i>@lang('Design System Controls')</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.ui.settings.update') }}" method="POST" id="uiSettingsForm">
                    @csrf
                    <input type="hidden" name="ui_action" id="uiActionInput" value="save">
                    <input type="hidden" name="preset_key" id="uiPresetKeyInput" value="">

                    <h6 class="text-uppercase mb-2 text-muted">@lang('Professional Presets')</h6>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm preset-chip ui-preset-btn" data-key="clean_default">@lang('Clean')</button>
                        <button type="button" class="btn btn-outline-primary btn-sm preset-chip ui-preset-btn" data-key="ocean_pro">@lang('Ocean')</button>
                        <button type="button" class="btn btn-outline-primary btn-sm preset-chip ui-preset-btn" data-key="midnight_lux">@lang('Midnight')</button>
                        <button type="button" class="btn btn-outline-primary btn-sm preset-chip ui-preset-btn" data-key="emerald_market">@lang('Emerald')</button>
                        <button type="button" class="btn btn-outline-primary btn-sm preset-chip ui-preset-btn" data-key="rose_studio">@lang('Rose')</button>
                        <button type="button" class="btn btn-outline-primary btn-sm preset-chip ui-preset-btn" data-key="arctic_glass">@lang('Arctic')</button>
                        <button type="button" class="btn btn-outline-primary btn-sm preset-chip ui-preset-btn" data-key="violet_glass">@lang('Violet')</button>
                        <button type="button" class="btn btn-outline-primary btn-sm preset-chip ui-preset-btn" data-key="graphite_clean">@lang('Graphite')</button>
                        <button type="button" class="btn btn-outline-danger btn-sm preset-chip ms-md-auto" id="uiResetBtn">@lang('Reset All')</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-3">
                            <thead>
                                <tr>
                                    <th>@lang('Element')</th>
                                    <th style="width:260px;">@lang('Digital Bar')</th>
                                    <th style="width:130px;">@lang('Code')</th>
                                    <th style="width:72px;">@lang('Reset')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach([
                                    'product_card_bg' => 'Product card / category card',
                                    'product_button_color' => 'Icon / action button',
                                    'product_buy_now_color' => 'Primary CTA',
                                    'product_buy_now_hover' => 'Primary CTA hover',
                                    'product_price_color' => 'Price text',
                                    'rating_color' => 'Rating stars',
                                    'discount_badge_color' => 'Discount badge',
                                    'stock_color' => 'Stock text',
                                    'shipping_badge_color' => 'Shipping badge',
                                    'header_top_bg' => 'Header top bar',
                                    'header_bg' => 'Header bar',
                                    'footer_bg' => 'Footer section',
                                ] as $name => $label)
                                    @php
                                        $current = $ui->{$name} ?? $defaults[$name];
                                        $defaultVal = $defaults[$name];
                                    @endphp
                                    <tr class="ui-row" data-field="{{ $name }}" data-default="{{ $defaultVal }}">
                                        <td class="fw-semibold">{{ __($label) }}</td>
                                        <td>
                                            <input type="color" class="form-control form-control-color form-control-sm ui-color-bar" data-name="{{ $name }}" value="{{ $current }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm ui-color-code" name="{{ $name }}" value="{{ $current }}" maxlength="30">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-outline-secondary btn-sm ui-field-reset">↺</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">@lang('Theme template')</label>
                            <select class="form-select" name="theme_template">
                                <option value="default" {{ ($ui->theme_template ?? 'default') === 'default' ? 'selected' : '' }}>@lang('Template 1 - Clean')</option>
                                <option value="template_2" {{ ($ui->theme_template ?? '') === 'template_2' ? 'selected' : '' }}>@lang('Template 2 - Ocean')</option>
                                <option value="template_3" {{ ($ui->theme_template ?? '') === 'template_3' ? 'selected' : '' }}>@lang('Template 3 - Midnight')</option>
                                <option value="template_4" {{ ($ui->theme_template ?? '') === 'template_4' ? 'selected' : '' }}>@lang('Template 4 - Emerald')</option>
                                <option value="template_5" {{ ($ui->theme_template ?? '') === 'template_5' ? 'selected' : '' }}>@lang('Template 5 - Rose')</option>
                                <option value="template_6" {{ ($ui->theme_template ?? '') === 'template_6' ? 'selected' : '' }}>@lang('Template 6 - Arctic')</option>
                                <option value="template_7" {{ ($ui->theme_template ?? '') === 'template_7' ? 'selected' : '' }}>@lang('Template 7 - Violet')</option>
                                <option value="template_8" {{ ($ui->theme_template ?? '') === 'template_8' ? 'selected' : '' }}>@lang('Template 8 - Graphite')</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn--primary btn-lg" {{ (isset($uiTableReady) && !$uiTableReady) ? 'disabled' : '' }}>
                        <i class="las la-save me-2"></i>@lang('Save UI Settings')
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-dark py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-white">@lang('Live Preview (Before Publish)')</h6>
                <button type="button" class="btn btn-light btn-sm" id="refreshPreviewBtn">@lang('Refresh')</button>
            </div>
            <div class="card-body p-0">
                <iframe id="uiLivePreviewFrame" src="{{ route('home') }}" style="width:100%; height:650px; border:0;"></iframe>
            </div>
            <div class="card-footer bg-light">
                <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm" target="_blank" rel="noopener">@lang('Open Full Public Page')</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        (function($) {
            function validHex(v) {
                return /^#([0-9a-f]{3}){1,2}$/i.test((v || '').trim());
            }

            $('.ui-row').each(function() {
                var row = $(this);
                var code = row.find('.ui-color-code');
                var bar = row.find('.ui-color-bar');

                code.on('input', function() {
                    var val = $(this).val().trim();
                    if (validHex(val)) bar.val(val);
                });
                bar.on('input', function() {
                    code.val($(this).val());
                });
                row.find('.ui-field-reset').on('click', function() {
                    var def = row.data('default');
                    code.val(def);
                    bar.val(def);
                    schedulePreview();
                });
            });

            var form = document.getElementById('uiSettingsForm');
            var actionInput = document.getElementById('uiActionInput');
            var presetInput = document.getElementById('uiPresetKeyInput');
            var previewFrame = document.getElementById('uiLivePreviewFrame');
            var refreshPreviewBtn = document.getElementById('refreshPreviewBtn');
            if (!form || !actionInput || !presetInput || !previewFrame) return;

            var presetMap = {
                clean_default: {
                    product_card_bg: '#ffffff',
                    product_button_color: '#1f2937',
                    product_buy_now_color: '#0e9f90',
                    product_buy_now_hover: '#0c8a7d',
                    product_price_color: '#0e9f90',
                    header_top_bg: '#0f172a',
                    rating_color: '#f59e0b',
                    discount_badge_color: '#dc2626',
                    stock_color: '#16a34a',
                    shipping_badge_color: '#2563eb',
                    header_bg: '#ffffff',
                    footer_bg: '#0f172a',
                    theme_template: 'default'
                },
                ocean_pro: {
                    product_card_bg: '#f8fbff',
                    product_button_color: '#0f172a',
                    product_buy_now_color: '#0284c7',
                    product_buy_now_hover: '#0369a1',
                    product_price_color: '#0ea5e9',
                    header_top_bg: '#0f172a',
                    rating_color: '#f59e0b',
                    discount_badge_color: '#e11d48',
                    stock_color: '#16a34a',
                    shipping_badge_color: '#2563eb',
                    header_bg: '#ffffff',
                    footer_bg: '#082f49',
                    theme_template: 'template_2'
                },
                midnight_lux: {
                    product_card_bg: '#111827',
                    product_button_color: '#f59e0b',
                    product_buy_now_color: '#f97316',
                    product_buy_now_hover: '#ea580c',
                    product_price_color: '#fbbf24',
                    header_top_bg: '#0f172a',
                    rating_color: '#f59e0b',
                    discount_badge_color: '#ef4444',
                    stock_color: '#22c55e',
                    shipping_badge_color: '#38bdf8',
                    header_bg: '#0b1220',
                    footer_bg: '#020617',
                    theme_template: 'template_3'
                },
                emerald_market: {
                    product_card_bg: '#f7fffb',
                    product_button_color: '#065f46',
                    product_buy_now_color: '#0d9488',
                    product_buy_now_hover: '#0f766e',
                    product_price_color: '#0f766e',
                    header_top_bg: '#0f172a',
                    rating_color: '#f59e0b',
                    discount_badge_color: '#dc2626',
                    stock_color: '#16a34a',
                    shipping_badge_color: '#0ea5e9',
                    header_bg: '#ecfdf5',
                    footer_bg: '#022c22',
                    theme_template: 'template_4'
                },
                rose_studio: {
                    product_card_bg: '#fff7fb',
                    product_button_color: '#be185d',
                    product_buy_now_color: '#ec4899',
                    product_buy_now_hover: '#db2777',
                    product_price_color: '#be185d',
                    header_top_bg: '#0f172a',
                    rating_color: '#f59e0b',
                    discount_badge_color: '#e11d48',
                    stock_color: '#22c55e',
                    shipping_badge_color: '#6366f1',
                    header_bg: '#fff1f2',
                    footer_bg: '#4a044e',
                    theme_template: 'template_5'
                },
                arctic_glass: {
                    product_card_bg: '#eef4ff',
                    product_button_color: '#1e3a8a',
                    product_buy_now_color: '#2563eb',
                    product_buy_now_hover: '#1d4ed8',
                    product_price_color: '#1d4ed8',
                    header_top_bg: '#0f172a',
                    rating_color: '#f59e0b',
                    discount_badge_color: '#dc2626',
                    stock_color: '#16a34a',
                    shipping_badge_color: '#2563eb',
                    header_bg: '#f8fbff',
                    footer_bg: '#0f1f3d',
                    theme_template: 'template_6'
                },
                violet_glass: {
                    product_card_bg: '#f8f5ff',
                    product_button_color: '#5b21b6',
                    product_buy_now_color: '#7c3aed',
                    product_buy_now_hover: '#6d28d9',
                    product_price_color: '#7c3aed',
                    header_top_bg: '#0f172a',
                    rating_color: '#f59e0b',
                    discount_badge_color: '#e11d48',
                    stock_color: '#16a34a',
                    shipping_badge_color: '#4f46e5',
                    header_bg: '#f6f3ff',
                    footer_bg: '#2e1065',
                    theme_template: 'template_7'
                },
                graphite_clean: {
                    product_card_bg: '#f3f4f6',
                    product_button_color: '#111827',
                    product_buy_now_color: '#0f766e',
                    product_buy_now_hover: '#115e59',
                    product_price_color: '#0f766e',
                    header_top_bg: '#0f172a',
                    rating_color: '#f59e0b',
                    discount_badge_color: '#dc2626',
                    stock_color: '#15803d',
                    shipping_badge_color: '#2563eb',
                    header_bg: '#ffffff',
                    footer_bg: '#111827',
                    theme_template: 'template_8'
                }
            };

            function applyPayload(payload) {
                Object.keys(payload).forEach(function(key) {
                    var field = form.querySelector('[name="' + key + '"]');
                    if (!field) return;
                    field.value = payload[key];
                    if (field.classList.contains('ui-color-code') && validHex(payload[key])) {
                        var row = field.closest('.ui-row');
                        if (row) {
                            var bar = row.querySelector('.ui-color-bar');
                            if (bar) bar.value = payload[key];
                        }
                    }
                });
            }

            document.querySelectorAll('.ui-preset-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var key = btn.getAttribute('data-key') || '';
                    if (!presetMap[key]) return;
                    actionInput.value = 'save';
                    presetInput.value = '';
                    applyPayload(presetMap[key]);
                    sendLivePreview();
                });
            });

            var resetBtn = document.getElementById('uiResetBtn');
            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    if (!window.confirm('Reset all public page colors to default?')) return;
                    applyPayload(presetMap.clean_default);
                    actionInput.value = 'save';
                    presetInput.value = '';
                    sendLivePreview();
                });
            }

            function collectPreviewPayload() {
                var payload = {};
                ['product_card_bg','product_button_color','product_buy_now_color','product_buy_now_hover','product_price_color','rating_color','discount_badge_color','stock_color','shipping_badge_color','header_top_bg','header_bg','footer_bg','theme_template'].forEach(function(name) {
                    var field = form.querySelector('[name="' + name + '"]');
                    payload[name] = field ? (field.value || '') : '';
                });
                return payload;
            }

            function sendLivePreview() {
                var payload = collectPreviewPayload();
                try {
                    previewFrame.contentWindow.postMessage({
                        type: 'stayl-ui-preview',
                        payload: payload
                    }, window.location.origin);
                } catch (e) {
                    // ignore
                }
            }

            function refreshPreviewNow() {
                var base = "{{ route('home') }}";
                previewFrame.src = base + (base.indexOf('?') === -1 ? '?' : '&') + 'preview_mode=1&t=' + Date.now();
            }

            form.querySelectorAll('input, select').forEach(function(el) {
                el.addEventListener('input', sendLivePreview);
                el.addEventListener('change', sendLivePreview);
            });
            previewFrame.addEventListener('load', sendLivePreview);
            if (refreshPreviewBtn) {
                refreshPreviewBtn.addEventListener('click', refreshPreviewNow);
            }
            refreshPreviewNow();

            form.addEventListener('submit', function() {
                if (actionInput.value !== 'apply_preset' && actionInput.value !== 'reset_default') {
                    actionInput.value = 'save';
                    presetInput.value = '';
                }
            });
        })(jQuery);
    </script>
@endpush
