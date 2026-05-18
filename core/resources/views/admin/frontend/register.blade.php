@extends('admin.layouts.app')
@section('panel')
<div class="registration-control-wrapper animate__animated animate__fadeIn">
    {{-- Top Action Bar --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-7">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded bg-label-primary shadow-sm"><i class="las la-user-plus fs-3"></i></span>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">@lang('Registration Architecture')</h5>
                    <p class="text-muted small mb-0">@lang('Design and regulate your site\'s entry point and user onboarding flow.')</p>
                </div>
            </div>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex gap-2">
                <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                    <a href="{{ route('admin.frontend.sections.register') }}" class="btn btn-primary btn-sm px-3 active border-0"><i class="las la-cog me-1"></i> @lang('Registration')</a>
                    <a href="{{ route('admin.frontend.sections.userprofile') }}" class="btn btn-outline-primary btn-sm px-3 border-0 bg-white"><i class="las la-user-circle me-1"></i> @lang('Profile Edit')</a>
                </div>
                <button type="submit" form="registerMasterForm" class="btn btn-primary btn-sm px-4 shadow-md rounded-pill">
                    <i class="las la-save me-1"></i> @lang('Deploy Changes')
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Configuration Column --}}
        <div class="col-xl-8 col-lg-7">
            <form action="{{ route('admin.frontend.sections.content.register') }}" method="POST" enctype="multipart/form-data" id="registerMasterForm" class="register-master-form">
                @csrf
                <input type="hidden" name="type" value="content">
                
                {{-- 1. Security Matrix --}}
                <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                    <div class="card-header border-bottom py-3 bg-white">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="badge bg-label-warning p-2 me-3 rounded">
                                    <i class="las la-shield-alt fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">@lang('Security & Anti-Bot Matrix')</h6>
                                    <small class="text-muted">@lang('Multi-layered verification for authentication routes')</small>
                                </div>
                            </div>
                            <span class="badge bg-label-secondary rounded-pill">@lang('Auth Shield')</span>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-3">
                            @php
                                $loginCaptchaEnabled = isset($content->data_values->login_captcha_enabled) ? (int) $content->data_values->login_captcha_enabled === 1 : true;
                                $registerCaptchaEnabled = isRegistrationFieldEnabled('captcha');
                            @endphp
                            <div class="col-md-6">
                                <label class="security-toggle-option p-3 rounded border w-100 cursor-pointer transition-all h-100 {{ $loginCaptchaEnabled ? 'border-primary bg-label-primary' : 'bg-light' }}" for="login_captcha_enabled">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="las la-sign-in-alt fs-4 me-2 {{ $loginCaptchaEnabled ? 'text-primary' : 'text-muted' }}"></i>
                                            <span class="fw-bold">@lang('Login Captcha')</span>
                                        </div>
                                        <div class="form-check form-switch modern-switch">
                                            <input type="hidden" name="login_captcha_enabled" value="0">
                                            <input class="form-check-input security-toggle-cb" type="checkbox" name="login_captcha_enabled" value="1" id="login_captcha_enabled" {{ $loginCaptchaEnabled ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-0 lh-sm">@lang('Enforce verification on both floating and full-page login modules.')</p>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="security-toggle-option p-3 rounded border w-100 cursor-pointer transition-all h-100 {{ $registerCaptchaEnabled ? 'border-primary bg-label-primary' : 'bg-light' }}" for="reg_captcha_switch">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="las la-user-plus fs-4 me-2 {{ $registerCaptchaEnabled ? 'text-primary' : 'text-muted' }}"></i>
                                            <span class="fw-bold">@lang('Register Captcha')</span>
                                        </div>
                                        <div class="form-check form-switch modern-switch">
                                            <input type="hidden" name="registration_fields[captcha]" value="0">
                                            <input class="form-check-input reg-field-cb security-toggle-cb" type="checkbox" name="registration_fields[captcha]" value="1" id="reg_captcha_switch" data-field="captcha" {{ $registerCaptchaEnabled ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-0 lh-sm">@lang('Prevent automated bot account creation with math verification.')</p>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Data Elements --}}
                <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                    <div class="card-header border-bottom py-3 bg-white">
                        <div class="row align-items-center g-3">
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="badge bg-label-success p-2 me-3 rounded">
                                    <i class="las la-stream fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">@lang('Field Configuration Matrix')</h6>
                                    <small class="text-muted">@lang('Enable or disable data collection modules')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-merge search-box shadow-none border rounded-pill px-2">
                                    <span class="input-group-text border-0 bg-transparent"><i class="las la-search text-muted"></i></span>
                                    <input type="text" class="form-control border-0 bg-transparent ps-0" id="fieldSearch" placeholder="@lang('Filter modules...')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        @foreach(registrationFieldsListGrouped() as $groupKey => $group)
                        <div class="registration-field-group mb-5 animate__animated animate__fadeInUp" data-group="{{ $groupKey }}">
                            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded-circle bg-label-{{ $groupKey == 'basic' ? 'primary' : 'info' }}"><i class="{{ $group['icon'] }}"></i></span>
                                    </div>
                                    <span class="fw-bold text-dark text-uppercase tracking-wider small">{{ $group['title'] }}</span>
                                    <span class="badge bg-label-primary rounded-pill ms-2 group-count">0</span>
                                </div>
                                <div class="btn-group btn-group-sm rounded-pill overflow-hidden border">
                                    <button type="button" class="btn btn-light px-3 reg-group-action" data-action="all">@lang('All')</button>
                                    <button type="button" class="btn btn-light px-3 reg-group-action border-start" data-action="none">@lang('None')</button>
                                </div>
                            </div>
                            <div class="reg-fields-grid">
                                @foreach($group['fields'] as $fkey => $label)
                                @if($fkey === 'captcha') @continue @endif
                                <div class="field-card-item" data-label="{{ strtolower($label) }}" data-key="{{ strtolower($fkey) }}">
                                    <input type="hidden" name="registration_fields[{{ $fkey }}]" value="0">
                                    <input type="checkbox" class="d-none reg-field-cb" name="registration_fields[{{ $fkey }}]" value="1" id="reg_field_{{ $fkey }}" {{ isRegistrationFieldEnabled($fkey) ? 'checked' : '' }} data-field="{{ $fkey }}">
                                    <label class="field-card-label rounded border p-3 w-100 transition-all cursor-pointer h-100 d-flex flex-column" for="reg_field_{{ $fkey }}">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="field-icon-box rounded bg-light d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="las la-{{ $fkey == 'email' ? 'envelope' : ($fkey == 'mobile' ? 'mobile' : ($fkey == 'password' ? 'key' : 'id-card')) }} text-muted"></i>
                                            </div>
                                            <div class="status-indicator"></div>
                                        </div>
                                        <span class="fw-semibold text-dark mb-1 text-truncate">{{ $label }}</span>
                                        <span class="tiny text-muted text-uppercase tracking-tighter">{{ $fkey }}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Visual Identity --}}
                <div class="card border-0 shadow-sm mb-4 overflow-hidden border-start border-4 border-primary">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between cursor-pointer" data-bs-toggle="collapse" data-bs-target="#visualBrandingCollapse">
                        <div class="d-flex align-items-center">
                            <div class="badge bg-label-primary p-2 me-3 rounded">
                                <i class="las la-magic fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">@lang('Visual Identity & Onboarding Content')</h6>
                                <small class="text-muted">@lang('Customize imagery and instructional text')</small>
                            </div>
                        </div>
                        <i class="las la-angle-down transition-all collapse-chevron"></i>
                    </div>
                    <div class="collapse show" id="visualBrandingCollapse">
                        <div class="card-body border-top pt-4">
                            <div class="row g-4">
                                @if(@$section->content)
                                    @foreach($section->content as $k => $item)
                                        @if($k == 'images')
                                            @foreach($item as $imgKey => $image)
                                                <div class="col-12">
                                                    <div class="branding-image-card p-4 rounded border bg-light-soft">
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-md-3">
                                                                <div class="image-preview-box rounded shadow-sm border p-2 bg-white text-center">
                                                                    <img src="{{ getImage('assets/images/frontend/register/'. (@$content->data_values->$imgKey ?? ''), @$section->content->images->$imgKey->size) }}" class="img-fluid rounded preview-branding-img" style="max-height: 80px;" id="branding_img_target_{{ $imgKey }}">
                                                                    <div class="mt-2 tiny text-muted fw-bold">@lang('Current View')</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <label class="form-label fw-bold text-dark mb-2">{{ __(keyToTitle($imgKey)) }}</label>
                                                                <input type="hidden" name="has_image" value="1">
                                                                <input type="file" class="form-control branding-file-input" name="image_input[{{ $imgKey }}]" data-target="branding_img_target_{{ $imgKey }}" accept=".png,.jpg,.jpeg,.webp">
                                                                <div class="d-flex align-items-center mt-2">
                                                                    <span class="badge bg-label-secondary small me-2">{{ @$section->content->images->$imgKey->size }}px</span>
                                                                    <small class="text-muted"><i class="las la-info-circle me-1"></i> @lang('High-resolution recommended for retina displays.')</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @elseif($k != 'images')
                                            <div class="{{ $item == 'textarea' ? 'col-12' : 'col-md-6' }}">
                                                <div class="form-group mb-0">
                                                    <label class="form-label fw-bold text-dark mb-2">{{ __(keyToTitle($k)) }}</label>
                                                    @if($item == 'textarea')
                                                        <textarea rows="3" class="form-control branding-input" name="{{ $k }}" data-preview="{{ $k }}" placeholder="@lang('Enter ' . keyToTitle($k))">{{ @$content->data_values->$k }}</textarea>
                                                    @else
                                                        <input type="text" class="form-control branding-input" name="{{ $k }}" data-preview="{{ $k }}" value="{{ @$content->data_values->$k }}" placeholder="@lang('Enter ' . keyToTitle($k))">
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Interactive Preview --}}
        <div class="col-xl-4 col-lg-5">
            <div class="sticky-preview-wrapper">
                <div class="preview-header mb-3 d-flex align-items-center justify-content-between px-2">
                    <div class="d-flex align-items-center">
                        <i class="las la-eye text-primary fs-4 me-2"></i>
                        <h6 class="mb-0 fw-bold">@lang('Live Render')</h6>
                    </div>
                    <span class="badge bg-label-primary px-3 py-2 rounded-pill" id="activeFieldsBadge">0 @lang('Active Fields')</span>
                </div>
                
                {{-- High-Fidelity Phone Mockup --}}
                <div class="phone-shell shadow-2xl mx-auto">
                    <div class="phone-bezel">
                        <div class="phone-sensor-strip"></div>
                        <div class="phone-screen-container">
                            <div class="phone-header-bar d-flex justify-content-between align-items-center px-4 pt-3">
                                <span class="fw-bold small clock-real-time">9:41</span>
                                <div class="d-flex gap-1 align-items-center">
                                    <i class="las la-signal text-dark" style="font-size: 0.7rem;"></i>
                                    <i class="las la-wifi text-dark" style="font-size: 0.7rem;"></i>
                                    <div class="battery-icon"></div>
                                </div>
                            </div>
                            
                            <div class="phone-content-body custom-scrollbar">
                                <div class="app-onboarding-header text-center mb-4 pt-4 px-4">
                                    <div class="mockup-logo-area mx-auto mb-3">
                                        <img src="{{ getImage('assets/images/frontend/register/'. (@$content->data_values->image ?? ''), @$section->content->images->image->size) }}" class="mockup-img-sync shadow-sm rounded-3" id="mockup_logo_img">
                                    </div>
                                    <h5 class="fw-bold mb-1 mockup-text-sync text-dark" data-sync="heading">{{ @$content->data_values->heading ?? __('Join Us') }}</h5>
                                    <p class="small text-muted mockup-text-sync mb-0" data-sync="subheading">{{ @$content->data_values->subheading ?? __('Create your account today') }}</p>
                                </div>

                                <div class="mockup-form-area px-4">
                                    <div id="mockupFieldsList" class="mockup-field-stack">
                                        {{-- Dynamic --}}
                                    </div>

                                    <div class="mt-4 pt-2 mb-5">
                                        <button type="button" class="btn btn-primary w-100 rounded-3 shadow-md py-2 fw-bold text-uppercase ls-1" style="font-size: 0.8rem;">@lang('Create My Account')</button>
                                        <div class="text-center mt-3 tiny text-muted">
                                            @lang('By registering, you agree to our') <a href="javascript:void(0)" class="text-primary fw-bold text-decoration-none">@lang('Terms of Service')</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="phone-home-indicator"></div>
                    </div>
                </div>
                
                <div class="premium-note-card mt-4 p-4 rounded-4 border-0 shadow-sm bg-gradient-light position-relative overflow-hidden">
                    <div class="d-flex align-items-start position-relative z-index-1">
                        <div class="badge bg-primary p-2 rounded-circle me-3 shadow-sm">
                            <i class="las la-lightbulb fs-4 text-white"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">@lang('Tactical Insight')</h6>
                            <p class="mb-0 small text-muted lh-base">@lang('Minimize friction by requesting only essential data during sign-up. You can always collect additional profile details later in the user dashboard.')</p>
                        </div>
                    </div>
                    <div class="abstract-shape"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('style')
