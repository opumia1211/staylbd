@php
    $carts = $carts ?? [];
    if (!is_countable($carts)) {
        $carts = is_array($carts) ? $carts : (is_object($carts) ? array_values((array) $carts) : []);
    }
    $isUserDashboard = ($cartPageContext ?? '') === 'user_dashboard';
    $cartCount = count($carts);
@endphp
@if($isUserDashboard)

{{-- inline style moved to critical-storefront.css --}}

@else

{{-- inline style moved to critical-storefront.css --}}

@endif
<div class="cart-page cart-container {{ $isUserDashboard ? 'cart-page--user-dashboard pt-0 pb-3 dashboard-list-page' : '' }}">
    <div class="container">
        @if($cartCount > 0 && !$isUserDashboard)
        <header class="cart-page__heading">
            <h1>@lang('Shopping Cart')</h1>
            <p>@lang('Review your items and proceed to checkout')</p>
        </header>
        @endif
        @if(!$isUserDashboard)
        @guest
        <div class="alert alert-light border mb-4 d-flex flex-column flex-sm-row align-items-center gap-3 cart-page__login-prompt">
            @include($activeTemplate . 'partials.icon', ['name' => 'user-circle', 'class' => 'fs-2 text--base'])
            <div class="flex-grow-1 text-center text-sm-start">
                <strong>@lang('Login for faster checkout')</strong><br>
                <span class="small text-muted">@lang('Sign in to save your cart and use saved addresses.')</span>
            </div>
            <a href="{{ route('user.login') }}?redirect={{ urlencode(request()->url()) }}" class="btn btn--base btn-sm flex-shrink-0">@lang('Login')</a>
        </div>
        @endguest
        @endif

        @if($cartCount > 0)
        <div class="row g-3 cart-page__row">
            <div class="{{ $isUserDashboard ? 'col-12' : 'col-lg-9' }}">
                <div class="card cart-page__card">
                    @if(!$isUserDashboard)
                    <div class="cart-page__toolbar d-none d-md-flex">
                        <div class="cart-page__toolbar-info">
                            <strong><span class="cart-toolbar__count-num">{{ $cartCount }}</span> {{ $cartCount == 1 ? __('item') : __('items') }}</strong>
                            <span class="text-muted ms-2" style="font-size: 0.8rem;">— @lang('Tick items to include, then checkout below')</span>
                        </div>
                        <div class="cart-page__toolbar-actions">
                            <button type="button" class="btn btn-outline-danger btn-sm cart-remove-selected d-none">@include($activeTemplate . 'partials.icon', ['name' => 'trash-alt', 'class' => 'me-1'])@lang('Remove Selected')</button>
                            <a href="#cart-page__summary-card" class="btn btn--base btn-sm">@include($activeTemplate . 'partials.icon', ['name' => 'arrow-right', 'class' => 'me-1'])@lang('Proceed to Checkout')</a>
                        </div>
                    </div>
                    @endif
                    <div class="table-responsive cart-page__table-wrap d-none {{ $isUserDashboard ? 'd-lg-block' : 'd-md-block' }}">
                        <table class="table table-hover align-middle mb-0 cart-page__table table-auto">
                            <thead class="cart-page__thead">
                                @if($isUserDashboard)
                                <tr>
                                    <th class="cart-page__th cart-row-user__check"><label class="mb-0"><input type="checkbox" class="form-check-input cart-select-all" id="cartSelectAll" aria-label="@lang('Select all')"></label></th>
                                    <th class="cart-page__th cart-row-user__img">@lang('Image')</th>
                                    <th class="cart-page__th cart-row-user__name">@lang('Product')</th>
                                    <th class="cart-page__th cart-row-user__sku">@lang('SKU')</th>
                                    <th class="cart-page__th cart-row-user__category">@lang('Category')</th>
                                    <th class="cart-page__th cart-row-user__brand">@lang('Brand')</th>
                                    <th class="cart-page__th cart-row-user__stock">@lang('Stock')</th>
                                    <th class="cart-page__th cart-row-user__discount">@lang('Discount')</th>
                                    <th class="cart-page__th cart-row-user__price">@lang('Price')</th>
                                    <th class="cart-page__th cart-row-user__rating">@lang('Rating')</th>
                                    <th class="cart-page__th cart-row-user__qty">@lang('Qty')</th>
                                    <th class="cart-page__th cart-row-user__subtotal">@lang('Subtotal')</th>
                                    <th class="cart-page__th cart-row-user__action">@lang('Action')</th>
                                </tr>
                                @else
                                <tr>
                                    <th class="cart-page__th cart-page__th--check">
                                        <label class="cart-page__check-label mb-0">
                                            <input type="checkbox" class="form-check-input cart-select-all" id="cartSelectAll" aria-label="@lang('Select all')">
                                        </label>
                                    </th>
                                    <th class="cart-page__th cart-page__th--img"></th>
                                    <th class="cart-page__th cart-page__th--product">@lang('Product & Size')</th>
                                    <th class="cart-page__th text-end">@lang('Price')</th>
                                    <th class="cart-page__th text-end">@lang('Quantity')</th>
                                    <th class="cart-page__th text-end">@lang('Subtotal')</th>
                                    <th class="cart-page__th text-end">@lang('Remove')</th>
                                </tr>
                                @endif
                            </thead>
                            <tbody class="cart-page__tbody">
                                @foreach($carts as $cart)
                                    @if($isUserDashboard)
                                        @include($activeTemplate . 'partials.cart_row_user', ['cart' => $cart])
                                    @else
                                        @include($activeTemplate . 'partials.cart_row', ['cart' => $cart, 'simpleCart' => false])
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(!$isUserDashboard)
                    <div class="cart-page__footer d-none d-md-flex">
                        <a href="{{ route('products') }}" class="btn btn-outline-primary btn-sm">@include($activeTemplate . 'partials.icon', ['name' => 'arrow-left', 'class' => 'me-1'])@lang('Continue Shopping')</a>
                        <a href="#cart-page__summary-card" class="btn btn--base btn-sm">@include($activeTemplate . 'partials.icon', ['name' => 'arrow-right', 'class' => 'me-1'])@lang('Proceed to Checkout')</a>
                    </div>
                    @endif
                    <div class="cart-page__mobile {{ $isUserDashboard ? 'cart-mobile-cards d-lg-none' : 'd-md-none' }}">
                        @if($cartCount > 0)
                        <div class="cart-page__mobile-select-all mb-2 py-2 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <label class="d-flex align-items-center gap-2 mb-0 cursor-pointer">
                                <input type="checkbox" class="form-check-input cart-select-all-mobile" id="cartSelectAllMobile" aria-label="@lang('Select all')">
                                <span class="small fw-medium">@lang('Select all for order')</span>
                            </label>
                            <button type="button" class="btn btn-outline-danger btn-sm cart-remove-selected d-none">
                                @include($activeTemplate . 'partials.icon', ['name' => 'trash-alt', 'class' => 'me-1'])@lang('Remove Selected')
                            </button>
                        </div>
                        @endif
                        @foreach($carts as $cart)
                            @include($activeTemplate . 'partials.cart_row_mobile', ['cart' => $cart])
                        @endforeach
                        <a href="{{ route('products') }}" class="btn btn-outline-primary btn-sm mt-2">@include($activeTemplate . 'partials.icon', ['name' => 'arrow-left', 'class' => 'me-1'])@lang('Continue Shopping')</a>
                    </div>
                </div>
            </div>
            @if(!$isUserDashboard)
            <aside class="col-lg-3" id="cart-page__summary-card">
                <div class="cart-sidebar">
                    <h3 class="cart-sidebar__title">
                        <span>@lang('Order Summary')</span>
                    </h3>
                    <div class="cart-sidebar__body">
                        <div class="cart-sidebar__row">
                            <span class="cart-sidebar__label">@lang('Subtotal')</span>
                            <span class="cart-sidebar__value subtotal-price">{{ $general->cur_sym }}0.00</span>
                        </div>
                        <div class="cart-sidebar__row coupon-show d-none">
                            <span class="cart-sidebar__label">@lang('Discount')</span>
                            <span class="cart-sidebar__value cart-sidebar__value--discount discount-price">-{{ $general->cur_sym }}0.00</span>
                        </div>
                        <div class="cart-sidebar__divider"></div>
                        <div class="cart-sidebar__row cart-sidebar__row--total total-show">
                            <span class="cart-sidebar__label">@lang('Total')</span>
                            <span class="cart-sidebar__value total-price">{{ $general->cur_sym }}0.00</span>
                        </div>
                        <div class="cart-sidebar__coupon mt-2">
                            <form class="coupon-form d-flex gap-2" role="search">
                                <label for="cart-coupon-input" class="visually-hidden">@lang('Coupon code')</label>
                                <input type="text" id="cart-coupon-input" class="form-control cart-sidebar__coupon-input coupon" name="coupon" placeholder="@lang('Coupon code')" autocomplete="off" aria-label="@lang('Coupon code')">
                                <button type="button" class="btn btn--base cart-sidebar__coupon-btn coupon-apply flex-shrink-0" aria-label="@lang('Apply coupon')">@lang('Apply')</button>
                            </form>
                        </div>
                        @auth
                        <form id="checkoutSelectionForm" action="{{ route('cart.list.set.checkout.selection') }}" method="POST" class="cart-sidebar__form">
                            @csrf
                            <div id="checkout-cart-ids-container"></div>
                            <button type="submit" class="btn btn--base w-100 cart-sidebar__cta" id="proceedToCheckoutBtn">@include($activeTemplate . 'partials.icon', ['name' => 'lock', 'class' => 'me-1'])@lang('Proceed to Checkout')</button>
                        </form>
                        <p class="cart-sidebar__note">@lang('Only selected items above will be included in the order.')</p>
                        @else
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('user.cart.quickorder') }}"
                               class="btn btn--base w-100 cart-sidebar__cta"
                               id="openGuestCheckoutBtn"
                               data-bs-toggle="modal"
                               data-bs-target="#guestCheckoutModal">
                                @include($activeTemplate . 'partials.icon', ['name' => 'bolt', 'class' => 'me-1'])@lang('Quick Order')
                            </a>
                            <a href="{{ route('user.login') }}?redirect={{ urlencode(route('user.checkout.index')) }}" class="btn btn-outline-secondary btn-sm w-100 text-decoration-none">@include($activeTemplate . 'partials.icon', ['name' => 'sign-in-alt', 'class' => 'me-1'])@lang('Login to Checkout')</a>
                        </div>
                        @endauth
                    </div>
                </div>
            </aside>
            @else
            {{-- User dashboard: Order Summary outside sidebar, below table --}}
            <div class="col-12 mt-3" id="cart-page__summary-card">
                <div class="card cart-sidebar cart-sidebar--below border rounded">
                    <div class="card-body py-3 px-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <h5 class="mb-0 cart-sidebar__title-inline fw-bold">@lang('Order Summary')</h5>
                            <div class="d-flex flex-wrap align-items-center gap-4">
                                <div class="cart-sidebar__row cart-sidebar__row--inline">
                                    <span class="cart-sidebar__label me-2">@lang('Subtotal')</span>
                                    <span class="cart-sidebar__value subtotal-price fw-semibold">{{ $general->cur_sym }}0.00</span>
                                </div>
                                <div class="cart-sidebar__divider-vertical d-none d-sm-block" style="width:1px;height:24px;background:#dee2e6;"></div>
                                <div class="cart-sidebar__row cart-sidebar__row--total cart-sidebar__row--inline">
                                    <span class="cart-sidebar__label me-2">@lang('Total')</span>
                                    <span class="cart-sidebar__value total-price fw-bold">{{ $general->cur_sym }}0.00</span>
                                </div>
                                @auth
                                <form id="checkoutSelectionForm" action="{{ route('cart.list.set.checkout.selection') }}" method="POST" class="d-inline">
                                    @csrf
                                    <div id="checkout-cart-ids-container"></div>
                                    <button type="submit" class="btn btn-success btn-lg px-4" id="proceedToCheckoutBtn">@include($activeTemplate . 'partials.icon', ['name' => 'lock', 'class' => 'me-1'])@lang('Proceed to Checkout')</button>
                                </form>
                                @else
                                <a href="{{ route('user.cart.quickorder') }}"
                                   class="btn btn-success btn-lg px-4"
                                   id="openGuestCheckoutBtnInline"
                                   data-bs-toggle="modal"
                                   data-bs-target="#guestCheckoutModal">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'bolt', 'class' => 'me-1'])@lang('Quick Order')
                                </a>
                                <a href="{{ route('user.login') }}?redirect={{ urlencode(route('user.checkout.index')) }}" class="btn btn-outline-secondary btn-lg px-4 text-decoration-none ms-2">@lang('Login')</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @else
        {{-- User dashboard: খালি কার্ট ব্লক সাইডবারের মাঝের কন্টেন্ট এরিয়ায় সেন্টার করে দেখানো --}}
        <div class="cart-page__empty-outer {{ $isUserDashboard ? 'cart-page__empty-outer--dashboard' : '' }}">
            <div class="card cart-page__empty list-page-empty">
                <div class="card-body p-0 text-center py-5 px-4">
                    @include($activeTemplate . 'partials.icon', ['name' => 'shopping-cart', 'class' => 'cart-page__empty-icon list-page-empty__icon d-block'])
                    <h5 class="mb-2 list-page-empty__title text-dark">@lang('Your cart is empty')</h5>
                    <p class="list-page-empty__text text-muted mb-4 small">{{ __($emptyMessage ?? 'Your cart is empty. Start adding products now!') }}</p>
                    <a href="{{ route('products') }}" class="btn btn--base">@lang('Start Shopping')</a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="removeCartModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Remove item?')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">@lang('Are you sure to remove this product?')</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                <button type="button" class="btn btn--base remove-product">@lang('Remove')</button>
            </div>
        </div>
    </div>
</div>
