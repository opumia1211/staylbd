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
    $subscribeSubtitle = $footerContent && isset($footerContent->data_values->subscribe_subtitle) ? $footerContent->data_values->subscribe_subtitle : __('Stay up to date with news and promotions by signing up for our newsletter');
    $connectTitle   = $footerContent && isset($footerContent->data_values->connect_title) ? $footerContent->data_values->connect_title : __('Find Us');

    $showCompanyBlock = $companyInfo && ($companyInfo->data_values->show ?? 1);
    $supportEnabled = !$supportCenter || ($supportCenter->data_values->enabled ?? 1);
    $showPaymentIcons = !$shippingPayment || ($shippingPayment->data_values->show_payment_icons ?? 1);
    $showShippingInfo = $shippingPayment && ($shippingPayment->data_values->show_shipping_info ?? 1);
    $appPromoEnabled = $appPromotion && ($appPromotion->data_values->enabled ?? 0);
    $showReturnForm = $returnPolicy && ($returnPolicy->data_values->show_form ?? 1);
    $returnFormTitle = $returnPolicy && isset($returnPolicy->data_values->form_title) ? $returnPolicy->data_values->form_title : __('Product Return Request');

    $fcv = $footerContent && is_object($footerContent->data_values ?? null) ? $footerContent->data_values : (object)[];
    $footerCompactMode = (int) ($fcv->footer_compact_mode ?? 1) === 1;
    $voteEnabled = (int) ($fcv->vote_enabled ?? 1) === 1;
    $voteScope = ($fcv->vote_scope ?? 'page') === 'global' ? 'global' : 'page';
    $voteTitle = trim((string) ($fcv->vote_title ?? __('Was this page helpful?')));
    $voteSubtitle = trim((string) ($fcv->vote_subtitle ?? __('Vote to help us improve your experience.')));
    $voteUpLabel = trim((string) ($fcv->vote_up_label ?? __('Helpful')));
    $voteDownLabel = trim((string) ($fcv->vote_down_label ?? __('Needs work')));
    $voteSlug = $voteScope === 'global' ? 'global' : ('page:' . md5(request()->path()));
    $votePublicKey = $voteScope === 'global' ? 'global' : md5(request()->path());
    $voteCacheKey = 'footer.vote.counts.' . $voteSlug;
    $voteCounts = \Illuminate\Support\Facades\Cache::get($voteCacheKey, ['up' => 0, 'down' => 0]);
    $voteUpCount = max(0, (int) ($voteCounts['up'] ?? 0));
    $voteDownCount = max(0, (int) ($voteCounts['down'] ?? 0));
    $voteTotalCount = $voteUpCount + $voteDownCount;
    $sellerAccountFeatureOn = (int)($fcv->seller_account_enabled ?? 0) === 1;
    // Keep footer Seller account destination identical to header SELLER button
    $sellerAccountHref = route('seller.apply');
    $sellerLinkNewTab = false;

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
    $aboutPhone = $companyInfo ? trim((string)($companyInfo->data_values->contact_phone ?? '')) : '';
    $aboutEmail = $companyInfo ? trim((string)($companyInfo->data_values->contact_email ?? '')) : '';

    $validQuickLinks = $quickLinks->filter(function ($link) {
        $dv = $link->data_values ?? (object)[];
        $t = trim((string)($dv->title ?? ''));
        return $t !== '' && strlen($t) >= 3 && preg_match('/[\p{L}\s]/u', $t);
    });
    $hasQuickLinksCol = $validQuickLinks->isNotEmpty();
    $hasSupportCol = $supportEnabled;
    // Social row can include quick email + Social Icons
    $hasSocialRow = $socialElement->isNotEmpty() || ($contactContent && !empty($contactContent->data_values->contact_email));
    $hasAboutCol = $showCompanyBlock || $policyPages->isNotEmpty();
    $rowExtraCount = ($securityBadges->isNotEmpty() ? 1 : 0) + ($voteEnabled ? 1 : 0) + ($appPromoEnabled ? 1 : 0);
    $privacyPolicyLink = $policyPages->first();
    $footerLogo = getLogo('logo') ?: getLogo('logo_dark');
    $footerEmailIcon = trim((string) (optional($contactContent)->data_values->contact_email_icon ?? ''));
    $footerEmailIconRel = ($footerEmailIcon !== '' && preg_match('#^[a-zA-Z0-9._-]+$#', $footerEmailIcon))
        ? 'assets/images/frontend/contact_us/' . $footerEmailIcon
        : '';
    $footerEmailIconAbs = $footerEmailIconRel !== '' ? public_path($footerEmailIconRel) : '';
    $footerHasEmailIconImage = $footerEmailIconAbs !== '' && is_file($footerEmailIconAbs);
