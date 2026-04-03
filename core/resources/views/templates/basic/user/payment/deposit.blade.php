@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="payment-methods-section">
        <div class="payment-methods-inner">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="payment-glass-card rounded-4 overflow-hidden shadow-sm">
                        <div class="payment-glass-header">
                            <div class="payment-glass-header-inner">
                                <h1 class="payment-title mb-2">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'wallet', 'class' => 'me-2'])
                                    @lang('Select Payment Method')
                                </h1>
                                <p class="payment-subtitle mb-0">
                                    @lang('Choose your preferred payment gateway. All methods show official logos. Click to pay securely.')
                                </p>
                            </div>
                        </div>
                        <div class="payment-glass-body">
                            {{-- Order total — clear and prominent --}}
                            <div class="order-total-glass d-flex align-items-center justify-content-between flex-wrap gap-2 rounded-3 mb-4">
                                <span class="order-total-label">@lang('Order total')</span>
                                <span class="order-total-amount">{{ $general->cur_sym }}{{ getAmount(@$order->total) }} {{ __($general->cur_text) }}</span>
                            </div>

                            <form id="deposit-form" action="{{ route('user.deposit.insert', $order->id ?? 0) }}" method="post">
                                @csrf
                                <input type="hidden" name="amount" value="{{ getAmount(@$order->total) }}">
                                <input type="hidden" name="gateway" id="input-gateway" value="">
                                <input type="hidden" name="currency" id="input-currency" value="">

                                <p class="gateway-instruction mb-3">@lang('Click on a payment method to continue:')</p>
                                <div class="gateway-grid row g-3">
                                    @foreach ($gatewayCurrency as $data)
                                        @php
                                            $alias = $data->method->alias ?? $data->gateway_alias ?? '';
                                            $logoUrl = getGatewayLogoUrl($alias, $data->method->logo ?? null);
                                        @endphp
                                        <div class="col-6 col-sm-4 col-lg-3">
                                            <button type="button" class="gateway-card-glass w-100 rounded-3 p-3 text-center text-decoration-none h-100 d-flex flex-column align-items-center justify-content-center gateway-card-btn"
                                                    data-gateway="{{ $data->method_code }}"
                                                    data-currency="{{ $data->currency }}"
                                                    data-name="{{ __($data->name) }}">
                                                @if($logoUrl)
                                                    <img src="{{ $logoUrl }}" alt="{{ __($data->name) }}" class="gateway-logo img-fluid mb-2" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
                                                    <span class="d-none gateway-placeholder-icon">@include($activeTemplate . 'partials.icon', ['name' => 'credit-card'])</span>
                                                @else
                                                    <span class="gateway-placeholder-icon mb-2">@include($activeTemplate . 'partials.icon', ['name' => 'credit-card'])</span>
                                                @endif
                                                <span class="gateway-name">{{ __($data->name) }}</span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                @if($gatewayCurrency->isEmpty())
                                    <div class="text-center py-5">
                                        <p class="gateway-empty mb-0">@lang('No payment methods available. Please contact support.')</p>
                                    </div>
                                @endif
                            </form>

                            <div class="payment-glass-footer mt-3 pt-3 d-flex flex-wrap align-items-center gap-2">
                                <a href="{{ route('user.order.index') }}" class="btn btn-outline-secondary btn-sm">
                                    @include($activeTemplate . 'partials.icon', ['name' => 'arrow-left', 'class' => 'me-1'])@lang('Back to orders')
                                </a>
                                <span class="small text-muted">@lang('More payment methods can be added by the store.')</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
