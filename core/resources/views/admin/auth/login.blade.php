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
<div class="login-main {{ $showCaptcha ? '' : 'login-main--no-captcha' }}">
    <div class="admin-login-shell">
                <div class="login-area {{ $showCaptcha ? '' : 'login-area--compact' }}">
                    <div class="login-wrapper {{ $showCaptcha ? '' : 'login-wrapper--compact' }}">
                        @include('admin.auth.partials.auth-card-header')
                        <div class="login-wrapper__body admin-auth-form">
                            @if($loginBlocked)
                            <div id="adminLoginCountdownWrap" class="admin-login-countdown-wrap" data-retry-at="{{ (int) ($blockInfo['retry_at'] ?? 0) }}">
                                <div class="admin-login-countdown-inner">
                                    <span class="admin-login-countdown-icon" aria-hidden="true"><i class="las la-clock"></i></span>
                                    <span class="admin-login-countdown-text">@lang('Too many failed attempts. Try again in')</span>
                                    <span id="adminLoginCountdown" class="admin-login-countdown-timer">--:--</span>
                                </div>
                                <p class="admin-login-countdown-note mb-0">@lang('Login form will be available when the countdown ends.')</p>
                            </div>
                            @endif
                            <form action="{{ route('admin.login.submit') }}" method="POST" class="cmn-form login-form" autocomplete="off" id="adminLoginForm" @if($loginBlocked) data-blocked="1" @endif>
                                @csrf
                                {{-- First username/password in DOM absorb autofill; not submitted (no name). Real fields stay empty until typed. --}}
                                <div class="admin-autofill-decoy" aria-hidden="true">
                                    <input type="text" tabindex="-1" autocomplete="username">
                                    <input type="password" tabindex="-1" autocomplete="current-password">
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="adminLoginUsername">@lang('Email or Username')</label>
                                    <input type="text"
                                           id="adminLoginUsername"
                                           class="form-control"
                                           name="username"
                                           value="{{ old('username') }}"
                                           placeholder="@lang('Email or username')"
                                           required
                                           autocomplete="off"
                                           autocorrect="off"
                                           autocapitalize="none"
                                           spellcheck="false"
                                           data-lpignore="true"
                                           data-1p-ignore
                                           data-bwignore
                                           data-form-type="other"
                                           @if($loginBlocked) disabled @endif>
                                </div>
                                <div class="form-group">
                                    <label class="sr-only" for="adminPassword">@lang('Password')</label>
                                    <div class="password-input-wrap">
                                        <input type="password"
                                               class="form-control"
                                               name="password"
                                               id="adminPassword"
                                               placeholder="@lang('Password')"
                                               required
                                               autocomplete="new-password"
                                               data-lpignore="true"
                                               data-1p-ignore
                                               data-bwignore
                                               data-form-type="other"
                                               @if($loginBlocked) disabled @endif>
                                        <button type="button" class="password-toggle-btn" id="adminPasswordToggle" title="@lang('Show password')" aria-label="@lang('Show password')">
                                            <i class="las la-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <a href="{{ route('admin.password.reset') }}" class="admin-forgot-link">@lang('Forgot Password?')</a>
                                </div>
                                @if($showCaptcha)
                                <div class="form-group admin-code-captcha-wrap">
                                    <label class="sr-only" for="adminCaptchaInput">@lang('Captcha')</label>
                                    <div class="admin-captcha-toolbar" role="group">
                                        <div class="captcha-image-wrap" aria-hidden="true">
                                            @if($useImageCaptcha ?? false)
                                            <img src="{{ route('admin.login.captcha.image') }}?t={{ time() }}" alt="" id="adminCaptchaImage" class="admin-captcha-img" width="132" height="34" decoding="async" fetchpriority="low">
                                            @else
                                            <span class="code-captcha-image" id="adminCaptchaCode">{{ $captchaCode }}</span>
                                            @endif
                                        </div>
                                        <button type="button" class="captcha-refresh-btn" id="adminCaptchaRefresh" title="@lang('Refresh')" aria-label="@lang('Refresh')"><i class="las la-redo-alt" aria-hidden="true"></i></button>
                                        <input type="text"
                                               name="admin_login_captcha"
                                               id="adminCaptchaInput"
                                               class="form-control admin-captcha-inline-input code-captcha-input"
                                               placeholder="@lang('Code')"
                                               title="@lang('Enter the code as shown. Capital or small letters both work.')"
                                               required
                                               autocomplete="off"
                                               inputmode="text"
                                               maxlength="10"
                                               @if($loginBlocked) disabled @endif>
                                    </div>
                                </div>
                                @endif
                                <div class="form-group form-check admin-remember-row">
                                    <input class="form-check-input" name="remember" type="checkbox" id="remember" value="1" @checked($adminLoginRememberChecked) @if($loginBlocked) disabled @endif>
                                    <label class="form-check-label" for="remember">@lang('Remember Me')</label>
                                </div>
                                <div class="form-group mb-0">
                                    <button type="submit"
                                            class="btn cmn-btn w-100"
                                            id="adminLoginBtn"
                                            data-submitting="0"
                                            disabled
                                            @if(!$loginBlocked) title="@lang('Tick Remember Me to enable Login.')" @endif>
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
    function init() {
        var countdownWrap = document.getElementById('adminLoginCountdownWrap');
        var countdownEl = document.getElementById('adminLoginCountdown');
        if (countdownWrap && countdownEl) {
            var retryAt = parseInt(countdownWrap.getAttribute('data-retry-at'), 10);
            function pad(n) { return n < 10 ? '0' + n : n; }
            function tick() {
                var now = Math.floor(Date.now() / 1000);
                var left = retryAt - now;
                if (left <= 0) {
                    countdownEl.textContent = '0:00';
                    location.reload();
                    return;
                }
                var m = Math.floor(left / 60);
                var s = left % 60;
                countdownEl.textContent = pad(m) + ':' + pad(s);
            }
            tick();
            setInterval(tick, 1000);
        }

        var userEl = document.getElementById('adminLoginUsername');
        var pw = document.getElementById('adminPassword');
        function adminNoAutofillUntilInteract(el) {
            if (!el || el.disabled) return;
            el.setAttribute('readonly', 'readonly');
            function unlock() {
                el.removeAttribute('readonly');
                el.removeEventListener('focus', unlock);
                el.removeEventListener('pointerdown', unlock);
            }
            el.addEventListener('focus', unlock);
            el.addEventListener('pointerdown', unlock);
        }
        adminNoAutofillUntilInteract(userEl);
        adminNoAutofillUntilInteract(pw);

        var btn = document.getElementById('adminPasswordToggle');
        if (pw && btn) {
            btn.addEventListener('click', function() {
                var icon = btn.querySelector('i');
                if (pw.type === 'password') {
                    pw.type = 'text';
                    if (icon) { icon.classList.remove('la-eye'); icon.classList.add('la-eye-slash'); }
                } else {
                    pw.type = 'password';
                    if (icon) { icon.classList.remove('la-eye-slash'); icon.classList.add('la-eye'); }
                }
            }, { passive: true });
        }

        var refreshBtn = document.getElementById('adminCaptchaRefresh');
        var codeEl = document.getElementById('adminCaptchaCode');
        var imgEl = document.getElementById('adminCaptchaImage');
        var inputEl = document.getElementById('adminCaptchaInput');
        if (refreshBtn) {
            var refreshHtml = refreshBtn.innerHTML;
            refreshBtn.addEventListener('click', function() {
                refreshBtn.disabled = true;
                refreshBtn.innerHTML = '&hellip;';
                if (imgEl) {
                    imgEl.src = '{{ route('admin.login.captcha.image') }}?t=' + Date.now();
                    if (inputEl) inputEl.value = '';
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML = refreshHtml;
                } else {
                    fetch('{{ route('admin.login.captcha.refresh') }}', { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            if (data && data.code && codeEl) codeEl.textContent = data.code;
                            if (inputEl) inputEl.value = '';
                        })
                        .catch(function() {})
                        .finally(function() {
                            refreshBtn.disabled = false;
                            refreshBtn.innerHTML = refreshHtml;
                        });
                }
            });
        }

        var loginBtn = document.getElementById('adminLoginBtn');
        var loginForm = document.getElementById('adminLoginForm');
        var rememberEl = document.getElementById('remember');
        function syncAdminLoginButton() {
            if (!loginBtn || !loginForm) return;
            if (loginForm.getAttribute('data-blocked') === '1') {
                loginBtn.disabled = true;
                return;
            }
            var checked = rememberEl && rememberEl.checked;
            loginBtn.disabled = !checked;
        }
        if (rememberEl) {
            rememberEl.addEventListener('change', syncAdminLoginButton);
        }
        syncAdminLoginButton();

        if (loginBtn && loginForm) {
            loginForm.addEventListener('submit', function(e) {
                if (loginForm.getAttribute('data-blocked') === '1') {
                    e.preventDefault();
                    return;
                }
                if (rememberEl && !rememberEl.checked) {
                    e.preventDefault();
                    return;
                }
                if (loginBtn.getAttribute('data-submitting') === '1') {
                    e.preventDefault();
                    return;
                }
                loginBtn.setAttribute('data-submitting', '1');
                loginBtn.disabled = true;
                loginBtn.textContent = @json(__('Please wait...'));
            });
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endpush
@endsection
