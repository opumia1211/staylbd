{{-- Quick Order modal – compact, smart, clean – minimal width/height --}}
@guest
<style>
#guestCheckoutModal {
    --qo-primary: #0d6efd;
    --qo-success: #198754;
    --qo-border: #e2e8f0;
    --qo-text: #334155;
    --qo-muted: #64748b;
    --qo-radius: 8px;
    --qo-shadow: 0 8px 24px rgba(0,0,0,.06), 0 1px 4px rgba(0,0,0,.04);
}
#guestCheckoutModal .modal-dialog {
    width: 100%;
    max-width: min(400px, calc(100vw - 20px));
    margin: 0.5rem auto;
}
#guestCheckoutModal .quick-order-dialog {
    border-radius: var(--qo-radius);
    box-shadow: var(--qo-shadow);
    border: 1px solid var(--qo-border);
    overflow: hidden;
}
#guestCheckoutModal .quick-order-header {
    background: #fff;
    padding: 0.6rem 3.75rem 0.5rem 0.85rem;
    border-bottom: 1px solid var(--qo-border);
}
#guestCheckoutModal .quick-order-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--qo-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    letter-spacing: -0.02em;
}
#guestCheckoutModal .quick-order-title i { font-size: 1.1em; color: var(--qo-primary); }
#guestCheckoutModal .quick-order-subtitle {
    font-size: 0.7rem;
    color: var(--qo-muted);
    margin: 0.15em 0 0;
    line-height: 1.35;
}
#guestCheckoutModal .quick-order-body {
    padding: 0.6rem 0.85rem 0.75rem;
    max-height: min(82vh, 76dvh);
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}
#guestCheckoutModal .quick-order-body::-webkit-scrollbar { width: 4px; }
#guestCheckoutModal .quick-order-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
#guestCheckoutModal .form-group-qo { margin-bottom: 0.4rem; }
#guestCheckoutModal .form-control-qo {
    width: 100%;
    min-height: 34px;
    padding: 0.35rem 0.5rem;
    font-size: 0.875rem;
    border: 1px solid var(--qo-border);
    border-radius: 6px;
    background: #fff;
    transition: border-color .15s, box-shadow .15s;
}
#guestCheckoutModal .form-control-qo:focus {
    border-color: var(--qo-primary);
    outline: 0;
    box-shadow: 0 0 0 2px rgba(13,110,253,.12);
}
#guestCheckoutModal .form-control-qo::placeholder { color: #94a3b8; font-size: 0.8rem; }
#guestCheckoutModal textarea.form-control-qo { min-height: 56px; resize: vertical; padding: 0.35rem 0.5rem; }
#guestCheckoutModal .invalid-feedback { font-size: 0.7rem; margin-top: 1px; }
#guestCheckoutModal .quick-order-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    margin-top: 0.75rem;
    padding-top: 0.6rem;
    border-top: 1px solid var(--qo-border);
}
#guestCheckoutModal .btn-confirm-order {
    min-height: 36px;
    padding: 0.4rem 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    border-radius: 6px;
    background: linear-gradient(135deg, var(--qo-success) 0%, #157347 100%);
    border: none;
    color: #fff;
    box-shadow: 0 1px 4px rgba(25,135,84,.25);
    transition: transform .12s, box-shadow .12s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    flex: 1 1 auto;
    min-width: 120px;
}
#guestCheckoutModal .btn-confirm-order:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 2px 8px rgba(25,135,84,.3); }
#guestCheckoutModal .btn-confirm-order:active { transform: translateY(0); }
#guestCheckoutModal .btn-login-link {
    font-size: 0.78rem;
    padding: 0.4rem 0.9rem;
    min-height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    font-weight: 600;
}
#guestCheckoutModal #guestCheckoutSuccess,
#guestCheckoutModal #guestCheckoutError { margin-top: 0.5rem; padding: 0.5rem 0.65rem; border-radius: 6px; font-size: 0.8rem; }
#guestCheckoutModal #guestCheckoutSuccess { background: rgba(25,135,84,.1); border: 1px solid rgba(25,135,84,.2); }
#guestCheckoutModal #guestCheckoutError { background: rgba(220,53,69,.08); border: 1px solid rgba(220,53,69,.18); }
#guestCheckoutModal .quick-order-header .btn-close {
    position: absolute;
    top: 0.55rem;
    right: 0.6rem;
    width: 28px;
    height: 28px;
    border-radius: 999px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    color: #9ca3af;
    opacity: 0.9;
}
#guestCheckoutModal .quick-order-header .btn-close span {
    display: block;
    font-size: 1.1rem;
    line-height: 1;
    font-weight: 600;
}
#guestCheckoutModal .quick-order-header .btn-close:hover {
    background: rgba(148,163,184,0.12);
    color: #4b5563;
}
#guestCheckoutModal .quick-order-header .btn-close:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(148,163,184,0.4);
}
#guestCheckoutModal .qo-row-2 { margin-bottom: 0.5rem; }
@media (min-width: 380px) {
    #guestCheckoutModal .qo-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; }
    #guestCheckoutModal .qo-row-2 .form-group-qo { margin-bottom: 0; }
}
@media (max-width: 575.98px) {
    #guestCheckoutModal .modal-dialog { margin: 0.5rem; max-width: calc(100vw - 12px); }
    #guestCheckoutModal .quick-order-body { padding: 0.5rem 0.65rem 0.6rem; max-height: 80vh; }
    #guestCheckoutModal .quick-order-actions { flex-direction: column; align-items: stretch; gap: 0.35rem; }
    #guestCheckoutModal .btn-confirm-order { min-width: 100%; }
}
@supports (padding: max(0px)) {
    #guestCheckoutModal .quick-order-body { padding-left: max(0.85rem, env(safe-area-inset-left)); padding-right: max(0.85rem, env(safe-area-inset-right)); }
    #guestCheckoutModal .quick-order-header { padding-right: max(3.25rem, env(safe-area-inset-right) + 2.75rem); }
}
</style>
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
