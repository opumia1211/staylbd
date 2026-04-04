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
            <h5 class="title mb-0">@lang('Verify Email Address')</h5>
            <p class="mb-0 fs--14px mt-1">@lang('A 6 digit verification code sent to your email address') : {{ showEmailAddress($email) }}</p>
        </div>
        <form action="{{ route('user.password.verify.code') }}" method="POST" class="auth-form-placeholder submit-form" autocomplete="off">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <div class="form-group">
                <label class="form--label" for="reset_code">@lang('Verification Code')</label>
                <input type="text" id="reset_code" name="code" class="form-control form--control" required autocomplete="one-time-code" placeholder="@lang('Enter 6 digit code')">
            </div>
            <div class="form-group">
                <button type="submit" class="auth-btn">@lang('Submit')</button>
            </div>
            <p class="mb-0 small">
                @lang('Didn\\'t receive the code?')
                <a href="{{ route('user.password.request') }}" class="text--base text-decoration-underline">@lang('Try again')</a>
            </p>
        </form>
    </div>
</div>
@endsection