@endphp

<style>
    /* Compact journal footer layout (plan2.md) */
    .footer-journal-bottom-container {
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 0.65rem !important;
    }
    .footer-journal-bottom-container img {
        height: auto !important;
        max-height: 18px !important;
        width: auto !important;
        display: block !important;
    }
    .footer-journal-bottom-left img {
        height: 20px !important;
        max-height: 20px !important;
        width: 20px !important;
        border-radius: 50% !important;
    }
    .footer-journal-bottom-right .pay-badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 3px 6px !important;
        border-radius: 4px !important;
    }
    footer.footer-journal-style .footer-journal-top {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
        gap: 0.75rem !important;
    }
    footer.footer-journal-style .footer-main-row.footer-journal-links {
        padding-top: 1.25rem !important;
        padding-bottom: 1.25rem !important;
        gap: 1.25rem 1.5rem !important;
    }
    footer.footer-journal-style .footer-secondary-row.footer-journal-extra {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
        gap: 1.25rem 1.5rem !important;
    }
    footer.footer-journal-style .footer-col-title {
        margin-bottom: 0.5rem !important;
        font-size: 0.9375rem !important;
        line-height: 1.3 !important;
    }
    footer.footer-journal-style .footer__bottom__widget p,
    footer.footer-journal-style .footer__bottom__widget li {
        margin-bottom: 0.25rem !important;
    }
    footer.footer-journal-style .footer__bottom__widget ul {
        gap: 0.35rem !important;
    }
    footer.footer-journal-style .footer-journal-bottom-row {
        margin-top: 0.5rem !important;
    }
    footer.footer-journal-style .footer-journal-bottom-container {
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
    }
    footer.footer-journal-style.stayl-footer--compact .footer-journal-top {
        padding-top: 0.65rem !important;
        padding-bottom: 0.65rem !important;
    }
    footer.footer-journal-style.stayl-footer--compact .footer-main-row.footer-journal-links {
        padding-top: 0.85rem !important;
        padding-bottom: 0.85rem !important;
    }
</style>

