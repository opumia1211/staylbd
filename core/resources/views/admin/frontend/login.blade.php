@extends('admin.layouts.app')
@section('panel')
@php
    $captchaEnabled = $content && isset($content->data_values->captcha_enabled)
        ? (int) $content->data_values->captcha_enabled === 1
        : true;
    $regList = registrationFieldsList();
    $credentialKeys = loginCredentialCapableKeys();
    $socialProviders = [
        'google'   => ['label' => 'Google', 'icon' => 'lab la-google', 'color' => '#db4437'],
        'facebook' => ['label' => 'Facebook', 'icon' => 'lab la-facebook-f', 'color' => '#4267b2'],
        'twitter'  => ['label' => 'Twitter (X)', 'icon' => 'lab la-twitter', 'color' => '#1da1f2'],
        'apple'    => ['label' => 'Apple', 'icon' => 'lab la-apple', 'color' => '#000000'],
        'github'   => ['label' => 'GitHub', 'icon' => 'lab la-github', 'color' => '#333333'],
    ];
    $socialConfig = getSocialLoginButtonsConfig();
@endphp

<div class="login-architecture-wrapper animate__animated animate__fadeIn">
    {{-- Top Action Bar --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded bg-label-primary shadow-sm"><i class="las la-shield-alt fs-3"></i></span>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">@lang('Login Architecture')</h5>
                    <p class="text-muted small mb-0">@lang('Configure the authentication surface and security protocols for user entry.')</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <button type="submit" form="loginContentForm" class="btn btn-primary btn-sm px-4 shadow-md rounded-pill">
                <i class="las la-save me-1"></i> @lang('Deploy entry point')
            </button>
        </div>
    </div>

    <form action="{{ route('admin.frontend.sections.content.login') }}" method="POST" enctype="multipart/form-data" id="loginContentForm">
        @csrf
        <input type="hidden" name="type" value="content">
        @if($content && isset($content->id))
            <input type="hidden" name="id" value="{{ $content->id }}">
        @endif

        <div class="row g-4">
            {{-- Configuration Column --}}
            <div class="col-xl-8 col-lg-7">
                
                {{-- 1. Identity & Branding --}}
                <div class="card border-0 shadow-sm mb-4 overflow-hidden border-start border-4 border-primary">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="badge bg-label-primary p-2 me-2 rounded-3">
                                <i class="las la-id-card fs-4"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">@lang('Identity & Branding')</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">@lang('Main Heading')</label>
                                <input type="text" class="form-control rounded-3 sync-input" name="heading" data-mockup="#mockupHeading" value="{{ old('heading', @$content->data_values->heading) }}" placeholder="@lang('Welcome back!')">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">@lang('Subheading')</label>
                                <input type="text" class="form-control rounded-3 sync-input" name="subheading" data-mockup="#mockupSubheading" value="{{ old('subheading', @$content->data_values->subheading) }}" placeholder="@lang('Enter your credentials to access account')">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Credential Nodes --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="badge bg-label-success p-2 me-2 rounded-3">
                                    <i class="las la-key fs-4"></i>
                                </div>
                                <h6 class="mb-0 fw-bold">@lang('Credential Access Points')</h6>
                            </div>
                            <span class="badge bg-label-secondary small rounded-pill">@lang('Auth Nodes')</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-4">@lang('Select which identity markers users can use for authentication. Only fields enabled in') <a href="{{ route('admin.frontend.sections.register') }}" class="fw-bold text-primary">@lang('Registration Architecture')</a> @lang('are available.')</p>
                        <div class="row g-3">
                            @foreach($credentialKeys as $fkey)
                            @php
                                $label = $regList[$fkey] ?? $fkey;
                                $regEnabled = isRegistrationFieldEnabled($fkey);
                                $loginChecked = isLoginFieldEnabledForDisplay($fkey);
                            @endphp
                            <div class="col-md-6 col-xl-4">
                                <div class="auth-node-card p-3 rounded-4 border transition-all {{ $regEnabled ? 'bg-light-soft border-primary border-opacity-10' : 'bg-gray-100 opacity-75' }}">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center min-w-0">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-white text-primary shadow-sm"><i class="las la-{{ $fkey === 'email' ? 'envelope' : ($fkey === 'username' ? 'user-tag' : 'phone') }}"></i></span>
                                            </div>
                                            <div class="min-w-0">
                                                <h6 class="mb-0 tiny fw-bold text-dark text-truncate">{{ $label }}</h6>
                                                @if(!$regEnabled)
                                                    <span class="badge bg-label-danger tiny px-2" style="font-size: 0.6rem;">@lang('LOCKED')</span>
                                                @else
                                                    <span class="badge bg-label-success tiny px-2" style="font-size: 0.6rem;">@lang('READY')</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-check form-switch modern-switch ms-2">
                                            <input type="hidden" name="login_fields[{{ $fkey }}]" value="0">
                                            <input class="form-check-input login-field-cb" type="checkbox" name="login_fields[{{ $fkey }}]" value="1" id="login_field_{{ $fkey }}" {{ $loginChecked ? 'checked' : '' }} {{ !$regEnabled ? 'disabled' : '' }} data-label="{{ $label }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- 3. Social Integration & Security --}}
                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-label-info p-2 me-2 rounded-3">
                                        <i class="las la-share-alt fs-4"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold">@lang('Social login architecture')</h6>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    @foreach($socialProviders as $provider => $info)
                                    @php
                                        $envEnabled = env(strtoupper($provider) . '_LOGIN_ENABLED') == '1';
                                        $showOnLogin = isset($socialConfig[$provider]) && ((int)$socialConfig[$provider] === 1 || $socialConfig[$provider] === '1');
                                    @endphp
                                    <div class="list-group-item px-0 py-3 border-bottom d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="social-icon-box p-2 rounded-3 me-3" style="background: {{ $info['color'] }}15; color: {{ $info['color'] }};">
                                                <i class="{{ $info['icon'] }} fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 small fw-bold">{{ $info['label'] }}</h6>
                                                <small class="text-muted tiny">
                                                    @if($envEnabled)
                                                        <span class="text-success fw-bold"><i class="las la-check-circle"></i> @lang('Configured')</span>
                                                    @else
                                                        <a href="{{ route('admin.setting.social.login') }}" class="text-danger text-decoration-none">@lang('Click to configure')</a>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch modern-switch">
                                            <input type="hidden" name="social_login_buttons[{{ $provider }}]" value="0">
                                            <input class="form-check-input social-login-cb" type="checkbox" name="social_login_buttons[{{ $provider }}]" value="1" id="social_btn_{{ $provider }}" {{ $showOnLogin ? 'checked' : '' }} {{ !$envEnabled ? 'disabled' : '' }} data-icon="{{ $info['icon'] }}" data-color="{{ $info['color'] }}">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card border-0 shadow-sm h-100 bg-label-secondary">
                            <div class="card-header bg-transparent border-bottom border-secondary border-opacity-10 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-white p-2 me-2 rounded-3 shadow-sm">
                                        <i class="las la-user-shield fs-4 text-dark"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold text-dark">@lang('Security protocol')</h6>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center">
                                <div class="p-3 bg-white rounded-4 shadow-sm text-center mb-3">
                                    <i class="las la-fingerprint fs-1 text-primary mb-2"></i>
                                    <h6 class="small fw-bold text-dark">@lang('Anti-Bot Matrix')</h6>
                                    <div class="form-check form-switch modern-switch d-inline-block mt-2">
                                        <input type="hidden" name="captcha_enabled" value="0">
                                        <input class="form-check-input" type="checkbox" name="captcha_enabled" value="1" id="login_captcha_enabled" {{ $captchaEnabled ? 'checked' : '' }}>
                                    </div>
                                    <p class="tiny text-muted mb-0 mt-1">@lang('Require verification for all sign-in attempts')</p>
                                </div>
                                <div class="alert alert-warning border-0 p-3 rounded-4 mb-0">
                                    <div class="d-flex align-items-start">
                                        <i class="las la-exclamation-triangle fs-4 me-2 mt-1"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1 tiny fw-bold">@lang('Critical Note')</h6>
                                            <p class="mb-0 tiny lh-base">@lang('At least one authentication node must remain active to prevent portal lockout.')</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Interactive Preview Column --}}
            <div class="col-xl-4 col-lg-5">
                <div class="sticky-preview-wrapper">
                    <div class="preview-header mb-3 d-flex align-items-center justify-content-between px-2">
                        <div class="d-flex align-items-center">
                            <i class="las la-eye text-primary fs-4 me-2"></i>
                            <h6 class="mb-0 fw-bold">@lang('Interface Render')</h6>
                        </div>
                        <span class="badge bg-label-primary px-3 py-2 rounded-pill">@lang('Live Sync')</span>
                    </div>

                    {{-- Phone Shell --}}
                    <div class="phone-shell shadow-2xl mx-auto">
                        <div class="phone-bezel">
                            <div class="phone-sensor-strip"></div>
                            <div class="phone-screen-container">
                                <div class="phone-header-bar d-flex justify-content-between align-items-center px-4 pt-3">
                                    <span class="fw-bold small clock-real-time">12:30</span>
                                    <div class="d-flex gap-1 align-items-center">
                                        <i class="las la-signal text-dark" style="font-size: 0.7rem;"></i>
                                        <i class="las la-wifi text-dark" style="font-size: 0.7rem;"></i>
                                        <div class="battery-icon border-dark"></div>
                                    </div>
                                </div>
                                
                                <div class="phone-content-body p-4 pt-5 custom-scrollbar">
                                    <div class="text-center mb-5 animate__animated animate__fadeInDown">
                                        <div class="mockup-logo-placeholder mx-auto mb-3 bg-label-primary rounded-pill d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                                            <i class="las la-shopping-bag fs-2"></i>
                                        </div>
                                        <h4 class="fw-bold text-dark mb-1" id="mockupHeading">@lang('Login')</h4>
                                        <p class="small text-muted mb-0" id="mockupSubheading">@lang('Enter credentials to access')</p>
                                    </div>

                                    <div class="mockup-form-group mb-3">
                                        <label class="mockup-n-label" id="mockupCredentialLabel">@lang('Credential Marker')</label>
                                        <div class="mockup-n-value"><i class="las la-user-circle me-2 opacity-50"></i> @lang('Enter your identity...')</div>
                                    </div>

                                    <div class="mockup-form-group mb-4">
                                        <label class="mockup-n-label">@lang('Security Key')</label>
                                        <div class="mockup-n-value d-flex justify-content-between align-items-center">
                                            <span>••••••••••••</span>
                                            <i class="las la-eye-slash opacity-50"></i>
                                        </div>
                                        <div class="text-end mt-1">
                                            <a href="javascript:void(0)" class="tiny text-primary text-decoration-none fw-bold">@lang('Forgot Key?')</a>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-primary w-100 rounded-pill py-2 shadow-md mb-4 fw-bold" style="font-size: 0.8rem;">@lang('Secure Entry')</button>

                                    <div id="mockupSocialSection" class="text-center animate__animated animate__fadeIn">
                                        <div class="d-flex align-items-center gap-2 mb-4">
                                            <div class="flex-grow-1 border-top opacity-10"></div>
                                            <span class="tiny text-muted fw-bold">@lang('OR ACCESS VIA')</span>
                                            <div class="flex-grow-1 border-top opacity-10"></div>
                                        </div>
                                        <div class="d-flex justify-content-center gap-3 mb-4" id="mockupSocialStack">
                                            {{-- Social buttons go here --}}
                                        </div>
                                    </div>

                                    <div class="text-center mt-auto pb-4">
                                        <p class="tiny text-muted">@lang('New around here?') <a href="javascript:void(0)" class="text-primary fw-bold text-decoration-none">@lang('Create Account')</a></p>
                                    </div>
                                </div>
                            </div>
                            <div class="phone-home-indicator"></div>
                        </div>
                    </div>

                    <div class="p-3 bg-label-info rounded-4 shadow-sm mt-4">
                        <div class="d-flex align-items-center">
                            <i class="las la-info-circle text-info fs-3 me-2"></i>
                            <p class="mb-0 small text-dark lh-sm">@lang('The render represents the high-fidelity user interface. All branding and auth nodes are synchronized in real-time.')</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<x-confirmation-modal />
