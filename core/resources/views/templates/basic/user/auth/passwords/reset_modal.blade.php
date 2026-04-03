@extends($activeTemplate . 'layouts.auth_modal')
@section('content')
<div class="auth-overlay">
    <div class="auth-card" style="position:relative;">
        @php $authLogo = getLogo('logo'); $siteName = gs('site_name'); $general = gs(); @endphp
        <button type="button" class="auth-close" onclick="window.location.href='{{ route('home') }}'" aria-label="@lang('Close')" title="@lang('Close')">&times;</button>
        <div class="auth-header">
            @if($authLogo)
                <img src="{{ $authLogo }}" alt="{{ $siteName }}">
            @endif
            @if($siteName)
                <span class="auth-title">{{ __($siteName) }}</span>
            @endif
        </div>
        <div class="account-header account-header--compact">
            <h5 class="title mb-0">@lang('Reset Password')</h5>
            <p class="mb-0 fs--14px mt-1">@lang('Please enter a new strong password and don\\'t share it with anyone.')</p>
        </div>
        <form method="POST" action="{{ route('user.password.update') }}" class="auth-form-placeholder" autocomplete="off">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group password-field">
                <label class="form--label sr-only" for="reset_password">@lang('Password')</label>
                <input type="password" id="reset_password" class="form-control form--control" name="password" required placeholder="@lang('New Password')" autocomplete="new-password">
                <span class="password-toggle" onclick="togglePassword('reset_password')">@lang('Show')</span>
                @if ($general->secure_password)
                    <div class="input-popup">
                        <p class="error lower">@lang('1 small letter minimum')</p>
                        <p class="error capital">@lang('1 capital letter minimum')</p>
                        <p class="error number">@lang('1 number minimum')</p>
                        <p class="error special">@lang('1 special character minimum')</p>
                        <p class="error minimum">@lang('6 character password')</p>
                    </div>
                @endif
            </div>
            <div class="form-group password-field">
                <label class="form--label sr-only" for="reset_password_confirm">@lang('Confirm Password')</label>
                <input type="password" id="reset_password_confirm" class="form-control form--control" name="password_confirmation" required placeholder="@lang('Confirm Password')" autocomplete="new-password">
                <span class="password-toggle" onclick="togglePassword('reset_password_confirm')">@lang('Show')</span>
            </div>
            <div class="form-group">
                <button type="submit" class="auth-btn">@lang('Submit')</button>
            </div>
        </form>
    </div>
</div>
@endsection

