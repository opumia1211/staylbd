@extends($activeTemplate . 'layouts.frontend')

@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush

@section('content')
@php
    $qoSettings = function_exists('quickOrderSettings') ? quickOrderSettings() : null;
@endphp
<section class="guest-order-page" id="guestOrderPage">
    <div class="container guest-order-container">
        <div class="guest-order-back">
            <a href="{{ route('user.cart') }}">@include($activeTemplate . 'partials.icon', ['name' => 'arrow-left']) @lang('Back to cart')</a>
        </div>
        <div class="guest-order-card">
            <div class="guest-order-card__head">
                <h1>@include($activeTemplate . 'partials.icon', ['name' => 'shopping-bag', 'class' => 'text-primary']) @lang('Your order')</h1>
                <p>{{ __('Review items and complete checkout. Payment: Cash on Delivery or as confirmed by our team.') }}</p>
            </div>
            <div class="guest-order-summary">
                @foreach($cartLines as $line)
                    <div class="guest-order-line">
                        <div>
                            <div class="guest-order-line__name">{{ __($line->product->name) }}</div>
                            <div class="guest-order-line__meta">
                                {{ __('Qty') }}: {{ $line->quantity }}
                                @if($line->variant_details)
                                    · {{ $line->variant_details }}
                                @endif
                            </div>
                        </div>
                        <div class="guest-order-line__total">{{ $general->cur_sym }}{{ showAmount($line->line_total) }}</div>
                    </div>
                @endforeach
                <div class="guest-order-total-row">
                    <span>@lang('Subtotal')</span>
                    <span>{{ $general->cur_sym }}{{ showAmount($subtotal) }}</span>
                </div>
            </div>
        </div>
        <div class="guest-order-card guest-order-form-wrap">
            <h2 class="quick-order-title-inline">@include($activeTemplate . 'partials.icon', ['name' => 'bolt']) @lang('Delivery & contact')</h2>
            <p class="qo-sub">{{ $qoSettings?->subtitle ?? __('Place your order — no account needed. Our team will confirm by phone.') }}</p>
            @include($activeTemplate . 'partials.guest_checkout_quick_form')
        </div>
    </div>
</section>
@endsection
