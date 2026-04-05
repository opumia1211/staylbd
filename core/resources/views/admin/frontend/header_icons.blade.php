@extends('admin.layouts.app')

@section('panel')
@php
    $values = (array) ($content->data_values ?? []);
    $groupLabels = [
        'search' => __('Search bar'),
        'nav' => __('Header, mobile bottom bar & desktop toolbar'),
        'product' => __('Product grid & product detail'),
        'account' => __('Account section (side menu)'),
    ];
    $groupOrder = ['search', 'nav', 'product', 'account'];
@endphp

@php $storefrontMainLogo = getLogo('logo'); @endphp
<div class="row g-2 mb-2">
    <div class="col-12">
        <div class="card border-0 shadow-sm hi-brand-card">
            <div class="card-body py-2 px-3">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2 min-w-0">
                        <span class="text-uppercase text-muted fw-bold small flex-shrink-0">@lang('Site logo')</span>
                        @if($storefrontMainLogo)
                            <img src="{{ $storefrontMainLogo }}" alt="" class="hi-brand-thumb rounded border bg-white" width="120" height="36" decoding="async">
                        @else
                            <span class="text-muted small">@lang('Not set')</span>
                        @endif
                    </div>
                    <form action="{{ route('admin.setting.logo.icon.update') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-wrap align-items-center gap-2 ms-md-auto">
                        @csrf
                        <input type="file" name="logo" class="form-control form-control-sm hi-brand-file" accept=".svg,.png,.jpg,.jpeg,.webp,.gif">
                        <button type="submit" class="btn btn--primary btn-sm">@lang('Update logo')</button>
                        <a href="{{ route('admin.frontend.sections.icon') }}" class="btn btn-sm btn-outline--primary">@lang('Favicon & more')</a>
                    </form>
                </div>
                <p class="mb-0 mt-2 text-muted" style="font-size:11px;line-height:1.35;">{{ __('This file is the same asset used site-wide: header, footer, mobile menu, contact page, live chat header, cookie notice, auth modals, and SEO markup.') }}</p>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card border-0 shadow-sm hi-intro-card">
            <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="min-w-0">
                    <h6 class="mb-0">{{ __('Icons (single set)') }}</h6>
                    <p class="mb-0 text-muted small lh-sm mt-1" style="font-size:11px;">
                        {{ __('PNG/SVG/WebP · each tile is square (same width & height). Optional pack: folder New1/lucide-ecommerce/.') }}
                    </p>
                </div>
                <span class="badge badge--primary flex-shrink-0" style="font-size:10px;">{{ __('Unified') }}</span>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.frontend.sections.content.headericons') }}" method="POST" enctype="multipart/form-data" id="headerIconsForm">
    @csrf

    @foreach($groupOrder as $gi => $groupKey)
        <h6 class="text-uppercase text-muted fw-bold mb-2 {{ $gi > 0 ? 'mt-3 pt-2 border-top' : '' }}" style="font-size:10px;letter-spacing:.04em;">{{ $groupLabels[$groupKey] ?? $groupKey }}</h6>
        <div class="hi-icon-grid mb-1">
            @foreach($slots as $slotKey => $slot)
                @continue(($slot['group'] ?? 'nav') !== $groupKey)
                @php
                    $iconField = $slot['field'];
                    $imageField = $iconField . '_image';
                    $currentImage = trim((string) ($values[$imageField] ?? ''));
                    $currentIcon = trim((string) ($values[$iconField] ?? $slot['default']));
                    $inputId = 'hi_txt_' . $slotKey;
                    $fileId = 'hi_file_' . $slotKey;
                    $delId = 'hi_del_' . $slotKey;
                @endphp
                <div class="hi-tile">
                    <div class="card border-0 shadow-sm hi-slot-card">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between gap-1 hi-slot-head">
                                <span class="fw-semibold hi-slot-label" title="{{ $slot['label'] }}">{{ $slot['label'] }}</span>
                                @if($currentImage)
                                    <span class="badge badge--success hi-slot-badge">@lang('Img')</span>
                                @else
                                    <span class="badge badge--dark hi-slot-badge">@lang('SVG')</span>
                                @endif
                            </div>

                            <label for="{{ $fileId }}" class="hi-drop rounded-2 flex-grow-1" title="@lang('Click to upload image')">
                                <input type="file" id="{{ $fileId }}" name="{{ $imageField }}" class="d-none hi-file-input" accept=".svg,.png,.jpg,.jpeg,.webp" data-hi-file>
                                @if($currentImage)
                                    <img src="{{ header_icon_uploaded_asset_url($currentImage) }}" alt="" class="hi-drop__img" width="48" height="48" data-hi-preview decoding="async">
                                @else
                                    <span class="hi-drop__placeholder d-flex flex-column align-items-center justify-content-center text-center">
                                        @include($activeTemplate . 'partials.icon', ['name' => $currentIcon, 'class' => 'text-secondary hi-drop__svg-fallback'])
                                        <span class="hi-drop__cta">@lang('Upload')</span>
                                    </span>
                                @endif
                            </label>

                            <details class="hi-details mt-1 mb-0 flex-shrink-0">
                                <summary class="text-muted user-select-none hi-details-sum">@lang('SVG name')</summary>
                                <input type="text" id="{{ $inputId }}" class="form-control form-control-sm mt-1 py-0 hi-fallback-input" name="{{ $iconField }}" value="{{ $currentIcon }}" placeholder="{{ $slot['default'] }}" autocomplete="off">
                            </details>

                            @if($currentImage)
                                <div class="form-check mt-1 mb-0 hi-remove-wrap flex-shrink-0">
                                    <input class="form-check-input" type="checkbox" name="{{ $imageField }}_delete" value="1" id="{{ $delId }}">
                                    <label class="form-check-label text-danger hi-remove-lbl" for="{{ $delId }}">@lang('Remove')</label>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="hi-sticky-save border-top bg--body mt-2 py-2">
        <button type="submit" class="btn btn--primary btn-sm px-4">@lang('Save all icons')</button>
        <span class="text-muted ms-2 d-none d-md-inline hi-save-hint">{{ __('Save after choosing files.') }}</span>
    </div>
