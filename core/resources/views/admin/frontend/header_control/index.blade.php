@extends('admin.layouts.app')

@section('panel')
@push('style')
<style>
    .header-preview-sticky {
        position: sticky;
        top: 80px;
        z-index: 100;
        transition: all 0.3s ease;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        padding: 10px 20px;
        font-weight: 700;
        color: #64748b;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .nav-tabs-custom .nav-link.active {
        color: #4e73df;
        border-bottom: 2px solid #4e73df;
        background: transparent;
    }
    .header-btn-row {
        border-left: 3px solid #4e73df !important;
        transition: transform 0.2s;
    }
    .header-btn-row:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
    }
    .url-field-highlight {
        background: #f0f7ff !important;
        border-color: #4e73df !important;
        font-weight: 500;
    }

    /* High-Fidelity Virtual Header Preview */
    .v-header {
        width: 100%;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.05);
        margin-bottom: 20px;
        font-family: 'Inter', sans-serif;
    }
    .v-bar {
        width: 100%;
        display: flex;
        align-items: center;
        padding: 0 30px;
        box-sizing: border-box;
        transition: all 0.3s ease;
        position: relative;
    }
    .v-bar--private { opacity: 0.5; filter: grayscale(0.5); }
    .v-bar--private::after {
        content: 'PRIVATE';
        position: absolute;
        right: 10px;
        top: 2px;
        font-size: 8px;
        font-weight: 900;
        color: #ef4444;
        letter-spacing: 1px;
    }
    
    .v-bar-top { font-size: 11px; font-weight: 600; }
    .v-bar-main { border-bottom: 1px solid rgba(0,0,0,0.04); }
    .v-bar-menu { border-bottom: 1px solid rgba(0,0,0,0.02); }
    
    .v-logo { font-weight: 900; font-size: 20px; letter-spacing: -0.5px; color: #0f172a; white-space: nowrap; }
    .v-search { 
        flex-grow: 1; 
        max-width: 500px; 
        height: 36px; 
        background: #f1f5f9; 
        border-radius: 18px; 
        margin: 0 40px; 
        display: flex; 
        align-items: center; 
        padding: 0 15px; 
        color: #94a3b8; 
        font-size: 12px;
        border: 1px solid #e2e8f0;
    }
    .v-icons { display: flex; align-items: center; gap: 15px; }
    .v-icon { width: 32px; height: 32px; background: #f1f5f9; border-radius: 50%; border: 1px solid #e2e8f0; flex-shrink: 0; }
    
    .v-link { 
        margin-right: 25px; 
        font-size: 11px; 
        font-weight: 700; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
        display: flex; 
        align-items: center; 
        gap: 5px;
        cursor: default;
    }
    .v-pill {
        padding: 4px 10px;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 6px;
        margin-left: 10px;
        font-size: 10px;
        white-space: nowrap;
    }
    .v-link--inactive { opacity: 0.3; text-decoration: line-through; }
    .v-dropdown-icon { font-size: 8px; opacity: 0.5; }
    
    .v-cat-btn {
        padding: 0 20px;
        height: 100%;
        background: rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        font-weight: 800;
        margin-right: 20px;
        font-size: 11px;
    }
    
    .preview-mode-badge {
        position: absolute;
        top: 10px;
        right: 20px;
        background: #0f172a;
        color: #fff;
        font-size: 10px;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 700;
        z-index: 50;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    /* Advanced Color Picker Styling */
    .advanced-color-picker {
        flex: 0 0 36px !important;
        width: 36px !important;
        height: 36px !important;
        padding: 2px !important;
        cursor: pointer;
        background-color: #fff;
    }
    .advanced-color-picker::-webkit-color-swatch-wrapper {
        padding: 0;
    }
    .advanced-color-picker::-webkit-color-swatch {
        border: 1px solid #cbd5e1;
        border-radius: 4px;
    }
    .color-hex-input {
        font-family: monospace;
        font-size: 13px !important;
        letter-spacing: 0.5px;
        min-width: 250px;
    }
</style>
@endpush
@php
    $d = $draftConfig;
    $appearance = (array) ($d['appearance'] ?? []);
    $topBar = (array) ($d['top_bar'] ?? []);
    $mainBar = (array) ($d['main_bar'] ?? []);
    $menuBar = (array) ($d['menu_bar'] ?? []);
    $headerItemsToText = function (array $items, int $depth = 1) use (&$headerItemsToText): array {
        $rows = [];
        foreach ($items as $item) {
            $label = trim((string) ($item['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $url = trim((string) ($item['url'] ?? '#')) ?: '#';
            $rows[] = str_repeat('/', max(1, $depth)) . $label . '|' . $url;
            $children = is_array($item['children'] ?? null) ? $item['children'] : [];
            if (!empty($children)) {
                $rows = array_merge($rows, $headerItemsToText($children, $depth + 1));
            }
        }
        return $rows;
    };
@endphp
<div class="row g-3">
    <div class="col-12">
        <div class="card shadow-sm mb-3 border-0 overflow-hidden">
            <div class="card-body p-0 position-relative">
                <div class="preview-mode-badge"><i class="las la-broadcast-tower me-1"></i> @lang('LIVE FRONTEND SYNC')</div>
                
                <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="m-0 text-primary"><i class="las la-eye me-1"></i> @lang('High-Fidelity Virtual Header')</h5>
                    <div id="headerDraftPreviewStats" class="d-flex gap-3 small fw-bold">
                        <span class="text-secondary">@lang('Total'): <span id="statTotal">0</span></span>
                        <span class="text-success">@lang('Public'): <span id="statPublic">0</span></span>
                        <span class="text-primary">@lang('Menus'): <span id="statDropdown">0</span></span>
                    </div>
                </div>

                <!-- High-Fidelity Virtual Header -->
                <div class="p-4 bg-light">
                    <div class="v-header" id="virtualHeaderContainer">
                        <div id="vTopBar" class="v-bar v-bar-top"></div>
                        <div id="vMainBar" class="v-bar v-bar-main"></div>
                        <div id="vMenuBar" class="v-bar v-bar-menu"></div>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-4 small text-muted mt-2">
                        <span><i class="las la-check-circle text-success"></i> @lang('Real-time Appearance Sync')</span>
                        <span><i class="las la-check-circle text-success"></i> @lang('Public/Private Status Visualization')</span>
                        <span><i class="las la-check-circle text-success"></i> @lang('Pro-Layout Preview')</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8 order-lg-1 order-2">
        <form action="{{ route('admin.frontend.sections.header.saveDraft') }}" method="POST" class="card mb-3" id="headerDraftFormMain">
            @csrf
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong><i class="las la-cog me-2"></i>@lang('Navigation Settings')</strong>
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn--primary btn-sm px-3"><i class="las la-save me-1"></i> @lang('Save Draft')</button>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline--dark dropdown-toggle" data-bs-toggle="dropdown">@lang('Tools')</button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><button type="button" class="dropdown-item" id="headerExportJsonBtn">@lang('Export JSON')</button></li>
                            <li><button type="button" class="dropdown-item" id="headerImportJsonBtn">@lang('Import JSON')</button></li>
                        </ul>
                    </div>
                    <input type="file" id="headerImportJsonFile" accept="application/json" class="d-none">
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Global Visibility Controls (Requested Feature) -->
                <div class="bg-light p-3 border-bottom">
                    <h6 class="mb-3 text-primary"><i class="las la-toggle-on me-2"></i>@lang('Header Visibility & Status')</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card border shadow-none bg-white mb-0">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold">@lang('Top Bar')</span>
                                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="top_bar[enabled]" value="1" @checked(!empty($topBar['enabled']))></div>
                                    </div>
                                    <div class="form-check form-switch p-0 d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                        <label class="form-check-label x-small fw-bold text-primary">@lang('Status: Public / Private')</label>
                                        <input class="form-check-input" type="checkbox" name="top_bar[is_public]" value="1" @checked(!empty($topBar['is_public']))>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border shadow-none bg-white mb-0">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold">@lang('Main Bar')</span>
                                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="main_bar[enabled]" value="1" @checked(!empty($mainBar['enabled']))></div>
                                    </div>
                                    <div class="form-check form-switch p-0 d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                        <label class="form-check-label x-small fw-bold text-primary">@lang('Status: Public / Private')</label>
                                        <input class="form-check-input" type="checkbox" name="main_bar[is_public]" value="1" @checked(!empty($mainBar['is_public']))>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border shadow-none bg-white mb-0">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold">@lang('Menu Bar (3rd Bar)')</span>
                                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="menu_bar[enabled]" value="1" @checked(!empty($menuBar['enabled']))></div>
                                    </div>
                                    <div class="form-check form-switch p-0 d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                        <label class="form-check-label x-small fw-bold text-primary">@lang('Status: Public / Private')</label>
                                        <input class="form-check-input" type="checkbox" name="menu_bar[is_public]" value="1" @checked(!empty($menuBar['is_public']))>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs nav-tabs-custom px-3 pt-2" id="headerEditorTabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-appearance" role="tab">@lang('Appearance')</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-topbar" role="tab">@lang('Top Bar')</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-mainbar" role="tab">@lang('Main Bar')</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-menubar" role="tab">@lang('Menu Bar')</a></li>
                </ul>

                <div class="tab-content p-4">
                    <!-- Appearance Tab -->
                    <div class="tab-pane fade show active" id="tab-appearance" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-primary">@lang('Top Bar Background')</label>
                                <div class="input-group">
                                    <input class="form-control advanced-color-picker color-bg-picker flex-shrink-0" type="color" data-target="top_bg_input" data-idx="1" value="{{ preg_match('/#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})/', $appearance['top_bg'] ?? '#0f172a', $m) ? $m[0] : '#0f172a' }}" title="@lang('Primary Color')">
                                    <input class="form-control advanced-color-picker color-bg-picker flex-shrink-0" type="color" data-target="top_bg_input" data-idx="2" value="{{ preg_match('/#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})/', $appearance['top_bg'] ?? '#0f172a', $m) ? $m[0] : '#0f172a' }}" title="@lang('Gradient Color (Optional)')">
                                    <input type="text" class="form-control color-hex-input px-1" id="top_bg_input" name="appearance[top_bg]" value="{{ $appearance['top_bg'] ?? '#0f172a' }}" placeholder="#HEX">
                                    <button type="button" class="btn btn-dark px-2 reset-bg-btn" data-target="top_bg_input" data-default="#0f172a" title="@lang('Reset')"><i class="las la-redo"></i></button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-primary">@lang('Main Bar Background')</label>
                                <div class="input-group">
                                    <input class="form-control advanced-color-picker color-bg-picker flex-shrink-0" type="color" data-target="main_bg_input" data-idx="1" value="{{ preg_match('/#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})/', $appearance['main_bg'] ?? '#f8fafc', $m) ? $m[0] : '#f8fafc' }}">
                                    <input class="form-control advanced-color-picker color-bg-picker flex-shrink-0" type="color" data-target="main_bg_input" data-idx="2" value="{{ preg_match('/#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})/', $appearance['main_bg'] ?? '#f8fafc', $m) ? $m[0] : '#f8fafc' }}">
                                    <input type="text" class="form-control color-hex-input px-1" id="main_bg_input" name="appearance[main_bg]" value="{{ $appearance['main_bg'] ?? '#f8fafc' }}">
                                    <button type="button" class="btn btn-dark px-2 reset-bg-btn" data-target="main_bg_input" data-default="#f8fafc"><i class="las la-redo"></i></button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-primary">@lang('Menu Bar Background')</label>
                                <div class="input-group">
                                    <input class="form-control advanced-color-picker color-bg-picker flex-shrink-0" type="color" data-target="menu_bg_input" data-idx="1" value="{{ preg_match('/#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})/', $appearance['menu_bg'] ?? '#c7eafe', $m) ? $m[0] : '#c7eafe' }}">
                                    <input class="form-control advanced-color-picker color-bg-picker flex-shrink-0" type="color" data-target="menu_bg_input" data-idx="2" value="{{ preg_match('/#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})/', $appearance['menu_bg'] ?? '#c7eafe', $m) ? $m[0] : '#c7eafe' }}">
                                    <input type="text" class="form-control color-hex-input px-1" id="menu_bg_input" name="appearance[menu_bg]" value="{{ $appearance['menu_bg'] ?? '#c7eafe' }}">
                                    <button type="button" class="btn btn-dark px-2 reset-bg-btn" data-target="menu_bg_input" data-default="#c7eafe"><i class="las la-redo"></i></button>
                                </div>
                            </div>

                            <!-- Bar Heights Split -->
                            <div class="col-12 mt-4 pt-2">
                                <h6 class="mb-2 small fw-bold text-muted">@lang('Bar Heights (px)')</h6>
                                <div class="d-flex gap-3">
                                    <div class="input-group input-group-sm" style="width: 130px;">
                                        <span class="input-group-text bg-light text-muted">T</span>
                                        <input type="number" min="30" max="80" class="form-control text-center" name="appearance[top_height]" value="{{ (int) ($appearance['top_height'] ?? 38) }}">
                                    </div>
                                    <div class="input-group input-group-sm" style="width: 130px;">
                                        <span class="input-group-text bg-light text-muted">M</span>
                                        <input type="number" min="40" max="100" class="form-control text-center" name="appearance[main_height]" value="{{ (int) ($appearance['main_height'] ?? 56) }}">
                                    </div>
                                    <div class="input-group input-group-sm" style="width: 130px;">
                                        <span class="input-group-text bg-light text-muted">B</span>
                                        <input type="number" min="30" max="80" class="form-control text-center" name="appearance[menu_height]" value="{{ (int) ($appearance['menu_height'] ?? 38) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Container Widths -->
                            <div class="col-12 mt-4 pt-2 border-top">
                                <h6 class="mb-2 small text-muted">@lang('Container Widths (Responsive Control)')</h6>
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="small fw-bold text-muted">@lang('Desktop')</label>
                                        <input type="number" min="1280" max="1920" class="form-control form-control-sm" name="appearance[width_desktop]" value="{{ (int) ($appearance['width_desktop'] ?? 1920) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small fw-bold text-muted">@lang('Laptop')</label>
                                        <input type="number" min="1024" max="1800" class="form-control form-control-sm" name="appearance[width_laptop]" value="{{ (int) ($appearance['width_laptop'] ?? 1600) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small fw-bold text-muted">@lang('Tablet')</label>
                                        <input type="number" min="768" max="1400" class="form-control form-control-sm" name="appearance[width_tablet]" value="{{ (int) ($appearance['width_tablet'] ?? 1200) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small fw-bold text-muted">@lang('Mobile layout width (px)')</label>
                                        <input type="number" min="320" max="900" class="form-control form-control-sm" name="appearance[width_mobile]" value="{{ (int) ($appearance['width_mobile'] ?? 100) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Bar Tab -->
                    <div class="tab-pane fade" id="tab-topbar" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-12 d-flex flex-wrap gap-3 mb-3">
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="top_bar[show_language]" value="1" @checked(!empty($topBar['show_language']))><label class="form-check-label">@lang('Language')</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="top_bar[show_currency]" value="1" @checked(!empty($topBar['show_currency']))><label class="form-check-label">@lang('Currency')</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="top_bar[show_apps]" value="1" @checked(!empty($topBar['show_apps']))><label class="form-check-label">@lang('Apps Menu')</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="top_bar[show_seller_button]" value="1" @checked(!empty($topBar['show_seller_button']))><label class="form-check-label">@lang('Seller Button')</label></div>
                            </div>
                            <div class="col-md-4"><label class="form-label small fw-bold">@lang('Support Contact Info')</label><input class="form-control mb-2 form-control-sm" name="top_bar[support_label]" value="{{ $topBar['support_label'] ?? '24/7 Support' }}" placeholder="Label"><input class="form-control mb-2 form-control-sm" name="top_bar[support_phone]" value="{{ $topBar['support_phone'] ?? '' }}" placeholder="Phone"><input class="form-control form-control-sm" type="email" name="top_bar[support_email]" value="{{ $topBar['support_email'] ?? '' }}" placeholder="Email"></div>
                            <div class="col-md-4"><label class="form-label small fw-bold">@lang('Modes')</label><div class="mb-3"><span class="small text-muted">@lang('Language')</span><select class="form-control form-control-sm" name="top_bar[language_mode]"><option value="code" @selected(($topBar['language_mode'] ?? 'code') === 'code')>@lang('Show Code (EN)')</option><option value="name" @selected(($topBar['language_mode'] ?? '') === 'name')>@lang('Show Name (English)')</option></select></div><div><span class="small text-muted">@lang('Currency')</span><select class="form-control form-control-sm" name="top_bar[currency_mode]"><option value="code" @selected(($topBar['currency_mode'] ?? 'code') === 'code')>@lang('Show Code (USD)')</option><option value="name" @selected(($topBar['currency_mode'] ?? '') === 'name')>@lang('Show Name (Dollar)')</option></select></div></div>
                            <div class="col-md-4"><label class="form-label small fw-bold">@lang('Seller Button Profile')</label><input class="form-control mb-2 form-control-sm" name="top_bar[seller_text]" value="{{ $topBar['seller_text'] ?? 'BECOME A SELLER' }}" placeholder="Text"><input class="form-control form-control-sm" name="top_bar[seller_url]" value="{{ $topBar['seller_url'] ?? '/seller/apply' }}" placeholder="URL"></div>
                        </div>
                    </div>

                    <!-- Main Bar Tab -->
                    <div class="tab-pane fade" id="tab-mainbar" role="tabpanel">
                        <div class="row g-3">
                             <div class="col-md-5 d-flex flex-wrap gap-3">
                                <div class="form-check mt-3 w-100"><input class="form-check-input" type="checkbox" name="main_bar[show_language_icon]" value="1" @checked(!empty($mainBar['show_language_icon']))><label class="form-check-label">@lang('Show Lang Icon')</label></div>
                            </div>
                            <div class="col-md-4"><label class="form-label small fw-bold">@lang('Logo Max Height (px)')</label><input type="number" min="28" max="90" class="form-control" name="main_bar[logo_max_height]" value="{{ (int) ($mainBar['logo_max_height'] ?? 48) }}"><div class="form-text small">@lang('Ensures logo doesn\'t break layout')</div></div>
                            <div class="col-md-4"><label class="form-label small fw-bold">@lang('Icon Size (px)')</label><input type="number" min="28" max="72" class="form-control" name="main_bar[icon_size]" value="{{ (int) ($mainBar['icon_size'] ?? 48) }}"><div class="form-text small">@lang('Cart, Wishlist, User icons size')</div></div>
                        </div>
                    </div>

                    <!-- Menu Bar Tab -->
                    <div class="tab-pane fade" id="tab-menubar" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-12 d-flex flex-wrap gap-3 mb-3">
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="menu_bar[show_sidebar_trigger]" value="1" @checked(!empty($menuBar['show_sidebar_trigger']))><label class="form-check-label">@lang('Sidebar Toggle')</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="menu_bar[show_category_button]" value="1" @checked(!empty($menuBar['show_category_button']))><label class="form-check-label">@lang('Category Button')</label></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="menu_bar[show_seller_button]" value="1" @checked(!empty($menuBar['show_seller_button']))><label class="form-check-label">@lang('Seller Button')</label></div>
                            </div>
                            <div class="col-md-4"><label class="form-label small fw-bold">@lang('Category Button Label')</label><input class="form-control form-control-sm" name="menu_bar[category_button_label]" value="{{ $menuBar['category_button_label'] ?? 'ALL CATEGORIES' }}"></div>
                            <div class="col-md-4"><label class="form-label small fw-bold">@lang('Seller Button Profile')</label><input class="form-control mb-2 form-control-sm" name="menu_bar[seller_text]" value="{{ $menuBar['seller_text'] ?? 'BECOME A SELLER' }}" placeholder="Text"><input class="form-control form-control-sm" name="menu_bar[seller_url]" value="{{ $menuBar['seller_url'] ?? '/seller/apply' }}" placeholder="URL"></div>
                            <div class="col-12 mt-2">
                                <label class="form-label small fw-bold">@lang('Quick Category Menu Items')</label>
                                <textarea class="form-control form-control-sm" rows="3" name="menu_bar[category_items_text]" placeholder="Label|URL (one per line)">@foreach((array)($menuBar['category_items'] ?? []) as $item){{ ($item['label'] ?? '') }}|{{ ($item['url'] ?? '#') }}
@endforeach</textarea>
                                <div class="form-text small">@lang('One per line: Name|/url')</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


                @php
                    $groups = [
                        ['wrap' => 'topButtonsWrap', 'prefix' => 'top_bar[custom_buttons]', 'title' => __('Top bar custom buttons'), 'kind' => 'top_custom', 'rows' => (array) ($topBar['custom_buttons'] ?? [])],
                        ['wrap' => 'menuNavButtonsWrap', 'prefix' => 'menu_bar[nav_links]', 'title' => __('Header buttons'), 'kind' => 'menu_nav', 'rows' => (array) ($menuBar['nav_links'] ?? [])],
                        ['wrap' => 'menuButtonsWrap', 'prefix' => 'menu_bar[custom_buttons]', 'title' => '', 'kind' => 'menu_custom', 'rows' => (array) ($menuBar['custom_buttons'] ?? [])],
                    ];
                @endphp

                @foreach($groups as $g)
                    <div class="{{ $loop->first ? 'mt-4' : 'mt-2' }}">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                            @if($g['title'] !== '')
                                <label class="form-label mb-0">{{ $g['title'] }}</label>
                            @else
                                <div class="small text-muted">@lang('More buttons')</div>
                            @endif
                            <div class="d-flex gap-2">
                                <input type="text" class="form-control form-control-sm header-btn-search" data-target-wrap="{{ $g['wrap'] }}" placeholder="@lang('Search button')">
                                <button type="button" class="btn btn-outline--primary btn-sm" onclick="window.addHeaderButtonRow('{{ $g['kind'] }}')">@lang('+ Add button')</button>
                            </div>
                        </div>
                        <div id="{{ $g['wrap'] }}" data-prefix="{{ $g['prefix'] }}">
                            @foreach($g['rows'] as $idx => $btn)
                                <div class="header-btn-row card mb-2 border-0 shadow-sm">
                                    <div class="card-body py-2">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-auto">
                                                <div class="btn-group-vertical btn-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary py-0 px-1 row-move-up"><i class="las la-angle-up"></i></button>
                                                    <button type="button" class="btn btn-outline-secondary py-0 px-1 row-move-down"><i class="las la-angle-down"></i></button>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light text-muted">@lang('Name')</span>
                                                    <input class="form-control" name="{{ $g['prefix'] }}[{{ $idx }}][label]" value="{{ $btn['label'] ?? '' }}" placeholder="@lang('e.g. Home')">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg--primary text-white" title="Link set directly inside button">@lang('Link/URL')</span>
                                                    <input class="form-control url-field-highlight" name="{{ $g['prefix'] }}[{{ $idx }}][url]" value="{{ $btn['url'] ?? '#' }}" placeholder="e.g. https://google.com">
                                                </div>
                                                <div class="form-text x-small text-muted mt-1">@lang('Set direct page link here (Admin only control)')</div>
                                            </div>
                                            <div class="col-md-2">
                                                <select class="form-control form-control-sm header-btn-type" name="{{ $g['prefix'] }}[{{ $idx }}][type]">
                                                    <option value="link" @selected(($btn['type'] ?? 'link') === 'link')>@lang('🔗 Single Link')</option>
                                                    <option value="dropdown" @selected(($btn['type'] ?? '') === 'dropdown')>@lang('📂 Dropdown Menu')</option>
                                                </select>
                                            </div>
                                            <div class="col-md-auto flex-grow-1 text-end d-flex gap-1 justify-content-end">
                                                <div class="form-check form-switch d-inline-block mt-1 me-2" title="@lang('Public Visibility')">
                                                    <input class="form-check-input" type="checkbox" name="{{ $g['prefix'] }}[{{ $idx }}][is_active]" value="1" @checked((int)($btn['is_active'] ?? 1) === 1)>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline--secondary border-0" onclick="$(this).closest('.header-btn-row').find('.advanced-options').toggle()" title="@lang('Advanced Features')">⚙️</button>
                                                <button type="button" class="btn btn-sm btn-outline--primary border-0" onclick="window.duplicateHeaderButtonRow(this)" title="@lang('Duplicate')"><i class="las la-copy"></i></button>
                                                <button type="button" class="btn btn-sm btn-outline--danger border-0" onclick="window.removeHeaderButtonRow(this)" title="@lang('Remove')"><i class="las la-times"></i></button>
                                            </div>
                                            <div class="col-12 advanced-options" style="display:none;">
                                                <div class="p-2 bg-light rounded mt-1 border">
                                                    <div class="row g-2">
                                                        <div class="col-md-4"><label class="small text-muted">@lang('Tracking Key / Analytics ID')</label><input class="form-control form-control-sm" name="{{ $g['prefix'] }}[{{ $idx }}][tracking_key]" value="{{ $btn['tracking_key'] ?? '' }}"></div>
                                                        <div class="col-md-4"><label class="small text-muted">@lang('Dropdown Panel Style')</label><select class="form-control form-control-sm header-panel-style" name="{{ $g['prefix'] }}[{{ $idx }}][dropdown_style]"><option value="dropdown" @selected(($btn['dropdown_style'] ?? 'dropdown') === 'dropdown')>@lang('Simple Vertical List')</option><option value="mega" @selected(($btn['dropdown_style'] ?? '') === 'mega')>@lang('Full Width Mega Menu')</option></select></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 header-btn-items-wrap mt-2" style="{{ (($btn['type'] ?? 'link') === 'dropdown') ? '' : 'display:none;' }}">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <label class="small fw-bold text--primary mb-0">@lang('Sub-menu Items (Visual Nested List)')</label>
                                                    <div class="btn-group btn-group-sm"><button type="button" class="btn btn-xs btn-outline-secondary px-2" onclick="window.insertNestedLine(this, '/')" title="@lang('Add Level 1')">+ L1</button><button type="button" class="btn btn-xs btn-outline-secondary px-2" onclick="window.insertNestedLine(this, '//')" title="@lang('Add Level 2')">+ L2</button><button type="button" class="btn btn-xs btn-outline-secondary px-2" onclick="window.insertNestedLine(this, '///')" title="@lang('Add Level 3')">+ L3</button><button type="button" class="btn btn-xs btn-outline-info px-2" onclick="alert('@lang('Syntax: Name|/url\nUse / for depth: //Child|/url')')">?</button></div>
                                                </div>
                                                <textarea class="form-control form-control-sm" rows="4" name="{{ $g['prefix'] }}[{{ $idx }}][items_text]" placeholder="Name|/url&#10;/Nested Name|/url">{{ implode("\n", $headerItemsToText((array) ($btn['items'] ?? []), 1)) }}</textarea>
                                                <div class="form-text small">@lang('Use slashes (/, //, ///) for nested levels.')</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer text-end"><button type="submit" class="btn btn--primary btn-sm">@lang('Save Draft')</button></div>
        </form>

        <form action="{{ route('admin.frontend.sections.header.publish') }}" method="POST" class="card">@csrf
            <div class="card-body text-end"><button type="submit" class="btn btn--success">@lang('Publish Saved Draft')</button></div>
        </form>
    </div>

    <div class="col-lg-4 order-lg-2 order-1 px-lg-0">
        <div class="header-preview-sticky">
            <div class="card shadow-sm border-0 mb-3 bg--primary text-white">
                <div class="card-body p-3">
                    <h6 class="mb-2 text-white"><i class="las la-info-circle me-2"></i>@lang('How it works')</h6>
                    <p class="small mb-0 opacity-75">
                        @lang('This editor allows you to manage all three header bars from one place. Changes are saved as a "Draft" so you can refine your layout before going live.')
                    </p>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-3 small text-muted">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="las la-keyboard"></i> <span class="fw-bold">@lang('Shortcuts')</span>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1"><kbd>Ctrl+S</kbd> : @lang('Save')</li>
                        <li><kbd>Ctrl+Enter</kbd> : @lang('Duplicate')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    (function () {
        function reindexRows(wrap) {
            if (!wrap) return;
            var prefix = wrap.getAttribute('data-prefix');
            var escapedPrefix = (prefix || '').replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            wrap.querySelectorAll('.header-btn-row').forEach(function (row, idx) {
                row.querySelectorAll('input, select, textarea').forEach(function (el) {
                    var n = el.getAttribute('name');
                    if (!n || !prefix) return;
                    el.setAttribute('name', n.replace(new RegExp(escapedPrefix + '\\[[0-9]+\\]'), prefix + '[' + idx + ']'));
                });
            });
        }

        function bindRowEvents(scope) {
            (scope || document).querySelectorAll('.header-btn-type').forEach(function (el) {
                if (el.dataset.bound === '1') return;
                el.dataset.bound = '1';
                el.addEventListener('change', function () {
                    var row = this.closest('.header-btn-row');
                    var itemsWrap = row ? row.querySelector('.header-btn-items-wrap') : null;
                    if (itemsWrap) itemsWrap.style.display = this.value === 'dropdown' ? '' : 'none';
                    renderQuickPreview();
                });
            });
            (scope || document).querySelectorAll('input, select, textarea').forEach(function (el) {
                if (el.dataset.livebound === '1') return;
                el.dataset.livebound = '1';
                el.addEventListener('input', renderQuickPreview);
                el.addEventListener('change', renderQuickPreview);
            });
            (scope || document).querySelectorAll('.row-move-up').forEach(function (btn) {
                if (btn.dataset.bound === '1') return;
                btn.dataset.bound = '1';
                btn.addEventListener('click', function () {
                    var row = btn.closest('.header-btn-row');
                    moveRow(row, -1);
                });
            });
            (scope || document).querySelectorAll('.row-move-down').forEach(function (btn) {
                if (btn.dataset.bound === '1') return;
                btn.dataset.bound = '1';
                btn.addEventListener('click', function () {
                    var row = btn.closest('.header-btn-row');
                    moveRow(row, 1);
                });
            });
        }

        function moveRow(row, delta) {
            if (!row || !row.parentElement) return;
            var wrap = row.parentElement;
            var topWrap = document.getElementById('topButtonsWrap');
            var navWrap = document.getElementById('menuNavButtonsWrap');
            var customWrap = document.getElementById('menuButtonsWrap');
            if (delta < 0) {
                var prev = row.previousElementSibling;
                if (prev) {
                    wrap.insertBefore(row, prev);
                } else if (wrap === customWrap && navWrap && navWrap.querySelector('.header-btn-row')) {
                    navWrap.appendChild(row);
                    wrap = navWrap;
                } else if (wrap === navWrap && topWrap && topWrap.querySelector('.header-btn-row')) {
                    topWrap.appendChild(row);
                    wrap = topWrap;
                }
            } else {
                var next = row.nextElementSibling;
                if (next) {
                    wrap.insertBefore(next, row);
                } else if (wrap === topWrap && navWrap) {
                    if (navWrap.firstElementChild) {
                        navWrap.insertBefore(row, navWrap.firstElementChild);
                    } else {
                        navWrap.appendChild(row);
                    }
                    wrap = navWrap;
                } else if (wrap === navWrap && customWrap) {
                    if (customWrap.firstElementChild) {
                        customWrap.insertBefore(row, customWrap.firstElementChild);
                    } else {
                        customWrap.appendChild(row);
                    }
                    wrap = customWrap;
                }
            }
            reindexRows(topWrap);
            reindexRows(navWrap);
            reindexRows(customWrap);
            renderQuickPreview();
        }

        function addRow(kind) {
            var wrap = kind === 'top_custom'
                ? document.getElementById('topButtonsWrap')
                : (kind === 'menu_nav' ? document.getElementById('menuNavButtonsWrap') : document.getElementById('menuButtonsWrap'));
            if (!wrap) return;
            var prefix = wrap.getAttribute('data-prefix');
            var idx = wrap.querySelectorAll('.header-btn-row').length;
            var html = `
                <div class="header-btn-row card mb-2 border-0 shadow-sm">
                    <div class="card-body py-2">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-auto">
                                <div class="btn-group-vertical btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary py-0 px-1 row-move-up"><i class="las la-angle-up"></i></button>
                                    <button type="button" class="btn btn-outline-secondary py-0 px-1 row-move-down"><i class="las la-angle-down"></i></button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted">@lang('Name')</span>
                                    <input class="form-control" name="${prefix}[${idx}][label]" placeholder="@lang('e.g. Home')">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg--primary text-white">@lang('Link/URL')</span>
                                    <input class="form-control url-field-highlight" name="${prefix}[${idx}][url]" value="#" placeholder="https://...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control form-control-sm header-btn-type" name="${prefix}[${idx}][type]">
                                    <option value="link">@lang('🔗 Single Link')</option>
                                    <option value="dropdown">@lang('📂 Dropdown Menu')</option>
                                </select>
                            </div>
                            <div class="col-md-auto flex-grow-1 text-end d-flex gap-1 justify-content-end">
                                <div class="form-check form-switch d-inline-block mt-1 me-2" title="@lang('Public Visibility')">
                                    <input class="form-check-input" type="checkbox" name="${prefix}[${idx}][is_active]" value="1" checked>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline--secondary border-0" onclick="$(this).closest('.header-btn-row').find('.advanced-options').toggle()" title="@lang('Advanced Features')">⚙️</button>
                                <button type="button" class="btn btn-sm btn-outline--primary border-0" onclick="window.duplicateHeaderButtonRow(this)" title="@lang('Duplicate')"><i class="las la-copy"></i></button>
                                <button type="button" class="btn btn-sm btn-outline--danger border-0" onclick="window.removeHeaderButtonRow(this)" title="@lang('Remove')"><i class="las la-times"></i></button>
                            </div>
                            <div class="col-12 advanced-options" style="display:none;">
                                <div class="p-2 bg-light rounded mt-1 border">
                                    <div class="row g-2">
                                        <div class="col-md-4"><label class="small text-muted">@lang('Tracking Key / Analytics ID')</label><input class="form-control form-control-sm" name="${prefix}[${idx}][tracking_key]"></div>
                                        <div class="col-md-4"><label class="small text-muted">@lang('Dropdown Panel Style')</label><select class="form-control form-control-sm header-panel-style" name="${prefix}[${idx}][dropdown_style]"><option value="dropdown">@lang('Simple Vertical List')</option><option value="mega">@lang('Full Width Mega Menu')</option></select></div>
                                    </div>
                                </div>
                                            <div class="col-12 header-btn-items-wrap mt-2" style="display:none;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="small fw-bold text--primary mb-0">@lang('Sub-menu Items (Visual Nested List)')</label>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-xs btn-outline-primary px-2" onclick="window.loadTemplateLinks(this)" title="@lang('Load Professional Examples')">✨ @lang('Load Examples')</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary px-2" onclick="window.insertNestedLine(this, '/')" title="@lang('Add Level 1')">+ L1</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary px-2" onclick="window.insertNestedLine(this, '//')" title="@lang('Add Level 2')">+ L2</button>
                                        <button type="button" class="btn btn-xs btn-outline-info px-2" onclick="alert('@lang('Syntax: Name|/url\nUse / for depth: //Child|/url')')">?</button>
                                    </div>
                                </div>
                                <textarea class="form-control form-control-sm" rows="4" name="${prefix}[${idx}][items_text]" placeholder="/All Categories|/categories&#10;/Track Order|/track-order&#10;/Customer Support|/contact"></textarea>
                                <div class="form-text small text-muted">@lang('Format: /Name|/link (Use slashes for nested levels)')</div>
                            </div>
             </div>
                        </div>
                    </div>
                </div>`;
            wrap.insertAdjacentHTML('beforeend', html);
            bindRowEvents(wrap);
            renderQuickPreview();
        }

        function duplicateRow(btn) {
            var row = btn.closest('.header-btn-row');
            if (!row || !row.parentElement) return;
            var clone = row.cloneNode(true);
            row.parentElement.insertBefore(clone, row.nextSibling);
            reindexRows(row.parentElement);
            bindRowEvents(row.parentElement);
            renderQuickPreview();
        }

        function removeRow(btn) {
            var row = btn.closest('.header-btn-row');
            var wrap = row ? row.parentElement : null;
            if (row) row.remove();
            reindexRows(wrap);
            renderQuickPreview();
        }

        function renderQuickPreview() {
            var vTop = document.getElementById('vTopBar');
            var vMain = document.getElementById('vMainBar');
            var vMenu = document.getElementById('vMenuBar');
            if (!vTop || !vMain || !vMenu) return;

            // Appearance settings
            var topBg = document.querySelector('[name="appearance[top_bg]"]')?.value || '#0f172a';
            var mainBg = document.querySelector('[name="appearance[main_bg]"]')?.value || '#f8fafc';
            var menuBg = document.querySelector('[name="appearance[menu_bg]"]')?.value || '#c7eafe';
            
            var topH = parseInt(document.querySelector('[name="appearance[top_height]"]')?.value || '38');
            var mainH = parseInt(document.querySelector('[name="appearance[main_height]"]')?.value || '56');
            var menuH = parseInt(document.querySelector('[name="appearance[menu_height]"]')?.value || '38');
            
            var topEnabled = document.querySelector('[name="top_bar[enabled]"]')?.checked;
            var mainEnabled = document.querySelector('[name="main_bar[enabled]"]')?.checked;
            var menuEnabled = document.querySelector('[name="menu_bar[enabled]"]')?.checked;
            
            var topPublic = document.querySelector('[name="top_bar[is_public]"]')?.checked;
            var mainPublic = document.querySelector('[name="main_bar[is_public]"]')?.checked;
            var menuPublic = document.querySelector('[name="menu_bar[is_public]"]')?.checked;

            // Apply Styles
            vTop.style.cssText = `height:${topH}px; background:${topBg}; color:white; display:${topEnabled ? 'flex' : 'none'};`;
            vTop.className = 'v-bar v-bar-top' + (topPublic ? '' : ' v-bar--private');
            
            vMain.style.cssText = `height:${mainH}px; background:${mainBg}; color:#0f172a; display:${mainEnabled ? 'flex' : 'none'};`;
            vMain.className = 'v-bar v-bar-main' + (mainPublic ? '' : ' v-bar--private');
            
            vMenu.style.cssText = `height:${menuH}px; background:${menuBg}; color:#0f172a; display:${menuEnabled ? 'flex' : 'none'};`;
            vMenu.className = 'v-bar v-bar-menu' + (menuPublic ? '' : ' v-bar--private');

            // Render Content
            var stats = { total: 0, public: 0, dropdown: 0 };
            
            // Top Bar Content
            var supportLabel = document.querySelector('[name="top_bar[support_label]"]')?.value || 'Support';
            var supportPhone = document.querySelector('[name="top_bar[support_phone]"]')?.value || '';
            vTop.innerHTML = `<span><i class="las la-headset me-1"></i> ${supportLabel}: ${supportPhone}</span><div style="flex-grow:1"></div>`;
            
            // Main Bar Content
            var logoH = parseInt(document.querySelector('[name="main_bar[logo_max_height]"]')?.value || '48');
            var iconS = parseInt(document.querySelector('[name="main_bar[icon_size]"]')?.value || '32');
            vMain.innerHTML = `
                <div class="v-logo" style="max-height:${logoH}px">STAYLBD</div>
                <div class="v-search">Search for products...</div>
                <div class="v-icons">
                    <div class="v-icon" style="width:${iconS}px; height:${iconS}px"></div>
                    <div class="v-icon" style="width:${iconS}px; height:${iconS}px"></div>
                    <div class="v-icon" style="width:${iconS}px; height:${iconS}px"></div>
                </div>
            `;

            // Menu Bar Content
            vMenu.innerHTML = '';
            if (document.querySelector('[name="menu_bar[show_category_button]"]')?.checked) {
                var catLabel = document.querySelector('[name="menu_bar[category_button_label]"]')?.value || 'CATEGORIES';
                vMenu.innerHTML += `<div class="v-cat-btn"><i class="las la-bars me-2"></i> ${catLabel}</div>`;
            }

            // Buttons Rendering
            ['topButtonsWrap', 'menuNavButtonsWrap', 'menuButtonsWrap'].forEach(function (id) {
                var wrap = document.getElementById(id);
                if (!wrap) return;
                wrap.querySelectorAll('.header-btn-row').forEach(function (row) {
                    var label = (row.querySelector('input[name*="[label]"]')?.value || '').trim();
                    if (!label) return;
                    var active = row.querySelector('input[name*="[is_active]"]')?.checked;
                    var type = row.querySelector('select[name*="[type]"]')?.value || 'link';

                    stats.total++;
                    if (active) stats.public++;
                    if (type === 'dropdown') stats.dropdown++;

                    if (id === 'topButtonsWrap') {
                        vTop.innerHTML += `<div class="v-pill ${active ? '' : 'v-link--inactive'}">${label}</div>`;
                    } else {
                        vMenu.innerHTML += `
                            <div class="v-link ${active ? '' : 'v-link--inactive'}">
                                ${label} ${type === 'dropdown' ? '<span class="v-dropdown-icon">▼</span>' : ''}
                            </div>
                        `;
                    }
                });
            });

            document.getElementById('statTotal').innerText = stats.total;
            document.getElementById('statPublic').innerText = stats.public;
            document.getElementById('statDropdown').innerText = stats.dropdown;
        }

        // Shortcuts
        document.addEventListener('keydown', function(e) {
            // Save: Ctrl+S
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                document.getElementById('headerDraftFormMain').submit();
            }
            // Duplicate: Ctrl+Enter (if focusing on a row input)
            if (e.ctrlKey && e.key === 'Enter') {
                var active = document.activeElement;
                var row = active ? active.closest('.header-btn-row') : null;
                if (row) {
                    e.preventDefault();
                    var btn = row.querySelector('.btn-outline--primary[onclick*="duplicate"]');
                    if (btn) btn.click();
                }
            }
        });

        document.querySelectorAll('.header-btn-search').forEach(function (input) {
            input.addEventListener('input', function () {
                var q = (this.value || '').trim().toLowerCase();
                var wrap = document.getElementById(this.getAttribute('data-target-wrap'));
                if (!wrap) return;
                wrap.querySelectorAll('.header-btn-row').forEach(function (row) {
                    var labelEl = row.querySelector('input[name*="[label]"]');
                    var txt = labelEl ? (labelEl.value || '').toLowerCase() : '';
                    row.style.display = !q || txt.indexOf(q) !== -1 ? '' : 'none';
                });
            });
        });


        function readButtonRows(wrapId) {
            var wrap = document.getElementById(wrapId);
            if (!wrap) return [];
            var out = [];
            wrap.querySelectorAll('.header-btn-row').forEach(function (row) {
                var labelEl = row.querySelector('input[name*="[label]"]');
                var urlEl = row.querySelector('input[name*="[url]"]');
                var trackingEl = row.querySelector('input[name*="[tracking_key]"]');
                var typeEl = row.querySelector('select[name*="[type]"]');
                var styleEl = row.querySelector('select[name*="[dropdown_style]"]');
                var activeEl = row.querySelector('input[name*="[is_active]"]');
                var itemsEl = row.querySelector('textarea[name*="[items_text]"]');
                var label = labelEl ? (labelEl.value || '').trim() : '';
                if (!label) return;
                out.push({
                    label: label,
                    url: urlEl ? ((urlEl.value || '').trim() || '#') : '#',
                    tracking_key: trackingEl ? (trackingEl.value || '').trim() : '',
                    type: typeEl ? typeEl.value : 'link',
                    dropdown_style: styleEl ? styleEl.value : 'dropdown',
                    is_active: !activeEl || activeEl.checked ? 1 : 0,
                    items_text: itemsEl ? (itemsEl.value || '') : ''
                });
            });
            return out;
        }

        function collectHeaderConfig() {
            return {
                appearance: {
                    top_bg: document.querySelector('[name="appearance[top_bg]"]')?.value || '#0f172a',
                    main_bg: document.querySelector('[name="appearance[main_bg]"]')?.value || '#f8fafc',
                    menu_bg: document.querySelector('[name="appearance[menu_bg]"]')?.value || '#c7eafe',
                    top_height: parseInt(document.querySelector('[name="appearance[top_height]"]')?.value || '38', 10),
                    main_height: parseInt(document.querySelector('[name="appearance[main_height]"]')?.value || '56', 10),
                    menu_height: parseInt(document.querySelector('[name="appearance[menu_height]"]')?.value || '38', 10),
                    width_desktop: parseInt(document.querySelector('[name="appearance[width_desktop]"]')?.value || '1920', 10),
                    width_laptop: parseInt(document.querySelector('[name="appearance[width_laptop]"]')?.value || '1600', 10),
                    width_tablet: parseInt(document.querySelector('[name="appearance[width_tablet]"]')?.value || '1200', 10),
                    width_mobile: parseInt(document.querySelector('[name="appearance[width_mobile]"]')?.value || '768', 10)
                },
                top_bar: {
                    enabled: document.querySelector('[name="top_bar[enabled]"]')?.checked ? 1 : 0,
                    is_public: document.querySelector('[name="top_bar[is_public]"]')?.checked ? 1 : 0,
                    show_language: document.querySelector('[name="top_bar[show_language]"]')?.checked ? 1 : 0,
                    show_currency: document.querySelector('[name="top_bar[show_currency]"]')?.checked ? 1 : 0,
                    show_apps: document.querySelector('[name="top_bar[show_apps]"]')?.checked ? 1 : 0,
                    show_seller_button: document.querySelector('[name="top_bar[show_seller_button]"]')?.checked ? 1 : 0,
                    language_mode: document.querySelector('[name="top_bar[language_mode]"]')?.value || 'code',
                    currency_mode: document.querySelector('[name="top_bar[currency_mode]"]')?.value || 'code',
                    support_label: document.querySelector('[name="top_bar[support_label]"]')?.value || '',
                    support_phone: document.querySelector('[name="top_bar[support_phone]"]')?.value || '',
                    support_email: document.querySelector('[name="top_bar[support_email]"]')?.value || '',
                    seller_text: document.querySelector('[name="top_bar[seller_text]"]')?.value || '',
                    seller_url: document.querySelector('[name="top_bar[seller_url]"]')?.value || '#',
                    custom_buttons: readButtonRows('topButtonsWrap')
                },
                main_bar: {
                    enabled: document.querySelector('[name="main_bar[enabled]"]')?.checked ? 1 : 0,
                    is_public: document.querySelector('[name="main_bar[is_public]"]')?.checked ? 1 : 0,
                    logo_max_height: parseInt(document.querySelector('[name="main_bar[logo_max_height]"]')?.value || '48', 10),
                    icon_size: parseInt(document.querySelector('[name="main_bar[icon_size]"]')?.value || '48', 10),
                    show_language_icon: document.querySelector('[name="main_bar[show_language_icon]"]')?.checked ? 1 : 0
                },
                menu_bar: {
                    enabled: document.querySelector('[name="menu_bar[enabled]"]')?.checked ? 1 : 0,
                    is_public: document.querySelector('[name="menu_bar[is_public]"]')?.checked ? 1 : 0,
                    show_sidebar_trigger: document.querySelector('[name="menu_bar[show_sidebar_trigger]"]')?.checked ? 1 : 0,
                    show_category_button: document.querySelector('[name="menu_bar[show_category_button]"]')?.checked ? 1 : 0,
                    category_button_label: document.querySelector('[name="menu_bar[category_button_label]"]')?.value || '',
                    show_seller_button: document.querySelector('[name="menu_bar[show_seller_button]"]')?.checked ? 1 : 0,
                    seller_text: document.querySelector('[name="menu_bar[seller_text]"]')?.value || '',
                    seller_url: document.querySelector('[name="menu_bar[seller_url]"]')?.value || '#',
                    category_items_text: document.querySelector('[name="menu_bar[category_items_text]"]')?.value || '',
                    nav_links: readButtonRows('menuNavButtonsWrap'),
                    custom_buttons: readButtonRows('menuButtonsWrap')
                }
            };
        }

        function setFieldValue(name, value) {
            var el = document.querySelector('[name="' + name + '"]');
            if (!el) return;
            if (el.type === 'checkbox') {
                el.checked = !!value;
                return;
            }
            el.value = value == null ? '' : value;
        }

        function setButtonRows(wrapId, kind, rows) {
            var wrap = document.getElementById(wrapId);
            if (!wrap) return;
            wrap.innerHTML = '';
            (Array.isArray(rows) ? rows : []).forEach(function (row) {
                addRow(kind);
                var last = wrap.lastElementChild;
                if (!last) return;
                var labelEl = last.querySelector('input[name*="[label]"]');
                var urlEl = last.querySelector('input[name*="[url]"]');
                var trackingEl = last.querySelector('input[name*="[tracking_key]"]');
                var typeEl = last.querySelector('select[name*="[type]"]');
                var styleEl = last.querySelector('select[name*="[dropdown_style]"]');
                var activeEl = last.querySelector('input[name*="[is_active]"]');
                var itemsEl = last.querySelector('textarea[name*="[items_text]"]');
                if (labelEl) labelEl.value = row.label || '';
                if (urlEl) urlEl.value = row.url || '#';
                if (trackingEl) trackingEl.value = row.tracking_key || '';
                if (typeEl) typeEl.value = row.type || 'link';
                if (styleEl) styleEl.value = row.dropdown_style || 'dropdown';
                if (activeEl) activeEl.checked = !!row.is_active;
                if (itemsEl) itemsEl.value = row.items_text || '';
                if (typeEl && itemsEl) {
                    itemsEl.closest('.header-btn-items-wrap').style.display = typeEl.value === 'dropdown' ? '' : 'none';
                }
            });
            reindexRows(wrap);
        }

        function applyImportedConfig(config) {
            if (!config || typeof config !== 'object') return;
            var a = config.appearance || {};
            var t = config.top_bar || {};
            var m = config.main_bar || {};
            var mb = config.menu_bar || {};

            setFieldValue('appearance[top_bg]', a.top_bg || '#0f172a');
            setFieldValue('appearance[main_bg]', a.main_bg || '#f8fafc');
            setFieldValue('appearance[menu_bg]', a.menu_bg || '#c7eafe');
            setFieldValue('appearance[top_height]', a.top_height ?? 38);
            setFieldValue('appearance[main_height]', a.main_height ?? 56);
            setFieldValue('appearance[menu_height]', a.menu_height ?? 38);
            setFieldValue('appearance[width_desktop]', a.width_desktop ?? 1920);
            setFieldValue('appearance[width_laptop]', a.width_laptop ?? 1600);
            setFieldValue('appearance[width_tablet]', a.width_tablet ?? 1200);
            setFieldValue('appearance[width_mobile]', a.width_mobile ?? 768);

            setFieldValue('top_bar[enabled]', t.enabled);
            setFieldValue('top_bar[show_language]', t.show_language);
            setFieldValue('top_bar[show_currency]', t.show_currency);
            setFieldValue('top_bar[show_apps]', t.show_apps);
            setFieldValue('top_bar[show_seller_button]', t.show_seller_button);
            setFieldValue('top_bar[language_mode]', t.language_mode || 'code');
            setFieldValue('top_bar[currency_mode]', t.currency_mode || 'code');
            setFieldValue('top_bar[support_label]', t.support_label || '');
            setFieldValue('top_bar[support_phone]', t.support_phone || '');
            setFieldValue('top_bar[support_email]', t.support_email || '');
            setFieldValue('top_bar[seller_text]', t.seller_text || '');
            setFieldValue('top_bar[seller_url]', t.seller_url || '#');

            setFieldValue('main_bar[enabled]', m.enabled);
            setFieldValue('main_bar[logo_max_height]', m.logo_max_height ?? 48);
            setFieldValue('main_bar[icon_size]', m.icon_size ?? 48);
            setFieldValue('main_bar[show_language_icon]', m.show_language_icon);

            setFieldValue('menu_bar[enabled]', mb.enabled);
            setFieldValue('menu_bar[show_sidebar_trigger]', mb.show_sidebar_trigger);
            setFieldValue('menu_bar[show_category_button]', mb.show_category_button);
            setFieldValue('menu_bar[category_button_label]', mb.category_button_label || '');
            setFieldValue('menu_bar[show_seller_button]', mb.show_seller_button);
            setFieldValue('menu_bar[seller_text]', mb.seller_text || '');
            setFieldValue('menu_bar[seller_url]', mb.seller_url || '#');
            setFieldValue('menu_bar[category_items_text]', mb.category_items_text || '');

            setButtonRows('topButtonsWrap', 'top_custom', t.custom_buttons || []);
            setButtonRows('menuNavButtonsWrap', 'menu_nav', mb.nav_links || []);
            setButtonRows('menuButtonsWrap', 'menu_custom', mb.custom_buttons || []);
            bindRowEvents(document);
            renderQuickPreview();
        }

        document.getElementById('headerExportJsonBtn')?.addEventListener('click', function () {
            var payload = collectHeaderConfig();
            var blob = new Blob([JSON.stringify(payload, null, 2)], { type: 'application/json;charset=utf-8' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'header-config-' + Date.now() + '.json';
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);
        });

        document.getElementById('headerImportJsonBtn')?.addEventListener('click', function () {
            document.getElementById('headerImportJsonFile')?.click();
        });

        document.getElementById('headerImportJsonFile')?.addEventListener('change', function (event) {
            var file = event.target && event.target.files ? event.target.files[0] : null;
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function () {
                try {
                    var json = JSON.parse(String(reader.result || '{}'));
                    applyImportedConfig(json);
                } catch (e) {
                    alert('Invalid JSON file. Please upload a valid header config file.');
                }
            };
            reader.readAsText(file);
            event.target.value = '';
        });

        window.loadTemplateLinks = function (btn) {
            var row = btn.closest('.header-btn-row');
            var textarea = row ? row.querySelector('textarea') : null;
            if (!textarea) return;
            var template = "/Alibaba|https://www.alibaba.com/\n/All Categories|/categories\n/Track Order|/track-order\n/Customer Support|/contact";
            textarea.value = template;
            textarea.focus();
            renderQuickPreview();
        };

        window.insertNestedLine = function (btn, prefix) {
            var row = btn.closest('.header-btn-row');
            var textarea = row ? row.querySelector('textarea') : null;
            if (!textarea) return;
            var val = textarea.value.trim();
            var newLine = (val ? "\n" : "") + prefix + "Item Name|#";
            textarea.value = val + newLine;
            textarea.focus();
            renderQuickPreview();
        };

        // Color Grading / Gradient and Reset Logic
        document.querySelectorAll('.color-bg-picker').forEach(function(picker) {
            picker.addEventListener('input', function() {
                var targetId = this.getAttribute('data-target');
                var textInput = document.getElementById(targetId);
                var picker1 = document.querySelector(`.color-bg-picker[data-target="${targetId}"][data-idx="1"]`);
                var picker2 = document.querySelector(`.color-bg-picker[data-target="${targetId}"][data-idx="2"]`);
                
                if (picker1 && picker2 && textInput) {
                    var val1 = picker1.value;
                    var val2 = picker2.value;
                    
                    if (val1 !== val2) {
                        textInput.value = `linear-gradient(90deg, ${val1}, ${val2})`;
                    } else {
                        textInput.value = val1;
                    }
                    renderQuickPreview();
                }
            });
        });

        document.querySelectorAll('.reset-bg-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var targetId = this.getAttribute('data-target');
                var defVal = this.getAttribute('data-default');
                var textInput = document.getElementById(targetId);
                var picker1 = document.querySelector(`.color-bg-picker[data-target="${targetId}"][data-idx="1"]`);
                var picker2 = document.querySelector(`.color-bg-picker[data-target="${targetId}"][data-idx="2"]`);
                
                if (textInput) textInput.value = defVal;
                if (picker1) picker1.value = defVal;
                if (picker2) picker2.value = defVal;
                renderQuickPreview();
            });
        });

        document.querySelectorAll('.color-hex-input').forEach(function(input) {
            input.addEventListener('input', function() {
                var targetId = this.id;
                var picker1 = document.querySelector(`.color-bg-picker[data-target="${targetId}"][data-idx="1"]`);
                var picker2 = document.querySelector(`.color-bg-picker[data-target="${targetId}"][data-idx="2"]`);
                var val = this.value.trim();
                
                var hexMatches = val.match(/#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})/g);
                if (hexMatches && hexMatches.length >= 2) {
                    if (picker1) picker1.value = hexMatches[0];
                    if (picker2) picker2.value = hexMatches[1];
                } else if (hexMatches && hexMatches.length === 1) {
                    if (picker1) picker1.value = hexMatches[0];
                    if (picker2) picker2.value = hexMatches[0];
                }
                renderQuickPreview();
            });
        });

        window.addHeaderButtonRow = addRow;
        window.duplicateHeaderButtonRow = duplicateRow;
        window.removeHeaderButtonRow = removeRow;
        bindRowEvents(document);
        renderQuickPreview();
    })();
</script>
@endpush

