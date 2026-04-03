{{-- Minimal layout for admin login & password reset – same look, fast load, no heavy scripts --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($general ?? null) ? $general->siteName($pageTitle ?? '') : ($pageTitle ?? 'Admin') }}</title>
    @php $adminFavicon = getLogo('favicon'); @endphp
    @if($adminFavicon)
    <link rel="icon" href="{{ $adminFavicon }}">
    @else
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/default.png') }}">
    @endif
    <link rel="preconnect" href="https://rsms.me/" crossorigin>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" crossorigin>
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}?v={{ $assetVersion ?? config('app.version') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}?v={{ $assetVersion ?? config('app.version') }}">
    <link rel="stylesheet" href="{{ url('serve-css/tailwind-admin') }}?v={{ $assetVersion ?? config('app.version') }}">
    @stack('style')
    @php
        $adminLoginCssPath = public_path('assets/admin/css/professional-login.css');
        $adminLoginCssVer = (is_file($adminLoginCssPath) ? (string) filemtime($adminLoginCssPath) : null) ?: ($assetVersion ?? config('app.version'));
    @endphp
    <link rel="stylesheet" href="{{ asset('assets/admin/css/professional-login.css') }}?v={{ $adminLoginCssVer }}">
    {{-- Inline: scroll + legacy app.css bleed only; spacing comes from professional-login.css --}}
    <style id="admin-auth-critical">
    body.admin-auth-page{overflow-x:hidden;margin:0;background:#f1f5f9}
    body.admin-auth-page .login-main,body.admin-auth-page .admin-login-shell,body.admin-auth-page .login-area,body.admin-auth-page .login-wrapper,body.admin-auth-page .login-wrapper__body{
      overflow:visible!important;max-height:none!important;height:auto!important
    }
    body.admin-auth-page .login-wrapper__body::after{content:none!important;display:none!important}
    body.admin-auth-page .login-main{background:transparent!important;min-height:100vh!important;min-height:100dvh!important}
    body.admin-auth-page .login-main::before{display:none!important;content:none!important}
    body.admin-auth-page .login-area::after{display:none!important;content:none!important}
    body.admin-auth-page .login-wrapper{background:#fff!important;overflow:visible!important}
    body.admin-auth-page .login-form .form-control,body.admin-auth-page .login-wrapper__body .form-control{
      height:auto!important;background:#fff!important;color:#1f2937!important;border:1px solid rgba(0,0,0,.1)!important
    }
    body.admin-auth-page .login-wrapper__body .btn.cmn-btn,body.admin-auth-page #adminLoginBtn,body.admin-auth-page #adminPasswordResetBtn,body.admin-auth-page #adminPasswordChangeBtn,body.admin-auth-page #adminCodeVerifyBtn,body.admin-auth-page #admin2faVerifyBtn,body.admin-auth-page #admin2faSetupBtn,body.admin-auth-page #adminRecoveryContinueBtn{
      background:#0e9f90!important;color:#fff!important;border-color:transparent!important;text-transform:none!important;letter-spacing:normal!important
    }
    </style>
</head>
<body class="admin-auth-page">
@yield('content')

{{-- No jQuery/Bootstrap JS here: faster first paint; toasts use iziToast only. --}}
@include('partials.notify')
@stack('script')
</body>
</html>
