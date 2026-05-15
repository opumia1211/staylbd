@extends('admin.layouts.app')

@section('panel')
@push('style')
<style>
    .header-preview-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 10px;
    }
    .header-preview-stat {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 10px;
        background: #f8fafc;
    }
    .header-preview-stat .label {
        font-size: 11px;
        color: #64748b;
        display: block;
        line-height: 1.1;
    }
    .header-preview-stat .value {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }
    #headerDraftQuickPreview .preview-row {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 7px 9px;
        margin-bottom: 6px;
        background: #fff;
    }
</style>
@endpush
@php
    $d = $draftConfig;
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
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-1">@lang('3rd Header Professional Control')</h5>
                    <p class="text-muted mb-0">@lang('Control button order, hover panel style, links and visibility from one compact panel.')</p>
                </div>
                <span class="badge badge--primary">@lang('Route'): /sajaladminopu/frontend/header</span>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <form action="{{ route('admin.frontend.sections.header.saveDraft') }}" method="POST" class="card mb-3">
            @csrf
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>@lang('Draft Editor')</strong>
                <button type="submit" class="btn btn--primary btn-sm">@lang('Save Draft')</button>
            </div>
            <div class="card-body">
                <div class="alert alert-primary py-2 mb-3">
                    <strong>@lang('How to setup quickly:')</strong>
                    <div class="small mt-1">
                        1) @lang('Drag rows to set order') ·
                        2) @lang('Set type to Dropdown for hover panel') ·
                        3) @lang('Use nested syntax:') <code>/item|/url</code>, <code>//child|/url</code>, <code>///subchild|/url</code> ·
                        4) @lang('Choose panel style: Simple Dropdown / Mega Panel').
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-3 form-check mt-4"><input class="form-check-input" type="checkbox" name="menu_bar[enabled]" value="1" @checked(!empty($menuBar['enabled']))><label class="form-check-label">@lang('Enable 3rd bar')</label></div>
                    <div class="col-md-3 form-check mt-4"><input class="form-check-input" type="checkbox" name="menu_bar[show_sidebar_trigger]" value="1" @checked(!empty($menuBar['show_sidebar_trigger']))><label class="form-check-label">@lang('Sidebar trigger')</label></div>
                    <div class="col-md-3 form-check mt-4"><input class="form-check-input" type="checkbox" name="menu_bar[show_category_button]" value="1" @checked(!empty($menuBar['show_category_button']))><label class="form-check-label">@lang('Category button')</label></div>
                    <div class="col-md-3 form-check mt-4"><input class="form-check-input" type="checkbox" name="menu_bar[show_seller_button]" value="1" @checked(!empty($menuBar['show_seller_button']))><label class="form-check-label">@lang('Seller button')</label></div>

                    <div class="col-md-4"><label class="form-label">@lang('Category button text')</label><input class="form-control" name="menu_bar[category_button_label]" value="{{ $menuBar['category_button_label'] ?? 'ALL CATEGORIES' }}"></div>
                    <div class="col-md-4"><label class="form-label">@lang('Seller button text')</label><input class="form-control" name="menu_bar[seller_text]" value="{{ $menuBar['seller_text'] ?? 'BECOME A SELLER' }}"></div>
                    <div class="col-md-4"><label class="form-label">@lang('Seller button URL')</label><input class="form-control" name="menu_bar[seller_url]" value="{{ $menuBar['seller_url'] ?? '/seller/apply' }}"></div>

                    <div class="col-12">
                        <label class="form-label">@lang('Category dropdown items')</label>
                        <textarea class="form-control" rows="3" name="menu_bar[category_items_text]" placeholder="Label|URL (one per line)">@foreach((array)($menuBar['category_items'] ?? []) as $item){{ ($item['label'] ?? '') }}|{{ ($item['url'] ?? '#') }}
