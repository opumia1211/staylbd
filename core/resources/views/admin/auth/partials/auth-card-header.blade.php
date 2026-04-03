@php
    $adminLoginLogo = getLogo('logo_dark') ?: getLogo('logo');
    $adminSiteName = $general->site_name ?? gs('site_name');
@endphp
<div class="login-wrapper__top login-wrapper__top--clean">
    <div class="login-header-inner">
        @if($adminLoginLogo)
            <a href="{{ url('/') }}" class="admin-login-logo-link">
                <img src="{{ $adminLoginLogo }}" alt="{{ __($adminSiteName) }}" class="admin-login-logo">
            </a>
        @endif
        @if($adminSiteName)
            <h3 class="login-site-name"><strong>{{ __($adminSiteName) }}</strong></h3>
        @endif
        <p class="admin-login-subtitle">{{ $subtitle ?? __('Admin Panel Login') }}</p>
    </div>
</div>
