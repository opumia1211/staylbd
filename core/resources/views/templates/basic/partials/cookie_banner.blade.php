@php
    $cookie = $cookie ?? App\Models\Frontend::where('data_keys', 'cookie.data')->first();
@endphp
@if ($cookie && isset($cookie->data_values->status) && ($cookie->data_values->status ?? 0) == \App\Constants\Status::ENABLE && !\Cookie::get('gdpr_cookie') && !\Cookie::get('gdpr_cookie_declined'))
@php
    $data = $cookie->data_values;
    $position = $data->banner_position ?? 'bottom';
    $style = $data->banner_style ?? 'compact';
    $linkText = $data->link_text ?? __('learn more');
    $allowText = $data->allow_btn_text ?? __('Allow');
    $declineText = $data->decline_btn_text ?? __('Decline');
    $showDecline = ($data->show_decline_btn ?? 1) != 0;
    $showDelay = min(60, max(0, (int)($data->show_delay ?? 2))) * 1000;
    $logo = getLogo('logo');
    $useLightBox = !empty($logo) && (($data->logo_box_style ?? 'light') === 'light');
@endphp
<style>
/* Cookie banner logo – always visible size and border (avoids cache issues) */
.gdpr-cookie-banner .gdpr-cookie-banner__icon {
    width: 100px !important;
    height: 100px !important;
    min-width: 100px !important;
    min-height: 100px !important;
    border: 2px solid #94a3b8 !important;
    border-radius: 14px !important;
    box-sizing: border-box !important;
}
.gdpr-cookie-banner .gdpr-cookie-banner__icon--light {
    background: #f1f5f9 !important;
    border: 2px solid #94a3b8 !important;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08) !important;
}
.gdpr-cookie-banner .gdpr-cookie-banner__icon .gdpr-cookie-banner__logo {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    padding: 8px !important;
    box-sizing: border-box !important;
}
.gdpr-cookie-banner--compact .gdpr-cookie-banner__icon {
    width: 92px !important;
    height: 92px !important;
    min-width: 92px !important;
    min-height: 92px !important;
}
.gdpr-cookie-banner--minimal .gdpr-cookie-banner__icon {
    width: 84px !important;
    height: 84px !important;
    min-width: 84px !important;
    min-height: 84px !important;
}
</style>
<div class="gdpr-cookie-banner gdpr-cookie-banner--{{ $position }} gdpr-cookie-banner--{{ $style }} gdpr-cookie-banner--smart hide" data-delay="{{ $showDelay }}" role="dialog" aria-label="@lang('Cookie consent')">
    <div class="gdpr-cookie-banner__inner">
        <div class="gdpr-cookie-banner__icon {{ $useLightBox ? 'gdpr-cookie-banner__icon--light' : 'bg--base' }}" aria-hidden="true">
            @if(!empty($logo))
                <img src="{{ $logo }}" alt="{{ gs('site_name') }}" class="gdpr-cookie-banner__logo">
            @else
                @include($activeTemplate . 'partials.icon', ['name' => 'cookie-bite'])
            @endif
        </div>
        <div class="gdpr-cookie-banner__content">
            <p class="gdpr-cookie-banner__text">{{ $data->short_desc ?? '' }} <a href="{{ route('cookie.policy') }}" target="_blank" rel="noopener" class="gdpr-cookie-banner__link text--base">{{ __($linkText) }}</a></p>
            <div class="gdpr-cookie-banner__btns">
                <button type="button" class="gdpr-cookie-banner__btn gdpr-cookie-banner__btn--allow gdpr-cookie-allow">{{ __($allowText) }}</button>
                @if($showDecline)
                <button type="button" class="gdpr-cookie-banner__btn gdpr-cookie-banner__btn--decline gdpr-cookie-decline">{{ __($declineText) }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
