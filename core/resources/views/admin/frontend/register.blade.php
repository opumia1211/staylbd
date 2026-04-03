@extends('admin.layouts.app')
@section('panel')
{{-- Quick links: Registration control (this page) | User profile control --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="fw-semibold text-muted me-2">@lang('Control boards'):</span>
            <a href="{{ route('admin.frontend.sections.register') }}" class="btn btn--primary btn-sm py-1"><i class="las la-clipboard-list me-1"></i> @lang('Registration page control')</a>
            <a href="{{ route('admin.frontend.sections.userprofile') }}" class="btn btn-outline-warning btn-sm py-1"><i class="las la-user-edit me-1"></i> @lang('User profile control')</a>
        </div>
    </div>
</div>

{{-- 1. Compact sticky preview --}}
<div class="register-preview-wrapper mb-2">
    <div class="card border-0 shadow-sm register-preview-card register-compact">
        <div class="card-header bg--info text-white py-1 px-2 d-flex align-items-center justify-content-between">
            <span class="small fw-semibold"><i class="las la-eye me-1"></i> @lang('Live preview')</span>
            <span class="badge bg-white text--info small" id="previewCount">0</span>
        </div>
        <div class="card-body p-2 bg-light">
            <div class="register-preview-inner bg-white rounded border p-2 mx-auto register-preview-box" style="max-width: 300px;">
                <div class="register-preview-form">
                    @foreach(registrationFieldsListGrouped() as $group)
                        @foreach($group['fields'] as $fkey => $label)
                        <div class="preview-field mb-1 d-none" id="preview_{{ $fkey }}" data-field="{{ $fkey }}">
                            <label class="form-label mb-0 preview-label">{{ $label }}</label>
                            @if(in_array($fkey, ['country', 'gender', 'how_heard', 'preferred_language']))
                                <div class="preview-input">—</div>
                            @elseif($fkey == 'agree' || $fkey == 'newsletter_subscribe')
                                <div class="form-check form-check-sm mt-0"><input type="checkbox" class="form-check-input" disabled> <span class="preview-checkbox-text">☐</span></div>
                            @elseif($fkey == 'password')
                                <div class="preview-input">••••</div>
                                <div class="preview-input preview-input-sm mt-1">@lang('Confirm')</div>
                            @elseif($fkey == 'captcha')
                                <div class="preview-captcha">@lang('Captcha')</div>
                            @elseif($fkey == 'profile_photo')
                                <div class="preview-captcha">@lang('Upload')</div>
                            @else
                                <div class="preview-input">{{ $fkey == 'referBy' ? '...' : ' ' }}</div>
                            @endif
                        </div>
                        @endforeach
                    @endforeach
                </div>
                <div class="mt-1 pt-1 border-top">
                    <button type="button" class="btn btn-sm btn--primary py-0 w-100 preview-register-btn" disabled>@lang('Register')</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. Captcha on user pages: two clear switches --}}
@php
    $loginCaptchaEnabled = isset($content->data_values->login_captcha_enabled)
        ? (int) $content->data_values->login_captcha_enabled === 1
        : true;
    $registerCaptchaEnabled = isRegistrationFieldEnabled('captcha');
