@php
    $assetVersion = app()->environment('local') ? time() : ($assetVersion ?? (config('app.asset_version') ?? '1'));
    // Respect page-level overrides (e.g. PDP needs jQuery + Bootstrap for interactions).
    $disableLegacyJquery = $disableLegacyJquery ?? true;
    $disableLegacyBootstrapBundle = $disableLegacyBootstrapBundle ?? true;
    $disableLegacyJqueryUi = $disableLegacyJqueryUi ?? true;
    $disableLegacyVisualLibs = $disableLegacyVisualLibs ?? true;
    $disableLegacyLightbox = $disableLegacyLightbox ?? true;
    $disableLegacyWow = $disableLegacyWow ?? true;
    $disableLegacyCarouselJs = $disableLegacyCarouselJs ?? true;
    $disableLegacyOwl = $disableLegacyOwl ?? true;
@endphp
<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    {{-- Bottom-nav নতুন ট্যাব: mb=1 শুধু একবার সিগনাল; ঠিকানা বারে mb দেখাব না; একই ট্যাবে পরের পেজে শেল ধরে রাখতে sessionStorage --}}
    <script>
    (function () {
        try {
            var params = new URLSearchParams(window.location.search);
            if (params.get('mb') === '1') {
                sessionStorage.setItem('stayl_mobile_tab_shell', '1');
                params.delete('mb');
                var qs = params.toString();
                var path = window.location.pathname;
                if (/\/index\.php$/i.test(path)) {
                    path = path.replace(/\/index\.php$/i, '/');
                    if (path === '') path = '/';
                }
                var next = path + (qs ? '?' + qs : '') + window.location.hash;
                window.history.replaceState(null, '', next);
            }
            if (sessionStorage.getItem('stayl_mobile_tab_shell') === '1') {
                document.documentElement.classList.add('mobile-tab-shell');
            }
        } catch (e) {}
    })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    {{-- Short HTML cache speeds repeat views; assets use ?v= + long Cache-Control on serve-css --}}
    <meta http-equiv="Cache-Control" content="private, max-age=120, must-revalidate">
    {{-- Critical CSS: Inter subset + icons + core template + Tailwind. Deferred legacy CSS loads async below. asset() respects ASSET_URL (CDN). --}}
    @php
        $storefrontCssBundle = $storefrontCssBundle ?? 'tailwind-product';
        $storefrontCssHref = storefront_compiled_stylesheet_url($storefrontCssBundle);
        $storefrontDeferredBundle = $storefrontDeferredBundle ?? 'tailwind-storefront-deferred';
        $storefrontDeferredHref = storefront_compiled_stylesheet_url($storefrontDeferredBundle);
    @endphp
    @include('partials.inter-font-preload', ['assetVersion' => $assetVersion])
    <link rel="preload" href="{{ $storefrontCssHref }}" as="style" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ $storefrontCssHref }}" crossorigin="anonymous">
    {{-- Non-critical CSS: carousels, lightbox, account/dashboard, compare, etc. — async to improve LCP --}}
    <link rel="preload" href="{{ $storefrontDeferredHref }}" as="style" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ $storefrontDeferredHref }}" media="print" onload="this.media='all'" crossorigin="anonymous">
    <noscript><link rel="stylesheet" href="{{ $storefrontDeferredHref }}" crossorigin="anonymous"></noscript>
    @stack('head-meta')
    <title>{{ $general->siteName(__($pageTitle ?? 'Home')) }}</title>
    @include('partials.seo')

    <!-- Favicon from admin settings -->
    @php $favicon = getLogo('favicon'); @endphp
    @if($favicon)
        <link rel="icon" sizes="32x32" href="{{ $favicon }}">
        <link rel="icon" sizes="64x64" href="{{ $favicon }}">
        <link rel="icon" sizes="180x180" href="{{ $favicon }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ $favicon }}">
        <link rel="shortcut icon" href="{{ $favicon }}">
    @else
        @php $mainLogo = getLogo('logo'); @endphp
        @if($mainLogo)
            <link rel="icon" sizes="32x32" href="{{ $mainLogo }}">
            <link rel="apple-touch-icon" sizes="180x180" href="{{ $mainLogo }}">
            <link rel="shortcut icon" href="{{ $mainLogo }}">
        @endif
    @endif

    {{-- Early critical CSS: resources/css/critical-storefront-head.css (appended to tailwind-homepage / tailwind-product at build). Admin UI vars below. --}}
    @include('partials.storefront_ui_variables')

      {{-- tailwind-homepage (/) or tailwind-product (most routes): Inter + icons + template + Tailwind. Stack only page-specific overrides. --}}

    @stack('style-lib')

    @stack('style')

    <link rel="stylesheet" href="{{ storefront_compiled_stylesheet_url('critical-storefront') }}" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    @include('partials.tracking_scripts')
