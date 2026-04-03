@guest
@php
    $guestLangs = \App\Models\Language::all();
@endphp
<div class="modal fade" id="guestAccountModal" tabindex="-1" aria-labelledby="guestAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered guest-account-modal-dialog" role="document">
        <div class="modal-content guest-account-modal-content border-0">
            <button type="button" class="guest-account-close-btn" data-bs-dismiss="modal" data-stayl-close="guest-account" aria-label="@lang('Close')">
                @include($activeTemplate . 'partials.icon', ['name' => 'times'])
            </button>
            <div class="modal-body p-3 p-sm-4">
                <div class="mx-auto w-full max-w-storefront">
                    <h6 id="guestAccountModalLabel" class="mb-3 fw-semibold text-slate-800">@lang('My Account')</h6>

                    <div class="guest-account-auth-row d-grid gap-2 mb-3">
                        <button type="button" class="btn btn--base btn-sm w-100" data-guest-auth="login">@lang('Login')</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" data-guest-auth="register">@lang('Registration')</button>
                    </div>

                    <div class="guest-account-links mb-3">
                        <div class="small text-muted mb-2">@lang('Quick Links')</div>
                        <div class="guest-account-grid guest-account-grid--links">
                            <a href="{{ route('products') }}" class="guest-account-card" data-no-ajax>
                                <span class="guest-account-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'box'])</span>
                                <span class="guest-account-card__text">@lang('Products')</span>
                            </a>
                            <a href="{{ route('contact') }}" class="guest-account-card" data-no-ajax>
                                <span class="guest-account-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'phone'])</span>
                                <span class="guest-account-card__text">@lang('Contact')</span>
                            </a>
                            <a href="{{ route('track.order') }}" class="guest-account-card" data-no-ajax>
                                <span class="guest-account-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast'])</span>
                                <span class="guest-account-card__text">@lang('Track Order')</span>
                            </a>
                            <button type="button" class="guest-account-card guest-account-card--btn" data-guest-auth="login">
                                <span class="guest-account-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'sign-in-alt'])</span>
                                <span class="guest-account-card__text">@lang('Login')</span>
                            </button>
                            <button type="button" class="guest-account-card guest-account-card--btn" data-guest-auth="register">
                                <span class="guest-account-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'user-plus'])</span>
                                <span class="guest-account-card__text">@lang('Register')</span>
                            </button>
                        </div>
                    </div>

                    <div class="guest-account-grid">
                        <a href="{{ route('user.wishlist') }}" class="guest-account-card" data-no-ajax>
                            <span class="guest-account-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'heart'])</span>
                            <span class="guest-account-card__text">@lang('Wishlist')</span>
                            <span class="guest-account-card__count show-wishlist-count">0</span>
                        </a>
                        <a href="{{ route('user.compare') }}" class="guest-account-card" data-no-ajax>
                            <span class="guest-account-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'exchange-alt'])</span>
                            <span class="guest-account-card__text">@lang('Compare')</span>
                            <span class="guest-account-card__count show-compare-count">0</span>
                        </a>
                        <a href="{{ route('user.cart') }}" class="guest-account-card" data-no-ajax>
                            <span class="guest-account-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'shopping-cart'])</span>
                            <span class="guest-account-card__text">@lang('Cart')</span>
                            <span class="guest-account-card__count show-cart-count">0</span>
                        </a>
                        <a href="{{ route('track.order') }}" class="guest-account-card" data-no-ajax>
                            <span class="guest-account-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast'])</span>
                            <span class="guest-account-card__text">@lang('Order Tracking')</span>
                        </a>
                    </div>

                    @if($general->multi_language && $guestLangs->isNotEmpty())
                        <div class="guest-account-lang mt-3">
                            <div class="small text-muted mb-2">@lang('Language')</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($guestLangs as $lng)
                                    <a href="{{ route('lang', $lng->code) }}" class="btn btn-sm btn-light border" data-no-ajax>{{ __($lng->name) }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
    /* StaylModal + Bootstrap .fade: must be visible when .is-open (above mobile bottom nav z-9999) */
    #guestAccountModal.is-open,
    #guestAccountModal.show {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        z-index: 100050 !important;
        overflow-x: hidden;
        overflow-y: auto;
        outline: 0;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: rgba(2, 6, 23, 0.5) !important;
    }
    #guestAccountModal.is-open .modal-dialog,
    #guestAccountModal.show .modal-dialog {
        opacity: 1 !important;
        transform: none !important;
        transition: opacity .2s ease, transform .2s ease;
    }
    #guestAccountModal .guest-account-modal-dialog { margin: 0.75rem auto; }
    /* Close control must sit inside the card, not viewport (avoid main.css .modal-close-btn top/right) */
    #guestAccountModal .guest-account-modal-content {
        position: relative !important;
        isolation: isolate;
    }
    #guestAccountModal .guest-account-close-btn {
        position: absolute !important;
        top: 10px !important;
        right: 10px !important;
        left: auto !important;
        bottom: auto !important;
        width: 36px !important;
        height: 36px !important;
        min-width: 36px !important;
        padding: 0 !important;
        margin: 0 !important;
        border: 0 !important;
        background: rgba(248, 250, 252, 0.95) !important;
        color: #475569 !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 20 !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12) !important;
        cursor: pointer !important;
    }
    #guestAccountModal .guest-account-close-btn .ui-icon {
        width: 16px !important;
        height: 16px !important;
    }
@media (max-width: 639.98px) {
    #guestAccountModal .guest-account-modal-dialog { max-width: min(560px, calc(100vw - 16px)); margin: 0.5rem auto; }
    #guestAccountModal .guest-account-modal-content { border-radius: 16px; box-shadow: 0 24px 60px rgba(2, 6, 23, 0.22); }
    #guestAccountModal .guest-account-auth-row { grid-template-columns: 1fr 1fr; }
    #guestAccountModal .guest-account-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 8px; }
    #guestAccountModal .guest-account-card {
        display: flex; align-items: center; gap: 8px; min-height: 44px; padding: 10px;
        text-decoration: none; color: #0f172a; border: 1px solid rgba(148,163,184,.35);
        border-radius: 10px; background: #fff;
    }
    #guestAccountModal .guest-account-card--btn {
        width: 100%;
        text-align: left;
        cursor: pointer;
    }
    #guestAccountModal .guest-account-grid--links {
        grid-template-columns: repeat(2, minmax(0,1fr));
    }
    #guestAccountModal .guest-account-card__icon .ui-icon { width: 18px; height: 18px; }
    #guestAccountModal .guest-account-card__text { font-size: 12px; font-weight: 600; }
    #guestAccountModal .guest-account-card__count {
        margin-left: auto; min-width: 18px; height: 18px; border-radius: 999px;
        background: #0f766e; color: #fff; font-size: 10px; line-height: 18px; text-align: center;
        padding: 0 5px; font-weight: 700;
    }
}
</style>
@endpush
@endguest
