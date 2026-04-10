@php
    $homeData = getCachedHomeSectionData();
    if (!($homeData['settings']->floating_cart_enabled ?? 1)) return;
@endphp
<a href="{{ route('user.cart') }}" class="floating-cart-widget" aria-label="@lang('Cart')" title="@lang('Cart')">
    @include($activeTemplate . 'partials.icon', ['name' => 'shopping-cart'])
    <span class="floating-cart-widget__count show-cart-count">0</span>
</a>

@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush
