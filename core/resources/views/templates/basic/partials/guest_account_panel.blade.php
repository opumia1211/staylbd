@php
    $guestLangs = $guestLangs ?? \App\Models\Language::all();
    $guestAccountHideHeading = !empty($guestAccountHideHeading);
@endphp
<div class="guest-account-ui">
    @unless($guestAccountHideHeading)
        <h2 @if(!empty($guestAccountHeadingId)) id="{{ $guestAccountHeadingId }}" @endif class="guest-account-ui__title">@lang('My Account')</h2>
    @endunless

    <div class="guest-account-ui__dual" role="group" aria-label="@lang('Sign in')">
        <button type="button" class="guest-account-ui__btn guest-account-ui__btn--primary" data-guest-auth="login">@lang('Login')</button>
        <button type="button" class="guest-account-ui__btn guest-account-ui__btn--outline" data-guest-auth="register">@lang('Registration')</button>
    </div>

    <p class="guest-account-ui__kicker">@lang('Quick links')</p>
    <nav class="guest-account-ui__nav" aria-label="@lang('Quick links')">
        <a href="{{ route('home') }}" class="guest-account-ui__row" data-no-ajax>
            <span class="guest-account-ui__row-icon">@include($activeTemplate . 'partials.icon', ['name' => 'home'])</span>
            <span class="guest-account-ui__row-label">@lang('Home')</span>
            @include($activeTemplate . 'partials.icon', ['name' => 'angle-right', 'class' => 'guest-account-ui__row-chevron'])
        </a>
        <a href="{{ route('category.all') }}" class="guest-account-ui__row" data-no-ajax>
            <span class="guest-account-ui__row-icon">@include($activeTemplate . 'partials.icon', ['name' => 'th-large'])</span>
            <span class="guest-account-ui__row-label">@lang('All categories')</span>
            @include($activeTemplate . 'partials.icon', ['name' => 'angle-right', 'class' => 'guest-account-ui__row-chevron'])
        </a>
        <a href="{{ route('products') }}" class="guest-account-ui__row" data-no-ajax>
            <span class="guest-account-ui__row-icon">@include($activeTemplate . 'partials.icon', ['name' => 'box'])</span>
            <span class="guest-account-ui__row-label">@lang('Products')</span>
            @include($activeTemplate . 'partials.icon', ['name' => 'angle-right', 'class' => 'guest-account-ui__row-chevron'])
        </a>
        @if(Route::has('brand.all'))
            <a href="{{ route('brand.all') }}" class="guest-account-ui__row" data-no-ajax>
                <span class="guest-account-ui__row-icon">@include($activeTemplate . 'partials.icon', ['name' => 'tag'])</span>
                <span class="guest-account-ui__row-label">@lang('Brands')</span>
                @include($activeTemplate . 'partials.icon', ['name' => 'angle-right', 'class' => 'guest-account-ui__row-chevron'])
            </a>
        @endif
        <a href="{{ route('contact.live') }}" class="guest-account-ui__row" data-no-ajax>
            <span class="guest-account-ui__row-icon">@include($activeTemplate . 'partials.icon', ['name' => 'phone'])</span>
            <span class="guest-account-ui__row-label">@lang('Contact')</span>
            @include($activeTemplate . 'partials.icon', ['name' => 'angle-right', 'class' => 'guest-account-ui__row-chevron'])
        </a>
        <a href="{{ route('track.order') }}" class="guest-account-ui__row" data-no-ajax>
            <span class="guest-account-ui__row-icon">@include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast'])</span>
            <span class="guest-account-ui__row-label">@lang('Track Order')</span>
            @include($activeTemplate . 'partials.icon', ['name' => 'angle-right', 'class' => 'guest-account-ui__row-chevron'])
        </a>
        <button type="button" class="guest-account-ui__row guest-account-ui__row--button" data-guest-auth="login">
            <span class="guest-account-ui__row-icon">@include($activeTemplate . 'partials.icon', ['name' => 'sign-in-alt'])</span>
            <span class="guest-account-ui__row-label">@lang('Login')</span>
            @include($activeTemplate . 'partials.icon', ['name' => 'angle-right', 'class' => 'guest-account-ui__row-chevron'])
        </button>
        <button type="button" class="guest-account-ui__row guest-account-ui__row--button" data-guest-auth="register">
            <span class="guest-account-ui__row-icon">@include($activeTemplate . 'partials.icon', ['name' => 'plus'])</span>
            <span class="guest-account-ui__row-label">@lang('Register')</span>
            @include($activeTemplate . 'partials.icon', ['name' => 'angle-right', 'class' => 'guest-account-ui__row-chevron'])
        </button>
    </nav>

    <p class="guest-account-ui__kicker">@lang('Shopping')</p>
    <nav class="guest-account-ui__nav" aria-label="@lang('Shopping')">
        <a href="{{ route('user.wishlist') }}" class="guest-account-ui__row" data-no-ajax>
            <span class="guest-account-ui__row-icon">@include($activeTemplate . 'partials.icon', ['name' => 'heart'])</span>
            <span class="guest-account-ui__row-label">@lang('Wishlist')</span>
            <span class="guest-account-ui__pill show-wishlist-count">0</span>
        </a>
        <a href="{{ route('user.compare') }}" class="guest-account-ui__row" data-no-ajax>
            <span class="guest-account-ui__row-icon">@include($activeTemplate . 'partials.icon', ['name' => 'exchange-alt'])</span>
            <span class="guest-account-ui__row-label">@lang('Compare')</span>
            <span class="guest-account-ui__pill show-compare-count">0</span>
        </a>
        <a href="{{ route('user.cart') }}" class="guest-account-ui__row" data-no-ajax>
            <span class="guest-account-ui__row-icon">@include($activeTemplate . 'partials.icon', ['name' => 'shopping-cart'])</span>
            <span class="guest-account-ui__row-label">@lang('Cart')</span>
            <span class="guest-account-ui__pill show-cart-count">0</span>
        </a>
        <a href="{{ route('track.order') }}" class="guest-account-ui__row" data-no-ajax>
            <span class="guest-account-ui__row-icon">@include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast'])</span>
            <span class="guest-account-ui__row-label">@lang('Order Tracking')</span>
            @include($activeTemplate . 'partials.icon', ['name' => 'angle-right', 'class' => 'guest-account-ui__row-chevron'])
        </a>
    </nav>

    @if($general->multi_language && $guestLangs->isNotEmpty())
        <p class="guest-account-ui__kicker">@lang('Language')</p>
        <div class="guest-account-ui__chips">
            @foreach($guestLangs as $lng)
                <a href="{{ route('lang', $lng->code) }}" class="guest-account-ui__chip" data-no-ajax>{{ __($lng->name) }}</a>
            @endforeach
        </div>
    @endif
</div>

@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush
