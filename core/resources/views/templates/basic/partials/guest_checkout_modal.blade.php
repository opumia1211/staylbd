{{-- Quick Order modal – compact, smart, clean – minimal width/height --}}
@guest

{{-- inline style moved to critical-storefront.css --}}

<div class="modal fade" id="guestCheckoutModal" tabindex="-1" aria-labelledby="guestCheckoutModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content quick-order-dialog border-0">
            <div class="modal-header quick-order-header border-0 position-relative">
                <div>
                    <h5 class="modal-title quick-order-title" id="guestCheckoutModalLabel">
                        @include($activeTemplate . 'partials.icon', ['name' => 'bolt'])
                        @lang('Quick Order')
                    </h5>
                    @php $qoSettings = function_exists('quickOrderSettings') ? quickOrderSettings() : null; @endphp
                    <p class="quick-order-subtitle">{{ $qoSettings?->subtitle ?? __('Place your order in seconds — no account needed. Our team will confirm by phone.') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body quick-order-body pt-0">
                <div class="mx-auto w-full max-w-storefront">
                    @include($activeTemplate . 'partials.guest_checkout_quick_form')
                </div>
            </div>
        </div>
    </div>
</div>
@endguest
