{{-- Restored baseline-safe header structure --}}
@php
    $headerIconsContent = getContent('header_icons.content', true);
    $iconValues = (array) ($headerIconsContent->data_values ?? []);
    $headerIcon = function (string $key, string $fallback) use ($iconValues): string {
        $value = trim((string) ($iconValues[$key] ?? ''));
        return $value !== '' ? $value : $fallback;
    };
    $headerIconImage = function (string $key) use ($iconValues): ?string {
        $imageKey = $key . '_image';
        $file = trim((string) ($iconValues[$imageKey] ?? ''));
        return $file !== '' ? $file : null;
    };
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
@endphp
{{-- Typography: Inter (app layout rsms.me/inter.css) + Tailwind theme font-sans; glass-header CSS is bundled in tailwind-storefront --}}
<header class="glass-header font-sans antialiased fixed top-0 left-0 right-0 z-[99999] w-full">
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
                    <a href="{{ route('home') }}" title="@lang('Home')">
                        @php $headerLogo = getLogo('logo'); @endphp
                        @if($headerLogo)
                            <img src="{{ $headerLogo }}" alt="{{ gs('site_name') }}" class="site-logo-img" width="{{ getLogoMaxWidth() }}" height="{{ getLogoMaxHeight() }}" fetchpriority="high" loading="eager" decoding="async" style="max-width: {{ getLogoMaxWidth() }}px; max-height: {{ getLogoMaxHeight() }}px; {{ getLogoStyle() }}">
                        @else
                            <span class="logo-text">{{ gs('site_name') }}</span>
                        @endif
                    </a>
                </div>
                @foreach($headerButtonsByPosition['left'] as $btn)
                    @php $b = (array)($btn->data_values ?? []); $href = trim((string)($b['button_url'] ?? '#')) ?: '#'; @endphp
                    <a href="{{ $href }}" class="glass-nav-btn glass-custom-btn" title="{{ $b['button_text'] ?? 'Button' }}">
                        @if(!empty($b['icon_image']))
                            <img src="{{ asset('assets/images/frontend/custom_buttons/' . $b['icon_image']) }}" alt="{{ $b['button_text'] ?? 'Button' }}" class="ui-icon" width="22" height="22" loading="lazy" decoding="async">
                        @else
                            @include($activeTemplate . 'partials.icon', ['name' => ($b['icon_name'] ?? 'circle')])
                        @endif
                    </a>
                @endforeach
            </div>

            <!-- Center: search pill (mic + search inside) + camera beside (same as other header round icons) -->
            <div class="glass-header-center glass-search-zone flex flex-[1_1_0%] min-w-0 max-w-[800px] mx-auto items-center self-center justify-center gap-2">
                <form action="{{ route('products') }}" method="GET" class="glass-search-form flex flex-1 min-w-0 flex-col justify-center" id="universalSearchForm" data-search-url="{{ url('/search/universal') }}" data-image-search-url="{{ url('/search/image') }}">
                    <div class="glass-search-wrapper glass-search-wrapper--card flex w-full min-w-0 flex-nowrap items-center">
                        <input type="text"
                               class="glass-search-input font-sans min-w-0"
                               id="universalSearchInput"
                               name="search"
                               placeholder="@lang('Search products, brands, and more')"
                               value="{{ request()->search ?? null }}"
                               autocomplete="off"
                               spellcheck="false"
                               data-search-url="{{ url('/search/universal') }}"
                               data-placeholder-listening="@lang('Listening… speak now')">
                        <button type="button" class="glass-search-icon glass-search-voice shrink-0" id="voiceSearchBtn" title="@lang('Voice Search')" aria-label="@lang('Voice Search')">
                            @include('templates.basic.partials.icon', ['name' => 'microphone', 'class' => 'icon-bold'])
                        </button>
                        <button type="submit" class="glass-search-icon glass-search-submit shrink-0" title="@lang('Search')" aria-label="@lang('Search')">
                            @if($headerIconImage('search_icon'))
                                <img src="{{ asset('assets/images/frontend/header_icons/' . $headerIconImage('search_icon')) }}" alt="" class="ui-icon" width="20" height="20" decoding="async" loading="eager">
                            @else
                                @include('templates.basic.partials.icon', ['name' => 'search', 'class' => 'icon-bold'])
                            @endif
                        </button>
                    </div>
                    <input type="file" id="imageSearchInput" accept="image/*" hidden tabindex="-1" aria-hidden="true">
                    <!-- Search Results Dropdown -->
                    <div class="glass-search-results" id="searchResults"></div>
                </form>
                <button type="button" class="glass-icon-btn glass-header-camera-btn shrink-0" id="cameraSearchBtn" title="@lang('Search by image')" aria-label="@lang('Search by image')">
                    @include($activeTemplate . 'partials.icon', ['name' => 'scan'])
                </button>
            </div>
            
            <!-- Navigation: icon-only links -->
            <nav class="glass-header-nav items-center gap-[10px] shrink-0 whitespace-nowrap">
                <a href="{{ route('products') }}" class="glass-nav-btn {{ menuActive('products') }}" title="@lang('Products')" aria-label="@lang('Products')">
                    @if($headerIconImage('products_icon'))
                        <img src="{{ asset('assets/images/frontend/header_icons/' . $headerIconImage('products_icon')) }}" alt="@lang('Products')" class="ui-icon" width="22" height="22" decoding="async">
                    @else
                        @include($activeTemplate . 'partials.icon', ['name' => $headerIcon('products_icon', 'box')])
                    @endif
                </a>
                <a href="{{ route('contact') }}" class="glass-nav-btn {{ request()->routeIs('contact') ? 'active' : '' }}" title="@lang('Contact')" aria-label="@lang('Contact')">
                    @if($headerIconImage('contact_icon'))
                        <img src="{{ asset('assets/images/frontend/header_icons/' . $headerIconImage('contact_icon')) }}" alt="@lang('Contact')" class="ui-icon" width="22" height="22" decoding="async">
                    @else
                        @include($activeTemplate . 'partials.icon', ['name' => $headerIcon('contact_icon', 'phone')])
                    @endif
                </a>
                <a href="{{ route('track.order') }}" class="glass-nav-btn {{ menuActive('track-order') }}" title="@lang('Track Order')" aria-label="@lang('Track Order')">
                    @if($headerIconImage('track_order_icon'))
                        <img src="{{ asset('assets/images/frontend/header_icons/' . $headerIconImage('track_order_icon')) }}" alt="@lang('Track Order')" class="ui-icon" width="22" height="22" decoding="async">
                    @else
                        @include($activeTemplate . 'partials.icon', ['name' => $headerIcon('track_order_icon', 'shipping-fast')])
                    @endif
                </a>
                @foreach($headerButtonsByPosition['nav'] as $btn)
                    @php $b = (array)($btn->data_values ?? []); $href = trim((string)($b['button_url'] ?? '#')) ?: '#'; @endphp
                    <a href="{{ $href }}" class="glass-nav-btn glass-custom-btn" title="{{ $b['button_text'] ?? 'Button' }}">
                        @if(!empty($b['icon_image']))
                            <img src="{{ asset('assets/images/frontend/custom_buttons/' . $b['icon_image']) }}" alt="{{ $b['button_text'] ?? 'Button' }}" class="ui-icon" width="22" height="22" loading="lazy" decoding="async">
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
                            <img src="{{ asset('assets/images/frontend/custom_buttons/' . $b['icon_image']) }}" alt="{{ $b['button_text'] ?? 'Button' }}" class="ui-icon" width="22" height="22" loading="lazy" decoding="async">
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
                            @if($headerIconImage('language_icon'))
                                <img src="{{ asset('assets/images/frontend/header_icons/' . $headerIconImage('language_icon')) }}" alt="@lang('Language')" class="ui-icon" width="22" height="22" decoding="async">
                            @else
                                @include($activeTemplate . 'partials.icon', ['name' => $headerIcon('language_icon', 'language')])
                            @endif
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
                    <a href="{{ route('user.notifications') }}" class="glass-icon-btn glass-notification-btn" title="@lang('Notifications')">
                        @if($headerIconImage('notification_icon'))
                            <img src="{{ asset('assets/images/frontend/header_icons/' . $headerIconImage('notification_icon')) }}" alt="@lang('Notifications')" class="ui-icon" width="22" height="22" decoding="async">
                        @else
                            @include($activeTemplate . 'partials.icon', ['name' => $headerIcon('notification_icon', 'bell')])
                        @endif
                        @if(($userNotificationCount ?? 0) > 0)
                            <span class="glass-badge show-notification-count">{{ $userNotificationCount }}</span>
                        @else
                            <span class="glass-badge show-notification-count d-none">0</span>
                        @endif
                    </a>
                @else
                    <!-- Empty space for alignment when not logged in -->
                    <div class="w-0 h-0 shrink-0 overflow-hidden min-w-0" style="width:0;height:0;flex-shrink:0;overflow:hidden;min-width:0;" aria-hidden="true"></div>
                @endauth

                <!-- Wishlist – same URL for guest and logged-in -->
                <a href="{{ route('user.wishlist') }}" id="header-wishlist" class="glass-icon-btn glass-wishlist-btn" title="@lang('Wishlist')" @auth data-dashboard-link="1" @endauth>
                    @if($headerIconImage('wishlist_icon'))
                        <img src="{{ asset('assets/images/frontend/header_icons/' . $headerIconImage('wishlist_icon')) }}" alt="@lang('Wishlist')" class="ui-icon" width="22" height="22" decoding="async">
                    @else
                        @include($activeTemplate . 'partials.icon', ['name' => $headerIcon('wishlist_icon', 'heart')])
                    @endif
                    <span class="glass-badge show-wishlist-count">0</span>
                </a>

                <!-- Compare – same URL for guest and logged-in -->
                <a href="{{ route('user.compare') }}" id="header-compare" class="glass-icon-btn glass-compare-btn" title="@lang('Compare')" @auth data-dashboard-link="1" @endauth>
                    @if($headerIconImage('compare_icon'))
                        <img src="{{ asset('assets/images/frontend/header_icons/' . $headerIconImage('compare_icon')) }}" alt="@lang('Compare')" class="ui-icon" width="22" height="22" decoding="async">
                    @else
                        @include($activeTemplate . 'partials.icon', ['name' => $headerIcon('compare_icon', 'exchange-alt')])
                    @endif
                    <span class="glass-badge show-compare-count">0</span>
                </a>

                <!-- Cart – same URL for guest and logged-in -->
                <a href="{{ route('user.cart') }}" id="header-cart" class="glass-icon-btn glass-cart-btn" title="@lang('Cart')" @auth data-dashboard-link="1" @endauth>
                    @if($headerIconImage('cart_icon'))
                        <img src="{{ asset('assets/images/frontend/header_icons/' . $headerIconImage('cart_icon')) }}" alt="@lang('Cart')" class="ui-icon" width="22" height="22" decoding="async">
                    @else
                        @include($activeTemplate . 'partials.icon', ['name' => $headerIcon('cart_icon', 'shopping-cart')])
                    @endif
                    <span class="glass-badge show-cart-count">0</span>
                </a>

                <!-- Orders/Bags -->
                @auth
                    <a href="{{ route('user.order.index') }}" class="glass-icon-btn glass-orders-btn" title="@lang('My Orders')">
                        @if($headerIconImage('orders_icon'))
                            <img src="{{ asset('assets/images/frontend/header_icons/' . $headerIconImage('orders_icon')) }}" alt="@lang('My Orders')" class="ui-icon" width="22" height="22" decoding="async">
                        @else
                            @include($activeTemplate . 'partials.icon', ['name' => $headerIcon('orders_icon', 'list-alt')])
                        @endif
                    </a>
                @else
                    <!-- Empty space for alignment when not logged in -->
                    <div class="w-0 h-0 shrink-0 overflow-hidden min-w-0" style="width:0;height:0;flex-shrink:0;overflow:hidden;min-width:0;" aria-hidden="true"></div>
                @endauth

                <!-- User Profile - অ্যাভাটারে ক্লিক করলে ড্যাশবোর্ডে যাবে (ক্লিক নিশ্চিত) -->
                @auth
                    @php
                        $avatarLetter = mb_strtoupper(mb_substr(trim(auth()->user()->fullname ?? auth()->user()->username ?? 'U'), 0, 1));
                    @endphp
                    <a href="{{ route('user.home') }}" class="glass-profile-btn glass-profile-btn--logged d-flex align-items-center justify-content-center w-10 h-10 min-w-10 min-h-10 max-w-10 max-h-10 p-0 mx-[2px] rounded-full overflow-hidden box-border cursor-pointer shrink-0 no-underline" style="width:40px;height:40px;min-width:40px;min-height:40px;max-width:40px;max-height:40px;padding:0;margin:0 2px;border-radius:50%;overflow:hidden;box-sizing:border-box;cursor:pointer;flex-shrink:0;text-decoration:none;" aria-label="@lang('Dashboard')" title="@lang('Dashboard')">
                        @if(auth()->user()->image)
                            <span class="block w-10 h-10 min-w-10 min-h-10 max-w-10 max-h-10 rounded-full overflow-hidden shrink-0 pointer-events-none" style="display:block;width:40px;height:40px;min-width:40px;min-height:40px;max-width:40px;max-height:40px;border-radius:50%;overflow:hidden;flex-shrink:0;pointer-events:none;"><img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, getFileSize('userProfile')) }}" alt="{{ auth()->user()->username }}" class="w-full h-full object-cover block pointer-events-none" style="width:100%;height:100%;object-fit:cover;display:block;pointer-events:none;"></span>
                        @else
                            <span class="glass-profile-btn__circle flex items-center justify-center w-10 h-10 min-w-10 min-h-10 max-w-10 max-h-10 rounded-full overflow-hidden shrink-0 box-border bg-[#0aa473] text-white text-base font-bold uppercase pointer-events-none" style="display:flex;align-items:center;justify-content:center;width:40px;height:40px;min-width:40px;min-height:40px;max-width:40px;max-height:40px;border-radius:50%;overflow:hidden;flex-shrink:0;box-sizing:border-box;background:#0aa473;color:#fff;font-size:16px;font-weight:700;text-transform:uppercase;pointer-events:none;">{{ $avatarLetter }}</span>
                        @endif
                    </a>
                @else
                    <a href="{{ route('user.login') }}" class="glass-icon-btn glass-login-btn" title="@lang('Login')" role="button">
                        @if($headerIconImage('login_icon'))
                            <img src="{{ asset('assets/images/frontend/header_icons/' . $headerIconImage('login_icon')) }}" alt="@lang('Login')" class="ui-icon" width="22" height="22" decoding="async">
                        @else
                            @include($activeTemplate . 'partials.icon', ['name' => $headerIcon('login_icon', 'user')])
                        @endif
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
                        <svg class="glass-mobile-search-submit__svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="M20 20l-4-4"></path></svg>
                    </button>
                </div>
            </form>
        </div>
        <nav class="glass-mobile-nav">
            <a href="{{ route('home') }}" class="{{ menuActive('home') }}">@include($activeTemplate . 'partials.icon', ['name' => 'home'])@lang('Home')</a>
            <a href="{{ route('products') }}" class="{{ menuActive('products') }}">@include($activeTemplate . 'partials.icon', ['name' => 'box'])@lang('Products')</a>
            <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">@include($activeTemplate . 'partials.icon', ['name' => 'phone'])@lang('Contact')</a>
            <a href="{{ route('track.order') }}" class="{{ menuActive('track-order') }}">@include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast'])@lang('Track Order')</a>
            @guest
                <a href="{{ route('user.login') }}" role="button">@include($activeTemplate . 'partials.icon', ['name' => 'sign-in-alt'])@lang('Login')</a>
                <a href="{{ route('user.register') }}" role="button">@include($activeTemplate . 'partials.icon', ['name' => 'user-plus'])@lang('Register')</a>
            @endguest
        </nav>

        {{-- Logged-in user: show Dashboard, Orders, Cart, Profile, Logout etc. in hamburger menu (mobile/tablet) --}}
        @auth
        <div class="glass-mobile-user-section">
            <div class="glass-mobile-user-menu-title">@lang('My Account')</div>
            <nav class="glass-mobile-nav glass-mobile-user-nav">
                <a href="{{ route('user.home') }}" class="{{ menuActive('user.home') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'home'])@lang('Dashboard')</a>
                <a href="{{ route('user.track.order') }}" class="{{ menuActive('user.track.order') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast'])@lang('Track Order')</a>
                <a href="{{ route('user.notifications') }}" class="{{ menuActive('user.notifications') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'bell'])@lang('Notifications')</a>
                <a href="{{ route('user.order.index') }}" class="{{ menuActive('user.order.index') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'shopping-bag'])@lang('My Orders')</a>
                <a href="{{ route('user.transactions') }}" class="{{ menuActive('user.transactions') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'money-bill-wave'])@lang('Transactions History')</a>
                <a href="{{ route('message.index') }}" class="{{ menuActive('message*') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'comments'])@lang('My Messages')</a>
                <a href="{{ route('user.cart') }}" class="{{ menuActive('user.cart') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'shopping-cart'])@lang('Cart')</a>
                <a href="{{ route('user.wishlist') }}" class="{{ menuActive('user.wishlist') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'heart'])@lang('Wishlist')</a>
                <a href="{{ route('user.compare') }}" class="{{ menuActive('user.compare') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'exchange-alt'])@lang('Compare')</a>
                <a href="{{ route('user.review.index') }}" class="{{ menuActive('user.review*') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'haykal'])@lang('Review Products')</a>
                <a href="{{ route('user.profile.setting') }}" class="{{ menuActive('user.profile.setting') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'user-tie'])@lang('Profile')</a>
                <a href="{{ route('user.change.password') }}" class="{{ menuActive('user.change.password') }}" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'key'])@lang('Change Password')</a>
                <form method="POST" action="{{ route('user.logout') }}" class="glass-mobile-logout-form">
                    @csrf
                    <button type="submit" class="glass-mobile-logout-btn" aria-label="@lang('Logout')">@include($activeTemplate . 'partials.icon', ['name' => 'sign-out-alt'])@lang('Logout')</button>
                </form>
            </nav>
        </div>
        @endauth

        {{-- Product categories removed per user request --}}
    </div>
