@push('style')
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">
    <style>
        #staylMainHeader {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            z-index: 1000 !important;
            pointer-events: none; /* Let clicks through to children */
        }
        
        .header-bar-section {
            pointer-events: auto; /* Re-enable clicks for bars */
            transition: transform 0.8s cubic-bezier(0.65, 0, 0.35, 1), 
                        opacity 0.6s ease !important;
            will-change: transform, opacity;
            backface-visibility: hidden;
            background: inherit;
        }

        /* DEFAULT STATES */
        #staylBarTop { position: relative; z-index: 3; }
        #staylBarMain { position: relative; z-index: 2; background: #ffffff !important; }
        #staylBarMenu { position: relative; z-index: 1; }

        .dark-mode #staylBarMain { background: #0f172a !important; }
        
        /* THE STICKY ANIMATION */
        #staylMainHeader.is-scrolled-down #staylBarTop {
            transform: translateY(-100%) !important;
            opacity: 0 !important;
        }
        
        #staylMainHeader.is-scrolled-down #staylBarMain {
            transform: translateY(calc(-1 * var(--stayl-bar-top-h, 40px))) !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important;
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        }
        
        #staylMainHeader.is-scrolled-down #staylBarMenu {
            transform: translateY(calc(-1 * var(--stayl-bar-top-h, 40px))) !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }

        .stayl-flag-img {
            width: 24px !important;
            height: 16px !important;
            object-fit: cover !important;
            border-radius: 2px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
    </style>
@endpush
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
        if (!array_key_exists($layoutId, $headerBarDefaults)) continue;
        $headerBarOrderIndex[$layoutId] = $layoutIndex + 1;
        $headerBarVisible[$layoutId] = !empty($layoutSlot['enabled']);
    }

    $footerData = function_exists('getCachedFooterData') ? getCachedFooterData() : [];
    $companyInfo = $footerData['footer_company_info'] ?? null;
    $supportCenter = $footerData['footer_support_center'] ?? null;
    $appPromotion = $footerData['footer_app_promotion'] ?? null;
    $appPromotionItems = $footerData['footer_app_promotion_items'] ?? collect();

    $headerLanguages = $general->multi_language ? \App\Models\Language::query()->orderBy('name')->get() : collect();
    $headerControl = \App\Services\HeaderControlService::getLiveConfig();
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

    $headerContactPhone = (string) ($companyInfo->data_values->contact_number ?? '');
    $headerContactEmail = (string) ($companyInfo->data_values->email_address ?? '');
    $headerCodNoticeText = __('Cash on Delivery available nationwide');
    $currentLocale = strtolower((string) (session('lang') ?: app()->getLocale() ?: 'en'));
    $isBN = str_starts_with($currentLocale, 'bn') || str_starts_with($currentLocale, 'bd');

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
        // Template Link Example for User: https://www.google.com or /all/products
        // Set this URL in Admin -> Frontend -> Header -> Menu Bar -> Navigation Links
        $html = '<ul class="stayl-menu-tree stayl-menu-tree--depth-' . $depth . ' professional-dropdown-card">';
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
            $html .= '<a href="' . e($url) . '" class="stayl-menu-link-pro" data-header-track="' . e($track) . '">';
            $html .= '<span>' . e(__($label)) . '</span>';
            if ($hasChildren) {
                $html .= '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ms-auto opacity-50"><path d="m9 18 6-6-6-6"></path></svg>';
            }
            $html .= '</a>';
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

    // Improved Localization Logic:
    // When BN is selected, show "বাংলা" (name) or "BN" (code) based on config, but ensured translated strings are responsive.
    $languageButtonLabel = (($headerTopCfg['language_mode'] ?? 'code') === 'name') ? __($currentLangName) : strtoupper(__($currentLangCode));

    $currencyButtonLabel = (($headerTopCfg['currency_mode'] ?? 'code') === 'name')
        ? __($general->cur_text ?? 'BDT')
        : strtoupper(__($general->cur_text ?? 'BDT'));
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

    // Phase 5.4: Professional Localization Hard-Fix
    $currentLocale = strtolower((string) (session('lang') ?: app()->getLocale() ?: 'en'));
    $isBN = str_starts_with($currentLocale, 'bn') || str_starts_with($currentLocale, 'bd');
@endphp

