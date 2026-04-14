@php
    $customButtonsAll = \App\Models\Frontend::where('data_keys', 'custom_buttons.element')->orderBy('id', 'asc')->get();
    $customHeaderButtons = $customButtonsAll->filter(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (($dv['target'] ?? '') === 'header') && ((int) ($dv['is_active'] ?? 1) === 1);
    })->sortBy(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (int) ($dv['display_order'] ?? 0);
    })->values();

    if (!isset($__staylHeaderCategories)) {
        $__staylHeaderCategories = \Illuminate\Support\Facades\Cache::remember(
            'storefront.header_nav_categories_v1',
            300,
            static fn () => \App\Models\Category::active()
                ->with(['subcategories' => static fn ($q) => $q->active()])
                ->orderByDesc('id')
                ->limit(24)
                ->get()
        );
    }

    $headerBarDefaults = [
        'header_bar_top_notice' => true,
        'header_bar_main' => true,
        'header_bar_menu' => true,
    ];
    $headerBarOrderIndex = [];
    $headerBarVisible = $headerBarDefaults;
    $headerLayout = \App\Services\HomepageLayoutService::getOrderedSections();
    foreach ($headerLayout as $layoutIndex => $layoutSlot) {
        $layoutId = (string) ($layoutSlot['id'] ?? '');
        if (!array_key_exists($layoutId, $headerBarDefaults)) {
            continue;
        }
        $headerBarOrderIndex[$layoutId] = $layoutIndex + 1;
        $headerBarVisible[$layoutId] = !empty($layoutSlot['enabled']);
    }

    $footerData = function_exists('getCachedFooterData') ? getCachedFooterData() : [];
    $companyInfo = $footerData['footer_company_info'] ?? null;
    $supportCenter = $footerData['footer_support_center'] ?? null;
    $appPromotion = $footerData['footer_app_promotion'] ?? null;
    $appPromotionItems = $footerData['footer_app_promotion_items'] ?? collect();
    $contactContent = $footerData['contact'] ?? null;

    $headerLanguages = $general->multi_language ? \App\Models\Language::query()->orderBy('name')->get() : collect();
    $headerControl = \App\Services\HeaderControlService::getLiveConfig();
    $headerAppearance = (array) ($headerControl['appearance'] ?? []);
    $headerTopCfg = (array) ($headerControl['top_bar'] ?? []);
    $headerMainCfg = (array) ($headerControl['main_bar'] ?? []);
    $headerMenuCfg = (array) ($headerControl['menu_bar'] ?? []);
    $topCustomButtons = is_array($headerTopCfg['custom_buttons'] ?? null) ? $headerTopCfg['custom_buttons'] : [];
    $menuCustomButtons = is_array($headerMenuCfg['custom_buttons'] ?? null) ? $headerMenuCfg['custom_buttons'] : [];

    $currentLangCode = (string) (session('lang') ?: optional($headerLanguages->first())->code ?: 'EN');
    $currentLangRow = $headerLanguages->firstWhere('code', $currentLangCode);
    $currentLangName = trim((string) (optional($currentLangRow)->name ?: $currentLangCode));
    $languageButtonLabel = (($headerTopCfg['language_mode'] ?? 'code') === 'name') ? $currentLangName : strtoupper($currentLangCode);
    $currencyButtonLabel = (($headerTopCfg['currency_mode'] ?? 'code') === 'name')
        ? (string) ($general->cur_text ?? 'BDT')
        : strtoupper((string) ($general->cur_text ?? 'BDT'));
    // Keep header hotline in sync with Footer > Company Info fields.
    $headerContactPhone = trim((string) (optional($companyInfo)->data_values->contact_phone ?? ''));
    $headerContactEmail = trim((string) (optional($companyInfo)->data_values->contact_email ?? ''));
    if (trim((string) ($headerTopCfg['support_phone'] ?? '')) !== '') {
        $headerContactPhone = trim((string) $headerTopCfg['support_phone']);
    }
    if ($headerContactPhone === '') {
        $headerContactPhone = '888-777-999';
    }
    $appPromoEnabled = !empty(optional($appPromotion)->data_values->enabled);
    $headerAppItems = $appPromoEnabled
        ? $appPromotionItems->filter(function ($it) {
            $dv = is_array($it->data_values ?? null) ? (object) $it->data_values : ($it->data_values ?? (object) []);
            return trim((string) ($dv->platform ?? $dv->name ?? '')) !== '';
        })->values()
        : collect();

    $shippingRule = \App\Models\ShippingRule::getCached();
    $headerCodNoticeText = trim((string) (optional($shippingRule)->header_notice_text ?? ''));
    if ($headerCodNoticeText === '') {
        $headerCodNoticeText = __('Cash on Delivery available nationwide');
    }
@endphp

