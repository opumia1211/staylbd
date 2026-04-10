@extends('admin.layouts.auth')
@section('content')
@php
    $adminLoginLogo = getLogo('logo_dark') ?: getLogo('logo');
    $adminSiteName = $general->site_name ?? gs('site_name');
    $captchaCode = $captchaCode ?? null;
    $blockInfo = $blockInfo ?? ['blocked' => false, 'retry_at' => null, 'retry_minutes' => 0];
    $loginBlocked = !empty($blockInfo['blocked']) && !empty($blockInfo['retry_at']);
    $showCaptcha = !empty($captchaCode) || !empty($useImageCaptcha ?? false);
    $adminLoginRememberChecked = old('remember') && (!isset($errors) || !$errors->any());
@endphp

<div class="login-main">
    <div class="admin-login-shell">
        <div class="login-area">
            <div class="login-wrapper">
                @include('admin.auth.partials.auth-card-header')
                <div class="login-wrapper__body">
                    <form action="{{ route('admin.login.submit') }}" method="POST" class="admin-auth-form" id="adminLoginForm">
                        @csrf
                        
                        <div class="form-group">
                            <label class="form-label">@lang('Email or Username')</label>
                            <input type="text" 
                                   id="adminLoginUsername" 
                                   class="form-control" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   placeholder="@lang('Enter email or username')" 
                                   required>
                        </div>


                        <div class="form-group">
                            <label class="form-label">@lang('Password')</label>
                            <div class="password-input-wrap">
                                <input type="password" 
                                       class="form-control" 
                                       name="password" 
                                       id="adminPassword" 
                                       placeholder="@lang('Enter password')" 
                                       required>
                                <button type="button" class="password-toggle-btn" id="adminPasswordToggle" title="@lang('Show password')">
                                    <i class="las la-eye"></i>
                                </button>
                            </div>
                        </div>

                        @if($showCaptcha)
                        <div class="form-group">
                            <label class="form-label">@lang('Verify You Are Human')</label>
                            <div class="captcha-box">
                                <div class="captcha-img-wrap">
                                    @if($useImageCaptcha ?? false)
                                        <img src="{{ route('admin.login.captcha.image') }}?t={{ time() }}" alt="captcha" id="adminCaptchaImage">
                                    @else
                                        <span class="captcha-code-text" id="adminCaptchaCode">{{ $captchaCode }}</span>
                                    @endif
                                </div>
                                <button type="button" class="captcha-refresh-btn" id="adminCaptchaRefresh" title="@lang('Refresh Code')">
                                    <i class="las la-sync-alt"></i>
                                </button>
                                <input type="text" 
                                       name="admin_login_captcha" 
                                       id="adminCaptchaInput" 
                                       class="form-control captcha-input" 
                                       placeholder="@lang('Code')" 
                                       required 
                                       autocomplete="off" 
                                       maxlength="10">
                            </div>
                        </div>
                        @endif

                        <div class="form-options">
                            <div class="form-check-wrap">
                                <input type="checkbox" name="remember" id="remember" value="1" required>
                                <label for="remember">@lang('Remember Me')</label>
                            </div>
                            <a href="{{ route('admin.password.reset') }}" class="forgot-link">@lang('Forgot Password?')</a>
                        </div>

                        <div class="form-action">
                            <button type="submit" class="btn btn-primary w-100" id="adminLoginBtn">
                                @lang('Login')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@push('script')
<script>
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Password Visibility Toggle
        var passwordInput = document.getElementById('adminPassword');
        var toggleBtn = document.getElementById('adminPasswordToggle');
        
        if (passwordInput && toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                var icon = toggleBtn.querySelector('i');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.replace('la-eye', 'la-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.replace('la-eye-slash', 'la-eye');
                }
            });
        }


        // 2. Captcha Refresh Logic
        var refreshBtn = document.getElementById('adminCaptchaRefresh');
        var captchaImg = document.getElementById('adminCaptchaImage');
        var captchaCodeText = document.getElementById('adminCaptchaCode');
        var captchaInput = document.getElementById('adminCaptchaInput');
        
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                refreshBtn.disabled = true;
                refreshBtn.querySelector('i').classList.add('la-spin');
                
                if (captchaImg) {
                    // Refresh image-based captcha
                    captchaImg.src = '{{ route("admin.login.captcha.image") }}?t=' + Date.now();
                    if (captchaInput) captchaInput.value = '';
                    refreshBtn.disabled = false;
                    refreshBtn.querySelector('i').classList.remove('la-spin');
                } else {
                    // Refresh text-based captcha via AJAX
                    fetch('{{ route("admin.login.captcha.refresh") }}', { 
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } 
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.code && captchaCodeText) captchaCodeText.textContent = data.code;
                        if (captchaInput) captchaInput.value = '';
                    })
                    .catch(err => console.error('Captcha refresh failed', err))
                    .finally(() => {
                        refreshBtn.disabled = false;
                        refreshBtn.querySelector('i').classList.remove('la-spin');
                    });
                }
            });
        }

        // 3. Form Submission State
        var loginForm = document.getElementById('adminLoginForm');
        var loginBtn = document.getElementById('adminLoginBtn');
        
        if (loginForm && loginBtn) {
            loginForm.addEventListener('submit', function() {
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<i class="las la-spinner la-spin"></i> @lang("Processing...")';
            });
        }
    });
})();
</script>
@endpush
@endsection
