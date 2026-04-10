{{-- Restored baseline-safe header structure --}}
@php
    $customButtonsAll = \App\Models\Frontend::where('data_keys', 'custom_buttons.element')->orderBy('id', 'asc')->get();
    $customHeaderButtons = $customButtonsAll->filter(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (($dv['target'] ?? '') === 'header') && ((int) ($dv['is_active'] ?? 1) === 1);
    })->sortBy(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (int) ($dv['display_order'] ?? 0);
    })->values();
    $headerButtonsByPosition = [
        'left' => $customHeaderButtons->filter(fn($r) => (($r->data_values->position ?? '') === 'left')),
        'nav' => $customHeaderButtons->filter(fn($r) => (($r->data_values->position ?? '') === 'nav')),
        'right' => $customHeaderButtons->filter(fn($r) => (($r->data_values->position ?? '') === 'right')),
    ];
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
@endphp
{{-- Typography: Inter subset + icons in compiled storefront CSS (see tailwind-storefront.css) --}}
<header class="glass-header font-sans antialiased fixed top-0 left-0 right-0 z-[99999] w-full supports-[backdrop-filter]:backdrop-blur-md">
    <div class="glass-header__shell">
        <div class="glass-header__max flex items-center w-full min-h-[52px]">
        <div class="glass-header-wrapper flex items-center justify-between flex-nowrap w-full gap-4 min-h-[52px]">
            <!-- Left: Logo + Hamburger -->
            <div class="glass-header-left flex items-center gap-[15px] shrink-0">
                <button class="glass-menu-toggle d-lg-none" type="button" id="glassMenuToggle" aria-label="@lang('Toggle Menu')">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <div class="glass-logo {{ getLogoEffectClasses() }}">
                    <a href="{{ route('home') }}" title="@lang('Home')" class="rounded-lg transition-opacity hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                        @php $headerLogo = getLogo('logo'); @endphp
                        @if($headerLogo)
                            <img src="{{ $headerLogo }}" alt="{{ gs('site_name') }}" class="site-logo-img" width="{{ getLogoMaxWidth() }}" height="{{ getLogoMaxHeight() }}" fetchpriority="high" loading="eager" decoding="async" style="max-width: {{ getLogoMaxWidth() }}px; max-height: {{ getLogoMaxHeight() }}px; {{ getLogoStyle() }}">
                        @else
                            <span class="logo-text">{{ gs('site_name') }}</span>
                        @endif
                    </a>
                </div>

                {{-- Desktop category menu (native &lt;details&gt; — keyboard + click-outside friendly) --}}
                @if($__staylHeaderCategories->isNotEmpty())
                <details class="group/cat relative hidden shrink-0 lg:block">
                    <summary class="flex cursor-pointer list-none items-center gap-2 rounded-xl border border-slate-200/70 bg-white/30 px-3 py-2 text-sm font-semibold text-slate-800 shadow-sm backdrop-blur-sm transition hover:bg-white/55 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 marker:hidden [&::-webkit-details-marker]:hidden">
                        @include($activeTemplate . 'partials.icon', ['name' => 'th-large', 'class' => 'h-4 w-4 text-emerald-600'])
                        @lang('Categories')
                        <svg class="h-4 w-4 text-slate-500 transition group-open/cat:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                    </summary>
                    <div class="absolute left-0 top-[calc(100%+8px)] z-[100220] hidden max-h-[min(28rem,72vh)] w-[min(20rem,calc(100vw-2rem))] overflow-y-auto rounded-xl border border-slate-200/90 bg-white py-2 shadow-xl ring-1 ring-slate-900/5 group-open/cat:block" role="menu">
                        @foreach($__staylHeaderCategories as $hc)
                            <div class="border-b border-slate-100 px-2 pb-1 last:border-b-0">
                                <a href="{{ route('category.products', [slug($hc->name), $hc->id]) }}" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-800 transition hover:bg-emerald-50 focus:outline-none focus-visible:bg-emerald-50" role="menuitem">{{ __($hc->name) }}</a>
                                @if($hc->subcategories && $hc->subcategories->isNotEmpty())
                                    <ul class="pb-1 pl-1">
                                        @foreach($hc->subcategories->take(10) as $sub)
                                            <li>
                                                <a href="{{ route('subcategory.products', [slug($sub->name), $sub->id]) }}" class="block rounded-md px-3 py-1.5 text-xs text-slate-600 transition hover:bg-slate-50 hover:text-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40" role="menuitem">{{ __($sub->name) }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endforeach
                        <div class="px-2 pt-1">
                            <a href="{{ route('category.all') }}" class="block rounded-lg bg-gradient-to-r from-emerald-600 to-teal-500 py-2.5 text-center text-xs font-bold text-white shadow-sm transition hover:from-emerald-500 hover:to-teal-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">@lang('View All Categories')</a>
                        </div>
                    </div>
                </details>
                @endif

                @foreach($headerButtonsByPosition['left'] as $btn)
                    @php $b = (array)($btn->data_values ?? []); $href = trim((string)($b['button_url'] ?? '#')) ?: '#'; @endphp
                    <a href="{{ $href }}" class="glass-nav-btn glass-custom-btn" title="{{ $b['button_text'] ?? 'Button' }}">
                        @if(!empty($b['icon_image']))
                            <img src="{{ asset('assets/images/frontend/custom_buttons/' . $b['icon_image']) }}" alt="{{ $b['button_text'] ?? 'Button' }}" class="staylbd-icon" width="22" height="22" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ stayl_placeholder_icon_data_url() }}';">
                        @else
                            @include($activeTemplate . 'partials.icon', ['name' => ($b['icon_name'] ?? 'circle')])
                        @endif
                    </a>
                @endforeach
            </div>

            <!-- Center: search pill (mic + search inside) + camera beside (same as other header round icons) -->
            <div class="glass-header-center glass-search-zone flex flex-[1_1_0%] min-w-0 max-w-[800px] mx-auto items-center self-center justify-center gap-2">
                <form action="{{ route('products') }}" method="GET" class="glass-search-form flex flex-1 min-w-0 flex-col justify-center" id="universalSearchForm" role="search" data-search-url="{{ url('/search/universal') }}" data-trending-url="{{ route('search.trending') }}" data-image-search-url="{{ url('/search/image') }}">
                    <div class="glass-search-wrapper glass-search-wrapper--card flex w-full min-w-0 flex-nowrap items-center rounded-xl transition-shadow focus-within:ring-2 focus-within:ring-emerald-500/35">
                        <input type="search"
                               class="glass-search-input font-sans min-w-0"
                               id="universalSearchInput"
                               name="search"
                               placeholder="@lang('Search products, brands, and more')"
                               value="{{ request()->search ?? null }}"
                               autocomplete="off"
                               spellcheck="false"
                               enterkeyhint="search"
                               data-search-url="{{ url('/search/universal') }}"
                               data-placeholder-listening="@lang('Listening… speak now')"
                               aria-label="@lang('Search products, brands, and more')"
                               onfocus="this.style.outline='none';this.style.boxShadow='none';this.style.border='none';"
                               onblur="this.style.outline='none';this.style.boxShadow='none';this.style.border='none';"
                               style="border:none !important;outline:none !important;box-shadow:none !important;-webkit-box-shadow:none !important;-moz-box-shadow:none !important;background:transparent !important;-webkit-appearance:none !important;appearance:none !important;">
                        <div class="glass-search-trailing shrink-0 flex items-center flex-nowrap" role="group" aria-label="@lang('Search actions')">
                        <button type="button" class="glass-search-icon glass-search-voice shrink-0" id="voiceSearchBtn" title="@lang('Voice Search')" aria-label="@lang('Voice Search')">
                            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'voice_search_icon', 'fallback' => 'microphone', 'svgClass' => 'icon-bold', 'width' => 20, 'height' => 20, 'alt' => ''])
                        </button>
                        <button type="submit" class="glass-search-icon glass-search-submit shrink-0" title="@lang('Search')" aria-label="@lang('Search')">
                            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'search_icon', 'fallback' => 'search', 'svgClass' => 'icon-bold', 'width' => 20, 'height' => 20, 'alt' => '', 'loading' => 'eager'])
                        </button>
                        </div>
                    </div>
                    <input type="file" id="imageSearchInput" accept="image/*" hidden tabindex="-1" aria-hidden="true">
                    <!-- Search Results Dropdown -->
                    <div class="glass-search-results" id="searchResults"></div>
                </form>
                <button type="button" class="glass-icon-btn glass-header-camera-btn shrink-0" id="cameraSearchBtn" title="@lang('Search by image')" aria-label="@lang('Search by image')" style="width:44px;height:44px;min-width:44px;min-height:44px;max-width:44px;max-height:44px;border-radius:9999px;aspect-ratio:1/1;display:inline-flex;align-items:center;justify-content:center;overflow:hidden;padding:0;">
                    @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'image_search_icon', 'fallback' => 'scan', 'width' => 22, 'height' => 22, 'alt' => ''])
                </button>
            </div>
            
            <!-- Navigation: icon-only links -->
            <nav class="glass-header-nav hidden items-center gap-[10px] shrink-0 whitespace-nowrap" aria-label="@lang('Quick links')">
                <a href="{{ route('products') }}" class="glass-nav-btn {{ menuActive('products') }}" title="@lang('Products')" aria-label="@lang('Products')">
                    @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'products_icon', 'fallback' => 'box', 'alt' => __('Products')])
                </a>
                <a href="{{ route('contact') }}" class="glass-nav-btn {{ request()->routeIs('contact') ? 'active' : '' }}" title="@lang('Contact')" aria-label="@lang('Contact')">
                    @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'contact_icon', 'fallback' => 'phone', 'alt' => __('Contact')])
                </a>
                <a href="{{ route('track.order') }}" class="glass-nav-btn {{ menuActive('track-order') }}" title="@lang('Track Order')" aria-label="@lang('Track Order')">
                    @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'track_order_icon', 'fallback' => 'shipping-fast', 'alt' => __('Track Order')])
                </a>
                @foreach($headerButtonsByPosition['nav'] as $btn)
                    @php $b = (array)($btn->data_values ?? []); $href = trim((string)($b['button_url'] ?? '#')) ?: '#'; @endphp
                    <a href="{{ $href }}" class="glass-nav-btn glass-custom-btn" title="{{ $b['button_text'] ?? 'Button' }}">
                        @if(!empty($b['icon_image']))
                            <img src="{{ asset('assets/images/frontend/custom_buttons/' . $b['icon_image']) }}" alt="{{ $b['button_text'] ?? 'Button' }}" class="staylbd-icon" width="22" height="22" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ stayl_placeholder_icon_data_url() }}';">
                        @else
                            @include($activeTemplate . 'partials.icon', ['name' => ($b['icon_name'] ?? 'circle')])
                        @endif
                    </a>
                @endforeach
            </nav>

            <!-- Right: All Features in One Line - serial order preserved even if CSS fails -->
            <div class="glass-header-right flex items-center gap-2 flex-nowrap shrink-0">
                @foreach($headerButtonsByPosition['right'] as $btn)
                    @php $b = (array)($btn->data_values ?? []); $href = trim((string)($b['button_url'] ?? '#')) ?: '#'; @endphp
                    <a href="{{ $href }}" class="glass-icon-btn glass-custom-btn" title="{{ $b['button_text'] ?? 'Button' }}">
                        @if(!empty($b['icon_image']))
                            <img src="{{ asset('assets/images/frontend/custom_buttons/' . $b['icon_image']) }}" alt="{{ $b['button_text'] ?? 'Button' }}" class="staylbd-icon" width="22" height="22" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ stayl_placeholder_icon_data_url() }}';">
                        @else
                            @include($activeTemplate . 'partials.icon', ['name' => ($b['icon_name'] ?? 'circle')])
                        @endif
                    </a>
                @endforeach
                <!-- Language selector -->
                @if($general->multi_language)
                    @php
                        $language = App\Models\Language::all();
                        $sessionLang = strtolower((string) session('lang', 'en'));
                        $lookupLang = $sessionLang === 'hi' ? 'hn' : $sessionLang;
                        $currentLang = $language->firstWhere('code', $sessionLang) ?? $language->firstWhere('code', $lookupLang);
                    @endphp
                    <div class="dropdown glass-dropdown-wrapper glass-lang-dropdown-wrap inline-flex shrink-0">
                        <button type="button" class="glass-icon-btn glass-lang-btn js-lang-dropdown-toggle" aria-expanded="false" title="{{ __($currentLang->name ?? 'EN') }}" aria-label="@lang('Language')">
                            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'language_icon', 'fallback' => 'language', 'alt' => __('Language')])
                            <span class="glass-badge-text d-none">{{ __($currentLang->name ?? 'EN') }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end glass-dropdown glass-lang-dropdown-menu">
                            @foreach ($language as $item)
                                <li>
                                    <a class="dropdown-item js-lang-switch" href="{{ route('lang', $item->code) }}" data-no-ajax data-lang-code="{{ $item->code }}">
                                        {{ __($item->name) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Notifications -->
                @auth
                    <a href="{{ route('user.notifications') }}" class="glass-icon-btn glass-notification-btn transition-transform duration-200 hover:scale-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2" title="@lang('Notifications')">
                        @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'notification_icon', 'fallback' => 'bell', 'alt' => __('Notifications')])
                        @if(($userNotificationCount ?? 0) > 0)
                            <span class="glass-badge show-notification-count">{{ $userNotificationCount }}</span>
                        @else
                            <span class="glass-badge show-notification-count d-none">0</span>
                        @endif
                    </a>
                @else
                    <a href="{{ route('user.login') }}" class="glass-icon-btn glass-notification-btn transition-transform duration-200 hover:scale-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2" title="@lang('Notifications')" aria-label="@lang('Sign in to view notifications')">
                        @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'notification_icon', 'fallback' => 'bell', 'alt' => __('Notifications')])
                    </a>
                @endauth

                <!-- Wishlist – same URL for guest and logged-in -->
                <a href="{{ route('user.wishlist') }}" id="header-wishlist" class="glass-icon-btn glass-wishlist-btn transition-transform duration-200 hover:scale-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2" title="@lang('Wishlist')" @auth data-dashboard-link="1" @endauth>
                    @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'wishlist_icon', 'fallback' => 'heart', 'alt' => __('Wishlist')])
                    <span class="glass-badge show-wishlist-count">0</span>
                </a>

                <!-- Compare (secondary — available in mobile menu) -->
                <a href="{{ route('user.compare') }}" id="header-compare" class="glass-icon-btn glass-compare-btn hidden transition-transform duration-200 hover:scale-105 2xl:inline-flex" title="@lang('Compare')" @auth data-dashboard-link="1" @endauth>
                    @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'compare_icon', 'fallback' => 'exchange-alt', 'alt' => __('Compare')])
                    <span class="glass-badge show-compare-count">0</span>
                </a>

                <!-- Cart – same URL for guest and logged-in -->
                <a href="{{ route('user.cart') }}" id="header-cart" class="glass-icon-btn glass-cart-btn transition-transform duration-200 hover:scale-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2" title="@lang('Cart')" @auth data-dashboard-link="1" @endauth>
                    @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'cart_icon', 'fallback' => 'shopping-cart', 'alt' => __('Cart')])
                    <span class="glass-badge show-cart-count">0</span>
                </a>

                <!-- Orders (secondary — in account menu / mobile) -->
                @auth
                    <a href="{{ route('user.order.index') }}" class="glass-icon-btn glass-orders-btn hidden transition-transform duration-200 hover:scale-105 2xl:inline-flex" title="@lang('My Orders')">
                        @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'orders_icon', 'fallback' => 'list-alt', 'alt' => __('My Orders')])
                    </a>
                @endauth

                <!-- User Profile - অ্যাভাটারে ক্লিক করলে ড্যাশবোর্ডে যাবে (ক্লিক নিশ্চিত) -->
                @auth
                    @php
                        $avatarLetter = mb_strtoupper(mb_substr(trim(auth()->user()->fullname ?? auth()->user()->username ?? 'U'), 0, 1));
                    @endphp
                    <a href="{{ route('user.home') }}" class="glass-profile-btn glass-profile-btn--logged d-flex align-items-center justify-content-center w-10 h-10 min-w-10 min-h-10 max-w-10 max-h-10 p-0 mx-[2px] rounded-full overflow-hidden box-border cursor-pointer shrink-0 no-underline transition-transform duration-200 hover:scale-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2" style="width:40px;height:40px;min-width:40px;min-height:40px;max-width:40px;max-height:40px;padding:0;margin:0 2px;border-radius:50%;overflow:hidden;box-sizing:border-box;cursor:pointer;flex-shrink:0;text-decoration:none;" aria-label="@lang('Dashboard')" title="@lang('Dashboard')">
                        @if(auth()->user()->image)
                            <span class="block w-10 h-10 min-w-10 min-h-10 max-w-10 max-h-10 rounded-full overflow-hidden shrink-0 pointer-events-none" style="display:block;width:40px;height:40px;min-width:40px;min-height:40px;max-width:40px;max-height:40px;border-radius:50%;overflow:hidden;flex-shrink:0;pointer-events:none;"><img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, getFileSize('userProfile')) }}" alt="{{ auth()->user()->username }}" class="w-full h-full object-cover block pointer-events-none" style="width:100%;height:100%;object-fit:cover;display:block;pointer-events:none;"></span>
                        @else
                            <span class="glass-profile-btn__circle flex items-center justify-center w-10 h-10 min-w-10 min-h-10 max-w-10 max-h-10 rounded-full overflow-hidden shrink-0 box-border bg-[#0aa473] text-white text-base font-bold uppercase pointer-events-none" style="display:flex;align-items:center;justify-content:center;width:40px;height:40px;min-width:40px;min-height:40px;max-width:40px;max-height:40px;border-radius:50%;overflow:hidden;flex-shrink:0;box-sizing:border-box;background:#0aa473;color:#fff;font-size:16px;font-weight:700;text-transform:uppercase;pointer-events:none;">{{ $avatarLetter }}</span>
                        @endif
                    </a>
                @else
                    <a href="{{ route('user.login') }}" class="glass-icon-btn glass-login-btn transition-transform duration-200 hover:scale-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2" title="@lang('Login')" role="button">
                        @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'login_icon', 'fallback' => 'user', 'alt' => __('Login')])
                    </a>
                @endauth
            </div>
        </div>
        </div>
    </div>
