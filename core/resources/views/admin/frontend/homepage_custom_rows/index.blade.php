@extends('admin.layouts.app')
@section('panel')
@php
    $createUrl = route('admin.frontend.sections.homepageCustomRows.create');
    $activeCustom = $rows->where('is_active', true)->count();
    $layoutCount = count($layoutSections);
@endphp
<form id="hpLayoutForm" method="POST" action="{{ route('admin.frontend.sections.homepageCustomRows.saveLayout') }}" class="d-none" aria-hidden="true">
    @csrf
    <input type="hidden" name="layout_json" id="hp_layout_json" value="">
</form>

<div class="hp-pro">
    {{-- Header: minimal, no heavy gradient --}}
    <div class="hp-pro__head card border shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="row g-3 align-items-center">
                <div class="col-lg">
                    <div class="d-flex align-items-start gap-3">
                        <div class="hp-pro__head-icon flex-shrink-0 rounded-3 d-flex align-items-center justify-content-center">
                            <i class="las la-home la-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <h1 class="hp-pro__head-title mb-1">@lang('Homepage layout')</h1>
                            <p class="hp-pro__head-lead mb-0">@lang('Order homepage blocks and optional product carousels.')</p>
                            <ul class="hp-pro__head-steps list-unstyled mb-0 mt-2">
                                <li><span class="hp-pro__step-n">1</span> @lang('Reorder · show/hide')</li>
                                <li><span class="hp-pro__step-n">2</span> @lang('Add rows (category or IDs)')</li>
                                <li><span class="hp-pro__step-n">3</span> @lang('Save to publish')</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-auto text-lg-end">
                    <div class="hp-pro__head-meta small text-muted mb-2">
                        <strong class="text-dark">{{ $layoutCount }}</strong> @lang('blocks')
                        <span class="text-muted mx-1">·</span>
                        <strong class="text-dark">{{ $activeCustom }}</strong> @lang('custom')
                    </div>
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        <button type="button" class="btn btn--primary btn-sm fw-semibold px-3" id="hpLayoutSaveBtn">
                            <i class="las la-save me-1"></i>@lang('Save layout')
                        </button>
                        <a href="{{ $createUrl }}" class="btn btn-outline-secondary btn-sm fw-semibold px-3">
                            <i class="las la-plus me-1"></i>@lang('Add row')
                        </a>
                        <a href="{{ route('admin.frontend.sections.homepageAds') }}" class="btn btn-outline-secondary btn-sm fw-semibold px-3">
                            <i class="las la-ad me-1"></i>@lang('Ads')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @error('layout_json')
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center gap-2 mb-3">
            <i class="las la-exclamation-circle la-lg"></i>
            <span>{{ $message }}</span>
        </div>
    @enderror

    @if(session('hp_highlight_row'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 py-2 px-3" id="hpFlashHighlight">
            <span class="small"><i class="las la-check-circle me-1"></i>@lang('Saved. Place in block list, then Save layout.')</span>
            <button type="button" class="btn btn-sm btn-success" id="hpScrollToNewRow" data-row-id="{{ (int) session('hp_highlight_row') }}">@lang('Blocks')</button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Section order (below custom lines on small screens so saved rows stay visible) --}}
        <div class="col-xl-5 order-2 order-xl-1">
            <div class="card border-0 shadow-sm h-100 hp-pro__card">
                <div class="card-header hp-pro__card-head border-0 py-3 px-3 d-flex align-items-center gap-2">
                    <span class="hp-pro__card-icon rounded-2 d-inline-flex align-items-center justify-content-center"><i class="las la-sort-amount-down"></i></span>
                    <div>
                        <h6 class="mb-0 fw-bold">@lang('Block order')</h6>
                        <span class="small text-muted">@lang('Store home · below banner')</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="px-3 py-2 border-bottom small text-muted hp-pro__hint-bar">
                        <i class="las la-info-circle me-1"></i>@lang('On = live · Drag ⋮⋮ · Trash = remove from home · Edit = label & scroll')
                    </div>
                    <div class="px-3 py-2 border-bottom small text-muted">
                        @lang('To show an ad between blocks: create an ad → drag the ad slot into position → Save layout.')
                        <a class="ms-1" href="{{ route('admin.frontend.sections.homepageAds') }}">@lang('Manage ads')</a>
                    </div>
                    <ul class="list-group list-group-flush hp-layout-sortable" id="hpLayoutSortable">
                        @foreach($layoutSections as $idx => $sec)
                            <li class="list-group-item hp-layout-item d-flex align-items-center gap-2 py-3 px-3
                                {{ \Illuminate\Support\Str::startsWith($sec['id'], 'custom_row_') && (int) session('hp_highlight_row') === (int) \Illuminate\Support\Str::after($sec['id'], 'custom_row_') ? 'hp-layout-item--pulse' : '' }}
                                {{ \Illuminate\Support\Str::startsWith($sec['id'], 'ad_slot_') && (int) session('hp_highlight_ad') === (int) \Illuminate\Support\Str::after($sec['id'], 'ad_slot_') ? 'hp-layout-item--pulse' : '' }}
                                "
                                data-id="{{ $sec['id'] }}"
                                @if(\Illuminate\Support\Str::startsWith($sec['id'], 'custom_row_')) data-hp-row-id="{{ (int) \Illuminate\Support\Str::after($sec['id'], 'custom_row_') }}" @endif
                                @if(\Illuminate\Support\Str::startsWith($sec['id'], 'ad_slot_')) data-hp-ad-id="{{ (int) \Illuminate\Support\Str::after($sec['id'], 'ad_slot_') }}" @endif
                            >
                                <div class="hp-layout-move d-flex flex-column flex-shrink-0" onclick="event.stopPropagation();">
                                    <button type="button" class="btn btn-light border py-0 px-1 hp-move-up lh-1 rounded-0 rounded-top" title="@lang('Move up')"><i class="las la-angle-up"></i></button>
                                    <button type="button" class="btn btn-light border border-top-0 py-0 px-1 hp-move-down lh-1 rounded-0 rounded-bottom" title="@lang('Move down')"><i class="las la-angle-down"></i></button>
                                </div>
                                <span class="hp-layout-drag text-muted flex-shrink-0" title="@lang('Drag')"><i class="las la-grip-vertical la-lg"></i></span>

                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                        <div class="min-w-0">
                                            <div class="fw-semibold small text-truncate">{{ \App\Services\HomepageLayoutService::displayLabel($sec['id'], $sec) }}</div>
                                            <div class="d-flex flex-wrap align-items-center gap-1 mt-1">
                                                <span class="hp-pro__pos badge rounded-pill">#<span class="hp-layout-pos">{{ $idx + 1 }}</span></span>
                                                @if(\Illuminate\Support\Str::startsWith($sec['id'], 'custom_row_'))
                                                    <span class="badge rounded-pill bg-light text-secondary border">@lang('Custom')</span>
                                                @elseif(\Illuminate\Support\Str::startsWith($sec['id'], 'ad_slot_'))
                                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">@lang('Ad')</span>
                                                @else
                                                    <span class="badge rounded-pill bg-light text-secondary border">@lang('Core')</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <button type="button" class="btn btn-sm btn-outline-secondary hp-layout-edit-btn" aria-expanded="false">
                                                <i class="las la-pen"></i> @lang('Edit')
                                            </button>
                                            @if(\Illuminate\Support\Str::startsWith($sec['id'], 'custom_row_'))
                                                @php $hpDelId = (int) \Illuminate\Support\Str::after($sec['id'], 'custom_row_'); @endphp
                                                <form method="post" action="{{ route('admin.frontend.sections.homepageCustomRows.destroy', $hpDelId) }}" class="d-inline mb-0 hp-layout-delete-form" onsubmit="return confirm('@lang('Delete this product line permanently?')');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline--danger py-1 px-2" title="@lang('Delete row')"><i class="las la-trash"></i></button>
                                                </form>
                                            @elseif(\Illuminate\Support\Str::startsWith($sec['id'], 'ad_slot_'))
                                                @php $hpAdDelId = (int) \Illuminate\Support\Str::after($sec['id'], 'ad_slot_'); @endphp
                                                <form method="post" action="{{ route('admin.frontend.sections.homepageAds.destroy', $hpAdDelId) }}" class="d-inline mb-0 hp-layout-delete-form" onsubmit="return confirm('@lang('Delete this ad permanently?')');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline--danger py-1 px-2" title="@lang('Delete ad')"><i class="las la-trash"></i></button>
                                                </form>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger py-1 px-2 hp-layout-soft-delete"
                                                    data-id="{{ $sec['id'] }}"
                                                    title="@lang('Remove from homepage')">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            @endif
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input hp-layout-enabled" type="checkbox" role="switch" id="en_{{ $idx }}" data-id="{{ $sec['id'] }}" {{ !empty($sec['enabled']) ? 'checked' : '' }}>
                                                <label class="visually-hidden" for="en_{{ $idx }}">@lang('Show on home')</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="hp-layout-settings-wrap mt-2" style="display:none;">
                                        <div class="hp-layout-settings d-flex flex-wrap align-items-end gap-2">
                                            <div style="min-width: 220px; flex: 1 1 220px;">
                                                <label class="form-label small mb-1 text-muted">@lang('Display name')</label>
                                                <input type="text" class="form-control form-control-sm hp-layout-label" value="{{ $sec['label'] ?? \App\Services\HomepageLayoutService::displayLabel($sec['id'], $sec) }}" placeholder="{{ \App\Services\HomepageLayoutService::displayLabel($sec['id'], []) }}">
                                            </div>
                                            <div style="width: 140px;">
                                                <label class="form-label small mb-1 text-muted">@lang('Scroll (sec)')</label>
                                                <input type="number" class="form-control form-control-sm hp-layout-interval" value="{{ $sec['interval_seconds'] ?? '' }}" min="2" max="30" placeholder="@lang('Default')">
                                            </div>
                                            <div style="width: 140px;">
                                                <label class="form-label small mb-1 text-muted">@lang('Speed (ms)')</label>
                                                <input type="number" class="form-control form-control-sm hp-layout-speed" value="{{ $sec['speed_ms'] ?? '' }}" min="300" max="2000" placeholder="@lang('Default')">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer bg-white border-top py-2 px-3">
                    <span class="small text-muted">@lang('Active rows appear here — drag to slot, then Save layout.')</span>
                </div>
            </div>
        </div>

    </div>
