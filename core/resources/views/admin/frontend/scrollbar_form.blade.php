@extends('admin.layouts.app')
@section('panel')
@php
    $fd = $formData ?? [];
    $barId = $bar ? $bar->id : null;
    $isCustomMode = (bool)($isCustomMode ?? false);
    $scrollbarMode = $scrollbarMode ?? ($isCustomMode ? 'custom' : 'default');
    $initialRichSegments = [];
    $fdItemsForRich = $fd['items'] ?? [];
    if (!is_array($fdItemsForRich)) {
        $fdItemsForRich = (array) $fdItemsForRich;
    }
    foreach ($fdItemsForRich as $it) {
        $it = is_array($it) ? $it : (array) $it;
        $type = (string) ($it['type'] ?? 'text');
        if ($type === 'image') {
            continue;
        }
        $segments = $it['segments'] ?? [];
        if (is_array($segments) && !empty($segments)) {
            foreach ($segments as $s) {
                $s = is_array($s) ? $s : (array) $s;
                $txt = trim((string) ($s['text'] ?? ''));
                if ($txt === '') {
                    continue;
                }
                $initialRichSegments[] = [
                    'text' => $txt,
                    'color' => (string) ($s['color'] ?? ($it['color'] ?? '#333333')),
                    'weight' => (string) ($s['weight'] ?? ($s['font_weight'] ?? ($it['font_weight'] ?? '400'))),
                ];
            }
        } else {
            $txt = trim((string) ($it['content_text'] ?? ($it['content'] ?? '')));
            if ($txt !== '') {
                $initialRichSegments[] = [
                    'text' => $txt,
                    'color' => (string) ($it['color'] ?? '#333333'),
                    'weight' => (string) ($it['font_weight'] ?? '400'),
                ];
            }
        }
    }
    if (empty($initialRichSegments)) {
        $initialRichSegments = [['text' => '', 'color' => '#333333', 'weight' => '400']];
    }
