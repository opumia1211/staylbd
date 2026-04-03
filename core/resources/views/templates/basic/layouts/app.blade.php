@php
    $assetVersion = app()->environment('local') ? time() : ($assetVersion ?? (config('app.asset_version') ?? '1'));
    $disableLegacyJquery = true;
    $disableLegacyBootstrapBundle = true;
    $disableLegacyJqueryUi = true;
    $disableLegacyVisualLibs = true;
    $disableLegacyLightbox = true;
    $disableLegacyWow = true;
    $disableLegacyCarouselJs = false;
    $disableLegacyOwl = true;
@endphp
<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    {{-- Short HTML cache speeds repeat views; assets use ?v= + long Cache-Control on serve-css --}}
    <meta http-equiv="Cache-Control" content="private, max-age=120, must-revalidate">
    {{-- Fonts: Inter only (rsms.me). Blocking stylesheet + preload so text renders with intended face immediately; Tailwind = serve-css/tailwind-storefront only. --}}
    <link rel="preconnect" href="https://rsms.me/" crossorigin>
    <link rel="preload" href="https://rsms.me/inter/inter.css" as="style" crossorigin>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" crossorigin>
    {{-- Footer social: FA + Line Awesome for admin-picked classes; Inter + Tailwind = serve-css/tailwind-storefront only. --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="preload" href="{{ url('serve-css/tailwind-storefront') }}?v={{ $assetVersion }}" as="style">
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

    <!-- Instant load - hide preloader; versioned footer bg; UI from admin ui_settings -->
    <style>
        .preloader{display:none!important;visibility:hidden!important;opacity:0!important}
        body.overflow-hidden{overflow-x:hidden!important;overflow-y:auto!important}
        body{font-family:Inter,ui-sans-serif,system-ui,sans-serif;overflow-x:hidden;max-width:100%;font-size:clamp(.875rem,1vw,1.125rem);-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
        html{font-size:16px;overflow-x:hidden;max-width:100%;scroll-behavior:smooth;-webkit-text-size-adjust:100%;text-size-adjust:100%}
        @media (max-width: 991.98px){
            main{padding-bottom:80px!important}
            /* main.css .overlay is also z-index 9999 and comes after the nav in DOM — steals taps on small screens */
            .overlay:not(.active){pointer-events:none!important}
        }
        /*
         * Unified public-page shell:
         * - Desktop/ultrawide: fixed professional max width with light side gaps
         * - Tablet/mobile: near full-bleed with very small safe gutters
         */
        :root{
            --fluid-space:clamp(8px,1.1vw,24px);
            --stayl-pad-x:clamp(10px,1.2vw,20px);
            --stayl-content-max:min(1920px,calc(100vw - 2 * var(--stayl-pad-x)));
            --stayl-shell-pad-x:var(--stayl-pad-x);
        }
        @media (max-width: 991.98px){
            :root{
                --stayl-pad-x:clamp(2px,0.75vw,6px);
                --stayl-content-max:calc(100vw - 2 * var(--stayl-pad-x));
            }
        }
        @media (max-width: 575.98px){
            :root{
                --stayl-pad-x:clamp(1px,0.5vw,4px);
                --stayl-content-max:calc(100vw - 2 * var(--stayl-pad-x));
            }
        }
        .main-container{width:100%;min-width:0;max-width:var(--stayl-content-max);margin-left:auto;margin-right:auto;padding-left:calc(var(--stayl-pad-x) + env(safe-area-inset-left,0px));padding-right:calc(var(--stayl-pad-x) + env(safe-area-inset-right,0px));box-sizing:border-box}
        .glass-header__shell{width:100%;min-width:0;max-width:var(--stayl-content-max);margin-left:auto;margin-right:auto;padding-left:calc(var(--stayl-pad-x) + env(safe-area-inset-left,0px));padding-right:calc(var(--stayl-pad-x) + env(safe-area-inset-right,0px));box-sizing:border-box}
        .glass-header__max{width:100%;max-width:100%;min-width:0;margin-left:auto;margin-right:auto;box-sizing:border-box}
        .main-container svg,.main-container i{max-width:44px;max-height:44px}
        main{min-width:0;max-width:none;width:100%}
        main [class*="col-"]{min-width:0}
        .main-container img,.main-container video{max-width:100%;height:auto}
        .glass-header-wrapper,.glass-header-right,.glass-header-nav{flex-wrap:nowrap!important}
        .glass-header-right{gap:clamp(4px,0.8vw,10px)!important}
        .glass-search-result-item{transition:background-color .12s ease,border-color .12s ease}
        .glass-search-result-item.glass-search-result-focused{background:rgba(15,118,110,.08)!important;border-color:rgba(15,118,110,.28)!important;outline:none}
        /* Critical above-the-fold: glass header + first product-row action stack */
        .glass-header{position:fixed;top:0;left:0;right:0;z-index:99999;display:flex;flex-direction:column;justify-content:center;align-items:stretch;height:56px;max-height:56px;padding-top:0!important;padding-bottom:0!important;box-sizing:border-box!important;background:rgba(255,255,255,.85);backdrop-filter:blur(25px) saturate(200%);-webkit-backdrop-filter:blur(25px) saturate(200%);border-bottom:1px solid rgba(255,255,255,.2);box-shadow:0 4px 24px rgba(31,38,135,.05)}
        .glass-header .glass-header__shell{display:flex;align-items:center;flex:1 1 auto;min-height:0;min-width:0;width:100%}
        .product-card .product-card__actions{position:absolute;right:8px;top:50%;transform:translateY(-50%);display:flex;flex-direction:column;gap:6px;z-index:12}
        .product-card .product-card__action{width:38px;height:38px;min-width:38px;min-height:38px;max-width:38px;max-height:38px;aspect-ratio:1/1;flex:0 0 auto;line-height:0;border-radius:9999px;background:#fff;border:1px solid rgba(0,0,0,.08);box-shadow:none;backdrop-filter:none;-webkit-backdrop-filter:none;transition:transform .1s cubic-bezier(0.4,0,0.2,1),background-color .12s ease,box-shadow .12s ease}
        .product-card .product-card__action.stayl-action-pressed{transform:scale(0.88)}
        .product-card .product-card__cta{transition:transform .1s cubic-bezier(0.4,0,0.2,1),filter .1s ease,background-color .12s ease}
        .product-card .product-card__cta.stayl-action-pressed{transform:scale(0.97)}
        .product-card .product-card__action svg.ui-icon{text-indent:0!important;overflow:visible!important;background:none!important;background-image:none!important;width:1.05rem!important;height:1.05rem!important;min-width:1.05rem!important;min-height:1.05rem!important;display:block!important;margin:0!important}
        .product-card .product-card__stars-inline{display:inline-flex;align-items:center;gap:1px;color:var(--product-rating-color,#f59e0b)}
        .product-card__img-wrap--skeleton{background:#f8fafc}
        .product-card__img-wrap--skeleton .product-card__img{display:block;background:#f8fafc}
        @media (max-width:575px){.product-card .product-card__action{width:40px;height:40px;min-width:40px;min-height:40px;max-width:40px;max-height:40px}.product-card .product-card__actions{right:6px;gap:7px}}
        /* Product card – 3 lines; নাম স্পষ্ট (ব্লার নয়), স্টক/ডেলিভারি দৃশ্যমান */
        .product-card__info{--pc-title:36px;--pc-gap:4px;--pc-price:18px;--pc-rating:20px;height:128px;min-height:128px;max-height:128px;display:flex;flex-direction:column;overflow:hidden}
        .product-card__info-inner{flex:1;min-height:0;overflow:hidden;display:grid;grid-template-rows:var(--pc-title) var(--pc-price) var(--pc-rating);row-gap:var(--pc-gap);padding:5px 6px 2px;align-content:start;align-items:start}
        .product-card__info-inner .product-card__title{height:var(--pc-title);max-height:var(--pc-title);overflow:hidden!important;display:block!important;color:#0f172a!important;-webkit-font-smoothing:antialiased}
        .product-card__title,.product-card__title a{font-size:clamp(.75rem,.9vw,.95rem);color:#0f172a!important}
        .product-card__info-inner .product-card__row--price,.product-card__info-inner .product-card__row--footer,.product-card__info-inner .product-card__row--reviews{min-height:0;max-height:100%;overflow:hidden;align-self:start}
        .product-card__row--price{margin-top:0!important}
        .product-card__row,.product-card__row--oneline{flex-wrap:nowrap!important;overflow:hidden!important;white-space:nowrap}
        .product-card__info-inner .product-card__row--footer,.product-card__info-inner .product-card__row--reviews{margin-top:0!important}
        .product-card__stock,.product-card__shipping-badge{font-size:0.65rem;font-weight:500}
        .product-card__cta,.buy-now-cta{flex:0 0 34px;flex-shrink:0;width:calc(100% - 12px);max-width:320px;margin:6px auto 0}
        .show-cart-count.is-pulsing,.show-wishlist-count.is-pulsing{animation:badgePulse .32s ease}
        @keyframes badgePulse{0%{transform:scale(1)}40%{transform:scale(1.16)}100%{transform:scale(1)}}
        :root{
            --footer-bg-image:url('{{ url("serve-css/img/footer-bg.png") }}?v={{ $assetVersion }}');
            --product-card-bg:{{ optional($uiSettings)->product_card_bg ?? $general->product_card_color ?? '#ffffff' }};
            --product-button-color:{{ optional($uiSettings)->product_button_color ?? $general->button_color ?? '#1f2937' }};
            --product-button-hover:{{ optional($uiSettings)->product_buy_now_hover ?? $general->button_hover_color ?? '#374151' }};
            --product-buy-now-color:{{ optional($uiSettings)->product_buy_now_color ?? '#0e9f90' }};
            --product-buy-now-hover:{{ optional($uiSettings)->product_buy_now_hover ?? '#0c8a7d' }};
            --product-price-color:{{ optional($uiSettings)->product_price_color ?? optional($uiSettings)->product_buy_now_color ?? '#0e9f90' }};
            --product-rating-color:{{ optional($uiSettings)->rating_color ?? $general->rating_star_color ?? '#f59e0b' }};
            --product-discount-badge:{{ optional($uiSettings)->discount_badge_color ?? $general->discount_badge_color ?? '#dc2626' }};
            --product-stock-color:{{ optional($uiSettings)->stock_color ?? '#16a34a' }};
            --product-shipping-color:{{ optional($uiSettings)->shipping_badge_color ?? '#2563eb' }};
            @if(!empty(optional($uiSettings)->header_bg))--header-bg:{{ optional($uiSettings)->header_bg }};@endif
            @if(!empty(optional($uiSettings)->footer_bg))--footer-bg-color:{{ optional($uiSettings)->footer_bg }};@endif
        }
        @if(optional($uiSettings)->theme_template && optional($uiSettings)->theme_template !== 'default')
        body[data-theme="{{ optional($uiSettings)->theme_template }}"] { }
        @endif
    </style>

    {{-- Single compiled storefront stylesheet (Bootstrap + legacy + Tailwind @tailwind) — fewer round trips, long cache --}}
    <link rel="stylesheet" href="{{ url('serve-css/tailwind-storefront') }}?v={{ $assetVersion }}">
    <noscript>
        <link rel="stylesheet" href="{{ url('serve-css/tailwind-storefront') }}?v={{ $assetVersion }}">
    </noscript>

    @stack('style-lib')

    @stack('style')

    <style>
        /* Minimal premium product card (Apple-style) */
        .product-card {
            font-family: Inter, ui-sans-serif, system-ui, sans-serif !important;
            background: #FFFFFF !important;
            border: 0 !important;
            border-radius: 0.75rem !important; /* rounded-xl */
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08), 0 1px 2px rgba(15, 23, 42, 0.05) !important; /* shadow-sm/md */
            overflow: hidden !important;
        }
        .product-card,
        .product-card-col,
        .product-card-col--home,
        .product-carousel__item,
        .product-card .product-card__img-wrap,
        .product-card .product-card__img-link,
        .product-card .product-card__info,
        .product-card .product-card__info-inner {
            background: #FFFFFF !important;
            border: 0 !important;
            box-shadow: none !important;
            outline: 0 !important;
        }
        .product-card .product-card__img-wrap::before,
        .product-card .product-card__img-wrap::after,
        .product-card .product-card__info::before,
        .product-card .product-card__info::after {
            content: none !important;
            display: none !important;
        }

        /* Image area */
        .product-card .product-card__img-wrap {
            position: relative !important;
            height: 16rem !important; /* h-64 */
            min-height: 16rem !important;
            max-height: 16rem !important;
            padding: 0.5rem !important;
            margin: 0 !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            border-radius: 0.75rem 0.75rem 0 0 !important;
        }
        .product-card .product-card__img-link {
            position: absolute !important;
            inset: 0.5rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .product-card .product-card__img {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
            object-position: center !important;
            aspect-ratio: 1 / 1 !important;
            transform: scale(1.06) !important;
        }

        /* Badge */
        .product-card .product-card__badges-top {
            top: 0.5rem !important;
            left: 0.5rem !important;
            gap: 0.35rem !important;
            transform: none !important;
            max-width: calc(100% - 1rem) !important;
        }
        .product-card .product-card__badge {
            font-size: 0.75rem !important; /* text-xs */
            line-height: 1 !important;
            padding: 0.25rem 0.5rem !important; /* px-2 py-1 */
            border-radius: 0.375rem !important; /* rounded-md */
            font-weight: 600 !important;
            text-transform: none !important;
            letter-spacing: 0 !important;
            box-shadow: none !important;
        }
        .product-card .product-card__badge--discount-banner { background: #fee2e2 !important; color: #991b1b !important; }
        .product-card .product-card__badge--custom { background: #dbeafe !important; color: #1e3a8a !important; }
        .product-card .product-card__badge--new { background: #e0f2fe !important; color: #075985 !important; }
        .product-card .product-card__badge--best { background: #fef3c7 !important; color: #92400e !important; }

        /* Content area */
        .product-card .product-card__info {
            margin-top: 0.25rem !important;
            padding: 0 0.625rem 0.04rem !important;
            border-top: 0 !important;
            min-height: 0 !important;
            height: auto !important;
            max-height: none !important;
        }
        .product-card .product-card__info-inner {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.2rem !important; /* equal gap for line 1-2-3 */
            padding: 0 !important;
        }
        .product-card .product-card__title { order: 1 !important; }
        .product-card .product-card__row--price { order: 2 !important; }
        .product-card .product-card__row--reviews { order: 3 !important; }
        .product-card .product-card__title,
        .product-card .product-card__title a {
            margin: 0 !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;   /* font-semibold */
            line-height: 1.15 !important;
            color: #1f2937 !important;      /* text-gray-800 */
            display: block !important;
            -webkit-line-clamp: 1 !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }
        .product-card .product-card__title {
            min-height: 0 !important;
            height: auto !important;
            max-height: none !important;
            padding: 0 !important;
            margin-bottom: 0 !important;
        }
        .product-card .product-card__price {
            font-size: 0.96rem !important;
            font-weight: 700 !important;    /* font-bold */
            color: #000000 !important;
        }
        .product-card .product-card__price-old {
            font-size: 0.7rem !important;
            color: #9ca3af !important;      /* text-gray-400 */
            text-decoration: line-through !important;
        }
        .product-card .product-card__discount-once {
            font-size: 0.75rem !important;
            color: #b91c1c !important;
            background: transparent !important;
            padding: 0 !important;
            border-radius: 0 !important;
        }
        .product-card .product-card__row--price,
        .product-card .product-card__row--reviews {
            margin: 0 !important;
            padding: 0 !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow: hidden !important;
            border: 0 !important;
            line-height: 1.05 !important;
        }
        .product-card .product-card__row--price { gap: 0.24rem !important; }
        .product-card .product-card__row--reviews { gap: 0.2rem !important; }
        .product-card .product-card__row--reviews .product-card__stock {
            font-size: 0.68rem !important;  /* compact text-xs */
            color: #16a34a !important;      /* text-green-600 */
            font-weight: 600 !important;
            background: transparent !important;
            padding: 0 !important;
            margin-left: 0 !important;
        }
        .product-card .product-card__row--reviews .product-card__reviews-count,
        .product-card .product-card__stars {
            font-size: 0.64rem !important;
        }
        .product-card .product-card__row--reviews .product-card__shipping-badge,
        .product-card .free-delivery-badge,
        .product-card .product-card__sep--delivery {
            display: none !important;
        }

        /* CTA */
        .product-card .product-card__cta.product-card__cta--cart {
            order: 4 !important;
            margin-top: 0.2rem !important; /* same visual gap after line 3 */
            margin-bottom: 0 !important;
            width: 100% !important;
            border: 0 !important;
            border-radius: 0.5rem !important; /* rounded-lg */
            padding-top: 0.32rem !important;
            padding-bottom: 0.32rem !important;
            min-height: 0 !important;
            height: auto !important;
            line-height: 1.25 !important;
            background: #16a34a !important;    /* bg-green-600 */
            color: #ffffff !important;
            font-size: 1rem !important;
            font-weight: 800 !important;
            letter-spacing: 0.01em !important;
            box-shadow: none !important;
            text-rendering: geometricPrecision !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            gap: 0.42rem !important;
        }
        .product-card .product-card__cta.product-card__cta--cart .product-card__cta-label {
            font-size: 1rem !important;
            line-height: 1 !important;
            font-weight: 800 !important;
            color: #fff !important;
            display: inline-block !important;
        }
        .product-card .product-card__cta .buy-now-cta__icon {
            width: 1rem !important;
            height: 1rem !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex: 0 0 1rem !important;
        }
        .product-card .product-card__cta .buy-now-cta__icon .ui-icon,
        .product-card .product-card__cta .buy-now-cta__icon svg {
            width: 1rem !important;
            height: 1rem !important;
            stroke-width: 2.2 !important;
        }
        .product-card .product-card__cta .buy-now-cta__icon .buy-now-cta__icon-img {
            width: 1rem !important;
            height: 1rem !important;
            object-fit: contain !important;
            display: block !important;
        }
        .product-card .product-card__cta.product-card__cta--cart:hover {
            background: #15803d !important;    /* bg-green-700 */
        }

        /* Fly-to-header animation visuals */
        .fly-to-header-clone {
            position: fixed;
            width: 60px;
            height: 60px;
            border-radius: 999px;
            overflow: hidden;
            z-index: 100200;
            pointer-events: none;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.18);
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .fly-to-header-clone img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .header-icon-bounce {
            animation: headerIconBounce .28s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .counter-updated {
            animation: headerCountPop .26s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes headerIconBounce {
            0% { transform: scale(1); }
            55% { transform: scale(1.14); }
            100% { transform: scale(1); }
        }
        @keyframes headerCountPop {
            0% { transform: scale(1); }
            55% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
    </style>

    @include('partials.tracking_scripts')
</head>

<body class="overflow-hidden font-sans antialiased" @if(optional($uiSettings)->theme_template && optional($uiSettings)->theme_template !== 'default') data-theme="{{ $uiSettings->theme_template }}" @endif>
    <!-- Preloader removed for instant page loads -->
    @yield('app')
    @include($activeTemplate . 'partials.mobile_bottom_nav')
    @guest
        @include($activeTemplate . 'partials.guest_account_modal')
    @endguest

    <div class="overlay"></div>
    @include($activeTemplate . 'partials.custom_site_messages')
    @include($activeTemplate . 'partials.cookie_banner')

    <button type="button" class="scrollToTop">@include($activeTemplate . 'partials.icon', ['name' => 'angle-double-up'])</button>
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

            var galleryStr = card.getAttribute('data-gallery');
            if (!galleryStr) return;

            var urls = [];
            try { urls = JSON.parse(galleryStr); } catch (e) { return; }
            if (!Array.isArray(urls) || urls.length < 2) return;

            var img = card.querySelector('.product-card__img--cycle');
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
            document.addEventListener('click', function(e) {
                var authBtn = e.target.closest('[data-guest-auth]');
                if (!authBtn) return;
                e.preventDefault();
                var mode = authBtn.getAttribute('data-guest-auth');
                var authUrl = mode === 'register' ? '{{ route("user.register") }}' : '{{ route("user.login") }}';
                var guestAccountModal = document.getElementById('guestAccountModal');
                if (guestAccountModal) modalApi.hide(guestAccountModal);
                if (typeof window.openAuthModalInIframe === 'function') window.openAuthModalInIframe(authUrl);
                else window.location.href = authUrl;
            });
        })();
    </script>
    @endguest
    <script>
        (function () {
            try {
                var accountBtn = document.getElementById('mobile-account-btn');
                if (!accountBtn || accountBtn.dataset.boundClick === '1') return;
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
</body>

</html>