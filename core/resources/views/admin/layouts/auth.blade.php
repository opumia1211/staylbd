{{-- Minimal layout for admin login & password reset --}}
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
        $assetVersion = $assetVersion ?? config('app.asset_version') ?? config('app.version');
    @endphp

    <!-- Fonts from Official Library -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    
    <!-- CSS from Official Global Library -->
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">
    
    <!-- Clean minimal styles for Auth purely to fix #1e157d and make it ultra-lightweight -->
    <style>
        body.admin-auth-page {
            font-family: 'Outfit', sans-serif;
            background-color: #ffffff;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-main {
            width: 100%;
            display: flex;
            justify-content: center;
            background-color: #ffffff;
            padding: 20px;
        }

        .admin-login-shell {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border: 1px solid #eaeaef;
            border-radius: 12px;
            box-shadow: 0px 10px 40px rgba(0, 0, 0, 0.05); /* Soft, modern shadow */
            padding: 40px 30px;
            position: relative;
            z-index: 10;
        }

        .login-header-inner {
            text-align: center;
            margin-bottom: 30px;
        }
        .admin-login-logo {
            max-height: 40px;
            margin-bottom: 12px;
        }
        .login-site-name {
            font-size: 1.5rem;
            color: #1e293b;
            margin-bottom: 5px;
        }
        .admin-login-subtitle {
            color: #0d9488;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            font-weight: 500;
            color: #334155;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.9rem;
        }
        .form-control {
            border: 1px solid #e2e8f0;
            padding: 12px 15px;
            border-radius: 8px;
            width: 100%;
            font-family: inherit;
            color: #334155;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.1);
            outline: none;
        }

        .password-input-wrap {
            position: relative;
        }
        .password-toggle-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0;
            font-size: 1.2rem;
            transition: color 0.3s;
        }
        .password-toggle-btn:hover {
            color: #334155;
        }

        .captcha-box {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: nowrap;
        }
        .captcha-img-wrap {
            background: #0f172a;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.1rem;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 110px;
        }
        .captcha-refresh-btn {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        .captcha-refresh-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        .forgot-link {
            color: #0d9488;
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover {
            text-decoration: underline;
        }
        .form-check-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
        }

        .btn-primary {
            background-color: #0d9488; 
            color: #ffffff;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #0f766e;
        }

        /* Responsive overrides */
        @media (max-width: 575px) {
            .admin-login-shell {
                padding: 30px 20px;
                border: none;
                box-shadow: none;
            }
        }
    </style>
    @stack('style')
</head>
<body class="admin-auth-page">
@yield('content')

{{-- Official Global JS Libraries --}}
<script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}?v={{ $assetVersion }}"></script>
<script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}?v={{ $assetVersion }}"></script>

@include('partials.notify')
@stack('script')
</body>
</html>