<style>
    :root {
        --stayl-h0: {{ (int) ($headerAppearance['top_height'] ?? 38) }}px;
        --stayl-h1: {{ (int) ($headerAppearance['main_height'] ?? 56) }}px;
        --stayl-h2: {{ (int) ($headerAppearance['menu_height'] ?? 38) }}px;
        --stayl-yellow: var(--stayl-color-header-menu, #0e9f90);
        --stayl-active-blue: var(--header-accent-color, #2eb4e7);
        --stayl-bg-light: #f1f3f5;
        --stayl-icon-gray: color-mix(in srgb, var(--header-bg, #ffffff) 84%, #e2e8f0);
        --stayl-header-top-bg: var(--stayl-color-header-main, #ffffff);
        --stayl-header-menu-bg: var(--stayl-color-header-menu, #0e9f90);
        --stayl-header-button-bg: var(--product-button-color, #111111);
        --stayl-header-accent: var(--product-buy-now-color, var(--stayl-active-blue));
        --stayl-header-badge-bg: var(--product-discount-badge, #ff4d4d);
        --stayl-header-top-glass-bg: {{ $headerAppearance['top_bg'] ?? '#0f172a' }};
        --stayl-header-main-glass-bg: {{ $headerAppearance['main_bg'] ?? '#f8fafc' }};
        --stayl-header-menu-glass-bg: {{ $headerAppearance['menu_bg'] ?? '#c7eafe' }};
        --stayl-header-glass-border: rgba(226, 232, 240, 0.22);
        --stayl-header-glass-text: #f8fafc;
        --stayl-header-top-text: #f8fafc;
        --stayl-header-main-text: #0f172a;
        --stayl-header-menu-text: #0f172a;
        --stayl-main-icon-size: {{ (int) ($headerMainCfg['icon_size'] ?? 48) }}px;
    }
    .stayl-fixed-master {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 100000;
        display: flex;
        flex-direction: column;
        box-shadow: none;
        font-family: 'Outfit', sans-serif !important;
        background: transparent;
        overflow: visible;
    }
    .stayl-top-bar {
        height: var(--stayl-h1);
        background: var(--stayl-header-main-glass-bg);
        border-bottom: 1px solid var(--stayl-header-glass-border);
        display: flex;
        align-items: center;
        width: 100%;
        color: var(--stayl-header-main-text);
        backdrop-filter: blur(14px) saturate(130%);
        -webkit-backdrop-filter: blur(14px) saturate(130%);
        position: relative;
        z-index: 100400;
        transition: transform 1.3s cubic-bezier(.23,1,.32,1), box-shadow 1s ease;
        will-change: transform;
    }
    .stayl-yellow-bar {
        height: var(--stayl-h2);
        background: color-mix(in srgb, var(--stayl-header-menu-glass-bg) 94%, #ffffff 6%);
        display: flex;
        align-items: center;
        width: 100%;
        border-bottom: 1px solid var(--stayl-header-glass-border);
        color: var(--stayl-header-menu-text);
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
        position: relative;
        z-index: 100300;
        transition: max-height 1.3s cubic-bezier(.23,1,.32,1), opacity 1.15s ease, transform 1.3s cubic-bezier(.23,1,.32,1), border-color 1.1s ease;
        max-height: var(--stayl-h2);
        will-change: max-height, transform, opacity;
    }
    .stayl-wrap {
        max-width: var(--stayl-content-max, min(1320px, calc(100vw - 2 * var(--stayl-pad-x, 16px))));
        margin: 0 auto;
        padding-left: calc(var(--stayl-pad-x, 16px) + env(safe-area-inset-left, 0px));
        padding-right: calc(var(--stayl-pad-x, 16px) + env(safe-area-inset-right, 0px));
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
    }
    .stayl-announcement-bar {
        min-height: var(--stayl-h0);
        height: var(--stayl-h0);
        background: var(--stayl-header-top-glass-bg);
        color: var(--stayl-header-top-text);
        border-bottom: 1px solid var(--stayl-header-glass-border);
        width: 100%;
        display: flex;
        align-items: center;
        font-size: 13px;
        font-weight: 600;
        backdrop-filter: blur(14px) saturate(130%);
        -webkit-backdrop-filter: blur(14px) saturate(130%);
        position: relative;
        z-index: 100500;
        transition: max-height 1.3s cubic-bezier(.23,1,.32,1), opacity 1.15s ease, transform 1.3s cubic-bezier(.23,1,.32,1), border-color 1.1s ease;
        max-height: var(--stayl-h0);
        will-change: max-height, transform, opacity;
    }
    .stayl-fixed-master.is-scrolled-down .stayl-announcement-bar,
    .stayl-fixed-master.is-scrolled-down .stayl-yellow-bar {
        max-height: 0 !important;
        min-height: 0 !important;
        height: 0 !important;
        opacity: 0;
        transform: translateY(-12px);
        border-bottom-color: transparent !important;
        pointer-events: none;
        overflow: hidden;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        margin: 0 !important;
    }
    .stayl-fixed-master.is-scrolled-down .stayl-announcement-bar .stayl-wrap,
    .stayl-fixed-master.is-scrolled-down .stayl-yellow-bar .stayl-wrap {
        min-height: 0 !important;
        height: 0 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        overflow: hidden !important;
    }
    .stayl-fixed-master.is-scrolled-down .stayl-top-bar {
        box-shadow: 0 6px 20px rgba(15, 23, 42, .10);
        transform: translateY(-4px);
        transition: transform 1.3s cubic-bezier(.23,1,.32,1), box-shadow 1s ease;
    }
    .stayl-announcement-bar .stayl-wrap {
        gap: 16px;
        justify-content: space-between;
        flex-wrap: nowrap;
        min-height: var(--stayl-h0);
        padding-top: 0;
        padding-bottom: 0;
    }
    .stayl-announcement-link {
        color: #f8fafc !important;
        text-decoration: none !important;
        opacity: 0.92;
        transition: opacity 0.2s;
    }
    .stayl-announcement-link:hover {
        opacity: 1;
        text-decoration: underline !important;
    }
    .stayl-topbar-menu {
        position: relative;
        padding-bottom: 8px;
        margin-bottom: -8px;
    }
    .stayl-topbar-menu__btn {
        background: transparent;
        border: none;
        color: var(--stayl-header-top-text);
        border-radius: 0;
        font-size: 13px;
        font-weight: 700;
        line-height: 1;
        padding: 0 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        min-height: 32px;
        box-shadow: none;
    }
    .stayl-support-inline {
        gap: 8px;
        white-space: nowrap;
    }
    .stayl-support-icon3d {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at 30% 30%, #ffffff, #dbeafe 35%, #93c5fd 100%);
        color: #0f172a;
        border: 1px solid rgba(15, 23, 42, .15);
        box-shadow: 0 2px 7px rgba(15, 23, 42, .22);
    }
    .stayl-topbar-menu__panel {
        position: absolute;
        top: 100%;
        bottom: auto;
        right: 0;
        min-width: 220px;
        background: #ffffff;
        color: #111827;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 10px;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.15);
        padding: 8px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(6px);
        transition: all 0.2s ease;
        z-index: 100900;
    }
    .stayl-topbar-menu:hover .stayl-topbar-menu__panel,
    .stayl-topbar-menu:focus-within .stayl-topbar-menu__panel {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .stayl-topbar-menu__item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 10px;
        border-radius: 8px;
        color: #0f172a !important;
        text-decoration: none !important;
        font-size: 14px;
        font-weight: 600;
    }
    .stayl-topbar-menu__item:hover {
        background: #f8fafc;
    }
    .stayl-topbar-select {
        width: 100%;
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        min-height: 34px;
        padding: 6px 10px;
    }
    .stayl-hotline-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,.2);
        background: rgba(255,255,255,.08);
        color: #f8fafc;
        font-weight: 800;
        font-size: 14px;
        letter-spacing: .2px;
        white-space: nowrap;
    }
    .stayl-hotline-chip__label {
        font-size: 12px;
        opacity: .82;
        font-weight: 700;
        text-transform: uppercase;
    }
    .stayl-support-grid {
        display: grid;
        gap: 6px;
    }
    .stayl-app-grid {
        display: grid;
        gap: 6px;
        max-height: 260px;
        overflow: auto;
    }
    @media (max-width: 1199.98px) {
        .stayl-topbar-menu {
            display: none !important;
        }
        .stayl-announcement-bar .stayl-wrap {
            flex-wrap: wrap;
        }
    }

    /* Professional Search UI */
    .stayl-search-pill {
        flex: 1;
        max-width: 620px;
        margin: 0 12px;
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid rgba(71, 85, 105, 0.25);
        border-radius: 8px;
        height: 42px;
        padding: 0 8px;
        padding-right: 8px;
        transition: 0.3s;
    }
    .stayl-search-pill:focus-within {
        background: #ffffff;
        border-color: rgba(14, 116, 144, 0.65);
        box-shadow: none;
    }
    .stayl-search-input {
        flex: 1;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        padding: 0 28px !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        color: #0f172a !important;
        width: 100%;
        box-shadow: none !important;
    }
    .stayl-search-input:focus,
    .stayl-search-input:focus-visible,
    .stayl-search-pill input:focus,
    .stayl-search-pill input:focus-visible {
        outline: none !important;
        box-shadow: none !important;
        border: none !important;
    }
    .stayl-search-input::placeholder {
        color: rgba(71, 85, 105, 0.82) !important;
    }
    .stayl-search-actions-inner {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-right: 10px;
    }
    .stayl-search-icon-btn {
        width: 32px;
        height: 32px;
        background: transparent;
        color: #0f172a !important;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: none;
    }
    .stayl-search-icon-btn:hover {
        filter: none;
        transform: none;
    }

    /* 3D/Professional Icons Alignment */
    .stayl-action-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }
    .stayl-icon-item {
        width: var(--stayl-main-icon-size);
        height: var(--stayl-main-icon-size);
        border-radius: 0;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        text-decoration: none !important;
        transition: color 0.2s ease;
        color: var(--stayl-header-main-text);
        filter: none;
        box-shadow: none;
        border: none;
    }
    .stayl-icon-item svg {
        stroke-width: 1.8;
        width: 25px;
        height: 25px;
    }
    .stayl-icon-item:hover {
        background: transparent;
        transform: none;
        box-shadow: none;
        filter: none;
    }
    .stayl-icon-item:hover svg {
        color: var(--header-accent-color, var(--stayl-header-accent));
    }
    .stayl-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: var(--stayl-header-badge-bg);
        color: #fff !important;
        font-size: 11px;
        font-weight: 800;
        min-width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.7);
        box-shadow: none;
    }

    /* Yellow Bar Navigation */
    .stayl-cat-btn {
        background: transparent !important;
        height: var(--stayl-h2);
        padding: 0 12px;
        display: flex;
        align-items: center;
        gap: 15px;
        color: #0f172a;
        font-weight: 800;
        font-size: 14px;
        cursor: pointer;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        transition: 0.3s;
    }
    .stayl-cat-btn:hover {
        background: transparent !important;
        color: #fff !important;
    }
    .stayl-nav-ul {
        display: flex;
        align-items: center;
        gap: clamp(14px, 1.4vw, 24px);
        margin-left: clamp(10px, 1.2vw, 20px);
        list-style: none;
        padding: 0;
        margin-bottom: 0;
        white-space: nowrap;
    }
    .stayl-nav-ul li a {
        color: #0f172a !important;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none !important;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .stayl-nav-ul li a:hover {
        color: #fff !important;
        transform: translateY(-2px);
    }
    .stayl-seller-btn {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 58%, #334155 100%);
        color: #ffffff !important;
        padding: 8px 14px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none !important;
        transition: 0.3s;
        box-shadow: none;
    }
    .stayl-seller-btn--top {
        background: linear-gradient(135deg, #111827 0%, #1f2937 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.22) !important;
        box-shadow: none !important;
        min-height: 32px !important;
        height: 32px !important;
        line-height: 1 !important;
        margin: 0 !important;
        padding: 0 12px !important;
        position: relative;
        overflow: hidden;
        align-self: center !important;
        transform: none !important;
    }
    .stayl-seller-btn--top svg {
        color: #f8fafc !important;
    }
    .stayl-seller-btn--top::before,
    .stayl-seller-btn--top::after {
        content: none !important;
        display: none !important;
    }
    .stayl-announcement-bar .stayl-wrap > .d-flex.align-items-center.gap-3:last-child {
        align-self: center;
    }
    .stayl-seller-btn:hover {
        filter: brightness(0.95);
        transform: none;
        box-shadow: none;
    }
    .stayl-top-bar .stayl-icon-item:last-child {
        background: #0f172a;
        color: #f8fafc !important;
        border-radius: 8px;
        border: 1px solid rgba(15, 23, 42, 0.85);
    }
    .stayl-account-icon,
    .stayl-account-icon svg {
        color: #0f172a !important;
        stroke: #0f172a !important;
    }
    .stayl-account-icon {
        background: transparent !important;
        border: none !important;
        border-radius: 0 !important;
    }
    .stayl-top-bar .stayl-wrap,
    .stayl-yellow-bar .stayl-wrap {
        gap: clamp(8px, 1vw, 14px);
    }
    .stayl-top-bar .stayl-wrap > .d-flex.align-items-center:first-child {
        flex-shrink: 0;
    }
    .stayl-top-bar .stayl-wrap > .flex.flex-1.items-center.justify-center.gap-2 {
        min-width: 0;
    }
    @media (max-width: 1599.98px) {
        .stayl-icon-item:nth-child(1),
        .stayl-icon-item:nth-child(2),
        .stayl-icon-item:nth-child(3),
        .stayl-icon-item:nth-child(4) {
            display: none !important;
        }
    }

    /* Categories Dropdown CSS */
    .stayl-cat-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        width: 300px;
        background: #fff;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        border-radius: 0 0 20px 20px;
        border-top: 4px solid #111;
        opacity: 0;
        visibility: hidden;
        transform: translateY(15px);
        transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 10000;
        padding: 15px 0;
    }
    /* Dropdown Hover State */
    #staylCatContainer:hover .stayl-cat-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .stayl-cat-dropdown ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .stayl-cat-dropdown li a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 30px;
        font-size: 15px;
        font-weight: 800;
        color: #111 !important;
        text-decoration: none !important;
        transition: 0.2s;
    }
    .stayl-cat-dropdown li a:hover {
        background: #f8f9fa;
        color: var(--stayl-header-accent) !important;
        padding-left: 35px;
    }

    /* Pages Dropdown CSS */
    .stayl-pages-item {
        position: relative;
    }
    .stayl-pages-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        width: 250px;
        background: #fff;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        border-radius: 16px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(15px);
        transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 10000;
        padding: 15px 0;
        border: 1px solid #f1f1f1;
    }
    .stayl-pages-item:hover .stayl-pages-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .stayl-pages-dropdown a {
        display: block;
        padding: 12px 25px;
        font-size: 15px;
        font-weight: 800;
        color: #111 !important;
        text-decoration: none !important;
        transition: 0.2s;
    }
    .stayl-pages-dropdown a:hover {
        background: #f8f9fa;
        color: var(--stayl-header-accent) !important;
        padding-left: 30px;
    }

    /* Premium Glass Sidebar CSS (Framework Independent) */
    .stayl-sb-header {
        padding: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f1f1;
        background: #f8f9fa;
    }
    .stayl-sb-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .stayl-sb-icon-box {
        background: #111;
        color: #fff;
        padding: 10px;
        border-radius: 12px;
        display: flex;
    }
    .stayl-sb-title {
        font-size: 22px;
        font-weight: 900;
        color: #111;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin: 0;
    }
    .stayl-sb-close {
        background: #fff;
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        cursor: pointer;
        transition: 0.3s;
    }
    .stayl-sb-close:hover {
        background: #f1f1f1;
        transform: rotate(90deg);
    }
    .stayl-sb-body {
        padding: 25px;
        overflow-y: auto;
        max-height: calc(100vh - 100px);
    }
    .stayl-sb-item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 15px 25px;
        border-radius: 18px;
        text-decoration: none !important;
        color: #111;
        margin-bottom: 15px;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #f8f9fa;
        border: 1px solid transparent;
    }
    .stayl-sb-item:hover {
        background: #ffffff;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        border-color: #f1f1f1;
        transform: translateY(-3px);
        color: var(--stayl-header-accent);
    }
    .stayl-sb-item-icon {
        width: 50px;
        height: 50px;
        background: #ffffff;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #555;
        transition: 0.3s;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }
    .stayl-sb-item:hover .stayl-sb-item-icon {
        background: var(--stayl-header-accent);
        color: #ffffff;
    }
    .stayl-sb-item-text {
        font-size: 18px;
        font-weight: 800;
        margin: 0;
    }
    .stayl-sb-divider-text {
        font-size: 12px;
        font-weight: 900;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin: 30px 10px 15px;
        border-bottom: 1px solid #f1f1f1;
        padding-bottom: 10px;
    }
    .stayl-sb-action-box {
        margin-top: 35px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 24px;
        border: 1px solid #f1f1f1;
    }
    .stayl-btn-dark {
        background: #111;
        color: #fff !important;
        width: 100%;
        padding: 18px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        text-decoration: none !important;
        font-weight: 800;
        font-size: 16px;
        transition: 0.3s;
    }
    .stayl-btn-dark:hover {
        background: var(--stayl-header-accent);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(46, 180, 231, 0.3);
    }
    .stayl-btn-outline {
        background: #fff;
        color: #111 !important;
        width: 100%;
        padding: 18px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        text-decoration: none !important;
        font-weight: 800;
        font-size: 16px;
        border: 2px solid #f1f1f1;
        margin-top: 15px;
        transition: 0.3s;
    }
    .stayl-btn-outline:hover {
        background: #f8f9fa;
        border-color: #ddd;
    }
</style>

<header class="stayl-fixed-master">
    @if(!empty($headerBarVisible['header_bar_top_notice']) && !empty($headerTopCfg['enabled']))
    <div class="stayl-announcement-bar" style="order: {{ $headerBarOrderIndex['header_bar_top_notice'] ?? 1 }};">
        <div class="stayl-wrap">
            <div class="d-flex align-items-center gap-3">
                <span>@lang('Need help?') <a href="{{ route('contact') }}" class="stayl-announcement-link">@lang('Contact support')</a></span>
                <span class="d-none d-md-inline">|</span>
                <span>{{ __($headerCodNoticeText) }}</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                @if(!empty($headerTopCfg['show_language']) && $headerLanguages->isNotEmpty())
                    <div class="stayl-topbar-menu">
                        <button type="button" class="stayl-topbar-menu__btn">
                            <span id="staylCurrentLanguageLabel">{{ $languageButtonLabel }}</span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"></path></svg>
                        </button>
                        <div class="stayl-topbar-menu__panel">
                            @foreach($headerLanguages as $lng)
                                <a href="{{ route('lang', $lng->code) }}" class="stayl-topbar-menu__item" data-stayl-lang-option="{{ strtoupper($lng->code) }}">
                                    <span>{{ __($lng->name) }}</span>
                                    @if(session('lang') === $lng->code)
                                        <span>✓</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($headerTopCfg['show_currency']))
                <div class="stayl-topbar-menu">
                    <button type="button" class="stayl-topbar-menu__btn">
                        <span id="staylCurrentCurrencyLabel">{{ $currencyButtonLabel }}</span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"></path></svg>
                    </button>
                    <div class="stayl-topbar-menu__panel" style="min-width:260px;">
                        <label class="small text-muted d-block px-1 pb-1 mb-1">@lang('Display product prices in')</label>
                        <select id="staylDisplayCurrency" class="stayl-topbar-select">
                            <option value="{{ $general->cur_text }}" selected>{{ $general->cur_text }} ({{ $general->cur_sym }})</option>
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                            <option value="GBP">GBP (£)</option>
                            <option value="INR">INR (₹)</option>
                            <option value="AED">AED (د.إ)</option>
                            <option value="SAR">SAR (ر.س)</option>
                            <option value="MYR">MYR (RM)</option>
                            <option value="SGD">SGD (S$)</option>
                            <option value="JPY">JPY (¥)</option>
                        </select>
                    </div>
                </div>
                @endif

                <a href="{{ $headerContactPhone !== '' ? 'tel:' . preg_replace('/[^0-9+]/', '', $headerContactPhone) : route('contact') }}" class="stayl-topbar-menu__btn stayl-support-inline text-decoration-none">
                    <span class="stayl-support-icon3d" aria-hidden="true">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </span>
                    <span>{{ $headerTopCfg['support_label'] ?? '24/7 Support' }}</span>
                    @if($headerContactPhone !== '')
                        <span class="ms-1">{{ $headerContactPhone }}</span>
                    @endif
                </a>

                @if(!empty($headerTopCfg['show_apps']))
                <div class="stayl-topbar-menu">
                    <button type="button" class="stayl-topbar-menu__btn">
                        @lang('Apps')
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"></path></svg>
                    </button>
                    <div class="stayl-topbar-menu__panel" style="min-width:270px;">
                        <div class="stayl-app-grid">
                            @forelse($headerAppItems as $item)
                                @php
                                    $dv = is_array($item->data_values ?? null) ? (object) $item->data_values : ($item->data_values ?? (object) []);
                                    $itemPlatform = trim((string) ($dv->platform ?? 'App'));
                                    $itemLink = trim((string) ($dv->link ?? '#')) ?: '#';
                                    $itemFile = trim((string) ($dv->app_file ?? ''));
                                    $itemHref = $itemFile !== '' ? asset('assets/files/frontend/apps/' . ltrim($itemFile, '/')) : $itemLink;
                                @endphp
                                <a href="{{ $itemHref }}" class="stayl-topbar-menu__item" @if($itemFile !== '') download @endif target="_blank" rel="noopener">
                                    <span>{{ $itemPlatform }}</span>
                                    <span>@lang('Open')</span>
                                </a>
                            @empty
                                <span class="stayl-topbar-menu__item text-muted">@lang('No apps added from admin panel')</span>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif

                @if(!empty($headerTopCfg['show_seller_button']))
                    <a href="{{ url($headerTopCfg['seller_url'] ?? '/seller/apply') }}" class="stayl-seller-btn stayl-seller-btn--top">
                        {{ __($headerTopCfg['seller_text'] ?? 'BECOME A SELLER') }}
                    </a>
                @endif
                @foreach($topCustomButtons as $tbtn)
                    @php
                        $tbtnLabel = trim((string) ($tbtn['label'] ?? ''));
                        $tbtnUrl = trim((string) ($tbtn['url'] ?? '#')) ?: '#';
                        $tbtnType = (string) ($tbtn['type'] ?? 'link');
                        $tbtnItems = is_array($tbtn['items'] ?? null) ? $tbtn['items'] : [];
                    @endphp
                    @continue($tbtnLabel === '')
                    @if($tbtnType === 'dropdown' && !empty($tbtnItems))
                        <div class="stayl-topbar-menu">
                            <button type="button" class="stayl-topbar-menu__btn">
                                {{ __($tbtnLabel) }}
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"></path></svg>
                            </button>
                            <div class="stayl-topbar-menu__panel">
                                @foreach($tbtnItems as $tbItem)
                                    <a href="{{ trim((string) ($tbItem['url'] ?? '#')) ?: '#' }}" class="stayl-topbar-menu__item">
                                        <span>{{ __(trim((string) ($tbItem['label'] ?? 'Item'))) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $tbtnUrl }}" class="stayl-topbar-menu__btn text-decoration-none">{{ __($tbtnLabel) }}</a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Row 1: The Main Action Bar --}}
    @if(!empty($headerBarVisible['header_bar_main']) && !empty($headerMainCfg['enabled']))
    <div class="stayl-top-bar" style="order: {{ $headerBarOrderIndex['header_bar_main'] ?? 2 }};">
        <div class="stayl-wrap">
            {{-- Logo --}}
            <div class="d-flex align-items-center">
                <a href="{{ route('home') }}">
                    @php $logo = getLogo('logo'); @endphp
                    @if($logo)
                        <img src="{{ $logo }}" alt="Staylbd" style="max-height: {{ (int) ($headerMainCfg['logo_max_height'] ?? 48) }}px; width: auto;">
                    @else
                        <span style="font-size: 34px; font-weight: 900; color: #111; letter-spacing: -2px;">{{ strtoupper(gs('site_name')) }}</span>
                    @endif
                </a>
            </div>

            {{-- Search Pill & External Lens --}}
            <div class="flex flex-1 items-center justify-center gap-2"> {{-- Tight gap for unified look --}}
                <form action="{{ route('products') }}" method="GET" class="stayl-search-pill" style="margin: 0 !important; flex: 1; max-width: 620px;">
                    <input type="text" name="search" class="stayl-search-input" placeholder="@lang('Search products, brands, and more')..." value="{{ request()->search ?? null }}" autocomplete="off">
                    <div class="stayl-search-actions-inner" style="gap: 12px; margin-right: 8px;">
                        {{-- Voice Search --}}
                        <div style="cursor:pointer; color:#555;" id="voiceSearchBtn" title="Voice Search">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="22"></line></svg>
                        </div>
                        {{-- Search Submit --}}
                        <button type="submit" class="stayl-search-icon-btn" style="width: 48px; height: 48px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </button>
                    </div>
                </form>
                {{-- Professional Lens Icon (Outside but Right Next to Search) --}}
                <div id="cameraSearchBtn" title="Camera Search" style="width: 34px; height: 34px; border-radius: 0; background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; box-shadow: none; transition: 0.2s; flex-shrink: 0;" onclick="alert('Camera Search feature');">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 7V5a2 2 0 0 1 2-2h2"></path>
                        <path d="M17 3h2a2 2 0 0 1 2 2v2"></path>
                        <path d="M21 17v2a2 2 0 0 1-2 2h-2"></path>
                        <path d="M7 21H5a2 2 0 0 1-2-2v-2"></path>
                        <circle cx="12" cy="12" r="3" stroke-width="2"></circle>
                    </svg>
                </div>
            </div>

            {{-- Action Icons (Premium SVG Set) --}}
            <div class="stayl-action-row">
                <a href="#" class="stayl-icon-item" title="Orders">
                    {{-- Lucide: shopping-bag --}}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                </a>
                <a href="{{ route('contact') }}" class="stayl-icon-item" title="Contact">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </a>
                <a href="{{ route('track.order') }}" class="stayl-icon-item" title="Track Order">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                </a>
                <a href="{{ route('user.wishlist') }}" class="stayl-icon-item" title="Wishlist">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    <span class="stayl-badge show-wishlist-count">0</span>
                </a>
                <a href="#" class="stayl-icon-item" title="Compare">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m16 4 4 4-4 4"></path><path d="M20 8H4"></path><path d="m8 20-4-4 4-4"></path><path d="M4 16h16"></path></svg>
                    <span class="stayl-badge show-compare-count">0</span>
                </a>
                <a href="{{ route('user.cart') }}" class="stayl-icon-item" title="Cart">
                    {{-- Lucide: shopping-cart --}}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                    <span class="stayl-badge show-cart-count">0</span>
                </a>
                <a href="{{ route('user.home') }}" class="stayl-icon-item stayl-account-icon" style="background:transparent !important;border:none !important;color:#0f172a !important;" title="Account">
                    {{-- Lucide: user-round --}}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 0 0-16 0"></path></svg>
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Row 2: Secondary Nav Bar --}}
    @if(!empty($headerBarVisible['header_bar_menu']) && !empty($headerMenuCfg['enabled']))
    <div class="stayl-yellow-bar" style="order: {{ $headerBarOrderIndex['header_bar_menu'] ?? 3 }};">
        <div class="stayl-wrap">
            <nav class="d-flex align-items-center h-100" style="gap: clamp(12px, 1.5vw, 24px);">
                {{-- Separate Hamburger Button --}}
                <div class="stayl-sidebar-trigger cursor-pointer flex items-center justify-center" style="width: 42px; height: 42px; transition: 0.3s; color: #0f172a;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1)';" title="Open Menu">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </div>

                {{-- All Categories Button (Separate) --}}
                <div class="h-100 position-relative" id="staylCatContainer">
                    <button class="stayl-cat-btn" id="staylCatBtn" style="padding: 0; gap: 10px;">
                        @lang('ALL CATEGORIES')
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="m6 9 6 6 6-6"></path></svg>
                    </button>
                    @if($__staylHeaderCategories->isNotEmpty())
                    <div class="stayl-cat-dropdown" id="staylCatDropdown">
                        <ul>
                            @foreach($__staylHeaderCategories->take(12) as $hc)
                                <li>
                                    <a href="{{ route('category.products', [slug($hc->name), $hc->id]) }}">
                                        {{ __($hc->name) }}
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="m9 18 6-6-6-6"></path></svg>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <ul class="stayl-nav-ul">
                    <li><a href="{{ route('home') }}">@lang('Homepage')</a></li>
                    <li><a href="{{ route('products') }}">@lang('Shop Products')</a></li>
                    <li class="stayl-pages-item">
                        <a href="#" style="display:flex; align-items:center; gap:8px;">@lang('Pages') <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="m6 9 6 6 6-6"></path></svg></a>
                        <div class="stayl-pages-dropdown">
                             <a href="{{ route('category.all') }}">@lang('All Categories')</a>
                             <a href="{{ route('track.order') }}">@lang('Track Order')</a>
                             <a href="{{ route('contact') }}">@lang('Customer Support')</a>
                        </div>
                    </li>
                    <li><a href="#">@lang('About Us')</a></li>
                    <li><a href="#">@lang('Latest Blog')</a></li>
                    <li><a href="{{ route('contact') }}">@lang('Contact Us')</a></li>
                    @foreach($menuCustomButtons as $mbtn)
                        @php
                            $mbtnLabel = trim((string) ($mbtn['label'] ?? ''));
                            $mbtnUrl = trim((string) ($mbtn['url'] ?? '#')) ?: '#';
                            $mbtnType = (string) ($mbtn['type'] ?? 'link');
                            $mbtnItems = is_array($mbtn['items'] ?? null) ? $mbtn['items'] : [];
                        @endphp
                        @continue($mbtnLabel === '')
                        @if($mbtnType === 'dropdown' && !empty($mbtnItems))
                            <li class="stayl-pages-item">
                                <a href="#" style="display:flex; align-items:center; gap:8px;">{{ __($mbtnLabel) }} <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="m6 9 6 6 6-6"></path></svg></a>
                                <div class="stayl-pages-dropdown">
                                    @foreach($mbtnItems as $mbItem)
                                        <a href="{{ trim((string) ($mbItem['url'] ?? '#')) ?: '#' }}">{{ __(trim((string) ($mbItem['label'] ?? 'Item'))) }}</a>
                                    @endforeach
                                </div>
                            </li>
                        @else
                            <li><a href="{{ $mbtnUrl }}">{{ __($mbtnLabel) }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </nav>

            @if(!empty($headerMenuCfg['show_seller_button']))
                <a href="{{ route('seller.apply') }}" class="stayl-seller-btn">
                    {{ __($headerTopCfg['seller_text'] ?? 'BECOME A SELLER') }}
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color:var(--stayl-yellow);"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            @endif
        </div>
    </div>
    @endif
</header>

<div class="glass-mobile-menu" id="glassSidebar" style="font-family: 'Outfit', sans-serif;">
    <div class="glass-mobile-menu-overlay glass-sidebar-overlay" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(5px);"></div>
    <div class="glass-mobile-menu-content" style="background: #ffffff; width: 420px; box-shadow: 20px 0 60px rgba(0,0,0,0.15);">
        
        {{-- Custom CSS Header --}}
        <div class="stayl-sb-header">
            <div class="stayl-sb-title-wrap">
                <div class="stayl-sb-icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </div>
                <h3 class="stayl-sb-title">@lang('NAVIGATION')</h3>
            </div>
            <button id="glassSidebarClose" class="stayl-sb-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <div class="stayl-sb-body">
            
            <a href="{{ route('home') }}" class="stayl-sb-item">
                <div class="stayl-sb-item-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                </div>
                <h5 class="stayl-sb-item-text">@lang('Home Page')</h5>
            </a>
            
            <a href="{{ route('products') }}" class="stayl-sb-item">
                <div class="stayl-sb-item-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h1.49l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                </div>
                <h5 class="stayl-sb-item-text">@lang('Shop Products')</h5>
            </a>

            <div class="stayl-sb-divider-text">@lang('Support & Orders')</div>
            
            <a href="{{ route('track.order') }}" class="stayl-sb-item">
                <div class="stayl-sb-item-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                </div>
                <h5 class="stayl-sb-item-text">@lang('Track My Order')</h5>
            </a>

            <a href="{{ route('contact') }}" class="stayl-sb-item">
                <div class="stayl-sb-item-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </div>
                <h5 class="stayl-sb-item-text">@lang('Customer Support')</h5>
            </a>

            {{-- Action Area --}}
            <div class="stayl-sb-action-box">
                @guest
                <a href="{{ route('user.login') }}" class="stayl-btn-dark">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 0 0-16 0"></path></svg>
                    @lang('LOGIN TO ACCOUNT')
                </a>
                <a href="{{ route('user.register') }}" class="stayl-btn-outline">
                    @lang('CREATE NEW ACCOUNT')
                </a>
                @else
                <a href="{{ route('user.home') }}" class="stayl-btn-dark" style="background: var(--stayl-active-blue);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    @lang('VIEW DASHBOARD')
                </a>
                @endguest
            </div>
            
        </div>
    </div>
</div>
<script>
    (function() {
        function syncHeaderHeightVar() {
            const header = document.querySelector('.stayl-fixed-master');
            if (!header) return;
            const h = Math.max(0, Math.ceil(header.offsetHeight || 0));
            if (h > 0) {
                document.documentElement.style.setProperty('--stayl-dynamic-header-height', h + 'px');
            }
        }
        function setupHeaderScrollCollapse() {
            const header = document.querySelector('.stayl-fixed-master');
            if (!header) return;
            let lastY = window.scrollY || 0;
            let hidden = false;
            let ticking = false;
            let lockUntil = 0;
            const SHOW_AT = 70;
            const HIDE_AT = 180;
            const DELTA = 8;
            const LOCK_MS = 1300;
            const onScroll = function () {
                if (ticking) return;
                ticking = true;
                window.requestAnimationFrame(function () {
                const y = window.scrollY || window.pageYOffset || 0;
                const movingDown = y > lastY + DELTA;
                const movingUp = y < lastY - DELTA;
                const now = Date.now();

                if (now >= lockUntil && !hidden && movingDown && y > HIDE_AT) {
                    hidden = true;
                    header.classList.add('is-scrolled-down');
                    lockUntil = now + LOCK_MS;
                } else if (now >= lockUntil && hidden && (movingUp || y < SHOW_AT)) {
                    hidden = false;
                    header.classList.remove('is-scrolled-down');
                    lockUntil = now + LOCK_MS;
                }

                lastY = y;
                syncHeaderHeightVar();
                ticking = false;
                });
            };
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });
        }

        // Vanilla JS Toggle to avoid dependency issues
        function setupStaylSidebar() {
            const triggers = document.querySelectorAll('.stayl-sidebar-trigger');
            const sidebar = document.getElementById('glassSidebar');
            const closeBtn = document.getElementById('glassSidebarClose');
            const overlay = document.querySelector('.glass-sidebar-overlay');

            if (!sidebar) return;

            triggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    sidebar.classList.add('active');
                    document.body.style.overflow = 'hidden'; // Prevent scroll
                });
            });

            const closeAction = function() {
                sidebar.classList.remove('active');
                document.body.style.overflow = '';
            };

            if (closeBtn) closeBtn.addEventListener('click', closeAction);
            if (overlay) overlay.addEventListener('click', closeAction);
        }

        // Initialize
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setupStaylSidebar();
                syncHeaderHeightVar();
                setupHeaderScrollCollapse();
            });
        } else {
            setupStaylSidebar();
            syncHeaderHeightVar();
            setupHeaderScrollCollapse();
        }
        window.addEventListener('resize', syncHeaderHeightVar, { passive: true });
        window.addEventListener('load', syncHeaderHeightVar, { once: true });

        const baseCurrency = {
            code: @json($general->cur_text ?? 'BDT'),
            symbol: @json($general->cur_sym ?? '৳')
        };
        const headerCurrencyLabel = document.getElementById('staylCurrentCurrencyLabel');
        const headerLanguageLabel = document.getElementById('staylCurrentLanguageLabel');
        const currencySymbols = {
            BDT: '৳', USD: '$', EUR: '€', GBP: '£', INR: '₹', AED: 'د.إ', SAR: 'ر.س', MYR: 'RM', SGD: 'S$', JPY: '¥'
        };
        const fallbackRates = {
            BDT: 1, USD: 0.0082, EUR: 0.0075, GBP: 0.0064, INR: 0.69, AED: 0.03, SAR: 0.031, MYR: 0.039, SGD: 0.011, JPY: 1.24
        };
        const priceSelectors = [
            '.staylbd-rt-price', '.price', '.old-price', '.qv-price', '.qv-price-old',
            '.subtotal-price', '.total-price', '.discount-price', '.grand-total-price',
            '.checkout-shipping-charge', '.track-quick-btn__amount', '.pro-detail-special-price',
            '.pro-detail-regular-price', '.sticky-add-to-cart-bar__price'
        ];

        function parseAmountFromText(text, sym) {
            if (!text) return null;
            const cleaned = text.replace(new RegExp('\\' + sym, 'g'), '').replace(/[^0-9.,-]/g, '').replace(/,/g, '');
            const n = parseFloat(cleaned);
            return Number.isFinite(n) ? n : null;
        }
        function formatAmount(value) {
            return (Math.round((value + Number.EPSILON) * 100) / 100).toFixed(2);
        }
        function convertDisplayedPrices(targetCode, targetRate) {
            const targetSym = currencySymbols[targetCode] || targetCode + ' ';
            const elements = new Set();
            priceSelectors.forEach(sel => document.querySelectorAll(sel).forEach(el => elements.add(el)));
            elements.forEach(el => {
                if (!el.dataset.staylBaseText) el.dataset.staylBaseText = (el.textContent || '').trim();
                const baseText = el.dataset.staylBaseText || '';
                const baseAmount = parseAmountFromText(baseText, baseCurrency.symbol);
                if (baseAmount === null) return;
                const converted = formatAmount(baseAmount * targetRate);
                el.textContent = targetSym + converted;
            });
        }
        async function loadRates(baseCode) {
            try {
                const res = await fetch('https://open.er-api.com/v6/latest/' + encodeURIComponent(baseCode), { method: 'GET' });
                const data = await res.json();
                if (data && data.result === 'success' && data.rates) return data.rates;
            } catch (err) {}
            return null;
        }
        async function initDisplayCurrency() {
            const sel = document.getElementById('staylDisplayCurrency');
            if (!sel) return;
            const saved = localStorage.getItem('stayl_display_currency_code');
            if (saved) sel.value = saved;
            let rates = await loadRates(baseCurrency.code);
            if (!rates) rates = fallbackRates;
            function applyNow(code) {
                if (!code || code === baseCurrency.code) {
                    convertDisplayedPrices(baseCurrency.code, 1);
                    if (headerCurrencyLabel) headerCurrencyLabel.textContent = baseCurrency.code;
                    return;
                }
                const rate = Number(rates[code] ?? fallbackRates[code] ?? 1);
                convertDisplayedPrices(code, Number.isFinite(rate) && rate > 0 ? rate : 1);
                if (headerCurrencyLabel) headerCurrencyLabel.textContent = code;
            }
            applyNow(sel.value || baseCurrency.code);
            sel.addEventListener('change', function() {
                const code = this.value || baseCurrency.code;
                localStorage.setItem('stayl_display_currency_code', code);
                applyNow(code);
                setTimeout(function () { window.location.reload(); }, 120);
            });
        }
        function initDisplayLanguageLabel() {
            if (!headerLanguageLabel) return;
            const savedLang = localStorage.getItem('stayl_display_language_code');
            if (savedLang) headerLanguageLabel.textContent = savedLang;
            document.querySelectorAll('[data-stayl-lang-option]').forEach(function (el) {
                el.addEventListener('click', function () {
                    const code = this.getAttribute('data-stayl-lang-option');
                    if (code) localStorage.setItem('stayl_display_language_code', code);
                });
            });
        }
        initDisplayLanguageLabel();
        initDisplayCurrency();
    })();
</script>