<header class="stayl-fixed-master" id="staylMainHeader">
    @if(!$isUserProfileHome && !empty($headerBarVisible['header_bar_top_notice']) && !empty($headerTopCfg['enabled']) && !empty($headerTopCfg['is_public']))
        <div class="stayl-announcement-bar stayl-dynamic-order header-bar-section" id="staylBarTop" style="--stayl-order: {{ $headerBarOrderIndex['header_bar_top_notice'] ?? 1 }};">
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
                    <span class="stayl-cod-badge no-break">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-sky-400">
                           <rect x="1" y="3" width="15" height="13"></rect>
                           <polygon points="16 8 20 8 23 11 23 16 16 16"></polygon>
                           <circle cx="5.5" cy="18.5" r="2.5"></circle>
                           <circle cx="18.5" cy="18.5" r="2.5"></circle>
                        </svg>
                        <span class="stayl-cod-text">{{ __($headerCodNoticeText) }}</span>
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2 lg:gap-3">
                    @if(!empty($headerTopCfg['show_language']) && $headerLanguages->isNotEmpty())
                        <div class="stayl-topbar-menu">
                                @php
                                    $langFlags = ['en' => 'gb', 'bn' => 'bd'];
                                    $currentLang = strtoupper(session('lang', 'EN'));
                                    $currentLangObj = $headerLanguages->firstWhere('code', strtolower($currentLang)) ?? $headerLanguages->firstWhere('code', strtoupper($currentLang));
                                    $currentLangName = $currentLangObj ? $currentLangObj->name : ('EN' === $currentLang ? 'English' : 'বাংলা');
                                @endphp
                            <button type="button" class="stayl-topbar-menu__btn d-inline-flex align-items-center gap-1 notranslate" style="white-space: nowrap; padding: 3px 10px; border-radius: 4px;">
                                <span id="staylCurrentLanguageLabel" class="d-inline-flex align-items-center flex-nowrap" style="min-width: 0 !important; width: auto !important; gap: 8px;">
                                    <span id="staylCurrentLanguageFlag" class="d-inline-flex align-items-center" style="min-width: 0 !important; width: auto !important;"><img src="https://flagcdn.com/w40/{{ $langFlags[strtolower($currentLang)] ?? 'un' }}.png" class="stayl-flag-img" style="border: 1px solid rgba(0,0,0,0.1); display: block; flex-shrink: 0;"></span>
                                    <span id="staylCurrentLanguageText" class="fw-medium stayl-theme-text" style="min-width: 0 !important; width: auto !important; font-size: 13px;">{{ __($currentLangName) }}</span>
                                </span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" style="margin-left: 2px; opacity: 0.7;">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </button>
                            <div class="stayl-topbar-menu__panel" style="min-width: 150px; padding: 4px 0;">
                                @foreach($headerLanguages as $lng)
                                    @php
                                        $lngCode = strtoupper($lng->code);
                                        $isLangActive = ($currentLang === $lngCode);
                                    @endphp
                                    <a href="{{ route('lang', $lng->code) }}" class="stayl-topbar-menu__item {{ $isLangActive ? 'is-active' : '' }}"
                                        data-stayl-lang-option="{{ $lngCode }}" style="padding: 8px 15px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="d-flex" style="width: 24px; height: 16px;"><img src="https://flagcdn.com/w40/{{ $langFlags[strtolower($lng->code)] ?? 'un' }}.png" class="stayl-flag-img" style="border: 1px solid rgba(0,0,0,0.1);"></span>
                                            <span class="fw-medium stayl-theme-text" style="font-size: 13px;">{{ __($lng->name) }}</span>
                                        </div>
                                        <span class="stayl-topbar-menu__check">✓</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(!empty($headerTopCfg['show_currency']))
                        <div class="stayl-topbar-menu">
                                @php
                                    $headerCurrencyCodes = [
                                        $general->cur_text ?? 'BDT', 'USD', 'EUR', 'GBP', 'INR', 'PKR', 'SAR', 'AED', 'MYR', 'SGD', 'JPY', 'CAD', 'AUD', 'QAR', 'KWD', 'RUB', 'UAH', 'NZD', 'ZAR', 'CNY', 'BRL', 'TRY', 'KRW'
                                    ];
                                    $headerCurrencyCodes = collect($headerCurrencyCodes)->map(static fn($c) => strtoupper(trim((string) $c)))->filter()->unique()->values();

                                    $headerCurrencySymbols = [
                                        'BDT' => '৳', 'USD' => '$', 'INR' => '₹', 'PKR' => 'Rs', 'SAR' => 'ر.س',
                                        'AED' => 'د.إ', 'MYR' => 'RM', 'EUR' => '€', 'GBP' => '£', 'SGD' => 'S$',
                                        'JPY' => '¥', 'CAD' => '$', 'AUD' => '$', 'QAR' => 'ر.ق', 'KWD' => 'د.ك',
                                        'RUB' => '₽', 'UAH' => '₴', 'NZD' => '$', 'ZAR' => 'R', 'CNY' => '¥',
                                        'BRL' => 'R$', 'TRY' => '₺', 'KRW' => '₩'
                                    ];

                                    $headerCurrencyFlags = [
                                        'BDT' => 'bd', 'USD' => 'us', 'INR' => 'in', 'PKR' => 'pk', 'SAR' => 'sa',
                                        'AED' => 'ae', 'MYR' => 'my', 'EUR' => 'eu', 'GBP' => 'gb', 'SGD' => 'sg',
                                        'JPY' => 'jp', 'CAD' => 'ca', 'AUD' => 'au', 'QAR' => 'qa', 'KWD' => 'kw',
                                        'RUB' => 'ru', 'UAH' => 'ua', 'NZD' => 'nz', 'ZAR' => 'za', 'CNY' => 'cn',
                                        'BRL' => 'br', 'TRY' => 'tr', 'KRW' => 'kr'
                                    ];

                                    $currencyCountryNames = [
                                        'BDT' => 'Bangladesh', 'USD' => 'United States', 'INR' => 'India', 'PKR' => 'Pakistan', 'SAR' => 'Saudi Arabia',
                                        'AED' => 'UAE', 'MYR' => 'Malaysia', 'EUR' => 'Europe', 'GBP' => 'United Kingdom', 'SGD' => 'Singapore',
                                        'JPY' => 'Japan', 'CAD' => 'Canada', 'AUD' => 'Australia', 'QAR' => 'Qatar', 'KWD' => 'Kuwait',
                                        'RUB' => 'Russia', 'UAH' => 'Ukraine', 'NZD' => 'New Zealand', 'ZAR' => 'South Africa', 'CNY' => 'China',
                                        'BRL' => 'Brazil', 'TRY' => 'Turkey', 'KRW' => 'South Korea'
                                    ];

                                    // Ensure codes are always the short international versions
                                    $headerCurrencyCodes = ['BDT', 'USD', 'EUR', 'GBP', 'INR', 'PKR', 'SAR', 'AED', 'MYR', 'SGD', 'JPY', 'CAD', 'AUD', 'QAR', 'KWD', 'RUB', 'UAH', 'NZD', 'ZAR', 'CNY', 'BRL', 'TRY', 'KRW'];
                                    $headerCurrencyCodes = array_unique($headerCurrencyCodes);

                                    $defaultCurrencyCode = strtoupper((string) ($general->cur_text ?? 'BDT'));
                                @endphp
                            <button type="button" class="stayl-topbar-menu__btn d-inline-flex align-items-center gap-1 notranslate" style="white-space: nowrap; padding: 3px 10px; border-radius: 4px;">
                                <span id="staylCurrentCurrencyLabel" class="d-inline-flex align-items-center flex-nowrap" style="min-width: 0 !important; width: auto !important; gap: 8px;">
                                    <span id="staylCurrentCurrencyFlag" class="d-inline-flex align-items-center" style="min-width: 0 !important; width: auto !important;"><img src="https://flagcdn.com/w40/{{ $headerCurrencyFlags[$defaultCurrencyCode] ?? 'un' }}.png" class="stayl-flag-img" style="border: 1px solid rgba(0,0,0,0.1); display: block; flex-shrink: 0;"></span>
                                    <span id="staylCurrentCurrencyText" class="fw-medium stayl-theme-text" style="min-width: 0 !important; width: auto !important; font-size: 13px;">{{ $defaultCurrencyCode }} {{ $headerCurrencySymbols[$defaultCurrencyCode] ?? '' }}</span>
                                </span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" style="margin-left: 2px; flex-shrink: 0; opacity: 0.7;">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </button>
                            <div class="stayl-topbar-menu__panel" style="min-width: 140px; max-height: 350px; overflow-y: auto; padding: 4px 0;">
                                @foreach($headerCurrencyCodes as $code)
                                    @php
                                        $currencySymbol = $headerCurrencySymbols[$code] ?? $general->cur_sym ?? '৳';
                                        $currencyFlag = $headerCurrencyFlags[$code] ?? 'un';
                                    @endphp
                                    <a href="#"
                                        class="stayl-topbar-menu__item {{ $code === $defaultCurrencyCode ? 'is-active' : '' }}"
                                        data-stayl-currency-option="{{ $code }}" style="padding: 10px 15px;">
                                        <div class="d-flex align-items-center gap-2 py-0">
                                            <span class="d-flex" style="width: 24px; height: 16px;"><img src="https://flagcdn.com/w40/{{ $currencyFlag }}.png" class="stayl-flag-img" style="border: 1px solid rgba(0,0,0,0.1); box-shadow: 0 1px 2px rgba(0,0,0,0.1);"></span>
                                            <span class="fw-semibold stayl-theme-text" style="font-size: 13px;">{{ strtoupper($code) }} {{ $currencySymbol }}</span>
                                        </div>
                                        <span class="stayl-topbar-menu__check">✓</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Theme Toggle (Day/Night) Relocated to Top Bar --}}
                    <div class="stayl-topbar-menu">
                        <a href="javascript:void(0)" id="staylThemeToggle" class="stayl-topbar-menu__btn stayl-theme-btn-top" title="{{ __('Toggle Theme') }}">
                            <svg id="themeIconSun" width="18" height="18" class="hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                            <svg id="themeIconMoon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                        </a>
                    </div>

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
                            class="stayl-seller-btn stayl-seller-btn--top border-0 bg-transparent text-white font-bold opacity-90 hover:opacity-100">
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
    @if(!empty($headerBarVisible['header_bar_main']) && !empty($headerMainCfg['enabled']) && !empty($headerMainCfg['is_public']))
        <div class="stayl-main-header-bar stayl-dynamic-order header-bar-section" id="staylBarMain" style="--stayl-order: {{ $headerBarOrderIndex['header_bar_main'] ?? 2 }};">
            <div class="stayl-wrap">
                {{-- Logo --}}
                <div class="d-flex align-items-center">
                    <a href="{{ route('home') }}">
                        @php $logo = getLogo('logo'); @endphp
                        @if($logo)
                                <img src="{{ $logo }}" alt="Staylbd" class="stayl-logo-img"
                                    style="--stayl-logo-h: {{ (int) ($headerMainCfg['logo_max_height'] ?? 48) }}px;">
                        @else
                                <span class="stayl-logo-text">{{ strtoupper(gs('site_name')) }}</span>
                        @endif
                    </a>
                </div>

                {{-- Search Pill & External Lens --}}
                <div class="stayl-search-container max-w-[520px] mx-auto flex-1 pl-4 pr-4 lg:pl-10 lg:pr-10">
                    <div class="stayl-search-inner-wrap">
                        <form action="{{ route('products') }}" method="GET" class="stayl-search-pill stayl-search-form-layout rounded-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 shadow-sm focus-within:shadow-md transition-shadow">
                            <input type="text" id="staylHeaderSearchInput" name="search" class="stayl-search-input bg-transparent"
                                placeholder="@lang('Search for products, brands and more')..."
                                value="{{ request()->search ?? null }}" autocomplete="off">
                            <button type="submit" class="stayl-search-icon-btn transition-colors" title="{{ __('Search') }}">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </button>
                            <button type="button" id="voiceSearchBtn" title="Voice Search" class="stayl-search-action-trigger stayl-voice-btn">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" class="stayl-mic-icon-premium transition-all duration-300">
                                    <path d="M12 1a5 5 0 0 0-5 5v6a5 5 0 0 0 10 0V6a5 5 0 0 0-5-5Z"/>
                                    <path d="M19 10v1a7 7 0 0 1-7 7 7 7 0 0 1-7-7v-1H3v1c0 4.53 3.39 8.27 7.75 8.87V23h2.5v-4.13C17.61 18.87 21 15.13 21 11v-1h-2Z"/>
                                </svg>
                            </button>

                        </form>
                        <button type="button" id="cameraSearchBtn" title="Camera Search" class="stayl-camera-btn ml-2">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>


                {{-- Action Icons (Dynamic Interactive Set) --}}
                <div class="stayl-action-grid">
                    <a href="{{ route('user.order.index') }}" class="stayl-action-item group" title="{{ __('Orders') }}" data-dashboard-nav="1">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="action-icon">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                            <path d="M3 6h18"></path>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                        <span class="stayl-hover-label stayl-label-order">
                            {{ $isBN ? 'অর্ডার' : __('Order') }}
                        </span>
                    </a>

                    <a href="{{ url('/user/ordertrack') }}" class="stayl-action-item group" title="{{ __('Track Order') }}" data-dashboard-nav="1">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="action-icon">
                            <rect x="1" y="3" width="15" height="13"></rect>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon>
                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                        </svg>
                        <span class="stayl-hover-label stayl-label-track">
                            {{ $isBN ? 'ট্র্যাকিং' : __('Track') }}
                        </span>
                    </a>
                    <a href="{{ route('user.wishlist') }}" class="stayl-action-item group relative" title="{{ __('Wishlist') }}" data-dashboard-nav="1">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="action-icon">
                            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                        </svg>
                        <span class="stayl-badge show-wishlist-count absolute -top-2.5 -right-2 bg-orange-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold shadow-sm ring-2 ring-white">0</span>
                        <span class="stayl-hover-label stayl-label-wish">
                            {{ $isBN ? 'পছন্দ' : __('Wish') }}
                        </span>
                    </a>

                    <a href="{{ route('user.cart') }}" class="stayl-action-item group relative" title="{{ __('Cart') }}" data-dashboard-nav="1">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="action-icon">
                            <circle cx="8" cy="21" r="1"></circle>
                            <circle cx="19" cy="21" r="1"></circle>
                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                        </svg>
                        <span class="stayl-badge show-cart-count absolute -top-2.5 -right-2 bg-sky-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-[10px] font-bold shadow-sm ring-2 ring-white">0</span>
                        <span class="stayl-hover-label stayl-label-cart">
                            {{ $isBN ? 'কার্ট' : __('Cart') }}
                        </span>
                    </a>
                    <a href="{{ route('user.home') }}" class="stayl-action-item group" title="{{ __('Account') }}" data-dashboard-nav="1">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="action-icon">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span class="stayl-acc-label stayl-label-acc">
                            {{ $isBN ? 'লগইন' : __('Login') }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Row 2: Secondary Nav Bar --}}
    @if(!$isUserProfileHome && !empty($headerBarVisible['header_bar_menu']) && !empty($headerMenuCfg['enabled']) && !empty($headerMenuCfg['is_public']))
        <div class="stayl-yellow-bar stayl-dynamic-order bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 header-bar-section" id="staylBarMenu" style="--stayl-order: {{ $headerBarOrderIndex['header_bar_menu'] ?? 3 }};">
            <div class="stayl-wrap">
                <nav class="stayl-nav-flex" style="--stayl-nav-gap: clamp(12px, 1.5vw, 24px);">
                    @if(!empty($headerMenuCfg['show_sidebar_trigger']))
                        {{-- Separate Hamburger Button --}}
                        <div class="stayl-sidebar-trigger text-slate-700 dark:text-slate-300 hover:text-sky-500" title="Open Menu">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="3" y1="12" x2="21" y2="12"></line>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <line x1="3" y1="18" x2="21" y2="18"></line>
                            </svg>
                        </div>
                    @endif

                    @if(!empty($headerMenuCfg['show_category_button']))
                        {{-- All Categories Button (Separate) --}}
                        <div class="h-full relative" id="staylCatContainer">
                            <button class="stayl-cat-btn h-full self-stretch flex items-center gap-2 stayl-btn-reset font-semibold text-slate-800 dark:text-slate-100 hover:text-sky-500 text-[12px] uppercase tracking-wide" id="staylCatBtn">
                                <span>{{ __($headerMenuCfg['category_button_label'] ?? 'ALL CATEGORIES') }}</span>
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" class="opacity-70">
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
                                                    <a href="{{ $catItemUrl }}" class="font-medium text-slate-700 hover:text-sky-500 hover:bg-slate-50 transition-colors">
                                                        {{ __($catItemLabel) }}
                                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="1.5">
                                                            <path d="m9 18 6-6-6-6"></path>
                                                        </svg>
                                                    </a>
                                                </li>
                                            @endforeach
                                        @else
                                            @foreach($__staylHeaderCategories->take(12) as $hc)
                                                <li>
                                                    <a href="{{ route('category.products', [slug($hc->name), $hc->id]) }}" class="font-medium text-slate-700 hover:text-sky-500 hover:bg-slate-50 transition-colors">
                                                        {{ __($hc->name) }}
                                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="1.5">
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

                    <ul class="stayl-nav-ul flex items-center h-full">
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
                                    <a href="{{ trim((string)($mbtnUrl ?? '#')) ?: '#' }}" class="stayl-flex-center font-semibold text-slate-700 hover:text-sky-500 transition-colors uppercase tracking-wide text-[12px] h-full"
                                        data-header-track="{{ $mbtnTrack }}">
                                        {{ __($mbtnLabel) }} 
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ml-1 opacity-70">
                                            <path d="m6 9 6 6 6-6"></path>
                                        </svg>
                                    </a>
                                    <div class="stayl-pages-dropdown {{ $mbtnDropdownStyle === 'mega' ? 'stayl-pages-dropdown--mega' : '' }} professional-card-shadow">
                                        {!! $renderHeaderDropdownItems($mbtnItems, 1) !!}
                                    </div>
                                </li>
                            @else
                                <li class="h-full flex items-center"><a href="{{ $mbtnUrl }}" data-header-track="{{ $mbtnTrack }}" class="font-semibold text-slate-700 hover:text-sky-500 transition-colors uppercase tracking-wide text-[12px] h-full flex items-center">{{ __($mbtnLabel) }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </nav>

                @if(!empty($headerMenuCfg['show_seller_button']))
                    <a href="{{ url($headerMenuCfg['seller_url'] ?? '/seller/apply') }}" class="stayl-seller-btn text-[12px] font-bold tracking-wide uppercase bg-orange-50 text-orange-600 border border-orange-200 hover:bg-orange-500 hover:text-white transition-colors duration-300 rounded-md px-4 py-2 flex items-center gap-2">
                        {{ __($headerMenuCfg['seller_text'] ?? 'BECOME A SELLER') }}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    @endif
</header>

<div class="glass-mobile-menu" id="glassSidebar" style="font-family: 'Inter', sans-serif;">
    <div class="glass-mobile-menu-overlay glass-sidebar-overlay"
        style="background: rgba(0,0,0,0.5); backdrop-filter: blur(5px);"></div>
    <div class="glass-mobile-menu-content"
        style="background: #ffffff; width: 420px; box-shadow: 20px 0 60px rgba(0,0,0,0.15);">

        {{-- Custom CSS Header --}}
        <div class="stayl-sb-header">
            <div class="stayl-sb-title-wrap">
                <div class="stayl-sb-icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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
            @php
                $iconMap = [
                    'home' => 'las la-home',
                    'shop' => 'las la-shopping-basket',
                    'product' => 'las la-box',
                    'page' => 'las la-layer-group',
                    'about' => 'las la-user-tag',
                    'blog' => 'las la-blog',
                    'contact' => 'las la-headset',
                    'opu' => 'las la-user-circle',
                    'dashboard' => 'las la-tachometer-alt',
                    'login' => 'las la-sign-in-alt',
                    'register' => 'las la-user-plus'
                ];
                $getNavIcon = function($label, $url = '') use ($iconMap) {
                    $label = strtolower($label);
                    $url = strtolower($url);

                    // Comprehensive mapping for translation safety
                    $refMap = [
                        'home' => ['home', 'হোম', 'বাড়ি'],
                        'shop' => ['shop', 'শপ', 'পণ্য', 'কিনুন', 'store'],
                        'product' => ['product', 'পণ্য'],
                        'blog' => ['blog', 'ব্লগ'],
                        'contact' => ['contact', 'যোগাযোগ', 'সাপোর্ট', 'সাপোর্ট', 'headset'],
                        'about' => ['about', 'সম্পর্কে'],
                        'login' => ['login', 'প্রবেশ', 'লগইন'],
                        'register' => ['register', 'নিবন্ধন', 'নতুন'],
                        'dashboard' => ['dashboard', 'ড্যাশবোর্ড'],
                        'profile' => ['profile', 'প্রোফাইল', 'user', 'opu']
                    ];

                    foreach($refMap as $key => $keywords) {
                        foreach($keywords as $k) {
                            if(str_contains($label, $k) || str_contains($url, $key)) {
                                return $iconMap[$key] ?? 'las la-link';
                            }
                        }
                    }
                    return 'las la-link'; // Default fallback
                };
            @endphp
            @forelse($sidebarLinks as $mobileNav)
                @php
                    $mobileNavLabel = trim((string) ($mobileNav['label'] ?? ''));
                    $mobileNavUrl = trim((string) ($mobileNav['url'] ?? '#')) ?: '#';
                    $mobileNavTrack = trim((string) ($mobileNav['tracking_key'] ?? ''));
                    if ($mobileNavTrack === '') {
                        $mobileNavTrack = $headerTrackKey($mobileNavLabel, 'mobile-link');
                    }
                    $mobileIconClass = $getNavIcon($mobileNavLabel, $mobileNavUrl);
                @endphp
                <a href="{{ $mobileNavUrl }}" class="stayl-sb-item" data-header-track="{{ $mobileNavTrack }}">
                    <div class="stayl-sb-item-icon">
                        <i class="{{ $mobileIconClass }}" style="font-size: 24px;"></i>
                    </div>
                    <h5 class="stayl-sb-item-text">{{ __($mobileNavLabel) }}</h5>
                </a>
            @empty
                <a href="{{ route('home') }}" class="stayl-sb-item">
                    <div class="stayl-sb-item-icon">
                        <i class="las la-home" style="font-size: 24px;"></i>
                    </div>
                    <h5 class="stayl-sb-item-text">@lang('Home Page')</h5>
                </a>
            @endforelse

            {{-- Action Area --}}
            <div class="stayl-sb-action-box">
                @guest
                    <a href="{{ route('user.login') }}" class="stayl-btn-dark">
                        <i class="las la-sign-in-alt" style="font-size: 20px;"></i>
                        @lang('LOGIN TO ACCOUNT')
                    </a>
                    <a href="{{ route('user.register') }}" class="stayl-btn-outline">
                        <i class="las la-user-plus" style="font-size: 20px;"></i>
                        @lang('CREATE NEW ACCOUNT')
                    </a>
                @else
                    <a href="{{ route('user.home') }}" class="stayl-btn-dark" style="background: var(--stayl-active-blue);">
                        <i class="las la-tachometer-alt" style="font-size: 20px;"></i>
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
            const barTop = document.getElementById('staylBarTop');
            const barMain = document.getElementById('staylBarMain');
            const barMenu = document.getElementById('staylBarMenu');
            if (!header) return;

            // Measure and store individual bar heights
            if (barTop && barTop.offsetHeight > 0) {
                document.documentElement.style.setProperty('--stayl-bar-top-h', barTop.offsetHeight + 'px');
            }

            // Calculate active header height for body padding
            let totalH = 0;
            if (header.classList.contains('is-scrolled-down')) {
                // In sticky mode, only Bar 2 (Main) is effectively taking space at the top
                totalH = barMain ? barMain.offsetHeight : 0;
            } else {
                // In normal mode, measure the whole container
                totalH = header.offsetHeight;
            }

            if (totalH > 0) {
                document.documentElement.style.setProperty('--stayl-dynamic-header-height', Math.ceil(totalH) + 'px');
            }
        }
        function setupHeaderScrollCollapse() {
            const header = document.querySelector('.stayl-fixed-master');
            if (!header) return;
            
            let lastScrollTop = window.pageYOffset || document.documentElement.scrollTop;
            let hidden = false;
            let ticking = false;

            const onScroll = function () {
                if (ticking) return;
                ticking = true;
                
                window.requestAnimationFrame(function () {
                    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
                    const delta = currentScroll - lastScrollTop;
                    const THRESHOLD_MIN = 120;
                    const SCROLL_SENSITIVITY = 5; // Pixels to ignore jitter

                    if (currentScroll < 50) {
                        // At the very top, always show everything
                        if (hidden) {
                            hidden = false;
                            header.classList.remove('is-scrolled-down');
                        }
                    } else if (delta > SCROLL_SENSITIVITY && currentScroll > THRESHOLD_MIN) {
                        // Scrolling Down -> Hide Bar 1 and 3
                        if (!hidden) {
                            hidden = true;
                            header.classList.add('is-scrolled-down');
                        }
                    } else if (delta < -SCROLL_SENSITIVITY) {
                        // Scrolling Up (Reverse) -> Re-show Bar 1 and 3
                        if (hidden) {
                            hidden = false;
                            header.classList.remove('is-scrolled-down');
                        }
                    }

                    lastScrollTop = currentScroll;
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
        const currencyNativeNames = {
            BDT: 'BDT',
            USD: 'USD',
            INR: 'INR',
            PKR: 'PKR',
            SAR: 'SAR',
            AED: 'AED',
            MYR: 'MYR',
            EUR: 'EUR',
            GBP: 'GBP',
            SGD: 'SGD',
            JPY: 'JPY',
            CAD: 'CAD',
            AUD: 'AUD',
            QAR: 'QAR',
            KWD: 'KWD',
            RUB: 'RUB',
            UAH: 'UAH',
            NZD: 'NZD',
            ZAR: 'ZAR',
            CNY: 'CNY',
            BRL: 'BRL',
            TRY: 'TRY',
            KRW: 'KRW'
        };
        const langNativeNames = { BN: 'বাংলা', EN: 'English', HI: 'হিন্দি' };
        const headerLanguageLabel = document.getElementById('staylCurrentLanguageLabel');
        const currencySymbols = {
            BDT: '৳', USD: '$', EUR: '€', GBP: '£', INR: '₹', PKR: 'Rs', AED: 'د.إ', SAR: 'ر.س', MYR: 'RM', SGD: 'S$', JPY: '¥'
        };
        const fallbackRates = {
            BDT: 1, USD: 0.0082, EUR: 0.0075, GBP: 0.0064, INR: 0.69, AED: 0.03, SAR: 0.031, MYR: 0.039, SGD: 0.011, JPY: 1.24
        };
        const priceSelectors = [
            '.staylbd-rt-price', '.price', '.old-price', '.qv-price', '.qv-price-old',
            '.subtotal-price', '.total-price', '.discount-price', '.grand-total-price',
            '.checkout-shipping-charge', '.track-quick-btn__amount', '.pro-detail-special-price',
            '.pro-detail-regular-price', '.sticky-add-to-cart-bar__price',
            '#priceMinInput', '#priceMaxInput' // Price range inputs
        ];

        function parseAmountFromText(text, sym) {
            if (!text) return null;
            const normalizedDigits = String(text)
                .replace(/[০-৯]/g, function (d) { return String('০১২৩৪৫৬৭৮৯'.indexOf(d)); })
                .replace(/[٠-٩]/g, function (d) { return String('٠١٢٣٤٥٦٧٨٩'.indexOf(d)); });
            const cleaned = normalizedDigits.replace(new RegExp('\\' + sym, 'g'), '').replace(/[^0-9.,-]/g, '').replace(/,/g, '');
            const n = parseFloat(cleaned);
            return Number.isFinite(n) ? n : null;
        }
        function formatAmount(value) {
            return (Math.round((value + Number.EPSILON) * 100) / 100).toFixed(2);
        }
        function setStaylCookie(name, value) {
            const d = new Date();
            d.setTime(d.getTime() + (30*24*60*60*1000));
            document.cookie = name + "=" + value + ";expires=" + d.toUTCString() + ";path=/";
        }
        function getStaylCookie(name) {
            let nameEQ = name + "=";
            let ca = document.cookie.split(';');
            for(let i=0;i < ca.length;i++) {
                let c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }
        function convertDisplayedPrices(targetCode, targetRate) {
            const targetSym = currencySymbols[targetCode] || targetCode + ' ';
            const elements = new Set();
            priceSelectors.forEach(sel => document.querySelectorAll(sel).forEach(el => elements.add(el)));
            elements.forEach(el => {
                if (!el.dataset.staylBaseAmount) {
                    const baseText = (el.textContent || '').trim();
                    const parsed = parseAmountFromText(baseText, baseCurrency.symbol);
                    if (parsed === null) return;
                    el.dataset.staylBaseAmount = String(parsed);
                }
                const baseAmount = Number(el.dataset.staylBaseAmount);
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
            const saved = (localStorage.getItem('stayl_display_currency_code') || getStaylCookie('stayl_display_currency_code') || '').toUpperCase();
            let rates = await loadRates(baseCurrency.code);
            if (!rates) rates = fallbackRates;
            let activeCurrencyCode = '';
            let mutationDebounce = null;
            let isApplyingCurrency = false;
            let lastAppliedCode = '';
            let lastAppliedTime = 0;

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
            const headerCurrencyLabel = document.getElementById('staylCurrentCurrencyLabel');
            if (headerCurrencyLabel && headerCurrencyLabel.parentElement) {
                headerCurrencyLabel.parentElement.classList.add('stayl-currency-label-wrap');
            }

            function applyNow(code, force = false) {
                const now = Date.now();
                const targetCode = (code || '').toUpperCase() || baseCurrency.code.toUpperCase();
                
                // Prevent redundant updates if the same code was applied recently (within 500ms)
                if (!force && targetCode === lastAppliedCode && (now - lastAppliedTime < 500)) return;
                
                if (isApplyingCurrency) return;
                isApplyingCurrency = true;

                activeCurrencyCode = targetCode;
                lastAppliedCode = activeCurrencyCode;
                lastAppliedTime = now;
                const activeLangCode = (@json(app()->getLocale() == 'bn' ? 'BN' : 'EN')).toString();

                // numeral formatting logic: native for BDT + BN lang, english for others
                const toNative = function(val) {
                    if (activeLangCode !== 'BN' || activeCurrencyCode !== 'BDT') return val;
                    const m = {'0':'০','1':'১','2':'২','3':'৩','4':'৪','5':'৫','6':'৬','7':'৭','8':'৮','9':'৯','.' :'.',',':','};
                    return String(val).split('').map(c => m[c] || c).join('');
                };

                const currencyNamesEN = { BDT: 'BDT', USD: 'USD', EUR: 'EUR', INR: 'INR' };
                const cNameMap = activeLangCode === 'BN' ? currencyNativeNames : currencyNamesEN;

                const currentSymbol = currencySymbols[activeCurrencyCode] || baseCurrency.symbol || '$';
                
                const currencyFlags = {
                    'BDT': 'bd', 'USD': 'us', 'INR': 'in', 'PKR': 'pk', 'SAR': 'sa',
                    'AED': 'ae', 'MYR': 'my', 'EUR': 'eu', 'GBP': 'gb', 'SGD': 'sg',
                    'JPY': 'jp', 'CAD': 'ca', 'AUD': 'au', 'QAR': 'qa', 'KWD': 'kw',
                    'RUB': 'ru', 'UAH': 'ua', 'NZD': 'nz', 'ZAR': 'za', 'CNY': 'cn',
                    'BRL': 'br', 'TRY': 'tr', 'KRW': 'kr'
                };
                
                const flagEl = document.getElementById('staylCurrentCurrencyFlag');
                const textEl = document.getElementById('staylCurrentCurrencyText');
                if (flagEl) {
                    const countryCode = currencyFlags[activeCurrencyCode] || 'un';
                    flagEl.innerHTML = `<img src="https://flagcdn.com/w40/${countryCode}.png" style="width: 24px; height: 16px; object-fit: cover; border-radius: 2px; border: 1px solid rgba(0,0,0,0.1); display: block; flex-shrink: 0;">`;
                    flagEl.style.cssText = "min-width: 0 !important; width: auto !important; display: inline-flex !important; align-items: center;";
                }
                if (textEl) {
                    textEl.textContent = activeCurrencyCode + ' ' + currentSymbol;
                    textEl.style.cssText = "min-width: 0 !important; width: auto !important; font-size: 13px;";
                }
                const rate = Number(rates[activeCurrencyCode] ?? fallbackRates[activeCurrencyCode] ?? 1);
                window.__staylDisplayCurrency = { code: activeCurrencyCode, symbol: currentSymbol, rate: rate };
                document.documentElement.setAttribute('data-display-currency', activeCurrencyCode);
                localStorage.setItem('stayl_display_currency_code', activeCurrencyCode);

                document.querySelectorAll('.staylbd-rt-price, .staylbd-rt-price-compare').forEach(el => {
                    el.classList.add('notranslate');
                    let baseVal = parseFloat(el.getAttribute('data-base-price'));
                    if (isNaN(baseVal)) {
                        let content = el.textContent || '';
                        const bnToEn = {'০':'0','১':'1','২':'2','৩':'3','৪':'4','৫':'5','৬':'6','৭':'7','৮':'8','৯':'9'};
                        let cleanMatch = content.split('').map(c => bnToEn[c] || c).join('').replace(/[^\d.]/g, '');
                        baseVal = parseFloat(cleanMatch);
                        if (!isNaN(baseVal)) el.setAttribute('data-base-price', baseVal);
                    }
                    if (!isNaN(baseVal)) {
                        const finalVal = (baseVal * rate).toFixed(2);
                        el.textContent = currentSymbol + toNative(finalVal);
                    }
                });

                // Global Selector Conversion (Sidebar, Inputs, etc.)
                priceSelectors.forEach(sel => {
                    document.querySelectorAll(sel).forEach(el => {
                        el.classList.add('notranslate');
                        // Skip if already processed by specific logic above
                        if (el.classList.contains('staylbd-rt-price')) return;

                        if (!el.dataset.staylBaseAmount) {
                            const baseText = (el.tagName === 'INPUT' ? (el.value || el.placeholder) : el.textContent) || '';
                            const parsed = parseAmountFromText(baseText, baseCurrency.symbol);
                            if (parsed !== null) el.dataset.staylBaseAmount = String(parsed);
                        }

                        const baseAmount = Number(el.dataset.staylBaseAmount);
                        if (!isNaN(baseAmount)) {
                            const converted = toNative(formatAmount(baseAmount * rate));
                            const finalDisplay = currentSymbol + converted;
                            if (el.tagName === 'INPUT') {
                                if (el.value) el.value = finalDisplay;
                                if (el.placeholder) el.placeholder = finalDisplay;
                            } else {
                                el.textContent = finalDisplay;
                            }
                        }
                    });
                });

                markCurrencyOption(activeCurrencyCode);

                // Cart Persistence Sync: Restore Session from LocalStorage if empty
                if (!window.__cartSynced) {
                    window.__cartSynced = true;
                    setTimeout(() => {
                        const localCart = localStorage.getItem('staylbd_guest_cart');
                        if (localCart) {
                            try {
                                const items = JSON.parse(localCart);
                                if (items && items.length > 0 && window.getCartCount) {
                                    window.getCartCount().then(res => {
                                        if (res && (!res.count || res.count == 0)) {
                                            fetch("{{ route('cart.list.restore.guest') }}", {
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                                body: JSON.stringify({ items: items })
                                            }).then(() => { if (window.getCartCount) window.getCartCount(); });
                                        }
                                    });
                                }
                            } catch (e) {}
                        }
                    }, 1500);
                }
                isApplyingCurrency = false;
                document.documentElement.classList.add('stayl-rt-ready');
            }
            applyNow(saved || baseCurrency.code);
            window.__staylApplyDisplayCurrency = applyNow;
            const observer = new MutationObserver(function (mutations) {
                if (isApplyingCurrency) return;
                let hasAddedNodes = false;
                for (let i = 0; i < mutations.length; i++) {
                    if (mutations[i].addedNodes && mutations[i].addedNodes.length > 0) {
                        hasAddedNodes = true;
                        break;
                    }
                }
                if (!hasAddedNodes) return;

                // Only process once per frame/cycle to prevent jitter
                if (mutationDebounce) window.clearTimeout(mutationDebounce);
                mutationDebounce = window.setTimeout(function () {
                    if (document.body.classList.contains('stayl-rt-processing')) return;
                    document.body.classList.add('stayl-rt-processing');
                    // Ensure the applied code is always valid from storage or base
                    const currentStored = localStorage.getItem('stayl_display_currency_code') || getStaylCookie('stayl_display_currency_code') || baseCurrency.code;
                    applyNow(currentStored);
                    document.body.classList.remove('stayl-rt-processing');
                }, 300); // Increased delay for stability
            });
            observer.observe(document.body, { childList: true, subtree: true });
            currencyOptions.forEach(function (optionEl) {
                optionEl.addEventListener('click', function (event) {
                    event.preventDefault();
                    const code = (this.getAttribute('data-stayl-currency-option') || '').toUpperCase() || baseCurrency.code;
                    setStaylCookie('stayl_display_currency_code', code);
                    localStorage.setItem('stayl_display_currency_code', code);
                    applyNow(code, true);
                    window.location.reload();
                });
            });
            window.addEventListener('staylbd:product-updated', function () {
                const currentStored = (localStorage.getItem('stayl_display_currency_code') || getStaylCookie('stayl_display_currency_code') || baseCurrency.code).toUpperCase();
                window.requestAnimationFrame(function () {
                    applyNow(currentStored);
                });
            });
        }
        function initDisplayLanguageLabel() {
            const langFlagsMap = {'EN': 'gb', 'BN': 'bd'};
            const langNamesMap = {'EN': 'English', 'BN': 'বাংলা'};
            const flagEl = document.getElementById('staylCurrentLanguageFlag');
            const textEl = document.getElementById('staylCurrentLanguageText');
            
            const savedLang = (localStorage.getItem('stayl_display_language_code') || getStaylCookie('stayl_display_language_code') || '').toUpperCase();
            if (savedLang) {
                if (flagEl) {
                    const countryCode = langFlagsMap[savedLang] || 'un';
                    flagEl.innerHTML = `<img src="https://flagcdn.com/w20/${countryCode}.png" width="20" alt="${savedLang}" style="border-radius: 2px; display: block; flex-shrink: 0;">`;
                    flagEl.style.cssText = "min-width: 0 !important; width: auto !important; display: inline-flex !important; align-items: center;";
                }
                if (textEl) {
                    textEl.textContent = langNamesMap[savedLang] || savedLang;
                    textEl.style.cssText = "min-width: 0 !important; width: auto !important;";
                }
            }
            document.querySelectorAll('[data-stayl-lang-option]').forEach(function (el) {
                el.addEventListener('click', function (event) {
                    event.preventDefault();
                    const code = (this.getAttribute('data-stayl-lang-option') || '').toUpperCase();
                    if (code) {
                        setStaylCookie('stayl_display_language_code', code);
                        localStorage.setItem('stayl_display_language_code', code);
                        
                        // Automatically change currency based on language selection
                        let defaultCurrency = '';
                        if (code === 'BN') defaultCurrency = 'BDT';
                        else if (code === 'EN') defaultCurrency = 'USD';
                        
                        if (defaultCurrency) {
                            setStaylCookie('stayl_display_currency_code', defaultCurrency);
                            localStorage.setItem('stayl_display_currency_code', defaultCurrency);
                        }
                        
                        // Briefly update label for instant feedback before reload
                        if (flagEl) {
                            const countryCode = langFlagsMap[code] || 'un';
                            flagEl.innerHTML = `<img src="https://flagcdn.com/w20/${countryCode}.png" width="20" alt="${code}" style="border-radius: 2px; display: block; flex-shrink: 0;">`;
                            flagEl.style.cssText = "min-width: 0 !important; width: auto !important; display: inline-flex !important; align-items: center;";
                        }
                        if (textEl) {
                            textEl.textContent = langNamesMap[code] || code;
                            textEl.style.cssText = "min-width: 0 !important; width: auto !important;";
                        }
                        window.location.href = this.href;
                    }
                });
            });
        }
        initDisplayLanguageLabel();
        initDisplayCurrency();
    })();
</script>

@push('script')
<script>
    (function() {
        const themeBtn = document.getElementById('staylThemeToggle');
        const sunIcon = document.getElementById('themeIconSun');
        const moonIcon = document.getElementById('themeIconMoon');
        const body = document.body;

        // Apply theme on load
        const currentTheme = localStorage.getItem('stayl-theme') || 'light';
        if (currentTheme === 'dark') {
            body.classList.add('dark-mode');
            if (sunIcon) sunIcon.classList.remove('hidden');
            if (moonIcon) moonIcon.classList.add('hidden');
        }

        if (themeBtn) {
            themeBtn.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                const isDark = body.classList.contains('dark-mode');

                if (isDark) {
                    localStorage.setItem('stayl-theme', 'dark');
                    if (sunIcon) sunIcon.classList.remove('hidden');
                    if (moonIcon) moonIcon.classList.add('hidden');
                } else {
                    localStorage.setItem('stayl-theme', 'light');
                    if (sunIcon) sunIcon.classList.add('hidden');
                    if (moonIcon) moonIcon.classList.remove('hidden');
                }
            });
        }
    })();
</script>
@endpush
