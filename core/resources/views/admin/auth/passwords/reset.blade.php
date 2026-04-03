@extends('admin.layouts.auth')
@section('content')
<div class="login-main">
    <div class="admin-login-shell">
                <div class="login-area">
                    <div class="login-wrapper">
                        @include('admin.auth.partials.auth-card-header', ['subtitle' => __('Set New Password')])
                        <div class="login-wrapper__body admin-auth-form">
                            <form action="{{ route('admin.password.change') }}" method="POST" class="cmn-form login-form" autocomplete="off" id="adminPasswordChangeForm">
                                @csrf
                                <input type="hidden" name="email" value="{{ $email }}">
                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="form-group">
                                    <label>@lang('New Password') <span class="text-danger">*</span></label>
                                    <div class="password-input-wrap">
                                        <input type="password"
                                               name="password"
                                               id="adminNewPassword"
                                               class="form-control"
                                               placeholder="@lang('Enter new password')"
                                               required
                                               minlength="8"
                                               autocomplete="new-password">
                                        <button type="button" class="password-toggle-btn" id="adminNewPasswordToggle" title="@lang('Show password')" aria-label="@lang('Show password')">
                                            <i class="las la-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Confirm Password') <span class="text-danger">*</span></label>
                                    <div class="password-input-wrap">
                                        <input type="password"
                                               name="password_confirmation"
                                               id="adminConfirmPassword"
                                               class="form-control"
                                               placeholder="@lang('Re-type new password')"
                                               required
                                               minlength="8"
                                               autocomplete="new-password">
                                        <button type="button" class="password-toggle-btn" id="adminConfirmPasswordToggle" title="@lang('Show password')" aria-label="@lang('Show password')">
                                            <i class="las la-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                    <a href="{{ route('admin.login') }}" class="forget-text">@lang('Login Here')</a>
                                </div>
                                <button type="submit" class="btn cmn-btn w-100 mt-2" id="adminPasswordChangeBtn" data-submitting="0">
                                    <i class="las la-key me-2"></i>@lang('Change Password')
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
    function setupToggle(inputId, btnId) {
        var pw = document.getElementById(inputId);
        var btn = document.getElementById(btnId);
        if (!pw || !btn) return;
        btn.addEventListener('click', function() {
            var icon = btn.querySelector('i');
            if (pw.type === 'password') {
                pw.type = 'text';
                if (icon) { icon.classList.remove('la-eye'); icon.classList.add('la-eye-slash'); }
            } else {
                pw.type = 'password';
                if (icon) { icon.classList.remove('la-eye-slash'); icon.classList.add('la-eye'); }
            }
        });
    }
    setupToggle('adminNewPassword', 'adminNewPasswordToggle');
    setupToggle('adminConfirmPassword', 'adminConfirmPasswordToggle');

    var form = document.getElementById('adminPasswordChangeForm');
    var btn = document.getElementById('adminPasswordChangeBtn');
    if (btn && form) {
        form.addEventListener('submit', function() {
            if (btn.getAttribute('data-submitting') === '1') return;
            btn.setAttribute('data-submitting', '1');
            btn.disabled = true;
            btn.innerHTML = '<i class="las la-spinner la-spin me-2"></i> @lang("Please wait...")';
        });
    }
});
</script>
@endpush
@endsection
