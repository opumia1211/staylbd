@extends($activeTemplate . 'layouts.frontend')
@php
    $disableLegacyBootstrapBundle = true;
    $disableLegacyJquery = true;
    $disableLegacyJqueryUi = true;
@endphp

@section('content')
    <div class="guest-account-standalone guest-account-standalone--fullbleed pb-120">
        <header class="guest-account-standalone__topbar">
            <a href="{{ route('home') }}" class="guest-account-standalone__back" aria-label="@lang('Back to home')">
                @include($activeTemplate . 'partials.icon', ['name' => 'angle-left'])
            </a>
            <h1 class="guest-account-standalone__title">@lang('My Account')</h1>
            <a href="{{ route('home') }}" class="guest-account-standalone__close" aria-label="@lang('Close')">
                @include($activeTemplate . 'partials.icon', ['name' => 'times'])
            </a>
        </header>
        @include($activeTemplate . 'partials.guest_account_panel', ['guestAccountHideHeading' => true])
    </div>
@endsection

@push('style')
<style>
    /*
     * ফুল-ব্লিড: প্যারেন্ট .main-container কেন্দ্রীকৃত হলেও ভিউপোর্ট জুড়ে ঠিক মাপ।
     * পুরনো left:50% + margin:-50vw ভুল (৫০% = কনটেইনারের, ৫০vw = ভিউপোর্টের) — ডিভাইস ভেদে UI ভাঙত।
     */
    .guest-account-standalone--fullbleed {
        position: relative;
        box-sizing: border-box;
        width: 100vw;
        max-width: 100vw;
        margin-left: calc(50% - 50vw);
        margin-right: calc(50% - 50vw);
        background: #f1f5f9;
        min-height: 75vh;
        min-height: 75dvh;
        padding-bottom: calc(72px + env(safe-area-inset-bottom, 0px));
        overflow-x: hidden;
    }
    .guest-account-standalone__topbar {
        position: sticky;
        top: 0;
        z-index: 20;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        padding-top: calc(10px + env(safe-area-inset-top, 0px));
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-bottom: 1px solid #e2e8f0;
        box-sizing: border-box;
    }
    .guest-account-standalone__back,
    .guest-account-standalone__close {
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        color: #334155;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }
    .guest-account-standalone__back:active,
    .guest-account-standalone__close:active {
        background: #e2e8f0;
    }
    .guest-account-standalone__back .ui-icon,
    .guest-account-standalone__close .ui-icon {
        width: 20px;
        height: 20px;
    }
    .guest-account-standalone__title {
        flex: 1 1 auto;
        margin: 0;
        font-size: clamp(1rem, 4vw, 1.125rem);
        font-weight: 800;
        color: #0f172a;
        text-align: center;
        letter-spacing: -0.02em;
        min-width: 0;
    }
    @supports (width: 100dvw) {
        .guest-account-standalone--fullbleed {
            width: 100dvw;
            max-width: 100dvw;
            margin-left: calc(50% - 50dvw);
            margin-right: calc(50% - 50dvw);
        }
    }
</style>
@endpush