@endphp
<form action="{{ route('admin.frontend.sections.content.register') }}" method="POST" enctype="multipart/form-data" id="registerMasterForm">
    @csrf
    <input type="hidden" name="type" value="content">
    <input type="hidden" name="heading" value="{{ @$content->data_values->heading }}">
    <input type="hidden" name="subheading" value="{{ @$content->data_values->subheading }}">
    <div class="card border-0 shadow-sm mb-2 register-compact captcha-switches-card">
        <div class="card-header bg-warning text-dark py-2 px-3 d-flex align-items-center">
            <i class="las la-robot me-2 fs-5"></i>
            <span class="fw-semibold">@lang('Captcha on user pages')</span>
        </div>
        <div class="card-body p-3">
            <p class="small text-muted mb-3">@lang('Turn ON to show captcha on the floating login and registration panels on the user site.')</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center justify-content-between p-3 rounded border bg-light">
                        <div>
                            <span class="fw-semibold d-block">@lang('Floating Login')</span>
                            <small class="text-muted">@lang('Captcha on floating login & full page login')</small>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input type="hidden" name="login_captcha_enabled" value="0">
                            <input class="form-check-input" type="checkbox" name="login_captcha_enabled" value="1" id="login_captcha_enabled" {{ $loginCaptchaEnabled ? 'checked' : '' }} style="width: 2.5rem; height: 1.25rem;">
                            <label class="form-check-label ms-2" for="login_captcha_enabled">@lang($loginCaptchaEnabled ? 'ON' : 'OFF')</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center justify-content-between p-3 rounded border bg-light">
                        <div>
                            <span class="fw-semibold d-block">@lang('Floating Registration')</span>
                            <small class="text-muted">@lang('Captcha on floating registration & full page register')</small>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input type="hidden" name="registration_fields[captcha]" value="0">
                            <input class="form-check-input reg-field-cb" type="checkbox" name="registration_fields[captcha]" value="1" id="reg_captcha_switch" data-field="captcha" {{ $registerCaptchaEnabled ? 'checked' : '' }} style="width: 2.5rem; height: 1.25rem;">
                            <label class="form-check-label ms-2" for="reg_captcha_switch">@lang($registerCaptchaEnabled ? 'ON' : 'OFF')</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Registration fields: fixed grid (captcha excluded – controlled by switch above) --}}
    <div class="card border-0 shadow-sm mb-2 register-compact">
        <div class="card-header bg--success text-white py-1 px-2 d-flex align-items-center justify-content-between">
            <span class="small fw-semibold"><i class="las la-list-check me-1"></i> @lang('Registration form fields')</span>
        </div>
        <div class="card-body p-2">
            @foreach(registrationFieldsListGrouped() as $groupKey => $group)
            <div class="registration-field-group mb-2 pb-2 border-bottom border-light">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="small fw-semibold text-dark"><i class="{{ $group['icon'] }} me-1 text--primary"></i>{{ $group['title'] }}</span>
                    <button type="button" class="btn btn-sm btn-outline--primary py-0 px-1 reg-group-toggle">@lang('Toggle')</button>
                </div>
                <div class="reg-fields-grid">
                    @foreach($group['fields'] as $fkey => $label)
                    @if($fkey === 'captcha') @continue @endif
                    <label class="reg-field-item" for="reg_field_{{ $fkey }}">
                        <input type="hidden" name="registration_fields[{{ $fkey }}]" value="0">
                        <input type="checkbox" class="form-check-input reg-field-cb" name="registration_fields[{{ $fkey }}]" value="1" id="reg_field_{{ $fkey }}" {{ isRegistrationFieldEnabled($fkey) ? 'checked' : '' }} data-field="{{ $fkey }}">
                        <span class="reg-field-label">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
            <div class="pt-1">
                <button type="submit" class="btn btn--success btn-sm py-1 w-100"><i class="las la-save me-1"></i> @lang('Save settings')</button>
            </div>
        </div>
    </div>

    {{-- 3. Compact section content (collapsible) --}}
    <div class="card border-0 shadow-sm mb-2 register-compact">
        <div class="card-header bg--primary text-white py-1 px-2 d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#sectionContentCollapse" aria-expanded="false">
            <span class="small fw-semibold"><i class="las la-heading me-1"></i> @lang('Section content')</span>
            <i class="las la-chevron-down transition-transform collapse-icon" style="font-size: 0.8rem;"></i>
        </div>
        <div class="collapse" id="sectionContentCollapse">
            <div class="card-body p-2">
                @if(@$section->content)
                    @foreach($section->content as $k => $item)
                        @if($k == 'images')
                            @foreach($item as $imgKey => $image)
                                <input type="hidden" name="has_image" value="1">
                                <div class="mb-2 d-flex align-items-center gap-2 flex-wrap">
                                    <label class="form-label mb-0 small col-12 col-md-auto" style="min-width: 50px;">{{ __(keyToTitle($imgKey)) }}</label>
                                    <div class="rounded border overflow-hidden flex-shrink-0" style="width: 70px; height: 50px; background: url({{ getImage('assets/images/frontend/register/'. (@$content->data_values->$imgKey ?? ''), @$section->content->images->$imgKey->size) }}) center/contain no-repeat #f8f9fa;"></div>
                                    <input type="file" class="form-control form-control-sm flex-grow-1" style="max-width: 180px; font-size: 0.75rem;" name="image_input[{{ $imgKey }}]" accept=".png,.jpg,.jpeg">
                                </div>
                            @endforeach
                        @elseif($k != 'images')
                            <div class="mb-2">
                                <label class="form-label small mb-0">{{ __(keyToTitle($k)) }}</label>
                                @if($item == 'textarea')
                                    <textarea rows="1" class="form-control form-control-sm" name="{{ $k }}" style="font-size: 0.8rem;">{{ @$content->data_values->$k }}</textarea>
                                @else
                                    <input type="text" class="form-control form-control-sm" name="{{ $k }}" value="{{ @$content->data_values->$k }}" style="font-size: 0.8rem;">
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</form>
@endsection

