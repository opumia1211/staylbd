@extends('admin.layouts.auth')
@section('content')
@php
    $recoverySubtitle = __('Recovery codes');
@endphp
<div class="login-main login-main--no-captcha login-main--2fa login-main--recovery-codes">
    <div class="admin-login-shell">
        <div class="login-area login-area--compact login-area--2fa">
            <div class="login-wrapper login-wrapper--compact login-wrapper--2fa login-wrapper--recovery-codes">
                @include('admin.auth.partials.auth-card-header', ['subtitle' => $recoverySubtitle])
                <div class="login-wrapper__body admin-auth-form admin-recovery-inner">
                    <div class="admin-recovery-status" role="status">
                        <span class="admin-recovery-status-icon" aria-hidden="true"><i class="las la-shield-alt"></i></span>
                        <p class="admin-recovery-status-text">{{ $pageTitle ?? __('Save Your Recovery Codes') }}</p>
                    </div>

                    <p class="admin-recovery-lead">@lang('Use one code per sign-in if you lose your authenticator. Each code works only once.')</p>

                    <div class="admin-recovery-warn" role="alert">
                        <strong>@lang('Shown once')</strong>
                        @lang('Copy or print now and store offline. You cannot view these codes again on this screen.')
                    </div>

                    <div class="admin-recovery-codes-box" id="recoveryCodesBox">
                        <div class="admin-recovery-grid">
                            @foreach($codes as $code)
                                <code class="admin-recovery-code recovery-code">{{ $code }}</code>
                            @endforeach
                        </div>
                    </div>

                    <div class="admin-recovery-toolbar">
                        <button type="button"
                                class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center justify-content-center"
                                id="copyRecoveryCodes"
                                data-codes="{{ e(json_encode(implode("\n", $codes))) }}">
                            <i class="las la-copy admin-recovery-btn-icon"></i>@lang('Copy all')
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center justify-content-center" id="printRecoveryCodes">
                            <i class="las la-print admin-recovery-btn-icon"></i>@lang('Print')
                        </button>
                    </div>

                    <div class="admin-recovery-continue">
                        <a href="{{ route('admin.dashboard') }}" class="btn cmn-btn w-100 d-inline-flex align-items-center justify-content-center" id="adminRecoveryContinueBtn">
                            <i class="las la-check-circle admin-recovery-btn-icon"></i>@lang("I've saved these — continue")
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
(function() {
    var copyBtn = document.getElementById('copyRecoveryCodes');
    var printBtn = document.getElementById('printRecoveryCodes');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            var raw = this.getAttribute('data-codes');
            try { raw = raw ? JSON.parse(raw) : ''; } catch (e) { raw = ''; }
            var codes = (raw || '').trim();
            if (!codes) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(codes).then(function() {
                    if (typeof notify === 'function') notify('success', @json(__('Recovery codes copied to clipboard.')));
                    else alert(@json(__('Recovery codes copied to clipboard.')));
                }).catch(function() { fallbackCopy(codes); });
            } else {
                fallbackCopy(codes);
            }
        });
    }
    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            if (typeof notify === 'function') notify('success', @json(__('Recovery codes copied.')));
            else alert(@json(__('Recovery codes copied.')));
        } catch (e) {}
        document.body.removeChild(ta);
    }
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            var box = document.getElementById('recoveryCodesBox');
            if (!box) return;
            var title = @json(__('Recovery Codes'));
            var h1 = @json(__('Save Your Recovery Codes'));
            var note = @json(__('Store these in a safe place. Each code works only once.'));
            var css = 'body{font:14px/1.45 system-ui,-apple-system,sans-serif;color:#111827;padding:24px;max-width:520px;margin:0 auto;}h1{font-size:15px;margin:0 0 6px;font-weight:600;}p{font-size:12px;color:#6b7280;margin:0 0 14px;}pre{font:12px/1.5 ui-monospace,Menlo,Consolas,monospace;margin:0;padding:12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;white-space:pre-wrap;word-break:break-all;}';
            var w = window.open('', '_blank');
            w.document.write('<html><head><title>' + title + '</title>
{{-- inline style moved to critical-admin.css --}}
</head><body><h1>' + h1 + '</h1><p>' + note + '</p><pre>' + (box.innerText || box.textContent || '') + '</pre></body></html>');
            w.document.close();
            w.print();
            w.close();
        });
    }
})();
</script>
@endpush
@endsection
