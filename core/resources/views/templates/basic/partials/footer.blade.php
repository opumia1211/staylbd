@php
    $footerData   = getCachedFooterData();
    $contactContent = $footerData['contact'] ?? null;
    $footerElement  = $footerData['footer_element'] ?? collect();
    $footerContent  = $footerData['footer_content'] ?? null;
    $socialElement  = $footerData['social_element'] ?? collect();
    $policyPages    = $footerData['policy_pages'] ?? collect();
    $services       = $footerData['services'] ?? collect();
    $cookieData     = $footerData['cookie_data'] ?? null;
    $companyInfo    = $footerData['footer_company_info'] ?? null;
    $quickLinks     = $footerData['footer_quick_links'] ?? collect();
    $supportCenter  = $footerData['footer_support_center'] ?? null;
    $securityBadges = $footerData['footer_security_badges'] ?? collect();
    $shippingPayment = $footerData['footer_shipping_payment'] ?? null;
    $appPromotion   = $footerData['footer_app_promotion'] ?? null;
    $appPromotionItems = $footerData['footer_app_promotion_items'] ?? collect();
    $returnPolicy   = $footerData['footer_return_policy'] ?? null;

    $showCookiePrefs = $cookieData && (($cookieData->data_values->status ?? 0) == \App\Constants\Status::ENABLE) && (($cookieData->data_values->show_preferences_link ?? 1) != 0);
    $cookiePrefsText = $cookieData ? ($cookieData->data_values->preferences_link_text ?? __('Cookie Preferences')) : __('Cookie Preferences');
    $subscribeTitle = $footerContent && isset($footerContent->data_values->subscribe_title) ? $footerContent->data_values->subscribe_title : __('Subscribe to our newsletter');
    $subscribeSubtitle = $footerContent && isset($footerContent->data_values->subscribe_subtitle) ? $footerContent->data_values->subscribe_subtitle : __('Subscribe for new Offers and updates');
    $connectTitle   = $footerContent && isset($footerContent->data_values->connect_title) ? $footerContent->data_values->connect_title : __('Find Us');

    $showCompanyBlock = $companyInfo && ($companyInfo->data_values->show ?? 1);
    $supportEnabled = !$supportCenter || ($supportCenter->data_values->enabled ?? 1);
    $showPaymentIcons = !$shippingPayment || ($shippingPayment->data_values->show_payment_icons ?? 1);
    $showShippingInfo = $shippingPayment && ($shippingPayment->data_values->show_shipping_info ?? 1);
    $appPromoEnabled = $appPromotion && ($appPromotion->data_values->enabled ?? 0);
    $showReturnForm = $returnPolicy && ($returnPolicy->data_values->show_form ?? 1);
    $returnFormTitle = $returnPolicy && isset($returnPolicy->data_values->form_title) ? $returnPolicy->data_values->form_title : __('Product Return Request');

    $fcv = $footerContent && is_object($footerContent->data_values ?? null) ? $footerContent->data_values : (object)[];
    $sellerAccountFeatureOn = (int)($fcv->seller_account_enabled ?? 0) === 1;
    $sellerUrlRaw = trim((string)($fcv->seller_account_url ?? ''));
    if ($sellerAccountFeatureOn) {
        if ($sellerUrlRaw !== '') {
            $sellerAccountHref = \Illuminate\Support\Str::startsWith($sellerUrlRaw, ['http://', 'https://'])
                ? $sellerUrlRaw
                : url('/' . ltrim($sellerUrlRaw, '/'));
        } else {
            $sellerAccountHref = route('seller.apply');
        }
    } else {
        $sellerAccountHref = route('contact.live') . '?open_contact=1';
    }
    $sellerLinkNewTab = $sellerAccountFeatureOn && $sellerUrlRaw !== '' && \Illuminate\Support\Str::startsWith($sellerUrlRaw, ['http://', 'https://']);

    $showPromoFeaturesBar = $services->isNotEmpty();
    $customButtonsAll = \App\Models\Frontend::where('data_keys', 'custom_buttons.element')->orderBy('id', 'asc')->get();
    $footerCustomButtons = $customButtonsAll->filter(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (($dv['target'] ?? '') === 'footer') && ((int) ($dv['is_active'] ?? 1) === 1);
    })->sortBy(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (int) ($dv['display_order'] ?? 0);
    })->values();
    $footerTopButtons = $footerCustomButtons->filter(fn($r) => (($r->data_values->position ?? '') === 'top'));
    $footerBottomButtons = $footerCustomButtons->filter(fn($r) => (($r->data_values->position ?? '') === 'bottom'));
@endphp

