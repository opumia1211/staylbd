@php
    $uiSource = [
        'product_card_bg' => optional($uiSettings)->product_card_bg ?? $general->product_card_color ?? '#ffffff',
        'product_button_color' => optional($uiSettings)->product_button_color ?? $general->button_color ?? '#1f2937',
        'product_buy_now_color' => optional($uiSettings)->product_buy_now_color ?? '#0e9f90',
        'product_buy_now_hover' => optional($uiSettings)->product_buy_now_hover ?? '#0c8a7d',
        'product_price_color' => optional($uiSettings)->product_price_color ?? optional($uiSettings)->product_buy_now_color ?? '#0e9f90',
        'rating_color' => optional($uiSettings)->rating_color ?? $general->rating_star_color ?? '#f59e0b',
        'discount_badge_color' => optional($uiSettings)->discount_badge_color ?? $general->discount_badge_color ?? '#dc2626',
        'stock_color' => optional($uiSettings)->stock_color ?? '#16a34a',
        'shipping_badge_color' => optional($uiSettings)->shipping_badge_color ?? '#2563eb',
        'header_top_bg' => optional($uiSettings)->header_top_bg ?? '#0f172a',
        'header_bg' => optional($uiSettings)->header_bg ?? '#ffffff',
        'footer_bg' => optional($uiSettings)->footer_bg ?? '#0f172a',
        'theme_template' => optional($uiSettings)->theme_template ?? 'default',
    ];

    $previewEncoded = request()->query('ui_preview');
    if (is_string($previewEncoded) && $previewEncoded !== '') {
        try {
            $decoded = base64_decode(strtr($previewEncoded, ' ', '+'), true);
            if (is_string($decoded) && $decoded !== '') {
                $parsed = json_decode($decoded, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($parsed)) {
                    foreach ($uiSource as $key => $currentValue) {
                        if (array_key_exists($key, $parsed) && is_string($parsed[$key])) {
                            $candidate = trim($parsed[$key]);
                            if ($candidate !== '' && strlen($candidate) <= 50) {
                                $uiSource[$key] = $candidate;
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore malformed preview payload
        }
    }
@endphp
{{-- Admin-driven CSS variables (must stay server-rendered) --}}
<style id="stayl-storefront-ui-vars">
        :root{
            --footer-bg-image:url('{{ url("serve-css/img/footer-bg.png") }}?v={{ $assetVersion }}');
            --product-card-bg:{{ $uiSource['product_card_bg'] }};
            --product-button-color:{{ $uiSource['product_button_color'] }};
            --product-button-hover:{{ $uiSource['product_buy_now_hover'] }};
            --product-buy-now-color:{{ $uiSource['product_buy_now_color'] }};
            --product-buy-now-hover:{{ $uiSource['product_buy_now_hover'] }};
            --product-price-color:{{ $uiSource['product_price_color'] }};
            --product-rating-color:{{ $uiSource['rating_color'] }};
            --product-discount-badge:{{ $uiSource['discount_badge_color'] }};
            --product-stock-color:{{ $uiSource['stock_color'] }};
            --product-shipping-color:{{ $uiSource['shipping_badge_color'] }};
            --category-card-bg:{{ $uiSource['product_card_bg'] }};
            --category-card-text:{{ $uiSource['product_button_color'] }};
            --category-title-color:{{ $uiSource['product_button_color'] }};
            --header-bg:{{ $uiSource['header_bg'] ?: '#ffffff' }};
            --header-top-bg: #020617; /* Deeper Professional Navy */
            --footer-bg-color:{{ $uiSource['footer_bg'] ?: '#0f172a' }};
            --header-icon-color:{{ $uiSource['product_button_color'] }};
            --header-accent-color:{{ $uiSource['product_buy_now_color'] }};
            /* Unified premium storefront palette */
            --stayl-color-header-top: #020617;
            --stayl-color-header-main: #ffffff;
            --stayl-color-header-menu: #0f172a; /* Deep matching menu */
            --stayl-color-surface:#f3f7fb;
            --stayl-color-surface-soft:#ffffff;
            --stayl-color-text:#0f172a;
            --stayl-color-text-soft:#475569;
            --stayl-glass-bg:rgba(255,255,255,.72);
            --stayl-glass-border:rgba(148,163,184,.26);
            --stayl-glass-shadow:0 14px 34px rgba(15,23,42,.09);
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --stayl-color-surface:#070b13;
                --stayl-color-surface-soft:#0b1220;
                --stayl-color-text:#e2e8f0;
                --stayl-color-text-soft:#94a3b8;
                --stayl-glass-bg:rgba(15,23,42,.68);
                --stayl-glass-border:rgba(148,163,184,.18);
                --stayl-glass-shadow:0 18px 36px rgba(2,6,23,.45);
                --header-bg:#020617;
                --header-top-bg:#020617;
                --stayl-color-header-top: #000000;
            }
        }
        body[data-theme="template_2"] {
            --product-card-bg:#f8fbff;
            --product-button-color:#0f172a;
            --product-buy-now-color:#0284c7;
            --product-buy-now-hover:#0369a1;
            --product-price-color:#0ea5e9;
            --header-bg:#ffffff;
            --footer-bg-color:#082f49;
        }
        body[data-theme="template_3"] {
            --product-card-bg:#111827;
            --product-button-color:#f59e0b;
            --product-buy-now-color:#f97316;
            --product-buy-now-hover:#ea580c;
            --product-price-color:#fbbf24;
            --header-bg:#0b1220;
            --footer-bg-color:#020617;
        }
        body[data-theme="template_4"] {
            --product-card-bg:#f7fffb;
            --product-button-color:#065f46;
            --product-buy-now-color:#0d9488;
            --product-buy-now-hover:#0f766e;
            --product-price-color:#0f766e;
            --header-bg:#ecfdf5;
            --footer-bg-color:#022c22;
        }
        body[data-theme="template_5"] {
            --product-card-bg:#fff7fb;
            --product-button-color:#be185d;
            --product-buy-now-color:#ec4899;
            --product-buy-now-hover:#db2777;
            --product-price-color:#be185d;
            --header-bg:#fff1f2;
            --footer-bg-color:#4a044e;
        }
        body[data-theme="template_6"] {
            --product-card-bg:#eef4ff;
            --product-button-color:#1e3a8a;
            --product-buy-now-color:#2563eb;
            --product-buy-now-hover:#1d4ed8;
            --product-price-color:#1d4ed8;
            --header-bg:#f8fbff;
            --footer-bg-color:#0f1f3d;
        }
        body[data-theme="template_7"] {
            --product-card-bg:#f8f5ff;
            --product-button-color:#5b21b6;
            --product-buy-now-color:#7c3aed;
            --product-buy-now-hover:#6d28d9;
            --product-price-color:#7c3aed;
            --header-bg:#f6f3ff;
            --footer-bg-color:#2e1065;
        }
        body[data-theme="template_8"] {
            --product-card-bg:#f3f4f6;
            --product-button-color:#111827;
            --product-buy-now-color:#0f766e;
            --product-buy-now-hover:#115e59;
            --product-price-color:#0f766e;
            --header-bg:#ffffff;
            --footer-bg-color:#111827;
        }

        .home-category-section__title { color: var(--category-title-color, #374151) !important; }
        .home-category-section__card {
            background: var(--category-card-bg, #f5f5f5) !important;
            color: var(--category-card-text, #1f2937) !important;
            border-color: color-mix(in srgb, var(--category-card-text, #1f2937) 24%, transparent) !important;
        }
        .home-category-section__card-icon { color: color-mix(in srgb, var(--category-card-text, #1f2937) 72%, #ffffff) !important; }
        .home-category-section__card:hover .home-category-section__card-icon { color: var(--product-buy-now-color, #0e9f90) !important; }

        .btn.btn--primary,
        .btn.btn-primary,
        .footer-glass__btn--primary,
        .custom-newsletter-btn {
            background: var(--product-buy-now-color, #0e9f90) !important;
            border-color: var(--product-buy-now-color, #0e9f90) !important;
            color: #ffffff !important;
        }
        .btn.btn--primary:hover,
        .btn.btn-primary:hover,
        .footer-glass__btn--primary:hover,
        .custom-newsletter-btn:hover {
            background: var(--product-buy-now-hover, #0c8a7d) !important;
            border-color: var(--product-buy-now-hover, #0c8a7d) !important;
        }
        .footer-glass__btn--outline,
        .stayl-footer-social-link {
            border-color: var(--product-button-color, #1f2937) !important;
            color: var(--product-button-color, #1f2937) !important;
        }
        .footer-glass__btn--outline:hover,
        .stayl-footer-social-link:hover {
            background: var(--product-button-color, #1f2937) !important;
            color: #ffffff !important;
        }
</style>
@php
    $headerControlLive = \App\Services\HeaderControlService::getLiveConfig();
    $headerControlAppearance = (array) ($headerControlLive['appearance'] ?? []);
    $headerTopBg = (string) ($headerControlAppearance['top_bg'] ?? '#0f172a');
    $headerMainBg = (string) ($headerControlAppearance['main_bg'] ?? '#f8fafc');
    $headerMenuBg = (string) ($headerControlAppearance['menu_bg'] ?? '#c7eafe');
    $headerTopHeight = (int) ($headerControlAppearance['top_height'] ?? 38);
    $headerMainHeight = (int) ($headerControlAppearance['main_height'] ?? 56);
    $headerMenuHeight = (int) ($headerControlAppearance['menu_height'] ?? 38);
    $layoutWidthDesktop = (int) ($headerControlAppearance['width_desktop'] ?? 1920);
    $layoutWidthLaptop = (int) ($headerControlAppearance['width_laptop'] ?? 1600);
    $layoutWidthTablet = (int) ($headerControlAppearance['width_tablet'] ?? 1200);
    $layoutWidthMobile = (int) ($headerControlAppearance['width_mobile'] ?? 100);
@endphp
<style id="stayl-storefront-live-glass-overrides">
    :root{
        --stayl-color-header-top:var(--header-top-bg, #0f172a);
        --stayl-color-header-main:#ffffff;
        --stayl-color-header-menu:#0e9f90;
        --stayl-surface-bg:#f8fafc;
        --stayl-surface-card:#ffffff;
        --stayl-surface-card-border:rgba(15, 23, 42, 0.06);
        --stayl-surface-shadow:0 10px 40px rgba(15,23,42,0.04);
        --stayl-text-main:#0f172a;
        --stayl-text-soft:#64748b;
        --stayl-content-max:min({{ $layoutWidthDesktop }}px, calc(100vw - 2 * var(--stayl-pad-x)));
    }

    @media (max-width: 1599.98px){
        :root{
            --stayl-content-max:min({{ $layoutWidthLaptop }}px, calc(100vw - 2 * var(--stayl-pad-x)));
        }
    }
    @media (max-width: 1199.98px){
        :root{
            --stayl-content-max:min({{ $layoutWidthTablet }}px, calc(100vw - 2 * var(--stayl-pad-x)));
        }
    }
    @media (max-width: 767.98px){
        :root{
            --stayl-content-max:min({{ $layoutWidthMobile }}px, calc(100vw - 2 * var(--stayl-pad-x)));
        }
    }

    html {
        height: 100%;
        overflow: hidden;
        scroll-behavior: smooth;
    }
    body.antialiased {
        height: 100%;
        min-height: 100vh;
        overflow-x: hidden !important;
        overflow-y: auto !important;
        position: relative !important;
        background: #f8fafc !important;
        color: var(--stayl-text-main) !important;
        font-family: 'Inter', sans-serif !important;
    }
    
    {{-- Anti-Flicker: Hide prices until JS applies the correct currency --}}
    [data-display-currency] .staylbd-rt-price,
    [data-display-currency] .staylbd-rt-price-compare,
    [data-display-currency] .price,
    [data-display-currency] .old-price {
        visibility: hidden !important;
    }
    .stayl-rt-ready .staylbd-rt-price,
    .stayl-rt-ready .staylbd-rt-price-compare,
    .stayl-rt-ready .price,
    .stayl-rt-ready .old-price {
        visibility: visible !important;
    }

    /* Header 3-bar exact color system + glass depth */
    .stayl-fixed-master{
        background: transparent !important;
        box-shadow: 0 18px 36px rgba(15,23,42,.12) !important;
    }
    .stayl-announcement-bar{
        background: {{ $headerTopBg }} !important;
        backdrop-filter: blur(14px) saturate(130%) !important;
        -webkit-backdrop-filter: blur(14px) saturate(130%) !important;
        min-height: {{ $headerTopHeight }}px !important;
        height: {{ $headerTopHeight }}px !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.22) !important;
        color: #f8fafc !important;
        position: relative !important;
        z-index: 100500 !important;
    }
    .stayl-top-bar{
        background: {{ $headerMainBg }} !important;
        backdrop-filter: blur(14px) saturate(130%) !important;
        -webkit-backdrop-filter: blur(14px) saturate(130%) !important;
        min-height: {{ $headerMainHeight }}px !important;
        height: {{ $headerMainHeight }}px !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.22) !important;
        color: #0f172a !important;
        position: relative !important;
        z-index: 100400 !important;
    }
    .stayl-yellow-bar{
        background: color-mix(in srgb, {{ $headerMenuBg }} 94%, #ffffff 6%) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        min-height: {{ $headerMenuHeight }}px !important;
        height: {{ $headerMenuHeight }}px !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.22) !important;
        color: #0f172a !important;
        position: relative !important;
        z-index: 100300 !important;
    }
    .stayl-announcement-bar .stayl-wrap{
        min-height: {{ $headerTopHeight }}px !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    .stayl-topbar-menu__panel{
        top: calc(100% + 8px) !important;
        bottom: auto !important;
        transform: translateY(8px) !important;
        z-index: 100900 !important;
    }
    .stayl-topbar-menu:hover .stayl-topbar-menu__panel,
    .stayl-topbar-menu:focus-within .stayl-topbar-menu__panel{
        transform: translateY(0) !important;
    }
    /* Slim bars with comfortable action targets */
    .stayl-topbar-menu__btn{
        min-height: 32px !important;
        padding: 0 8px !important;
        font-size: 13px !important;
        background: transparent !important;
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        color: #f8fafc !important;
    }
    .stayl-icon-item{
        width: 48px !important;
        height: 48px !important;
        border-radius: 0 !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }
    .stayl-announcement-bar,
    .stayl-top-bar,
    .stayl-yellow-bar {
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .stayl-fixed-master.is-scrolled-down .stayl-announcement-bar,
    .stayl-fixed-master.is-scrolled-down .stayl-yellow-bar{
        min-height: 0 !important;
        height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        opacity: 0 !important;
        border: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        pointer-events: none !important;
    }
    .stayl-fixed-master.is-scrolled-down .stayl-announcement-bar .stayl-wrap,
    .stayl-fixed-master.is-scrolled-down .stayl-yellow-bar .stayl-wrap{
        min-height: 0 !important;
        height: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
    }
    .stayl-search-pill{
        background: rgba(255, 255, 255, 0.96) !important;
        border: 1px solid rgba(71, 85, 105, 0.25) !important;
        border-radius: 8px !important;
        box-shadow: none !important;
        height: 42px !important; /* Balanced height for mega icons */
        margin: 7px 0 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        display: flex !important;
        align-items: center !important;
    }
    .stayl-search-pill:focus-within{
        background: #ffffff !important;
        border-color: rgba(14, 116, 144, 0.65) !important;
        box-shadow: none !important;
    }
    .stayl-search-input,
    .stayl-search-input:focus,
    .stayl-search-input:focus-visible{
        outline: none !important;
        border: none !important;
        box-shadow: none !important;
    }
    .stayl-seller-btn{
        min-height: 40px !important;
        padding: 10px 18px !important;
        font-size: 13px !important;
    }

    /* Header <-> banner gap */
    body.antialiased{ padding-top: calc(var(--stayl-dynamic-header-height, 175px) + 14px) !important; }
    .storefront-main{ padding-top: 0 !important; }
    #home-banner-section{ margin-top: 10px !important; }

    /* Banner নিচের gap আরও professional */
    .storefront-banner-separation{ height: 14px !important; }
    .storefront-section-separation{ height: 18px !important; }

    /* Public page global glass cards */
    .storefront-main .card,
    .storefront-main .checkout-card,
    .storefront-main .cart-sidebar,
    .storefront-main .product-card,
    .storefront-main .footer-glass__card,
    .storefront-main .modal-content,
    .storefront-main .home-category-section__card,
    .storefront-main .power-zone-unified-card,
    .storefront-main .deal__item,
    .storefront-main .track-quick-btn {
        background: var(--stayl-surface-card) !important;
        border: 1px solid var(--stayl-surface-card-border) !important;
        box-shadow: var(--stayl-surface-shadow) !important;
        border-radius: 24px !important;
        backdrop-filter: none !important; {{-- Cleaner non-glass for stability on Kartify look --}}
        -webkit-backdrop-filter: none !important;
    }

    .modal-content {
        border-radius: 32px !important;
    }

    /* Sections readable spacing on all public pages */
    .storefront-main > .main-container{ padding-top: clamp(10px, 1.1vw, 16px) !important; padding-bottom: clamp(10px, 1.1vw, 16px) !important; }
    .storefront-main section{ margin-bottom: clamp(14px, 1.6vw, 24px) !important; }

    /* Buttons + accents consistent */
    .btn--primary, .btn.btn-primary {
        background: var(--stayl-color-header-menu) !important;
        border-color: var(--stayl-color-header-menu) !important;
    }
    .btn--primary:hover, .btn.btn-primary:hover {
        filter: brightness(.95);
    }

    @media (prefers-color-scheme: dark){
        :root{
            --stayl-surface-bg:#070d17;
            --stayl-surface-card:rgba(15,23,42,.66);
            --stayl-surface-card-border:rgba(148,163,184,.20);
            --stayl-surface-shadow:0 18px 38px rgba(2,6,23,.44);
            --stayl-text-main:#e2e8f0;
            --stayl-text-soft:#a8b3c7;
        }
    }

    /* Manual Dark Mode Override (Premium Elite Midnight Look) */
    body.dark-mode {
        --stayl-surface-bg: #030712;
        --stayl-surface-card: #0f172a;
        --stayl-surface-card-border: rgba(148, 163, 184, 0.12);
        --stayl-surface-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
        --stayl-text-main: #f8fafc;
        --stayl-text-soft: #94a3b8;
        --header-bg: #030712;
        --header-main-bg: #030712;
        --stayl-color-header-main: #030712;
        --stayl-color-header-menu: #0d9488;
        background: #030712 !important;
        color: #f8fafc !important;
    }

    body.dark-mode .stayl-announcement-bar { background: rgba(2, 6, 23, 0.95) !important; border-bottom-color: rgba(255,255,255,0.03) !important; filter: brightness(0.9); }
    body.dark-mode .stayl-top-bar { background: rgba(15, 23, 42, 0.95) !important; color: #f8fafc !important; border-bottom-color: rgba(255,255,255,0.05) !important; }
    body.dark-mode .stayl-yellow-bar { background: rgba(30, 41, 59, 1) !important; color: #f8fafc !important; border-bottom-color: rgba(255,255,255,0.05) !important; }
    
    body.dark-mode .stayl-search-pill { background: #1e293b !important; border-color: rgba(255, 255, 255, 0.1) !important; }
    body.dark-mode .stayl-search-input { color: #f8fafc !important; }
    body.dark-mode .stayl-icon-item { color: #f8fafc !important; }
    body.dark-mode .stayl-sidebar-trigger { color: #f8fafc !important; }
    body.dark-mode .stayl-feature-card,
    body.dark-mode .home-features-section a { 
        background-color: #0f172a !important; 
        border: 1px solid rgba(255,255,255,0.05) !important; 
    }
    body.dark-mode .home-features-section h5 { color: #f8fafc !important; }
    body.dark-mode .home-features-section p { color: #94a3b8 !important; }
    
    {{-- Logo Adjustment --}}
    body.dark-mode .logo img { filter: brightness(0) invert(1) !important; }
    
    {{-- Product Cards & General Surfaces --}}
    body.dark-mode .product-card { background: #0f172a !important; border-color: rgba(255,255,255,0.05) !important; box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important; }
    body.dark-mode .product-card__name { color: #f8fafc !important; }
    body.dark-mode .product-card__price { color: #2dd4bf !important; }
    body.dark-mode .card, body.dark-mode .checkout-card, body.dark-mode .cart-sidebar { background: #0f172a !important; border-color: rgba(255,255,255,0.05) !important; }
    
    body.dark-mode .storefront-main .home-category-section__card { background: #1e293b !important; color: #f8fafc !important; }
    body.dark-mode .footer-glass { background: #020617 !important; border-top: 1px solid rgba(255,255,255,0.05); }
    body.dark-mode .footer-glass__bottom { border-top-color: rgba(255,255,255,0.05); }
    /* Smart Header Animation Styles */
    .stayl-fixed-master {
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1), background 0.4s ease !important;
        will-change: transform;
    }
    .stayl-yellow-bar {
        transition: opacity 0.4s ease, transform 0.4s ease !important;
    }
    .header-is-scrolled .stayl-top-bar {
        box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
    }
    
    body.antialiased { 
        padding-top: var(--stayl-dynamic-header-height, 175px) !important; 
    }
</style>