@push('style')
<style>
.register-compact .card-header { min-height: auto; }
.register-compact .card-body { font-size: 0.8rem; }
/* Fixed grid: no overlap, no jump - same column count everywhere */
.reg-fields-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px 10px;
}
@media (max-width: 991px) {
    .reg-fields-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 576px) {
    .reg-fields-grid { grid-template-columns: repeat(2, 1fr); }
}
.reg-field-item {
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
    padding: 5px 8px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: #fff;
    cursor: pointer;
    min-height: 28px;
    transition: background .1s ease;
}
.reg-field-item:hover { background: #f8f9fa; }
.reg-field-item .form-check-input { flex-shrink: 0; width: 14px; height: 14px; margin: 0; }
.reg-field-item .reg-field-label {
    font-size: 0.7rem;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0;
}
.register-preview-wrapper { position: relative; z-index: 5; }
.register-preview-card { position: sticky; top: 70px; z-index: 10; }
@media (max-width: 991.98px) { .register-preview-card { position: relative; top: 0; } }
/* প্রিভিউতে লেখা স্পষ্ট দেখাতে - গাঢ় রঙ */
.register-preview-box .preview-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #1a1a1a !important;
    display: block;
    margin-bottom: 2px;
}
.register-preview-box .preview-input,
.register-preview-box .preview-input-sm {
    font-size: 0.75rem;
    min-height: 24px;
    padding: 2px 8px;
    background: #e9ecef !important;
    border: 1px solid #adb5bd !important;
    border-radius: 4px;
    color: #212529 !important;
    cursor: default;
    pointer-events: none;
}
.register-preview-box .preview-input-sm { min-height: 20px; font-size: 0.7rem; }
.register-preview-box .preview-captcha {
    font-size: 0.7rem;
    padding: 4px 8px;
    background: #e9ecef !important;
    border: 1px solid #adb5bd !important;
    border-radius: 4px;
    color: #212529 !important;
}
.register-preview-box .preview-checkbox-text { color: #212529 !important; font-size: 0.75rem; }
.register-preview-box .preview-register-btn { color: #fff !important; }
.card-header[data-bs-toggle="collapse"] { cursor: pointer; }
.card-header[data-bs-toggle="collapse"] .collapse-icon { transition: transform .2s; }
.card-header[data-bs-toggle="collapse"][aria-expanded="true"] .collapse-icon { transform: rotate(180deg); }
.card { overflow: visible; }
</style>
@endpush

@push('script')
<script>
(function () {
    'use strict';
    function updatePreview() {
        var count = 0;
        document.querySelectorAll('.reg-field-cb').forEach(function (cb) {
            var fid = cb.getAttribute('data-field');
            var el = document.getElementById('preview_' + fid);
            if (el) { el.classList.toggle('d-none', !cb.checked); if (cb.checked) count++; }
        });
        var badge = document.getElementById('previewCount');
        if (badge) badge.textContent = count + ' {{ __("fields") }}';
    }
    function updateSwitchLabels() {
        var loginSwitch = document.getElementById('login_captcha_enabled');
        var regSwitch = document.getElementById('reg_captcha_switch');
        if (loginSwitch && loginSwitch.nextElementSibling) loginSwitch.nextElementSibling.textContent = loginSwitch.checked ? '{{ __("ON") }}' : '{{ __("OFF") }}';
        if (regSwitch && regSwitch.nextElementSibling) regSwitch.nextElementSibling.textContent = regSwitch.checked ? '{{ __("ON") }}' : '{{ __("OFF") }}';
    }
    document.querySelectorAll('.reg-field-cb').forEach(function (cb) { cb.addEventListener('change', function() { updatePreview(); updateSwitchLabels(); }); });
    var loginCap = document.getElementById('login_captcha_enabled');
    if (loginCap) loginCap.addEventListener('change', updateSwitchLabels);
    document.querySelectorAll('.reg-group-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var group = this.closest('.registration-field-group');
            var checkboxes = group.querySelectorAll('.reg-field-cb');
            var allChecked = Array.prototype.every.call(checkboxes, function (c) { return c.checked; });
            checkboxes.forEach(function (c) { c.checked = !allChecked; });
            updatePreview();
        });
    });
    updatePreview();
    updateSwitchLabels();
})();
</script>
@endpush
