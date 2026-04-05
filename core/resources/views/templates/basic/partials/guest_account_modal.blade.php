@guest
@php
    $guestLangs = \App\Models\Language::all();
@endphp
<div class="modal fade" id="guestAccountModal" tabindex="-1" aria-labelledby="guestAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered guest-account-modal-dialog" role="document">
        <div class="modal-content guest-account-modal-content border-0">
            <button type="button" class="guest-account-close-btn" data-bs-dismiss="modal" data-stayl-close="guest-account" aria-label="@lang('Close')">
                @include($activeTemplate . 'partials.icon', ['name' => 'times'])
            </button>
            <div class="modal-body p-3 p-sm-4">
                @include($activeTemplate . 'partials.guest_account_panel', ['guestAccountHeadingId' => 'guestAccountModalLabel'])
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
    /* StaylModal + Bootstrap .fade: must be visible when .is-open (above mobile bottom nav z-9999) */
    #guestAccountModal.is-open,
    #guestAccountModal.show {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        z-index: 100050 !important;
        overflow-x: hidden;
        overflow-y: auto;
        outline: 0;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: rgba(2, 6, 23, 0.5) !important;
    }
    #guestAccountModal.is-open .modal-dialog,
    #guestAccountModal.show .modal-dialog {
        opacity: 1 !important;
        transform: none !important;
        transition: opacity .2s ease, transform .2s ease;
    }
    #guestAccountModal .guest-account-modal-dialog { margin: 0.75rem auto; }
    /* Close control must sit inside the card, not viewport (avoid main.css .modal-close-btn top/right) */
    #guestAccountModal .guest-account-modal-content {
        position: relative !important;
        isolation: isolate;
    }
    #guestAccountModal .guest-account-close-btn {
        position: absolute !important;
        top: 10px !important;
        right: 10px !important;
        left: auto !important;
        bottom: auto !important;
        width: 36px !important;
        height: 36px !important;
        min-width: 36px !important;
        padding: 0 !important;
        margin: 0 !important;
        border: 0 !important;
        background: rgba(248, 250, 252, 0.95) !important;
        color: #475569 !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 20 !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.12) !important;
        cursor: pointer !important;
    }
    #guestAccountModal .guest-account-close-btn .ui-icon {
        width: 16px !important;
        height: 16px !important;
    }
@media (max-width: 639.98px) {
    #guestAccountModal .guest-account-modal-dialog { max-width: min(560px, calc(100vw - 16px)); margin: 0.5rem auto; }
    #guestAccountModal .guest-account-modal-content { border-radius: 16px; box-shadow: 0 24px 60px rgba(2, 6, 23, 0.22); }
}
</style>
@endpush
@endguest
