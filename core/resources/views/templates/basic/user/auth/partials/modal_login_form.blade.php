@php
    $loginContent = getLoginContent();
    if (!$loginContent || !isset($loginContent->data_values)) {
        $loginContent = (object)['data_values' => (object)['heading' => __('Login'), 'subheading' => '']];
    }
    $credentialLabel = getLoginFieldLabel();
    $showCaptcha = isLoginCaptchaEnabled() || !empty($showLoginCaptchaDueToAttempts);
    $socialProvidersList = [
        'google'   => ['label' => 'Google'],
        'facebook' => ['label' => 'Facebook'],
        'twitter'  => ['label' => 'Twitter (X)'],
        'apple'    => ['label' => 'Apple'],
        'github'   => ['label' => 'GitHub'],
    ];
    $socialButtons = [];
    foreach ($socialProvidersList as $provider => $info) {
        if (isSocialLoginButtonEnabled($provider)) {
            $socialButtons[$provider] = $info;
        }
    }
    $loginRedirect = session('url.intended');
    if (!$loginRedirect || $loginRedirect === route('user.login') || !\Illuminate\Support\Str::startsWith($loginRedirect, url('/'))) {
        $loginRedirect = route('home');
    }
@endphp
{{-- Order: 1. Header 2. Credential 3. Password 4. Captcha 5. Remember 6. Submit 7. Social 8. Register link --}}
<div class="account-header account-header--compact">
    <h5 class="title mb-0">{{ __(@$loginContent->data_values->heading) }}</h5>
    @php $loginSub = trim((string)@$loginContent->data_values->subheading ?? ''); @endphp
    @if($loginSub !== '' && stripos($loginSub, 'Lorem ipsum') === false)
        <p class="mb-0 fs--14px mt-1">{{ __($loginSub) }}</p>
    @endif
</div>
@if(!empty($loginLockoutUntil))
    <div class="login-lockout-countdown" id="pageLoginLockoutCountdown" data-unlock-at="{{ $loginLockoutUntil }}">
        <span class="login-lockout-label">@lang('Too many attempts. Try again in')</span>
        <span class="login-lockout-timer" id="pageLoginLockoutTimer">--:--</span>
    </div>
@endif
<form method="POST" action="{{ route('user.login') }}" class="auth-form-placeholder {{ $showCaptcha ? 'verify-gcaptcha' : '' }}" id="pageLoginForm" autocomplete="off">
    @csrf
    <input type="hidden" name="redirect" value="{{ $loginRedirect }}">
    <div class="form-group">
        <label for="modal_login_username" class="form--label sr-only">{{ $credentialLabel }}</label>
        <input type="text" id="modal_login_username" name="username" value="{{ old('username') }}" class="form-control form--control {{ $errors->has('username') ? 'is-invalid' : '' }}" placeholder="{{ $credentialLabel }}" required autocomplete="off" autocapitalize="none">
        @if($errors->has('username'))
            <div class="invalid-feedback d-block" role="alert">{{ $errors->first('username') }}</div>
        @endif
    </div>
    <div class="form-group password-field">
        <label for="modal_login_password" class="form--label sr-only">@lang('Password')</label>
        <div class="password-input-wrap">
            <input id="modal_login_password" type="password" class="form-control form--control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password" placeholder="@lang('Password')" required autocomplete="new-password">
            <button type="button" class="password-toggle" onclick="togglePassword('modal_login_password'); this.querySelector('.pwd-icon-show').classList.toggle('d-none'); this.querySelector('.pwd-icon-hide').classList.toggle('d-none');" title="@lang('Show password')" aria-label="@lang('Show password')">
                <span class="pwd-icon-show" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
                <span class="pwd-icon-hide d-none" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg></span>
            </button>
        </div>
        <a class="text--base text-decoration-underline forgot-pass mt-1 small d-inline-block" href="{{ route('user.password.request') }}">@lang('Forgot your password?')</a>
        @if($errors->has('password'))
            <div class="invalid-feedback d-block" role="alert">{{ $errors->first('password') }}</div>
        @endif
    </div>
    @if($showCaptcha)
        <div class="form-group">
            <x-captcha/>
        </div>
    @endif
    <div class="form-group form-check">
        <input class="form-check-input" type="checkbox" name="remember" id="modal_remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="modal_remember">@lang('Remember Me')</label>
    </div>
    <div class="form-group">
        <button type="submit" class="auth-btn" @if(!empty($loginLockoutUntil)) disabled @endif>@lang('Login')</button>
    </div>
    @if(count($socialButtons) > 0)
    <div class="form-group social-login-buttons">
        <div class="social-login-grid">
            @foreach($socialButtons as $provider => $info)
            <a href="{{ route('user.social.login', ['provider' => $provider]) }}?popup=1" class="btn btn--social-login js-social-login" data-provider="{{ $provider }}" data-base="{{ route('user.social.login', ['provider' => $provider]) }}?popup=1" title="{{ $info['label'] }}">
                @include($activeTemplate . 'user.auth.partials.social_icon', ['provider' => $provider])
                <span class="btn--social-text">{{ $info['label'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif
    <p class="mb-0 small mt-1">@lang('Don\'t have any account?') <a href="{{ route('user.register') }}" class="switch-auth">@lang('Registration')</a></p>
</form>