<footer class="site-footer stayl-footer footer-glass footer-journal-style {{ $footerCompactMode ? 'stayl-footer--compact' : '' }}" role="contentinfo" aria-label="@lang('Site footer')">

    <div class="footer-glass__inner w-full">
        @if($footerTopButtons->isNotEmpty())
        <div class="main-container pt-4">
            <div class="flex flex-wrap gap-2">
                @foreach($footerTopButtons as $btn)
                    @php $b = (array)($btn->data_values ?? []); $href = trim((string)($b['button_url'] ?? '#')) ?: '#'; @endphp
                    <a href="{{ $href }}" class="inline-flex items-center gap-1 rounded border border-slate-700 bg-slate-800 px-3 py-1.5 text-sm text-slate-300 hover:bg-slate-700 hover:text-white">
                        @if(!empty($b['icon_image']))
                            <img src="{{ asset('assets/images/frontend/custom_buttons/' . $b['icon_image']) }}" alt="{{ $b['button_text'] ?? 'Button' }}" width="16" height="16" loading="lazy">
                        @else
                            @include($activeTemplate . 'partials.icon', ['name' => ($b['icon_name'] ?? 'circle'), 'sizePx' => 14])
                        @endif
                        <span>{{ $b['button_text'] ?? __('Button') }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="main-container">
            {{-- Row: Logo | Social --}}
            <div class="flex flex-col items-start justify-between gap-3 py-4 sm:flex-row sm:items-center footer-journal-top">
                @if($footerLogo)
                <a href="{{ route('home') }}" class="inline-block shrink-0" title="@lang('Home')">
                    <img src="{{ $footerLogo }}" alt="{{ gs('site_name') }}" class="footer-logo site-logo-img stayl-footer-logo {{ getLogoEffectClasses() }}" style="--stayl-footer-logo-h: {{ getFooterLogoHeight() }}px; height: {{ max(32, (int) getFooterLogoHeight()) }}px; width: auto; {{ getLogoStyle() }}" loading="lazy" width="125" height="{{ getFooterLogoHeight() }}">
                </a>
                @endif
                @if($hasSocialRow)
                <div class="flex flex-wrap items-center gap-2 sm:justify-end footer-social-row" role="list" aria-label="{{ __($connectTitle) }}">
                    @if($contactContent && !empty($contactContent->data_values->contact_email))
                        <a href="mailto:{{ $contactContent->data_values->contact_email }}" class="footer-social-link" aria-label="@lang('Email')">
                            @if($footerHasEmailIconImage)
                                <img src="{{ getImage($footerEmailIconRel, '96x96') }}" alt="" width="22" height="22" class="object-fit-contain footer-social-image" loading="lazy" decoding="async">
                            @else
                                @include($activeTemplate . 'partials.icon', ['name' => 'envelope', 'sizePx' => 22])
                            @endif
                        </a>
                    @endif
                    @foreach ($socialElement as $social)
                        <span role="listitem" class="footer-social-item">@include($activeTemplate . 'partials.footer_social_link', ['social' => $social])</span>
                    @endforeach
                </div>
                @endif
            </div>

            <hr class="border-0 border-t border-slate-800 m-0">

            {{-- Row: 4 columns (About | Quick Links | Support | Newsletter) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-6 py-5 footer-main-row footer-journal-links">

                @if($hasAboutCol)
                <div class="min-w-0 footer__bottom__widget footer-about-widget">
                    <h3 class="text-base font-semibold text-white mb-2 footer-col-title">@lang('About Us')</h3>
                    @if($showCompanyBlock && !empty($companyInfo->data_values->about_text))
                        <p class="text-sm text-slate-400 mb-2 leading-snug">{{ __($companyInfo->data_values->about_text) }}</p>
                    @endif
                    <ul class="list-none m-0 p-0 space-y-1.5" role="list">
                        @foreach ($policyPages as $policy)
                            <li role="listitem"><a href="{{ route('policy.pages.short', $policy->id) }}" class="text-sm text-slate-400 no-underline hover:text-white">{{ __($policy->data_values->title ?? '') }}</a></li>
                        @endforeach
                        @if($showCompanyBlock && !empty($companyInfo->data_values->mission_text))
                            <li role="listitem"><span class="text-sm text-slate-500">{{ __($companyInfo->data_values->mission_text) }}</span></li>
                        @endif
                    </ul>
                    @if($contactContent && !empty($contactContent->data_values->address))
                        @php $aboutAddress = preg_replace('/\s*,\s*/', ', ', trim((string)($contactContent->data_values->address ?? ''))); @endphp
                        <p class="text-sm text-slate-500 mt-2 mb-0">{{ __($aboutAddress) }}</p>
                    @endif
                </div>
                @endif

                @if($hasQuickLinksCol)
                <div class="min-w-0 footer__bottom__widget footer-quick-links-widget">
                    <h3 class="text-base font-semibold text-white mb-2 footer-col-title">@lang('Quick Links')</h3>
                    <ul class="list-none m-0 p-0 space-y-1.5" role="list">
                        @foreach ($validQuickLinks as $link)
                            @php $dv = $link->data_values ?? (object)[]; $u = $dv->url ?? '#'; @endphp
                            <li role="listitem"><a href="{{ $u }}" class="text-sm text-slate-400 no-underline hover:text-white" @if($u !== '#') target="_blank" rel="noopener noreferrer" @endif>{{ __($dv->title ?? '') }}</a></li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($hasSupportCol)
                <div class="min-w-0 footer__bottom__widget footer-support-widget">
                    <h3 class="text-base font-semibold text-white mb-2 footer-col-title">@lang('Customer Service')</h3>
                    <ul class="list-none m-0 p-0 space-y-1.5" role="list">
                        @php $sc = $supportCenter ? ($supportCenter->data_values ?? (object)[]) : (object)[]; @endphp
                        @if(!empty($sc->help_center_url))
                            <li role="listitem"><a href="{{ $sc->help_center_url }}" class="text-sm text-slate-400 no-underline hover:text-white" target="_blank" rel="noopener noreferrer">@lang('Help Center')</a></li>
                        @endif
                        @if(!empty($sc->return_policy_url))
                            <li role="listitem"><a href="{{ $sc->return_policy_url }}" class="text-sm text-slate-400 no-underline hover:text-white">@lang('Return Policy')</a></li>
                        @endif
                        @if(!empty($sc->refund_policy_url))
                            <li role="listitem"><a href="{{ $sc->refund_policy_url }}" class="text-sm text-slate-400 no-underline hover:text-white">@lang('Refund Policy')</a></li>
                        @endif
                        <li role="listitem"><a href="{{ !empty($sc->track_order_url) ? $sc->track_order_url : route('track.order') }}" class="text-sm text-slate-400 no-underline hover:text-white">@lang('Track Order')</a></li>
                        @if(($sc->support_ticket_enabled ?? 1) && route('message.open', [], false))
                            <li role="listitem"><a href="{{ route('message.open') }}" class="text-sm text-slate-400 no-underline hover:text-white">@lang('Support Ticket')</a></li>
                        @endif
                        @if(!empty($sc->support_email))
                            <li role="listitem"><a href="mailto:{{ $sc->support_email }}" class="text-sm text-slate-400 no-underline hover:text-white">@lang('Contact Support')</a></li>
                        @endif
                    </ul>
                </div>
                @endif

                <div class="min-w-0 footer__bottom__widget footer-newsletter-widget">
                    <h3 class="text-base font-semibold text-white mb-2 footer-col-title">@lang('Newsletter')</h3>
                    @php $subT = $subscribeSubtitle ? __($subscribeSubtitle) : ''; $mainT = __($subscribeTitle); @endphp
                    <p class="text-sm text-slate-400 mb-2 leading-snug">
                        @if($subT && $subT !== $mainT){{ $subT }}@else{{ $mainT }}@endif
                    </p>
                    <form class="newletter-form js-footer-subscribe" action="{{ route('subscribe') }}" method="post" aria-label="@lang('Newsletter subscription')">
                        @csrf
                        <div class="flex w-full footer-journal-newsletter-input">
                            <label class="sr-only" for="footer-subscribe-email">@lang('Email address')</label>
                            <input id="footer-subscribe-email" type="email" name="email" class="subscribe-email min-w-0 flex-1 border border-slate-600 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-500 focus:border-slate-500 focus:outline-none" placeholder="@lang('Enter email')" required autocomplete="email">
                            <button type="submit" class="subscribe-btn inline-flex shrink-0 items-center justify-center border border-slate-700 bg-slate-900 px-3 py-2 text-white hover:bg-black focus:outline-none" aria-label="@lang('Subscribe')">
                                @include($activeTemplate . 'partials.icon', ['name' => 'paper-plane', 'sizePx' => 18, 'class' => 'text-current'])
                            </button>
                        </div>
                        <div class="subscribe-inline-message mt-2 text-sm" aria-live="polite"></div>
                        @if($privacyPolicyLink)
                        <div class="mt-2 flex items-start gap-2 text-sm text-slate-400">
                            <input type="checkbox" id="footer-newsletter-agree" name="newsletter_agree" value="1" class="mt-1 shrink-0">
                            <label for="footer-newsletter-agree" class="leading-snug">
                                @lang('I have read and agree to the')
                                <a href="{{ route('policy.pages.short', $privacyPolicyLink->id) }}" class="font-semibold text-sky-400 no-underline hover:text-sky-300">@lang('Privacy Policy')</a>
                            </label>
                        </div>
                        @endif
                    </form>
                    @if($aboutPhone !== '')
                        <p class="text-sm text-slate-500 mt-2 mb-0">
                            <span class="text-slate-400">@lang('Questions? Call us'):</span>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $aboutPhone) }}" class="text-slate-400 no-underline hover:text-white">{{ __($aboutPhone) }}</a>
                        </p>
                    @endif
                    @guest
                    <div class="mt-2 flex flex-col gap-1.5" role="navigation" aria-label="@lang('Account')">
                        <a href="{{ route('user.login') }}?open=login&redirect={{ urlencode(url()->current()) }}" class="js-footer-floating-login text-sm text-slate-400 no-underline hover:text-white">@lang('Login')</a>
                        <a href="{{ route('user.register') }}?open=register&redirect={{ urlencode(url()->current()) }}" class="js-footer-floating-register text-sm text-slate-400 no-underline hover:text-white">@lang('Registration')</a>
                        <a href="{{ $sellerAccountHref }}" class="text-sm text-slate-400 no-underline hover:text-white"@if($sellerLinkNewTab) target="_blank" rel="noopener noreferrer"@endif>@lang('Seller account')</a>
                    </div>
                    @else
                    <div class="mt-2 flex flex-col gap-1.5" role="navigation" aria-label="@lang('Account')">
                        <a href="{{ route('user.home') }}" class="text-sm text-slate-400 no-underline hover:text-white">@lang('My account')</a>
                        <a href="{{ $sellerAccountHref }}" class="text-sm text-slate-400 no-underline hover:text-white"@if($sellerLinkNewTab) target="_blank" rel="noopener noreferrer"@endif>@lang('Seller account')</a>
                    </div>
                    @endguest
                    @if($showShippingInfo && $shippingPayment)
                    <div class="mt-2 pt-2 border-t border-slate-800">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">@lang('Payment & Shipping')</p>
                        @if(($shippingPayment->data_values->cod_enabled ?? 1) == 1)
                            <p class="text-sm text-slate-500 mb-1">@lang('Cash on Delivery available')</p>
                        @endif
                        @if(!empty($shippingPayment->data_values->estimated_delivery_text))
                            <p class="text-sm text-slate-500 mb-1">@lang('Delivery'): {{ __($shippingPayment->data_values->estimated_delivery_text) }}</p>
                        @endif
                        @if(!empty($shippingPayment->data_values->shipping_partners_text))
                            <p class="text-sm text-slate-500 mb-1">{{ __($shippingPayment->data_values->shipping_partners_text) }}</p>
                        @endif
                        @if(!empty($shippingPayment->data_values->delivery_zones_text))
                            <p class="text-sm text-slate-500 mb-0">{{ __($shippingPayment->data_values->delivery_zones_text) }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            @if($rowExtraCount > 0 || $securityBadges->isNotEmpty())
            <hr class="border-0 border-t border-slate-800 m-0">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 py-4 footer-secondary-row footer-journal-extra">
                @if($securityBadges->isNotEmpty())
                <div class="min-w-0 footer__bottom__widget footer-security-widget">
                    <h3 class="text-base font-semibold text-white mb-2 footer-col-title">@lang('Trust & Security')</h3>
                    <div class="flex flex-wrap items-center gap-3" role="list">
                        @foreach ($securityBadges as $badge)
                            @php $dv = $badge->data_values ?? (object)[]; $img = $dv->image ?? null; $u = $dv->url ?? '#'; @endphp
                            @if($img)
                                <span role="listitem">
                                    @if($u && $u !== '#')
                                        <a href="{{ $u }}" target="_blank" rel="noopener noreferrer" title="{{ __($dv->title ?? '') }}">
                                            <img src="{{ getImage('assets/images/frontend/footer/' . $img, '80x80') }}" alt="{{ __($dv->title ?? '') }}" class="h-10 w-auto max-w-full object-contain opacity-90" loading="lazy">
                                        </a>
                                    @else
                                        <img src="{{ getImage('assets/images/frontend/footer/' . $img, '80x80') }}" alt="{{ __($dv->title ?? '') }}" class="h-10 w-auto max-w-full object-contain opacity-90" loading="lazy">
                                    @endif
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
                @if($voteEnabled)
                <div class="min-w-0 footer__bottom__widget footer-vote-widget">
                    <h3 class="text-base font-semibold text-white mb-2 footer-col-title">{{ __($voteTitle !== '' ? $voteTitle : __('Was this page helpful?')) }}</h3>
                    @if($voteSubtitle !== '')<p class="text-sm text-slate-500 mb-2">{{ __($voteSubtitle) }}</p>@endif
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="footer-vote-btn inline-flex items-center gap-1 rounded border border-slate-700 bg-slate-800 px-3 py-1.5 text-sm text-slate-300 hover:text-white" data-vote-kind="up">
                            @include($activeTemplate . 'partials.icon', ['name' => 'thumbs-up', 'sizePx' => 14])
                            <span>{{ __($voteUpLabel) }}</span>
                            <span class="text-xs text-slate-500" data-vote-count="up">{{ $voteUpCount }}</span>
                        </button>
                        <button type="button" class="footer-vote-btn inline-flex items-center gap-1 rounded border border-slate-700 bg-slate-800 px-3 py-1.5 text-sm text-slate-300 hover:text-white" data-vote-kind="down">
                            @include($activeTemplate . 'partials.icon', ['name' => 'thumbs-down', 'sizePx' => 14])
                            <span>{{ __($voteDownLabel) }}</span>
                            <span class="text-xs text-slate-500" data-vote-count="down">{{ $voteDownCount }}</span>
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mt-2 mb-0"><span data-vote-total>{{ $voteTotalCount }}</span> @lang('votes')</p>
                </div>
                @endif
                @if($appPromoEnabled)
                <div class="min-w-0 footer__bottom__widget footer-connect-widget">
                    <h3 class="text-base font-semibold text-white mb-2 footer-col-title">@lang('Get our app')</h3>
                    <div class="flex flex-col gap-1.5">
                        @foreach($appPromotionItems as $appItem)
                            @php
                                $adv = $appItem->data_values ?? (object)[];
                                if (is_array($adv)) $adv = (object)$adv;
                                $platform = trim($adv->platform ?? $adv->title ?? '');
                                $link = $adv->link ?? $adv->android_url ?? $adv->ios_url ?? '';
                                $appFile = $adv->app_file ?? null;
                                $label = $platform ?: ($adv->name ?? $adv->title ?? __('Download'));
                                $downloadUrl = $appFile ? asset('assets/files/frontend/apps/' . $appFile) : null;
                                $finalLink = $downloadUrl ?: $link ?: '#';
                            @endphp
                            <a href="{{ $finalLink }}" class="text-sm text-slate-400 no-underline hover:text-white" @if(!$downloadUrl && $link) target="_blank" rel="noopener noreferrer" @endif>{{ __($label) }}</a>
                        @endforeach
                        @if($appPromotionItems->isEmpty())
                            @if(!empty($appPromotion->data_values->android_url))
                                <a href="{{ $appPromotion->data_values->android_url }}" target="_blank" rel="noopener noreferrer" class="text-sm text-slate-400 no-underline hover:text-white">@lang('Android')</a>
                            @endif
                            @if(!empty($appPromotion->data_values->ios_url))
                                <a href="{{ $appPromotion->data_values->ios_url }}" target="_blank" rel="noopener noreferrer" class="text-sm text-slate-400 no-underline hover:text-white">@lang('iOS')</a>
                            @endif
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endif

            <hr class="border-0 border-t border-slate-800 m-0">

        </div>{{-- .main-container --}}

        {{-- Row: Copyright | Payment icons (Using Theme Classes) --}}
        <div class="footer-journal-bottom-row w-full mt-2 border-t border-slate-800">
            <div class="main-container footer-journal-bottom-container py-3 lg:py-4 gap-4">
                {{-- Left: Logo + Copyright --}}
                <div class="flex items-center gap-4">
                    @if($footerLogo)
                        <img src="{{ $footerLogo }}" alt="{{ gs('site_name') }}" class="footer-journal-bottom-left-img" loading="lazy">
                    @endif
                    @php
                        $copyrightText = $footerContent && trim($footerContent->data_values->copyright_text ?? '') !== ''
                            ? $footerContent->data_values->copyright_text
                            : __('Copyright') . ' &copy; ' . date('Y') . ' ' . gs('site_name') . '. ' . __('All Right Reserved.');
                        $copyrightText = str_replace('{year}', date('Y'), $copyrightText);
                    @endphp
                    <span class="text-xs text-slate-500 font-medium">{!! $copyrightText !!}</span>

                    {{-- Dynamic Cookie & Policy Links moved here for a cleaner look --}}
                    @if($showCookiePrefs || $footerBottomButtons->isNotEmpty())
                        <div class="hidden sm:flex items-center gap-3 ml-2 border-l border-slate-800 pl-4">
                            @if($showCookiePrefs)
                                <button class="text-[10px] uppercase tracking-wider text-slate-500 hover:text-white transition policyCookie">@lang('Cookie Preferences')</button>
                            @endif

                            @foreach($footerBottomButtons as $btn)
                                <a href="{{ $btn->data_values->url }}" class="text-[10px] uppercase tracking-wider text-slate-500 hover:text-white transition">
                                    {{ __($btn->data_values->button_text) }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Right: Payment icons --}}
                @if($showPaymentIcons && $footerElement->isNotEmpty())
                <div class="flex flex-wrap items-center gap-2 lg:justify-end" role="list" aria-label="@lang('Payment Methods')">
                    @foreach ($footerElement as $footerPay)
                        @php
                            $payImg = $footerPay->data_values->image ?? null;
                            $payUrl = trim($footerPay->data_values->url ?? '');
                            $payTitle = $footerPay->data_values->title ?? '';
                        @endphp
                        @if($payImg)
                            <div class="pay-badge bg-slate-900/40 border border-slate-800 transition hover:bg-slate-800" role="listitem">
                                @if($payUrl !== '')
                                    <a href="{{ $payUrl }}" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ getImage('assets/images/frontend/footer/' . $payImg) }}" alt="{{ __($payTitle) }}" loading="lazy" class="contrast-125 brightness-90">
                                    </a>
                                @else
                                    <img src="{{ getImage('assets/images/frontend/footer/' . $payImg) }}" alt="{{ __($payTitle) }}" loading="lazy" class="contrast-125 brightness-90">
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>




        </div>
    </div>
