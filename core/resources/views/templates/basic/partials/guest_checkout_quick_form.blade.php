{{-- Shared Quick Order form (modal + full /user/order page). IDs must match layout JS. --}}
@php $qoSettings = function_exists('quickOrderSettings') ? quickOrderSettings() : null; @endphp
<form id="guestCheckoutForm" class="guest-checkout-form quick-order-form">
    @csrf
    <input type="hidden" name="country" value="Bangladesh">
    <input type="hidden" name="division" value="">
    <input type="hidden" name="district" id="guest_district_hidden" value="">
    <input type="hidden" name="city" id="guest_city" value="">
    <input type="hidden" name="thana" value="">
    @if(!function_exists('isQuickOrderFieldEnabled') || !isQuickOrderFieldEnabled('postal_code'))
    <input type="hidden" name="postal_code" value="">
    @endif
    @if(!function_exists('isQuickOrderFieldEnabled') || !isQuickOrderFieldEnabled('guest_email'))
    <input type="hidden" name="guest_email" value="">
    @endif
    @if(!function_exists('isQuickOrderFieldEnabled') || !isQuickOrderFieldEnabled('guest_preferred_delivery_time'))
    <input type="hidden" name="guest_preferred_delivery_time" value="">
    @endif
    @if(!function_exists('isQuickOrderFieldEnabled') || !isQuickOrderFieldEnabled('guest_alternate_phone'))
    <input type="hidden" name="guest_alternate_phone" value="">
    @endif
    @if(!function_exists('isQuickOrderFieldEnabled') || !isQuickOrderFieldEnabled('guest_landmark'))
    <input type="hidden" name="guest_landmark" value="">
    @endif
    @if(!function_exists('isQuickOrderFieldEnabled') || !isQuickOrderFieldEnabled('guest_order_note'))
    <input type="hidden" name="guest_order_note" value="">
    @endif
    @if(!function_exists('isQuickOrderFieldEnabled') || !isQuickOrderFieldEnabled('guest_preferred_contact_time'))
    <input type="hidden" name="guest_preferred_contact_time" value="">
    @endif
    <input type="hidden" name="order_source" value="quick_order">
    <div class="qo-row-2">
    @if(!function_exists('isQuickOrderFieldEnabled') || isQuickOrderFieldEnabled('guest_phone'))
    <div class="form-group-qo">
        <input type="tel" class="form-control form-control-qo" name="guest_phone" id="guest_phone" required placeholder="@lang('Mobile Number') *" maxlength="20" autocomplete="tel" aria-label="@lang('Mobile Number')">
        <div class="invalid-feedback" data-field="guest_phone"></div>
    </div>
    @endif
    @if(!function_exists('isQuickOrderFieldEnabled') || isQuickOrderFieldEnabled('guest_name'))
    <div class="form-group-qo">
        <input type="text" class="form-control form-control-qo" name="guest_name" id="guest_name" required maxlength="200" autocomplete="name" placeholder="@lang('Full Name') *" aria-label="@lang('Full Name')">
        <div class="invalid-feedback" data-field="guest_name"></div>
    </div>
    @endif
    </div>
    @if(function_exists('isQuickOrderFieldEnabled') && isQuickOrderFieldEnabled('guest_email'))
    <div class="form-group-qo">
        <input type="email" class="form-control form-control-qo" name="guest_email" id="guest_email" maxlength="100" placeholder="@lang('Email') (@lang('Optional'))" autocomplete="email" aria-label="@lang('Email')">
    </div>
    @endif
    @if(function_exists('isQuickOrderFieldEnabled') && isQuickOrderFieldEnabled('guest_alternate_phone'))
    <div class="form-group-qo">
        <input type="tel" class="form-control form-control-qo" name="guest_alternate_phone" id="guest_alternate_phone" maxlength="20" placeholder="@lang('Alternate Phone') (@lang('Optional'))" autocomplete="tel-national" aria-label="@lang('Alternate Phone')">
    </div>
    @endif
    @if(function_exists('isQuickOrderFieldEnabled') && isQuickOrderFieldEnabled('guest_preferred_contact_time'))
    <div class="form-group-qo">
        <input type="text" class="form-control form-control-qo" name="guest_preferred_contact_time" id="guest_preferred_contact_time" maxlength="200" placeholder="@lang('Preferred contact time') (@lang('Optional'))" aria-label="@lang('Preferred contact time')">
    </div>
    @endif
    @if(!function_exists('isQuickOrderFieldEnabled') || isQuickOrderFieldEnabled('guest_address'))
    <div class="form-group-qo">
        <textarea class="form-control form-control-qo" name="guest_address" id="guest_address" rows="2" required maxlength="1000" placeholder="@lang('Delivery Address') *" autocomplete="street-address" aria-label="@lang('Delivery Address')"></textarea>
        <div class="invalid-feedback" data-field="guest_address"></div>
    </div>
    @endif
    <div class="qo-row-2">
    @if(!function_exists('isQuickOrderFieldEnabled') || isQuickOrderFieldEnabled('guest_area_city'))
    <div class="form-group-qo">
        <input type="text" class="form-control form-control-qo" name="guest_area_city" id="guest_area_city" required maxlength="200" placeholder="@lang('Area / City') *" aria-label="@lang('Area / City')">
        <div class="invalid-feedback" data-field="guest_area_city"></div>
    </div>
    @endif
    @if(function_exists('isQuickOrderFieldEnabled') && isQuickOrderFieldEnabled('postal_code'))
    <div class="form-group-qo">
        <input type="text" class="form-control form-control-qo" name="postal_code" id="guest_postal_code" maxlength="20" placeholder="@lang('Postal Code') (@lang('Optional'))" aria-label="@lang('Postal Code')">
    </div>
    @endif
    @if(function_exists('isQuickOrderFieldEnabled') && isQuickOrderFieldEnabled('guest_landmark'))
    <div class="form-group-qo">
        <input type="text" class="form-control form-control-qo" name="guest_landmark" id="guest_landmark" maxlength="200" placeholder="@lang('Landmark') (@lang('Optional'))" aria-label="@lang('Landmark')">
    </div>
    @endif
    </div>
    @if(!function_exists('isQuickOrderFieldEnabled') || isQuickOrderFieldEnabled('guest_delivery_note'))
    <div class="form-group-qo">
        <input type="text" class="form-control form-control-qo" name="guest_delivery_note" id="guest_delivery_note" maxlength="500" placeholder="@lang('Delivery instructions') (@lang('Optional'))" aria-label="@lang('Delivery instructions')">
    </div>
    @endif
    @if(function_exists('isQuickOrderFieldEnabled') && isQuickOrderFieldEnabled('guest_preferred_delivery_time'))
    <div class="form-group-qo">
        <input type="text" class="form-control form-control-qo" name="guest_preferred_delivery_time" id="guest_preferred_delivery_time" maxlength="200" placeholder="@lang('Preferred delivery time') (@lang('Optional'))" aria-label="@lang('Preferred delivery time')">
    </div>
    @endif
    @if(function_exists('isQuickOrderFieldEnabled') && isQuickOrderFieldEnabled('guest_order_note'))
    <div class="form-group-qo">
        <textarea class="form-control form-control-qo" name="guest_order_note" id="guest_order_note" rows="2" maxlength="500" placeholder="@lang('Order note / Special request') (@lang('Optional'))" aria-label="@lang('Order note')"></textarea>
    </div>
    @endif
    <div class="quick-order-actions">
        <button type="submit" class="btn btn-confirm-order" id="guestCheckoutSubmitBtn">
            <span class="btn-text">@include($activeTemplate . 'partials.icon', ['name' => 'check-circle', 'class' => 'me-1']) @lang('Confirm Order')</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <a href="{{ route('user.login') }}?open=login&redirect={{ urlencode(request()->fullUrl()) }}" class="btn btn-outline-secondary btn-login-link js-open-floating-login">
            @lang('Login to Checkout')
        </a>
        @if($qoSettings?->show_register_link ?? true)
        <a href="{{ route('user.register') }}?open=register&redirect={{ urlencode(request()->fullUrl()) }}" class="btn btn-outline-primary btn-login-link js-open-floating-register">
            @lang('Register')
        </a>
        @endif
    </div>
</form>
<div id="guestCheckoutSuccess" class="d-none rounded-3" role="alert">
    <p class="mb-0 text-success fw-medium">@include($activeTemplate . 'partials.icon', ['name' => 'check-circle', 'class' => 'me-1']) <span id="guestCheckoutSuccessMessage"></span></p>
</div>
<div id="guestCheckoutError" class="d-none rounded-3" role="alert">
    <p class="mb-0 text-danger" id="guestCheckoutErrorMessage"></p>
</div>
