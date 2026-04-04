@extends($activeTemplate . 'layouts.auth_modal')
@section('content')
@include($activeTemplate . 'user.auth.partials.auth_page_styles')
<div class="auth-overlay" id="authModalOverlay">
    <div class="auth-card" style="position:relative;">
        @php $authLogo = getLogo('logo'); $siteName = gs('site_name'); @endphp
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
            <h5 class="title mb-0">@lang('Account Recovery')</h5>
            <p class="mb-0 fs--14px mt-1">@lang('Enter the email or username and the phone number you used when you registered. Both must match to recover your account.')</p>
        </div>
        <form method="POST" action="{{ route('user.password.email') }}" class="auth-form-placeholder verify-gcaptcha" autocomplete="off">
            @csrf
            <div class="form-group">
                <label class="form--label sr-only" for="email_or_username">@lang('Email or Username')</label>
                <input type="text" id="email_or_username" class="form-control form--control" name="email_or_username" value="{{ old('email_or_username') }}" required autocomplete="username" placeholder="@lang('Email or Username')">
            </div>
            <div class="form-group">
                <label class="form--label sr-only" for="phone">@lang('Phone number')</label>
                <input type="text" id="phone" class="form-control form--control" name="phone" value="{{ old('phone') }}" required autocomplete="tel" placeholder="@lang('Phone number (as registered)')">
            </div>
            <x-captcha />
            <div class="form-group">
                <button type="submit" class="auth-btn">@lang('Submit')</button>
            </div>
            <p class="mb-0 small"><a href="{{ route('user.login') }}" class="text--base">@lang('Back to Login')</a></p>
        </form>
    </div>
</div>
@endsection