</head>

<body class="antialiased" style="font-family: 'Outfit', sans-serif; padding-top: 155px !important;" @if(optional($uiSettings)->theme_template && optional($uiSettings)->theme_template !== 'default') data-theme="{{ $uiSettings->theme_template }}" @endif @stack('body_attrs')>
    <!-- Preloader removed for instant page loads -->
    @yield('app')
    @include($activeTemplate . 'partials.mobile_bottom_nav')
    @guest
        @include($activeTemplate . 'partials.guest_account_modal')
    @endguest

    <div class="overlay"></div>
    @include($activeTemplate . 'partials.custom_site_messages')
    @include($activeTemplate . 'partials.cookie_banner')

    <button type="button" class="scrollToTop">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'scroll_top_icon', 'fallback' => 'angle-double-up', 'width' => 22, 'height' => 22, 'alt' => ''])</button>
    <!-- jQuery + Bootstrap (required for Quick View modal, guest checkout modal) -->
    @if(empty($disableLegacyJquery))
    <script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}?v={{ $assetVersion }}"></script>
    @endif
    @if(empty($disableLegacyBootstrapBundle))
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}?v={{ $assetVersion }}"></script>
    @endif
    {{-- CSRF for all AJAX (jQuery + fetch) – required for Laravel POST/PUT/DELETE --}}
    <script>
    (function(){
        var token=document.querySelector('meta[name="csrf-token"]'); if(!token||!token.getAttribute('content')) return;
        var csrf=token.getAttribute('content');
        if(window.jQuery){ window.jQuery.ajaxSetup({ headers: {'X-CSRF-TOKEN':csrf} }); }
        var origFetch=window.fetch; if(typeof origFetch==='function'){ window.fetch=function(url,opts){ opts=opts||{}; var h=opts.headers; if(!(h instanceof Headers)){ h=new Headers(h||{}); } h.set('X-CSRF-TOKEN',csrf); h.set('X-Requested-With','XMLHttpRequest'); h.set('Accept','application/json'); opts.headers=h; return origFetch(url,opts); }; }
    })();
    </script>
    @if(empty($disableLegacyOwl) && request()->routeIs('home'))
    {{-- Homepage: banner is CSS+vanilla JS; defer Owl until after load (other pages need it in head order) --}}
    <script>
    window.addEventListener('load', function() {
      var s = document.createElement('script');
      s.src = '{{ asset($activeTemplateTrue . 'js/owl.min.js') }}?v={{ $assetVersion }}';
      s.async = true;
      document.body.appendChild(s);
    }, { once: true });
    </script>
    @elseif(empty($disableLegacyOwl))
    <script src="{{ asset($activeTemplateTrue . 'js/owl.min.js') }}?v={{ $assetVersion }}"></script>
    @endif
    @php
        try {
            $staylLucideJs = mix('js/storefront-lucide.js');
        } catch (\Throwable $e) {
            $staylLucideJs = asset('js/storefront-lucide.js');
        }
    @endphp
    <script src="{{ $staylLucideJs }}?v={{ $assetVersion }}" defer></script>
    {{-- Fly To Header & Product Carousel – defer for fast TTI --}}
    <script src="{{ url('serve-js/fly-to-header') }}?v={{ $assetVersion }}" defer></script>
    <script src="{{ url('serve-js/glass-header') }}?v={{ $assetVersion }}" defer></script>
    @if(empty($disableLegacyJqueryUi))
    <script src="{{ asset($activeTemplateTrue . 'js/jquery-ui.min.js') }}?v={{ $assetVersion }}" defer></script>
    @endif
    <script src="{{ asset($activeTemplateTrue . 'js/rafcounter.min.js') }}?v={{ $assetVersion }}" defer></script>
    @if(empty($disableLegacyLightbox))
    <script src="{{ asset($activeTemplateTrue . 'js/lightbox.min.js') }}?v={{ $assetVersion }}" defer></script>
    @endif
    @if(empty($disableLegacyWow))
    <script src="{{ asset($activeTemplateTrue . 'js/wow.min.js') }}?v={{ $assetVersion }}" defer></script>
    @endif
    @if(empty($disableLegacyCarouselJs))
    <script src="{{ url('serve-js/product-carousel') }}?v={{ $assetVersion }}" defer></script>
    @endif
    {{-- WOW: delay 600ms after load so first paint is stable – avoids reflow that breaks product card CSS --}}
    @if(empty($disableLegacyWow))
    <script>
    (function(){
        if (window.__wowInitialized) return;
        function initWOW() {
            if (window.__wowInitialized || typeof window.WOW === 'undefined') return;
            window.__wowInitialized = true;
            try { new window.WOW({ offset: 24, mobile: true, live: true }).init(); } catch (e) {}
        }
        function run() {
            if (document.readyState === 'complete') setTimeout(initWOW, 600);
            else window.addEventListener('load', function() { setTimeout(initWOW, 600); }, { once: true });
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run, { once: true });
        else run();
    })();
    </script>
    @endif

    <!-- Single init: prevent double run / reload; remove preloader once -->
    <script>
        (function() {
            if (window.__staylbdPageReady) return;
            window.__staylbdPageReady = true;
            function ready() {
                var preloader = document.querySelector('.preloader');
                if (preloader) preloader.style.display = 'none';
                document.body.classList.remove('overflow-hidden');
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', ready, { once: true });
            } else {
                ready();
            }
        })();
    </script>

    @stack('script-lib')
    @stack('script')

    {{-- Tawk.to: never load on localhost/127.0.0.1 to avoid CORS (embed.tawk.to blocks localhost) --}}
    @php
        $tawkHttpHost = request()->getHttpHost();
        $tawkSkip = app()->environment('local')
            || stripos($tawkHttpHost, 'localhost') !== false
            || stripos($tawkHttpHost, '127.0.0.1') !== false
            || str_contains(request()->url(), 'localhost')
            || str_contains(request()->url(), '127.0.0.1');
    @endphp
    @if(!$tawkSkip)
        @include('partials.tawk')
    @endif

    {{-- Product card: cycle gallery images on hover --}}
    <script>
    (function() {
        var CYCLE_INTERVAL = 1100;
        var initializedCards = new WeakSet();

        function bindCycle(card) {
            if (!card || initializedCards.has(card)) return;
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

            var galleryStr = card.getAttribute('data-gallery');
            if (!galleryStr) return;

            var urls = [];
            try { urls = JSON.parse(galleryStr); } catch (e) { return; }
            if (!Array.isArray(urls) || urls.length < 2) return;

            var img = card.querySelector('.product-card-glass__cycle-img') || card.querySelector('.product-card__img--cycle');
            if (!img) return;

            initializedCards.add(card);
            var idx = 0;
            var tid = null;

            function showNext() {
                idx = (idx + 1) % urls.length;
                img.src = urls[idx];
                img.setAttribute('data-cycle-index', String(idx));
            }

            card.addEventListener('mouseenter', function() {
                if (tid) clearInterval(tid);
                idx = 0;
                tid = setInterval(showNext, CYCLE_INTERVAL);
            }, { passive: true });

            card.addEventListener('mouseleave', function() {
                if (tid) { clearInterval(tid); tid = null; }
                img.src = urls[0];
                img.setAttribute('data-cycle-index', '0');
            }, { passive: true });
        }

        function initProductCardGalleryCycle() {
            var cards = document.querySelectorAll('.product-card[data-gallery]');
            if (!cards.length) return;

            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (!entry.isIntersecting) return;
                        bindCycle(entry.target);
                        io.unobserve(entry.target);
                    });
                }, { rootMargin: '220px 0px' });
                cards.forEach(function(card) { io.observe(card); });
            } else {
                cards.forEach(bindCycle);
            }
        }

        window.initProductCardGalleryCycle = initProductCardGalleryCycle;
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                if ('requestIdleCallback' in window) {
                    requestIdleCallback(initProductCardGalleryCycle, { timeout: 500 });
                } else {
                    setTimeout(initProductCardGalleryCycle, 0);
                }
            }, { once: true });
        } else {
            initProductCardGalleryCycle();
        }
    })();
    </script>

    @include('partials.plugins')
    @include('partials.notify')

    <script>
        (function () {
            "use strict";

            document.querySelectorAll(".langSel").forEach(function (el) {
                el.addEventListener("change", function () {
                    window.location.href = "{{ route('home') }}/change/" + (el.value || "");
                });
            });

            function hideCookieBanner() {
                document.querySelectorAll('.gdpr-cookie-banner').forEach(function (b) {
                    b.classList.add('d-none');
                });
            }

            function callCookieRoute(url) {
                fetch(url, { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .finally(hideCookieBanner);
            }

            document.addEventListener('click', function (e) {
                if (e.target.closest('.gdpr-cookie-allow')) {
                    e.preventDefault();
                    callCookieRoute('{{ route('cookie.accept') }}');
                }
                if (e.target.closest('.gdpr-cookie-decline')) {
                    e.preventDefault();
                    callCookieRoute('{{ route('cookie.decline') }}');
                }
            });

            document.querySelectorAll('.gdpr-cookie-banner').forEach(function (banner) {
                var delay = parseInt(banner.getAttribute('data-delay') || '2000', 10) || 2000;
                setTimeout(function () { banner.classList.remove('hide'); }, delay);
            });

            document.querySelectorAll('input, select, textarea').forEach(function (element) {
                if (element.getAttribute('type') !== 'checkbox' && element.hasAttribute('required')) {
                    var formGroup = element.closest('.form-group');
                    if (!formGroup) return;
                    var label = formGroup.querySelector('label');
                    if (label) label.classList.add('required');
                }
            });

            document.querySelectorAll('table').forEach(function (table) {
                var headings = table.querySelectorAll('thead tr th');
                table.querySelectorAll('tbody tr').forEach(function (row) {
                    row.querySelectorAll('td').forEach(function (column, i) {
                        if (column.colSpan === 100) return;
                        if (headings[i]) column.setAttribute('data-label', headings[i].innerText);
                    });
                });
            });
        })();
    </script>
    <script>
        (function() {
            function addPulseBadgeEffect() {
                var selector = '.show-cart-count, .show-wishlist-count';
                document.querySelectorAll(selector).forEach(function(el) {
                    if (el.dataset.pulseBound === '1') return;
                    el.dataset.pulseBound = '1';
                    var prev = (el.textContent || '').trim();
                    var mo = new MutationObserver(function() {
                        var next = (el.textContent || '').trim();
                        if (next === prev) return;
                        prev = next;
                        el.classList.remove('is-pulsing');
                        void el.offsetWidth;
                        el.classList.add('is-pulsing');
                        setTimeout(function() { el.classList.remove('is-pulsing'); }, 380);
                    });
                    mo.observe(el, { childList: true, characterData: true, subtree: true });
                });
            }
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', addPulseBadgeEffect);
            else addPulseBadgeEffect();
        })();
    </script>
    @guest
    <script>
        (function() {
            function ensureStaylModal() {
                if (window.StaylModal) return window.StaylModal;
                function staylResolveModal(input) {
                    if (!input) return null;
                    if (typeof input === 'string') {
                        return document.getElementById(String(input).replace(/^#/, ''));
                    }
                    return input;
                }
                function show(modalEl) {
                    modalEl = staylResolveModal(modalEl);
                    if (!modalEl) return;
                    modalEl.classList.add('is-open', 'show');
                    modalEl.style.display = 'block';
                    modalEl.style.opacity = '1';
                    modalEl.style.visibility = 'visible';
                    modalEl.style.pointerEvents = 'auto';
                    modalEl.style.zIndex = '100050';
                    modalEl.removeAttribute('aria-hidden');
                    document.body.classList.add('modal-open');
                    try { modalEl.dispatchEvent(new CustomEvent('stayl:modal:shown')); } catch (e) {}
                }
                function hide(modalEl) {
                    modalEl = staylResolveModal(modalEl);
                    if (!modalEl) return;
                    modalEl.classList.remove('is-open', 'show');
                    modalEl.style.display = 'none';
                    modalEl.style.opacity = '';
                    modalEl.style.visibility = '';
                    modalEl.style.pointerEvents = '';
                    modalEl.style.zIndex = '';
                    modalEl.setAttribute('aria-hidden', 'true');
                    if (!document.querySelector('.modal.is-open')) document.body.classList.remove('modal-open');
                    try { modalEl.dispatchEvent(new CustomEvent('stayl:modal:hidden')); } catch (e) {}
                }
                window.StaylModal = { show: show, hide: hide };
                return window.StaylModal;
            }
            var modalApi = ensureStaylModal();
            document.addEventListener('click', function(e) {
                var closeBtn = e.target.closest('[data-stayl-close="guest-account"], [data-bs-dismiss="modal"]');
                if (!closeBtn) return;
                var guestAccountModal = document.getElementById('guestAccountModal');
                if (guestAccountModal) {
                    e.preventDefault();
                    modalApi.hide(guestAccountModal);
                }
            }, true);
            document.addEventListener('click', function(e) {
                var guestAccountModal = document.getElementById('guestAccountModal');
                if (!guestAccountModal || !guestAccountModal.classList.contains('is-open')) return;
                if (e.target === guestAccountModal) {
                    e.preventDefault();
                    modalApi.hide(guestAccountModal);
                }
            }, true);
            document.addEventListener('keydown', function(e) {
                if (e.key !== 'Escape') return;
                var guestAccountModal = document.getElementById('guestAccountModal');
                if (guestAccountModal && guestAccountModal.classList.contains('is-open')) {
                    modalApi.hide(guestAccountModal);
                }
            });
            // Handled globally by auth_iframe_overlay_script.blade.php
            // document.addEventListener('click', function(e) { ... });
        })();
    </script>
    @endguest
    <script>
        (function () {
            try {
                var accountBtn = document.getElementById('mobile-account-btn');
                if (!accountBtn || accountBtn.tagName !== 'BUTTON' || accountBtn.dataset.boundClick === '1') return;
                accountBtn.dataset.boundClick = '1';
                function openGuestAccountModal(e) {
                    try {
                        var accountUrl = accountBtn.getAttribute('data-account-url');
                        if (accountUrl) return;
                        var modalId = accountBtn.getAttribute('data-account-modal') || 'guestAccountModal';
                        var modalEl = document.getElementById(modalId);
                        if (!modalEl) return;
                        if (e) e.preventDefault();
                        if (window.StaylModal && typeof window.StaylModal.show === 'function') {
                            window.StaylModal.show(modalId);
                            return;
                        }
                        modalEl.classList.add('is-open', 'show');
                        modalEl.style.display = 'block';
                        modalEl.style.opacity = '1';
                        modalEl.style.visibility = 'visible';
                        modalEl.style.pointerEvents = 'auto';
                        modalEl.style.zIndex = '100050';
                    } catch (err) {
                        console.error('mobile account click failed', err);
                    }
                }
                accountBtn.addEventListener('pointerdown', openGuestAccountModal, { passive: false });
                accountBtn.addEventListener('touchend', openGuestAccountModal, { passive: false });
                accountBtn.addEventListener('click', openGuestAccountModal, { passive: false });
            } catch (e) {
                console.error('mobile account bootstrap failed', e);
            }
        })();
    </script>
    @include('partials.storefront_echo')
</body>

</html>