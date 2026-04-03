@extends($activeTemplate . 'layouts.frontend')
@php
    $disableLegacyBootstrapBundle = true;
    $disableLegacyJquery = true;
    $disableLegacyJqueryUi = true;
    $disableLegacyOwl = true;
    $disableLegacyLightbox = true;
    $disableLegacyWow = true;
    $disableLegacyCarouselJs = true;
    $disableLegacyVisualLibs = true;
@endphp
@section('content')
    @php
        $userAddress = $userAddress ?? ($user->address ?? (object)['address' => '', 'address_2' => '', 'state' => '', 'city' => '', 'zip' => '', 'country' => '', 'thana' => '', 'division' => '']);
        $districtLabels = getDistrictLabels();
        $thanasByDistrict = $thanasByDistrict ?? [];
        $divisionList = $divisionList ?? [];
        $districtsByDivision = $districtsByDivision ?? [];
        $isBangladesh = (isset($userAddress->country) && (stripos($userAddress->country, 'Bangladesh') !== false || ($userAddress->country ?? '') === 'Bangladesh'));
        $savedAddresses = $savedAddresses ?? collect();
    @endphp
    <div class="checkout-section pt-2 pb-3 bg-light">
        <div class="container checkout-container w-full max-w-storefront mx-auto px-4 sm:px-6 lg:px-8">
            <div class="checkout-header text-center mb-1">
                <a href="{{ route('user.cart') }}" class="btn btn-sm btn-outline-primary mb-2">@include($activeTemplate . 'partials.icon', ['name' => 'arrow-left', 'class' => 'me-1'])@lang('Back to Cart')</a>
                <h5 class="mb-0 fw-bold checkout-title">@lang('Secure Checkout')</h5>
                <p class="text-muted small mb-0 mt-0">@lang('Complete your order securely.')</p>
            </div>

            <div class="checkout-trust-bar d-flex flex-wrap justify-content-center gap-1 gap-md-2 mb-2 py-1 px-2 rounded-3">
                <span class="checkout-trust-pill">@include($activeTemplate . 'partials.icon', ['name' => 'shield-alt', 'class' => 'text-success']) @lang('Secure')</span>
                <span class="checkout-trust-pill">@include($activeTemplate . 'partials.icon', ['name' => 'lock', 'class' => 'text-success']) @lang('SSL')</span>
                <span class="checkout-trust-pill">@include($activeTemplate . 'partials.icon', ['name' => 'user-shield', 'class' => 'text-success']) @lang('Protected')</span>
                <span class="checkout-trust-pill">@include($activeTemplate . 'partials.icon', ['name' => 'headset', 'class' => 'text-success']) @lang('Support')</span>
            </div>

            @php $checkoutTimers = get_offer_timers_for_display('checkout', 'checkout_top'); @endphp
            @if($checkoutTimers->isNotEmpty())
                @foreach($checkoutTimers as $ct)
                    <div class="mb-3">
                        @include('partials.offer_timer_bar', ['timer' => $ct])
                    </div>
                @endforeach
            @endif
            @if($data['discount'] > 0)
            <div class="checkout-discount-banner d-flex align-items-center justify-content-between flex-wrap gap-2 p-2 px-3 rounded-3 bg-white border mb-3" id="checkoutDiscountBanner">
                <div class="d-flex align-items-center gap-2">
                    @include($activeTemplate . 'partials.icon', ['name' => 'bolt', 'class' => 'text--base'])
                    <span class="small fw-medium">@lang('Discount applied') — @lang('Complete order to save')</span>
                </div>
                <div class="d-flex gap-1 small" id="checkoutCountdown">
                    <span class="px-2 py-0 rounded bg-light"><span class="countdown-hrs fw-bold">00</span>h</span>
                    <span class="px-2 py-0 rounded bg-light"><span class="countdown-mins fw-bold">00</span>m</span>
                    <span class="px-2 py-0 rounded bg-light"><span class="countdown-secs fw-bold">00</span>s</span>
                </div>
            </div>
            @endif

            <form id="checkout-form" action="{{ route('user.checkout.order') }}" method="POST">
                @csrf
                <input type="hidden" name="device_lat" id="checkoutDeviceLat" value="">
                <input type="hidden" name="device_lng" id="checkoutDeviceLng" value="">
                <div class="row g-1 g-lg-2 align-items-stretch">
                    <div class="col-lg-7 d-flex">
                        <div class="card border-0 shadow-sm checkout-card flex-grow-1 d-flex flex-column w-100">
                            <div class="card-header checkout-card-header py-1 px-2">
                                <span class="checkout-step-num">1</span>
                                <h6 class="mb-0 text-truncate checkout-card-title">@include($activeTemplate . 'partials.icon', ['name' => 'user', 'class' => 'me-1 text--base'])@lang('Billing & Delivery')</h6>
                            </div>
                            <div class="card-body p-2 flex-grow-1 checkout-card-body">
                                <p class="small text-muted mb-1 checkout-hint">@include($activeTemplate . 'partials.icon', ['name' => 'info-circle', 'class' => 'me-1 text--base'])@lang('Pre-filled from your account. Edit below.')</p>
                                <div class="checkout-form-groups">
                                    <div class="checkout-group">
                                        <div class="checkout-group-label">@include($activeTemplate . 'partials.icon', ['name' => 'user', 'class' => 'me-1'])@lang('Contact')</div>
                                        <div class="row g-1">
                                            <div class="col-6 col-md-6">
                                                <label class="form-label checkout-label">@lang('First name')</label>
                                                <input type="text" class="form-control form-control-sm checkout-input" name="firstname" required value="{{ old('firstname', $user->firstname) }}" readonly>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <label class="form-label checkout-label">@lang('Last name')</label>
                                                <input type="text" class="form-control form-control-sm checkout-input" name="lastname" required value="{{ old('lastname', $user->lastname) }}" readonly>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <label class="form-label checkout-label">@lang('Email')</label>
                                                <input type="email" name="email" class="form-control form-control-sm checkout-input" required value="{{ old('email', $user->email) }}" readonly>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <label class="form-label checkout-label">@lang('Phone')</label>
                                                <input type="tel" class="form-control form-control-sm checkout-input" name="mobile" required value="{{ old('mobile', $user->mobile) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="checkout-group">
                                        <div class="checkout-group-label">@include($activeTemplate . 'partials.icon', ['name' => 'map-marker-alt', 'class' => 'me-1'])@lang('Location')</div>
                                        @if($savedAddresses->isNotEmpty())
                                        <div class="row g-1 mb-1">
                                            <div class="col-12">
                                                <label class="form-label checkout-label">@lang('Use saved address')</label>
                                                <select id="checkoutSavedAddress" class="form-select form-select-sm checkout-input">
                                                    <option value="">— @lang('New address') —</option>
                                                    @foreach($savedAddresses as $addr)
                                                    <option value="{{ $addr->id }}" data-address="{{ e(json_encode([
                                                        'country' => $addr->country,
                                                        'division' => $addr->division->name_en ?? '',
                                                        'district' => $addr->district->name_en ?? '',
                                                        'city' => $addr->district->name_en ?? $addr->city,
                                                        'thana' => $addr->thana->name_en ?? '',
                                                        'postal_code' => $addr->postal_code ?? '',
                                                        'address' => $addr->address_line,
                                                        'address_2' => $addr->address_line_2 ?? '',
                                                        'state' => $addr->state ?? '',
                                                    ])) }}">{{ $addr->label ?: __('Address') }} {{ $addr->is_default ? '★' : '' }} — {{ $addr->address_line }}, {{ $addr->district->name_en ?? $addr->city }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="row g-1">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label checkout-label">@lang('Country') <span class="text-danger">*</span></label>
                                        <select name="country" id="checkoutCountry" class="form-select form-select-sm checkout-input" required>
                                            <option value="">@lang('Select country')</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->country }}" @selected(old('country', $userAddress->country ?? '') == $country->country)>{{ __($country->country) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label checkout-label">@lang('Post Code') / @lang('ZIP') <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm checkout-input" name="zip" required value="{{ old('zip', $userAddress->zip ?? '') }}" placeholder="@lang('ZIP / Postal code')">
                                    </div>
                                    {{-- Bangladesh: Division ও District পাশাপাশি --}}
                                    <div class="col-12 col-md-6" id="wrapBangladeshDivision" style="{{ $isBangladesh ? '' : 'display:none;' }}">
                                        <label class="form-label checkout-label">@lang('Division') / বিভাগ <span class="text-danger">*</span></label>
                                        <select id="checkoutDivisionBd" class="form-select form-select-sm checkout-input" name="division">
                                            <option value="">@lang('Select division')</option>
                                            @foreach ($divisionList as $div)
                                                @php $divId = is_array($div) ? ($div['id'] ?? '') : ($div->id ?? ''); $nameEn = is_array($div) ? ($div['name_en'] ?? '') : ($div->name_en ?? ''); $nameBn = is_array($div) ? ($div['name_bn'] ?? '') : ($div->name_bn ?? ''); @endphp
                                                <option value="{{ $nameEn }}" data-id="{{ $divId }}" @selected(old('division', $userAddress->division ?? '') == $nameEn)>{{ $nameEn }}@if($nameBn) / {{ $nameBn }}@endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6" id="wrapBangladeshDistrict" style="{{ $isBangladesh ? '' : 'display:none;' }}">
                                        <label class="form-label checkout-label">{{ $districtLabels->label_en }} / {{ $districtLabels->label_bn }} <span class="text-danger">*</span></label>
                                        <select id="checkoutDistrictBd" class="form-select form-select-sm checkout-input">
                                            <option value="">@lang('Select division first')</option>
                                        </select>
                                    </div>
                                    {{-- Bangladesh: ৪. Thana --}}
                                    <div class="col-12 col-md-6" id="wrapBangladeshThana" style="{{ $isBangladesh ? '' : 'display:none;' }}">
                                        <label class="form-label checkout-label">@lang('Thana') / থানা</label>
                                        <select id="checkoutThanaBd" class="form-select form-select-sm checkout-input" name="thana">
                                            <option value="">@lang('Select district first')</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="city" id="cityValue" value="{{ old('city', $userAddress->city ?? '') }}">
                                    {{-- Other countries: City ও State (সব ম্যানুয়াল) --}}
                                    <div class="col-12 col-md-6" id="wrapCityStateZip" style="{{ $isBangladesh ? 'display:none;' : '' }}">
                                        <label class="form-label checkout-label">@lang('City') <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm checkout-input" id="checkoutCity" value="{{ old('city', $userAddress->city ?? '') }}" placeholder="@lang('City')">
                                    </div>
                                    <div class="col-12 col-md-6" id="wrapStateZip" style="{{ $isBangladesh ? 'display:none;' : '' }}">
                                        <label class="form-label checkout-label">@lang('State') <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm checkout-input" name="state" id="checkoutState" value="{{ old('state', $userAddress->state ?? '') }}" placeholder="@lang('State / Province')">
                                    </div>
                                        </div>
                                    </div>
                                    <div class="checkout-group">
                                        <div class="checkout-group-label">@include($activeTemplate . 'partials.icon', ['name' => 'home', 'class' => 'me-1'])@lang('Address')</div>
                                        <div class="row g-1">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label checkout-label">@lang('Address') <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm checkout-input" name="address" required value="{{ old('address', $userAddress->address ?? '') }}" placeholder="@lang('Street, house no., area')">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label checkout-label">@lang('Address 2')</label>
                                        <input type="text" class="form-control form-control-sm checkout-input" name="address_2" value="{{ old('address_2', $userAddress->address_2 ?? '') }}" placeholder="@lang('Apartment, landmark (optional)')">
                                    </div>
                                    <div class="col-12" id="wrapDeliveryCol">
                                        <label class="form-label checkout-label">@lang('Delivery') <span class="text-danger">*</span></label>
                                        <select name="shipping_method" id="shippingMethodSelect" class="form-select form-select-sm checkout-input" required>
                                            <option value="">@lang('Select country & area first')</option>
                                            @foreach($shippingMethodOptions ?? [] as $opt)
                                                <option value="{{ $opt['id'] }}" data-price="{{ getAmount($opt['price']) }}" data-days="{{ $opt['estimated_days'] ?? '' }}" data-courier="{{ $opt['courier_name'] ?? '' }}" data-free="{{ !empty($opt['free_applied']) ? '1' : '0' }}" {{ $loop->first ? 'selected' : '' }}>{{ __($opt['name']) }} — {{ $general->cur_sym }}{{ getAmount($opt['price']) }}{{ !empty($opt['free_applied']) ? ' (' . __('Free shipping') . ')' : '' }}{{ !empty($opt['estimated_days']) ? ' (' . ($opt['estimated_days'] ?? '') . ')' : '' }}</option>
                                            @endforeach
                                        </select>
                                        <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
                                            <span class="checkout-delivery-charge-label small text-muted">@lang('Delivery charge')</span>
                                            <span id="shippingChargeLeft" class="checkout-shipping-charge small fw-medium">{{ $general->cur_sym }}0.00</span>
                                            <span id="shippingDaysLeft" class="checkout-shipping-days small text-muted"></span>
                                        </div>
                                        <p class="small text-muted mb-0 mt-0">@include($activeTemplate . 'partials.icon', ['name' => 'info-circle', 'class' => 'me-1']) @lang('First option is auto-selected; you can change it manually.')</p>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-check-sm checkout-save-address">
                                            <input type="hidden" name="save_address" value="0">
                                            <input class="form-check-input" type="checkbox" name="save_address" id="saveAddress" value="1" checked>
                                            <label class="form-check-label checkout-label" for="saveAddress">@lang('Save address for future orders')</label>
                                        </div>
                                    </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 d-flex">
                        <div class="card border-0 shadow-sm checkout-card flex-grow-1 d-flex flex-column w-100 sticky-top">
                            <div class="card-header checkout-card-header py-2 px-3">
                                <span class="checkout-step-num">2</span>
                                <h6 class="mb-0 text-truncate">@include($activeTemplate . 'partials.icon', ['name' => 'shopping-bag', 'class' => 'me-1 text--base'])@lang('Order & Payment')</h6>
                            </div>
                            <div class="card-body p-3 flex-grow-1 checkout-card-body">
                                <div class="checkout-products mb-2">
                                    @foreach($carts as $cart)
                                        @php
                                            if ($cart->product === null) { continue; }
                                            $price = productPrice($cart->product);
                                            if ($cart->variant_id) {
                                                $variant = \App\Models\ProductVariant::find($cart->variant_id);
                                                $price = $variant ? showDiscountPrice($variant->price, $variant->discount ?? 0, $variant->discount_type ?? 1) : $price;
                                            }
                                            $image = $cart->product->image ?? '';
                                            $name = $cart->product->name ?? '';
                                        @endphp
                                        <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom checkout-product-row">
                                            <div class="flex-shrink-0 rounded overflow-hidden checkout-thumb">
                                                <img src="{{ getImage(getFilePath('product') . '/' . $image, getFileSize('product')) }}" alt="{{ __($name) }}" class="w-100 h-100 object-fit-contain" loading="lazy" width="56" height="56" decoding="async">
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="small fw-medium text-truncate">{{ __($name) }}</div>
                                                <small class="text-muted">{{ $general->cur_sym }}{{ getAmount($price) }} × {{ $cart->quantity }}</small>
                                            </div>
                                            <div class="small fw-semibold text--base flex-shrink-0">{{ $general->cur_sym }}{{ getAmount($price * $cart->quantity) }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                <ul class="list-unstyled small mb-0">
                                    <li class="d-flex justify-content-between py-1">
                                        <span class="text-muted">@lang('Subtotal')</span>
                                        <span class="subtotal-price fw-medium">{{ $general->cur_sym }}{{ getAmount($data['subtotal']) }}</span>
                                    </li>
                                    @if($data['discount'] > 0)
                                    <li class="d-flex justify-content-between py-1">
                                        <span class="text-muted">@lang('Discount')</span>
                                        <span class="text-success">-{{ $general->cur_sym }}{{ getAmount($data['discount']) }}</span>
                                    </li>
                                    @endif
                                    <li class="d-flex justify-content-between align-items-center py-1 grand-total-row flex-wrap gap-1">
                                        <span class="text-muted">@lang('Shipping')</span>
                                        <span class="d-inline-flex align-items-center gap-2 flex-shrink-0">
                                            <span class="shipping-price checkout-shipping-charge">{{ $general->cur_sym }}0.00</span>
                                            <span id="shippingDaysRight" class="checkout-shipping-days text-muted"></span>
                                        </span>
                                    </li>
                                    <li class="d-flex justify-content-between py-1 cod-charge-row flex-wrap gap-1" id="codChargeRow" style="display: none;">
                                        <span class="text-muted">@lang('COD Charge')</span>
                                        <span class="cod-charge-price fw-medium">{{ $general->cur_sym }}<span id="codChargeAmount">0.00</span></span>
                                    </li>
                                    <li class="d-flex justify-content-between py-2 border-top mt-1 pt-2">
                                        <span class="fw-semibold">@lang('Total Payable')</span>
                                        <span class="grand-total-price fw-bold text--base">{{ $general->cur_sym }}{{ getAmount($data['total']) }}</span>
                                    </li>
                                </ul>
                                <div class="payment-methods mt-2 pt-2 border-top">
                                    <h6 class="small fw-semibold mb-2">@lang('Payment') <span class="text-danger">*</span></h6>
                                    <div class="d-flex flex-column gap-1">
                                        <label class="payment-option-card d-flex align-items-center gap-2 p-2 rounded-2 border cursor-pointer">
                                            <input type="radio" name="payment_type" value="1" class="form-check-input" checked>
                                            @include($activeTemplate . 'partials.icon', ['name' => 'credit-card', 'class' => 'text--base'])
                                            <div class="min-w-0">
                                                <span class="small fw-medium d-block">@lang('Online Payment')</span>
                                                <small class="text-muted" style="font-size: 0.7rem;">{{ $onlinePaymentSubtitle ?? __('Card, bKash, Nagad, etc.') }}</small>
                                            </div>
                                        </label>
                                        @if($codEligible ?? true)
                                        <label class="payment-option-card d-flex align-items-center gap-2 p-2 rounded-2 border cursor-pointer" id="codOptionLabel">
                                            <input type="radio" name="payment_type" value="2" class="form-check-input">
                                            @include($activeTemplate . 'partials.icon', ['name' => 'money-bill-wave', 'class' => 'text--base'])
                                            <div class="min-w-0">
                                                <span class="small fw-medium d-block">@lang('Pay with Cash on Delivery')</span>
                                                <small class="text-muted" style="font-size: 0.7rem;">@lang('Pay when you receive')</small>
                                            </div>
                                        </label>
                                        @else
                                        <div class="p-2 rounded-2 border bg-light" id="codDisabledMessage">
                                            <span class="small text-muted">@include($activeTemplate . 'partials.icon', ['name' => 'ban', 'class' => 'me-1'])@lang('Cash on Delivery') — {{ $codReason ?? __('Not available for this order.') }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    @if(($codOtpRequired ?? false) && ($codEligible ?? true))
                                    <p class="small text-muted mt-1 mb-0">@include($activeTemplate . 'partials.icon', ['name' => 'sms', 'class' => 'me-1'])@lang('OTP will be sent to your mobile to confirm COD order.')</p>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn--base w-100 py-2 mt-2 btn-place-order">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'lock', 'class' => 'me-1'])@lang('Place order')
                                </button>
                                <a href="{{ route('user.cart') }}" class="btn btn-outline-secondary btn-sm w-100 mt-1">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'arrow-left', 'class' => 'me-1'])@lang('Back to cart')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('style')
