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

{{-- inline style moved to critical-storefront.css --}}

@endpush
