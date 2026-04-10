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

{{-- inline style moved to critical-storefront.css --}}

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
