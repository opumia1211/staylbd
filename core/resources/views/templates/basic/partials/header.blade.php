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
            static fn() => \App\Models\Category::active()
                ->with(['subcategories' => static fn($q) => $q->active()])
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
    $isUserProfileHome = request()->routeIs('user.home') || request()->is('user');
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
    $menuNavLinks = is_array($headerMenuCfg['nav_links'] ?? null) ? $headerMenuCfg['nav_links'] : [];
    $menuCategoryItems = is_array($headerMenuCfg['category_items'] ?? null) ? $headerMenuCfg['category_items'] : [];
    if (!empty($menuCustomButtons)) {
        $menuNavLinks = array_merge($menuNavLinks, $menuCustomButtons);
    }
    usort($menuNavLinks, static function (array $a, array $b): int {
        return (int) ($a['display_order'] ?? 0) <=> (int) ($b['display_order'] ?? 0);
    });
    $headerTrackKey = static function ($label, $fallback = 'header-link'): string {
        $label = strtolower(trim((string) $label));
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $label);
        $slug = trim((string) $slug, '-');
        return $slug !== '' ? mb_substr($slug, 0, 80) : $fallback;
    };
    $renderHeaderDropdownItems = function (array $items, int $depth = 1) use (&$renderHeaderDropdownItems): string {
        if (empty($items) || $depth > 4) {
            return '';
        }
        $html = '<ul class="stayl-menu-tree stayl-menu-tree--depth-' . $depth . '">';
        foreach ($items as $item) {
            $label = trim((string) ($item['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $url = trim((string) ($item['url'] ?? '#')) ?: '#';
            $children = is_array($item['children'] ?? null) ? $item['children'] : [];
            $hasChildren = !empty($children);
            $track = preg_replace('/[^a-z0-9]+/i', '-', strtolower($label));
            $track = trim((string) $track, '-');
            $track = $track !== '' ? mb_substr($track, 0, 80) : 'header-dropdown-item';
            $html .= '<li class="stayl-menu-item' . ($hasChildren ? ' has-children' : '') . '">';
            $html .= '<a href="' . e($url) . '" data-header-track="' . e($track) . '">' . e(__($label)) . '</a>';
            if ($hasChildren) {
                $html .= $renderHeaderDropdownItems($children, $depth + 1);
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    };

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
    if (trim((string) ($headerTopCfg['support_email'] ?? '')) !== '') {
        $headerContactEmail = trim((string) $headerTopCfg['support_email']);
    }
    if ($headerContactPhone === '') {
        $headerContactPhone = '888-777-999';
    }
    if ($headerContactEmail === '') {
        $headerContactEmail = 'support@staylbd.com';
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
        --stayl-h0:
            {{ (int) ($headerAppearance['top_height'] ?? 38) }}
            px;
        --stayl-h1:
            {{ (int) ($headerAppearance['main_height'] ?? 56) }}
            px;
        --stayl-h2:
            {{ (int) ($headerAppearance['menu_height'] ?? 38) }}
            px;
        --stayl-yellow: var(--stayl-color-header-menu, #0e9f90);
        --stayl-active-blue: var(--header-accent-color, #2eb4e7);
        --stayl-bg-light: #f1f3f5;
        --stayl-icon-gray: color-mix(in srgb, var(--header-bg, #ffffff) 84%, #e2e8f0);
        --stayl-header-top-bg: var(--stayl-color-header-main, #ffffff);
        --stayl-header-menu-bg: var(--stayl-color-header-menu, #0e9f90);
        --stayl-header-button-bg: var(--product-button-color, #111111);
        --stayl-header-accent: var(--product-buy-now-color, var(--stayl-active-blue));
        --stayl-header-badge-bg: var(--product-discount-badge, #ff4d4d);
        --stayl-header-top-glass-bg:
            {{ $headerAppearance['top_bg'] ?? '#0f172a' }}
        ;
        --stayl-header-main-glass-bg:
            {{ $headerAppearance['main_bg'] ?? '#f8fafc' }}
        ;
        --stayl-header-menu-glass-bg:
            {{ $headerAppearance['menu_bg'] ?? '#c7eafe' }}
        ;
        --stayl-header-glass-border: rgba(226, 232, 240, 0.22);
        --stayl-header-glass-text: #f8fafc;
        --stayl-header-top-text: #f8fafc;
        --stayl-header-main-text: #0f172a;
        --stayl-header-menu-text: #0f172a;
        --stayl-main-icon-size:
            {{ (int) ($headerMainCfg['icon_size'] ?? 48) }}
            px;
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
        transition: transform 1.3s cubic-bezier(.23, 1, .32, 1), box-shadow 1s ease;
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
        transition: max-height 1.3s cubic-bezier(.23, 1, .32, 1), opacity 1.15s ease, transform 1.3s cubic-bezier(.23, 1, .32, 1), border-color 1.1s ease;
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
        transition: max-height 1.3s cubic-bezier(.23, 1, .32, 1), opacity 1.15s ease, transform 1.3s cubic-bezier(.23, 1, .32, 1), border-color 1.1s ease;
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
        transform: translateY(0);
        transition: transform 1.3s cubic-bezier(.23, 1, .32, 1), box-shadow 1s ease;
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
        transition: opacity 0.2s, transform 0.2s ease;
    }

    .stayl-announcement-link:hover {
        opacity: 1;
        text-decoration: none !important;
        transform: translateY(-1px);
    }

    .stayl-top-contact-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 3px 6px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.16);
    }

    .stayl-top-contact-icon3d {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at 28% 28%, #ffffff 0%, #dbeafe 36%, #93c5fd 100%);
        color: #0f172a;
        border: 1px solid rgba(15, 23, 42, 0.18);
        box-shadow: 0 2px 8px rgba(2, 6, 23, 0.25);
        flex-shrink: 0;
    }

    .stayl-top-contact-text {
        font-size: 13px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: 0.1px;
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
        transition: color 0.2s ease, transform 0.2s ease, opacity 0.2s ease;
    }

    .stayl-topbar-menu__btn:hover {
        color: #ffffff !important;
        transform: translateY(-2px);
        opacity: 1;
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
        width: 180px;
        min-width: 180px;
        max-width: 180px;
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

    .stayl-topbar-menu__item.is-active {
        background: #f8fafc;
        font-weight: 700;
    }

    .stayl-topbar-menu__check {
        opacity: 0;
        transition: opacity 0.16s ease;
    }

    .stayl-topbar-menu__item.is-active .stayl-topbar-menu__check {
        opacity: 1;
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
        border: 1px solid rgba(255, 255, 255, .2);
        background: rgba(255, 255, 255, .08);
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

    .stayl-topbar-menu__panel--apps {
        width: 210px;
        min-width: 210px;
        max-width: 210px;
        padding: 1px;
    }

    .stayl-app-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1px;
        max-height: none;
        overflow: visible;
    }

    .stayl-app-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1px;
        padding: 5px 2px;
        border-radius: 0;
        text-decoration: none !important;
        color: #0f172a !important;
    }

    .stayl-app-item:hover {
        background: rgba(15, 23, 42, 0.04);
    }

    .stayl-app-item__icon {
        width: 40px;
        height: 40px;
        border-radius: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: 0;
        overflow: hidden;
    }

    .stayl-app-item__icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .stayl-app-item__icon-svg {
        width: 100%;
        height: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #111827;
    }

    .stayl-app-item__icon-svg svg {
        width: 36px;
        height: 36px;
        stroke: currentColor;
        fill: none;
        stroke-width: 1.7;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .stayl-app-item__fallback {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
    }

    .stayl-app-item__label {
        max-width: 100%;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.05;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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
        min-height: 48px;
        height: 48px;
        padding: 0 8px;
        padding-right: 8px;
        transition: 0.3s;
    }

    .stayl-search-pill:focus-within {
        background: #ffffff;
        border-color: rgba(14, 116, 144, 0.65);
        box-shadow: none;
    }

    .stayl-search-input,
    .stayl-search-pill input[type="text"],
    #staylHeaderSearchInput {
        flex: 1;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        padding: 0 18px !important;
        font-size: 18px !important;
        font-weight: 400 !important;
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif !important;
        color: #0f172a !important;
        width: 100%;
        box-shadow: none !important;
        line-height: 1.4 !important;
        letter-spacing: 0 !important;
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
        font-size: 17px !important;
        font-weight: 400 !important;
        font-family: Arial, "Helvetica Neue", Helvetica, sans-serif !important;
    }

    @media (max-width: 991.98px) {

        .stayl-search-input,
        .stayl-search-pill input[type="text"],
        #staylHeaderSearchInput {
            font-size: 17px !important;
        }
    }

    .stayl-search-actions-inner {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-right: 10px;
        position: relative;
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

    #voiceSearchBtn {
        cursor: pointer;
        color: #555;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s ease;
        border: 0;
        background: transparent;
        padding: 0;
    }

    #voiceSearchBtn.is-listening {
        color: #ef4444;
    }

    #voiceSearchBtn:hover,
    #cameraSearchBtn:hover,
    .stayl-account-icon:hover,
    .stayl-account-icon:hover svg {
        color: var(--header-accent-color, #0ea5e9) !important;
        stroke: var(--header-accent-color, #0ea5e9) !important;
    }

    #cameraSearchBtn {
        width: 34px;
        height: 34px;
        border-radius: 0;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        box-shadow: none;
        transition: 0.2s;
        flex-shrink: 0;
        color: #0f172a;
    }

    #cameraSearchBtn svg,
    #voiceSearchBtn svg,
    .stayl-account-icon svg {
        transition: color 0.2s ease, stroke 0.2s ease;
    }

    #voiceSearchBtn.is-listening svg {
        color: #ef4444 !important;
        stroke: #ef4444 !important;
    }

    .stayl-voice-status {
        position: absolute;
        top: calc(100% + 4px);
        right: 0;
        font-size: 11px;
        line-height: 1.2;
        color: #ef4444;
        background: #fff1f2;
        border: 1px solid #fecdd3;
        border-radius: 6px;
        padding: 3px 6px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }

    .stayl-voice-status.is-visible {
        opacity: 1;
    }

    .stayl-camera-modal {
        position: fixed;
        inset: 0;
        z-index: 100950;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .stayl-camera-modal.is-open {
        display: flex;
    }

    .stayl-camera-modal__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
    }

    .stayl-camera-modal__dialog {
        position: relative;
        z-index: 1;
        width: min(92vw, 420px);
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid rgba(15, 23, 42, 0.14);
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.24);
        padding: 14px;
    }

    .stayl-camera-modal__video {
        width: 100%;
        aspect-ratio: 4 / 3;
        border-radius: 8px;
        background: #020617;
        object-fit: cover;
    }

    .stayl-camera-modal__actions {
        margin-top: 10px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .stayl-camera-modal__btn {
        border: 1px solid rgba(15, 23, 42, 0.2);
        background: #ffffff;
        color: #0f172a;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
    }

    .stayl-camera-modal__btn--primary {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
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

    .stayl-nav-ul>li>a {
        color: #0f172a !important;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none !important;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .stayl-nav-ul>li>a:hover {
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
        background: #111827 !important;
        border: 0 !important;
        border-left: 0 !important;
        box-shadow: none !important;
        min-height: 32px !important;
        height: 32px !important;
        line-height: 1 !important;
        margin: 0 !important;
        padding: 0 14px !important;
        position: relative;
        overflow: hidden;
        align-self: center !important;
        transform: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 6px !important;
        white-space: nowrap !important;
    }

    .stayl-seller-btn--top svg {
        color: #f8fafc !important;
    }

    .stayl-seller-btn--top::before,
    .stayl-seller-btn--top::after {
        content: none !important;
        display: none !important;
    }

    .stayl-announcement-bar .stayl-wrap>.d-flex.align-items-center.gap-3:last-child {
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

    .stayl-top-bar .stayl-wrap>.d-flex.align-items-center:first-child {
        flex-shrink: 0;
    }

    .stayl-top-bar .stayl-wrap>.flex.flex-1.items-center.justify-center.gap-2 {
        min-width: 0;
    }

    .stayl-top-bar .stayl-wrap {
        min-height: 100%;
        align-items: center;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
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
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
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
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
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
        transform: none !important;
    }

    .stayl-menu-tree {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .stayl-menu-item {
        position: relative;
    }

    .stayl-menu-item>a {
        display: block;
        padding: 10px 14px;
        font-size: 14px;
        font-weight: 700;
        color: #111 !important;
        text-decoration: none !important;
        border-radius: 8px;
    }

    .stayl-menu-item>a:hover {
        background: #f8fafc;
        color: var(--stayl-header-accent) !important;
    }

    .stayl-menu-tree--depth-2,
    .stayl-menu-tree--depth-3,
    .stayl-menu-tree--depth-4 {
        position: absolute;
        top: 0;
        left: calc(100% + 6px);
        min-width: 220px;
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-radius: 10px;
        padding: 6px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
        opacity: 0;
        visibility: hidden;
        transform: translateY(6px);
        transition: all 0.2s ease;
        z-index: 10020;
    }

    .stayl-menu-item.has-children:hover>.stayl-menu-tree {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .stayl-pages-dropdown--mega .stayl-menu-tree--depth-1 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .stayl-pages-dropdown--mega .stayl-menu-item.has-children>.stayl-menu-tree--depth-2 {
        position: static;
        min-width: 0;
        border: 0;
        border-radius: 0;
        padding: 4px 0 0;
        margin-top: 4px;
        box-shadow: none;
        opacity: 1;
        visibility: visible;
        transform: none;
        background: transparent;
    }

    .stayl-pages-dropdown--mega .stayl-menu-tree--depth-2 .stayl-menu-item>a {
        background: #ffffff;
        border: 1px solid #edf2f7;
        font-size: 13px;
        font-weight: 600;
        padding: 8px 10px;
    }

    .stayl-pages-dropdown--mega .stayl-menu-tree--depth-2 .stayl-menu-item>a:hover {
        background: #eef6ff;
    }

    .stayl-pages-dropdown--mega .stayl-menu-item>a {
        background: #f8fafc;
    }

    .stayl-pages-dropdown.stayl-pages-dropdown--mega {
        width: min(560px, 85vw);
        padding: 14px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        border-radius: 14px;
    }

    .stayl-pages-dropdown.stayl-pages-dropdown--mega a {
        padding: 10px 12px;
        border-radius: 10px;
        background: #f8fafc;
        font-size: 14px;
    }

    .stayl-pages-dropdown.stayl-pages-dropdown--mega a:hover {
        padding-left: 12px;
        background: #eef6ff;
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
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
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
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
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
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
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
    @if(!$isUserProfileHome && !empty($headerBarVisible['header_bar_top_notice']) && !empty($headerTopCfg['enabled']))
        <div class="stayl-announcement-bar" style="order: {{ $headerBarOrderIndex['header_bar_top_notice'] ?? 1 }};">
            <div class="stayl-wrap">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ $headerContactPhone !== '' ? 'tel:' . preg_replace('/[^0-9+]/', '', $headerContactPhone) : route('contact') }}"
                        class="stayl-announcement-link stayl-top-contact-link">
                        <span class="stayl-top-contact-icon3d" aria-hidden="true">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                </path>
                            </svg>
                        </span>
                        <span class="stayl-top-contact-text">{{ $headerContactPhone }}</span>
                    </a>
                    <span class="d-none d-md-inline">|</span>
                    <a href="{{ $headerContactEmail !== '' ? 'mailto:' . $headerContactEmail : route('contact') }}"
                        class="stayl-announcement-link stayl-top-contact-link">
                        <span class="stayl-top-contact-icon3d" aria-hidden="true">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2">
                                <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                <path d="m3 7 9 6 9-6"></path>
                            </svg>
                        </span>
                        <span class="stayl-top-contact-text">{{ $headerContactEmail }}</span>
                    </a>
                    <span class="d-none d-md-inline">|</span>
                    <span>{{ __($headerCodNoticeText) }}</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @if(!empty($headerTopCfg['show_language']) && $headerLanguages->isNotEmpty())
                        <div class="stayl-topbar-menu">
                            <button type="button" class="stayl-topbar-menu__btn">
                                <span id="staylCurrentLanguageLabel">{{ $languageButtonLabel }}</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </button>
                            <div class="stayl-topbar-menu__panel">
                                @foreach($headerLanguages as $lng)
                                    <a href="{{ route('lang', $lng->code) }}" class="stayl-topbar-menu__item"
                                        data-stayl-lang-option="{{ strtoupper($lng->code) }}">
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
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </button>
                            <div class="stayl-topbar-menu__panel">
                                @php
                                    $headerCurrencyCodes = [$general->cur_text ?? 'BDT', 'USD', 'EUR', 'GBP', 'INR', 'AED', 'SAR', 'MYR', 'SGD', 'JPY'];
                                    $headerCurrencyCodes = collect($headerCurrencyCodes)->map(static fn($c) => strtoupper(trim((string) $c)))->filter()->unique()->values();
                                    $headerCurrencySymbols = ['BDT' => '৳', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'INR' => '₹', 'AED' => 'د.إ', 'SAR' => 'ر.س', 'MYR' => 'RM', 'SGD' => 'S$', 'JPY' => '¥'];
                                    $defaultCurrencyCode = strtoupper((string) ($general->cur_text ?? 'BDT'));
                                @endphp
                                @foreach($headerCurrencyCodes as $code)
                                    @php $currencySymbol = $headerCurrencySymbols[$code] ?? $general->cur_sym ?? '৳'; @endphp
                                    <a href="#"
                                        class="stayl-topbar-menu__item {{ $code === $defaultCurrencyCode ? 'is-active' : '' }}"
                                        data-stayl-currency-option="{{ $code }}">
                                        <span>{{ $code }} ({{ $currencySymbol }})</span>
                                        <span class="stayl-topbar-menu__check">✓</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('user.register') }}" class="stayl-topbar-menu__btn text-decoration-none">
                        @lang('Registration')
                    </a>

                    @if(!empty($headerTopCfg['show_apps']))
                        <div class="stayl-topbar-menu">
                            <button type="button" class="stayl-topbar-menu__btn">
                                @lang('Apps')
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </button>
                            <div class="stayl-topbar-menu__panel stayl-topbar-menu__panel--apps">
                                <div class="stayl-app-grid">
                                    @forelse($headerAppItems as $item)
                                        @php
                                            $dv = is_array($item->data_values ?? null) ? (object) $item->data_values : ($item->data_values ?? (object) []);
                                            $itemPlatform = trim((string) ($dv->platform ?? 'App'));
                                            $itemLink = trim((string) ($dv->link ?? '#')) ?: '#';
                                            $itemFile = trim((string) ($dv->app_file ?? ''));
                                            $itemHref = $itemFile !== '' ? asset('assets/files/frontend/apps/' . ltrim($itemFile, '/')) : $itemLink;
                                            $itemImage = trim((string) ($dv->image ?? $dv->qr_image ?? ''));
                                            $itemIcon = $itemImage !== '' ? getImage('assets/images/frontend/footer/' . ltrim($itemImage, '/'), '68x68') : '';
                                            $platformLower = strtolower($itemPlatform);
                                            $itemBrandIcon = str_contains($platformLower, 'android') ? 'android'
                                                : (str_contains($platformLower, 'ios') || str_contains($platformLower, 'iphone') || str_contains($platformLower, 'apple') || str_contains($platformLower, 'mac') ? 'apple'
                                                    : ((str_contains($platformLower, 'windows') || str_contains($platformLower, 'desktop') || str_contains($platformLower, 'pc') || str_contains($platformLower, 'web')) ? 'desktop'
                                                        : (str_contains($platformLower, 'linux') ? 'linux' : 'mobile')));
                                        @endphp
                                        <a href="{{ $itemHref }}" class="stayl-app-item" @if($itemFile !== '') download @endif
                                            target="_blank" rel="noopener">
                                            <span class="stayl-app-item__icon">
                                                @if($itemIcon !== '')
                                                    <img src="{{ $itemIcon }}" alt="{{ $itemPlatform }}">
                                                @else
                                                    <span class="stayl-app-item__icon-svg" aria-hidden="true">
                                                        @if($itemBrandIcon === 'android')
                                                            <svg viewBox="0 0 24 24">
                                                                <path d="M7 9h10v7a2 2 0 0 1-2 2h-1v2h-1v-2h-2v2h-1v-2H9a2 2 0 0 1-2-2V9z">
                                                                </path>
                                                                <path d="M8 9V8a4 4 0 0 1 8 0v1"></path>
                                                                <line x1="9" y1="5" x2="7.5" y2="3.5"></line>
                                                                <line x1="15" y1="5" x2="16.5" y2="3.5"></line>
                                                                <circle cx="10" cy="12" r=".7" fill="currentColor" stroke="none"></circle>
                                                                <circle cx="14" cy="12" r=".7" fill="currentColor" stroke="none"></circle>
                                                            </svg>
                                                        @elseif($itemBrandIcon === 'apple')
                                                            <svg viewBox="0 0 24 24">
                                                                <path
                                                                    d="M15.2 5.1c.8-1 .7-2 .7-2s-1.5.1-2.4 1.1c-.8.8-.9 1.9-.9 1.9s1.7.2 2.6-1Z">
                                                                </path>
                                                                <path
                                                                    d="M12.2 7.4c1.2 0 1.8.7 2.7.7.8 0 1.2-.7 2.3-.7 1.4 0 2.8.9 3.5 2.3-1.2.7-1.8 1.8-1.8 3.1 0 1.5.8 2.6 1.9 3.2-.5 1.4-1.3 2.8-2.7 2.8-.8 0-1.4-.5-2.3-.5-.9 0-1.6.5-2.5.5-1.6 0-2.6-1.6-3.2-3-.5-1-.8-2.1-.8-3.2 0-3.1 2-5.2 4.9-5.2Z">
                                                                </path>
                                                            </svg>
                                                        @elseif($itemBrandIcon === 'desktop')
                                                            <svg viewBox="0 0 24 24">
                                                                <rect x="3" y="4" width="18" height="12" rx="2"></rect>
                                                                <line x1="8" y1="20" x2="16" y2="20"></line>
                                                                <line x1="12" y1="16" x2="12" y2="20"></line>
                                                            </svg>
                                                        @elseif($itemBrandIcon === 'linux')
                                                            <svg viewBox="0 0 24 24">
                                                                <path
                                                                    d="M12 4c1.6 0 3 1.4 3 3v2l1.2 1.6c.5.6.8 1.4.8 2.2V17a3 3 0 0 1-3 3H10a3 3 0 0 1-3-3v-4.2c0-.8.3-1.6.8-2.2L9 9V7c0-1.6 1.4-3 3-3Z">
                                                                </path>
                                                                <circle cx="10.5" cy="10.5" r=".5"></circle>
                                                                <circle cx="13.5" cy="10.5" r=".5"></circle>
                                                                <path d="M10 14c.5.4 1.1.6 2 .6s1.5-.2 2-.6"></path>
                                                            </svg>
                                                        @else
                                                            <svg viewBox="0 0 24 24">
                                                                <rect x="7" y="3" width="10" height="18" rx="2"></rect>
                                                                <circle cx="12" cy="17" r="1"></circle>
                                                            </svg>
                                                        @endif
                                                    </span>
                                                @endif
                                            </span>
                                            <span class="stayl-app-item__label">{{ $itemPlatform }}</span>
                                        </a>
                                    @empty
                                        <span class="stayl-topbar-menu__item text-muted">@lang('No apps added')</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!empty($headerTopCfg['show_seller_button']))
                        <a href="{{ url($headerTopCfg['seller_url'] ?? '/seller/apply') }}"
                            class="stayl-seller-btn stayl-seller-btn--top">
                            {{ __($headerTopCfg['seller_text'] ?? 'BECOME A SELLER') }}
                        </a>
                    @endif
                    @foreach($topCustomButtons as $tbtn)
                        @php
                            $tbtnLabel = trim((string) ($tbtn['label'] ?? ''));
                            $tbtnUrl = trim((string) ($tbtn['url'] ?? '#')) ?: '#';
                            $tbtnType = (string) ($tbtn['type'] ?? 'link');
                            $tbtnActive = (int) ($tbtn['is_active'] ?? 1) === 1;
                            $tbtnTrack = trim((string) ($tbtn['tracking_key'] ?? ''));
                            if ($tbtnTrack === '') {
                                $tbtnTrack = $headerTrackKey($tbtnLabel, 'top-button');
                            }
                            $tbtnItems = is_array($tbtn['items'] ?? null) ? $tbtn['items'] : [];
                        @endphp
                        @continue($tbtnLabel === '' || !$tbtnActive)
                        @if($tbtnType === 'dropdown' && !empty($tbtnItems))
                            <div class="stayl-topbar-menu">
                                <button type="button" class="stayl-topbar-menu__btn">
                                    {{ __($tbtnLabel) }}
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="m6 9 6 6 6-6"></path>
                                    </svg>
                                </button>
                                <div class="stayl-topbar-menu__panel">
                                    @foreach($tbtnItems as $tbItem)
                                        <a href="{{ trim((string) ($tbItem['url'] ?? '#')) ?: '#' }}" class="stayl-topbar-menu__item"
                                            data-header-track="{{ $tbtnTrack }}-item-{{ $loop->iteration }}">
                                            <span>{{ __(trim((string) ($tbItem['label'] ?? 'Item'))) }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $tbtnUrl }}" class="stayl-topbar-menu__btn text-decoration-none"
                                data-header-track="{{ $tbtnTrack }}">{{ __($tbtnLabel) }}</a>
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
                            <img src="{{ $logo }}" alt="Staylbd"
                                style="max-height: {{ (int) ($headerMainCfg['logo_max_height'] ?? 48) }}px; width: auto;">
                        @else
                            <span
                                style="font-size: 34px; font-weight: 900; color: #111; letter-spacing: -2px;">{{ strtoupper(gs('site_name')) }}</span>
                        @endif
                    </a>
                </div>

                {{-- Search Pill & External Lens --}}
                <div class="flex flex-1 items-center justify-center gap-2"> {{-- Tight gap for unified look --}}
                    <form action="{{ route('products') }}" method="GET" class="stayl-search-pill"
                        style="margin: 0 !important; flex: 1; max-width: 620px;">
                        <input type="text" id="staylHeaderSearchInput" name="search" class="stayl-search-input"
                            placeholder="@lang('Search products, brands, and more')..."
                            value="{{ request()->search ?? null }}" autocomplete="off">
                        <div class="stayl-search-actions-inner" style="gap: 12px; margin-right: 8px;">
                            {{-- Voice Search --}}
                            <button type="button" id="voiceSearchBtn" title="Voice Search" aria-label="Voice Search">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.2">
                                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path>
                                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                    <line x1="12" y1="19" x2="12" y2="22"></line>
                                </svg>
                            </button>
                            <span id="staylVoiceStatus" class="stayl-voice-status" aria-live="polite"></span>
                            {{-- Search Submit --}}
                            <button type="submit" class="stayl-search-icon-btn" style="width: 48px; height: 48px;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </button>
                        </div>
                    </form>
                    {{-- Professional Lens Icon (Outside but Right Next to Search) --}}
                    <button type="button" id="cameraSearchBtn" title="Camera Search" aria-label="Camera Search">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 7V5a2 2 0 0 1 2-2h2"></path>
                            <path d="M17 3h2a2 2 0 0 1 2 2v2"></path>
                            <path d="M21 17v2a2 2 0 0 1-2 2h-2"></path>
                            <path d="M7 21H5a2 2 0 0 1-2-2v-2"></path>
                            <circle cx="12" cy="12" r="3" stroke-width="2"></circle>
                        </svg>
                    </button>
                </div>

                {{-- Action Icons (Premium SVG Set) --}}
                <div class="stayl-action-row">
                    <a href="{{ route('user.order.index') }}" class="stayl-icon-item" title="Orders" data-dashboard-nav="1">
                        {{-- Lucide: shopping-bag --}}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                            <path d="M3 6h18"></path>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                    </a>
                    <a href="{{ url('/user/contact') }}" class="stayl-icon-item" title="Contact" data-dashboard-nav="1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                            </path>
                        </svg>
                    </a>
                    <a href="{{ url('/user/ordertrack') }}" class="stayl-icon-item" title="Track Order"
                        data-dashboard-nav="1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="1" y="3" width="15" height="13"></rect>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                        </svg>
                    </a>
                    <a href="{{ route('user.wishlist') }}" class="stayl-icon-item" title="Wishlist" data-dashboard-nav="1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path
                                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
                            </path>
                        </svg>
                        <span class="stayl-badge show-wishlist-count">0</span>
                    </a>
                    <a href="{{ route('user.compare') }}" class="stayl-icon-item" title="Compare" data-dashboard-nav="1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="m16 4 4 4-4 4"></path>
                            <path d="M20 8H4"></path>
                            <path d="m8 20-4-4 4-4"></path>
                            <path d="M4 16h16"></path>
                        </svg>
                        <span class="stayl-badge show-compare-count">0</span>
                    </a>
                    <a href="{{ route('user.cart') }}" class="stayl-icon-item" title="Cart" data-dashboard-nav="1">
                        {{-- Lucide: shopping-cart --}}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="8" cy="21" r="1"></circle>
                            <circle cx="19" cy="21" r="1"></circle>
                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12">
                            </path>
                        </svg>
                        <span class="stayl-badge show-cart-count">0</span>
                    </a>
                    <a href="{{ route('user.home') }}" class="stayl-icon-item stayl-account-icon"
                        style="background:transparent !important;border:none !important;color:#0f172a !important;"
                        title="Account" data-dashboard-nav="1">
                        {{-- Lucide: user-round --}}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle cx="12" cy="8" r="5"></circle>
                            <path d="M20 21a8 8 0 0 0-16 0"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Row 2: Secondary Nav Bar --}}
    @if(!$isUserProfileHome && !empty($headerBarVisible['header_bar_menu']) && !empty($headerMenuCfg['enabled']))
        <div class="stayl-yellow-bar" style="order: {{ $headerBarOrderIndex['header_bar_menu'] ?? 3 }};">
            <div class="stayl-wrap">
                <nav class="d-flex align-items-center h-100" style="gap: clamp(12px, 1.5vw, 24px);">
                    @if(!empty($headerMenuCfg['show_sidebar_trigger']))
                        {{-- Separate Hamburger Button --}}
                        <div class="stayl-sidebar-trigger cursor-pointer flex items-center justify-center"
                            style="width: 42px; height: 42px; transition: 0.3s; color: #0f172a;"
                            onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1)';"
                            title="Open Menu">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <line x1="3" y1="12" x2="21" y2="12"></line>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <line x1="3" y1="18" x2="21" y2="18"></line>
                            </svg>
                        </div>
                    @endif

                    @if(!empty($headerMenuCfg['show_category_button']))
                        {{-- All Categories Button (Separate) --}}
                        <div class="h-100 position-relative" id="staylCatContainer">
                            <button class="stayl-cat-btn" id="staylCatBtn" style="padding: 0; gap: 10px;">
                                {{ __($headerMenuCfg['category_button_label'] ?? 'ALL CATEGORIES') }}
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="3">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </button>
                            @if(!empty($menuCategoryItems) || $__staylHeaderCategories->isNotEmpty())
                                <div class="stayl-cat-dropdown" id="staylCatDropdown">
                                    <ul>
                                        @if(!empty($menuCategoryItems))
                                            @foreach($menuCategoryItems as $catItem)
                                                @php
                                                    $catItemLabel = trim((string) ($catItem['label'] ?? ''));
                                                    $catItemUrl = trim((string) ($catItem['url'] ?? '#')) ?: '#';
                                                @endphp
                                                @continue($catItemLabel === '')
                                                <li>
                                                    <a href="{{ $catItemUrl }}">
                                                        {{ __($catItemLabel) }}
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="3">
                                                            <path d="m9 18 6-6-6-6"></path>
                                                        </svg>
                                                    </a>
                                                </li>
                                            @endforeach
                                        @else
                                            @foreach($__staylHeaderCategories->take(12) as $hc)
                                                <li>
                                                    <a href="{{ route('category.products', [slug($hc->name), $hc->id]) }}">
                                                        {{ __($hc->name) }}
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="3">
                                                            <path d="m9 18 6-6-6-6"></path>
                                                        </svg>
                                                    </a>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endif

                    <ul class="stayl-nav-ul">
                        @foreach($menuNavLinks as $mbtn)
                            @php
                                $mbtnLabel = trim((string) ($mbtn['label'] ?? ''));
                                $mbtnUrl = trim((string) ($mbtn['url'] ?? '#')) ?: '#';
                                $mbtnType = (string) ($mbtn['type'] ?? 'link');
                                $mbtnActive = (int) ($mbtn['is_active'] ?? 1) === 1;
                                $mbtnTrack = trim((string) ($mbtn['tracking_key'] ?? ''));
                                if ($mbtnTrack === '') {
                                    $mbtnTrack = $headerTrackKey($mbtnLabel, 'menu-link');
                                }
                                $mbtnDropdownStyle = (string) ($mbtn['dropdown_style'] ?? 'dropdown');
                                $mbtnItems = is_array($mbtn['items'] ?? null) ? $mbtn['items'] : [];
                            @endphp
                            @continue($mbtnLabel === '' || !$mbtnActive)
                            @if($mbtnType === 'dropdown' && !empty($mbtnItems))
                                <li class="stayl-pages-item">
                                    <a href="{{ $mbtnUrl }}" style="display:flex; align-items:center; gap:8px;"
                                        data-header-track="{{ $mbtnTrack }}">{{ __($mbtnLabel) }} <svg width="12" height="12"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <path d="m6 9 6 6 6-6"></path>
                                        </svg></a>
                                    <div
                                        class="stayl-pages-dropdown {{ $mbtnDropdownStyle === 'mega' ? 'stayl-pages-dropdown--mega' : '' }}">
                                        {!! $renderHeaderDropdownItems($mbtnItems, 1) !!}
                                    </div>
                                </li>
                            @else
                                <li><a href="{{ $mbtnUrl }}" data-header-track="{{ $mbtnTrack }}">{{ __($mbtnLabel) }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </nav>

                @if(!empty($headerMenuCfg['show_seller_button']))
                    <a href="{{ url($headerMenuCfg['seller_url'] ?? '/seller/apply') }}" class="stayl-seller-btn">
                        {{ __($headerMenuCfg['seller_text'] ?? 'BECOME A SELLER') }}
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            style="color:var(--stayl-yellow);">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    @endif
</header>

<div class="glass-mobile-menu" id="glassSidebar" style="font-family: 'Outfit', sans-serif;">
    <div class="glass-mobile-menu-overlay glass-sidebar-overlay"
        style="background: rgba(0,0,0,0.5); backdrop-filter: blur(5px);"></div>
    <div class="glass-mobile-menu-content"
        style="background: #ffffff; width: 420px; box-shadow: 20px 0 60px rgba(0,0,0,0.15);">

        {{-- Custom CSS Header --}}
        <div class="stayl-sb-header">
            <div class="stayl-sb-title-wrap">
                <div class="stayl-sb-icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </div>
                <h3 class="stayl-sb-title">@lang('NAVIGATION')</h3>
            </div>
            <button id="glassSidebarClose" class="stayl-sb-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="stayl-sb-body">
            @php
                $sidebarLinks = collect($menuNavLinks)
                    ->filter(function ($item) {
                        return is_array($item) && trim((string) ($item['label'] ?? '')) !== '' && (int) ($item['is_active'] ?? 1) === 1;
                    })
                    ->take(12)
                    ->values();
            @endphp
            @forelse($sidebarLinks as $mobileNav)
                @php
                    $mobileNavLabel = trim((string) ($mobileNav['label'] ?? ''));
                    $mobileNavUrl = trim((string) ($mobileNav['url'] ?? '#')) ?: '#';
                    $mobileNavTrack = trim((string) ($mobileNav['tracking_key'] ?? ''));
                    if ($mobileNavTrack === '') {
                        $mobileNavTrack = $headerTrackKey($mobileNavLabel, 'mobile-link');
                    }
                @endphp
                <a href="{{ $mobileNavUrl }}" class="stayl-sb-item" data-header-track="{{ $mobileNavTrack }}">
                    <div class="stayl-sb-item-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <h5 class="stayl-sb-item-text">{{ __($mobileNavLabel) }}</h5>
                </a>
            @empty
                <a href="{{ route('home') }}" class="stayl-sb-item">
                    <div class="stayl-sb-item-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <h5 class="stayl-sb-item-text">@lang('Home Page')</h5>
                </a>
            @endforelse

            {{-- Action Area --}}
            <div class="stayl-sb-action-box">
                @guest
                    <a href="{{ route('user.login') }}" class="stayl-btn-dark">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <circle cx="12" cy="8" r="5"></circle>
                            <path d="M20 21a8 8 0 0 0-16 0"></path>
                        </svg>
                        @lang('LOGIN TO ACCOUNT')
                    </a>
                    <a href="{{ route('user.register') }}" class="stayl-btn-outline">
                        @lang('CREATE NEW ACCOUNT')
                    </a>
                @else
                    <a href="{{ route('user.home') }}" class="stayl-btn-dark" style="background: var(--stayl-active-blue);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        @lang('VIEW DASHBOARD')
                    </a>
                @endguest
            </div>

        </div>
    </div>
</div>
<div id="staylCameraModal" class="stayl-camera-modal" aria-hidden="true">
    <div class="stayl-camera-modal__backdrop" data-camera-close="1"></div>
    <div class="stayl-camera-modal__dialog" role="dialog" aria-modal="true" aria-label="@lang('Camera preview')">
        <video id="staylCameraVideo" class="stayl-camera-modal__video" autoplay playsinline muted></video>
        <canvas id="staylCameraCanvas" style="display:none;"></canvas>
        <div class="stayl-camera-modal__actions">
            <button type="button" class="stayl-camera-modal__btn" id="staylCameraCloseBtn">@lang('Close')</button>
            <button type="button" class="stayl-camera-modal__btn stayl-camera-modal__btn--primary"
                id="staylCameraCaptureBtn">@lang('Capture')</button>
        </div>
    </div>
</div>
<script>
    (function () {
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
                trigger.addEventListener('click', function (e) {
                    e.preventDefault();
                    sidebar.classList.add('active');
                    document.body.style.overflow = 'hidden'; // Prevent scroll
                });
            });

            const closeAction = function () {
                sidebar.classList.remove('active');
                document.body.style.overflow = '';
            };

            if (closeBtn) closeBtn.addEventListener('click', closeAction);
            if (overlay) overlay.addEventListener('click', closeAction);
        }
        function setupVoiceSearch() {
            const voiceBtn = document.getElementById('voiceSearchBtn');
            const searchInput = document.getElementById('staylHeaderSearchInput');
            const voiceStatus = document.getElementById('staylVoiceStatus');
            if (!voiceBtn || !searchInput) return;
            const SpeechRecognitionApi = window.SpeechRecognition || window.webkitSpeechRecognition;
            const canRequestMic = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
            let micStreamRef = null;
            let voiceStatusTimer = null;
            const setVoiceStatus = function (message, persist) {
                if (!voiceStatus) return;
                voiceStatus.textContent = message || '';
                if (message) {
                    voiceStatus.classList.add('is-visible');
                } else {
                    voiceStatus.classList.remove('is-visible');
                }
                if (voiceStatusTimer) {
                    window.clearTimeout(voiceStatusTimer);
                    voiceStatusTimer = null;
                }
                if (!persist && message) {
                    voiceStatusTimer = window.setTimeout(function () {
                        voiceStatus.classList.remove('is-visible');
                    }, 2200);
                }
            };
            if (!SpeechRecognitionApi) {
                if (!canRequestMic) {
                    voiceBtn.style.opacity = '0.55';
                    voiceBtn.title = 'Voice search is not supported in this browser';
                    setVoiceStatus('Voice search is not supported', false);
                    return;
                }
            }
            const recognition = SpeechRecognitionApi ? new SpeechRecognitionApi() : null;
            if (recognition) {
                recognition.lang = 'bn-BD';
                recognition.interimResults = false;
                recognition.maxAlternatives = 1;
                recognition.continuous = false;
            }
            const startRecognition = function () {
                if (!recognition) {
                    alert('Voice typing is not supported in this browser.');
                    setVoiceStatus('Voice typing is not supported', false);
                    return;
                }
                try {
                    recognition.start();
                } catch (e) {
                    // Fallback ভাষা, কিছু ব্রাউজারে bn-BD কাজ না করলে
                    try {
                        recognition.lang = 'en-US';
                        recognition.start();
                    } catch (err) {
                        alert('Microphone start failed. Please allow microphone permission.');
                        setVoiceStatus('Microphone start failed.', false);
                    }
                }
            };
            const stopMicStream = function () {
                if (!micStreamRef) return;
                micStreamRef.getTracks().forEach(function (track) { track.stop(); });
                micStreamRef = null;
            };
            voiceBtn.addEventListener('click', async function () {
                if (!canRequestMic) {
                    startRecognition();
                    return;
                }
                try {
                    // Force permission prompt on click.
                    micStreamRef = await navigator.mediaDevices.getUserMedia({ audio: true, video: false });
                    voiceBtn.classList.add('is-listening');
                    setVoiceStatus('Microphone active. You can speak now.', true);
                    startRecognition();
                } catch (err) {
                    alert('Microphone permission blocked. Please allow microphone in browser site settings.');
                    setVoiceStatus('Microphone permission blocked.', false);
                }
            });
            if (recognition) {
                recognition.addEventListener('start', function () {
                    voiceBtn.classList.add('is-listening');
                    setVoiceStatus('Microphone active. You can speak now.', true);
                });
                recognition.addEventListener('end', function () {
                    voiceBtn.classList.remove('is-listening');
                    stopMicStream();
                    setVoiceStatus('', false);
                });
                recognition.addEventListener('result', function (event) {
                    const text = (((event.results || [])[0] || [])[0] || {}).transcript || '';
                    if (!text) return;
                    searchInput.value = text.trim();
                    searchInput.focus();
                    setVoiceStatus('Voice captured successfully.', false);
                });
                recognition.addEventListener('error', function (event) {
                    voiceBtn.classList.remove('is-listening');
                    stopMicStream();
                    const errorCode = (event && event.error) ? String(event.error) : 'unknown';
                    if (errorCode === 'not-allowed') {
                        alert('Microphone permission blocked. Please allow it in browser settings.');
                        setVoiceStatus('Microphone permission blocked.', false);
                    } else if (errorCode === 'no-speech') {
                        alert('No speech detected. Please try again.');
                        setVoiceStatus('No speech detected.', false);
                    } else {
                        alert('Voice search error: ' + errorCode);
                        setVoiceStatus('Voice error: ' + errorCode, false);
                    }
                });
            }
        }
        function setupCameraSearch() {
            const cameraBtn = document.getElementById('cameraSearchBtn');
            const modal = document.getElementById('staylCameraModal');
            const video = document.getElementById('staylCameraVideo');
            const canvas = document.getElementById('staylCameraCanvas');
            const closeBtn = document.getElementById('staylCameraCloseBtn');
            const captureBtn = document.getElementById('staylCameraCaptureBtn');
            if (!cameraBtn || !modal || !video || !closeBtn || !captureBtn) return;
            let streamRef = null;
            const stopStream = function () {
                if (!streamRef) return;
                streamRef.getTracks().forEach(function (track) { track.stop(); });
                streamRef = null;
            };
            const closeModal = function () {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                video.srcObject = null;
                stopStream();
            };
            cameraBtn.addEventListener('click', async function () {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert('Camera is not supported in this browser.');
                    return;
                }
                try {
                    streamRef = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
                    if (!streamRef) {
                        throw new Error('empty-stream');
                    }
                    video.srcObject = streamRef;
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                } catch (error) {
                    try {
                        // Desktop/laptop fallback
                        streamRef = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                        video.srcObject = streamRef;
                        modal.classList.add('is-open');
                        modal.setAttribute('aria-hidden', 'false');
                    } catch (fallbackError) {
                        alert('Camera permission denied or unavailable.');
                    }
                }
            });
            captureBtn.addEventListener('click', function () {
                if (!video.videoWidth || !video.videoHeight || !canvas) return;
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                if (!ctx) return;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                closeModal();
            });
            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function (event) {
                if (event.target && event.target.getAttribute('data-camera-close') === '1') {
                    closeModal();
                }
            });
        }

        // Initialize
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                setupStaylSidebar();
                setupVoiceSearch();
                setupCameraSearch();
                syncHeaderHeightVar();
                setupHeaderScrollCollapse();
            });
        } else {
            setupStaylSidebar();
            setupVoiceSearch();
            setupCameraSearch();
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
            } catch (err) { }
            return null;
        }
        async function initDisplayCurrency() {
            const currencyOptions = Array.from(document.querySelectorAll('[data-stayl-currency-option]'));
            if (!currencyOptions.length) return;
            const saved = (localStorage.getItem('stayl_display_currency_code') || '').toUpperCase();
            let rates = await loadRates(baseCurrency.code);
            if (!rates) rates = fallbackRates;
            let activeCurrencyCode = '';
            let mutationDebounce = null;
            function markCurrencyOption(code) {
                currencyOptions.forEach(function (el) {
                    const itemCode = (el.getAttribute('data-stayl-currency-option') || '').toUpperCase();
                    if (itemCode === code) {
                        el.classList.add('is-active');
                    } else {
                        el.classList.remove('is-active');
                    }
                });
            }
            function applyNow(code) {
                activeCurrencyCode = (code || '').toUpperCase() || baseCurrency.code;
                if (!code || code === baseCurrency.code) {
                    convertDisplayedPrices(baseCurrency.code, 1);
                    if (headerCurrencyLabel) headerCurrencyLabel.textContent = baseCurrency.code;
                    markCurrencyOption(baseCurrency.code);
                    return;
                }
                const rate = Number(rates[code] ?? fallbackRates[code] ?? 1);
                convertDisplayedPrices(code, Number.isFinite(rate) && rate > 0 ? rate : 1);
                if (headerCurrencyLabel) headerCurrencyLabel.textContent = code;
                markCurrencyOption(code);
            }
            applyNow(saved || baseCurrency.code);
            const observer = new MutationObserver(function () {
                if (mutationDebounce) window.clearTimeout(mutationDebounce);
                mutationDebounce = window.setTimeout(function () {
                    applyNow(activeCurrencyCode || (localStorage.getItem('stayl_display_currency_code') || baseCurrency.code));
                }, 120);
            });
            observer.observe(document.body, { childList: true, subtree: true });
            currencyOptions.forEach(function (optionEl) {
                optionEl.addEventListener('click', function (event) {
                    event.preventDefault();
                    const code = (this.getAttribute('data-stayl-currency-option') || '').toUpperCase() || baseCurrency.code;
                    localStorage.setItem('stayl_display_currency_code', code);
                    applyNow(code);
                    setTimeout(function () { window.location.reload(); }, 120);
                });
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