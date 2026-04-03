@if(session('notify'))
    @foreach(session('notify') as $n)
        <div class="alert alert-{{ $n[0] === 'error' ? 'danger' : 'success' }} alert-dismissible fade show py-2 px-3 small mb-2" role="alert">
            {{ __($n[1]) }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach
@endif
@if($errors->any())
    <div class="alert alert-danger py-2 px-3 small mb-2">
        <ul class="mb-0 list-unstyled small">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
    </div>
@endif

<div class="ps-page">
    <ul class="ps-nav nav nav-tabs border-0 gap-1 mb-2" role="tablist">
        <li class="nav-item">
            <a class="nav-link rounded-2 px-3 py-1 {{ !request('edit') ? 'active' : '' }}" href="{{ route('admin.frontend.sections.footer.section', 'payment-shipping') }}#section-settings">
                <i class="las la-cog me-1"></i>@lang('Settings')
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-2 px-3 py-1 {{ request('edit') ? 'active' : '' }}" href="{{ route('admin.frontend.sections.footer.section', 'payment-shipping') }}#payment-methods">
                <i class="las la-credit-card me-1"></i>@lang('Payment Icons')
            </a>
        </li>
    </ul>

    <section id="section-settings" class="ps-section ps-section--settings mb-3">
        <div class="ps-section__head">
            <span class="ps-section__title">@lang('Payment & Shipping')</span>
        </div>
        <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}" class="ps-form">
            @csrf
            <input type="hidden" name="section" value="shipping_payment">
            <div class="row g-2">
                <div class="col-6 col-md-4">
                    <label class="ps-form__label">@lang('Payment Icons')</label>
                    <select name="show_payment_icons" class="form-select form-select-sm">
                        <option value="1" {{ (optional($shippingPayment)->data_values->show_payment_icons ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                        <option value="0" {{ !(optional($shippingPayment)->data_values->show_payment_icons ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                    </select>
                </div>
                <div class="col-6 col-md-4">
                    <label class="ps-form__label">@lang('Shipping Info')</label>
                    <select name="show_shipping_info" class="form-select form-select-sm">
                        <option value="1" {{ (optional($shippingPayment)->data_values->show_shipping_info ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                        <option value="0" {{ !(optional($shippingPayment)->data_values->show_shipping_info ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                    </select>
                </div>
                <div class="col-6 col-md-4">
                    <label class="ps-form__label">@lang('Cash on Delivery')</label>
                    <select name="cod_enabled" class="form-select form-select-sm">
                        <option value="1" {{ (optional($shippingPayment)->data_values->cod_enabled ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                        <option value="0" {{ !(optional($shippingPayment)->data_values->cod_enabled ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="ps-form__label">@lang('Estimated Delivery')</label>
                    <input type="text" name="estimated_delivery_text" class="form-control form-control-sm" placeholder="e.g. 3-5 days" value="{{ optional($shippingPayment)->data_values->estimated_delivery_text ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="ps-form__label">@lang('Shipping Partners')</label>
                    <input type="text" name="shipping_partners_text" class="form-control form-control-sm" value="{{ optional($shippingPayment)->data_values->shipping_partners_text ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="ps-form__label">@lang('Delivery Zones')</label>
                    <input type="text" name="delivery_zones_text" class="form-control form-control-sm" value="{{ optional($shippingPayment)->data_values->delivery_zones_text ?? '' }}">
                </div>
            </div>
            <button type="submit" class="btn btn--primary btn-sm mt-2">@lang('Save')</button>
        </form>
    </section>

    <section id="payment-methods" class="ps-section ps-section--icons">
        @include('admin.frontend.footer.sections.partials.payment_methods_block', [
            'paymentMethodsSection' => 'payment-shipping',
            'editingPaymentItem' => $editingPaymentItem ?? null,
            'footerElements' => $footerElements ?? collect(),
        ])
    </section>
</div>

@push('style')
<style>
.ps-page { font-size: 0.8125rem; }
.ps-nav .nav-link { color: var(--bs-secondary); font-weight: 500; }
.ps-nav .nav-link:hover { color: var(--bs-primary); }
.ps-nav .nav-link.active { background: var(--bs-primary); color: #fff; border: none; }
.ps-section { background: #f8f9fa; border-radius: 8px; padding: 0.6rem 0.9rem; }
.ps-section__head { margin-bottom: 0.4rem; }
.ps-section__title { font-weight: 600; font-size: 0.8rem; color: #333; }
.ps-form__label { display: block; font-size: 0.7rem; color: #6c757d; margin-bottom: 0.15rem; }
.ps-form .form-control-sm, .ps-form .form-select-sm { font-size: 0.8rem; padding: 0.25rem 0.45rem; height: auto; }
.ps-section .payment-methods-block { background: none; padding: 0; }
.ps-section .payment-methods-block .table { font-size: 0.8rem; }
.ps-section .payment-methods-block .table td, .ps-section .payment-methods-block .table th { padding: 0.3rem 0.45rem; }
.ps-section .payment-methods-block .table thead th { font-size: 0.7rem; font-weight: 600; color: #6c757d; }
.ps-section .payment-methods-block .payment-icon-form .row { margin-bottom: 0; }
.ps-section .payment-methods-block .form-control-sm { font-size: 0.8rem; padding: 0.25rem 0.45rem; }
</style>
@endpush

@push('script')
<script>
(function(){
    if (window.location.search.indexOf('edit=') !== -1 || window.location.hash === '#payment-methods') {
        var el = document.getElementById('payment-methods');
        if (el) setTimeout(function(){ el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 150);
    }
})();
</script>
@endpush
