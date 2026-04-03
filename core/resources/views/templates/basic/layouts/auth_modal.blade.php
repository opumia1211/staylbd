<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? __('Login') }} - {{ gs('site_name') ?? config('app.name') }}</title>
    @include('partials.seo')
    <link rel="preconnect" href="https://rsms.me/" crossorigin>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://rsms.me/inter/inter.css"></noscript>
    <style>
        body, html {
            background: transparent !important;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Hide scrollbars within the iframe if not needed */
            font-family: Inter, system-ui, sans-serif;
        }
    </style>
    <link rel="stylesheet" href="{{ url('serve-css/tailwind-storefront') }}?v={{ $assetVersion ?? time() }}" crossorigin="anonymous">
</head>
<body class="floating-auth-page">
    @yield('content')
    <script>
        window.authLoginUrl = @json(route('user.login'));
        window.authRegisterUrl = @json(route('user.register'));
        window.checkUserUrl = @json(route('user.checkUser'));
        window.csrfToken = @json(csrf_token());
    </script>
    <script>
        (function () {
            // When inside iframe overlay, clicking .auth-close should only close overlay, not open a new page
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.auth-close');
                if (!btn) return;
                if (window.self !== window.top) {
                    e.preventDefault();
                    try {
                        window.parent.postMessage('close-auth-overlay', '*');
                    } catch (err) {}
                }
            }, true);
            // Social login: in iframe, open popup via parent so OAuth runs in top window
            document.addEventListener('click', function (e) {
                var socialBtn = e.target.closest('.js-social-login');
                if (!socialBtn) return;
                var href = socialBtn.getAttribute('data-base') || socialBtn.getAttribute('href');
                if (!href) return;
                if (window.self !== window.top && typeof window.parent.openSocialPopup === 'function') {
                    e.preventDefault();
                    window.parent.openSocialPopup(href);
                }
            }, true);
        })();
    </script>
    <script src="{{ url('serve-js/auth') }}?v={{ $assetVersion ?? time() }}" defer></script>
    @stack('script')
</body>
</html>