</form>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <strong>@lang('Custom header / footer buttons')</strong>
        <button type="button" class="btn btn--primary btn-sm addCustomBtn">@lang('+ Add')</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>@lang('Target')</th>
                    <th>@lang('Position')</th>
                    <th>@lang('Text')</th>
                    <th>@lang('Icon')</th>
                    <th>@lang('Order')</th>
                    <th width="120">@lang('Action')</th>
                </tr>
                </thead>
                <tbody>
                @forelse($customButtons as $btn)
                    @php $dv = (array)($btn->data_values ?? []); @endphp
                    <tr>
                        <td>{{ strtoupper($dv['target'] ?? '-') }}</td>
                        <td>{{ $dv['position'] ?? '-' }}</td>
                        <td>{{ $dv['button_text'] ?? '-' }}</td>
                        <td>
                            @if(!empty($dv['icon_image']))
                                <img src="{{ asset('assets/images/frontend/custom_buttons/' . $dv['icon_image']) }}" alt="" width="24" height="24" class="rounded border bg-white p-1">
                            @else
                                <code class="small">{{ $dv['icon_name'] ?? '-' }}</code>
                            @endif
                        </td>
                        <td>{{ (int)($dv['display_order'] ?? 0) }}</td>
                        <td>
                            <form action="{{ route('admin.frontend.sections.headericons.buttons.delete', $btn->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline--danger">@lang('Remove')</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">@lang('No custom button added yet')</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addCustomBtnModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="mb-0">@lang('Add custom button')</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('admin.frontend.sections.headericons.buttons.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body py-2">
                    <div class="form-group mb-2">
                        <label>@lang('Target')</label>
                        <select class="form-control form-control-sm" name="target" required>
                            <option value="header">Header</option>
                            <option value="footer">Footer</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>@lang('Position')</label>
                        <select class="form-control form-control-sm" name="position" required>
                            <option value="left">header: left</option>
                            <option value="nav">header: nav</option>
                            <option value="right">header: right</option>
                            <option value="top">footer: top</option>
                            <option value="bottom">footer: bottom</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label>@lang('Button text')</label>
                        <input type="text" class="form-control form-control-sm" name="button_text" placeholder="@lang('Button text')">
                    </div>
                    <div class="form-group mb-2">
                        <label>@lang('Button URL')</label>
                        <input type="text" class="form-control form-control-sm" name="button_url" placeholder="/products">
                    </div>
                    <div class="form-group mb-2">
                        <label>@lang('Icon name (fallback)')</label>
                        <input type="text" class="form-control form-control-sm" name="icon_name" placeholder="bell">
                    </div>
                    <div class="form-group mb-2">
                        <label>@lang('Icon image')</label>
                        <input type="file" class="form-control form-control-sm" name="icon_image" accept=".svg,.png,.jpg,.jpeg,.webp">
                    </div>
                    <div class="form-group mb-0">
                        <label>@lang('Display order')</label>
                        <input type="number" class="form-control form-control-sm" name="display_order" value="0" min="0" max="9999">
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="submit" class="btn btn--primary btn-sm">@lang('Add')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
.hi-brand-card, .hi-intro-card { border-radius: 10px; }
.hi-brand-thumb { max-width: 120px; max-height: 36px; width: auto; height: auto; object-fit: contain; }
.hi-brand-file { max-width: 200px; min-width: 140px; }
/* Square tiles: cell is 1:1; inner preview area stays square */
.hi-icon-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(108px, 1fr));
    gap: 0.5rem;
    align-items: stretch;
}
.hi-tile {
    aspect-ratio: 1 / 1;
    min-width: 0;
}
.hi-slot-card {
    border-radius: 8px;
    transition: box-shadow .15s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.hi-slot-card:hover { box-shadow: 0 .2rem .65rem rgba(15, 23, 42, .07) !important; }
.hi-slot-card .card-body {
    flex: 1 1 auto;
    min-height: 0;
    padding: 0.35rem !important;
}
.hi-slot-head { flex-shrink: 0; margin-bottom: 2px; }
.hi-slot-label { font-size: 9px; line-height: 1.2; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.hi-slot-badge { font-size: 7px !important; padding: 1px 4px !important; flex-shrink: 0; }
.hi-drop {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1 1 0;
    min-height: 0;
    width: 100%;
    aspect-ratio: 1 / 1;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px dashed rgba(15, 23, 42, 0.14);
    cursor: pointer;
    transition: border-color .2s ease, background .2s ease;
    margin-bottom: 0;
}
.hi-drop:hover {
    border-color: rgba(37, 99, 235, 0.45);
    background: #eff6ff;
}
.hi-drop__img {
    width: 48px;
    height: 48px;
    max-width: 72%;
    max-height: 72%;
    object-fit: contain;
    display: block;
}
.hi-drop__svg-fallback { width: 28px !important; height: 28px !important; max-width: 55% !important; max-height: 55% !important; opacity: .88; }
/* If admin loads jQuery UI elsewhere, kill sprite bleed on inline SVG fallbacks */
.hi-slot-card svg.ui-icon { background: none !important; background-image: none !important; }
.hi-drop__cta { color: #64748b; font-weight: 600; font-size: 9px; line-height: 1.1; margin-top: 2px; }
.hi-details-sum { font-size: 10px; outline: none; cursor: pointer; }
.hi-fallback-input { font-size: 11px !important; height: 26px !important; }
.hi-remove-wrap { line-height: 1.1; }
.hi-remove-lbl { font-size: 9px !important; }
.hi-save-hint { font-size: 11px; }
.hi-sticky-save {
    position: sticky;
    bottom: 0;
    z-index: 5;
    margin-left: -4px;
    margin-right: -4px;
    padding-left: 4px !important;
    padding-right: 4px !important;
}
</style>
@endpush

@push('script')
<script>
    (function () {
        'use strict';
        document.querySelectorAll('[data-hi-file]').forEach(function (input) {
            input.addEventListener('change', function () {
                var file = input.files && input.files[0];
                var label = input.closest('label.hi-drop');
                if (!file || !label) return;
                var reader = new FileReader();
                reader.onload = function (e) {
                    var url = e.target && e.target.result;
                    var existing = label.querySelector('[data-hi-preview]');
                    if (existing) {
                        existing.src = url;
                        return;
                    }
                    var ph = label.querySelector('.hi-drop__placeholder');
                    if (ph) ph.remove();
                    var img = document.createElement('img');
                    img.src = url;
                    img.alt = '';
                    img.width = 48;
                    img.height = 48;
                    img.className = 'hi-drop__img';
                    img.setAttribute('data-hi-preview', '');
                    img.decoding = 'async';
                    label.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
        var addBtn = document.querySelector('.addCustomBtn');
        if (addBtn && window.jQuery) {
            addBtn.addEventListener('click', function () {
                window.jQuery('#addCustomBtnModal').modal('show');
            });
        }
    })();
</script>
@endpush
