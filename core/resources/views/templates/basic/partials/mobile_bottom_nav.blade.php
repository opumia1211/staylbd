@php
    $mobileNavCartCount = 0;
    $mbNewTab = ['mb' => '1'];
@endphp

<nav class="mobile-bottom-nav pointer-events-auto fixed bottom-0 left-0 w-full z-[99999] bg-white border-t border-gray-100 shadow-[0_-2px_10px_rgba(0,0,0,0.1)]" aria-label="@lang('Mobile Navigation')" data-mobile-bottom-nav="1">
    <a href="{{ route('home', $mbNewTab) }}" target="_blank" rel="noopener noreferrer" class="mobile-bottom-nav__item {{ menuActive('home') }}">
        @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'home_icon', 'fallback' => 'home', 'width' => 19, 'height' => 19, 'alt' => ''])
        <span>@lang('Home')</span>
    </a>
    <a href="{{ route('category.all', $mbNewTab) }}" target="_blank" rel="noopener noreferrer" class="mobile-bottom-nav__item {{ menuActive('category*') }}">
        @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'categories_icon', 'fallback' => 'th-large', 'width' => 19, 'height' => 19, 'alt' => ''])
        <span>@lang('Categories')</span>
    </a>
    @auth
        <a href="{{ route('message.index', $mbNewTab) }}" target="_blank" rel="noopener noreferrer" class="mobile-bottom-nav__item {{ menuActive('message*') }}">
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'messages_icon', 'fallback' => 'comments', 'width' => 19, 'height' => 19, 'alt' => ''])
            <span>@lang('Messages')</span>
        </a>
    @else
        <a href="{{ route('user.login', $mbNewTab) }}" target="_blank" rel="noopener noreferrer" class="mobile-bottom-nav__item {{ menuActive('user.login') }}">
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'messages_icon', 'fallback' => 'comments', 'width' => 19, 'height' => 19, 'alt' => ''])
            <span>@lang('Messages')</span>
        </a>
    @endauth
    <a href="{{ route('user.cart', $mbNewTab) }}" target="_blank" rel="noopener noreferrer" class="mobile-bottom-nav__item {{ menuActive('user.cart') }}">
        <span class="mobile-bottom-nav__icon-wrap">
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'cart_icon', 'fallback' => 'shopping-cart', 'width' => 19, 'height' => 19, 'alt' => ''])
            <span class="mobile-bottom-nav__badge show-cart-count">{{ $mobileNavCartCount }}</span>
        </span>
        <span>@lang('Cart')</span>
    </a>
    @auth
        <a href="{{ route('user.home', $mbNewTab) }}" target="_blank" rel="noopener noreferrer" id="mobile-account-btn" class="mobile-bottom-nav__item {{ menuActive('user.home') }}" aria-label="@lang('Account')">
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'login_icon', 'fallback' => 'user', 'width' => 19, 'height' => 19, 'alt' => ''])
            <span>@lang('Account')</span>
        </a>
    @else
        <a href="{{ route('guest.account.menu', $mbNewTab) }}" target="_blank" rel="noopener noreferrer" class="mobile-bottom-nav__item guest-account-tab-link" aria-label="@lang('Account')">
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'login_icon', 'fallback' => 'user', 'width' => 19, 'height' => 19, 'alt' => ''])
            <span>@lang('Account')</span>
        </a>
    @endauth
</nav>

<style>
.mobile-bottom-nav { display: none !important; }
@media (max-width: 991.98px) {
    body { padding-bottom: calc(62px + env(safe-area-inset-bottom)); }
    .mobile-bottom-nav {
        position: fixed !important;
        left: 0 !important;
        right: 0 !important;
        width: 100vw !important;
        bottom: 0 !important;
        z-index: 99999 !important;
        display: grid !important;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        align-items: center;
        gap: 0;
        min-height: 56px;
        padding: 4px 8px calc(5px + env(safe-area-inset-bottom));
        background: #ffffff !important;
        opacity: 1 !important;
        border-top: 1px solid rgba(241,245,249,1) !important;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        will-change: transform;
        transform: translateZ(0);
    }
    .mobile-bottom-nav__item {
        position: relative;
        z-index: 1;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0;
        min-height: 36px;
        color: #475569;
        text-decoration: none;
        font-size: 9.5px;
        line-height: 1.1;
        font-weight: 700;
    }
    .mobile-bottom-nav__item .ui-icon {
        width: 20px;
        height: 20px;
        transition: transform 0.2s ease, color 0.2s ease;
    }
    .mobile-bottom-nav__btn {
        -webkit-appearance: none;
        appearance: none;
        border: 0;
        background: transparent;
        padding: 0;
        cursor: pointer;
        width: 100%;
        max-width: 100%;
        touch-action: manipulation;
    }
    #mobile-account-btn:active {
        transform: scale(0.96);
    }
    .mobile-bottom-nav,
    .mobile-bottom-nav__item { pointer-events: auto; }
    .mobile-bottom-nav__item.active,
    .mobile-bottom-nav__item:hover,
    .mobile-bottom-nav__item:focus-visible {
        color: #2563eb;
    }
    .mobile-bottom-nav__item:hover .ui-icon,
    .mobile-bottom-nav__item:focus-visible .ui-icon,
    .mobile-bottom-nav__item.active .ui-icon {
        transform: scale(1.08);
        color: #2563eb;
    }
    .mobile-bottom-nav__icon-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .mobile-bottom-nav__badge {
        position: absolute;
        top: -8px;
        right: -10px;
        min-width: 15px;
        height: 15px;
        padding: 0 4px;
        border-radius: 999px;
        background: #ef4444;
        color: #fff;
        font-size: 9px;
        line-height: 15px;
        text-align: center;
        font-weight: 700;
        display: inline-block;
    }
}
@media (max-width: 575.98px) {
    .mobile-bottom-nav {
        min-height: 54px;
        padding: 4px 6px calc(4px + env(safe-area-inset-bottom));
    }
    .mobile-bottom-nav__item .ui-icon { width: 19px; height: 19px; }
}
</style>
