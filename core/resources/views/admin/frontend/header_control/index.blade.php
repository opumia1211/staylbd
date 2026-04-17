@extends('admin.layouts.app')

@section('panel')
@push('style')
<style>
    .header-preview-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 12px;
    }
    .header-preview-stat {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px;
        background: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        text-align: center;
    }
    .header-preview-stat .label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        display: block;
        margin-bottom: 2px;
    }
    .header-preview-stat .value {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
    }
    .header-preview-sticky {
        position: sticky;
        top: 80px;
        z-index: 100;
        transition: all 0.3s ease;
    }
    .header-preview-frame {
        height: 300px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
    }
    /* Visual Live Preview Mock */
    #headerVisualMock {
        background: #f1f5f9;
        border-radius: 8px;
        padding: 5px;
        border: 1px dashed #cbd5e1;
        margin-bottom: 15px;
        overflow: hidden;
    }
    .mock-bar {
        width: 100%;
        display: flex;
        align-items: center;
        padding: 0 10px;
        font-size: 10px;
        box-sizing: border-box;
        overflow: hidden;
        white-space: nowrap;
        margin-bottom: 2px;
        border-radius: 4px;
    }
    .mock-btn {
        padding: 2px 6px;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 4px;
        margin-right: 4px;
        max-width: 60px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .mock-btn.inactive { opacity: 0.4; text-decoration: line-through; }
    .nav-tabs-custom .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        padding: 8px 15px;
        font-weight: 600;
        color: #64748b;
    }
    .nav-tabs-custom .nav-link.active {
        color: #4e73df;
        border-bottom: 2px solid #4e73df;
        background: transparent;
    }
    .compact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
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
        <div class="card shadow-sm mb-3">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="m-0"><i class="las la-eye me-1"></i> @lang('Instant Live Preview')</h5>
                    <div id="headerDraftPreviewStats" class="d-flex gap-3 small fw-bold">
                        <span class="text-secondary">@lang('Total'): <span id="statTotal">0</span></span>
                        <span class="text-success">@lang('Public'): <span id="statPublic">0</span></span>
                        <span class="text-primary">@lang('Menus'): <span id="statDropdown">0</span></span>
                    </div>
                </div>
                <!-- Visual Mockup -->
                <div id="headerVisualMock" class="bg-light border rounded p-2 mb-0" style="min-height: 100px;">
                    <div id="mockTopBar" class="mock-bar"></div>
                    <div id="mockMainBar" class="mock-bar"></div>
                    <div id="mockMenuBar" class="mock-bar"></div>
                </div>
                <div class="text-muted small mt-2">
                    <i class="las la-info-circle"></i> @lang('This represents a live mockup of your header design.')
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
                                <label class="form-label small fw-bold">@lang('Top Bar Background')</label>
                                <div class="input-group">
                                    <input class="form-control form-control-color" type="color" name="appearance[top_bg]" value="{{ $appearance['top_bg'] ?? '#0f172a' }}">
                                    <input type="text" class="form-control" value="{{ $appearance['top_bg'] ?? '#0f172a' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">@lang('Main Bar Background')</label>
                                <div class="input-group">
                                    <input class="form-control form-control-color" type="color" name="appearance[main_bg]" value="{{ $appearance['main_bg'] ?? '#f8fafc' }}">
                                    <input type="text" class="form-control" value="{{ $appearance['main_bg'] ?? '#f8fafc' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">@lang('Menu Bar Background')</label>
                                <div class="input-group">
                                    <input class="form-control form-control-color" type="color" name="appearance[menu_bg]" value="{{ $appearance['menu_bg'] ?? '#c7eafe' }}">
                                    <input type="text" class="form-control" value="{{ $appearance['menu_bg'] ?? '#c7eafe' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">@lang('Bar Heights (px)')</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light p-1 px-2" title="Top">T</span>
                                    <input type="number" min="30" max="80" class="form-control" name="appearance[top_height]" value="{{ (int) ($appearance['top_height'] ?? 38) }}">
                                    <span class="input-group-text bg-light p-1 px-2" title="Main">M</span>
                                    <input type="number" min="40" max="100" class="form-control" name="appearance[main_height]" value="{{ (int) ($appearance['main_height'] ?? 56) }}">
                                    <span class="input-group-text bg-light p-1 px-2" title="Menu">B</span>
                                    <input type="number" min="30" max="80" class="form-control" name="appearance[menu_height]" value="{{ (int) ($appearance['menu_height'] ?? 38) }}">
                                </div>
                            </div>
                            <div class="col-12 mt-4 pt-2 border-top">
                                <h6 class="mb-2">@lang('Container Widths (Responsive Control)')</h6>
                                <div class="row g-2">
                                    <div class="col-md-3"><label class="small text-muted">@lang('Desktop')</label><input type="number" min="1280" max="1920" class="form-control form-control-sm" name="appearance[width_desktop]" value="{{ (int) ($appearance['width_desktop'] ?? 1920) }}"></div>
                                    <div class="col-md-3"><label class="small text-muted">@lang('Laptop')</label><input type="number" min="1024" max="1800" class="form-control form-control-sm" name="appearance[width_laptop]" value="{{ (int) ($appearance['width_laptop'] ?? 1600) }}"></div>
                                    <div class="col-md-3"><label class="small text-muted">@lang('Tablet')</label><input type="number" min="768" max="1400" class="form-control form-control-sm" name="appearance[width_tablet]" value="{{ (int) ($appearance['width_tablet'] ?? 1200) }}"></div>
                                    <div class="col-md-3"><label class="small text-muted">@lang('Mobile layout width (px)')</label><input type="number" min="320" max="900" class="form-control form-control-sm" name="appearance[width_mobile]" value="{{ (int) ($appearance['width_mobile'] ?? 100) }}"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Bar Tab -->
                    <div class="tab-pane fade" id="tab-topbar" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-12 d-flex flex-wrap gap-3 mb-3">
                                <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="top_bar[enabled]" value="1" @checked(!empty($topBar['enabled']))><label class="form-check-label fw-bold">@lang('Enable Top Bar')</label></div>
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
                            <div class="col-md-3"><div class="form-check form-switch mt-4"><input class="form-check-input" type="checkbox" name="main_bar[enabled]" value="1" @checked(!empty($mainBar['enabled']))><label class="form-check-label fw-bold">@lang('Enable Main Bar')</label></div><div class="form-check mt-3"><input class="form-check-input" type="checkbox" name="main_bar[show_language_icon]" value="1" @checked(!empty($mainBar['show_language_icon']))><label class="form-check-label">@lang('Show Lang Icon')</label></div></div>
                            <div class="col-md-4"><label class="form-label small fw-bold">@lang('Logo Max Height (px)')</label><input type="number" min="28" max="90" class="form-control" name="main_bar[logo_max_height]" value="{{ (int) ($mainBar['logo_max_height'] ?? 48) }}"><div class="form-text small">@lang('Ensures logo doesn\'t break layout')</div></div>
                            <div class="col-md-4"><label class="form-label small fw-bold">@lang('Icon Size (px)')</label><input type="number" min="28" max="72" class="form-control" name="main_bar[icon_size]" value="{{ (int) ($mainBar['icon_size'] ?? 48) }}"><div class="form-text small">@lang('Cart, Wishlist, User icons size')</div></div>
                        </div>
                    </div>

                    <!-- Menu Bar Tab -->
                    <div class="tab-pane fade" id="tab-menubar" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-12 d-flex flex-wrap gap-3 mb-3">
                                <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="menu_bar[enabled]" value="1" @checked(!empty($menuBar['enabled']))><label class="form-check-label fw-bold">@lang('Enable 3rd Bar')</label></div>
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
                                                    <span class="input-group-text bg--primary text-white">@lang('Link/URL')</span>
                                                    <input class="form-control url-field-highlight" name="{{ $g['prefix'] }}[{{ $idx }}][url]" value="{{ $btn['url'] ?? '#' }}" placeholder="https://...">
                                                </div>
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
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="las la-desktop me-2"></i>@lang('Full Preview')</h6>
                    <button type="button" class="btn btn-xs btn-outline-secondary" id="refreshHeaderDraftFrameBtn" title="@lang('Reload')"><i class="las la-sync-alt"></i></button>
                </div>
                <div class="card-body p-0">
                    <iframe class="w-100" id="headerDraftIframePreview" src="{{ route('admin.frontend.sections.header.preview') }}" loading="lazy" style="border:none; height: 350px;"></iframe>
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
                            </div>
                            <div class="col-12 header-btn-items-wrap mt-2" style="display:none;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="small fw-bold text--primary mb-0">@lang('Sub-menu Items (Visual Nested List)')</label>
                                    <div class="btn-group btn-group-sm"><button type="button" class="btn btn-xs btn-outline-secondary px-2" onclick="window.insertNestedLine(this, '/')" title="@lang('Add Level 1')">+ L1</button><button type="button" class="btn btn-xs btn-outline-secondary px-2" onclick="window.insertNestedLine(this, '//')" title="@lang('Add Level 2')">+ L2</button><button type="button" class="btn btn-xs btn-outline-secondary px-2" onclick="window.insertNestedLine(this, '///')" title="@lang('Add Level 3')">+ L3</button><button type="button" class="btn btn-xs btn-outline-info px-2" onclick="alert('@lang('Syntax: Name|/url\nUse / for depth: //Child|/url')')">?</button></div>
                                </div>
                                <textarea class="form-control form-control-sm" rows="4" name="${prefix}[${idx}][items_text]" placeholder="Name|/url&#10;/Nested Name|/url"></textarea>
                                <div class="form-text small">@lang('Use slashes (/, //, ///) to create up to 3 levels of nesting. Example: /Category|/url')</div>
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
            var mockTop = document.getElementById('mockTopBar');
            var mockMain = document.getElementById('mockMainBar');
            var mockMenu = document.getElementById('mockMenuBar');
            if (!mockTop || !mockMain || !mockMenu) return;

            // Appearance settings
            var topBg = document.querySelector('[name="appearance[top_bg]"]')?.value || '#0f172a';
            var mainBg = document.querySelector('[name="appearance[main_bg]"]')?.value || '#f8fafc';
            var menuBg = document.querySelector('[name="appearance[menu_bg]"]')?.value || '#c7eafe';
            var topH = Math.max(15, parseInt(document.querySelector('[name="appearance[top_height]"]')?.value || '38') / 2);
            var mainH = Math.max(25, parseInt(document.querySelector('[name="appearance[main_height]"]')?.value || '56') / 2);
            var menuH = Math.max(15, parseInt(document.querySelector('[name="appearance[menu_height]"]')?.value || '38') / 2);

            mockTop.style.cssText = 'height:' + topH + 'px; background:' + topBg + '; color: white; display:' + (document.querySelector('[name="top_bar[enabled]"]')?.checked ? 'flex' : 'none') + ';';
            mockMain.style.cssText = 'height:' + mainH + 'px; background:' + mainBg + '; color: #0f172a; display:' + (document.querySelector('[name="main_bar[enabled]"]')?.checked ? 'flex' : 'none') + ';';
            mockMenu.style.cssText = 'height:' + menuH + 'px; background:' + menuBg + '; color: #0f172a; display:' + (document.querySelector('[name="menu_bar[enabled]"]')?.checked ? 'flex' : 'none') + ';';

            // Stats
            var stats = { total: 0, public: 0, dropdown: 0 };
            mockTop.innerHTML = '<span class="me-2" style="opacity:0.5; text-transform:uppercase;">Top:</span>';
            mockMain.innerHTML = '<span class="me-2" style="font-weight:bold;">LOGO</span>';
            mockMenu.innerHTML = '';

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

                    var btnHtml = '<div class="mock-btn ' + (active ? '' : 'inactive') + '" title="' + label + '">' + label + (type === 'dropdown' ? ' ▾' : '') + '</div>';
                    if (id === 'topButtonsWrap') mockTop.insertAdjacentHTML('beforeend', btnHtml);
                    else mockMenu.insertAdjacentHTML('beforeend', btnHtml);
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

        document.getElementById('refreshHeaderDraftFrameBtn')?.addEventListener('click', function () {
            var iframe = document.getElementById('headerDraftIframePreview');
            if (!iframe) return;
            iframe.src = '{{ route('admin.frontend.sections.header.preview') }}?t=' + Date.now();
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
                    logo_max_height: parseInt(document.querySelector('[name="main_bar[logo_max_height]"]')?.value || '48', 10),
                    icon_size: parseInt(document.querySelector('[name="main_bar[icon_size]"]')?.value || '48', 10),
                    show_language_icon: document.querySelector('[name="main_bar[show_language_icon]"]')?.checked ? 1 : 0
                },
                menu_bar: {
                    enabled: document.querySelector('[name="menu_bar[enabled]"]')?.checked ? 1 : 0,
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

        window.addHeaderButtonRow = addRow;
        window.duplicateHeaderButtonRow = duplicateRow;
        window.removeHeaderButtonRow = removeRow;
        bindRowEvents(document);
        renderQuickPreview();
    })();
</script>
@endpush