<style>
    :root {
        --premium-primary: #696cff;
        --premium-bg-light: #f8f9fa;
        --phone-bezel: #1e1e2d;
        --phone-frame: #343444;
    }

    .registration-control-wrapper { font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
    
    /* Sneat Badges & Avatars */
    .bg-label-primary { background-color: #e7e7ff !important; color: var(--premium-primary) !important; }
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
    .bg-label-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; }
    
    .avatar-md { width: 48px; height: 48px; }
    .avatar-xs { width: 26px; height: 26px; }
    .tracking-wider { letter-spacing: 1px; }
    .tracking-tighter { letter-spacing: -0.5px; }

    /* Security Options */
    .security-toggle-option { border-width: 2px !important; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .security-toggle-option:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); }
    
    /* Grid Layout */
    .reg-fields-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 1rem;
    }

    .field-card-label {
        border-width: 2px !important;
        background: #fff;
        transition: all 0.2s ease;
    }

    .field-card-label:hover { border-color: var(--premium-primary) !important; background-color: #fdfdff; }

    .reg-field-cb:checked + .field-card-label {
        border-color: var(--premium-primary) !important;
        background-color: #f8f8ff;
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.1);
    }

    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #dee2e6;
        transition: all 0.3s ease;
    }

    .reg-field-cb:checked + .field-card-label .status-indicator {
        background-color: var(--premium-primary);
        box-shadow: 0 0 8px var(--premium-primary);
    }

    .reg-field-cb:checked + .field-card-label .field-icon-box {
        background-color: var(--premium-primary) !important;
    }
    .reg-field-cb:checked + .field-card-label .field-icon-box i {
        color: #fff !important;
    }

    /* Search Box */
    .search-box { transition: all 0.3s; }
    .search-box:focus-within { border-color: var(--premium-primary) !important; box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.1) !important; }

    /* Interactive Preview System */
    .sticky-preview-wrapper { position: sticky; top: 120px; }
    
    .phone-shell {
        width: 300px;
        height: 620px;
        background: var(--phone-frame);
        padding: 12px;
        border-radius: 45px;
        position: relative;
        box-shadow: 0 50px 100px -20px rgba(50, 50, 93, 0.25), 0 30px 60px -30px rgba(0, 0, 0, 0.3);
    }

    .phone-bezel {
        width: 100%;
        height: 100%;
        background: #fff;
        border-radius: 35px;
        overflow: hidden;
        position: relative;
        border: 4px solid #000;
    }

    .phone-sensor-strip {
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 120px;
        height: 25px;
        background: #000;
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
        z-index: 100;
    }

    .phone-screen-container {
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .phone-content-body {
        flex: 1;
        overflow-y: auto;
        padding-bottom: 40px;
    }

    .mockup-logo-area {
        width: 60px;
        height: 60px;
        background: #f8f9fa;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .mockup-logo-area img { width: 100%; height: 100%; object-fit: contain; padding: 5px; }

    .battery-icon {
        width: 18px;
        height: 9px;
        border: 1px solid #333;
        border-radius: 2px;
        position: relative;
    }
    .battery-icon::after {
        content: '';
        position: absolute;
        right: -3px;
        top: 2px;
        width: 2px;
        height: 3px;
        background: #333;
    }

    .phone-home-indicator {
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: #000;
        border-radius: 2px;
        opacity: 0.2;
    }

    .mockup-field-item {
        margin-bottom: 12px;
        animation: slideInUp 0.3s ease;
    }

    .mockup-f-label { font-size: 0.65rem; color: #697a8d; font-weight: 600; margin-bottom: 3px; display: block; }
    .mockup-f-input {
        height: 36px;
        background: #fcfdfe;
        border: 1px solid #d9dee3;
        border-radius: 8px;
        width: 100%;
        padding: 0 12px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        color: #ccd1d6;
    }

    .ls-1 { letter-spacing: 1px; }

    /* Custom Scrollbar for Phone */
    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }

    /* Branding Card */
    .branding-image-card { transition: all 0.3s; }
    .branding-image-card:hover { border-color: var(--premium-primary) !important; background-color: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }

    .premium-note-card { background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); }
    .abstract-shape {
        position: absolute;
        bottom: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        background: var(--premium-primary);
        opacity: 0.05;
        border-radius: 50%;
    }

    @keyframes slideInUp {
        from { transform: translateY(10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Collapse Animation */
    .collapse-chevron { transition: transform 0.3s; }
    [aria-expanded="true"] .collapse-chevron { transform: rotate(180deg); }

    @media (max-width: 1199px) {
        .sticky-preview-wrapper { position: relative; top: 0; margin-top: 3rem; }
        .phone-shell { width: 100%; max-width: 300px; }
    }
</style>
@endpush

@push('script')
<script>
(function ($) {
    'use strict';

    // 1. Data Definitions
    const fieldLabels = {!! json_encode(registrationFieldsList()) !!};
    
    // 2. Real-time Clock for Mockup
    function updateClock() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        $('.clock-real-time').text(hours + ':' + minutes);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 3. Live Preview Engine
    function syncMockup() {
        const $list = $('#mockupFieldsList');
        $list.empty();
        
        let activeCount = 0;
        let groupCounts = {};

        $('.reg-field-cb').each(function () {
            const $cb = $(this);
            const isChecked = $cb.is(':checked');
            const fieldKey = $cb.data('field');
            const label = fieldLabels[fieldKey] || fieldKey;
            const groupKey = $cb.closest('.registration-field-group').data('group');

            if (!groupCounts[groupKey]) groupCounts[groupKey] = 0;

            // Highlight the card
            const $card = $cb.closest('.security-toggle-option, .field-card-item');
            if ($card.length) {
                if (isChecked) {
                    $card.addClass('border-primary bg-label-primary').removeClass('bg-light');
                } else {
                    $card.removeClass('border-primary bg-label-primary').addClass('bg-light');
                }
            }

            if (isChecked) {
                activeCount++;
                groupCounts[groupKey]++;
                
                // Add to Mockup Stack
                let html = `
                    <div class="mockup-field-item" id="mockup_f_${fieldKey}">
                        <span class="mockup-f-label">${label}</span>
                        <div class="mockup-f-input">
                            ${fieldKey === 'agree' ? '<i class="las la-check-square me-2 text-primary"></i> <span style="font-size:0.6rem">@lang("I agree to terms")</span>' : 
                              (fieldKey === 'captcha' ? '<div class="d-flex gap-2 w-100"><div class="bg-light rounded p-1 flex-grow-1 text-center small opacity-50" style="font-size:0.6rem">1 + 4 = ?</div><div class="mockup-f-input p-0" style="width:50px"></div></div>' : 
                               label + '...')
                            }
                        </div>
                    </div>
                `;
                $list.append(html);
            }
        });

        $('#activeFieldsBadge').text(activeCount + ' ' + (activeCount === 1 ? '@lang("Active Field")' : '@lang("Active Fields")'));
        
        // Update group badges
        Object.keys(groupCounts).forEach(key => {
            $(`.registration-field-group[data-group="${key}"] .group-count`).text(groupCounts[key]);
        });
    }

    // 4. Content Sync
    $('.branding-input').on('input', function() {
        const target = $(this).data('preview');
        $(`.mockup-text-sync[data-sync="${target}"]`).text($(this).val());
    });

    // 5. Image Sync
    $('.branding-file-input').on('change', function() {
        const input = this;
        const targetId = $(this).data('target');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $(`#${targetId}`).attr('src', e.target.result);
                // Also update mockup if it's the main logo
                if (targetId.includes('image')) {
                    $('#mockup_logo_img').attr('src', e.target.result);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    });

    // 6. Search Filter
    $('#fieldSearch').on('input', function() {
        const query = $(this).val().toLowerCase();
        $('.field-card-item').each(function() {
            const $item = $(this);
            const text = $item.data('label') + ' ' + $item.data('key');
            $item.toggle(text.includes(query));
        });

        $('.registration-field-group').each(function() {
            const visible = $(this).find('.field-card-item:visible').length;
            $(this).toggle(visible > 0);
        });
    });

    // 7. Batch Actions
    $('.reg-group-action').on('click', function() {
        const action = $(this).data('action');
        const $group = $(this).closest('.registration-field-group');
        $group.find('.reg-field-cb').prop('checked', action === 'all').trigger('change');
    });

    // Initialization
    $(document).on('change', '.reg-field-cb, .security-toggle-cb', syncMockup);
    syncMockup();

})(jQuery);
</script>
@endpush