</footer>
@push('script')
<script>
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
@if($voteEnabled)
<script>
(function() {
  var host = document.querySelector('.footer-vote-widget');
  if (!host) return;
  var endpoint = @json(route('footer.vote.submit'));
  var csrf = @json(csrf_token());
  var voteKey = @json($votePublicKey);
  var buttons = host.querySelectorAll('[data-vote-kind]');
  var upCountNode = host.querySelector('[data-vote-count="up"]');
  var downCountNode = host.querySelector('[data-vote-count="down"]');
  var totalNode = host.querySelector('[data-vote-total]');
  var inFlight = false;
  var locked = false;

  function setCounts(payload) {
    if (upCountNode && Number.isFinite(payload.up)) upCountNode.textContent = payload.up;
    if (downCountNode && Number.isFinite(payload.down)) downCountNode.textContent = payload.down;
    if (totalNode && Number.isFinite(payload.total)) totalNode.textContent = payload.total;
  }

  function setLockedState() {
    buttons.forEach(function(btn) {
      btn.disabled = true;
      btn.classList.add('is-disabled');
    });
  }

  buttons.forEach(function(btn) {
    btn.addEventListener('click', function() {
      if (inFlight || locked) return;
      var kind = btn.getAttribute('data-vote-kind');
      if (!kind) return;
      inFlight = true;

      fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ vote: kind, vote_key: voteKey })
      }).then(function(res) {
        return res.json();
      }).then(function(res) {
        if (!res || !res.success) return;
        setCounts({
          up: Number(res.up || 0),
          down: Number(res.down || 0),
          total: Number(res.total || 0)
        });
        locked = true;
        setLockedState();
      }).catch(function() {
      }).finally(function() {
        inFlight = false;
      });
    });
  });
})();
</script>
@endif
@endpush
