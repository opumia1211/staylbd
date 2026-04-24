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
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'cart_icon', 'fallback' => 'shopping-cart', 'width' => 19, 'height' => 19, 'alt' => '', 'class' => 'stayl-header-icon-cart'])
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


{{-- inline style moved to critical-storefront.css --}}