/* প্রফেশনাল ই-কমার্স পেমেন্ট সেকশন – সব ডিভাইসে সুন্দর */
.payment-methods-inner { width: 100%; max-width: 100%; padding: 0 0.25rem; }
.payment-methods-section {
    min-height: 50vh;
    padding: 1.25rem 0 1.5rem;
    background: linear-gradient(165deg, rgba(var(--base-rgb, 99, 102, 241), 0.05) 0%, #f8fafc 40%, #fff 100%);
}
@media (min-width: 576px) {
    .payment-methods-section { padding: 1.5rem 0 2rem; }
}
@media (min-width: 992px) {
    .payment-methods-section { padding: 1.75rem 0 2rem; min-height: 55vh; }
}

.payment-glass-card {
    background: #fff;
    border: 1px solid rgba(0, 0, 0, 0.06);
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06), 0 2px 8px rgba(0, 0, 0, 0.03);
}
.payment-glass-header {
    background: linear-gradient(135deg, var(--base, #6366f1) 0%, rgba(var(--base-rgb, 99, 102, 241), 0.9) 100%);
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.12);
}
.payment-glass-header-inner { color: #fff; }
.payment-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.3;
    margin-bottom: 0.35rem;
}
.payment-subtitle {
    font-size: 0.875rem;
    opacity: 0.95;
    color: rgba(255,255,255,0.95);
    line-height: 1.45;
    margin: 0;
}
@media (min-width: 576px) {
    .payment-title { font-size: 1.4rem; }
    .payment-glass-header { padding: 1.35rem 1.75rem; }
}
@media (min-width: 768px) {
    .payment-title { font-size: 1.5rem; }
}

.payment-glass-body { padding: 1.5rem 1.25rem; }
@media (min-width: 576px) { .payment-glass-body { padding: 1.75rem 1.5rem; } }
@media (min-width: 768px) { .payment-glass-body { padding: 2rem 1.75rem; } }

.order-total-glass {
    background: rgba(var(--base-rgb, 99, 102, 241), 0.08);
    border: 1px solid rgba(var(--base-rgb, 99, 102, 241), 0.2);
    padding: 1rem 1.25rem;
    border-radius: 10px;
}
.order-total-label { font-weight: 600; color: #374151; font-size: 0.95rem; }
.order-total-amount {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--base, #6366f1);
    letter-spacing: -0.02em;
}
@media (min-width: 576px) { .order-total-amount { font-size: 1.4rem; } }

.gateway-instruction { font-size: 0.9rem; color: #4b5563; font-weight: 500; margin-bottom: 1rem; }

.gateway-grid { margin-bottom: 0.5rem; }
.gateway-card-glass {
    cursor: pointer;
    min-height: 120px;
    background: #fff;
    border: 1px solid rgba(0, 0, 0, 0.08);
    transition: border-color .2s ease, box-shadow .2s ease, transform .15s ease;
}
.gateway-card-glass:hover {
    border-color: rgba(var(--base-rgb, 99, 102, 241), 0.35);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}
.gateway-card-glass:active { transform: translateY(0); }
@media (max-width: 575.98px) {
    .gateway-card-glass { min-height: 100px; padding: 0.85rem !important; }
}
.gateway-card-glass .gateway-logo {
    max-height: 40px;
    width: auto;
    max-width: 100%;
    object-fit: contain;
}
@media (min-width: 576px) {
    .gateway-card-glass .gateway-logo { max-height: 48px; }
}
.gateway-card-glass .gateway-placeholder-icon {
    font-size: 1.75rem;
    color: var(--base, #6366f1);
    opacity: 0.85;
}
@media (min-width: 576px) {
    .gateway-card-glass .gateway-placeholder-icon { font-size: 2rem; }
}
.gateway-card-glass .gateway-name {
    font-size: 0.8rem;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.3;
    display: block;
    width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
@media (min-width: 576px) {
    .gateway-card-glass .gateway-name { font-size: 0.85rem; }
}

.gateway-empty { color: #6b7280; font-size: 0.95rem; }
.payment-glass-footer {
    border-top: 1px solid rgba(0, 0, 0, 0.06);
    padding-top: 1rem;
}
.payment-glass-footer .btn { min-height: 38px; }
@media (max-width: 575.98px) {
    .payment-glass-footer { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
    .payment-glass-footer .small.text-muted { margin-left: 0 !important; }
}
</style>
@endpush

@push('script')
<script>
(function() {
    'use strict';
    var form = document.getElementById('deposit-form');
    var inputGateway = document.getElementById('input-gateway');
    var inputCurrency = document.getElementById('input-currency');
    if (!form || !inputGateway || !inputCurrency) return;
    document.querySelectorAll('.gateway-card-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var gw = this.getAttribute('data-gateway');
            var cur = this.getAttribute('data-currency');
            if (!gw || !cur) return;
            inputGateway.value = gw;
            inputCurrency.value = cur;
            form.submit();
        });
    });
})();
</script>
@endpush
