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

{{-- inline style moved to critical-storefront.css --}}

@endpush
@endguest