</div>
<!-- Old header completely hidden - replaced by glass header -->
@push('style')
<style>
/* Safety: never show edge launcher on desktop/large screens */
.glass-sidebar-edge-toggle {
    display: none !important;
}

/*
 * Mobile drawer: always apply (not only <992px) so base glass-header.css flex rules
 * never win. Inter via font-sans + layout app; Tailwind bundle loads separately.
 */
#glassSidebar.glass-mobile-menu .glass-mobile-menu-content {
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, sans-serif;
    contain: layout style;
}
#glassSidebar.glass-mobile-menu .glass-mobile-menu-header {
    position: sticky !important;
    top: 0 !important;
    z-index: 4 !important;
    display: grid !important;
    grid-template-columns: 1fr auto 1fr !important;
    align-items: center !important;
    column-gap: 8px !important;
    padding: calc(10px + env(safe-area-inset-top, 0px)) 12px 12px !important;
    margin: 0 !important;
    min-height: 52px !important;
    background: rgba(255, 255, 255, 0.97) !important;
    border-bottom: 1px solid rgba(15, 23, 42, 0.06) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
}
#glassSidebar.glass-mobile-menu .glass-mobile-menu-header__brand {
    grid-column: 2 !important;
    justify-self: center !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 10px !important;
    flex-wrap: nowrap !important;
    min-width: 0 !important;
    max-width: min(220px, 72vw) !important;
}
#glassSidebar.glass-mobile-menu .glass-mobile-menu-logo {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 0 !important;
    max-width: 150px !important;
    text-decoration: none !important;
}
#glassSidebar.glass-mobile-menu .glass-mobile-menu-logo__img {
    display: block !important;
    max-height: 28px !important;
    width: auto !important;
    height: auto !important;
    max-width: 100% !important;
    object-fit: contain !important;
    object-position: center center !important;
}
#glassSidebar.glass-mobile-menu .glass-mobile-menu-logo__text {
    font-weight: 700 !important;
    font-size: 0.875rem !important;
    color: #0f172a !important;
    font-family: inherit !important;
}
#glassSidebar.glass-mobile-menu .glass-mobile-menu-title {
    font-weight: 700 !important;
    font-size: 0.9375rem !important;
    color: #64748b !important;
    white-space: nowrap !important;
    font-family: inherit !important;
}
#glassSidebar.glass-mobile-menu .glass-mobile-menu-close {
    grid-column: 3 !important;
    justify-self: end !important;
    align-self: start !important;
    position: relative !important;
    inset: auto !important;
    width: 40px !important;
    height: 40px !important;
    min-width: 40px !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    border-radius: 10px !important;
    background: rgba(15, 23, 42, 0.07) !important;
    color: #0f172a !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important;
}
#glassSidebar.glass-mobile-menu .glass-mobile-menu-close:hover {
    background: rgba(239, 68, 68, 0.12) !important;
    color: #b91c1c !important;
}
#glassSidebar.glass-mobile-menu .glass-mobile-menu-close__icon {
    display: block !important;
    flex-shrink: 0;
}
/* Search: single pill row — override legacy .glass-mobile-search form / input rules */
#glassSidebar.glass-mobile-menu .glass-mobile-search {
    padding: 10px 14px 12px !important;
}
#glassSidebar.glass-mobile-menu form.glass-mobile-search-form {
    display: block !important;
    width: 100% !important;
    margin: 0 !important;
}
#glassSidebar.glass-mobile-menu .glass-mobile-search-inner {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    align-items: center !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    min-height: 44px !important;
    padding: 3px 4px 3px 12px !important;
    gap: 6px !important;
    border-radius: 9999px !important;
    border: 1px solid rgba(15, 23, 42, 0.1) !important;
    background: #fff !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important;
}
#glassSidebar.glass-mobile-menu .glass-mobile-search-inner:focus-within {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12) !important;
}
#glassSidebar.glass-mobile-menu input.glass-mobile-search-input[type="text"] {
    flex: 1 1 0% !important;
    min-width: 0 !important;
    width: auto !important;
    max-width: none !important;
    border: 0 !important;
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
    border-radius: 0 !important;
    padding: 8px 4px 8px 0 !important;
    margin: 0 !important;
    font-size: 0.9375rem !important;
    line-height: 1.35 !important;
    font-family: inherit !important;
    -webkit-appearance: none;
    appearance: none;
}
#glassSidebar.glass-mobile-menu input.glass-mobile-search-input::placeholder {
    color: #94a3b8 !important;
}
#glassSidebar.glass-mobile-menu button.glass-mobile-search-submit[type="submit"] {
    flex: 0 0 36px !important;
    width: 36px !important;
    height: 36px !important;
    min-width: 36px !important;
    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 9999px !important;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
    color: #fff !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
    box-shadow: 0 1px 4px rgba(37, 99, 235, 0.28) !important;
    transition: transform 0.1s ease, filter 0.1s ease !important;
}
#glassSidebar.glass-mobile-menu button.glass-mobile-search-submit:active {
    transform: scale(0.96);
}
#glassSidebar.glass-mobile-menu .glass-mobile-search-submit__svg {
    display: block !important;
    pointer-events: none;
}

