@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2 px-3">
                <div class="small text-muted">
                    @lang('Compact header icon manager: upload your own icon image or keep SVG icon name as fallback.')
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.frontend.sections.content.headericons') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-1">
        @php $values = (array) ($content->data_values ?? []); @endphp
        @foreach($slots as $slotKey => $slot)
            @php
                $iconField = $slot['field'];
                $imageField = $iconField . '_image';
                $currentImage = trim((string)($values[$imageField] ?? ''));
                $currentIcon = trim((string)($values[$iconField] ?? $slot['default']));
                $inputId = 'hi_' . $slotKey;
                $fileId = 'hi_file_' . $slotKey;
                $delId = 'hi_del_' . $slotKey;
            @endphp
            <div class="col-4 col-md-3 col-lg-2 col-xl-1">
                <div class="card border shadow-sm rounded-2 h-100">
                    <div class="card-body p-1 d-flex flex-column" style="padding:.28rem !important; gap:2px;">
                        <div class="d-flex align-items-center justify-content-between mb-0" style="line-height:1;">
                            <strong class="small text-truncate" style="font-size:10px;max-width:72px;" title="{{ $slot['label'] }}">{{ $slot['label'] }}</strong>
                            <span class="badge badge--dark">{{ strtoupper(substr($slotKey, 0, 1)) }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center border rounded bg-white" style="height:62px;min-height:62px;">
                            @if($currentImage)
                                <img src="{{ asset('assets/images/frontend/header_icons/' . $currentImage) }}" alt="{{ $slot['label'] }}" style="width:44px;height:44px;object-fit:contain;display:block;" data-preview-image>
                            @else
                                <span class="d-inline-flex align-items-center justify-content-center border rounded-circle bg-light" style="width:46px;height:46px;">
                                    @include($activeTemplate . 'partials.icon', ['name' => $currentIcon, 'class' => 'text-dark'])
                                </span>
                            @endif
                        </div>
                        <div class="small text-muted text-center" style="font-size:9px;line-height:1;">
                            @if($currentImage)
                                @lang('Uploaded')
                            @else
                                @lang('SVG Preview')
                            @endif
                        </div>
                        <div class="form-group mb-0">
                            <input type="text" id="{{ $inputId }}" class="form-control form-control-sm" style="height:24px;font-size:10px;padding:1px 5px;" name="{{ $iconField }}" value="{{ $currentIcon }}" placeholder="icon">
                        </div>
                        <div class="form-group mb-0">
                            <input type="file" id="{{ $fileId }}" class="form-control form-control-sm js-icon-file-input" style="height:24px;font-size:9px;padding:1px 3px;" name="{{ $imageField }}" accept=".svg,.png,.jpg,.jpeg,.webp">
                        </div>
                        <div class="small text-muted js-upload-help {{ $currentImage ? 'd-none' : '' }}" style="font-size:8px;line-height:1;">
                            <div>64x64 px</div>
                            <div>SVG/PNG/JPG/WEBP</div>
                        </div>
                        @if($currentImage)
                            <div class="form-check mb-0" style="line-height:1;">
                                <input class="form-check-input" type="checkbox" name="{{ $imageField }}_delete" value="1" id="{{ $delId }}">
                                <label class="form-check-label small text-danger" style="font-size:9px;" for="{{ $delId }}">@lang('Remove')</label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn--primary btn-sm px-4">@lang('Save Header Icons')</button>
    </div>
</form>

<div class="card border-0 shadow-sm mt-3">
    <div class="card-header d-flex align-items-center justify-content-between py-2">
        <strong>@lang('Custom Header/Footer Buttons')</strong>
        <button type="button" class="btn btn--primary btn-sm addCustomBtn">@lang('+ Add')</button>
    </div>
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                <tr>
                    <th>@lang('Target')</th>
                    <th>@lang('Position')</th>
                    <th>@lang('Text')</th>
                    <th>@lang('Icon')</th>
                    <th>@lang('Order')</th>
                    <th>@lang('Action')</th>
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
                                <img src="{{ asset('assets/images/frontend/custom_buttons/' . $dv['icon_image']) }}" alt="icon" width="20" height="20">
                            @else
                                {{ $dv['icon_name'] ?? '-' }}
                            @endif
                        </td>
                        <td>{{ (int)($dv['display_order'] ?? 0) }}</td>
                        <td>
                            <form action="{{ route('admin.frontend.sections.headericons.buttons.delete', $btn->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline--danger">@lang('Remove')</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">@lang('No custom button added yet')</td></tr>
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
                <h6 class="mb-0">@lang('Add Custom Button')</h6>
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
                        <label>@lang('Button Text')</label>
                        <input type="text" class="form-control form-control-sm" name="button_text" placeholder="Button text">
                    </div>
                    <div class="form-group mb-2">
                        <label>@lang('Button URL')</label>
                        <input type="text" class="form-control form-control-sm" name="button_url" placeholder="/products অথবা https://example.com">
                    </div>
                    <div class="form-group mb-2">
                        <label>@lang('Icon Name (fallback)')</label>
                        <input type="text" class="form-control form-control-sm" name="icon_name" placeholder="e.g. bell">
                    </div>
                    <div class="form-group mb-2">
                        <label>@lang('Icon Image')</label>
                        <input type="file" class="form-control form-control-sm" name="icon_image" accept=".svg,.png,.jpg,.jpeg,.webp">
                    </div>
                    <div class="form-group mb-0">
                        <label>@lang('Display Order')</label>
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

@push('script')
<script>
    (function () {
        "use strict";
        document.querySelectorAll('.js-icon-file-input').forEach(function (input) {
            input.addEventListener('change', function () {
                var card = input.closest('.card-body');
                if (!card) return;
                var help = card.querySelector('.js-upload-help');
                if (!help) return;
                help.classList.toggle('d-none', input.files && input.files.length > 0);
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