<footer class="site-footer footer-glass footer-glass--premium" role="contentinfo">
    {{-- Width/padding: footer-glass.css uses same :root tokens as header (.glass-header__shell) + main (.main-container) --}}
    <div class="footer-glass__inner">
        <div class="footer-glass__card w-full">
        @if($footerTopButtons->isNotEmpty())
        <div class="d-flex flex-wrap gap-2 px-2 pt-2">
            @foreach($footerTopButtons as $btn)
                @php $b = (array)($btn->data_values ?? []); $href = trim((string)($b['button_url'] ?? '#')) ?: '#'; @endphp
                <a href="{{ $href }}" class="btn btn-sm btn-outline-light d-inline-flex align-items-center">
                    @if(!empty($b['icon_image']))
                        <img src="{{ asset('assets/images/frontend/custom_buttons/' . $b['icon_image']) }}" alt="{{ $b['button_text'] ?? 'Button' }}" width="16" height="16" loading="lazy" class="me-1">
                    @else
                        @include($activeTemplate . 'partials.icon', ['name' => ($b['icon_name'] ?? 'circle'), 'class' => 'me-1'])
                    @endif
                    <span>{{ $b['button_text'] ?? __('Button') }}</span>
                </a>
            @endforeach
        </div>
        @endif
        @if($showPromoFeaturesBar)
        <div class="promo-features-bar footer-glass__promo-strip" aria-label="@lang('Highlights')">
            <div class="w-full">
                <div class="promo-features-grid grid">
                    @foreach ($services as $service)
                        @php
                            $dv = $service->data_values ?? (object)[];
                            $url = $dv->url ?? null;
                            $href = $url ? e($url) : '#';
                            $img = $dv->image ?? null;
                        @endphp
                        <a href="{{ $href }}" class="promo-feature-card"{{ $url ? '' : ' aria-disabled="true"' }}>
                            @if($img)
                                <div class="promo-feature-icon">
                                    <img src="{{ getImage('assets/images/frontend/service/' . $img, '50x50') }}" alt="{{ __($dv->title ?? '') }}" loading="lazy" width="40" height="40">
                                </div>
                            @endif
                            <div class="promo-feature-content">
                                <h6 class="promo-feature-title">{{ __($dv->title ?? '') }}</h6>
                                <p class="promo-feature-desc">{{ __($dv->short_detail ?? '') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        <div class="footer-bottom">
            <div class="footer__wrapper footer-grid flex flex-wrap">

                @if($showCompanyBlock && ($companyInfo->data_values->about_text ?? $companyInfo->data_values->mission_text ?? $companyInfo->data_values->registration_info ?? ''))
                <div class="footer__bottom__widget footer-about-widget">
                    <h6 class="title footer-col-title">@lang('About Us')</h6>
                    @if(!empty($companyInfo->data_values->about_text))
                        <p class="mb-0 text-white small">{{ __($companyInfo->data_values->about_text) }}</p>
                    @endif
                    @if(!empty($companyInfo->data_values->mission_text))
                        <p class="mb-0 text-white small">{{ __($companyInfo->data_values->mission_text) }}</p>
                    @endif
                    @if(!empty($companyInfo->data_values->registration_info))
                        <p class="mb-0 small text-white-50">{{ __($companyInfo->data_values->registration_info) }}</p>
                    @endif
                    @if(!empty($companyInfo->data_values->business_license))
                        <p class="mb-0 small text-white-50">{{ __($companyInfo->data_values->business_license) }}</p>
                    @endif
                </div>
                @endif

                {{-- Quick Links, Support, Security: same grid row as other columns (no full-width wrapper — avoids empty gap beside About) --}}
                @if($quickLinks->isNotEmpty())
                @php
                    $validQuickLinks = $quickLinks->filter(function ($link) {
                        $dv = $link->data_values ?? (object)[];
                        $t = trim((string)($dv->title ?? ''));
                        return $t !== '' && strlen($t) >= 3 && preg_match('/[\p{L}\s]/u', $t);
                    });
                @endphp
                @if($validQuickLinks->isNotEmpty())
                <div class="footer__bottom__widget footer-quick-links-widget">
                    <h6 class="title footer-col-title">@lang('Quick Links')</h6>
                    <ul class="list-unstyled mb-0" role="list">
                        @foreach ($validQuickLinks as $link)
                            @php $dv = $link->data_values ?? (object)[]; $u = $dv->url ?? '#'; @endphp
                            <li role="listitem"><a href="{{ $u }}" class="text-white" @if($u !== '#') target="_blank" rel="noopener noreferrer" @endif>{{ __($dv->title ?? '') }}</a></li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @endif

                @if($supportEnabled)
                <div class="footer__bottom__widget footer-support-widget">
                    <h6 class="title footer-col-title">@lang('Support')</h6>
                    <ul class="list-unstyled mb-0" role="list">
                        @php $sc = $supportCenter ? ($supportCenter->data_values ?? (object)[]) : (object)[]; @endphp
                        @if(!empty($sc->help_center_url))
                            <li role="listitem"><a href="{{ $sc->help_center_url }}" class="text-white" target="_blank" rel="noopener noreferrer">@lang('Help Center')</a></li>
                        @endif
                        @if(!empty($sc->return_policy_url))
                            <li role="listitem"><a href="{{ $sc->return_policy_url }}" class="text-white">@lang('Return Policy')</a></li>
                        @endif
                        @if(!empty($sc->refund_policy_url))
                            <li role="listitem"><a href="{{ $sc->refund_policy_url }}" class="text-white">@lang('Refund Policy')</a></li>
                        @endif
                        <li role="listitem"><a href="{{ !empty($sc->track_order_url) ? $sc->track_order_url : route('track.order') }}" class="text-white">@lang('Track Order')</a></li>
                        @if(($sc->support_ticket_enabled ?? 1) && route('message.open', [], false))
                            <li role="listitem"><a href="{{ route('message.open') }}" class="text-white">@lang('Support Ticket')</a></li>
                        @endif
                        @if(!empty($sc->support_email))
                            <li role="listitem"><a href="mailto:{{ $sc->support_email }}" class="text-white">@lang('Contact Support')</a></li>
                        @endif
                    </ul>
                </div>
                @endif

                @if($securityBadges->isNotEmpty())
                <div class="footer__bottom__widget footer-security-widget">
                    <h6 class="title footer-col-title">@lang('Trust & Security')</h6>
                    <div class="d-flex flex-wrap gap-2 align-items-center" role="list">
                        @foreach ($securityBadges as $badge)
                            @php $dv = $badge->data_values ?? (object)[]; $img = $dv->image ?? null; $u = $dv->url ?? '#'; @endphp
                            @if($img)
                                <span role="listitem">
                                    @if($u && $u !== '#')
                                        <a href="{{ $u }}" target="_blank" rel="noopener noreferrer" title="{{ __($dv->title ?? '') }}">
                                            <img src="{{ getImage('assets/images/frontend/footer/' . $img, '80x80') }}" alt="{{ __($dv->title ?? '') }}" loading="lazy" width="50" height="50">
                                        </a>
                                    @else
                                        <img src="{{ getImage('assets/images/frontend/footer/' . $img, '80x80') }}" alt="{{ __($dv->title ?? '') }}" loading="lazy" width="50" height="50">
                                    @endif
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @if($showReturnForm)
                <div class="footer__bottom__widget footer-widget-return">
                    <h6 class="title footer-col-title">{{ __($returnFormTitle) }}</h6>
                    <div class="footer-return-form-wrap">
                        <form class="js-footer-return-form" action="{{ route('footer.return.submit') }}" method="post">
                            @csrf
                            <div class="row g-0">
                                <div class="col-12"><input type="text" name="name" class="form-control form-control-sm" placeholder="{{ __('Name') }}" required></div>
                                <div class="col-12"><input type="email" name="email" class="form-control form-control-sm" placeholder="{{ __('Email') }}" required></div>
                                <div class="col-12"><input type="text" name="order_number" class="form-control form-control-sm" placeholder="{{ __('Order number (optional)') }}"></div>
                                <div class="col-12"><input type="text" name="reason" class="form-control form-control-sm" placeholder="{{ __('Reason for return (optional)') }}"></div>
                                <div class="col-12"><textarea name="message" class="form-control form-control-sm" rows="1" placeholder="{{ __('Message') }}" required></textarea></div>
                                <div class="col-12"><button type="submit" class="btn btn--primary btn-sm w-100">@lang('Submit Request')</button></div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                @if($showPaymentIcons && $footerElement->isNotEmpty())
                <div class="footer__bottom__widget footer-payment-methods font-sans">
                    <h6 class="title footer-col-title font-sans">@lang('Payment Methods')</h6>
                    <div class="footer-payment-grid grid w-full max-w-full grid-cols-3 gap-px leading-none" role="list">
                        @foreach ($footerElement as $footer)
                            @php
                                $img = $footer->data_values->image ?? null;
                                $payUrl = trim($footer->data_values->url ?? '');
                                $payTitle = $footer->data_values->title ?? '';
                            @endphp
                            @if($img)
                                <div class="footer-payment-item min-h-0 min-w-0 p-0" role="listitem">
                                    @if($payUrl !== '')
                                        <a href="{{ $payUrl }}" target="_blank" rel="noopener noreferrer" class="pay-img pay-img-link footer-payment-tile flex w-full items-center justify-center border-0 bg-transparent p-0 shadow-none outline-none ring-0 focus:outline-none focus-visible:ring-0" title="{{ __($payTitle) ?: __('Payment method') }}">
                                            <img src="{{ getImage('assets/images/frontend/footer/' . $img) }}" alt="{{ __($payTitle) ?: __('Payment method') }}" class="block h-auto w-full max-w-full border-0 object-contain object-center outline-none" width="280" height="100" loading="lazy" decoding="async" fetchpriority="low" sizes="(max-width: 480px) 33vw, 140px">
                                        </a>
                                    @else
                                        <span class="pay-img footer-payment-tile flex w-full items-center justify-center border-0 bg-transparent p-0 shadow-none">
                                            <img src="{{ getImage('assets/images/frontend/footer/' . $img) }}" alt="{{ __($payTitle) ?: __('Payment method') }}" class="block h-auto w-full max-w-full border-0 object-contain object-center outline-none" width="280" height="100" loading="lazy" decoding="async" fetchpriority="low" sizes="(max-width: 480px) 33vw, 140px">
                                        </span>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="footer__bottom__widget footer-newsletter-widget">
                    <style>
                        .custom-newsletter-heading {
                            font-family: 'Inter', sans-serif;
                            font-weight: 700;
                            color: #0f172a;
                            font-size: 15px;
                            margin-bottom: 4px;
                            letter-spacing: -0.01em;
                        }
                        .custom-newsletter-subtitle {
                            font-family: 'Inter', sans-serif;
                            font-size: 13px;
                            color: #475569;
                            margin-bottom: 12px;
                            line-height: 1.45;
                        }
                        .custom-newsletter-input-group {
                            display: flex;
                            align-items: stretch;
                            background: rgba(255, 255, 255, 0.95);
                            border-radius: 8px;
                            border: 1px solid rgba(148, 163, 184, 0.4);
                            overflow: hidden;
                            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02), 0 2px 8px rgba(0,0,0,0.04);
                            transition: all 0.25s ease;
                            position: relative;
                            z-index: 30;
                            height: 42px;
                            width: 100%;
                        }
                        .custom-newsletter-input-group:focus-within {
                            border-color: #3b82f6;
                            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
                        }
                        .custom-newsletter-icon {
                            padding: 0 6px 0 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: #64748b;
                            font-size: 15px;
                            pointer-events: none;
                        }
                        .custom-newsletter-input {
                            flex: 1 1 auto;
                            min-width: 50px;
                            width: 100%;
                            border: none !important;
                            background: transparent !important;
                            padding: 0 10px 0 2px;
                            font-family: 'Inter', sans-serif;
                            font-size: 14px !important;
                            color: #0f172a !important;
                            -webkit-text-fill-color: #0f172a !important;
                            font-weight: 500;
                            outline: none !important;
                            box-shadow: none !important;
                            caret-color: #3b82f6 !important;
                            height: 100%;
                            pointer-events: auto;
                            z-index: 30;
                        }
                        .custom-newsletter-input::placeholder {
                            color: #94a3b8 !important;
                            -webkit-text-fill-color: #94a3b8 !important;
                            font-weight: 400;
                        }
                        .custom-newsletter-btn {
                            flex: 0 0 48px !important;
                            width: 48px !important;
                            border: none;
                            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                            color: #ffffff;
                            padding: 0 !important;
                            font-size: 15px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            cursor: pointer;
                            transition: all 0.2s ease;
                            margin: 0;
                            height: 100%;
                            pointer-events: auto;
                            z-index: 30;
                        }
                        .custom-newsletter-btn:hover {
                            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
                        }
                        .custom-newsletter-btn:active {
                            transform: scale(0.95);
                        }
                    </style>
                    <div class="footer-newsletter-card" style="padding: 0; background: transparent; border: none; box-shadow: none;">
                        <h6 class="custom-newsletter-heading footer-col-title">@lang('Subscribe Newsletter')</h6>
                        @php $subT = $subscribeSubtitle ? __($subscribeSubtitle) : ''; $mainT = __($subscribeTitle); @endphp
                        @if($subT && $subT !== $mainT)
                            <p class="custom-newsletter-subtitle">{{ $mainT }} — {{ $subT }}</p>
                        @endif
                        <form class="newletter-form js-footer-subscribe w-full mt-2" action="{{ route('subscribe') }}" method="post" aria-label="@lang('Newsletter subscription')">
                            @csrf
                            <div class="custom-newsletter-input-group">
                                <div class="custom-newsletter-icon">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'envelope'])
                                </div>
                                <input type="email" name="email" class="custom-newsletter-input" placeholder="@lang('Enter Your Email')" required aria-label="@lang('Email address')" autocomplete="email">
                                <button type="submit" class="custom-newsletter-btn" aria-label="@lang('Subscribe')">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'paper-plane'])
                                </button>
                            </div>
                            <div class="subscribe-inline-message mt-2" style="font-size: 12px; font-weight: 500; color: #10b981;" aria-live="polite"></div>
                        </form>
                        @guest
                        <div class="footer-glass__auth-actions footer-glass__auth-actions--newsletter" style="margin-top: 16px; padding-top: 16px;" role="navigation" aria-label="@lang('Account')">
                            <a href="{{ route('user.login') }}?open=login&redirect={{ urlencode(url()->current()) }}" class="footer-glass__btn footer-glass__btn--outline footer-glass__btn--compact js-footer-floating-login">@lang('Login')</a>
                            <a href="{{ route('user.register') }}?open=register&redirect={{ urlencode(url()->current()) }}" class="footer-glass__btn footer-glass__btn--primary footer-glass__btn--compact js-footer-floating-register">@lang('Registration')</a>
                            <a href="{{ $sellerAccountHref }}" class="footer-glass__btn footer-glass__btn--outline footer-glass__btn--compact" title="{{ $sellerAccountFeatureOn ? __('Seller registration') : __('Contact us about seller account') }}"@if($sellerLinkNewTab) target="_blank" rel="noopener noreferrer"@endif>@lang('Seller account')</a>
                        </div>
                        @else
                        <div class="footer-glass__auth-actions footer-glass__auth-actions--newsletter" style="margin-top: 16px; padding-top: 16px;" role="navigation" aria-label="@lang('Account')">
                            <a href="{{ route('user.home') }}" class="footer-glass__btn footer-glass__btn--primary footer-glass__btn--compact">@lang('My account')</a>
                            <a href="{{ $sellerAccountHref }}" class="footer-glass__btn footer-glass__btn--outline footer-glass__btn--compact" title="{{ $sellerAccountFeatureOn ? __('Seller account') : __('Contact us about seller account') }}"@if($sellerLinkNewTab) target="_blank" rel="noopener noreferrer"@endif>@lang('Seller account')</a>
                        </div>
                        @endguest
                    @if($showShippingInfo && $shippingPayment)
                    <div class="footer-shipping-info mt-3 pt-3">
                        <h6 class="custom-newsletter-heading footer-col-title mb-1.5" style="font-size: 14px;">@lang('Payment & Shipping')</h6>
                        @if(($shippingPayment->data_values->cod_enabled ?? 1) == 1)
                            <p class="mb-1 text-xs text-slate-500 font-medium flex items-center"><span class="w-1.5 h-1.5 rounded-full mr-2" style="background:#10b981;display:inline-block;width:6px;height:6px;border-radius:50%;margin-right:6px;"></span>@lang('Cash on Delivery available')</p>
                        @endif
                        @if(!empty($shippingPayment->data_values->estimated_delivery_text))
                            <p class="mb-1 text-xs text-slate-500 font-medium flex items-center">@include($activeTemplate . 'partials.icon', ['name' => 'truck', 'style' => 'margin-right:6px;']) @lang('Delivery'): {{ __($shippingPayment->data_values->estimated_delivery_text) }}</p>
                        @endif
                        @if(!empty($shippingPayment->data_values->shipping_partners_text))
                            <p class="mb-1 text-xs text-slate-500 font-medium">{{ __($shippingPayment->data_values->shipping_partners_text) }}</p>
                        @endif
                        @if(!empty($shippingPayment->data_values->delivery_zones_text))
                            <p class="mb-0 text-xs text-slate-500 font-medium">{{ __($shippingPayment->data_values->delivery_zones_text) }}</p>
                        @endif
                    </div>
                    @endif
                    </div>
                </div>

                <div class="footer__bottom__widget footer-connect-widget footer-right-col">
                    <p class="footer-connect-text mb-1 small text-white">{{ __($connectTitle) }}</p>
                    <div class="footer-connect-inner footer-social-first">
                        <ul class="social-icons footer-social-grid stayl-footer-social-grid flex flex-wrap items-center justify-start gap-2.5 list-none m-0 p-0" role="list">
                        @if($contactContent && !empty($contactContent->data_values->contact_email))
                            <li class="m-0 shrink-0" role="listitem">
                                <a class="stayl-footer-social-link" href="mailto:{{ $contactContent->data_values->contact_email }}" aria-label="@lang('Email')">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'envelope'])
                                </a>
                            </li>
                        @endif
                        @foreach ($socialElement as $social)
                            @php
                                $feDv = $social->data_values ?? null;
                                $feDv = is_array($feDv) ? (object) $feDv : ($feDv ?? (object) []);
                                $sUrl = trim((string) ($feDv->url ?? ''));
                                $sUrl = $sUrl !== '' ? $sUrl : '#';
                                $customIcon = trim((string) ($feDv->custom_icon ?? ''));
                                if ($customIcon === '0' || strtolower($customIcon) === 'null') {
                                    $customIcon = '';
                                }
                                $customIconRel = $customIcon !== '' && preg_match('#^[a-zA-Z0-9._-]+$#', $customIcon)
                                    ? 'assets/images/frontend/social_icon/' . $customIcon
                                    : '';
                                $customIconAbs = $customIconRel !== '' && function_exists('public_path')
                                    ? public_path($customIconRel)
                                    : '';
                                $useCustomImg = $customIconAbs !== '' && is_file($customIconAbs);
                                $inlineCustom = trim((string) ($feDv->custom_icon_svg ?? ''));
                                $iconStored = trim((string) ($feDv->icon ?? ''));
                                $iconClassAttr = '';
                                if ($iconStored !== '') {
                                    if (preg_match('/<i\b[^>]*\bclass\s*=\s*(["\'])([^"\']*)\1/i', $iconStored, $im)) {
                                        $iconClassAttr = trim(preg_replace('/\s+/', ' ', html_entity_decode($im[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                                    } elseif (preg_match('/<i\b[^>]*\bclass\s*=\s*([^\s>]+)/i', $iconStored, $im)) {
                                        $iconClassAttr = trim(preg_replace('/\s+/', ' ', html_entity_decode($im[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                                    } else {
                                        $iconClassAttr = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($iconStored, ENT_QUOTES | ENT_HTML5, 'UTF-8'))));
                                    }
                                }
                                $iconClassSafe = ($iconClassAttr !== '' && preg_match('#^[a-zA-Z0-9 _\-.]+$#u', $iconClassAttr)) ? $iconClassAttr : '';
                                $iconRawLower = strtolower($iconClassSafe);
                                $useLibraryIcon = $iconClassSafe !== '' && (
                                    preg_match('/\b(fab|far|fas|fa-brands|fa-solid|fa-regular|lab|lar|las|lal)\b/', $iconRawLower)
                                    || str_contains($iconRawLower, 'fa-')
                                    || str_contains($iconRawLower, 'la-')
                                );
                                $socialLabel = trim((string) ($feDv->title ?? ''));
                                $socialIconName = match (true) {
                                    $iconRawLower === '' => 'link',
                                    str_contains($iconRawLower, 'facebook') => 'facebook',
                                    str_contains($iconRawLower, 'instagram') => 'instagram',
                                    str_contains($iconRawLower, 'youtube') => 'youtube',
                                    str_contains($iconRawLower, 'linkedin') => 'linkedin',
                                    str_contains($iconRawLower, 'whatsapp') => 'whatsapp',
                                    str_contains($iconRawLower, 'telegram') => 'telegram',
                                    str_contains($iconRawLower, 'pinterest') => 'pinterest',
                                    str_contains($iconRawLower, 'tiktok') => 'tiktok',
                                    str_contains($iconRawLower, 'github') => 'github',
                                    str_contains($iconRawLower, 'discord') => 'discord',
                                    str_contains($iconRawLower, 'reddit') => 'reddit',
                                    str_contains($iconRawLower, 'spotify') => 'spotify',
                                    str_contains($iconRawLower, 'snapchat'), str_contains($iconRawLower, 'threads') => 'link',
                                    str_contains($iconRawLower, 'twitter'), str_contains($iconRawLower, 'x-twitter'), str_contains($iconRawLower, 'x.com') => 'x-twitter',
                                    str_contains($iconRawLower, 'envelope') => 'envelope',
                                    default => 'link',
                                };
                            @endphp
                            <li class="m-0 shrink-0" role="listitem">
                                <a class="stayl-footer-social-link" href="{{ $sUrl }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $socialLabel !== '' ? $socialLabel : __('Social link') }}">
                                    @if($inlineCustom !== '')
                                        <span class="stayl-footer-social-inline-wrap inline-flex items-center justify-center shrink-0">{!! $inlineCustom !!}</span>
                                    @elseif($useCustomImg)
                                        <img src="{{ getImage($customIconRel, '96x96') }}" alt="" class="stayl-footer-social-img object-contain" width="22" height="22" loading="lazy" decoding="async">
                                    @elseif($useLibraryIcon)
                                        <span class="stayl-footer-social-inline-wrap inline-flex items-center justify-center shrink-0 text-lg leading-none" aria-hidden="true"><i class="{{ e($iconClassSafe) }}"></i></span>
                                    @elseif(str_contains($iconStored, '<i ') && $socialIconName === 'link')
                                        <span class="stayl-footer-social-inline-wrap inline-flex items-center justify-center shrink-0">{!! $iconStored !!}</span>
                                    @else
                                        @include($activeTemplate . 'partials.icon', ['name' => $socialIconName])
                                    @endif
                                </a>
                            </li>
                        @endforeach
                        </ul>
                    </div>
                    @if($appPromoEnabled)
                    <div class="footer-app-qr-inner footer-app-qr-block border-top border-white border-opacity-25">
                        <h6 class="title footer-col-title mb-2">@lang('Get our app')</h6>
                        @if($appPromotionItems->isNotEmpty())
                            <div class="footer-app-grid">
                            @foreach($appPromotionItems as $appItem)
                                @php
                                    $adv = $appItem->data_values ?? (object)[];
                                    if (is_array($adv)) $adv = (object)$adv;
                                    $platform = trim($adv->platform ?? $adv->title ?? '');
                                    $name = $adv->name ?? $adv->title ?? '';
                                    $link = $adv->link ?? $adv->android_url ?? $adv->ios_url ?? '';
                                    $appFile = $adv->app_file ?? null;
                                    $img = $adv->image ?? $adv->qr_image ?? null;
                                    $label = $platform ?: $name ?: __('Download');
                                    $platformKey = strtolower($platform);
                                    $platformIcon = match(true) {
                                        str_contains($platformKey, 'android') => 'android',
                                        in_array($platformKey, ['ios', 'apple', 'mac']) => 'apple',
                                        str_contains($platformKey, 'windows') => 'windows',
                                        str_contains($platformKey, 'mac') => 'apple',
                                        str_contains($platformKey, 'desktop') => 'desktop',
                                        default => 'mobile-alt',
                                    };
                                    $platformLogoUrl = getPlatformLogoUrl($platform);
                                    $downloadUrl = $appFile ? asset('assets/files/frontend/apps/' . $appFile) : null;
                                @endphp
                                <div class="footer-app-card">
                                    @if($img)
                                        <img src="{{ getImage('assets/images/frontend/footer/' . $img) }}" alt="{{ $name ?: $platform }}" loading="lazy" width="28" height="28" class="footer-app-card__logo rounded">
                                    @elseif($platformLogoUrl)
                                        <img src="{{ $platformLogoUrl }}" alt="{{ $label }}" loading="lazy" width="28" height="28" class="footer-app-card__logo rounded">
                                    @else
                                        <span class="footer-app-card__icon">@include($activeTemplate . 'partials.icon', ['name' => $platformIcon])</span>
                                    @endif
                                    @if($downloadUrl)
                                        <a href="{{ $downloadUrl }}" download class="footer-app-card__btn">{{ __($label) }} @include($activeTemplate . 'partials.icon', ['name' => 'download', 'class' => 'ms-1'])</a>
                                    @elseif($link)
                                        <a href="{{ $link }}" target="_blank" rel="noopener noreferrer" class="footer-app-card__btn">{{ __($label) }}</a>
                                    @else
                                        <span class="footer-app-card__label">{{ __($label) }}</span>
                                    @endif
                                </div>
                            @endforeach
                            </div>
                        @else
                            <div class="footer-app-grid d-flex flex-wrap gap-2">
                                @if(!empty($appPromotion->data_values->qr_image))
                                    <img src="{{ getImage('assets/images/frontend/footer/' . $appPromotion->data_values->qr_image) }}" alt="@lang('QR Code')" width="48" height="48" loading="lazy" class="footer-qr-img rounded">
                                @endif
                                @if(!empty($appPromotion->data_values->android_url))
                                    <a href="{{ $appPromotion->data_values->android_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light footer-app-btn">@include($activeTemplate . 'partials.icon', ['name' => 'android', 'class' => 'me-1']) @lang('Android')</a>
                                @endif
                                @if(!empty($appPromotion->data_values->ios_url))
                                    <a href="{{ $appPromotion->data_values->ios_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light footer-app-btn">@include($activeTemplate . 'partials.icon', ['name' => 'apple', 'class' => 'me-1']) @lang('iOS')</a>
                                @endif
                            </div>
                        @endif
                        <div class="footer-app-info footer-address-block">
                            @if($contactContent && !empty($contactContent->data_values->address))
                                <p class="mb-0">@include($activeTemplate . 'partials.icon', ['name' => 'map-marker-alt', 'class' => 'me-1']){{ __($contactContent->data_values->address) }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="footer-app-info footer-address-block mt-1 pt-1 border-top border-white border-opacity-25">
                        @if($contactContent && !empty($contactContent->data_values->address))
                            <p class="mb-0">@include($activeTemplate . 'partials.icon', ['name' => 'map-marker-alt', 'class' => 'me-1']){{ __($contactContent->data_values->address) }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
        </div>
    </div>
    <div class="footer-glass__copyright">
        <div class="footer-glass__copyright-inner">
            <div class="copyright-area footer-glass__copyright-row justify-content-between align-items-center flex flex-wrap">
                <div class="copyright d-flex flex-wrap align-items-center {{ getLogoEffectClasses() }}">
                    @php $footerLogo = getLogo('logo') ?: getLogo('logo_dark'); @endphp
                    @if($footerLogo)
                        <a href="{{ route('home') }}" class="me-3" title="@lang('Home')">
                            <img src="{{ $footerLogo }}" alt="{{ gs('site_name') }}" class="footer-logo site-logo-img" style="height: {{ getFooterLogoHeight() }}px; {{ getLogoStyle() }}" loading="lazy" width="120" height="{{ getFooterLogoHeight() }}">
                        </a>
                    @endif
                    @php
                        $copyrightText = $footerContent && trim($footerContent->data_values->copyright_text ?? '') !== ''
                            ? $footerContent->data_values->copyright_text
                            : __('Copyright') . ' © ' . date('Y') . ' ' . __('All Right Reserved');
                        $copyrightText = str_replace('{year}', date('Y'), $copyrightText);
                        @endphp
                    <span class="footer-glass__copy-text">{{ $copyrightText }}</span>
                </div>
                <nav class="policy-page footer-glass__policy-nav" aria-label="@lang('Legal and policies')">
                    @foreach ($policyPages as $policy)
                        <a href="{{ route('policy.pages.short', $policy->id) }}" class="footer-glass__policy-link">{{ __($policy->data_values->title ?? '') }}</a>
                    @endforeach
                    @if($showCookiePrefs)
                        <a href="{{ route('cookie.revoke') }}" class="footer-glass__policy-link">{{ __($cookiePrefsText) }}</a>
                    @endif
                </nav>
                @if($footerBottomButtons->isNotEmpty())
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @foreach($footerBottomButtons as $btn)
                        @php $b = (array)($btn->data_values ?? []); $href = trim((string)($b['button_url'] ?? '#')) ?: '#'; @endphp
                        <a href="{{ $href }}" class="btn btn-sm btn-outline-light d-inline-flex align-items-center">
                            @if(!empty($b['icon_image']))
                                <img src="{{ asset('assets/images/frontend/custom_buttons/' . $b['icon_image']) }}" alt="{{ $b['button_text'] ?? 'Button' }}" width="16" height="16" loading="lazy" class="me-1">
                            @else
                                @include($activeTemplate . 'partials.icon', ['name' => ($b['icon_name'] ?? 'circle'), 'class' => 'me-1'])
                            @endif
                            <span>{{ $b['button_text'] ?? __('Button') }}</span>
                        </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</footer>
@push('script')
<script>
(function() {
  document.addEventListener('click', function(e) {
    var loginLnk = e.target.closest('footer.site-footer .js-footer-floating-login');
    var regLnk = e.target.closest('footer.site-footer .js-footer-floating-register');
    if (!loginLnk && !regLnk) return;
    e.preventDefault();
    var href = (loginLnk || regLnk).getAttribute('href') || '';
    if (typeof window.openAuthModalInIframe === 'function') {
      window.openAuthModalInIframe((loginLnk || regLnk).href || href);
    } else {
      window.location.href = href;
    }
  });
})();

(function() {
  var footer = document.querySelector('footer.site-footer.footer-glass');
  if (!footer) return;
  var mq = window.matchMedia('(max-width: 767.98px)');
  var widgets = Array.prototype.slice.call(footer.querySelectorAll('.footer__bottom__widget'));

  function applyState() {
    widgets.forEach(function(widget, index) {
      var title = widget.querySelector('.footer-col-title');
      if (!title) return;
      var isNewsletter = widget.classList.contains('footer-newsletter-widget');
      if (mq.matches) {
        if (isNewsletter) {
          title.removeAttribute('role');
          title.removeAttribute('tabindex');
          widget.classList.add('is-open');
          return;
        }
        title.setAttribute('role', 'button');
        title.setAttribute('tabindex', '0');
        if (!widget.classList.contains('is-open') && index === 0) widget.classList.add('is-open');
      } else {
        title.removeAttribute('role');
        title.removeAttribute('tabindex');
        widget.classList.add('is-open');
      }
    });
  }

  function toggleWidget(widget) {
    if (!mq.matches) return;
    if (widget.classList.contains('footer-newsletter-widget')) return;
    var isOpen = widget.classList.contains('is-open');
    widgets.forEach(function(w) { w.classList.remove('is-open'); });
    if (!isOpen) widget.classList.add('is-open');
  }

  footer.addEventListener('click', function(e) {
    var title = e.target.closest('.footer-col-title');
    if (!title) return;
    var widget = title.closest('.footer__bottom__widget');
    if (!widget) return;
    toggleWidget(widget);
  });

  footer.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter' && e.key !== ' ') return;
    var title = e.target.closest('.footer-col-title');
    if (!title) return;
    e.preventDefault();
    var widget = title.closest('.footer__bottom__widget');
    if (!widget) return;
    toggleWidget(widget);
  });

  applyState();
  if (typeof mq.addEventListener === 'function') {
    mq.addEventListener('change', applyState);
  } else if (typeof mq.addListener === 'function') {
    mq.addListener(applyState);
  }
})();
</script>
@endpush
