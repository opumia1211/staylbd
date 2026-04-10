@extends('admin.layouts.auth')
@section('content')
@php
    $setupSubtitle = __('Authenticator setup');
    $clearSetupCodeField = (bool) session()->pull('admin_2fa_setup_clear_code');
@endphp
<div class="login-main login-main--no-captcha login-main--2fa login-main--2fa-setup">
    <div class="admin-login-shell">
        <div class="login-area login-area--compact login-area--2fa">
            <div class="login-wrapper login-wrapper--compact login-wrapper--2fa login-wrapper--2fa-setup">
                @include('admin.auth.partials.auth-card-header', ['subtitle' => $setupSubtitle])
                <div class="login-wrapper__body admin-auth-form">
                    <form action="{{ route('admin.2fa.setup.confirm') }}" method="POST" class="cmn-form login-form" id="admin2faSetupForm" autocomplete="off" novalidate data-clear-code="{{ $clearSetupCodeField ? '1' : '' }}">
                        @csrf
                        <div class="admin-2fa-setup-card">
                            <div class="admin-2fa-setup-qr-row">
                                <div id="twofa-qr-code" class="admin-2fa-setup-qr-inner" aria-label="@lang('Authenticator QR code')"></div>
                            </div>

                            <div class="admin-2fa-setup-sep" role="presentation"></div>

                            <div class="admin-2fa-setup-manual-row">
                                <span class="admin-2fa-secret-label">@lang('Manual key')</span>
                                <code class="admin-2fa-secret-code" title="@lang('Copy into your authenticator app')">{{ $secret }}</code>
                                <p class="admin-2fa-setup-regenerate">
                                    <a href="{{ route('admin.2fa.setup') }}">@lang('Generate a new QR code')</a>
                                </p>
                            </div>

                            <div class="admin-2fa-setup-sep" role="presentation"></div>

                            <div class="admin-2fa-setup-code-row">
                                <label class="admin-2fa-setup-field-label" for="admin2faSetupCode">@lang('Verification code')</label>
                                <input type="text"
                                       name="code"
                                       id="admin2faSetupCode"
                                       class="form-control admin-2fa-otp-input admin-2fa-otp-input--setup"
                                       value=""
                                       placeholder="000000"
                                       title="@lang('6-digit code from your app')"
                                       maxlength="6"
                                       minlength="6"
                                       inputmode="numeric"
                                       pattern="[0-9]{6}"
                                       required
                                       autocomplete="off"
                                       autocorrect="off"
                                       autocapitalize="off"
                                       spellcheck="false"
                                       autofocus>
                            </div>

                            <div class="admin-2fa-setup-actions-row">
                                <button type="submit" class="btn cmn-btn w-100" id="admin2faSetupBtn" data-submitting="0">
                                    @lang('Enable 2FA')
                                </button>
                                <a href="{{ route('admin.logout') }}" class="btn btn-outline-secondary w-100 admin-2fa-cancel">@lang('Cancel')</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script src="{{ asset('assets/admin/js/vendor/qrcode.min.js') }}?v={{ $assetVersion ?? config('app.version') }}"></script>
<script>
(function() {
    var otpauthUrl = @json($otpauthUrl);
    var qrSize = 200;
    function initQr() {
        var el = document.getElementById('twofa-qr-code');
        if (typeof QRCode === 'undefined' || !el) return;
        el.innerHTML = '';
        try {
            var opts = { text: otpauthUrl, width: qrSize, height: qrSize };
            if (QRCode.CorrectLevel) {
                opts.correctLevel = QRCode.CorrectLevel.H;
            }
            new QRCode(el, opts);
        } catch (e) {}
    }
    function initForm() {
        var form = document.getElementById('admin2faSetupForm');
        var btn = document.getElementById('admin2faSetupBtn');
        var code = document.getElementById('admin2faSetupCode');
        if (code) {
            if (form && form.getAttribute('data-clear-code') === '1') {
                code.value = '';
                try { code.focus(); } catch (e) {}
            }
            code.addEventListener('input', function() {
                code.value = code.value.replace(/\D/g, '').slice(0, 6);
            });
        }
        if (form && btn) {
            form.addEventListener('submit', function() {
                if (btn.getAttribute('data-submitting') === '1') return;
                btn.setAttribute('data-submitting', '1');
                btn.disabled = true;
                btn.textContent = @json(__('Please wait...'));
            });
        }
    }
    window.addEventListener('pageshow', function(ev) {
        if (!ev.persisted) return;
        var code = document.getElementById('admin2faSetupCode');
        var btn = document.getElementById('admin2faSetupBtn');
        if (code) code.value = '';
        if (btn) {
            btn.disabled = false;
            btn.setAttribute('data-submitting', '0');
            btn.textContent = @json(__('Enable 2FA'));
        }
    });
    function boot() {
        initQr();
        initForm();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
</script>
@endpush
@endsection
