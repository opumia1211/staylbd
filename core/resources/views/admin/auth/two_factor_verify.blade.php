@extends('admin.layouts.auth')
@section('content')
@php
    $twoFaSubtitle = $pageTitle ?? __('Two-Factor Authentication');
@endphp
<div class="login-main login-main--no-captcha login-main--2fa">
    <div class="admin-login-shell">
        <div class="login-area login-area--compact login-area--2fa">
            <div class="login-wrapper login-wrapper--compact login-wrapper--2fa">
                @include('admin.auth.partials.auth-card-header', ['subtitle' => $twoFaSubtitle])
                <div class="login-wrapper__body admin-auth-form">
                    <form action="{{ route('admin.2fa.verify.submit') }}" method="POST" class="cmn-form login-form" autocomplete="off" id="admin2faVerifyForm">
                        @csrf
                        <p class="admin-2fa-lead">@lang('Enter the 6-digit code from your authenticator app.')</p>
                        <div class="form-group admin-2fa-otp-group">
                            <label class="sr-only" for="admin2faCode">@lang('Authentication code')</label>
                            <input type="text"
                                   name="code"
                                   id="admin2faCode"
                                   class="form-control admin-2fa-otp-input"
                                   placeholder="000000"
                                   maxlength="6"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   autocomplete="one-time-code"
                                   autofocus>
                        </div>
                        <p class="admin-2fa-hint">@lang('Codes refresh every 30 seconds. Wait for a new code if the current one fails.')</p>

                        <div class="admin-2fa-divider" role="presentation">
                            <span>@lang('or')</span>
                        </div>

                        <div class="form-group admin-2fa-recovery-wrap">
                            <label class="admin-2fa-recovery-label" for="admin2faRecovery">@lang('Recovery code')</label>
                            <input type="text"
                                   name="recovery_code"
                                   id="admin2faRecovery"
                                   class="form-control"
                                   placeholder="@lang('One-time backup code')"
                                   autocomplete="off"
                                   spellcheck="false"
                                   autocapitalize="characters">
                            <p class="admin-2fa-recovery-hint mb-0">@lang('Use a saved recovery code if you cannot access your authenticator.')</p>
                        </div>

                        <div class="form-group admin-2fa-actions mb-0">
                            <button type="submit" class="btn cmn-btn w-100" id="admin2faVerifyBtn" data-submitting="0">
                                @lang('Verify & continue')
                            </button>
                            <a href="{{ route('admin.logout') }}" class="btn btn-outline-secondary w-100 admin-2fa-cancel">@lang('Cancel and return to login')</a>
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
        var form = document.getElementById('admin2faVerifyForm');
        var btn = document.getElementById('admin2faVerifyBtn');
        if (form && btn) {
            form.addEventListener('submit', function() {
                if (btn.getAttribute('data-submitting') === '1') return;
                btn.setAttribute('data-submitting', '1');
                btn.disabled = true;
                btn.textContent = @json(__('Please wait...'));
            });
        }
        var otp = document.getElementById('admin2faCode');
        if (otp) {
            otp.addEventListener('input', function() {
                otp.value = otp.value.replace(/\D/g, '').slice(0, 6);
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