@media (max-width: 991.98px) {
    .glass-header > .w-full {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    .glass-header {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 99999 !important;
    }
    .glass-header .glass-header-wrapper {
        gap: 10px !important;
    }
    .glass-header .glass-header-right,
    .glass-header .glass-header-nav {
        display: none !important;
    }
    .glass-header .glass-logo,
    .glass-header .glass-header-left .glass-nav-btn {
        display: none !important;
    }
    .glass-header .glass-header-left {
        gap: 0 !important;
        flex: 0 0 42px !important;
        min-width: 42px !important;
    }
    .glass-header .glass-header-center {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        max-width: none !important;
        margin-inline: 0 !important;
        overflow: visible !important;
    }
    .glass-header .glass-search-wrapper {
        min-height: 40px !important;
        max-height: 46px !important;
        border-radius: 999px !important;
        padding-left: 12px !important;
        padding-right: 4px !important;
        width: 100% !important;
    }
    .glass-header .glass-search-input {
        padding-right: 2.5rem !important;
    }
    .glass-header .glass-search-icon,
    .glass-header .glass-search-submit,
    .glass-header .glass-header-camera-btn {
        z-index: 3 !important;
        pointer-events: auto !important;
    }
    /* Faster drawer (Tailwind bundle may use longer transition) */
    #glassSidebar.glass-mobile-menu .glass-mobile-menu-content {
        transition: transform 0.2s cubic-bezier(0.32, 0.72, 0, 1) !important;
    }
    .glass-header .glass-menu-toggle {
        width: 42px !important;
        height: 42px !important;
        min-width: 42px !important;
        min-height: 42px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .glass-sidebar-edge-toggle {
        position: fixed;
        top: 50%;
        left: 2px;
        transform: translateY(-50%);
        width: 28px;
        height: 44px;
        border: 0;
        border-radius: 0 10px 10px 0;
        background: rgba(15, 23, 42, 0.86);
        color: #fff;
        z-index: 100001;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 20px rgba(2, 6, 23, 0.22);
        opacity: .96;
        transition: opacity .18s ease, transform .22s ease, box-shadow .22s ease;
        font-size: 0 !important;
        line-height: 0 !important;
        overflow: hidden !important;
    }
    .glass-sidebar-edge-toggle:hover {
        opacity: 1;
        transform: translateY(-50%) translateX(1px);
        box-shadow: 0 8px 24px rgba(2, 6, 23, 0.26);
    }
    body.glass-sidebar-open .glass-sidebar-edge-toggle {
        opacity: 0;
        transform: translateY(-50%) translateX(-8px);
        pointer-events: none;
    }
    .glass-mobile-menu.active + .glass-sidebar-edge-toggle {
        opacity: 0 !important;
        transform: translateY(-50%) translateX(-8px) !important;
        pointer-events: none !important;
    }
    .glass-sidebar-edge-toggle .ui-icon {
        transform: rotate(0deg);
    }
    body.glass-sidebar-open .glass-sidebar-edge-toggle .ui-icon {
        transform: rotate(180deg);
    }
    .glass-sidebar-edge-toggle .ui-icon {
        width: 14px;
        height: 14px;
        transition: transform .15s ease;
        display: block !important;
        flex: 0 0 14px !important;
    }
}

/* Submit = same neutral icon treatment as mic (no fill color) */
.glass-search-submit.glass-search-icon {
    width: 36px !important;
    height: 36px !important;
    min-width: 36px !important;
    min-height: 36px !important;
}
.glass-search-submit .ui-icon,
.glass-search-submit__icon {
    width: 20px;
    height: 20px;
    color: #5f6368;
    stroke: #5f6368;
}
.glass-search-submit img.ui-icon {
    width: 20px;
    height: 20px;
    object-fit: contain;
    display: block;
}
@media (max-width: 575.98px) {
    .glass-search-submit.glass-search-icon {
        width: 30px !important;
        height: 30px !important;
        min-width: 30px !important;
        min-height: 30px !important;
    }
    .glass-search-submit .ui-icon,
    .glass-search-submit__icon {
        width: 17px;
        height: 17px;
    }
}
</style>
@endpush