@endphp
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.frontend.sections.scrollbar') }}">@lang('Scroll Bar')</a></li>
                    <li class="breadcrumb-item active">{{ $bar ? __('Edit') : __('New') }}</li>
                </ol>
            </nav>
            <a href="{{ route('admin.frontend.sections.scrollbar') }}" class="btn btn-sm btn-outline-secondary">
                <i class="las la-arrow-left me-1"></i>@lang('Back to list')
            </a>
        </div>

        <div class="card mb-2 scrollbar-actionbar">
            <div class="scrollbar-actionbar-row">
                <span class="badge bg-primary">@lang('Edit Scroll Bar')</span>
                <a class="btn btn-sm btn-outline-secondary" href="#cat-basic">@lang('Basic')</a>
                <a class="btn btn-sm btn-outline-secondary" href="#cat-style">@lang('Bar')</a>
                <a class="btn btn-sm btn-outline-secondary" href="#cat-color">@lang('Color')</a>
                <a class="btn btn-sm btn-outline-secondary" href="#cat-visibility">@lang('Visibility')</a>
                <a class="btn btn-sm btn-outline-secondary" href="#cat-animation">@lang('Animation')</a>
                <a class="btn btn-sm btn-outline-secondary" href="#cat-editor">@lang('Editor')</a>
                <button type="button" class="btn btn-sm btn-outline-success scrollbar-preset-btn" data-preset="home_offer">@lang('Home Offer')</button>
                <button type="button" class="btn btn-sm btn-outline-danger scrollbar-preset-btn" data-preset="breaking_news">@lang('Breaking News')</button>
                <button type="button" class="btn btn-sm btn-outline-info scrollbar-preset-btn" data-preset="product_highlight">@lang('Product Highlight')</button>
                <button type="button" class="btn btn-sm btn-outline-secondary scrollbar-preset-btn" data-preset="minimal_clean">@lang('Minimal Clean')</button>
                <button type="submit" form="scrollbarFormPage" class="btn btn--primary btn-sm ms-auto">@lang('Save Scroll Bar')</button>
            </div>
        </div>

        {{-- Live Preview (sticky) --}}
        <div class="card mb-4 scrollbar-live-preview-card">
            <div class="card-header scrollbar-live-preview-header py-2 d-flex flex-wrap align-items-center gap-2">
                <span class="badge bg-white text-primary me-2">@lang('Live Preview')</span>
                <span class="text-dark">@lang('Changes below update instantly')</span>
                <button type="button" class="btn btn-sm btn-outline-primary ms-auto" id="scrollbarRefreshPreviewBtn" title="@lang('Refresh')"><i class="las la-sync-alt"></i></button>
            </div>
            <div class="card-body p-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span class="small text-muted">@lang('Preview width')</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary scrollbar-preview-size-btn" data-width="100%">Desktop</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary scrollbar-preview-size-btn" data-width="768px">Tablet</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary scrollbar-preview-size-btn" data-width="390px">Mobile</button>
                    <span class="ms-auto small text-muted">@lang('Auto-refresh')</span>
                    <span class="badge bg-light text-dark">@lang('ON')</span>
                </div>
                <iframe id="scrollbarLivePreviewIframe" src="about:blank" style="width:100%; height:188px; border:1px solid #d8e1ee; border-radius:10px; background:rgba(255,255,255,0.65);" title="@lang('Scroll bar live preview')"></iframe>
            </div>
        </div>

        @php
            $initialItems = old('items', $fd['items'] ?? []);
            if (!is_array($initialItems) || empty($initialItems)) {
                $initialItems = [
                    ['type' => 'text', 'content' => '', 'content_text' => '', 'color' => '#333333', 'is_active' => 1],
                ];
            }
            $initialItems = array_values($initialItems);
        @endphp
        <form action="{{ route('admin.frontend.sections.scrollbar.save') }}" method="POST" enctype="multipart/form-data" id="scrollbarFormPage">
            @csrf
            @if($barId)<input type="hidden" name="id" value="{{ $barId }}">@endif
            <input type="hidden" name="scrollbar_mode" value="{{ $scrollbarMode }}">

            <div class="card">
                <div class="card-body scrollbar-form-page">
                    {{-- Basic --}}
                    <section class="scrollbar-category-box" id="cat-basic">
                    <h6 class="text-muted border-bottom pb-2 mb-2"><span class="badge bg-light text-dark me-1">1</span>@lang('Basic')</h6>
                    <div class="row g-2 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">@lang('Title')</label>
                            <input type="text" class="form-control" name="title" value="{{ old('title', $fd['title'] ?? '') }}" placeholder="@lang('e.g. Home Banner Ticker')" maxlength="100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('Position')</label>
                            <select class="form-select" name="position" required>
                                @php
                                    $posValue = old('position', $fd['position'] ?? '');
                                    $positionGroups = [
                                        __('Everywhere (All Pages)') => ['header_above', 'header_below', 'content_top', 'content_bottom', 'footer_above', 'footer_below'],
                                        __('Homepage Only') => ['banner_above', 'banner_below', 'product_line'],
                                        __('Products & Categories') => ['product_listing_above', 'product_listing_below', 'product_listing', 'category_above', 'category_below', 'category_page'],
                                        __('Product Detail Page') => ['product_detail_above', 'product_detail_below'],
                                        __('Advanced') => ['custom'],
                                    ];
                                @endphp
                                @foreach($positionGroups as $groupLabel => $groupValues)
                                    <optgroup label="{{ $groupLabel }}">
                                        @foreach($groupValues as $v)
                                            @php $label = \App\Services\ScrollbarService::POSITIONS[$v] ?? $v; @endphp
                                            <option value="{{ $v }}" {{ $posValue == $v ? 'selected' : '' }}>{{ __($label) }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('Template')</label>
                            <select class="form-select" name="template" required>
                                @foreach(['glass'=>'Glass','solid'=>'Solid','minimal'=>'Minimal','dark'=>'Dark','breaking_news'=>'Breaking News','offer'=>'Offer','alert'=>'Alert','info'=>'Info'] as $v => $l)
                                    <option value="{{ $v }}" {{ old('template', $fd['template'] ?? 'glass') == $v ? 'selected' : '' }}>{{ __($l) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">@lang('Order Priority')</label>
                            <input type="number" class="form-control" name="display_order" value="{{ old('display_order', $fd['display_order'] ?? '') }}" min="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">@lang('Show/Hide')</label>
                            <select class="form-select" name="status">
                                <option value="1" {{ (old('status', $fd['status'] ?? 1) == 1) ? 'selected' : '' }}>@lang('Active (Show)')</option>
                                <option value="0" {{ (old('status', $fd['status'] ?? 1) == 0) ? 'selected' : '' }}>@lang('Off (Hide)')</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">@lang('Scroll Speed (Slow-Fast)')</label>
                            <input type="number" class="form-control" name="scroll_speed" value="{{ old('scroll_speed', $fd['scroll_speed'] ?? 45) }}" min="1" max="100" title="1 = Very SLow, 100 = Very Fast">
                        </div>
                    </div>
                    </section>

                    {{-- Bar: height (thin to thick), text size & weight --}}
                    <section class="scrollbar-category-box" id="cat-style">
                    <h6 class="text-muted border-bottom pb-2 mb-2"><span class="badge bg-light text-dark me-1">2</span>@lang('Bar') — @lang('Height, text size, weight & thickness')</h6>
                    <div class="row g-2 mb-4">
                        <div class="col-md-2">
                            <label class="form-label">@lang('Custom Height (px)')</label>
                            <!-- Changed max height to 150 so it can actually be thick -->
                            <input type="number" class="form-control form-control-sm" name="bar_height" id="scrollbarBarHeight" value="{{ old('bar_height', $fd['bar_height'] ?? 52) }}" min="8" max="150" title="8 = ultra thin, 150 = ultra thick">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">@lang('Text Size')</label>
                            <select class="form-select form-select-sm" name="default_text_size">
                                @foreach(['extra_small'=>'Extra Small','small'=>'Small','normal'=>'Normal','large'=>'Large','extra_large'=>'Extra Large'] as $v => $l)
                                    <option value="{{ $v }}" {{ old('default_text_size', $fd['default_text_size'] ?? 'normal') == $v ? 'selected' : '' }}>{{ __($l) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">@lang('Text Thickness')</label>
                            <select class="form-select form-select-sm" name="default_text_weight">
                                @php $wt = old('default_text_weight', $fd['default_text_weight'] ?? 'normal'); @endphp
                                <option value="light" {{ $wt == 'light' ? 'selected' : '' }}>@lang('Thinner (Light)')</option>
                                <option value="normal" {{ $wt == 'normal' ? 'selected' : '' }}>@lang('Normal')</option>
                                <option value="medium" {{ $wt == 'medium' ? 'selected' : '' }}>@lang('Medium')</option>
                                <option value="semibold" {{ $wt == 'semibold' ? 'selected' : '' }}>@lang('Semi Bold')</option>
                                <option value="bold" {{ $wt == 'bold' ? 'selected' : '' }}>@lang('Thick (Bold)')</option>
                                <option value="extrabold" {{ $wt == 'extrabold' ? 'selected' : '' }}>@lang('Extra Thick')</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">@lang('Line Thickness')</label>
                            <select class="form-select form-select-sm" name="bar_thickness">
                                @foreach(['ultra_thin'=>'Ultra Thin','extra_thin'=>'Extra Thin','thin'=>'Thin','normal'=>'Normal','thick'=>'Thick','extra_thick'=>'Extra Thick','ultra_thick'=>'Ultra Thick'] as $v => $l)
                                    <option value="{{ $v }}" {{ old('bar_thickness', $fd['bar_thickness'] ?? 'normal') == $v ? 'selected' : '' }}>{{ __($l) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Custom Placement Options (Only works if position is Custom) -->
                        <div class="col-md-4">
                            <label class="form-label text-primary">@lang('Custom Position Settings (For Advanced)')</label>
                            <div class="d-flex gap-2">
                                <input type="number" class="form-control form-control-sm" name="custom_y_px" value="{{ old('custom_y_px', $fd['custom_y_px'] ?? 0) }}" placeholder="Top Space (px)" title="Distance from top in px">
                                <input type="number" class="form-control form-control-sm" name="custom_width_percent" value="{{ old('custom_width_percent', $fd['custom_width_percent'] ?? 100) }}" placeholder="Width %" title="Width in percentage (e.g. 50)">
                            </div>
                        </div>
                        <input type="hidden" name="width_type" value="{{ old('width_type', $fd['width_type'] ?? 'full') }}">
                        <input type="hidden" name="width_value" value="{{ old('width_value', $fd['width_value'] ?? '') }}">
                        <input type="hidden" name="max_width" value="{{ old('max_width', $fd['max_width'] ?? '') }}">
                    </div>
                    </section>

                    {{-- Bar color --}}
                    <section class="scrollbar-category-box" id="cat-color">
                    <h6 class="text-muted border-bottom pb-2 mb-2"><span class="badge bg-light text-dark me-1">3</span>@lang('Bar color')</h6>
                    <div class="row g-2 mb-4">
                        <div class="col-md-12">
                            <label class="form-label small text-muted mb-1">@lang('Quick color presets')</label>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2" id="scrollbarColorPresets">
                                @foreach(['#0d6efd'=>'Blue','#198754'=>'Green','#dc3545'=>'Red','#fd7e14'=>'Orange','#6f42c1'=>'Purple','#0dcaf0'=>'Cyan','#212529'=>'Black','#ffffff'=>'White','#ffc107'=>'Yellow','#20c997'=>'Teal'] as $hex => $label)
                                    <button type="button" class="btn btn-sm border scrollbar-color-preset" data-hex="{{ $hex }}" style="width:2rem;height:2rem;background:{{ $hex }};padding:0;min-width:2rem;{{ $hex === '#ffffff' ? 'border:2px solid #dee2e6!important' : '' }}" title="{{ $label }} ({{ $hex }})" aria-label="{{ $label }}"></button>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('Background type')</label>
                            <select class="form-select form-select-sm" name="bar_background_type" id="scrollbarBarBackgroundType">
                                <option value="" {{ old('bar_background_type', $fd['bar_background_type'] ?? '') == '' ? 'selected' : '' }}>@lang('Use template')</option>
                                <option value="solid" {{ old('bar_background_type', $fd['bar_background_type'] ?? '') == 'solid' ? 'selected' : '' }}>Solid</option>
                                <option value="gradient" {{ old('bar_background_type', $fd['bar_background_type'] ?? '') == 'gradient' ? 'selected' : '' }}>Gradient</option>
                                <option value="image" {{ old('bar_background_type', $fd['bar_background_type'] ?? '') == 'image' ? 'selected' : '' }}>Image</option>
                            </select>
                        </div>
                        @php $barBgVal = old('bar_background_value', $fd['bar_background_value'] ?? ''); @endphp
                        <div class="col-md-4">
                            <label class="form-label">@lang('Bar color')</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" class="form-control form-control-color p-1" id="scrollbarBarColorQuick" value="{{ preg_match('/^#[0-9A-Fa-f]{6}$/', $barBgVal) ? $barBgVal : '#0d6efd' }}" title="@lang('Pick color')" style="width:2.75rem;height:2.25rem;cursor:pointer;border:1px solid #dee2e6;">
                                <input type="text" class="form-control form-control-sm" name="bar_background_value" id="scrollbarBarBackgroundValue" value="{{ $barBgVal }}" placeholder="#0d6efd" maxlength="500">
                            </div>
                            <small class="text-muted">Hex or picker</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('Padding')</label>
                            <input type="text" class="form-control form-control-sm" name="bar_padding" value="{{ old('bar_padding', $fd['bar_padding'] ?? '') }}" placeholder="12px 20px">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('Border')</label>
                            <input type="text" class="form-control form-control-sm" name="bar_border" value="{{ old('bar_border', $fd['bar_border'] ?? '') }}" placeholder="1px solid #ddd">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Shadow')</label>
                            <input type="text" class="form-control form-control-sm" name="bar_shadow" value="{{ old('bar_shadow', $fd['bar_shadow'] ?? '') }}" placeholder="0 2px 8px rgba(0,0,0,0.1)">
                        </div>
                    </div>
                    </section>

                    {{-- Where to show --}}
                    <section class="scrollbar-category-box" id="cat-visibility">
                    <h6 class="text-muted border-bottom pb-2 mb-2"><span class="badge bg-light text-dark me-1">4</span>@lang('Where to show')</h6>
                    <div class="row g-2 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">@lang('Visibility')</label>
                            <select class="form-select" name="visibility">
                                <option value="public" {{ ($fd['visibility'] ?? 'public') == 'public' ? 'selected' : '' }}>@lang('Public')</option>
                                <option value="private" {{ ($fd['visibility'] ?? '') == 'private' ? 'selected' : '' }}>@lang('Private')</option>
                            </select>
                        </div>
                        @if(!$isCustomMode)
                        <input type="hidden" name="visibility_pages" value="all">
                        <input type="hidden" name="visibility_users" value="all">
                        <input type="hidden" name="custom_url_mode" value="contains">
                        <input type="hidden" name="custom_urls" value="">
                        @endif
                        @if($isCustomMode)
                        <div class="col-md-4">
                            <label class="form-label">@lang('User scope')</label>
                            <select class="form-select form-select-sm" name="visibility_users">
                                <option value="all" {{ ($fd['visibility_users'] ?? 'all') == 'all' ? 'selected' : '' }}>@lang('All users')</option>
                                <option value="guest" {{ ($fd['visibility_users'] ?? '') == 'guest' ? 'selected' : '' }}>@lang('Guest only')</option>
                                <option value="logged_in" {{ ($fd['visibility_users'] ?? '') == 'logged_in' ? 'selected' : '' }}>@lang('Logged in only')</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('Page scope')</label>
                            <select class="form-select form-select-sm" name="visibility_pages" id="scrollbarVisibilityPages">
                                @php $vp = $fd['visibility_pages'] ?? ($isCustomMode ? 'custom_urls' : 'all'); @endphp
                                <option value="all" {{ $vp == 'all' ? 'selected' : '' }}>@lang('All pages')</option>
                                <option value="home" {{ $vp == 'home' ? 'selected' : '' }}>@lang('Home')</option>
                                <option value="product" {{ $vp == 'product' ? 'selected' : '' }}>@lang('Product')</option>
                                <option value="all_products" {{ $vp == 'all_products' ? 'selected' : '' }}>@lang('Products listing')</option>
                                <option value="product_detail" {{ $vp == 'product_detail' ? 'selected' : '' }}>@lang('Product detail')</option>
                                <option value="category" {{ $vp == 'category' ? 'selected' : '' }}>@lang('Category')</option>
                                <option value="cart" {{ $vp == 'cart' ? 'selected' : '' }}>@lang('Cart')</option>
                                <option value="checkout" {{ $vp == 'checkout' ? 'selected' : '' }}>@lang('Checkout')</option>
                                <option value="custom_urls" {{ $vp == 'custom_urls' ? 'selected' : '' }}>@lang('Custom URL rules')</option>
                            </select>
                        </div>
                        <div class="col-md-3 scrollbar-custom-url-wrap {{ $vp === 'custom_urls' ? '' : 'd-none' }}">
                            <label class="form-label">@lang('URL match mode')</label>
                            <select class="form-select form-select-sm" name="custom_url_mode">
                                <option value="contains" {{ ($fd['custom_url_mode'] ?? 'contains') == 'contains' ? 'selected' : '' }}>@lang('Contains')</option>
                                <option value="exact" {{ ($fd['custom_url_mode'] ?? '') == 'exact' ? 'selected' : '' }}>@lang('Exact')</option>
                                <option value="path" {{ ($fd['custom_url_mode'] ?? '') == 'path' ? 'selected' : '' }}>@lang('Path')</option>
                            </select>
                        </div>
                        <div class="col-md-9 scrollbar-custom-url-wrap {{ $vp === 'custom_urls' ? '' : 'd-none' }}">
                            <label class="form-label">@lang('Custom URLs')</label>
                            <input type="text" class="form-control form-control-sm" name="custom_urls" value="{{ old('custom_urls', $fd['custom_urls'] ?? '') }}" placeholder="/offer, /campaign/summer, /product/*">
                            <small class="text-muted">@lang('Comma separated. Example: /offer,/campaign/summer')</small>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label">@lang('Schedule start')</label>
                            <input type="date" class="form-control form-control-sm" name="schedule_start" value="{{ old('schedule_start', $fd['schedule_start'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Schedule end')</label>
                            <input type="date" class="form-control form-control-sm" name="schedule_end" value="{{ old('schedule_end', $fd['schedule_end'] ?? '') }}">
                        </div>
                    </div>
                    </section>

                    {{-- Scroll & Animation --}}
                    <section class="scrollbar-category-box" id="cat-animation">
                    <h6 class="text-muted border-bottom pb-2 mb-2"><span class="badge bg-light text-dark me-1">5</span>@lang('Scroll & Animation')</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">@lang('Direction')</label>
                            <select class="form-select form-select-sm" name="scroll_direction">
                                <option value="ltr" {{ ($fd['scroll_direction'] ?? 'ltr') == 'ltr' ? 'selected' : '' }}>@lang('Left → Right')</option>
                                <option value="rtl" {{ ($fd['scroll_direction'] ?? '') == 'rtl' ? 'selected' : '' }}>@lang('Right → Left')</option>
                                <option value="ttb" {{ ($fd['scroll_direction'] ?? '') == 'ttb' ? 'selected' : '' }}>@lang('Top → Bottom')</option>
                                <option value="btt" {{ ($fd['scroll_direction'] ?? '') == 'btt' ? 'selected' : '' }}>@lang('Bottom → Top')</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('Animation')</label>
                            <select class="form-select form-select-sm" name="animation_type">
                                <option value="linear" {{ ($fd['animation_type'] ?? 'linear') == 'linear' ? 'selected' : '' }}>@lang('Linear')</option>
                                <option value="ease" {{ ($fd['animation_type'] ?? '') == 'ease' ? 'selected' : '' }}>@lang('Ease')</option>
                                <option value="fade" {{ ($fd['animation_type'] ?? '') == 'fade' ? 'selected' : '' }}>@lang('Fade')</option>
                                <option value="slide" {{ ($fd['animation_type'] ?? '') == 'slide' ? 'selected' : '' }}>@lang('Slide')</option>
                                <option value="bounce" {{ ($fd['animation_type'] ?? '') == 'bounce' ? 'selected' : '' }}>@lang('Bounce')</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('Text alignment')</label>
                            <select class="form-select form-select-sm" name="align">
                                <option value="left" {{ ($fd['align'] ?? 'center') == 'left' ? 'selected' : '' }}>@lang('Left')</option>
                                <option value="center" {{ ($fd['align'] ?? 'center') == 'center' ? 'selected' : '' }}>@lang('Center')</option>
                                <option value="right" {{ ($fd['align'] ?? '') == 'right' ? 'selected' : '' }}>@lang('Right')</option>
                            </select>
                        </div>
                    </div>
                    </section>

                    {{-- Items --}}
                    <section class="scrollbar-category-box" id="cat-editor">
                    <h6 class="text-muted border-bottom pb-2 mb-2"><span class="badge bg-light text-dark me-1">6</span>@lang('Unified headline editor')</h6>
                    <p class="text-muted small mb-2">@lang('Single typing area: write text + emoji in one place, then select any word and apply color/weight.')</p>
                    <div class="card border mb-3">
                        <div class="card-body p-2">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <input type="color" id="scrollbarRichColor" class="form-control form-control-color p-1" value="#333333" style="width:2.6rem;height:2rem;">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="scrollbarRichApplyColor">@lang('Apply color')</button>
                                <select class="form-select form-select-sm" id="scrollbarRichFontFamily" style="width:auto;min-width:170px;">
                                    <option value="inherit">@lang('Font family')</option>
                                    <option value="Inter, Arial, sans-serif">Inter</option>
                                    <option value="Segoe UI, Arial, sans-serif">Segoe UI</option>
                                    <option value="Poppins, Arial, sans-serif">Poppins</option>
                                    <option value="Roboto, Arial, sans-serif">Roboto</option>
                                    <option value="Arial, sans-serif">Arial</option>
                                    <option value="Tahoma, sans-serif">Tahoma</option>
                                    <option value="Georgia, serif">Georgia</option>
                                    <option value="Times New Roman, serif">Times New Roman</option>
                                    <option value="Courier New, monospace">Courier New</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="scrollbarRichApplyFont">@lang('Apply font')</button>
                                <select class="form-select form-select-sm" id="scrollbarRichFontSize" style="width:auto;min-width:95px;">
                                    <option value="">@lang('Text size')</option>
                                    <option value="12px">12px</option>
                                    <option value="13px">13px</option>
                                    <option value="14px">14px</option>
                                    <option value="15px">15px</option>
                                    <option value="16px">16px</option>
                                    <option value="18px">18px</option>
                                    <option value="20px">20px</option>
                                    <option value="22px">22px</option>
                                    <option value="24px">24px</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="scrollbarRichApplySize">@lang('Apply size')</button>
                                <div class="d-flex flex-wrap gap-2" id="scrollbarRichQuickColors">
                                    <button type="button" class="btn btn-outline-secondary rich-quick-color" data-color="#ef4444" title="Red"></button>
                                    <button type="button" class="btn btn-outline-secondary rich-quick-color" data-color="#22c55e" title="Green"></button>
                                    <button type="button" class="btn btn-outline-secondary rich-quick-color" data-color="#3b82f6" title="Blue"></button>
                                    <button type="button" class="btn btn-outline-secondary rich-quick-color" data-color="#a855f7" title="Purple"></button>
                                    <button type="button" class="btn btn-outline-secondary rich-quick-color" data-color="#111827" title="Black"></button>
                                    <button type="button" class="btn btn-outline-secondary rich-quick-color" data-color="#e11d48" title="Rose"></button>
                                    <button type="button" class="btn btn-outline-secondary rich-quick-color" data-color="#f59e0b" title="Amber"></button>
                                    <button type="button" class="btn btn-outline-secondary rich-quick-color" data-color="#14b8a6" title="Teal"></button>
                                    <button type="button" class="btn btn-outline-secondary rich-quick-color" data-color="#0ea5e9" title="Sky"></button>
                                    <button type="button" class="btn btn-outline-secondary rich-quick-color" data-color="#000000" title="Pure black"></button>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="scrollbarRichWeightNormal">@lang('Normal')</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="scrollbarRichWeightBold">@lang('Bold')</button>
                                <button type="button" class="btn btn-sm btn-outline-warning" id="scrollbarRichClearFormat">@lang('Clear format')</button>
                                <button type="button" class="btn btn-sm btn-outline-dark" id="scrollbarEmojiPickerToggle">@lang('Emoji picker')</button>
                            </div>
                            <div id="scrollbarRichEditor" class="form-control scrollbar-rich-editor" contenteditable="true"></div>
                            <div class="scrollbar-emoji-picker shadow-sm border rounded bg-white d-none" id="scrollbarEmojiPickerPanel">
                                <div class="p-2 scrollbar-emoji-grid" id="scrollbarEmojiGrid"></div>
                            </div>
                            <small class="text-muted d-block mt-1">@lang('Type and insert emoji together in one editor. Emoji click keeps cursor typing flow active.')</small>
                        </div>
                    </div>
                    <input type="hidden" name="rich_content" id="scrollbarRichContentInput" value="">
                    <input type="hidden" name="rich_segments_json" id="scrollbarRichSegmentsInput" value="">

                    <div id="scrollbarItemsContainer" class="d-none">
                        @foreach($initialItems as $idx => $item)
                            @include('admin.frontend.partials.scrollbar_item_row', ['idx' => $idx, 'item' => $item])
                        @endforeach
                    </div>
                    </section>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.frontend.sections.scrollbar') }}" class="btn btn-secondary">@lang('Cancel')</a>
                    <button type="submit" class="btn btn--primary">@lang('Save Scroll Bar')</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
(function() {
    "use strict";
    if (typeof jQuery === 'undefined') return;
    var $ = jQuery;
    var itemIndex = parseInt(document.getElementById('scrollbarItemNextIndex')?.value || $('#scrollbarItemsContainer .scrollbar-item-row').length || 0, 10);
    var scrollbarPreviewLiveBase = {!! json_encode(url()->route('admin.frontend.sections.scrollbar.preview.live')) !!};
    var scrollbarLivePreviewTimer = null;
    var initialRichSegments = {!! json_encode($initialRichSegments, JSON_UNESCAPED_UNICODE) !!};
    var savedEditorRange = null;
    var savedCaretOffset = null;

    function escapeForTextarea(s) {
        if (!s) return '';
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    function itemTemplate(data) {
        data = data || {};
        var idx = itemIndex++;
        var type = data.type || 'text';
        var content = (data.content !== undefined && data.content !== null) ? String(data.content) : '';
        var contentEscaped = escapeForTextarea(content);
        var color = (data.color || '#333333').replace(/"/g, '&quot;');
        var colorPickerVal = /^#[0-9A-Fa-f]{6}$/.test(color) ? color : (/^#[0-9A-Fa-f]{3}$/.test(color) ? ('#'+color[1]+color[1]+color[2]+color[2]+color[3]+color[3]) : '#333333');
        var fontFamily = (data.font_family || 'inherit').replace(/"/g, '&quot;');
        var fontStyle = data.font_style || 'normal';
        var fontWeight = (data.font_weight !== undefined && data.font_weight !== null) ? String(data.font_weight) : '400';
        var letterSpacing = (data.letter_spacing !== undefined && data.letter_spacing !== null) ? String(data.letter_spacing).replace(/"/g, '&quot;') : '';
        var textTransform = data.text_transform || 'none';
        var isActive = (data.is_active !== undefined && data.is_active !== null) ? parseInt(data.is_active, 10) : 1;
        var checked = (isActive === 1) ? ' checked' : '';
        var contentLabel = type === 'image' ? 'Image file' : 'Content (text, max 2000 characters)';
        return '<div class="scrollbar-item-row border rounded p-3 mb-3" data-idx="' + idx + '">' +
            '<div class="row g-2 align-items-start"><div class="col-md-12"><div class="row g-2 align-items-end">' +
            '<div class="col-md-2"><label class="form-label small">Type</label><select class="form-select form-select-sm item-type" name="items[' + idx + '][type]">' +
            '<option value="text"' + (type === 'text' ? ' selected' : '') + '>Text</option><option value="emoji"' + (type === 'emoji' ? ' selected' : '') + '>Emoji</option><option value="image"' + (type === 'image' ? ' selected' : '') + '>Image</option></select></div>' +
            '<div class="col-md-2 item-text-wrap"><label class="form-label small">Color</label><div class="d-flex align-items-center gap-1"><input type="color" class="form-control form-control-color border item-color-picker" value="' + colorPickerVal + '" style="width:2.25rem;height:2rem;min-width:2.25rem;padding:2px;cursor:pointer"><input type="text" class="form-control form-control-sm item-color flex-grow-1" name="items[' + idx + '][color]" value="' + color + '" placeholder="#333"></div></div>' +
            '<div class="col-md-2"><label class="form-label small">Font</label><input type="text" class="form-control form-control-sm" name="items[' + idx + '][font_family]" value="' + fontFamily + '"></div>' +
            '<div class="col-md-2"><label class="form-label small">Style</label><select class="form-select form-select-sm" name="items[' + idx + '][font_style]"><option value="normal"' + (fontStyle === 'normal' ? ' selected' : '') + '>Normal</option><option value="bold"' + (fontStyle === 'bold' ? ' selected' : '') + '>Bold</option><option value="italic"' + (fontStyle === 'italic' ? ' selected' : '') + '>Italic</option></select></div>' +
            '<div class="col-md-1"><label class="form-label small">Size</label><input type="text" class="form-control form-control-sm item-font-size" name="items[' + idx + '][font_size]" value="' + (data.font_size || '').replace(/"/g, '&quot;') + '"></div>' +
            '<div class="col-md-1"><label class="form-label small">Weight</label><input type="text" class="form-control form-control-sm" name="items[' + idx + '][font_weight]" value="' + String(fontWeight).replace(/"/g, '&quot;') + '"></div>' +
            '<div class="col-md-1"><label class="form-label small">Spacing</label><input type="text" class="form-control form-control-sm" name="items[' + idx + '][letter_spacing]" value="' + letterSpacing + '"></div>' +
            '<div class="col-md-1"><label class="form-label small">Transform</label><select class="form-select form-select-sm" name="items[' + idx + '][text_transform]"><option value="none"' + (textTransform === 'none' ? ' selected' : '') + '>None</option><option value="uppercase"' + (textTransform === 'uppercase' ? ' selected' : '') + '>Upper</option><option value="lowercase"' + (textTransform === 'lowercase' ? ' selected' : '') + '>Lower</option><option value="capitalize"' + (textTransform === 'capitalize' ? ' selected' : '') + '>Cap</option></select></div>' +
            '<div class="col-md-1"><label class="form-label small">Show</label><div class="form-check mt-1"><input type="checkbox" class="form-check-input item-is-active" name="items[' + idx + '][is_active]" value="1"' + checked + '></div></div>' +
            '<div class="col-md-2"><label class="form-label small">&nbsp;</label><div class="btn-group btn-group-sm w-100"><button type="button" class="btn btn-outline-secondary move-item-up" title="Move up"><i class="las la-arrow-up"></i></button><button type="button" class="btn btn-outline-secondary move-item-down" title="Move down"><i class="las la-arrow-down"></i></button><button type="button" class="btn btn-danger remove-item"><i class="las la-times"></i></button></div></div>' +
            '</div></div><div class="col-md-12 mt-2 item-content-wrap"><label class="form-label small">' + contentLabel + '</label><textarea class="form-control item-content scrollbar-content-field" name="items[' + idx + '][content_text]" rows="3" maxlength="2000">' + contentEscaped + '</textarea><small class="text-muted item-char-count">0 / 2000</small></div>' +
            '<div class="col-md-12 mt-2 item-image-wrap d-none"><label class="form-label small">Image</label><input type="hidden" name="items[' + idx + '][content_image]" class="item-image-content" value="' + (type === 'image' ? content.replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;') : '') + '"><input type="file" class="form-control form-control-sm item-image-file" name="items[' + idx + '][image_file]" accept="image/*"></div>' +
            '</div></div>';
    }
    function updateItemRowVisibility($row) {
        var type = $row.find('.item-type').val();
        $row.find('.item-content-wrap').toggle(type !== 'image');
        $row.find('.item-image-wrap').toggle(type === 'image');
        var $content = $row.find('.item-content');
        var $imgContent = $row.find('.item-image-content');
        if (type === 'image') {
            $row.find('.item-color').closest('.item-text-wrap').addClass('d-none');
            $imgContent.val($content.val() || '');
        } else {
            $row.find('.item-text-wrap').removeClass('d-none');
            // Keep existing textarea text; only restore from hidden image value when textarea is empty.
            // This prevents wiping saved text items on edit page load.
            if (($content.val() || '') === '' && ($imgContent.val() || '') !== '') {
                $content.val($imgContent.val() || '');
            }
            updateCharCount($row);
        }
    }
    function updateCharCount($row) {
        var $field = $row.find('.item-content');
        var $count = $row.find('.item-char-count');
        if ($field.length && $count.length) { var len = ($field.val() || '').length; $count.text(len + ' / 2000'); }
    }
    function escapeHtml(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    function renderRichEditor(segments) {
        var html = '';
        (segments || []).forEach(function(seg, i) {
            var text = (seg && seg.text !== undefined) ? String(seg.text) : '';
            if (!text) return;
            var color = (seg && seg.color) ? String(seg.color) : '#333333';
            var weight = (seg && seg.weight !== undefined && seg.weight !== null) ? String(seg.weight) : '';
            var family = (seg && seg.font_family) ? String(seg.font_family) : 'inherit';
            var size = (seg && seg.font_size) ? String(seg.font_size) : '';
            var style = 'color:' + escapeHtml(color) + ';font-family:' + escapeHtml(family) + ';';
            if (weight) style += 'font-weight:' + escapeHtml(weight) + ';';
            if (size) style += 'font-size:' + escapeHtml(size) + ';';
            html += '<span style="' + style + '">' + escapeHtml(text) + '</span>';
        });
        if (!html) html = '<span style="color:#333333;"></span>';
        $('#scrollbarRichEditor').html(html);
    }
    function rgbToHex(color) {
        if (!color) return '#333333';
        if (color[0] === '#') return color;
        var m = String(color).match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
        if (!m) return '#333333';
        function hx(v){ var n = parseInt(v,10).toString(16); return n.length===1 ? '0'+n : n; }
        return '#' + hx(m[1]) + hx(m[2]) + hx(m[3]);
    }
    function getRichSegmentsFromEditor() {
        var out = [];
        var editor = document.getElementById('scrollbarRichEditor');
        if (!editor) return out;
        var walker = document.createTreeWalker(editor, NodeFilter.SHOW_TEXT, null);
        var node;
        while ((node = walker.nextNode())) {
            var txt = String(node.nodeValue || '').replace(/\u00A0/g, ' ');
            if (!txt || !txt.trim()) continue;
            var p = node.parentElement || editor;
            var cs = window.getComputedStyle(p);
            var inlineWeight = '';
            var inlineSize = '';
            if (p && p.style) {
                inlineWeight = String(p.style.fontWeight || '').trim();
                inlineSize = String(p.style.fontSize || '').trim();
            }
            var computedWeight = String(cs.fontWeight || '400');
            // Persist explicit weight only when user applies bold/strong styles.
            var weightToSave = inlineWeight;
            if (!weightToSave) {
                var wNum = parseInt(computedWeight, 10);
                if (!isNaN(wNum) && wNum >= 600) {
                    weightToSave = computedWeight;
                }
            }
            out.push({
                text: txt,
                color: rgbToHex(cs.color),
                weight: weightToSave,
                font_family: String(cs.fontFamily || 'inherit'),
                font_size: inlineSize
            });
        }
        return out;
    }
    function wrapSelectionWithInlineStyles(styleObj) {
        var range = getEditorRange();
        if (!range || range.collapsed) return;
        var frag = range.extractContents();
        var span = document.createElement('span');
        if (styleObj.fontFamily) span.style.fontFamily = styleObj.fontFamily;
        if (styleObj.fontSize) span.style.fontSize = styleObj.fontSize;
        span.appendChild(frag);
        range.insertNode(span);
        var sel = window.getSelection();
        if (sel) {
            sel.removeAllRanges();
            var newRange = document.createRange();
            newRange.selectNodeContents(span);
            sel.addRange(newRange);
        }
    }
    function getEditorRange() {
        var editor = document.getElementById('scrollbarRichEditor');
        var sel = window.getSelection();
        if (!editor || !sel || sel.rangeCount === 0) return null;
        var range = sel.getRangeAt(0);
        if (!editor.contains(range.commonAncestorContainer)) return null;
        return range;
    }
    function saveEditorSelection() {
        var editor = document.getElementById('scrollbarRichEditor');
        var range = getEditorRange();
        if (!range) return;
        savedEditorRange = range.cloneRange();
        try {
            var pre = document.createRange();
            pre.selectNodeContents(editor);
            pre.setEnd(range.startContainer, range.startOffset);
            savedCaretOffset = pre.toString().length;
        } catch (e) {
            savedCaretOffset = null;
        }
    }
    function setCaretAtOffset(offset) {
        var editor = document.getElementById('scrollbarRichEditor');
        if (!editor) return false;
        var target = Math.max(0, parseInt(offset || 0, 10));
        var walker = document.createTreeWalker(editor, NodeFilter.SHOW_TEXT, null);
        var node;
        var remaining = target;
        while ((node = walker.nextNode())) {
            var len = (node.nodeValue || '').length;
            if (remaining <= len) {
                var r = document.createRange();
                r.setStart(node, remaining);
                r.collapse(true);
                var sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(r);
                return true;
            }
            remaining -= len;
        }
        var endRange = document.createRange();
        endRange.selectNodeContents(editor);
        endRange.collapse(false);
        var selEnd = window.getSelection();
        selEnd.removeAllRanges();
        selEnd.addRange(endRange);
        return true;
    }
    function restoreEditorSelection() {
        var editor = document.getElementById('scrollbarRichEditor');
        if (!editor) return;
        editor.focus();
        var sel = window.getSelection();
        if (!sel) return;
        if (savedEditorRange) {
            try {
                sel.removeAllRanges();
                sel.addRange(savedEditorRange);
                return;
            } catch (e) {
                // fall back to end of editor if stored range becomes invalid
            }
        }
        if (savedCaretOffset !== null && setCaretAtOffset(savedCaretOffset)) {
            return;
        }
        var range = document.createRange();
        range.selectNodeContents(editor);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);
    }
    function applyStyleToSelection(styleObj) {
        var sel = window.getSelection();
        var editor = document.getElementById('scrollbarRichEditor');
        if (!sel || !editor) return;
        if (sel.rangeCount > 0) {
            var range = sel.getRangeAt(0);
            if (!editor.contains(range.commonAncestorContainer)) return;
        }
        if (styleObj && styleObj.color) {
            try {
                document.execCommand('styleWithCSS', false, true);
            } catch (e) {}
            document.execCommand('foreColor', false, styleObj.color);
        }
        if (styleObj && styleObj.fontWeight) {
            var normalized = String(styleObj.fontWeight);
            var makeBold = normalized === '700' || normalized.toLowerCase() === 'bold';
            document.execCommand('bold', false, null);
            if (!makeBold) {
                // If target is normal and command made it bold, toggle once more.
                var focusNode = sel.focusNode && sel.focusNode.parentElement ? sel.focusNode.parentElement : null;
                var isBold = focusNode ? (window.getComputedStyle(focusNode).fontWeight >= 600) : false;
                if (isBold) {
                    document.execCommand('bold', false, null);
                }
            }
        }
        if (styleObj && (styleObj.fontFamily || styleObj.fontSize)) {
            wrapSelectionWithInlineStyles({
                fontFamily: styleObj.fontFamily || '',
                fontSize: styleObj.fontSize || ''
            });
        }
    }
    function applyColorToSelection() {
        restoreEditorSelection();
        var color = $('#scrollbarRichColor').val() || '#333333';
        applyStyleToSelection({ color: color });
        saveEditorSelection();
        refreshScrollbarLivePreview();
    }
    function applyWeightToSelection(weight) {
        restoreEditorSelection();
        applyStyleToSelection({ fontWeight: String(weight || '400') });
        saveEditorSelection();
        refreshScrollbarLivePreview();
    }
    function applyFontFamilyToSelection() {
        restoreEditorSelection();
        var family = $('#scrollbarRichFontFamily').val() || 'inherit';
        applyStyleToSelection({ fontFamily: family });
        saveEditorSelection();
        refreshScrollbarLivePreview();
    }
    function applyFontSizeToSelection() {
        restoreEditorSelection();
        var size = $('#scrollbarRichFontSize').val() || '';
        if (!size) return;
        applyStyleToSelection({ fontSize: size });
        saveEditorSelection();
        refreshScrollbarLivePreview();
    }
    function clearFormattingSelection() {
        restoreEditorSelection();
        var range = getEditorRange();
        if (!range || range.collapsed) return;
        var text = range.toString();
        range.deleteContents();
        var span = document.createElement('span');
        span.style.color = '#111827';
        span.style.fontWeight = '400';
        span.textContent = text;
        range.insertNode(span);
        var sel = window.getSelection();
        sel.removeAllRanges();
        var newRange = document.createRange();
        newRange.selectNodeContents(span);
        sel.addRange(newRange);
        saveEditorSelection();
        refreshScrollbarLivePreview();
    }
    var advancedEmojiList = [
        { e:'🔥', k:'fire hot deal sale offer flash' },
        { e:'🎁', k:'gift present bonus offer promo' },
        { e:'⚡', k:'flash lightning fast quick speed' },
        { e:'💥', k:'boom blast hot trend' },
        { e:'🚀', k:'rocket launch new growth' },
        { e:'💎', k:'diamond premium luxury' },
        { e:'🛍️', k:'shopping bag buy order' },
        { e:'✅', k:'check done verified' },
        { e:'⭐', k:'star featured top rating' },
        { e:'📢', k:'announce announcement megaphone' },
        { e:'🎉', k:'party celebrate celebration' },
        { e:'🎯', k:'target goal focus' },
        { e:'💯', k:'hundred best perfect' },
        { e:'🆕', k:'new fresh latest' },
        { e:'📣', k:'loud announce promo' },
        { e:'🎊', k:'confetti celebration event' },
        { e:'🧨', k:'firecracker blast festival' },
        { e:'✨', k:'sparkles shine' },
        { e:'💫', k:'dizzy stars magic' },
        { e:'🌟', k:'glowing star highlight' },
        { e:'🏷️', k:'tag label discount' },
        { e:'💸', k:'money spend save' },
        { e:'💰', k:'money bag cash profit' },
        { e:'📦', k:'box package delivery' },
        { e:'🚚', k:'truck shipping delivery' },
        { e:'🎈', k:'balloon offer event' },
        { e:'🔔', k:'bell alert notification' },
        { e:'📌', k:'pin important' },
        { e:'🛒', k:'cart checkout buy' },
        { e:'🛵', k:'bike delivery express' },
        { e:'🧾', k:'receipt bill invoice' },
        { e:'👑', k:'crown premium king' },
        { e:'💡', k:'idea tip' },
        { e:'📈', k:'growth up chart' },
        { e:'📊', k:'analytics chart stats' },
        { e:'📱', k:'mobile phone app' },
        { e:'💻', k:'laptop computer' },
        { e:'🎮', k:'game gaming' },
        { e:'🎵', k:'music note' },
        { e:'🎬', k:'movie video' },
        { e:'🍔', k:'burger food' },
        { e:'🍕', k:'pizza food' },
        { e:'☕', k:'coffee drink' },
        { e:'🍎', k:'apple fresh food' },
        { e:'🥳', k:'party happy celebrate' },
        { e:'😎', k:'cool smile' },
        { e:'🤩', k:'star eyes wow' },
        { e:'😍', k:'love heart eyes' },
        { e:'😊', k:'smile happy' },
        { e:'😇', k:'angel innocent' },
        { e:'👏', k:'clap applause' },
        { e:'🙌', k:'raise hands success' },
        { e:'👍', k:'thumbs up like ok' },
        { e:'❤️', k:'heart love red' },
        { e:'🧡', k:'orange heart love' },
        { e:'💛', k:'yellow heart love' },
        { e:'💚', k:'green heart love' },
        { e:'💙', k:'blue heart love' },
        { e:'💜', k:'purple heart love' },
        { e:'🖤', k:'black heart love' },
        { e:'🤍', k:'white heart love' },
        { e:'🤎', k:'brown heart love' },
        { e:'🎀', k:'ribbon gift pink' },
        { e:'📍', k:'location pin map' }
    ];
    function renderEmojiGrid() {
        var html = '';
        advancedEmojiList.forEach(function(item) {
            var em = item.e;
            var keywords = item.k || '';
            html += '<button type="button" class="scrollbar-emoji-btn" data-emoji="' + em + '" title="' + keywords + '">' + em + '</button>';
        });
        if (!html) html = '<div class="text-muted small px-1">No emoji found</div>';
        $('#scrollbarEmojiGrid').html(html);
    }
    function insertEmojiAtCursor(emoji) {
        if (!emoji) return;
        restoreEditorSelection();
        var sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) return;
        var range = sel.getRangeAt(0);
        range.deleteContents();
        var node = document.createTextNode(emoji);
        range.insertNode(node);
        range.setStartAfter(node);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);
        saveEditorSelection();
        var editor = document.getElementById('scrollbarRichEditor');
        if (editor) editor.focus();
        refreshScrollbarLivePreview();
    }
    function buildScrollbarPreviewLiveUrl() {
        var params = [];
        function add(key, val) { if (val === undefined || val === null) return; params.push(encodeURIComponent(key) + '=' + encodeURIComponent(String(val))); }
        var $f = $('#scrollbarFormPage');
        add('position', $f.find('select[name="position"]').val());
        add('template', $f.find('select[name="template"]').val());
        add('bar_height', $f.find('input[name="bar_height"]').val());
        add('scroll_speed', $f.find('input[name="scroll_speed"]').val());
        add('scroll_direction', $f.find('select[name="scroll_direction"]').val());
        add('gap_between_items', $f.find('input[name="gap_between_items"]').val());
        add('bar_background_type', $f.find('select[name="bar_background_type"]').val());
        add('bar_background_value', $f.find('input[name="bar_background_value"]').val());
        var barColor = $('#scrollbarBarColorQuick').val();
        if (barColor) add('bar_color', barColor);
        add('bar_thickness', $f.find('select[name="bar_thickness"]').val());
        add('default_text_size', $f.find('select[name="default_text_size"]').val());
        add('default_text_weight', $f.find('select[name="default_text_weight"]').val());
        add('width_type', $f.find('select[name="width_type"]').val());
        add('width_value', $f.find('input[name="width_value"]').val());
        add('max_width', $f.find('input[name="max_width"]').val());
        add('custom_x_percent', $f.find('input[name="custom_x_percent"]').val());
        add('custom_y_px', $f.find('input[name="custom_y_px"]').val());
        add('custom_width_percent', $f.find('input[name="custom_width_percent"]').val());
        var items = [];
        var rich = getRichSegmentsFromEditor();
        if (rich.length > 0) {
            var plain = rich.map(function(s){ return s.text || ''; }).join(' ').trim();
            items.push({ type: 'text', content: plain.substring(0, 200), color: '#333333', is_active: 1, segments: rich.slice(0, 120) });
        }
        if (items.length > 0) add('items', JSON.stringify(items.slice(0, 8)));
        if (params.length === 0) add('position', $f.find('select[name="position"]').val() || 'header_below');
        return scrollbarPreviewLiveBase + (params.length ? '?' + params.join('&') : '');
    }
    function refreshScrollbarLivePreview() {
        if (scrollbarLivePreviewTimer) clearTimeout(scrollbarLivePreviewTimer);
        scrollbarLivePreviewTimer = setTimeout(function() {
            var url = buildScrollbarPreviewLiveUrl();
            $('#scrollbarLivePreviewIframe').attr('src', url);
            scrollbarLivePreviewTimer = null;
        }, 350);
    }
    function toggleVisibilityAdvanced() {
        var $f = $('#scrollbarFormPage');
        var pageScope = String($f.find('select[name="visibility_pages"]').val() || 'all');
        $('.scrollbar-custom-url-wrap').toggleClass('d-none', pageScope !== 'custom_urls');
    }
    function applyPreset(preset) {
        var $f = $('#scrollbarFormPage');
        var presets = {
            home_offer: {
                position: 'banner_below',
                template: 'offer',
                scroll_speed: 48,
                animation_type: 'linear',
                gap_between_items: 10,
                pause_on_hover: 1,
                bar_thickness: 'normal',
                default_text_size: 'normal',
                default_text_weight: 'bold',
                status: '1'
            },
            breaking_news: {
                position: 'header_below',
                template: 'breaking_news',
                scroll_speed: 60,
                animation_type: 'linear',
                gap_between_items: 12,
                pause_on_hover: 1,
                bar_thickness: 'thin',
                default_text_size: 'normal',
                default_text_weight: 'bold',
                status: '1'
            },
            product_highlight: {
                position: 'product_listing_above',
                template: 'info',
                scroll_speed: 42,
                animation_type: 'ease',
                gap_between_items: 8,
                pause_on_hover: 1,
                bar_thickness: 'normal',
                default_text_size: 'small',
                default_text_weight: 'normal',
                status: '1'
            },
            minimal_clean: {
                position: 'content_top',
                template: 'minimal',
                scroll_speed: 28,
                animation_type: 'linear',
                gap_between_items: 6,
                pause_on_hover: 0,
                bar_thickness: 'extra_thin',
                default_text_size: 'small',
                default_text_weight: 'normal',
                status: '1'
            }
        };
        var conf = presets[preset];
        if (!conf) return;
        Object.keys(conf).forEach(function(k) {
            var $el = $f.find('[name="' + k + '"]');
            if ($el.length) {
                $el.val(String(conf[k])).trigger('change');
            }
        });
        
        refreshScrollbarLivePreview();
    }
    $(document).ready(function() {
        var $form = $('#scrollbarFormPage');
        renderRichEditor(initialRichSegments);
        setTimeout(function() { refreshScrollbarLivePreview(); }, 300);

        renderEmojiGrid();
        @if($isCustomMode)
        toggleVisibilityAdvanced();
        @endif
        $('#scrollbarRichApplyColor').on('click', applyColorToSelection);
        $('#scrollbarRichApplyFont').on('click', applyFontFamilyToSelection);
        $('#scrollbarRichApplySize').on('click', applyFontSizeToSelection);
        $('#scrollbarRichWeightBold').on('click', function(){ applyWeightToSelection('700'); });
        $('#scrollbarRichWeightNormal').on('click', function(){ applyWeightToSelection('400'); });
        $('#scrollbarRichClearFormat').on('click', clearFormattingSelection);
        $(document).on('click', '.rich-quick-color', function() {
            var color = String($(this).data('color') || '').trim();
            if (!color) return;
            $('#scrollbarRichColor').val(color);
            applyColorToSelection();
        });
        $('#scrollbarEmojiPickerToggle').on('click', function() {
            $('#scrollbarEmojiPickerPanel').toggleClass('d-none');
            restoreEditorSelection();
        });
        $(document).on('click', '.scrollbar-emoji-btn', function(){ insertEmojiAtCursor($(this).data('emoji')); });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#scrollbarEmojiPickerPanel, #scrollbarEmojiPickerToggle').length) {
                $('#scrollbarEmojiPickerPanel').addClass('d-none');
            }
        });
        $('#scrollbarRichEditor').on('input keyup mouseup', function() {
            refreshScrollbarLivePreview();
        });
        $('#scrollbarRichEditor').on('keyup mouseup focus blur', function() { saveEditorSelection(); });
        $('#scrollbarRichEditor').on('click', function() { saveEditorSelection(); });
        document.addEventListener('selectionchange', function() {
            var editor = document.getElementById('scrollbarRichEditor');
            var sel = window.getSelection();
            if (!editor || !sel || sel.rangeCount === 0) return;
            var range = sel.getRangeAt(0);
            if (editor.contains(range.commonAncestorContainer)) {
                saveEditorSelection();
            }
        });
        $('#scrollbarRichEditor').on('keydown', function(e) {
            // Ctrl+B: bold selected text
            if (e.ctrlKey && !e.shiftKey && (e.key === 'b' || e.key === 'B')) {
                e.preventDefault();
                applyWeightToSelection('700');
            }
            // Ctrl+Shift+C: apply currently selected color
            if (e.ctrlKey && e.shiftKey && (e.key === 'C' || e.key === 'c')) {
                e.preventDefault();
                applyColorToSelection();
            }
        });
        $('#scrollbarRichApplyColor, #scrollbarRichApplyFont, #scrollbarRichApplySize, #scrollbarRichWeightNormal, #scrollbarRichWeightBold, #scrollbarRichClearFormat, #scrollbarEmojiPickerToggle').on('mousedown', function(e) { e.preventDefault(); });
        $(document).on('mousedown', '.scrollbar-emoji-btn', function(e) { e.preventDefault(); });
        $('.scrollbar-preview-size-btn').on('click', function() {
            var w = $(this).data('width') || '100%';
            $('#scrollbarLivePreviewIframe').css('width', w);
            $('.scrollbar-preview-size-btn').removeClass('active');
            $(this).addClass('active');
        });

        $(document).on('input', '.item-color, .item-font-size, .item-content, .item-image-content', function() { refreshScrollbarLivePreview(); });
        $(document).on('input change', '.item-color-picker', function() {
            var hex = $(this).val();
            $(this).closest('.item-text-wrap').find('.item-color').val(hex);
            refreshScrollbarLivePreview();
        });
        $(document).on('input', '.item-color', function() {
            var v = $(this).val().trim();
            if (/^#[0-9A-Fa-f]{6}$/.test(v) || /^#[0-9A-Fa-f]{3}$/.test(v)) {
                $(this).closest('.item-text-wrap').find('.item-color-picker').val(v.length === 4 ? v[0]+v[1]+v[1]+v[2]+v[2]+v[3]+v[3] : v);
            }
            refreshScrollbarLivePreview();
        });

        $('#scrollbarBarColorQuick').on('input change', function() {
            $form.find('select[name="bar_background_type"]').val('solid');
            $form.find('input[name="bar_background_value"]').val($(this).val());
            refreshScrollbarLivePreview();
        });
        $(document).on('click', '.scrollbar-color-preset', function() {
            var hex = $(this).data('hex');
            if (!hex) return;
            $form.find('select[name="bar_background_type"]').val('solid');
            $form.find('input[name="bar_background_value"]').val(hex);
            $('#scrollbarBarColorQuick').val(hex);
            refreshScrollbarLivePreview();
        });
        $form.on('input change', 'input[name="bar_background_value"]', function() {
            var v = $(this).val().trim();
            if (/^#[0-9A-Fa-f]{6}$/.test(v) || /^#[0-9A-Fa-f]{3}$/.test(v)) $('#scrollbarBarColorQuick').val(v);
            refreshScrollbarLivePreview();
        });
        $form.on('change input', 'select[name="position"], select[name="template"], input[name="bar_height"], input[name="scroll_speed"], select[name="scroll_direction"], select[name="animation_type"], select[name="align"], select[name="bar_background_type"], select[name="bar_thickness"], select[name="default_text_size"], select[name="default_text_weight"], input[name="bar_background_value"], input[name="bar_padding"], input[name="bar_border"], input[name="bar_shadow"]', function() { refreshScrollbarLivePreview(); });
        @if($isCustomMode)
        $form.on('change', 'select[name="visibility_pages"]', function() { toggleVisibilityAdvanced(); });
        @endif
        $form.on('change', 'select[name="position"]', function(){ refreshScrollbarLivePreview(); });

        $('#scrollbarRefreshPreviewBtn').on('click', function() {
            var url = buildScrollbarPreviewLiveUrl();
            $('#scrollbarLivePreviewIframe').attr('src', url);
        });
        $(document).on('click', '.scrollbar-preset-btn', function() {
            var preset = $(this).data('preset');
            applyPreset(preset);
            $('.scrollbar-preset-btn').removeClass('active');
            $(this).addClass('active');
        });
        $form.on('submit', function() {
            var segs = getRichSegmentsFromEditor();
            var plain = segs.map(function(s){ return s.text || ''; }).join(' ').trim();
            $('#scrollbarRichContentInput').val(plain);
            $('#scrollbarRichSegmentsInput').val(JSON.stringify(segs));
            $('#scrollbarItemsContainer').find(':input').prop('disabled', true);
        });
    });
})();
</script>
@endpush