@endsection

@push('style')
<style>
    :root {
        --premium-primary: #696cff;
        --phone-frame: #343444;
    }

    .login-architecture-wrapper { font-family: 'Public Sans', sans-serif; }

    /* Sneat Colors */
    .bg-label-primary { background-color: #e7e7ff !important; color: var(--premium-primary) !important; }
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .bg-label-secondary { background-color: #f5f5f9 !important; color: #8592a3 !important; }
    
    .avatar-md { width: 48px; height: 48px; }
    .avatar-xs { width: 28px; height: 28px; }

    .shadow-premium { box-shadow: 0 0 10px rgba(105, 108, 255, 0.4); }
    .bg-light-soft { background-color: rgba(105, 108, 255, 0.02); }

    /* Modern Switch */
    .modern-switch .form-check-input { width: 3rem; height: 1.5rem; cursor: pointer; }
    .modern-switch .form-check-input:checked { background-color: var(--premium-primary); border-color: var(--premium-primary); }

    .auth-node-card { cursor: default; }
    .auth-node-card:hover { border-color: var(--premium-primary) !important; background: #fff; }

    /* Phone Shell Styling */
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
        display: flex;
        flex-direction: column;
    }

    .mockup-n-label { font-size: 0.65rem; color: #8592a3; font-weight: 600; margin-bottom: 5px; display: block; }
    .mockup-n-value {
        height: 40px;
        background: #f8f9fb;
        border: 1px solid #d9dee3;
        border-radius: 8px;
        width: 100%;
        padding: 0 12px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        color: #566a7f;
    }

    .social-btn-mockup {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        animation: slideInUp 0.3s ease;
    }
    
    .battery-icon { width: 18px; height: 9px; border: 1px solid #000; border-radius: 2px; position: relative; opacity: 0.5; }
    .battery-icon::after { content: ''; position: absolute; right: -3px; top: 2px; width: 2px; height: 3px; background: #000; }

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

    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }

    .shadow-md { box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }

    @keyframes slideInUp {
        from { transform: translateY(10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @media (max-width: 1199px) {
        .sticky-preview-wrapper { position: relative; top: 0; margin-top: 3rem; }
    }
</style>
@endpush

@push('script')
<script>
(function ($) {
    'use strict';

    // 1. Clock Sync
    function updateClock() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        $('.clock-real-time').text(hours + ':' + minutes);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Preview Synchronizer
    function syncLoginPreview() {
        // Text sync
        $('.sync-input').each(function() {
            const val = $(this).val();
            const target = $(this).data('mockup');
            $(target).text(val || $(this).attr('placeholder'));
        });

        // Credential Label sync
        const $checkedCreds = $('.login-field-cb:checked');
        const labels = $checkedCreds.map(function() { return $(this).data('label'); }).get();
        $('#mockupCredentialLabel').text(labels.length > 0 ? labels.join(' / ') : '@lang("Identity Marker")');

        // Social Buttons sync
        const $mockupSocialStack = $('#mockupSocialStack');
        const $mockupSocialSection = $('#mockupSocialSection');
        $mockupSocialStack.empty();

        const $checkedSocial = $('.social-login-cb:checked');
        if ($checkedSocial.length > 0) {
            $mockupSocialSection.show();
            $checkedSocial.each(function() {
                const icon = $(this).data('icon');
                const color = $(this).data('color');
                const html = `
                    <div class="social-btn-mockup" style="background: ${color}15; color: ${color};">
                        <i class="${icon} fs-5"></i>
                    </div>
                `;
                $mockupSocialStack.append(html);
            });
        } else {
            $mockupSocialSection.hide();
        }
    }

    $('.sync-input').on('input', syncLoginPreview);
    $(document).on('change', '.login-field-cb, .social-login-cb', syncLoginPreview);

    // Initial Sync
    syncLoginPreview();

})(jQuery);
</script>
@endpush

