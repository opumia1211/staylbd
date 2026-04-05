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
<style>
    .guest-account-ui { width: 100%; max-width: 42rem; margin-left: auto; margin-right: auto; box-sizing: border-box; }
    .guest-account-ui__title { font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0 0 1rem; letter-spacing: -0.02em; }
    .guest-account-ui__dual { display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem; margin-bottom: 1.25rem; }
    /* ছোট ফোন / ন্যারো ভিউ: এক কলাম; ট্যাবলেট পোর্ট্রেটেও আরাম */
    @media (max-width: 480px) {
        .guest-account-ui__dual { grid-template-columns: 1fr; }
    }
    .guest-account-ui__btn {
        min-height: 48px; padding: 0 14px; border-radius: 10px; font-size: 0.875rem; font-weight: 700;
        border: 1px solid transparent; cursor: pointer; touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }
    .guest-account-ui__btn--primary {
        background: linear-gradient(135deg, var(--base, #6366f1) 0%, #4f46e5 100%); color: #fff; border-color: transparent;
    }
    .guest-account-ui__btn--outline { background: #fff; color: #334155; border-color: #cbd5e1; }
    .guest-account-ui__kicker {
        font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b;
        margin: 1rem 0 0.4rem; padding: 0 2px;
    }
    .guest-account-ui__nav {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 0.25rem;
    }
    .guest-account-ui__row {
        display: flex; align-items: center; gap: 12px; min-height: 52px; padding: 12px 14px;
        text-decoration: none; color: #0f172a; border: 0; border-bottom: 1px solid #f1f5f9; width: 100%;
        text-align: left; background: #fff; cursor: pointer; box-sizing: border-box; font: inherit;
    }
    .guest-account-ui__row:last-child { border-bottom: 0; }
    .guest-account-ui__row:active { background: #f8fafc; }
    .guest-account-ui__row-icon .ui-icon { width: 20px; height: 20px; color: #475569; flex-shrink: 0; }
    .guest-account-ui__row-label { flex: 1 1 auto; font-size: 0.9375rem; font-weight: 600; min-width: 0; }
    .guest-account-ui__row-chevron { width: 18px !important; height: 18px !important; color: #94a3b8; flex-shrink: 0; margin-left: auto; }
    .guest-account-ui__pill {
        margin-left: auto; min-width: 22px; height: 22px; padding: 0 7px; border-radius: 999px;
        background: #0f766e; color: #fff; font-size: 11px; line-height: 22px; text-align: center; font-weight: 700;
    }
    .guest-account-ui__chips { display: flex; flex-wrap: wrap; gap: 8px; padding-bottom: 0.5rem; }
    .guest-account-ui__chip {
        display: inline-flex; align-items: center; padding: 8px 14px; border-radius: 999px;
        font-size: 0.8125rem; font-weight: 600; color: #0f172a; background: #fff; border: 1px solid #e2e8f0; text-decoration: none;
    }
    .guest-account-ui__chip:active { background: #f1f5f9; }
    /* স্ট্যান্ডঅলোন পেজ: কার্ড বর্ডার সরিয়ে ফুল-উইডথ সেকশন */
    .guest-account-standalone--fullbleed .guest-account-ui { max-width: none; margin: 0; padding: 0 clamp(12px, 4vw, 18px) 24px; }
    .guest-account-standalone--fullbleed .guest-account-ui__nav { border-radius: 0; border-left: 0; border-right: 0; }
    @media (min-width: 576px) {
        .guest-account-standalone--fullbleed .guest-account-ui__nav {
            border-radius: 12px;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            max-width: 40rem;
            margin-left: auto;
            margin-right: auto;
        }
    }
    @media (min-width: 768px) {
        .guest-account-standalone--fullbleed .guest-account-ui__dual { grid-template-columns: 1fr 1fr; max-width: 28rem; margin-left: auto; margin-right: auto; }
    }
    #guestAccountModal .guest-account-ui { max-width: none; }
    #guestAccountModal .guest-account-ui__nav { border-radius: 12px; }
</style>
@endpush
