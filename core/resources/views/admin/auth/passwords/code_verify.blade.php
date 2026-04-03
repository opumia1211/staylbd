@extends('admin.layouts.auth')
@section('content')
<div class="login-main">
    <div class="admin-login-shell">
                <div class="login-area">
                    <div class="login-wrapper">
                        @include('admin.auth.partials.auth-card-header', ['subtitle' => __('Verify Code')])
                        <div class="login-wrapper__body admin-auth-form">
                            <p class="text-muted small mb-3">@lang('Please check your email and enter the verification code you got in your email.')</p>
                            <form action="{{ route('admin.password.verify.code') }}" method="POST" class="cmn-form login-form" autocomplete="off" id="adminCodeVerifyForm">
                                @csrf
                                <div class="form-group">
                                    <label>@lang('Verification Code') <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="code"
                                           class="form-control"
                                           placeholder="000000"
                                           maxlength="6"
                                           pattern="[0-9]*"
                                           inputmode="numeric"
                                           autocomplete="one-time-code"
                                           required>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                    <a href="{{ route('admin.password.reset') }}" class="forget-text">@lang('Try to send again')</a>
                                </div>
                                <button type="submit" class="btn cmn-btn w-100 mt-2" id="adminCodeVerifyBtn" data-submitting="0">
                                    <i class="las la-check-double me-2"></i>@lang('Verify')
                                </button>
                            </form>
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.login') }}" class="forget-text"><i class="las la-sign-in-alt me-1"></i>@lang('Back to Login')</a>
                            </div>
                        </div>
                    </div>
                </div>
    </div>
</div>

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('adminCodeVerifyForm');
    var btn = document.getElementById('adminCodeVerifyBtn');
    var codeInput = form ? form.querySelector('input[name="code"]') : null;
    if (btn && form) {
        form.addEventListener('submit', function() {
            if (btn.getAttribute('data-submitting') === '1') return;
            btn.setAttribute('data-submitting', '1');
            btn.disabled = true;
            btn.innerHTML = '<i class="las la-spinner la-spin me-2"></i> @lang("Please wait...")';
        });
    }
    if (codeInput) {
        codeInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
    }
});
</script>
@endpush
@endsection
