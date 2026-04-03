@extends($activeTemplate . 'layouts.frontend')

@push('style')
<style>
.guest-order-page { padding: 1.25rem 0 2rem; background: linear-gradient(180deg, #f8fafc 0%, #fff 40%); }
.guest-order-page .guest-order-container { max-width: 720px; margin: 0 auto; }
.guest-order-page .guest-order-card {
    background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;
    box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; margin-bottom: 1rem;
}
.guest-order-page .guest-order-card__head {
    padding: 0.85rem 1rem; border-bottom: 1px solid #e2e8f0;
    background: #fafafa;
}
.guest-order-page .guest-order-card__head h1 {
    font-size: 1.15rem; font-weight: 700; margin: 0; color: #0f172a;
}
.guest-order-page .guest-order-card__head p { margin: 0.25rem 0 0; font-size: 0.8125rem; color: #64748b; }
.guest-order-page .guest-order-summary { padding: 0.75rem 1rem; }
.guest-order-page .guest-order-line {
    display: flex; gap: 0.75rem; align-items: flex-start; padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9; font-size: 0.875rem;
}
.guest-order-page .guest-order-line:last-child { border-bottom: none; }
.guest-order-page .guest-order-line__name { flex: 1; font-weight: 600; color: #1e293b; }
.guest-order-page .guest-order-line__meta { color: #64748b; font-size: 0.8rem; }
.guest-order-page .guest-order-line__total { font-weight: 600; color: #0e9f90; white-space: nowrap; }
.guest-order-page .guest-order-total-row {
    display: flex; justify-content: space-between; align-items: center;
    padding-top: 0.65rem; margin-top: 0.25rem; border-top: 1px solid #e2e8f0;
    font-weight: 700; font-size: 1rem; color: #0f172a;
}
.guest-order-page .guest-order-form-wrap {
    padding: 1rem 1rem 1.25rem;
}
.guest-order-page .guest-order-form-wrap .quick-order-title-inline {
    font-size: 1rem; font-weight: 700; color: #0d6efd; margin: 0 0 0.35rem;
    display: flex; align-items: center; gap: 0.35rem;
}
.guest-order-page .guest-order-form-wrap .qo-sub { font-size: 0.78rem; color: #64748b; margin: 0 0 0.75rem; }
.guest-order-page .form-group-qo { margin-bottom: 0.5rem; }
.guest-order-page .form-control-qo {
    width: 100%; min-height: 38px; padding: 0.4rem 0.65rem; font-size: 0.875rem;
    border: 1px solid #e2e8f0; border-radius: 8px; background: #fff;
}
.guest-order-page .form-control-qo:focus {
    border-color: #0d6efd; outline: 0; box-shadow: 0 0 0 2px rgba(13,110,253,.12);
}
.guest-order-page textarea.form-control-qo { min-height: 72px; resize: vertical; }
.guest-order-page .quick-order-actions {
    display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.85rem; padding-top: 0.75rem; border-top: 1px solid #e2e8f0;
}
.guest-order-page .btn-confirm-order {
    flex: 1 1 auto; min-width: 160px; min-height: 42px; font-weight: 600; font-size: 0.9rem;
    border-radius: 8px; border: none; color: #fff;
    background: linear-gradient(135deg, #198754 0%, #157347 100%);
}
.guest-order-page .btn-confirm-order:hover { color: #fff; opacity: 0.95; }
.guest-order-page .btn-login-link { font-size: 0.8125rem; padding: 0.45rem 0.85rem; border-radius: 8px; font-weight: 600; }
.guest-order-page .qo-row-2 { margin-bottom: 0.35rem; }
@media (min-width: 480px) {
    .guest-order-page .qo-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; }
    .guest-order-page .qo-row-2 .form-group-qo { margin-bottom: 0; }
}
.guest-order-page #guestCheckoutSuccess,
.guest-order-page #guestCheckoutError { margin-top: 0.75rem; padding: 0.65rem 0.85rem; border-radius: 8px; font-size: 0.875rem; }
.guest-order-page #guestCheckoutSuccess { background: rgba(25,135,84,.1); border: 1px solid rgba(25,135,84,.2); }
.guest-order-page #guestCheckoutError { background: rgba(220,53,69,.08); border: 1px solid rgba(220,53,69,.18); }
.guest-order-page .guest-order-back { margin-bottom: 0.75rem; }
.guest-order-page .guest-order-back a { font-size: 0.875rem; font-weight: 600; text-decoration: none; color: #475569; }
.guest-order-page .guest-order-back a:hover { color: #0e9f90; }
</style>
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
