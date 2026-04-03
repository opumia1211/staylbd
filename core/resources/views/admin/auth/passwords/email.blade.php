@extends('admin.layouts.auth')
@section('content')
@php
    $captchaCode = $captchaCode ?? null;
    $useImageCaptcha = $useImageCaptcha ?? false;
    $passResetLockoutUntil = (int) ($passResetLockoutUntil ?? 0);
    $passResetFormLocked = $passResetLockoutUntil > time();
@endphp
<div class="login-main">
    <div class="admin-login-shell">
                <div class="login-area">
                    <div class="login-wrapper">
                        @include('admin.auth.partials.auth-card-header', ['subtitle' => __('Recover Account')])
                        <div class="login-wrapper__body admin-auth-form">
                            @if($passResetFormLocked)
                            <div id="adminPassResetCountdownWrap" class="admin-login-countdown-wrap" data-retry-at="{{ $passResetLockoutUntil }}">
                                <div class="admin-login-countdown-inner">
                                    <span class="admin-login-countdown-icon" aria-hidden="true"><i class="las la-clock"></i></span>
                                    <span class="admin-login-countdown-text">@lang('Too many attempts. Try again in')</span>
                                    <span id="adminPassResetCountdown" class="admin-login-countdown-timer">--:--</span>
                                </div>
                                <p class="admin-login-countdown-note mb-0">@lang('The form will unlock when the countdown ends.')</p>
                            </div>
                            @endif
                            <form action="{{ route('admin.password.reset') }}" method="POST" class="cmn-form login-form" autocomplete="off" id="adminPasswordResetForm" @if($passResetFormLocked) data-blocked="1" @endif>
                                @csrf
                                <div class="form-group">
                                    <label class="sr-only" for="adminResetEmail">@lang('Email')</label>
                                    <input type="email"
                                           id="adminResetEmail"
                                           name="email"
                                           class="form-control"
                                           value="{{ old('email') }}"
                                           placeholder="@lang('Email address')"
                                           required
                                           autocomplete="email"
                                           @if($passResetFormLocked) disabled @endif>
                                </div>
                                @if($captchaCode || $useImageCaptcha)
                                <div class="form-group admin-code-captcha-wrap">
                                    <label class="sr-only" for="adminResetCaptchaInput">@lang('Captcha')</label>
                                    <div class="admin-captcha-toolbar" role="group">
                                        <div class="captcha-image-wrap" aria-hidden="true">
                                            @if($useImageCaptcha)
                                            <img src="{{ route('admin.login.captcha.image') }}?t={{ time() }}" alt="" id="adminResetCaptchaImage" class="admin-captcha-img" width="132" height="34" decoding="async" fetchpriority="low">
                                            @else
                                            <span class="code-captcha-image" id="adminResetCaptchaCode">{{ $captchaCode }}</span>
                                            @endif
                                        </div>
                                        <button type="button" class="captcha-refresh-btn" id="adminResetCaptchaRefresh" title="@lang('Refresh')" aria-label="@lang('Refresh')" @if($passResetFormLocked) disabled @endif><i class="las la-redo-alt" aria-hidden="true"></i></button>
                                        <input type="text"
                                               name="admin_login_captcha"
                                               id="adminResetCaptchaInput"
                                               class="form-control admin-captcha-inline-input code-captcha-input"
                                               placeholder="@lang('Code')"
                                               title="@lang('Enter the code as shown. Capital or small letters both work.')"
                                               required
                                               autocomplete="off"
                                               inputmode="text"
                                               maxlength="10"
                                               @if($passResetFormLocked) disabled @endif>
                                    </div>
                                </div>
                                @endif
                                <div class="form-group mb-0">
                                    <a href="{{ route('admin.login') }}" class="forget-text">@lang('Login Here')</a>
                                </div>
                                <button type="submit" class="btn cmn-btn w-100" id="adminPasswordResetBtn" data-submitting="0" @if($passResetFormLocked) disabled @endif>
                                    @lang('Submit')
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
    </div>
</div>

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var countdownWrap = document.getElementById('adminPassResetCountdownWrap');
    var countdownEl = document.getElementById('adminPassResetCountdown');
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

    var form = document.getElementById('adminPasswordResetForm');
    var btn = document.getElementById('adminPasswordResetBtn');
    if (btn && form) {
        form.addEventListener('submit', function(e) {
            if (form.getAttribute('data-blocked') === '1') {
                e.preventDefault();
                return;
            }
            if (btn.getAttribute('data-submitting') === '1') return;
            btn.setAttribute('data-submitting', '1');
            btn.disabled = true;
            btn.innerHTML = '<i class="las la-spinner la-spin me-2"></i> @lang("Please wait...")';
        });
    }
    var refreshBtn = document.getElementById('adminResetCaptchaRefresh');
    var imgEl = document.getElementById('adminResetCaptchaImage');
    var codeEl = document.getElementById('adminResetCaptchaCode');
    var inputEl = document.getElementById('adminResetCaptchaInput');
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
});
</script>
@endpush
@endsection
