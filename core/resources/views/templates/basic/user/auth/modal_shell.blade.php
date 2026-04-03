{{-- Floating auth modal: /user/login and /user/register as centered overlay. Switch between Login and Register without page reload. --}}
@php
    $openModal = $openModal ?? 'login';
    $authLogo = getLogo('logo');
    $siteName = gs('site_name');
@endphp
@extends($activeTemplate . 'layouts.auth_modal')
@section('content')
@include($activeTemplate . 'user.auth.partials.auth_page_styles')
<div class="auth-overlay" id="authModalOverlay">
    <div class="auth-card" style="position:relative;">
        <a href="{{ route('home') }}" class="auth-close" aria-label="@lang('Close')" title="@lang('Close')">&times;</a>

        <div class="auth-header">
            @if($authLogo)
                <img src="{{ $authLogo }}" alt="{{ $siteName }}">
            @endif
            @if($siteName)
                <span class="auth-title">{{ __($siteName) }}</span>
            @endif
        </div>

        <div id="auth-panel-login" class="auth-panel {{ $openModal === 'login' ? 'auth-panel-active' : '' }}">
            @include($activeTemplate . 'user.auth.partials.modal_login_form')
        </div>

        <div id="auth-panel-register" class="auth-panel {{ $openModal === 'register' ? 'auth-panel-active' : '' }}">
            @include($activeTemplate . 'user.auth.partials.modal_register_form')
        </div>
    </div>
</div>
@if(!empty($loginLockoutUntil))
@push('script')
<script>
(function(){
    var el = document.getElementById('pageLoginLockoutCountdown');
    var timer = document.getElementById('pageLoginLockoutTimer');
    var btn = document.getElementById('pageLoginForm') && document.getElementById('pageLoginForm').querySelector('button[type=submit]');
    if (!el || !timer) return;
    var unlockAt = parseInt(el.getAttribute('data-unlock-at'), 10);
    function up() {
        var now = Math.floor(Date.now() / 1000);
        var left = unlockAt - now;
        if (left <= 0) {
            el.classList.add('login-lockout-done');
            var label = el.querySelector('.login-lockout-label');
            if (label) label.textContent = @json(__('You can try again.'));
            if (timer) timer.textContent = '00:00';
            if (btn) btn.disabled = false;
            return;
        }
        var m = Math.floor(left / 60), s = left % 60;
        timer.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
        if (btn) btn.disabled = true;
        setTimeout(up, 1000);
    }
    up();
})();
</script>
@endpush
@endif
@push('script')
<script>
(function() {
    var card = document.querySelector('.auth-card');
    if (!card) return;

    var codeInput = card.querySelector('input[name=mobile_code]');
    var countryCodeInput = card.querySelector('input[name=country_code]');

    function setMobileCodeFromSelect(sel) {
        if (!sel || !codeInput) return;
        var opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) return;
        var code = opt.getAttribute('data-mobile_code') || opt.dataset.mobile_code;
        if (code) codeInput.value = code;
    }
    var phoneCountrySel = card.querySelector('select[name=country_code].phone-country-select');
    if (phoneCountrySel) {
        phoneCountrySel.addEventListener('change', function() { setMobileCodeFromSelect(phoneCountrySel); });
        setMobileCodeFromSelect(phoneCountrySel);
    }

    var group = card.querySelector('.phone-country-input-group');
    if (group) {
        var searchInput = group.querySelector('.phone-country-search');
        var countryDial = {};
        var dialToCountry = {};
        try {
            countryDial = JSON.parse(group.getAttribute('data-country-dial') || '{}');
            dialToCountry = JSON.parse(group.getAttribute('data-dial-to-country') || '{}');
        } catch (e) {}
        function syncFromSearch() {
            var val = (searchInput && searchInput.value) ? searchInput.value.trim() : '';
            if (!val) {
                if (countryCodeInput) countryCodeInput.value = '';
                if (codeInput) codeInput.value = '';
                return;
            }
            var match = val.match(/^([A-Za-z]{2})\s*\+\s*(\d+)$/);
            if (match) {
                var cc = match[1].toUpperCase();
                var dial = match[2];
                if (countryCodeInput) countryCodeInput.value = cc;
                if (codeInput) codeInput.value = dial;
                return;
            }
            if (countryDial[val.toUpperCase()]) {
                if (countryCodeInput) countryCodeInput.value = val.toUpperCase();
                if (codeInput) codeInput.value = countryDial[val.toUpperCase()];
                return;
            }
            var dialOnly = val.replace(/\D/g, '');
            if (dialToCountry[dialOnly]) {
                if (countryCodeInput) countryCodeInput.value = dialToCountry[dialOnly];
                if (codeInput) codeInput.value = dialOnly;
            }
        }
        if (searchInput) {
            searchInput.addEventListener('input', syncFromSearch);
            searchInput.addEventListener('change', syncFromSearch);
            searchInput.addEventListener('blur', syncFromSearch);
            syncFromSearch();
        }
    }

    var countrySel = card.querySelector('select[name=country]');
    if (countrySel && codeInput) {
        var codeCountryInput = card.querySelector('input[name=country_code]');
        function updateFromCountry() {
            if (!countrySel) return;
            var opt = countrySel.options[countrySel.selectedIndex];
            if (!opt) return;
            var code = opt.getAttribute('data-mobile_code') || opt.dataset.mobile_code;
            var dataCode = opt.getAttribute('data-code') || opt.dataset.code;
            if (code) codeInput.value = code;
            if (codeCountryInput && dataCode) codeCountryInput.value = dataCode;
        }
        countrySel.addEventListener('change', updateFromCountry);
        updateFromCountry();
    }
})();
</script>
@endpush
@endsection
