@php
    $homeData = getCachedHomeSectionData();
    if (!($homeData['settings']->floating_cart_enabled ?? 1)) return;
@endphp
<a href="{{ route('user.cart') }}" class="floating-cart-widget" aria-label="@lang('Cart')" title="@lang('Cart')">
    @include($activeTemplate . 'partials.icon', ['name' => 'shopping-cart'])
    <span class="floating-cart-widget__count show-cart-count">0</span>
</a>

@push('style')
<style>
.floating-cart-widget { position: fixed; border-radius: 50%; background: var(--base, #6366f1); color: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(0,0,0,.25); text-decoration: none; transition: transform .2s, box-shadow .2s; }
.floating-cart-widget:hover { color: #fff; transform: scale(1.05); box-shadow: 0 6px 20px rgba(0,0,0,.3); }
.floating-cart-widget__count { position: absolute; top: -4px; right: -4px; min-width: 20px; height: 20px; padding: 0 6px; background: #dc3545; color: #fff; font-size: 0.75rem; font-weight: 700; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
</style>
@endpush
