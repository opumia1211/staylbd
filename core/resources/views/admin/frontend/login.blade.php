@extends('admin.layouts.app')
@section('panel')
@php
    $captchaEnabled = $content && isset($content->data_values->captcha_enabled)
        ? (int) $content->data_values->captcha_enabled === 1
        : true;
    $regList = registrationFieldsList();
    $credentialKeys = loginCredentialCapableKeys();
    $socialProviders = [
        'google'   => ['label' => 'Google', 'icon' => 'lab la-google'],
        'facebook' => ['label' => 'Facebook', 'icon' => 'lab la-facebook-f'],
        'twitter'  => ['label' => 'Twitter (X)', 'icon' => 'lab la-twitter'],
        'apple'    => ['label' => 'Apple', 'icon' => 'lab la-apple'],
        'github'   => ['label' => 'GitHub', 'icon' => 'lab la-github'],
    ];
    $socialConfig = getSocialLoginButtonsConfig();
@endphp

<div class="admin-login-control">
    <p class="text-muted small mb-3">@lang('Control what appears on the user login page. Save to apply.')</p>

    <form action="{{ route('admin.frontend.sections.content.login') }}" method="POST" enctype="multipart/form-data" id="loginContentForm">
        @csrf
        <input type="hidden" name="type" value="content">
        @if($content && isset($content->id))
        <input type="hidden" name="id" value="{{ $content->id }}">
        @endif

        {{-- 1. Page content --}}
        <div class="card mb-3">
            <div class="card-header py-2 px-3">
                <span class="fw-semibold">@lang('Login page content')</span>
            </div>
            <div class="card-body p-3">
                <div class="row g-2">
                    <div class="col-12 col-sm-6">
                        <label class="form-label small mb-1">@lang('Heading')</label>
                        <input type="text" class="form-control form-control-sm" name="heading" value="{{ old('heading', @$content->data_values->heading) }}" placeholder="@lang('Login')">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label small mb-1">@lang('Subheading')</label>
                        <input type="text" class="form-control form-control-sm" name="subheading" value="{{ old('subheading', @$content->data_values->subheading) }}" placeholder="@lang('Enter your credentials')">
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Login with (credentials) --}}
        <div class="card mb-3">
            <div class="card-header py-2 px-3">
                <span class="fw-semibold">@lang('Login with')</span>
            </div>
            <div class="card-body p-3">
                <p class="small text-muted mb-2">@lang('Allow users to sign in with these. Only options enabled in Registration can be turned on here.')</p>
                <div class="row g-2">
                    @foreach($credentialKeys as $fkey)
                    @php
                        $label = $regList[$fkey] ?? $fkey;
                        $regEnabled = isRegistrationFieldEnabled($fkey);
                        $loginChecked = isLoginFieldEnabledForDisplay($fkey);
                    @endphp
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="d-flex align-items-center justify-content-between p-2 rounded border {{ $regEnabled ? 'bg-light' : 'bg-secondary bg-opacity-10' }}">
                            <div class="min-w-0 flex-grow-1">
                                <span class="small fw-medium text-truncate d-block text-dark">{{ $label }}</span>
                                @if(!$regEnabled)
                                <small class="text-muted">@lang('Enable in Registration first')</small>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-2">
                                <div class="form-check form-switch mb-0">
                                    <input type="hidden" name="login_fields[{{ $fkey }}]" value="0">
                                    <input class="form-check-input" type="checkbox" name="login_fields[{{ $fkey }}]" value="1" id="login_field_{{ $fkey }}" {{ $loginChecked ? 'checked' : '' }}>
                                    <label class="form-check-label login-switch-label text-dark fw-semibold" for="login_field_{{ $fkey }}" data-on="@lang('ON')" data-off="@lang('OFF')">{{ $loginChecked ? __('ON') : __('OFF') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 3. Captcha --}}
        <div class="card mb-3">
            <div class="card-header py-2 px-3">
                <span class="fw-semibold">@lang('Security captcha')</span>
            </div>
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between p-2 rounded border bg-light">
                    <div class="min-w-0">
                        <span class="small fw-medium d-block text-dark">@lang('Show captcha on login')</span>
                        <small class="text-muted">@lang('Floating & full page')</small>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-2">
                        <div class="form-check form-switch mb-0">
                            <input type="hidden" name="captcha_enabled" value="0">
                            <input class="form-check-input" type="checkbox" name="captcha_enabled" value="1" id="login_captcha_enabled" {{ $captchaEnabled ? 'checked' : '' }}>
                            <label class="form-check-label login-switch-label text-dark fw-semibold" for="login_captcha_enabled" data-on="@lang('ON')" data-off="@lang('OFF')">{{ $captchaEnabled ? __('ON') : __('OFF') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Social login buttons --}}
        <div class="card mb-3">
            <div class="card-header py-2 px-3">
                <span class="fw-semibold">@lang('Social login buttons')</span>
            </div>
            <div class="card-body p-3">
                <p class="small text-muted mb-2">@lang('Show these on the user login page. Each must also be enabled in') <a href="{{ route('admin.setting.social.login') }}">@lang('Settings → Social Login')</a>.</p>
                <div class="row g-2">
                    @foreach($socialProviders as $provider => $info)
                    @php
                        $envEnabled = env(strtoupper($provider) . '_LOGIN_ENABLED') == '1';
                        $showOnLogin = isset($socialConfig[$provider]) && ((int)$socialConfig[$provider] === 1 || $socialConfig[$provider] === '1');
                    @endphp
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="d-flex align-items-center justify-content-between p-2 rounded border {{ $envEnabled ? 'bg-light' : 'bg-secondary bg-opacity-10' }}">
                            <div class="min-w-0 flex-grow-1">
                                <span class="small fw-medium text-truncate d-block text-dark"><i class="{{ $info['icon'] }} me-1 text--primary"></i>{{ $info['label'] }}</span>
                                @if(!$envEnabled)
                                <small class="text-muted">@lang('Configure in Settings')</small>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-2">
                                <div class="form-check form-switch mb-0">
                                    <input type="hidden" name="social_login_buttons[{{ $provider }}]" value="0">
                                    <input class="form-check-input" type="checkbox" name="social_login_buttons[{{ $provider }}]" value="1" id="social_btn_{{ $provider }}" {{ $showOnLogin ? 'checked' : '' }}>
                                    <label class="form-check-label login-switch-label text-dark fw-semibold" for="social_btn_{{ $provider }}" data-on="@lang('ON')" data-off="@lang('OFF')">{{ $showOnLogin ? __('ON') : __('OFF') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn--primary btn-sm">@lang('Save')</button>
    </form>
</div>

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
(function () {
    function setLabels() {
        document.querySelectorAll('#loginContentForm .login-switch-label').forEach(function (lab) {
            var cb = lab.control || document.getElementById(lab.getAttribute('for'));
            if (cb) lab.textContent = cb.checked ? (lab.getAttribute('data-on') || 'ON') : (lab.getAttribute('data-off') || 'OFF');
        });
    }
    document.getElementById('loginContentForm')?.addEventListener('change', setLabels);
    setLabels();
})();
</script>
@endpush
@endsection