<style>
/* Checkout: professional layout, no cache (handled by controller headers) */
.checkout-section { overflow-x: hidden; width: 100%; box-sizing: border-box; -webkit-overflow-scrolling: touch; }
.checkout-container { max-width: var(--stayl-content-max, min(1920px, calc(100vw - 20px))); margin: 0 auto; width: 100%; box-sizing: border-box; padding-left: 12px; padding-right: 12px; }
.checkout-container .row > [class*="col-"] { min-width: 0; }
.checkout-trust-bar { background: #fff; border: 1px solid #eee; }
.checkout-trust-pill { font-size: 0.75rem; color: #555; white-space: nowrap; }
.checkout-trust-pill i { margin-right: 2px; }
.checkout-card { border-radius: 12px; min-height: 0; }
.checkout-card-header { background: #f8f9fa; border-radius: 12px 12px 0 0; display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.checkout-step-num { width: 22px; height: 22px; border-radius: 50%; background: var(--base, #6366f1); color: #fff; font-size: 0.7rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
.checkout-card-body { min-height: 0; overflow-wrap: break-word; word-wrap: break-word; overflow-x: hidden; }
.checkout-section .form-control, .checkout-section .form-select { max-width: 100%; box-sizing: border-box; }
/* Billing & Delivery: grouped, compact, same page size */
.checkout-form-groups { display: flex; flex-direction: column; gap: 0.5rem; }
.checkout-group { background: rgba(0,0,0,.02); border-radius: 8px; padding: 0.4rem 0.5rem; border: 1px solid rgba(0,0,0,.06); }
.checkout-group-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.02em; color: #555; margin-bottom: 0.35rem; padding-bottom: 0.2rem; border-bottom: 1px solid rgba(0,0,0,.06); display: flex; align-items: center; }
.checkout-group-label i { opacity: 0.8; }
.checkout-label { font-size: 0.75rem; margin-bottom: 0.15rem; font-weight: 500; color: #444; }
.checkout-input,
.checkout-section .checkout-card-body .form-control,
.checkout-section .checkout-card-body .form-select,
.checkout-section .checkout-card-body .form-control-sm,
.checkout-section .checkout-card-body .form-select-sm { font-size: 0.8rem !important; padding: 0.22rem 0.4rem !important; min-height: 26px !important; height: auto !important; line-height: 1.3; border-radius: 6px; border-color: rgba(0,0,0,.12); transition: border-color .15s, box-shadow .15s; }
.checkout-section .checkout-card-body .form-control:focus,
.checkout-section .checkout-card-body .form-select:focus { border-color: var(--base, #6366f1); box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15); outline: 0; }
.checkout-section .checkout-card-body select.form-select,
.checkout-section .checkout-card-body select.form-select-sm { padding-top: 0.2rem; padding-bottom: 0.2rem; }
.checkout-section .checkout-card-body .form-control::placeholder { font-size: 0.75rem; color: #999; }
.checkout-title { font-size: 1rem; }
.checkout-hint { font-size: 0.75rem; }
.checkout-card-title { font-size: 0.9rem; }
.checkout-card-body .row.g-1 .col-12 { margin-bottom: 0.1rem; }
.checkout-card-body .row.g-1 .col-12:last-child { margin-bottom: 0; }
.checkout-save-address { margin-top: 0.15rem; }
.checkout-save-address .form-check-label { font-weight: 400; }
.checkout-shipping-display { background: rgba(0,0,0,.04); border: 1px solid rgba(0,0,0,.08); color: #333; }
.checkout-shipping-charge { font-size: 0.8rem; font-weight: 600; color: var(--base, #6366f1); }
.checkout-shipping-days { font-size: 0.75rem; white-space: nowrap; }
.checkout-delivery-charge-label { font-size: 0.75rem; }
.checkout-thumb { width: 40px; height: 40px; background: #f0f0f0; flex-shrink: 0; }
.checkout-product-row { max-width: 100%; min-width: 0; }
.checkout-product-row .flex-grow-1 { min-width: 0; }
.checkout-products .text-truncate { max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.payment-methods .payment-option-card .min-w-0 { min-width: 0; overflow: hidden; }
.payment-option-card { cursor: pointer; transition: border-color .15s, background .15s; min-width: 0; }
.payment-option-card:hover, .payment-option-card:has(input:checked) { border-color: var(--base, #6366f1) !important; background: rgba(99, 102, 241, 0.06); }
.btn-place-order { font-weight: 600; border-radius: 8px; }
/* Tablet (768px - 991px) */
@media (max-width: 991px) {
    .checkout-section { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }
    .sticky-top { position: relative !important; }
    .checkout-container { padding-left: 15px; padding-right: 15px; }
    .checkout-container .row.align-items-stretch .col-lg-5, .checkout-container .row.align-items-stretch .col-lg-7 { align-self: stretch; }
}
/* Mobile (< 768px) */
@media (max-width: 767px) {
    .checkout-section { padding-top: 1rem !important; padding-bottom: 1rem !important; }
    .checkout-container { padding-left: 10px; padding-right: 10px; max-width: 100%; }
    .checkout-header h5 { font-size: 1.1rem; }
    .checkout-trust-bar { gap: 6px; padding: 8px 10px !important; }
    .checkout-trust-pill { font-size: 0.7rem; }
    .checkout-card-header { padding: 10px 12px !important; }
    .checkout-card-body { padding: 12px !important; }
    .checkout-section .col-4 { flex: 0 0 33.333%; max-width: 33.333%; }
    .btn-place-order { padding: 10px 16px; font-size: 0.95rem; }
}
/* Small mobile (< 576px) */
@media (max-width: 575px) {
    .checkout-section .col-4 { flex: 0 0 100%; max-width: 100%; }
    .checkout-section .col-6 { flex: 0 0 100%; max-width: 100%; }
}
</style>
@endpush

@push('script')
<script>
(function initCheckout() {
    'use strict';
    var curSym = '{{ $general->cur_sym }}';
    var codCharge = parseFloat('{{ getAmount($codCharge ?? 0) }}') || 0;
    var codEligible = {{ ($codEligible ?? true) ? 'true' : 'false' }};
    var shippingOptionsUrl = '{{ route("user.checkout.shipping.options") }}';
    var thanasByDistrict = @json($thanasByDistrict);
    var districtsByDivision = @json($districtsByDivision);
    var savedThana = '{{ old("thana", $userAddress->thana ?? "") }}';
    var savedCity = '{{ old("city", $userAddress->city ?? "") }}';

    var checkoutForm = document.getElementById('checkout-form');
    if (!checkoutForm) return;

    var checkoutCountry = document.getElementById('checkoutCountry');
    var checkoutDivisionBd = document.getElementById('checkoutDivisionBd');
    var checkoutDistrictBd = document.getElementById('checkoutDistrictBd');
    var checkoutThanaBd = document.getElementById('checkoutThanaBd');
    var checkoutCity = document.getElementById('checkoutCity');
    var checkoutState = document.getElementById('checkoutState');
    var cityValue = document.getElementById('cityValue');
    var shippingMethodSelect = document.getElementById('shippingMethodSelect');
    var checkoutSavedAddress = document.getElementById('checkoutSavedAddress');
    var shippingChargeLeft = document.getElementById('shippingChargeLeft');
    var shippingDaysLeft = document.getElementById('shippingDaysLeft');
    var shippingDaysRight = document.getElementById('shippingDaysRight');
    var codChargeRow = document.getElementById('codChargeRow');
    var codChargeAmount = document.getElementById('codChargeAmount');
    var checkoutDeviceLat = document.getElementById('checkoutDeviceLat');
    var checkoutDeviceLng = document.getElementById('checkoutDeviceLng');

    var wrapBangladeshDivision = document.getElementById('wrapBangladeshDivision');
    var wrapBangladeshDistrict = document.getElementById('wrapBangladeshDistrict');
    var wrapBangladeshThana = document.getElementById('wrapBangladeshThana');
    var wrapCityStateZip = document.getElementById('wrapCityStateZip');
    var wrapStateZip = document.getElementById('wrapStateZip');
    var zipInput = checkoutForm.querySelector('input[name="zip"]');
    var addressInput = checkoutForm.querySelector('input[name="address"]');
    var address2Input = checkoutForm.querySelector('input[name="address_2"]');
    var stateInput = checkoutForm.querySelector('[name="state"]');
    var subtotalPriceEl = checkoutForm.querySelector('.subtotal-price');
    var shippingPriceEls = checkoutForm.querySelectorAll('.shipping-price');
    var grandTotalPriceEls = checkoutForm.querySelectorAll('.grand-total-price');
    var paymentTypeInputs = checkoutForm.querySelectorAll('input[name="payment_type"]');

    function setRequired(el, required) { if (el) el.required = required; }
    function setVisible(el, visible) { if (el) el.style.display = visible ? '' : 'none'; }
    function getSelectedPaymentType() {
        var checked = checkoutForm.querySelector('input[name="payment_type"]:checked');
        return checked ? checked.value : '1';
    }

    function fillDistrictOptions(divisionId) {
        if (!checkoutDistrictBd) return;
        checkoutDistrictBd.innerHTML = '<option value="">@lang('Select district')</option>';
        if (!divisionId || !districtsByDivision || !districtsByDivision[divisionId]) return;

        var list = districtsByDivision[divisionId];
        list.forEach(function(item) {
            var en = item.en || '';
            var bn = item.bn || '';
            var label = en + (bn ? ' / ' + bn : '');
            var opt = document.createElement('option');
            opt.value = en;
            opt.textContent = label;
            if (savedCity && savedCity === en) opt.selected = true;
            checkoutDistrictBd.appendChild(opt);
        });
        if (savedCity) checkoutDistrictBd.value = savedCity;
        fillThanaOptions(checkoutDistrictBd.value);
    }

    function fillThanaOptions(district) {
        if (!checkoutThanaBd) return;
        checkoutThanaBd.innerHTML = '<option value="">@lang('Select Thana')</option>';
        if (!district || !thanasByDistrict || !thanasByDistrict[district]) return;

        thanasByDistrict[district].forEach(function(item) {
            var en = item.en || item.name_en || '';
            var bn = item.bn || item.name_bn || '';
            var postal = item.postal_code || '';
            var label = en + (bn ? ' / ' + bn : '');
            var opt = document.createElement('option');
            opt.value = en;
            opt.textContent = label;
            opt.dataset.postal = postal;
            if (savedThana && savedThana === en) opt.selected = true;
            checkoutThanaBd.appendChild(opt);
        });

        if (savedThana) checkoutThanaBd.value = savedThana;
        updateZipFromThana();
    }

    function updateZipFromThana() {
        if (!checkoutThanaBd || !zipInput) return;
        var selected = checkoutThanaBd.options[checkoutThanaBd.selectedIndex];
        var postal = selected ? selected.dataset.postal : '';
        if (postal) zipInput.value = postal;
    }

    function getSubtotal() {
        var fallback = parseFloat('{{ getAmount($data["subtotal"]) }}') || 0;
        if (!subtotalPriceEl) return fallback;
        var text = (subtotalPriceEl.textContent || '').replace(curSym, '').trim();
        var parsed = parseFloat(text);
        return Number.isFinite(parsed) ? parsed : fallback;
    }

    function updateTotals() {
        var subtotal = getSubtotal();
        var discount = parseFloat('{{ getAmount($data["discount"]) }}') || 0;
        var selectedOpt = shippingMethodSelect ? shippingMethodSelect.options[shippingMethodSelect.selectedIndex] : null;
        var price = selectedOpt ? (parseFloat(selectedOpt.dataset.price) || 0) : 0;
        var isCod = getSelectedPaymentType() === '2';
        var addCod = (codEligible && isCod) ? codCharge : 0;
        var grandTotal = subtotal + price - discount + addCod;
        var priceText = curSym + price.toFixed(2);

        shippingPriceEls.forEach(function(el) { el.textContent = priceText; });
        if (shippingChargeLeft) shippingChargeLeft.textContent = priceText;
        grandTotalPriceEls.forEach(function(el) { el.textContent = curSym + grandTotal.toFixed(2); });

        if (codEligible && isCod && codCharge > 0) {
            if (codChargeRow) codChargeRow.style.display = '';
            if (codChargeAmount) codChargeAmount.textContent = codCharge.toFixed(2);
        } else {
            if (codChargeRow) codChargeRow.style.display = 'none';
            if (codChargeAmount) codChargeAmount.textContent = '0.00';
        }

        var days = selectedOpt ? (selectedOpt.dataset.days || '') : '';
        var courier = selectedOpt ? (selectedOpt.dataset.courier || '') : '';
        var daysText = days ? ((courier ? (courier + ' — ') : '') + '{{ __("Est. delivery") }}: ' + days) : '';
        if (shippingDaysLeft) shippingDaysLeft.textContent = daysText;
        if (shippingDaysRight) shippingDaysRight.textContent = daysText;
    }

    function isBangladesh() {
        var c = checkoutCountry ? (checkoutCountry.value || '').toLowerCase() : '';
        return c.indexOf('bangladesh') !== -1 || c === 'bangladesh';
    }

    function getCityForShipping() {
        if (isBangladesh()) return (cityValue && cityValue.value) || (checkoutDistrictBd && checkoutDistrictBd.value) || '';
        return (checkoutCity && checkoutCity.value) || (cityValue && cityValue.value) || '';
    }

    function syncCityValue() {
        if (!cityValue) return;
        if (isBangladesh()) cityValue.value = (checkoutDistrictBd && checkoutDistrictBd.value) || '';
        else cityValue.value = (checkoutCity && checkoutCity.value) || '';
    }

    function toggleCountryFields() {
        var bd = isBangladesh();
        setVisible(wrapBangladeshDivision, bd);
        setVisible(wrapBangladeshDistrict, bd);
        setVisible(wrapBangladeshThana, bd);
        setVisible(wrapCityStateZip, !bd);
        setVisible(wrapStateZip, !bd);
        setRequired(checkoutDistrictBd, bd);
        setRequired(checkoutCity, !bd);

        if (bd) {
            var selectedDivisionOpt = checkoutDivisionBd ? checkoutDivisionBd.options[checkoutDivisionBd.selectedIndex] : null;
            var divId = selectedDivisionOpt ? selectedDivisionOpt.dataset.id : '';
            if (divId) fillDistrictOptions(divId);
            else if (checkoutDistrictBd) checkoutDistrictBd.innerHTML = '<option value="">@lang('Select division first')</option>';
        }
        syncCityValue();
    }

    var shippingFetchController = null;
    function fetchShippingOptions() {
        var country = checkoutCountry ? checkoutCountry.value : '';
        syncCityValue();
        var city = getCityForShipping();
        var state = stateInput ? stateInput.value : '';
        var paymentType = getSelectedPaymentType();
        var subtotal = getSubtotal();

        if (!shippingMethodSelect) return;
        if (!country) {
            while (shippingMethodSelect.options.length > 1) shippingMethodSelect.remove(1);
            shippingMethodSelect.value = '';
            updateTotals();
            return;
        }

        if (shippingFetchController) shippingFetchController.abort();
        shippingFetchController = new AbortController();
        var params = new URLSearchParams({
            country: country,
            city: city,
            state: state,
            payment_type: paymentType,
            subtotal: String(subtotal)
        });

        fetch(shippingOptionsUrl + '?' + params.toString(), {
            method: 'GET',
            signal: shippingFetchController.signal,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function(res) { return res.json(); })
        .then(function(res) {
            while (shippingMethodSelect.options.length > 1) shippingMethodSelect.remove(1);
            if (res && res.success && Array.isArray(res.methods) && res.methods.length > 0) {
                res.methods.forEach(function(m, i) {
                    var priceVal = parseFloat(m.price) || 0;
                    var text = (m.name || '') + ' — ' + curSym + priceVal.toFixed(2);
                    if (m.free_applied) text += ' ({{ __("Free shipping") }})';
                    if (m.estimated_days) text += ' (' + m.estimated_days + ')';
                    var opt = document.createElement('option');
                    opt.value = m.id;
                    opt.dataset.price = String(priceVal);
                    opt.dataset.days = m.estimated_days || '';
                    opt.dataset.courier = m.courier_name || '';
                    opt.dataset.free = m.free_applied ? '1' : '0';
                    opt.textContent = text;
                    if (i === 0) opt.selected = true;
                    shippingMethodSelect.appendChild(opt);
                });
            }
            updateTotals();
        })
        .catch(function(err) {
            if (err && err.name === 'AbortError') return;
            updateTotals();
        });
    }

    if (checkoutSavedAddress) {
        checkoutSavedAddress.addEventListener('change', function() {
            var selectedOpt = checkoutSavedAddress.options[checkoutSavedAddress.selectedIndex];
            var raw = selectedOpt ? selectedOpt.getAttribute('data-address') : '';
            if (!raw || !selectedOpt.value) return;

            try {
                var ad = JSON.parse(raw);
                if (checkoutCountry) checkoutCountry.value = ad.country || '';
                toggleCountryFields();

                if (ad.division && checkoutDivisionBd) {
                    checkoutDivisionBd.value = ad.division;
                    var divOpt = checkoutDivisionBd.options[checkoutDivisionBd.selectedIndex];
                    var divId = divOpt ? divOpt.dataset.id : '';
                    if (divId) fillDistrictOptions(divId);

                    setTimeout(function() {
                        if (ad.district && checkoutDistrictBd) checkoutDistrictBd.value = ad.district;
                        if (ad.thana) {
                            fillThanaOptions(ad.district || (checkoutDistrictBd && checkoutDistrictBd.value) || '');
                            setTimeout(function() {
                                if (checkoutThanaBd) checkoutThanaBd.value = ad.thana;
                                updateZipFromThana();
                            }, 50);
                        }
                        if (ad.postal_code && zipInput) zipInput.value = ad.postal_code;
                    }, 100);
                }

                if (ad.address && addressInput) addressInput.value = ad.address;
                if (ad.address_2 !== undefined && address2Input) address2Input.value = ad.address_2;
                if (ad.state && stateInput) stateInput.value = ad.state;
                var bdDistrictVisible = wrapBangladeshDistrict && wrapBangladeshDistrict.style.display !== 'none';
                if (ad.city && !bdDistrictVisible && checkoutCity) checkoutCity.value = ad.city;
                syncCityValue();
                fetchShippingOptions();
            } catch (e) {}
        });
    }

    if (checkoutCountry) checkoutCountry.addEventListener('change', function() { toggleCountryFields(); fetchShippingOptions(); });
    if (checkoutDivisionBd) {
        checkoutDivisionBd.addEventListener('change', function() {
            var selectedDivisionOpt = checkoutDivisionBd.options[checkoutDivisionBd.selectedIndex];
            var divId = selectedDivisionOpt ? selectedDivisionOpt.dataset.id : '';
            fillDistrictOptions(divId);
            if (checkoutThanaBd) checkoutThanaBd.innerHTML = '<option value="">@lang('Select district first')</option>';
            syncCityValue();
            fetchShippingOptions();
        });
    }
    if (checkoutDistrictBd) checkoutDistrictBd.addEventListener('change', function() { syncCityValue(); fillThanaOptions(checkoutDistrictBd.value); fetchShippingOptions(); });
    if (checkoutThanaBd) checkoutThanaBd.addEventListener('change', function() { updateZipFromThana(); fetchShippingOptions(); });
    if (checkoutCity) {
        checkoutCity.addEventListener('input', syncCityValue);
        checkoutCity.addEventListener('change', function() { syncCityValue(); fetchShippingOptions(); });
    }
    if (checkoutState) checkoutState.addEventListener('change', fetchShippingOptions);

    if (shippingMethodSelect) shippingMethodSelect.addEventListener('change', updateTotals);
    paymentTypeInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            fetchShippingOptions();
            updateTotals();
        });
    });

    checkoutForm.addEventListener('submit', function(e) {
        syncCityValue();
        if (shippingMethodSelect && !shippingMethodSelect.value) {
            e.preventDefault();
            alert('{{ __("Please set country and area, then select a delivery option.") }}');
        }
    });

    checkoutForm.querySelectorAll('.payment-option-card').forEach(function(card) {
        card.addEventListener('click', function() {
            var radio = card.querySelector('input[type="radio"]');
            if (!radio) return;
            radio.checked = true;
            radio.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    toggleCountryFields();
    if (isBangladesh() && checkoutDivisionBd) {
        var initialOpt = checkoutDivisionBd.options[checkoutDivisionBd.selectedIndex];
        var initialDivId = initialOpt ? initialOpt.dataset.id : '';
        if (initialDivId) fillDistrictOptions(initialDivId);
    }

    if (shippingMethodSelect && shippingMethodSelect.value) updateTotals();
    else fetchShippingOptions();

    if (navigator.geolocation && navigator.geolocation.getCurrentPosition) {
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                if (checkoutDeviceLat) checkoutDeviceLat.value = pos.coords.latitude;
                if (checkoutDeviceLng) checkoutDeviceLng.value = pos.coords.longitude;
            },
            function() {},
            { enableHighAccuracy: false, timeout: 5000, maximumAge: 300000 }
        );
    }

    var countdown = document.getElementById('checkoutCountdown');
    var endTime = Date.now() + 24 * 60 * 60 * 1000;
    function updateCountdown() {
        var now = Date.now();
        var diff = endTime - now;
        if (diff <= 0) {
            endTime = Date.now() + 24 * 60 * 60 * 1000;
            diff = endTime - now;
        }
        var h = Math.floor(diff / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);
        if (countdown) {
            var hrs = countdown.querySelector('.countdown-hrs');
            var mins = countdown.querySelector('.countdown-mins');
            var secs = countdown.querySelector('.countdown-secs');
            if (hrs) hrs.textContent = ('0' + h).slice(-2);
            if (mins) mins.textContent = ('0' + m).slice(-2);
            if (secs) secs.textContent = ('0' + s).slice(-2);
        }
    }
    if (countdown) { updateCountdown(); setInterval(updateCountdown, 1000); }

    function updateOfferTimerBars() {
        document.querySelectorAll('.offer-timer-bar[data-end-ts]').forEach(function(bar) {
            var endTs = parseInt(bar.getAttribute('data-end-ts') || '0', 10);
            var d = endTs - Date.now();
            var wrap = bar.querySelector('.offer-timer-bar__countdown');
            if (!wrap) return;
            var hEl = wrap.querySelector('.countdown-hours');
            var mEl = wrap.querySelector('.countdown-mins');
            var sEl = wrap.querySelector('.countdown-secs');
            if (d <= 0) {
                if (hEl) hEl.textContent = '00';
                if (mEl) mEl.textContent = '00';
                if (sEl) sEl.textContent = '00';
                return;
            }
            var h = Math.floor(d / 3600000);
            var m = Math.floor((d % 3600000) / 60000);
            var s = Math.floor((d % 60000) / 1000);
            if (hEl) hEl.textContent = ('0' + h).slice(-2);
            if (mEl) mEl.textContent = ('0' + m).slice(-2);
            if (sEl) sEl.textContent = ('0' + s).slice(-2);
        });
    }
    updateOfferTimerBars();
    setInterval(updateOfferTimerBars, 1000);
})();
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.indexOf('checkout') !== -1) {
        document.documentElement.style.overflowX = 'hidden';
        document.body.style.overflowX = 'hidden';
    }
});
</script>
@endpush
