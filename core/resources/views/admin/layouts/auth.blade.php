{{-- Minimal layout for admin login & password reset – Inter + Tailwind admin bundle only (no duplicate global CSS links) --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($general ?? null) ? $general->siteName($pageTitle ?? '') : ($pageTitle ?? 'Admin') }}</title>
    @php $adminFavicon = getLogo('favicon'); @endphp
    @if($adminFavicon)
    <link rel="icon" href="{{ $adminFavicon }}">
    @else
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/default.png') }}">
    @endif
    @php
        $tailwindAdminPath = public_path('css/tailwind-admin.css');
        $tailwindAdminVer = (is_file($tailwindAdminPath) ? (string) filemtime($tailwindAdminPath) : null) ?: ($assetVersion ?? config('app.version'));
    @endphp

    @include('partials.inter-font-preload', ['assetVersion' => $assetVersion ?? config('app.asset_version') ?? config('app.version')])
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">
    <link rel="preload" href="{{ url('serve-css/tailwind-admin') }}?v={{ $tailwindAdminVer }}" as="style">
    <link rel="stylesheet" href="{{ url('serve-css/tailwind-admin') }}?v={{ $tailwindAdminVer }}" crossorigin="anonymous">
    @stack('style')
</head>
<body class="admin-auth-page">
@yield('content')

{{-- No jQuery/Bootstrap JS here: faster first paint; toasts use iziToast only. --}}
@include('partials.notify')
@stack('script')
</body>
</html>
