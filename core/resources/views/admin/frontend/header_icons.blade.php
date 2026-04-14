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
                        {{ __('PNG/SVG/WebP · square tiles. If you remove an upload, the site falls back to bundled Lucide SVGs in public/assets/images/frontend/header_icons/bundled/ (ship-safe defaults).') }}
                    </p>
                </div>
                <span class="badge badge--primary flex-shrink-0" style="font-size:10px;">{{ __('Unified') }}</span>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="alert alert-primary mb-0 py-2 px-3">
            <strong>@lang('Category'):</strong> @lang('Frontend Manager') → @lang('Manage Section') → @lang('Header Icons Upload')
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

{{-- inline style moved to critical-admin.css --}}

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
