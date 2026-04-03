@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">@lang('UI & Theme Settings')</h4>
            <p class="text-muted mb-0">@lang('Control product card, header, footer and button colors. Changes apply on homepage and product listing.')</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg--primary py-3">
                    <h5 class="mb-0 text-white"><i class="las la-palette me-2"></i>@lang('Colors & Template')</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.ui.settings.update') }}" method="POST" id="uiSettingsForm">
                        @csrf

                        <h6 class="text-uppercase mb-3 text-muted border-bottom pb-2">@lang('Product Card')</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">@lang('Card background')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                        <input type="text" class="form-control colorPicker border-0" data-name="product_card_bg" value="{{ $ui->product_card_bg ?? '#ffffff' }}" style="width:38px;height:38px;cursor:pointer;">
                                    </span>
                                    <input type="text" class="form-control" name="product_card_bg" value="{{ $ui->product_card_bg ?? '#ffffff' }}" placeholder="#ffffff" maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">@lang('Action button color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                        <input type="text" class="form-control colorPicker border-0" data-name="product_button_color" value="{{ $ui->product_button_color ?? '#1f2937' }}" style="width:38px;height:38px;cursor:pointer;">
                                    </span>
                                    <input type="text" class="form-control" name="product_button_color" value="{{ $ui->product_button_color ?? '#1f2937' }}" maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">@lang('Buy Now button')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                        <input type="text" class="form-control colorPicker border-0" data-name="product_buy_now_color" value="{{ $ui->product_buy_now_color ?? '#0e9f90' }}" style="width:38px;height:38px;cursor:pointer;">
                                    </span>
                                    <input type="text" class="form-control" name="product_buy_now_color" value="{{ $ui->product_buy_now_color ?? '#0e9f90' }}" placeholder="#0e9f90" maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">@lang('Buy Now hover')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                        <input type="text" class="form-control colorPicker border-0" data-name="product_buy_now_hover" value="{{ $ui->product_buy_now_hover ?? '#0c8a7d' }}" style="width:38px;height:38px;cursor:pointer;">
                                    </span>
                                    <input type="text" class="form-control" name="product_buy_now_hover" value="{{ $ui->product_buy_now_hover ?? '#0c8a7d' }}" maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">@lang('Rating star color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                        <input type="text" class="form-control colorPicker border-0" data-name="rating_color" value="{{ $ui->rating_color ?? '#f59e0b' }}" style="width:38px;height:38px;cursor:pointer;">
                                    </span>
                                    <input type="text" class="form-control" name="rating_color" value="{{ $ui->rating_color ?? '#f59e0b' }}" maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">@lang('Discount badge color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                        <input type="text" class="form-control colorPicker border-0" data-name="discount_badge_color" value="{{ $ui->discount_badge_color ?? '#dc2626' }}" style="width:38px;height:38px;cursor:pointer;">
                                    </span>
                                    <input type="text" class="form-control" name="discount_badge_color" value="{{ $ui->discount_badge_color ?? '#dc2626' }}" maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">@lang('Product price color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                        <input type="text" class="form-control colorPicker border-0" data-name="product_price_color" value="{{ $ui->product_price_color ?? $ui->product_buy_now_color ?? '#0e9f90' }}" style="width:38px;height:38px;cursor:pointer;">
                                    </span>
                                    <input type="text" class="form-control" name="product_price_color" value="{{ $ui->product_price_color ?? $ui->product_buy_now_color ?? '#0e9f90' }}" placeholder="#0e9f90" maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">@lang('Stock text color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                        <input type="text" class="form-control colorPicker border-0" data-name="stock_color" value="{{ $ui->stock_color ?? '#16a34a' }}" style="width:38px;height:38px;cursor:pointer;">
                                    </span>
                                    <input type="text" class="form-control" name="stock_color" value="{{ $ui->stock_color ?? '#16a34a' }}" placeholder="#16a34a" maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">@lang('Shipping badge color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border bg-light" style="min-width: 42px;">
                                        <input type="text" class="form-control colorPicker border-0" data-name="shipping_badge_color" value="{{ $ui->shipping_badge_color ?? '#2563eb' }}" style="width:38px;height:38px;cursor:pointer;">
                                    </span>
                                    <input type="text" class="form-control" name="shipping_badge_color" value="{{ $ui->shipping_badge_color ?? '#2563eb' }}" placeholder="#2563eb" maxlength="30">
                                </div>
                            </div>
                        </div>

                        <h6 class="text-uppercase mb-3 text-muted border-bottom pb-2">@lang('Header & Footer')</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">@lang('Header background')</label>
                                <input type="text" class="form-control" name="header_bg" value="{{ $ui->header_bg ?? '' }}" placeholder="@lang('Leave blank for default')" maxlength="30">
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">@lang('Footer background')</label>
                                <input type="text" class="form-control" name="footer_bg" value="{{ $ui->footer_bg ?? '' }}" placeholder="@lang('Leave blank for default')" maxlength="30">
                            </div>
                        </div>

                        <h6 class="text-uppercase mb-3 text-muted border-bottom pb-2">@lang('Theme Template')</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">@lang('Product card style')</label>
                                <select class="form-select" name="theme_template">
                                    <option value="default" {{ ($ui->theme_template ?? 'default') === 'default' ? 'selected' : '' }}>@lang('Template 1 (Default)')</option>
                                    <option value="template_2" {{ ($ui->theme_template ?? '') === 'template_2' ? 'selected' : '' }}>@lang('Template 2')</option>
                                    <option value="template_3" {{ ($ui->theme_template ?? '') === 'template_3' ? 'selected' : '' }}>@lang('Template 3')</option>
                                </select>
                                <small class="text-muted">@lang('Changes product card layout style on storefront.')</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn--primary btn-lg"><i class="las la-save me-2"></i>@lang('Save UI Settings')</button>
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
            $('.colorPicker').spectrum({
                preferredFormat: 'hex',
                change: function(color) {
                    var name = $(this).data('name');
                    if (name) $('input[name="' + name + '"]').val(color.toHexString());
                }
            });
            $('input[name="product_card_bg"], input[name="product_buy_now_color"], input[name="product_button_color"], input[name="product_buy_now_hover"], input[name="product_price_color"], input[name="rating_color"], input[name="discount_badge_color"], input[name="stock_color"], input[name="shipping_badge_color"]').on('input', function() {
                var name = $(this).attr('name');
                $(this).closest('.card-body').find('.colorPicker[data-name="' + name + '"]').spectrum('set', $(this).val());
            });
        })(jQuery);
    </script>
@endpush
