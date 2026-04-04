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
            <h5 class="title mb-0">@lang('Too many attempts')</h5>
            <p class="mb-2 fs--14px mt-1">@lang('For your security, please wait before trying again.')</p>
            <p class="mb-0 small text-muted">@lang('You can try again in')</p>
            <div class="password-reset-countdown mt-2 mb-3" id="passwordResetCountdown" role="timer" aria-live="polite">
                <span class="countdown-display fw-bold fs-4 text--base" id="countdownDisplay">--:--</span>
            </div>
            <p class="mb-0 small text-muted" id="countdownLabel">@lang('minutes : seconds')</p>
            <div class="mt-3 d-none" id="tryAgainWrap">
                <a href="{{ route('user.password.request') }}" class="auth-btn d-inline-block text-center text-decoration-none">@lang('Try again')</a>
            </div>
        </div>
    </div>
</div>
<script>
(function() {
    var unlockAt = {{ (int) $unlockAt }};
    var display = document.getElementById('countdownDisplay');
    var label = document.getElementById('countdownLabel');
    var tryAgainWrap = document.getElementById('tryAgainWrap');

    function update() {
        var now = Math.floor(Date.now() / 1000);
        var left = Math.max(0, unlockAt - now);
        if (left <= 0) {
            display.textContent = '00:00';
            if (label) label.classList.add('d-none');
            if (tryAgainWrap) tryAgainWrap.classList.remove('d-none');
            return;
        }
        var m = Math.floor(left / 60);
        var s = left % 60;
        display.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
        if (tryAgainWrap) tryAgainWrap.classList.add('d-none');
    }
    update();
    var t = setInterval(update, 1000);
})();
</script>
@endsection