</div>

@push('style')
<style>
    .hp-pro__head { background: #fafbfc; border-color: #e5e7eb !important; }
    .hp-pro__head-icon {
        width: 44px; height: 44px;
        background: #eef2ff;
        color: var(--primary, #6366f1);
    }
    .hp-pro__head-title { font-size: 1.125rem; font-weight: 700; color: #111827; letter-spacing: -0.02em; }
    .hp-pro__head-lead { font-size: .8125rem; color: #6b7280; line-height: 1.45; }
    .hp-pro__head-steps {
        display: flex; flex-wrap: wrap; gap: .5rem 1.25rem;
        font-size: .75rem; color: #4b5563; line-height: 1.4;
    }
    .hp-pro__head-steps li { display: inline-flex; align-items: center; gap: .4rem; }
    .hp-pro__step-n {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 1.35rem; height: 1.35rem; padding: 0 .25rem;
        border-radius: 6px; background: #e5e7eb; color: #374151;
        font-size: .65rem; font-weight: 800;
    }
    .hp-pro__head-meta strong { font-weight: 700; }
    .hp-pro__hint-bar { background: #f9fafb; }
    .hp-pro__card { border-radius: 14px; overflow: hidden; }
    .hp-pro__card-head { background: #fff; border-bottom: 1px solid #f3f4f6 !important; }
    .hp-pro__card-icon {
        width: 38px; height: 38px;
        background: #f0f4ff;
        color: var(--primary, #6366f1);
        font-size: 1.1rem;
    }
    .hp-pro__pos { font-size: 0.65rem; font-weight: 700; background: #e2e8f0; color: #475569; padding: 0.2rem 0.5rem; }
    .hp-pro__empty-icon { width: 88px; height: 88px; background: #f1f5f9; }
    .hp-pro__table thead th { border-bottom: 2px solid #e2e8f0; font-weight: 600; font-size: 0.65rem; }
    .letter-spacing-half { letter-spacing: 0.04em; }
    .hp-layout-sortable .hp-layout-item { cursor: grab; transition: background .15s, box-shadow .15s; border-color: #eef0f3 !important; }
    .hp-layout-sortable .hp-layout-item:hover { background: #fafbfc; }
    .hp-layout-sortable .hp-layout-item.sortable-ghost { opacity: 0.45; background: #e0e7ff; }
    .hp-layout-sortable .hp-layout-item.sortable-chosen { cursor: grabbing; box-shadow: 0 6px 20px rgba(0,0,0,.08); }
    .hp-layout-item--pulse { animation: hpPulse 1.2s ease 2; box-shadow: inset 0 0 0 2px rgba(34,197,94,.5); }
    @keyframes hpPulse { 0%, 100% { background: #fff; } 50% { background: #ecfdf5; } }
    .hp-layout-drag { cursor: grab; }
    .hp-layout-move .btn { min-width: 30px; color: #64748b; line-height: 1.1; }
    .hp-layout-move .btn:hover { color: var(--primary, #6366f1); background: #f8fafc !important; }
    .hp-layout-settings .form-control-sm { border-radius: 10px; }
    .hp-layout-item { padding-top: .65rem !important; padding-bottom: .65rem !important; }
    .hp-layout-edit-btn { border-radius: 999px; padding: .2rem .55rem; font-weight: 600; }
    .hp-layout-item--removed { opacity: .55; background: #fff7ed; }
</style>
@endpush
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js" crossorigin="anonymous"></script>
<script>
(function() {
    var el = document.getElementById('hpLayoutSortable');
    function refreshPositions() {
        if (!el) return;
        el.querySelectorAll('.hp-layout-pos').forEach(function(span, i) { span.textContent = i + 1; });
    }
    if (el && typeof Sortable !== 'undefined') {
        Sortable.create(el, {
            animation: 150,
            draggable: '.hp-layout-item',
            handle: '.hp-layout-drag',
            filter: 'input, button, .hp-layout-move, .form-check, .hp-layout-delete-form',
            preventOnFilter: true,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: refreshPositions
        });
    }
    if (el) {
        el.addEventListener('click', function(e) {
            var t = e.target.closest('.hp-move-up, .hp-move-down');
            if (!t || !el.contains(t)) return;
            e.preventDefault();
            var li = t.closest('.hp-layout-item');
            if (!li || !li.parentNode) return;
            if (t.classList.contains('hp-move-up') && li.previousElementSibling) {
                li.parentNode.insertBefore(li, li.previousElementSibling);
            } else if (t.classList.contains('hp-move-down') && li.nextElementSibling) {
                li.parentNode.insertBefore(li.nextElementSibling, li);
            }
            refreshPositions();
        });
    }
    function collectSections() {
        var sections = [];
        document.querySelectorAll('#hpLayoutSortable .hp-layout-item').forEach(function(li) {
            var id = li.getAttribute('data-id');
            var cb = li.querySelector('.hp-layout-enabled');
            var label = li.querySelector('.hp-layout-label');
            var interval = li.querySelector('.hp-layout-interval');
            var speed = li.querySelector('.hp-layout-speed');
            sections.push({
                id: id,
                enabled: cb && cb.checked,
                label: label ? label.value : '',
                interval_seconds: interval ? interval.value : '',
                speed_ms: speed ? speed.value : ''
            });
        });
        return sections;
    }

    refreshPositions();
    var saveBtn = document.getElementById('hpLayoutSaveBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            var btn = this;
            var form = document.getElementById('hpLayoutForm');
            var inp = document.getElementById('hp_layout_json');
            if (!form || !inp) return;
            inp.value = JSON.stringify(collectSections());
            btn.disabled = true;
            btn.innerHTML = '<i class="las la-spinner la-spin me-1"></i>' + @json(__('Saving…'));
            form.submit();
        });
    }
    function scrollToLayoutRow(id) {
        var li = document.querySelector('[data-hp-row-id="' + id + '"]');
        if (li) li.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    var hpScroll = document.getElementById('hpScrollToNewRow');
    if (hpScroll) {
        hpScroll.addEventListener('click', function() {
            scrollToLayoutRow(this.getAttribute('data-row-id'));
            var al = document.getElementById('hpFlashHighlight');
            if (al) al.remove();
        });
    }
    @if(session('hp_highlight_row'))
    (function() {
        var id = {{ (int) session('hp_highlight_row') }};
        setTimeout(function() { scrollToLayoutRow(String(id)); }, 400);
    })();
    @endif

    // Compact inline editor toggle (no bootstrap dependency)
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.hp-layout-edit-btn');
        if (!btn) return;
        var item = btn.closest('.hp-layout-item');
        if (!item) return;
        var wrap = item.querySelector('.hp-layout-settings-wrap');
        if (!wrap) return;
        var open = wrap.style.display !== 'none';
        wrap.style.display = open ? 'none' : 'block';
        btn.setAttribute('aria-expanded', open ? 'false' : 'true');
        if (!open) {
            var first = wrap.querySelector('input');
            if (first) first.focus();
        }
    });

    // “Delete” for core sections = turn Off (hide from homepage)
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.hp-layout-soft-delete');
        if (!btn) return;
        var id = btn.getAttribute('data-id') || '';
        if (!id) return;
        if (!confirm(@json(__('Remove this block from homepage? (You can re-enable it later)')))) return;
        var li = document.querySelector('#hpLayoutSortable .hp-layout-item[data-id="' + id + '"]');
        if (!li) return;
        var cb = li.querySelector('.hp-layout-enabled');
        if (cb) cb.checked = false;
        li.classList.add('hp-layout-item--removed');
        var wrap = li.querySelector('.hp-layout-settings-wrap');
        if (wrap) wrap.style.display = 'none';
        var editBtn = li.querySelector('.hp-layout-edit-btn');
        if (editBtn) editBtn.setAttribute('aria-expanded', 'false');
    });
})();
</script>
@endpush
@endsection