</header>

<!-- Mobile Menu - Slides from left, above header, all devices -->
<div class="glass-mobile-menu" id="glassSidebar">
    <div class="glass-mobile-menu-overlay glass-sidebar-overlay"></div>
    <div class="glass-mobile-menu-content font-sans antialiased">
        <div class="glass-mobile-menu-header">
            <div class="glass-mobile-menu-header__brand">
                <a href="{{ route('home') }}" class="glass-mobile-menu-logo" aria-label="@lang('Home')">
                    @php $mobileMenuLogo = getLogo('logo'); @endphp
                    @if($mobileMenuLogo)
                        <img src="{{ $mobileMenuLogo }}" alt="{{ gs('site_name') }}" class="glass-mobile-menu-logo__img" width="112" height="28" loading="eager" decoding="async">
                    @else
                        <span class="glass-mobile-menu-logo__text">{{ gs('site_name') }}</span>
                    @endif
                </a>
                <span class="glass-mobile-menu-title">@lang('Menu')</span>
            </div>
            <button type="button" class="glass-mobile-menu-close" id="glassSidebarClose" aria-label="@lang('Close Menu')">
                <svg class="glass-mobile-menu-close__icon" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"></path></svg>
            </button>
        </div>
        <div class="glass-mobile-search font-sans">
            <form action="{{ route('products') }}" method="GET" class="glass-mobile-search-form">
                <div class="glass-mobile-search-inner">
                    <input type="text" name="search" class="glass-mobile-search-input" placeholder="@lang('Search here')" value="{{ request()->search ?? null }}" autocomplete="off">
                    <button type="submit" class="glass-mobile-search-submit" aria-label="@lang('Search')">
                        @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'search_icon', 'fallback' => 'search', 'width' => 20, 'height' => 20, 'alt' => ''])
                    </button>
                </div>
            </form>
        </div>
        <nav class="glass-mobile-nav">
            <a href="{{ route('home') }}" class="{{ menuActive('home') }}">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'home_icon', 'fallback' => 'home', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Home')</a>
            <a href="{{ route('products') }}" class="{{ menuActive('products') }}">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'products_icon', 'fallback' => 'box', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Products')</a>
            <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'contact_icon', 'fallback' => 'phone', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Contact')</a>
            <a href="{{ route('track.order') }}" class="{{ menuActive('track-order') }}">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'track_order_icon', 'fallback' => 'shipping-fast', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Track Order')</a>
            @guest
                <a href="{{ route('user.login') }}" role="button">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'login_icon', 'fallback' => 'sign-in-alt', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Login')</a>
                <a href="{{ route('user.register') }}" role="button">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'register_icon', 'fallback' => 'user-plus', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Register')</a>
            @endguest
        </nav>

        {{-- Logged-in user: show Dashboard, Orders, Cart, Profile, Logout etc. in hamburger menu (mobile/tablet) --}}
        @auth
        <div class="glass-mobile-user-section">
            <div class="glass-mobile-user-menu-title">@lang('My Account')</div>
            <nav class="glass-mobile-nav glass-mobile-user-nav">
                <a href="{{ route('user.home') }}" class="{{ menuActive('user.home') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'home_icon', 'fallback' => 'home', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Dashboard')</a>
                <a href="{{ route('user.track.order') }}" class="{{ menuActive('user.track.order') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'track_order_icon', 'fallback' => 'shipping-fast', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Track Order')</a>
                <a href="{{ route('user.notifications') }}" class="{{ menuActive('user.notifications') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'notification_icon', 'fallback' => 'bell', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Notifications')</a>
                <a href="{{ route('user.order.index') }}" class="{{ menuActive('user.order.index') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'orders_icon', 'fallback' => 'shopping-bag', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('My Orders')</a>
                <a href="{{ route('user.transactions') }}" class="{{ menuActive('user.transactions') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'transactions_icon', 'fallback' => 'money-bill-wave', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Transactions History')</a>
                <a href="{{ route('message.index') }}" class="{{ menuActive('message*') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'messages_icon', 'fallback' => 'comments', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('My Messages')</a>
                <a href="{{ route('user.cart') }}" class="{{ menuActive('user.cart') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'cart_icon', 'fallback' => 'shopping-cart', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Cart')</a>
                <a href="{{ route('user.wishlist') }}" class="{{ menuActive('user.wishlist') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'wishlist_icon', 'fallback' => 'heart', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Wishlist')</a>
                <a href="{{ route('user.compare') }}" class="{{ menuActive('user.compare') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'compare_icon', 'fallback' => 'exchange-alt', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Compare')</a>
                <a href="{{ route('user.review.index') }}" class="{{ menuActive('user.review*') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'review_icon', 'fallback' => 'star', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Review Products')</a>
                <a href="{{ route('user.profile.setting') }}" class="{{ menuActive('user.profile.setting') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'profile_icon', 'fallback' => 'user-tie', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Profile')</a>
                <a href="{{ route('user.change.password') }}" class="{{ menuActive('user.change.password') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'change_password_icon', 'fallback' => 'key', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Change Password')</a>
                <form method="POST" action="{{ route('user.logout') }}" class="glass-mobile-logout-form">
                    @csrf
                    <button type="submit" class="glass-mobile-logout-btn" aria-label="@lang('Logout')">@include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'logout_icon', 'fallback' => 'sign-out-alt', 'width' => 20, 'height' => 20, 'alt' => ''])@lang('Logout')</button>
                </form>
            </nav>
        </div>
        @endauth

        @if($__staylHeaderCategories->isNotEmpty())
        <div class="border-t border-slate-200/60 px-3 py-3">
            <div class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-500">@lang('Categories')</div>
            <div class="-mx-1 flex max-h-48 flex-wrap gap-2 overflow-y-auto">
                @foreach($__staylHeaderCategories->take(16) as $mc)
                    <a href="{{ route('category.products', [slug($mc->name), $mc->id]) }}" class="inline-flex max-w-full items-center rounded-xl border border-slate-200/80 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                        <span class="truncate">{{ __($mc->name) }}</span>
                    </a>
                @endforeach
            </div>
            <a href="{{ route('category.all') }}" class="mt-3 block rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 py-2.5 text-center text-xs font-bold text-white shadow-sm">@lang('View All Categories')</a>
        </div>
        @endif
    </div>
</div>
<!-- Old header completely hidden - replaced by glass header -->
@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush
