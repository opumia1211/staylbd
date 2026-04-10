@php
    $assetVersion = app()->environment('local') ? time() : ($assetVersion ?? (config('app.asset_version') ?? '1'));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? __('Login') }} - {{ gs('site_name') ?? config('app.name') }}</title>
    @include('partials.seo')
    @php
        $storefrontCssHref = storefront_compiled_stylesheet_url('tailwind-storefront');
        $storefrontDeferredHref = storefront_compiled_stylesheet_url('tailwind-storefront-deferred');
    @endphp
    @include('partials.inter-font-preload', ['assetVersion' => $assetVersion])
    
{{-- inline style moved to critical-storefront.css --}}

    <link rel="preload" href="{{ $storefrontCssHref }}" as="style" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ $storefrontCssHref }}" crossorigin="anonymous">
    <link rel="preload" href="{{ $storefrontDeferredHref }}" as="style" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ $storefrontDeferredHref }}" media="print" onload="this.media='all'" crossorigin="anonymous">
    <noscript><link rel="stylesheet" href="{{ $storefrontDeferredHref }}" crossorigin="anonymous"></noscript>
</head>
<body class="floating-auth-page">
    <script>try{if(window.self!==window.top){document.documentElement.classList.add('st-auth-iframe');document.body.classList.add('st-auth-iframe');}}catch(e){}</script>
    @yield('content')
    <script>
        window.authLoginUrl = @json(route('user.login'));
        window.authRegisterUrl = @json(route('user.register'));
        window.checkUserUrl = @json(route('user.checkUser'));
        window.csrfToken = @json(csrf_token());
    </script>
    <script>
        (function () {
            var closeToParentLock = false;
            function closeAuthIframe(e) {
                if (window.self === window.top) return;
                var btn = e.target && e.target.closest && e.target.closest('.auth-close');
                if (!btn) return;
                if (e.type === 'pointerdown' || e.type === 'touchstart') {
                    e.preventDefault();
                } else if (e.type === 'click') {
                    e.preventDefault();
                }
                if (closeToParentLock) return;
                closeToParentLock = true;
                try {
                    window.parent.postMessage('close-auth-overlay', '*');
                } catch (err) {}
                window.setTimeout(function () { closeToParentLock = false; }, 400);
            }
            document.addEventListener('pointerdown', closeAuthIframe, { capture: true, passive: false });
            document.addEventListener('click', closeAuthIframe, true);
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
            // From iframe: login ↔ register toggle stays in-frame (switch-auth). Other auth shell links update parent URL + iframe src.
            document.addEventListener('click', function (e) {
                if (window.self === window.top) return;
                var a = e.target.closest('a[href]');
                if (!a || a.classList.contains('switch-auth')) return;
                var href = a.getAttribute('href');
                if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0) return;
                var u;
                try { u = new URL(href, window.location.origin); } catch (err) { return; }
                if (u.origin !== window.location.origin) return;
                var p = u.pathname.replace(/\/+$/, '') || '/';
                var shell = /\/user\/login$/.test(p) || /\/user\/register$/.test(p)
                    || /\/user\/password\/reset$/.test(p) || /\/user\/password\/code-verify$/.test(p)
                    || /\/user\/password\/reset\/.+/.test(p);
                if (!shell) return;
                e.preventDefault();
                if (e.stopImmediatePropagation) e.stopImmediatePropagation();
                e.stopPropagation();
                try {
                    window.parent.postMessage({ type: 'st-auth-nav', url: u.pathname + u.search + u.hash }, '*');
                } catch (err) {}
            }, true);
        })();
    </script>
    <script src="{{ url('serve-js/auth') }}?v={{ $assetVersion ?? time() }}" defer></script>
    @stack('script')
</body>
</html>