@endforeach</textarea>
                        <small class="text-muted">@lang('If empty, category list will be loaded from database automatically.')</small>
                    </div>
                </div>

                @php
                    $groups = [
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
                                <div class="header-btn-row card mb-2">
                                    <div class="card-body py-2">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-1 d-flex justify-content-center">
                                                <div class="d-flex flex-column gap-1 align-items-center">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1 row-move-up" title="@lang('Move up')">˄</button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1 row-move-down" title="@lang('Move down')">˅</button>
                                                </div>
                                            </div>
                                            <div class="col-md-2"><input class="form-control form-control-sm" name="{{ $g['prefix'] }}[{{ $idx }}][label]" value="{{ $btn['label'] ?? '' }}" placeholder="@lang('Button name')"></div>
                                            <div class="col-md-2"><input class="form-control form-control-sm" name="{{ $g['prefix'] }}[{{ $idx }}][url]" value="{{ $btn['url'] ?? '#' }}" placeholder="/url"></div>
                                            <div class="col-md-3">
                                                <select class="form-control form-control-sm header-btn-type" name="{{ $g['prefix'] }}[{{ $idx }}][type]">
                                                    <option value="link" @selected(($btn['type'] ?? 'link') === 'link')>@lang('Link')</option>
                                                    <option value="dropdown" @selected(($btn['type'] ?? '') === 'dropdown')>@lang('Dropdown')</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <select class="form-control form-control-sm header-panel-style" name="{{ $g['prefix'] }}[{{ $idx }}][dropdown_style]">
                                                    <option value="dropdown" @selected(($btn['dropdown_style'] ?? 'dropdown') === 'dropdown')>@lang('Simple panel')</option>
                                                    <option value="mega" @selected(($btn['dropdown_style'] ?? '') === 'mega')>@lang('Mega panel')</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1 text-center">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" name="{{ $g['prefix'] }}[{{ $idx }}][is_active]" value="1" @checked((int)($btn['is_active'] ?? 1) === 1)>
                                                    <label class="form-check-label small">@lang('Public')</label>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button type="button" class="btn btn-sm btn-outline--danger" onclick="window.removeHeaderButtonRow(this)">×</button>
                                            </div>
                                            <div class="col-12 header-btn-items-wrap" style="{{ (($btn['type'] ?? 'link') === 'dropdown') ? '' : 'display:none;' }}">
                                                <textarea class="form-control form-control-sm" rows="3" name="{{ $g['prefix'] }}[{{ $idx }}][items_text]" placeholder="/item|/url&#10;//child|/url">{{ implode("\n", $headerItemsToText((array) ($btn['items'] ?? []), 1)) }}</textarea>
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

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header"><strong>@lang('Professional Live Preview')</strong></div>
            <div class="card-body">
                <div id="headerDraftPreviewStats" class="header-preview-stats"></div>
                <div id="headerDraftQuickPreview" class="border rounded p-2 mb-3" style="max-height:220px; overflow:auto;"></div>
                <div class="d-flex justify-content-end mb-2"><button type="button" class="btn btn-sm btn-outline--primary" id="refreshHeaderDraftFrameBtn">@lang('Refresh Frame')</button></div>
                <div class="ratio ratio-16x9"><iframe id="headerDraftIframePreview" src="{{ route('admin.frontend.sections.header.preview') }}" loading="lazy" style="border:1px solid #e2e8f0; border-radius:8px;"></iframe></div>
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
            wrap.querySelectorAll('.header-btn-row').forEach(function (row, idx) {
                row.querySelectorAll('input, select, textarea').forEach(function (el) {
                    var n = el.getAttribute('name');
                    if (!n || !prefix) return;
                    el.setAttribute('name', n.replace(new RegExp(prefix.replace('[', '\\[').replace(']', '\\]') + '\\[[0-9]+\\]'), prefix + '[' + idx + ']'));
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
            var navWrap = document.getElementById('menuNavButtonsWrap');
            var customWrap = document.getElementById('menuButtonsWrap');
            if (delta < 0) {
                var prev = row.previousElementSibling;
                if (prev) {
                    wrap.insertBefore(row, prev);
                } else if (wrap === customWrap && navWrap && navWrap.querySelector('.header-btn-row')) {
                    navWrap.appendChild(row);
                    wrap = navWrap;
                }
            } else {
                var next = row.nextElementSibling;
                if (next) {
                    wrap.insertBefore(next, row);
                } else if (wrap === navWrap && customWrap) {
                    if (customWrap.firstElementChild) {
                        customWrap.insertBefore(row, customWrap.firstElementChild);
                    } else {
                        customWrap.appendChild(row);
                    }
                    wrap = customWrap;
                }
            }
            reindexRows(navWrap);
            reindexRows(customWrap);
            renderQuickPreview();
        }

        function addRow(kind) {
            var wrap = kind === 'menu_nav' ? document.getElementById('menuNavButtonsWrap') : document.getElementById('menuButtonsWrap');
            if (!wrap) return;
            var prefix = wrap.getAttribute('data-prefix');
            var idx = wrap.querySelectorAll('.header-btn-row').length;
            var html = ''
                + '<div class="header-btn-row card mb-2"><div class="card-body py-2"><div class="row g-2 align-items-center">'
                + '<div class="col-md-1 d-flex justify-content-center"><div class="d-flex flex-column gap-1 align-items-center"><button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1 row-move-up" title="Move up">˄</button><button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1 row-move-down" title="Move down">˅</button></div></div>'
                + '<div class="col-md-2"><input class="form-control form-control-sm" name="' + prefix + '[' + idx + '][label]" placeholder="Button name"></div>'
                + '<div class="col-md-2"><input class="form-control form-control-sm" name="' + prefix + '[' + idx + '][url]" value="#" placeholder="/url"></div>'
                + '<div class="col-md-3"><select class="form-control form-control-sm header-btn-type" name="' + prefix + '[' + idx + '][type]"><option value="link">Link</option><option value="dropdown">Dropdown</option></select></div>'
                + '<div class="col-md-2"><select class="form-control form-control-sm header-panel-style" name="' + prefix + '[' + idx + '][dropdown_style]"><option value="dropdown">Simple panel</option><option value="mega">Mega panel</option></select></div>'
                + '<div class="col-md-1 text-center"><div class="form-check mb-0"><input class="form-check-input" type="checkbox" name="' + prefix + '[' + idx + '][is_active]" value="1" checked><label class="form-check-label small">Public</label></div></div>'
                + '<div class="col-md-1 text-end"><button type="button" class="btn btn-sm btn-outline--danger" onclick="window.removeHeaderButtonRow(this)">×</button></div>'
                + '<div class="col-12 header-btn-items-wrap" style="display:none;"><textarea class="form-control form-control-sm" rows="3" name="' + prefix + '[' + idx + '][items_text]" placeholder="/item|/url&#10;//child|/url"></textarea></div>'
                + '</div></div></div>';
            wrap.insertAdjacentHTML('beforeend', html);
            bindRowEvents(wrap);
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
            var box = document.getElementById('headerDraftQuickPreview');
            var stats = document.getElementById('headerDraftPreviewStats');
            if (!box) return;
            var data = [];
            ['menuNavButtonsWrap', 'menuButtonsWrap'].forEach(function (id) {
                var wrap = document.getElementById(id);
                if (!wrap) return;
                wrap.querySelectorAll('.header-btn-row').forEach(function (row, rowIndex) {
                    var labelEl = row.querySelector('input[name*="[label]"]');
                    var activeEl = row.querySelector('input[name*="[is_active]"]');
                    var typeEl = row.querySelector('select[name*="[type]"]');
                    var styleEl = row.querySelector('select[name*="[dropdown_style]"]');
                    var label = labelEl ? (labelEl.value || '').trim() : '';
                    if (!label) return;
                    data.push({
                        label: label,
                        order: rowIndex + 1,
                        active: !activeEl || activeEl.checked,
                        type: typeEl ? typeEl.value : 'link',
                        panel: styleEl ? styleEl.value : 'dropdown'
                    });
                });
            });
            data.sort(function (a, b) { return a.order - b.order; });
            if (!data.length) {
                if (stats) {
                    stats.innerHTML = '<div class="header-preview-stat"><span class="label">Total</span><span class="value">0</span></div>'
                        + '<div class="header-preview-stat"><span class="label">Public</span><span class="value">0</span></div>'
                        + '<div class="header-preview-stat"><span class="label">Dropdown</span><span class="value">0</span></div>';
                }
                box.innerHTML = '<span class="text-muted">No buttons configured.</span>';
                return;
            }
            var publicCount = data.filter(function (d) { return d.active; }).length;
            var dropdownCount = data.filter(function (d) { return d.type === 'dropdown'; }).length;
            if (stats) {
                stats.innerHTML = '<div class="header-preview-stat"><span class="label">Total</span><span class="value">' + data.length + '</span></div>'
                    + '<div class="header-preview-stat"><span class="label">Public</span><span class="value">' + publicCount + '</span></div>'
                    + '<div class="header-preview-stat"><span class="label">Dropdown</span><span class="value">' + dropdownCount + '</span></div>';
            }
            box.innerHTML = data.map(function (item) {
                return '<div class="preview-row d-flex justify-content-between align-items-center">'
                    + '<span>#' + item.order + ' ' + item.label + ' <small class="text-muted">(' + item.type + (item.type === 'dropdown' ? ', ' + item.panel : '') + ')</small></span>'
                    + '<span class="badge ' + (item.active ? 'badge--success' : 'badge--danger') + '">' + (item.active ? 'Public' : 'Private') + '</span>'
                    + '</div>';
            }).join('');
        }

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

        window.addHeaderButtonRow = addRow;
        window.removeHeaderButtonRow = removeRow;
        bindRowEvents(document);
        renderQuickPreview();
    })();
</script>
@endpush

