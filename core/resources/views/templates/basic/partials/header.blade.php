{{-- Language & currency switchers: Tailwind-only components (see resources/views/components/*-switcher.blade.php) --}}
@php
    $customButtonsAll = \Illuminate\Support\Facades\Cache::remember('header_custom_buttons_v1', 1800, function() {
        return \App\Models\Frontend::where('data_keys', 'custom_buttons.element')->orderBy('id', 'asc')->get();
    });
    $customHeaderButtons = $customButtonsAll->filter(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (($dv['target'] ?? '') === 'header') && ((int) ($dv['is_active'] ?? 1) === 1);
    })->sortBy(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (int) ($dv['display_order'] ?? 0);
    })->values();

    if (!isset($__staylHeaderCategories)) {
        $__staylHeaderCategories = \Illuminate\Support\Facades\Cache::remember(
            'storefront.header_nav_categories_v3',
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
    $headerLayout = \Illuminate\Support\Facades\Cache::remember('homepage_layout_sections_v1', 1800, function() {
        return \App\Services\HomepageLayoutService::getOrderedSections();
    });
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
    $footerContent = $footerData['footer_content'] ?? null;
    $sellerAccountEnabled = (int) ($footerContent->data_values->seller_account_enabled ?? 0) === 1;
    $contactContent = $footerData['contact'] ?? null;

    $headerLanguages = $general->multi_language ? \App\Models\Language::query()->orderBy('name')->get() : collect();
    $headerControl = $headerControl ?? \App\Services\HeaderControlService::getLiveConfig();
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

    $currentLangCode = strtoupper((string) (app()->getLocale() ?: session('lang') ?: optional($headerLanguages->first())->code ?: 'en'));
    $currentLangRow = $headerLanguages->first(function ($row) use ($currentLangCode) {
        return strtoupper(trim((string) ($row->code ?? ''))) === $currentLangCode;
    });
    $currentLangName = trim((string) (optional($currentLangRow)->name ?: $currentLangCode));

    $langFlags = [
        'EN' => '🇺🇸',
        'BN' => '🇧🇩',
        'HI' => '🇮🇳',
        'ZH' => '🇨🇳',
        'UR' => '🇵🇰',
        'AR' => '🇸🇦',
        'RU' => '🇷🇺',
        'FR' => '🇫🇷',
        'DE' => '🇩🇪',
        'ES' => '🇪🇸',
        'TR' => '🇹🇷',
    ];
    $currentLangFlag = $langFlags[$currentLangCode] ?? '🌐';

    $languageButtonLabel = $currentLangFlag . ' ' . (($headerTopCfg['language_mode'] ?? 'code') === 'name' ? __($currentLangName) : __($currentLangCode));

    $headerCurrencies = [
        ['code' => 'USD', 'symbol' => '$', 'country' => 'United States', 'flag' => 'https://flagcdn.com/w20/us.png'],
        ['code' => 'EUR', 'symbol' => '€', 'country' => 'Italy', 'flag' => 'https://flagcdn.com/w20/it.png'],
        ['code' => 'EUR', 'symbol' => '€', 'country' => 'Netherlands', 'flag' => 'https://flagcdn.com/w20/nl.png'],
        ['code' => 'EUR', 'symbol' => '€', 'country' => 'Spain', 'flag' => 'https://flagcdn.com/w20/es.png'],
        ['code' => 'GBP', 'symbol' => '£', 'country' => 'United Kingdom', 'flag' => 'https://flagcdn.com/w20/gb.png'],
        ['code' => 'BDT', 'symbol' => '৳', 'country' => 'Bangladesh', 'flag' => 'https://flagcdn.com/w20/bd.png'],
        ['code' => 'INR', 'symbol' => '₹', 'country' => 'India', 'flag' => 'https://flagcdn.com/w20/in.png'],
        ['code' => 'SAR', 'symbol' => 'SR', 'country' => 'Saudi Arabia', 'flag' => 'https://flagcdn.com/w20/sa.png'],
        ['code' => 'AED', 'symbol' => 'د.إ', 'country' => 'UAE', 'flag' => 'https://flagcdn.com/w20/ae.png'],
        ['code' => 'MYR', 'symbol' => 'RM', 'country' => 'Malaysia', 'flag' => 'https://flagcdn.com/w20/my.png'],
        // South Asia
        ['code' => 'PKR', 'symbol' => '₨', 'country' => 'Pakistan', 'flag' => 'https://flagcdn.com/w20/pk.png'],
        ['code' => 'LKR', 'symbol' => 'Rs', 'country' => 'Sri Lanka', 'flag' => 'https://flagcdn.com/w20/lk.png'],
        ['code' => 'NPR', 'symbol' => 'रु', 'country' => 'Nepal', 'flag' => 'https://flagcdn.com/w20/np.png'],
        ['code' => 'BTN', 'symbol' => 'Nu.', 'country' => 'Bhutan', 'flag' => 'https://flagcdn.com/w20/bt.png'],
        ['code' => 'MVR', 'symbol' => 'Rf', 'country' => 'Maldives', 'flag' => 'https://flagcdn.com/w20/mv.png'],
        ['code' => 'AFN', 'symbol' => '؋', 'country' => 'Afghanistan', 'flag' => 'https://flagcdn.com/w20/af.png'],
        // Popular / Others
        ['code' => 'RUB', 'symbol' => '₽', 'country' => 'Russia', 'flag' => 'https://flagcdn.com/w20/ru.png'],
        ['code' => 'CNY', 'symbol' => '¥', 'country' => 'China', 'flag' => 'https://flagcdn.com/w20/cn.png'],
        ['code' => 'JPY', 'symbol' => '¥', 'country' => 'Japan', 'flag' => 'https://flagcdn.com/w20/jp.png'],
        ['code' => 'KRW', 'symbol' => '₩', 'country' => 'South Korea', 'flag' => 'https://flagcdn.com/w20/kr.png'],
        ['code' => 'AUD', 'symbol' => '$', 'country' => 'Australia', 'flag' => 'https://flagcdn.com/w20/au.png'],
        ['code' => 'CAD', 'symbol' => '$', 'country' => 'Canada', 'flag' => 'https://flagcdn.com/w20/ca.png'],
        ['code' => 'SGD', 'symbol' => '$', 'country' => 'Singapore', 'flag' => 'https://flagcdn.com/w20/sg.png'],
        ['code' => 'BRL', 'symbol' => 'R$', 'country' => 'Brazil', 'flag' => 'https://flagcdn.com/w20/br.png'],
        ['code' => 'ZAR', 'symbol' => 'R', 'country' => 'South Africa', 'flag' => 'https://flagcdn.com/w20/za.png'],
        ['code' => 'TRY', 'symbol' => '₺', 'country' => 'Turkey', 'flag' => 'https://flagcdn.com/w20/tr.png'],
        ['code' => 'QAR', 'symbol' => 'ر.ق', 'country' => 'Qatar', 'flag' => 'https://flagcdn.com/w20/qa.png'],
        ['code' => 'KWD', 'symbol' => 'د.ক', 'country' => 'Kuwait', 'flag' => 'https://flagcdn.com/w20/kw.png'],
        ['code' => 'UAH', 'symbol' => '₴', 'country' => 'Ukraine', 'flag' => 'https://flagcdn.com/w20/ua.png'],
    ];

    $currentCurrencyCode = strtoupper((string) (session('stayl_display_currency_code') ?: request()->cookie('stayl_display_currency_code') ?: $general->cur_text ?: 'BDT'));

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
    $currentLocale = app()->getLocale();
    $isBN = $currentLocale === 'bn';

    // Flag mapping for flagcdn.com
    $isoFlagMap = [
        'en' => 'us', 'bn' => 'bd', 'hi' => 'in', 'ar' => 'sa', 'ur' => 'pk',
        'ru' => 'ru', 'zh' => 'cn', 'es' => 'es', 'fr' => 'fr', 'de' => 'de',
        'pt' => 'pt', 'ja' => 'jp', 'tr' => 'tr', 'sa' => 'sa'
    ];
    $currentLangFlagCode = $isoFlagMap[strtolower($currentLangCode)] ?? 'us';
@endphp

@php
    // Caching Header Appearance for high performance
    $headerAppearanceSync = \Illuminate\Support\Facades\Cache::remember('header_appearance_cfg', 1800, function() use ($headerControl) {
        return (array) ($headerControl['appearance'] ?? []);
    });
@endphp

@php
    // Admin Control & Database Sync: Each bar wrapped in visibility check
    $isTopVisible = !$isUserProfileHome && !empty($headerTopCfg['enabled']) && !empty($headerTopCfg['is_public']);
    $isMainVisible = !empty($headerMainCfg['enabled']) && !empty($headerMainCfg['is_public']);
    $isMenuVisible = !$isUserProfileHome && !empty($headerMenuCfg['enabled']) && !empty($headerMenuCfg['is_public']);
@endphp

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.5);
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(51, 65, 85, 0.5);
    }
</style>

<header class="stayl-fixed-master">
    @if($isTopVisible)
        <div class="stayl-announcement-bar stayl-dynamic-order" style="--stayl-order: {{ $headerBarOrderIndex['header_bar_top_notice'] ?? 1 }};">
            <div class="stayl-wrap">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ $headerContactPhone !== '' ? 'tel:' . preg_replace('/[^0-9+]/', '', $headerContactPhone) : route('contact') }}"
                        class="stayl-announcement-link stayl-top-contact-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <span class="stayl-top-contact-text">{{ $headerContactPhone }}</span>
                    </a>
                    <span class="d-none d-md-inline">|</span>
                    <a href="{{ $headerContactEmail !== '' ? 'mailto:' . $headerContactEmail : route('contact') }}"
                        class="stayl-announcement-link stayl-top-contact-link">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <path d="m3 7 9 6 9-6"></path>
                        </svg>
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

                <!-- Middle Section: Comprehensive High-Fidelity Weather (Stability First) -->
                <div class="stayl-top-middle flex items-center flex-grow px-2 md:px-6 overflow-hidden">
                    <!-- Slot 1: Location Slot -->
                    <div class="flex-initial flex justify-start items-center overflow-hidden pr-4">
                        <div id="stayl-live-location" 
                             class="group flex items-center gap-1.5 text-[12.5px] font-medium text-slate-50 transition-all duration-300 dark:text-slate-300 cursor-help shrink-0" 
                             title="Loading location details...">
                            <span class="flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </span>
                            <span class="stayl-location-text whitespace-nowrap font-inter tracking-tight">@lang('Locating')...</span>
                        </div>
                    </div>

                    <!-- Separator -->
                    <div class="h-3 w-[1px] bg-white/10 shrink-0"></div>

                    <!-- Slot 2: Enhanced Weather Slot (No Clipping) -->
                    <div class="flex-1 flex justify-start items-center overflow-hidden pl-4 mr-10">
                        <div id="stayl-live-weather" 
                             class="group flex items-center gap-2 text-[12.5px] font-medium text-slate-50 transition-all duration-300 dark:text-slate-300 cursor-default shrink-0" 
                             style="min-width: 220px;">
                            <span id="stayl-weather-svg" class="flex items-center justify-center transition-transform group-hover:scale-110 shrink-0">
                                {{-- Weather icon injected via JS --}}
                            </span>
                            <div class="overflow-hidden flex justify-start w-full">
                                <span class="stayl-weather-text opacity-100 transition-opacity duration-700 font-inter text-left whitespace-nowrap inline-block" style="min-width: 180px;">
                                    @lang('Updating')...
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 lg:gap-3">
                    @if(!empty($headerTopCfg['show_language']))
                        <!-- Language Dropdown -->
                        <div class="relative inline-block text-left z-[9999] group" 
                             onmouseenter="const m=document.getElementById('langMenu'); if(m) m.classList.remove('hidden')" 
                             onmouseleave="const m=document.getElementById('langMenu'); if(m) m.classList.add('hidden')">
                            <button id="langBtn" class="flex items-center gap-2 px-3 py-1.5 bg-transparent rounded-md text-white border-0 transition-colors hover:text-sky-400 text-sm font-semibold shadow-none outline-none focus:ring-0">
                                <img src="https://flagcdn.com/w20/{{ $currentLangFlagCode }}.png" class="w-5 h-4 shrink-0 rounded-none object-cover" loading="lazy">
                                <span class="uppercase">{{ $currentLangCode }}</span>
                                <svg class="size-3.5 opacity-70 transition-transform group-hover:!rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div id="langMenu" class="hidden group-hover:!block absolute right-0 mt-0 w-48 pt-2 bg-transparent z-[9999]">
                                <div class="bg-white border border-slate-200 rounded-lg shadow-xl text-slate-800 overflow-hidden ring-1 ring-black/5">
                                    <div class="bg-slate-50 px-3 py-1.5 border-b border-slate-100">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">@lang('Language')</span>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto py-1">
                                        @foreach($headerLanguages as $lang)
                                            @php
                                                $lCode = strtolower($lang->code);
                                                $lFlagCode = $isoFlagMap[$lCode] ?? 'us';
                                                // Build target URL
                                                $path = request()->path();
                                                // Remove current locale prefix if exists
                                                $cleanPath = preg_replace('/^[a-z]{2}(\/|$)/', '', $path);
                                                $targetUrl = url($lCode . ($cleanPath ? '/' . $cleanPath : ''));
                                                if (request()->getQueryString()) $targetUrl .= '?' . request()->getQueryString();
                                            @endphp
                                            <a href="{{ $targetUrl }}" class="flex items-center gap-3 px-3 py-2 hover:bg-slate-50 transition-colors no-underline text-slate-700 font-medium text-sm" data-stayl-lang-option="{{ $lCode }}">
                                                <img src="https://flagcdn.com/w20/{{ $lFlagCode }}.png" class="w-5 h-4 shrink-0 rounded-none object-cover" loading="lazy">
                                                <span>{{ $lang->name }}</span>
                                                @if(strtolower($currentLangCode) === $lCode)
                                                    <svg class="ms-auto size-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                        @if(!empty($headerTopCfg['show_currency']))
                        <!-- Currency Dropdown -->
                        <div class="relative inline-block text-left z-[9999] group"
                             onmouseenter="const m=document.getElementById('currencyMenu'); if(m) m.classList.remove('hidden')" 
                             onmouseleave="const m=document.getElementById('currencyMenu'); if(m) m.classList.add('hidden')">
                            <button id="currencyBtn" class="flex items-center gap-2 px-3 py-1.5 bg-transparent rounded-md text-white border-0 transition-colors hover:text-sky-400 text-sm font-semibold shadow-none outline-none focus:ring-0">
                                @php
                                    $currData = collect($headerCurrencies)->first(fn($c) => strtoupper($c['code']) === strtoupper($currentCurrencyCode));
                                @endphp
                                @if($currData && !empty($currData['flag']))
                                    <img src="{{ $currData['flag'] }}" class="w-5 h-4 shrink-0 rounded-none object-cover" loading="lazy">
                                @endif
                                <span>{{ $currentCurrencyCode }}</span>
                                <svg class="size-3.5 opacity-70 transition-transform group-hover:!rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div id="currencyMenu" class="hidden group-hover:!block absolute right-0 mt-0 w-48 pt-2 bg-transparent z-[9999]">
                                <div class="bg-white border border-slate-200 rounded-lg shadow-xl text-slate-800 overflow-hidden ring-1 ring-black/5">
                                    <div class="bg-slate-50 px-3 py-1.5 border-b border-slate-100">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">@lang('Currency')</span>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto py-1">
                                        @foreach($headerCurrencies as $curr)
                                            @php
                                                $cCode = strtoupper($curr['code']);
                                                $isActive = strtoupper($currentCurrencyCode) === $cCode;
                                            @endphp
                                            <a href="javascript:void(0)" class="flex items-center gap-3 px-3 py-2 hover:bg-slate-50 transition-colors no-underline text-slate-700 font-medium text-sm" data-stayl-currency-option="{{ $cCode }}">
                                                @if(!empty($curr['flag']))
                                                    <img src="{{ $curr['flag'] }}" class="w-5 h-4 shrink-0 rounded-none object-cover" loading="lazy">
                                                @endif
                                                <span>{{ $cCode }}</span>
                                                @if($isActive)
                                                    <svg class="ms-auto size-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
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

                    @guest
                        <a href="{{ route('user.login') }}" class="stayl-topbar-menu__btn stayl-auth-btn-top">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="opacity-80">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span class="font-bold">@lang('Sign In') / @lang('Register')</span>
                        </a>
                    @endguest

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
                                            $appExt = $itemFile !== '' ? pathinfo($itemFile, PATHINFO_EXTENSION) : 'apk';
                                            $downloadName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $itemPlatform) . '_StayLBD.' . $appExt;
                                        @endphp
                                        <a href="{{ $itemHref }}" class="stayl-app-item" @if($itemFile !== '') download="{{ $downloadName }}" @endif
                                            target="_blank" rel="noopener">
                                            <span class="stayl-app-item__icon">
                                                @if($itemIcon !== '')
                                                    <img src="{{ $itemIcon }}" alt="{{ $itemPlatform }}" loading="lazy">
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
                        <a href="{{ $sellerAccountEnabled ? route('seller.apply') : 'javascript:void(0)' }}"
                            @if(!$sellerAccountEnabled) onclick="openSellerModal()" @endif
                            class="stayl-seller-btn stayl-seller-btn--top border-0 bg-transparent text-white font-bold opacity-90 hover:opacity-100">
                            {{ __('SELLER') }}
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
    @if($isMainVisible)
        <div class="stayl-top-bar stayl-dynamic-order" style="--stayl-order: {{ $headerBarOrderIndex['header_bar_main'] ?? 2 }};">
            <div class="stayl-wrap flex items-center justify-between gap-4 py-2 lg:py-3">
                {{-- Logo (Left) --}}
                <div class="stayl-header-side stayl-header-side--left">
                    <a href="{{ route('home') }}" class="block">
                        @php $logo = getLogo('logo'); @endphp
                        @if($logo)
                            <img src="{{ $logo }}" alt="Staylbd" class="stayl-logo-img max-h-[40px] md:max-h-[48px] w-auto"
                                style="--stayl-logo-h: {{ (int) ($headerMainCfg['logo_max_height'] ?? 48) }}px;">
                        @else
                            <span class="stayl-logo-text text-xl font-black tracking-tighter text-slate-900 dark:text-white uppercase">{{ gs('site_name') }}</span>
                        @endif
                    </a>
                </div>

                {{-- Search Pill (Center) --}}
                <div class="stayl-search-container--desktop">
                    <div class="stayl-search-inner-wrap w-full">
                        <div class="stayl-search-pill-container flex items-center gap-3 w-full">
                            <div class="stayl-search-pill-wrapper flex-1">
                                <form action="{{ route('products') }}" method="GET" class="w-full" id="universalSearchForm"
                                    data-universal-url="{{ route('search.universal') }}"
                                    data-trending-url="{{ route('search.trending') }}"
                                    data-image-search-url="{{ route('search.image') }}"
                                    data-voice-search-url="{{ route('search.voice') }}">
                                    <input type="text" id="universalSearchInput" name="search" 
                                        class="stayl-search-pill--premium"
                                        placeholder="@lang('Search for products, brands and more')..."
                                        value="{{ request()->search ?? null }}" autocomplete="off">
                                    <input type="file" id="staylCameraInput" accept="image/*" class="hidden">
                                </form>
                                <div id="staylVoiceStatus" class="stayl-voice-status-indicator"></div>
                                <div class="stayl-search-icons-inner">
                                    <button type="button" id="voiceSearchBtn" title="@lang('Voice Search')" class="stayl-search-icon-btn p-2">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                            <line x1="12" y1="19" x2="12" y2="23"></line>
                                            <line x1="8" y1="23" x2="16" y2="23"></line>
                                        </svg>
                                    </button>
                                    <button type="submit" form="universalSearchForm" class="stayl-search-icon-btn p-2">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="relative">
                                <button type="button" id="cameraSearchBtn" class="stayl-search-icon-btn" title="{{ __('Camera Search') }}">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 7V5a2 2 0 0 1 2-2h2"></path>
                                        <path d="M17 3h2a2 2 0 0 1 2 2v2"></path>
                                        <path d="M21 17v2a2 2 0 0 1-2 2h-2"></path>
                                        <path d="M7 21H5a2 2 0 0 1-2-2v-2"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                        <path d="M12 9v1"></path>
                                        <path d="M12 14v1"></path>
                                        <path d="M9 12h1"></path>
                                        <path d="M14 12h1"></path>
                                    </svg>
                                </button>
                                
                                {{-- Lens Hover Card --}}
                                <div id="staylLensCard" class="stayl-lens-card">
                                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3 px-2">@lang('Search by Image')</div>
                                    <button type="button" id="lensOptionCamera" class="stayl-lens-option">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                        @lang('Take Photo')
                                    </button>
                                    <button type="button" id="lensOptionFile" class="stayl-lens-option mt-1">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        @lang('Upload from Gallery')
                                    </button>
                                    <input type="file" id="staylCameraFileDirect" accept="image/*" capture="environment" class="hidden">
                                    <input type="file" id="staylGalleryFileDirect" accept="image/*" class="hidden">
                                </div>
                            </div>
                        </div>
                        
                        {{-- AJAX Search Results Container (Desktop) --}}
                        <div id="searchResults" class="glass-search-results absolute top-full left-0 right-0 mt-2 rounded-2xl shadow-2xl z-[9999] overflow-hidden max-h-[80vh] overflow-y-auto">
                            <div class="stayl-search-results-inner p-4">
                                {{-- Recent/Trending section (visible initially) --}}
                                <div id="staylSearchDiscovery" class="stayl-search-section">
                                    <div class="mb-4">
                                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">@lang('Trending Now')</h4>
                                        <div id="staylTrendingKeywords" class="flex flex-wrap gap-2">
                                            <span class="animate-pulse bg-slate-100 dark:bg-slate-800 h-6 w-20 rounded-full"></span>
                                            <span class="animate-pulse bg-slate-100 dark:bg-slate-800 h-6 w-24 rounded-full"></span>
                                        </div>
                                    </div>
                                    <div id="staylRecentSearchWrap" class="hidden">
                                        <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">@lang('Recent Searches')</h4>
                                        <div id="staylRecentSearches" class="flex flex-wrap gap-2"></div>
                                    </div>
                                </div>
                                
                                {{-- Results section (visible when typing) --}}
                                <div id="staylSearchResults" class="hidden">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="stayl-search-products-col">
                                            <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">@lang('Products')</h4>
                                            <div id="staylSearchProductList" class="flex flex-col gap-3"></div>
                                        </div>
                                        <div class="stayl-search-meta-col">
                                            <div class="mb-5">
                                                <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">@lang('Categories & Brands')</h4>
                                                <div id="staylSearchMetaList" class="flex flex-wrap gap-2"></div>
                                            </div>
                                            <div id="staylSearchDidYouMeanWrap" class="hidden">
                                                <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">@lang('Did you mean?')</h4>
                                                <div id="staylSearchDidYouMean" class="flex flex-wrap gap-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="stayl-search-results-footer p-3 text-center border-t">
                                <a href="javascript:void(0)" id="staylViewAllSearch" class="text-xs font-semibold text-sky-600 dark:text-sky-400 hover:underline">@lang('View All Results')</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mobile Search Trigger (Visible on small screens) --}}
                <div class="lg:hidden flex items-center gap-3">
                    <button type="button" id="staylMobileSearchToggle" class="p-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors" title="{{ __('Search') }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                    <button type="button" id="glassSidebarEdgeToggle" class="p-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors" title="{{ __('Menu') }}">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                </div>


                <div class="stayl-header-side stayl-header-side--right">
                    <div class="stayl-action-grid">
                        {{-- Orders Icon --}}
                        <a href="{{ route('user.order.index') }}" class="stayl-action-item group relative" title="{{ __('Orders') }}" data-dashboard-nav="1">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="action-icon transition-transform group-hover:scale-110">
                                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                                <path d="M3 6h18"></path>
                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                            </svg>
                        </a>

                        {{-- Track Order --}}
                        <a href="{{ route('user.track.order') }}" class="stayl-action-item group relative" title="{{ __('Track Order') }}">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="action-icon transition-transform group-hover:scale-110">
                                <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path>
                                <path d="M15 18H9"></path>
                                <path d="M19 18h2a1 1 0 0 0 1-1v-5l-4-4h-3v10Z"></path>
                                <circle cx="7" cy="18" r="2"></circle>
                                <circle cx="17" cy="18" r="2"></circle>
                            </svg>
                        </a>

                        {{-- Wishlist --}}
                        <a href="{{ route('user.wishlist') }}" class="stayl-action-item group relative" title="{{ __('Wishlist') }}" data-dashboard-nav="1">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="action-icon transition-transform group-hover:scale-110">
                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                            </svg>
                            <span class="stayl-badge show-wishlist-count absolute -top-1 -right-1 bg-rose-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[9px] font-bold shadow-sm ring-2 ring-white dark:ring-slate-900 transition-all group-hover:scale-110">0</span>
                        </a>

                        {{-- Cart --}}
                        <a href="{{ route('user.cart') }}" class="stayl-action-item group relative" title="{{ __('Cart') }}" data-dashboard-nav="1">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="action-icon transition-transform group-hover:scale-110">
                                <circle cx="8" cy="21" r="1"></circle>
                                <circle cx="19" cy="21" r="1"></circle>
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                            </svg>
                            <span class="stayl-badge show-cart-count absolute -top-1 -right-1 bg-sky-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[9px] font-bold shadow-sm ring-2 ring-white dark:ring-slate-900 transition-all group-hover:scale-110">0</span>
                        </a>

                        {{-- Account --}}
                        <a href="{{ auth()->check() ? route('user.home') : route('user.login') }}" class="stayl-action-item group flex items-center gap-2" title="{{ __('Account') }}" data-dashboard-nav="1">
                            <div class="relative">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="action-icon transition-transform group-hover:scale-105">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="hidden xl:flex flex-col items-start leading-none gap-0.5">
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">
                                    @auth @lang('Profile') @else @lang('Hello, Sign in') @endauth
                                </span>
                                <span class="text-[12px] font-bold text-slate-700 dark:text-slate-200 truncate max-w-[90px]">
                                    @auth {{ auth()->user()->username }} @else @lang('My Account') @endauth
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
                {{-- Mobile Full-Screen Search Overlay --}}
                <div id="staylMobileSearchOverlay" class="fixed inset-0 bg-white dark:bg-slate-950 z-[10000] hidden flex-col transition-all duration-300">
                    <div class="flex items-center gap-3 p-4 border-b border-slate-100 dark:border-slate-800">
                        <button type="button" id="staylMobileSearchClose" class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                        <form action="{{ route('products') }}" method="GET" class="flex-1 flex items-center bg-slate-50 dark:bg-slate-900 rounded-full px-4 py-2 border border-slate-200 dark:border-slate-700">
                            <input type="text" id="staylMobileSearchInput" name="search" class="flex-1 bg-transparent border-none focus:ring-0 text-sm py-1" placeholder="@lang('Search')..." autocomplete="off">
                            <button type="submit" class="text-slate-400">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </button>
                        </form>
                    </div>
                    <div class="flex-1 overflow-y-auto p-4" id="staylMobileSearchResults">
                        {{-- Mobile Search Suggestions injected here --}}
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Row 2: Secondary Nav Bar --}}
    @if($isMenuVisible)
        <div class="stayl-yellow-bar stayl-dynamic-order border-b border-slate-200 dark:border-slate-800" style="--stayl-order: {{ $headerBarOrderIndex['header_bar_menu'] ?? 3 }};">
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
                                <div class="stayl-cat-dropdown stayl-mega-menu-panel" id="staylCatDropdown">
                                    <div class="stayl-mega-menu-inner p-4 md:p-6 max-h-[520px] overflow-y-auto custom-scrollbar">
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                                            {{-- Dynamic Categories with Perfectly Square Cards (Full Bleed Image) --}}
                                            @foreach($__staylHeaderCategories->take(24) as $hc)
                                                <div class="w-full">
                                                    <a href="{{ route('category.products', [slug($hc->name), $hc->id]) }}" 
                                                       class="group relative block w-full rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-100 dark:border-slate-800 hover:border-sky-500 hover:shadow-xl transition-all duration-300"
                                                       style="padding-bottom: 100%; height: 0;">
                                                        @php $hcImage = $hc->imageShow(); @endphp
                                                        <img src="{{ $hcImage }}" 
                                                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                                             alt="{{ __($hc->name) }}" 
                                                             loading="lazy"
                                                             onerror="this.onerror=null;this.src='{{ stayl_placeholder_icon_data_url() }}';">
                                                        
                                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent p-3 pt-8 transform translate-y-1 group-hover:translate-y-0 transition-transform duration-300">
                                                            <p class="text-[10px] font-bold text-center text-white uppercase tracking-wider truncate drop-shadow-md">
                                                                {{ __($hc->name) }}
                                                            </p>
                                                        </div>
                                                        <div class="absolute inset-0 bg-sky-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <ul class="stayl-nav-ul flex items-center h-full">
                        @forelse($menuNavLinks as $mbtn)
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
                                    <a href="{{ trim((string)($mbtnUrl ?? '#')) ?: '#' }}" class="stayl-flex-center stayl-nav-link-premium h-full"
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
                                <li class="h-full flex items-center"><a href="{{ $mbtnUrl }}" data-header-track="{{ $mbtnTrack }}" class="stayl-nav-link-premium h-full flex items-center">{{ __($mbtnLabel) }}</a></li>
                            @endif
                        @empty
                            {{-- Fallback Links to match design if admin config is empty --}}
                            <li class="h-full flex items-center"><a href="{{ route('home') }}" class="stayl-nav-link-premium px-3">@lang('About Us')</a></li>
                            <li class="h-full flex items-center"><a href="{{ route('home') }}" class="stayl-nav-link-premium px-3">@lang('Latest Blog')</a></li>
                            <li class="h-full flex items-center"><a href="{{ route('contact') }}" class="stayl-nav-link-premium px-3">@lang('Contact Us')</a></li>
                        @endforelse
                    </ul>
                </nav>

                @if(!empty($headerMenuCfg['show_seller_button']))
                    <a href="{{ $sellerAccountEnabled ? route('seller.apply') : 'javascript:void(0)' }}" 
                        @if(!$sellerAccountEnabled) onclick="openSellerModal()" @endif
                        class="stayl-seller-btn text-[12px] font-bold tracking-wide uppercase bg-orange-50 text-orange-600 border border-orange-200 hover:bg-orange-500 hover:text-white transition-colors duration-300 rounded-md px-4 py-2 flex items-center gap-2">
                        {{ __('SELLER') }}
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
    (function() {
        "use strict";

        // 1. Critical Constants & Helpers
        const master = document.querySelector('.stayl-fixed-master');
        if (!master) return;

        /**
         * Dynamic Header Height Synchronization
         */
        window.syncHeaderHeightVar = function() {
            requestAnimationFrame(() => {
                const masterEl = document.querySelector('.stayl-fixed-master');
                if (!masterEl) return;
                
                let h = 0;
                if (masterEl.classList.contains('is-scrolled-down')) {
                    h = 72;
                } else {
                    // Sum the heights of visible children to avoid collapsed parent bounding rect height bugs
                    Array.from(masterEl.children).forEach(child => {
                        const style = window.getComputedStyle(child);
                        if (style.display !== 'none' && style.visibility !== 'hidden' && style.position !== 'absolute') {
                            h += child.offsetHeight || 0;
                        }
                    });
                }
                
                if (h === 0) {
                    h = masterEl.getBoundingClientRect().height || 136;
                }
                
                document.documentElement.style.setProperty('--stayl-dynamic-header-height', Math.round(h) + 'px');
            });
        };
        const syncHeaderHeightVar = window.syncHeaderHeightVar;

        // --- Bulletproof Scroll Detection System ---
        (function() {
            const masterEl = document.querySelector('.stayl-fixed-master');
            if (!masterEl) return;

            // Create a sentinel element at the threshold point
            const sentinel = document.createElement('div');
            sentinel.id = 'stayl-header-sentinel';
            sentinel.style.cssText = 'position:absolute; top:20px; left:0; width:1px; height:1px; pointer-events:none; visibility:hidden; z-index:-1;';
            document.body.prepend(sentinel);

            const observer = new IntersectionObserver((entries) => {
                const isAtTop = entries[0].isIntersecting;
                if (isAtTop) {
                    masterEl.classList.remove('is-scrolled-down');
                    console.log('[Stayl] State: At Top');
                } else {
                    masterEl.classList.add('is-scrolled-down');
                    console.log('[Stayl] State: Scrolled Down');
                }
                syncHeaderHeightVar();
            }, { 
                root: null, // viewport
                threshold: 0 
            });

            observer.observe(sentinel);

            // Fallback for manual height syncs
            window.addEventListener('resize', syncHeaderHeightVar, { passive: true });
            window.addEventListener('load', syncHeaderHeightVar);
            setTimeout(syncHeaderHeightVar, 500);
        })();

        // 2. Sub-Modules Area
        function setupMegaMenu() {
            const container = document.getElementById('staylCatContainer');
            const dropdown = document.getElementById('staylCatDropdown');
            if (!container || !dropdown) return;

            const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            if (isTouch) {
                const btn = document.getElementById('staylCatBtn');
                if (btn) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        dropdown.classList.toggle('is-active');
                    });
                }
                document.addEventListener('click', (e) => {
                    if (!container.contains(e.target)) dropdown.classList.remove('is-active');
                });
            }
        }

        /** Sub-Module: Mobile Search Overlay */
        function setupMobileSearch() {
            const toggle = document.getElementById('staylMobileSearchToggle');
            const overlay = document.getElementById('staylMobileSearchOverlay');
            const close = document.getElementById('staylMobileSearchClose');
            const input = document.getElementById('staylMobileSearchInput');
            const results = document.getElementById('staylMobileSearchResults');

            if (!toggle || !overlay || !close) return;

            toggle.addEventListener('click', () => {
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
                document.body.style.overflow = 'hidden';
                setTimeout(() => input && input.focus(), 300);
            });

            const hideOverlay = () => {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
                document.body.style.overflow = '';
            };

            close.addEventListener('click', hideOverlay);

            if (input && results) {
                input.addEventListener('input', debounce((e) => {
                    const q = e.target.value.trim();
                    if (q.length < 1) { results.innerHTML = ''; return; }
                    fetchSearch(q, results);
                }, 300));
            }
        }

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func(...args), wait);
            };
        }

        async function fetchSearch(query, container) {
            try {
                const response = await fetch(`{{ route('search.universal') }}?search=${encodeURIComponent(query)}`);
                const data = await response.json();
                if (data.success) renderMobileResults(data.results, container);
            } catch (err) { console.error('Search failed', err); }
        }

        function renderMobileResults(results, container) {
            let html = '';
            if (results.products && results.products.length > 0) {
                html += `<div class="mb-6"><h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">@lang('Products')</h4><div class="flex flex-col gap-4">`;
                results.products.slice(0, 8).forEach(p => {
                    html += `
                        <a href="${p.url}" class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-slate-50 flex items-center justify-center border border-slate-100 overflow-hidden">
                                <img src="${p.image}" class="w-10 h-10 object-contain" alt="">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">${p.name}</p>
                                <p class="text-xs text-red-500 font-bold">${p.price_formated || p.price}</p>
                            </div>
                        </a>`;
                });
                html += `</div></div>`;
            }
            if (results.categories && results.categories.length > 0) {
                html += `<div class="mb-4"><h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">@lang('Discovery')</h4><div class="flex flex-wrap gap-2">`;
                results.categories.forEach(c => {
                    html += `<a href="${c.url}" class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-full text-[11px] font-medium text-slate-700 dark:text-slate-300">${c.name}</a>`;
                });
                html += `</div></div>`;
            }
            container.innerHTML = html || `<div class="text-center py-10"><p class="text-sm text-slate-400">@lang('No results found')</p></div>`;
        }

        function setupVoiceSearch() {
            const voiceBtn = document.getElementById('voiceSearchBtn');
            const searchInput = document.getElementById('universalSearchInput');
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
                recognition.interimResults = true; // Show text as you speak
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
            voiceBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (voiceBtn.classList.contains('is-listening')) {
                    if (recognition) recognition.stop();
                    return;
                }

                setVoiceStatus('', true);
                startRecognition();
            });
            
            if (recognition) {
                recognition.onstart = function() {
                    voiceBtn.classList.add('is-listening', 'voice-pulse');
                    setVoiceStatus('', true);
                };
                
                recognition.onend = function() {
                    voiceBtn.classList.remove('is-listening', 'voice-pulse');
                    setVoiceStatus('', false);
                };
                
                recognition.onresult = function(event) {
                    let interimTranscript = '';
                    let finalTranscript = '';

                    for (let i = event.resultIndex; i < event.results.length; ++i) {
                        if (event.results[i].isFinal) {
                            finalTranscript += event.results[i][0].transcript;
                        } else {
                            interimTranscript += event.results[i][0].transcript;
                        }
                    }

                    const result = finalTranscript || interimTranscript;
                    if (result) {
                        searchInput.value = result.trim();
                        searchInput.focus();
                    }
                    
                    if (finalTranscript) {
                        setVoiceStatus('Success', false);
                    }
                };

                recognition.onerror = function(event) {
                    voiceBtn.classList.remove('is-listening', 'voice-pulse');
                    console.error("Speech Error:", event.error);
                    const err = event.error || 'unknown';
                    if (err === 'not-allowed') alert("Microphone permission denied.");
                    setVoiceStatus('Error: ' + err, false);
                };
            }
        }


        function setupLiveLocationAndWeather() {
            const locWrapper = document.getElementById('stayl-live-location');
            const locText = document.querySelector('.stayl-location-text');
            const weatherText = document.querySelector('.stayl-weather-text');
            const weatherSvg = document.getElementById('stayl-weather-svg');
            if (!locText || !weatherText || !locWrapper) return;

            // High-Fidelity Literal Weather SVGs (Library Compliant)
            const icons = {
                sunny: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="6" fill="#FBBF24" stroke="#F59E0B" stroke-width="1.5"/><path d="M12 2V4M12 20V22M4.93 4.93L6.34 6.34M17.66 17.66L19.07 19.07M2 12H4M20 12H22M6.34 17.66L4.93 19.07M19.07 4.93L17.66 6.34" stroke="#FBBF24" stroke-width="2" stroke-linecap="round"/></svg>`,
                cloudy: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.5 19H9C5.13401 19 2 15.866 2 12C2 8.13401 5.13401 5 9 5H9.79C11.1 2.5 14.1 1 17.5 1C21.0899 1 24 3.91015 24 7.5C24 11.0899 21.0899 14 17.5 14H17.5V19Z" fill="#94A3B8" fill-opacity="0.3" stroke="#94A3B8" stroke-width="2" stroke-linejoin="round"/></svg>`,
                rainy: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 10c0-4.418-3.582-8-8-8S4 5.582 4 10a8 8 0 0 0 8 8 8 8 0 0 0 8-8Z" fill="#38BDF8" fill-opacity="0.2" stroke="#38BDF8" stroke-width="2"/><path d="M12 18v4M8 17v4M16 17v4" stroke="#0EA5E9" stroke-width="2" stroke-linecap="round"/></svg>`,
                stormy: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 10c0-4.418-3.582-8-8-8S4 5.582 4 10a8 8 0 0 0 8 8 8 8 0 0 0 8-8Z" fill="#1E293B" fill-opacity="0.4" stroke="#475569" stroke-width="2"/><path d="M13 14l-4 6h5l-4 6" stroke="#FBBF24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`
            };

            // Inline Professional SVGs for Ticker
            // Vibrant Multi-color Professional SVGs
            // Official Lucide Iconic SVGs (Retrieved from Downloads/s)
            const vSvgSunrise = `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FBBF24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 6px;"><path d="M12 2v8"/><path d="m4.93 10.93 1.41 1.41"/><path d="M2 18h2"/><path d="M20 18h2"/><path d="m19.07 10.93-1.41 1.41"/><path d="M22 22H2"/><path d="m8 6 4-4 4 4"/><path d="M16 18a4 4 0 0 0-8 0"/></svg>`;
            const vSvgSunset = `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#F43F5E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 6px;"><path d="M12 10V2"/><path d="m4.93 10.93 1.41 1.41"/><path d="M2 18h2"/><path d="M20 18h2"/><path d="m19.07 10.93-1.41 1.41"/><path d="M22 22H2"/><path d="m16 10-4 4-4-4"/><path d="M16 18a4 4 0 0 0-8 0"/></svg>`;
            const vSvgHum = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#38BDF8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z" fill="#E0F2FE" fill-opacity="0.3"/></svg>`;
            const vSvgWind = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M9.59 4.59A2 2 0 1 1 11 8H2M10.59 19.41A2 2 0 1 0 14 16H2M15.73 8.27A2.5 2.5 0 1 1 19.5 12H2"/></svg>`;
            const vSvgCal = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2" fill="#F5F3FF" fill-opacity="0.2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`;
            const vSvgRain = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M16 13v8M8 13v8M12 15v8M20 16.58A5 5 0 0 0 18 7h-1.26A8 8 0 1 0 4 15.25" fill="#DBEAFE" fill-opacity="0.3"/></svg>`;
            const vSvgFeels = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FCA5A5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><path d="M14 4v10.54a4 4 0 1 1-4 0V4a2 2 0 0 1 4 0Z" fill="#FEE2E2" fill-opacity="0.3"/></svg>`;
            const vSvgUV = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FBBF24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><circle cx="12" cy="12" r="4" fill="#FEF3C7" fill-opacity="0.4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>`;
            const vSvgAlert = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px; filter: drop-shadow(0 0 6px rgba(239,68,68,0.6));"><path d="m12 19 .01 0M12 8v7M3 20h18L12 4z"/></svg>`;

            const getIconByCode = (code) => {
                if (code <= 1) return icons.sunny;
                if (code <= 3) return icons.cloudy;
                if (code >= 51 && code <= 67 || code >= 80 && code <= 82) return icons.rainy;
                if (code >= 95) return icons.stormy;
                return icons.cloudy;
            };

            const getDescByCode = (code) => {
                if (code === 0) return 'Sunny';
                if (code === 1 || code === 2) return 'Mostly Sunny';
                if (code === 3) return 'Cloudy';
                if (code >= 51 && code <= 67) return 'Rainy';
                if (code >= 80 && code <= 82) return 'Showers';
                if (code >= 95) return 'Thunderstorm';
                return 'Clear';
            };

            // Absolute Stability Animator (Fixed Container, No Layout Shifts)
            const applyTicker = (messages) => {
                weatherText.classList.remove('opacity-0');
                weatherText.classList.add('opacity-100');
                
                let idx = 0;
                weatherText.innerHTML = messages[idx];
                
                if (messages.length > 1) {
                    // Clear existing intervals if any to prevent double-looping
                    if (window.staylWeatherInterval) clearInterval(window.staylWeatherInterval);
                    
                    window.staylWeatherInterval = setInterval(() => {
                        weatherText.classList.replace('opacity-100', 'opacity-0');
                        setTimeout(() => {
                            idx = (idx + 1) % messages.length;
                            weatherText.innerHTML = messages[idx];
                            weatherText.classList.replace('opacity-0', 'opacity-100');
                        }, 500);
                    }, 4500);
                }
            };

            const fallbackWeather = () => {
                if(weatherSvg) weatherSvg.innerHTML = icons.sunny;
                applyTicker([
                    `32°C Sunny | ${vSvgHum} Hum: 60% | ${vSvgWind} 12km/h`, 
                    `${vSvgSunrise} Sunrise: 5:45 AM | ${vSvgSunset} Sunset: 6:15 PM`, 
                    `${vSvgCal} Today's High: 35°C | Low: 25°C`
                ]);
            };

            const formatTime = (isoStr) => {
                if (!isoStr) return '';
                const d = new Date(isoStr);
                let h = d.getHours();
                let m = d.getMinutes();
                const ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12;
                h = h ? h : 12;
                m = m < 10 ? '0' + m : m;
                return `${h}:${m} ${ampm}`;
            };

            const getAlertMessage = (code, wind) => {
                if (code >= 95) return `${vSvgAlert} <span style="color:#ef4444;font-weight:700;">SEVERE STORM ALERT | Seek Shelter Now</span>`;
                if (code >= 80) return `${vSvgAlert} <span style="color:#f97316;font-weight:700;">HEAVY RAIN WARNING | Flash Flood Risk</span>`;
                if (wind > 50) return `${vSvgAlert} <span style="color:#facc15;font-weight:700;">GALE WARNING | High Winds Alert</span>`;
                if (code >= 71) return `${vSvgAlert} <span style="color:#60a5fa;font-weight:700;">SNOWSTORM ALERT | Travel Hazards</span>`;
                return null;
            };

            const processWeather = (ipData) => {
                let lat = ipData.latitude || 23.8103;
                let lon = ipData.longitude || 90.4125;
                let city = ipData.city || 'Dhaka';
                let country = ipData.country_name || 'Bangladesh';

                // Phase 2: Granular Reverse Geocoding
                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json&accept-language=en`, {
                    headers: {} // CSRF fix for external fetch
                })
                    .then(res => res.json())
                    .then(geoData => {
                        let area = geoData.address.suburb || geoData.address.neighbourhood || geoData.address.residential || geoData.address.city_district || '';
                        locText.textContent = `${country}, ${city}`;
                        locWrapper.title = `Full Location: ${country}, ${city}${area ? ' (' + area + ')' : ''}`;
                        
                        const cached = JSON.parse(localStorage.getItem('stayl_weather_cache') || '{}');
                        if (cached.timestamp) {
                            cached.fullLoc = locText.textContent;
                            cached.tooltip = locWrapper.title;
                            localStorage.setItem('stayl_weather_cache', JSON.stringify(cached));
                        }
                    })
                    .catch(() => {
                        locText.textContent = `${country}, ${city}`;
                        locWrapper.title = `${country}, ${city}`;
                    });
                
                fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&hourly=temperature_2m,relativehumidity_2m,windspeed_10m,precipitation_probability,weathercode,apparent_temperature,uv_index&daily=sunrise,sunset,temperature_2m_max,temperature_2m_min,precipitation_probability_max&timezone=auto`, {
                    headers: {} // CSRF fix for external fetch
                })
                    .then(res => res.json())
                    .then(weatherData => {
                        if (weatherData && weatherData.current_weather) {
                            const cur = weatherData.current_weather;
                            const currentTemp = Math.round(cur.temperature);
                            const wind = Math.round(cur.windspeed);
                            
                            if(weatherSvg) {
                                weatherSvg.innerHTML = getIconByCode(cur.weathercode);
                            }

                            const now = new Date();
                            const currentHourIso = now.toISOString().slice(0, 13) + ":00";
                            let cIdx = weatherData.hourly.time.findIndex(t => t.startsWith(currentHourIso));
                            if(cIdx === -1) cIdx = 0;

                            const humidity = weatherData.hourly.relativehumidity_2m[cIdx] || 50;
                            const apparentTemp = Math.round(weatherData.hourly.apparent_temperature[cIdx] || cur.temperature);
                            const uvIndex = Math.round(weatherData.hourly.uv_index[cIdx] || 0);
                            const rainProb = weatherData.hourly.precipitation_probability[cIdx] || 0;
                            const condition = getDescByCode(cur.weathercode);

                            let messages = [];
                            const alertMsg = getAlertMessage(cur.weathercode, wind);
                            if (alertMsg) messages.push(alertMsg);

                            // Expanded Information Architecture
                            messages.push(`${currentTemp}°C ${condition} | ${vSvgFeels} Feels: ${apparentTemp}°C`);
                            messages.push(`${vSvgRain} Rain Chance: ${rainProb}% | ${vSvgHum} ${humidity}%`);
                            
                            if (weatherData.daily) {
                                messages.push(`Today ${vSvgSunrise} ${formatTime(weatherData.daily.sunrise[0])} | ${vSvgSunset} ${formatTime(weatherData.daily.sunset[0])}`);
                                if (weatherData.daily.sunrise[1]) {
                                    messages.push(`Tomorrow ${vSvgSunrise} ${formatTime(weatherData.daily.sunrise[1])} | ${vSvgSunset} ${formatTime(weatherData.daily.sunset[1])}`);
                                }
                                messages.push(`${vSvgCal} High: ${Math.round(weatherData.daily.temperature_2m_max[0])}°C | Low: ${Math.round(weatherData.daily.temperature_2m_min[0])}°C`);
                            }
                            messages.push(`${vSvgWind} Wind: ${wind} km/h | ${vSvgUV} UV: ${uvIndex}`);

                            const cacheData = { 
                                timestamp: Date.now(), 
                                fullLoc: locText.textContent,
                                tooltip: locWrapper.title,
                                messages, 
                                code: cur.weathercode 
                            };
                            localStorage.setItem('stayl_weather_cache', JSON.stringify(cacheData));
                            applyTicker(messages);
                        } else {
                            fallbackWeather();
                        }
                    })
                    .catch(() => fallbackWeather());
            };

            // Instant Cache-First Logic (Eliminates "Slow" Feeling)
            const cached = localStorage.getItem('stayl_weather_cache');
            if (cached) {
                const c = JSON.parse(cached);
                if (Date.now() - c.timestamp < 1800000) { // 30 mins cache
                    locText.textContent = c.fullLoc || 'Bangladesh, Dhaka';
                    locWrapper.title = c.tooltip || locText.textContent;
                    if(weatherSvg) weatherSvg.innerHTML = getIconByCode(c.code);
                    applyTicker(c.messages);
                    return; 
                }
            }

            fetch('https://ipapi.co/json/')
                .then(res => res.json())
                .then(data => processWeather(data))
                .catch(() => processWeather({error: true}));
        }

        // Initialize with Performance Optimizations
        const initHeavyFeatures = () => {
            try { syncHeaderHeightVar(); handleHeaderScroll(); } catch(e){}
            // Defer weather fetching to keep the initial load extremely light
            setTimeout(() => {
                try { setupLiveLocationAndWeather(); } catch(e){ console.error("Weather init err:", e); }
            }, 1500);
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initHeavyFeatures);
        } else {
            initHeavyFeatures();
        }
        window.addEventListener('resize', syncHeaderHeightVar, { passive: true });
        window.addEventListener('load', syncHeaderHeightVar, { once: true });

        const baseCurrency = {
            code: @json($general->cur_text ?? 'BDT'),
            symbol: @json($general->cur_sym ?? '৳')
        };
        const headerCurrencyLabel = document.getElementById('staylCurrentCurrencyLabel');
        if (headerCurrencyLabel) headerCurrencyLabel.classList.add('notranslate');
        const currencyNativeNames = {
            BDT: 'বিডিটি (টাকা)',
            USD: 'মার্কিন ডলার',
            INR: 'ভারতীয় রুপি',
            PKR: 'পাকিস্তানি রুপি',
            SAR: 'সৌদি রিয়াল',
            AED: 'ইউএই দিরহাম',
            MYR: 'মালয়েশিয়ান রিঙ্গিত',
            EUR: 'ইউরো',
            GBP: 'ব্রিটিশ পাউন্ড',
            SGD: 'সিঙ্গাপুর ডলার',
            JPY: 'জাপানি ইয়েন',
            CAD: 'কানাডিয়ান ডলার',
            AUD: 'অস্ট্রেলিয়ান ডলার',
            QAR: 'কাতারি রিয়াল',
            KWD: 'কুয়েতি দিনার',
            RUB: 'রাশিয়ান রুবল',
            UAH: 'ইউক্রেনীয় রিভনিয়া',
            NZD: 'নিউজিল্যান্ড ডলার',
            ZAR: 'দক্ষিণ আফ্রিকান র‍্যান্ড',
            CNY: 'চীনা ইউয়ান',
            BRL: 'ব্রাজিলীয় রিয়াল',
            TRY: 'তুর্কি লিরা',
            KRW: 'দক্ষিণ কোরিয়ান ওয়ান'
        };
        const langNativeNames = { BN: 'বাংলা', EN: 'English', HI: 'হিন্দি' };
        const headerLanguageLabel = document.getElementById('staylCurrentLanguageLabel');
        const currencySymbols = {
            BDT: '৳', USD: '$', EUR: '€', GBP: '£', INR: '₹', AED: 'د.إ', SAR: 'SR', MYR: 'RM', SGD: '$', JPY: '¥',
            PKR: '₨', LKR: 'Rs', NPR: 'रु', BTN: 'Nu.', MVR: 'Rf', AFN: '؋',
            RUB: '₽', CNY: '¥', KRW: '₩', AUD: '$', CAD: '$', BRL: 'R$', ZAR: 'R', TRY: '₺', QAR: 'ر.ق', KWD: 'د.ك', UAH: '₴'
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
            // Aggressive cleaning to prevent double symbol issues
            let cleaned = normalizedDigits;
            const symbolsToRemove = ['৳','$','€','£','₹','₨','د.إ','ر.س','SR','RM','¥','₽','₴','₺','₩','Rs','रु','Nu.','Rf','؋','R$','R','ر.ق','د.ك', sym];
            symbolsToRemove.forEach(s => {
                if(s) cleaned = cleaned.replace(new RegExp(s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g'), '');
            });
            cleaned = cleaned.replace(/[^0-9.,-]/g, '').replace(/,/g, '');
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
            const saved = (getStaylCookie('stayl_display_currency_code') || '').toUpperCase();
            let rates = await loadRates(baseCurrency.code);
            if (!rates) rates = fallbackRates;
            let activeCurrencyCode = '';
            let mutationDebounce = null;
            let isApplyingCurrency = false;
            function markCurrencyOption(code) {
                currencyOptions.forEach(function (el) {
                    const itemCode = (el.getAttribute('data-stayl-currency-option') || '').toUpperCase();
                    const on = itemCode === code;
                    el.setAttribute('aria-selected', on ? 'true' : 'false');
                    el.classList.toggle('bg-sky-50', on);
                    el.classList.toggle('text-sky-700', on);
                    el.classList.toggle('dark:bg-sky-950/40', on);
                    el.classList.toggle('dark:text-sky-400', on);
                    el.classList.toggle('text-slate-800', !on);
                    el.classList.toggle('dark:text-slate-200', !on);
                    const check = el.querySelector('[data-stayl-currency-check]');
                    if (check) {
                        check.classList.toggle('hidden', !on);
                    }
                });
            }
            const headerCurrencyLabel = document.getElementById('staylCurrentCurrencyLabel');

            function applyNow(code) {
                if (isApplyingCurrency) return;
                isApplyingCurrency = true;

                activeCurrencyCode = (code || '').toUpperCase() || baseCurrency.code.toUpperCase();
                const activeLangCode = "{{ $currentLangCode }}";

                // numeral formatting logic: native for BDT + BN lang, english for others
                const toNative = function(val) {
                    if (activeLangCode !== 'BN' || activeCurrencyCode !== 'BDT') return val;
                    const m = {'0':'০','1':'১','2':'২','3':'৩','4':'৪','5':'৫','6':'৬','7':'৭','8':'৮','9':'৯','.' :'.',',':','};
                    return String(val).split('').map(c => m[c] || c).join('');
                };

                const currencyNamesEN = { BDT: 'BDT', USD: 'USD', EUR: 'EUR', INR: 'INR' };
                const cNameMap = activeLangCode === 'BN' ? currencyNativeNames : currencyNamesEN;

                if (headerCurrencyLabel) {
                    const cData = @json($headerCurrencies).find(c => String(c.code).toUpperCase() === activeCurrencyCode) || { flag: '' };
                    headerCurrencyLabel.textContent = activeCurrencyCode;
                    const triggerBtn = headerCurrencyLabel.closest('button');
                    if (triggerBtn) {
                        const img = triggerBtn.querySelector('img');
                        if (img && cData.flag) {
                            img.src = cData.flag;
                        }
                    }
                }


                const currentSymbol = currencySymbols[activeCurrencyCode] || baseCurrency.symbol || '$';
                const rate = Number(rates[activeCurrencyCode] ?? fallbackRates[activeCurrencyCode] ?? 1);
                window.__staylDisplayCurrency = { code: activeCurrencyCode, symbol: currentSymbol, rate: rate };
                document.documentElement.setAttribute('data-display-currency', activeCurrencyCode);

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
                        // Add notranslate to prevent Google Translate interference
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
            
            // MutationObserver removed for performance optimization. 
            // Dynamic elements should now trigger: window.dispatchEvent(new CustomEvent('staylbd:product-updated'))
            
            currencyOptions.forEach(function (optionEl) {
                optionEl.addEventListener('click', function (event) {
                    event.preventDefault();
                    const code = (this.getAttribute('data-stayl-currency-option') || '').toUpperCase() || baseCurrency.code;
                    setStaylCookie('stayl_display_currency_code', code);
                    applyNow(code);
                    window.location.reload();
                });
            });
            window.addEventListener('staylbd:product-updated', function () {
                const currentStored = (getStaylCookie('stayl_display_currency_code') || baseCurrency.code).toUpperCase();
                window.requestAnimationFrame(function () {
                    applyNow(currentStored);
                });
            });
        }
        function initDisplayLanguageLabel() {
            if (!headerLanguageLabel) return;
            const savedLang = (getStaylCookie('stayl_display_language_code') || '').toUpperCase();
            if (savedLang) {
                // Keep header button label consistent with UI (code only)
                headerLanguageLabel.textContent = savedLang;
            }
            document.querySelectorAll('[data-stayl-lang-option]').forEach(function (el) {
                el.addEventListener('click', function (event) {
                    event.preventDefault();
                    const code = (this.getAttribute('data-stayl-lang-option') || '').toUpperCase();
                    if (code) {
                        setStaylCookie('stayl_display_language_code', code);
                        
                        // Briefly update label for instant feedback before reload (code only)
                        if (headerLanguageLabel) {
                            headerLanguageLabel.textContent = code;
                        }
                        window.location.href = this.href;
                    }
                });
            });
        }
        // Execution Phase (Wrapped in try-catch for robustness)
        try { setupMegaMenu(); } catch(e) { console.warn('MegaMenu init failed', e); }
        try { setupMobileSearch(); } catch(e) { console.warn('MobileSearch init failed', e); }
        try { setupLiveLocationAndWeather(); } catch(e) { console.warn('Weather init failed', e); }
        try { initDisplayLanguageLabel(); } catch(e) { console.warn('Lang init failed', e); }
        try { initDisplayCurrency(); } catch(e) { console.warn('Currency init failed', e); }
        
        // Lucide fallback fix for missing icons
        if (typeof lucide !== 'undefined') {
            try { lucide.createIcons(); } catch(e) {}
        }

        // Seller Modal Functions
        window.openSellerModal = function() {
            const modal = document.getElementById('sellerDisabledModal');
            if (modal) {
                modal.classList.remove('hidden');
                setTimeout(() => modal.classList.add('is-active'), 10);
                document.body.style.overflow = 'hidden';
            }
        };

        window.closeSellerModal = function() {
            const modal = document.getElementById('sellerDisabledModal');
            if (modal) {
                modal.classList.remove('is-active');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }, 300);
            }
        };
    })();
</script>

{{-- Seller Disabled Modal HTML --}}
<div id="sellerDisabledModal" class="stayl-modal-overlay hidden">
    <div class="stayl-modal-card">
        <button class="stayl-modal-close" onclick="closeSellerModal()">&times;</button>
        <div class="stayl-modal-body text-center p-5 pt-5 mt-4">
            <div class="seller-disabled-icon mb-4">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"/></svg>
            </div>
            <h3 class="h4 mb-3 font-bold text-slate-800 dark:text-white">@lang('Onboarding Paused')</h3>
            <p class="text-muted dark:text-slate-400 mb-4 px-3" style="line-height: 1.6;">
                @lang('We are currently updating our seller portal and not accepting new applications at this moment. Please check back later or contact support for further information.')
            </p>
            <div class="flex justify-center gap-3 mb-2">
                <button onclick="closeSellerModal()" class="btn-elite-secondary cursor-pointer">@lang('Close')</button>
                <button onclick="closeSellerModal()" class="btn-elite-primary js-contact-panel-open cursor-pointer">@lang('Contact Support')</button>
            </div>
        </div>
    </div>
</div>

 @push('script')
 <script src="{{ asset('assets/templates/basic/js/banner-slider.js') }}"></script>
 @endpush
