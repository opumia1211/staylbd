@extends('admin.layouts.master')
@section('content')
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/professional-login.css') }}">
@endpush
@php
    $adminLoginLogo = getLogo('logo_dark') ?: getLogo('logo');
    $adminSiteName = $general->site_name ?? gs('site_name');
    $retryMinutes = $retryMinutes ?? 0;
    $retryAt = $retryAt ?? null;
    $retryAtFormatted = $retryAt ? \Carbon\Carbon::createFromTimestamp($retryAt)->format('h:i A, d M Y') : '';
    $currentTime = now()->format('h:i:s A');
    $currentDate = now()->format('d M Y');
@endphp
<div class="login-main">
    <div class="container custom-container">
        <div class="row justify-content-center">
            <div class="col-12 admin-login-col">
                <div class="login-area">
                    <div class="login-wrapper">
                        <div class="login-wrapper__top login-wrapper__top--clean">
                            <div class="login-header-inner">
                                @if($adminLoginLogo)
                                    <a href="{{ url('/') }}" class="admin-login-logo-link">
                                        <img src="{{ $adminLoginLogo }}" alt="{{ __($adminSiteName) }}" class="admin-login-logo">
                                    </a>
                                @endif
                                @if($adminSiteName)
                                    <h3 class="login-site-name"><strong>{{ __($adminSiteName) }}</strong></h3>
                                @endif
                                <p class="admin-login-subtitle">@lang('Admin Panel')</p>
                            </div>
                        </div>
                        <div class="login-wrapper__body">
                            {{-- Time: current date & time --}}
                            <div class="admin-blocked-time mb-3">
                                <div class="d-flex align-items-center justify-content-center gap-2 text-muted small">
                                    <i class="las la-clock"></i>
                                    <span id="adminBlockedCurrentDate">{{ $currentDate }}</span>
                                    <span class="opacity-75">|</span>
                                    <span id="adminBlockedCurrentTime">{{ $currentTime }}</span>
                                </div>
                            </div>

                            <div class="admin-blocked-card rounded p-3 mb-3" style="background: rgba(255, 193, 7, 0.12); border: 1px solid rgba(255, 193, 7, 0.4);">
                                <div class="d-flex align-items-start gap-2">
                                    <span class="admin-blocked-icon mt-1" style="color: #856404;"><i class="las la-lock" style="font-size: 1.25rem;"></i></span>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-bold text-dark" style="font-size: 0.95rem;">@lang('Login temporarily blocked')</h6>
                                        <p class="mb-2 small text-body-secondary" style="line-height: 1.5;">@lang('Too many failed login attempts from your IP. Please try again after the waiting period.')</p>
                                        <div class="admin-blocked-retry small">
                                            <span class="text-dark fw-semibold">@lang('You can try again in'):</span>
                                            <span class="text-dark">{{ $retryMinutes }} @lang('minute(s)')</span>
                                        </div>
                                        @if($retryAtFormatted)
                                            <p class="mb-0 mt-1 small text-body-secondary">@lang('Block expires at'): <strong>{{ $retryAtFormatted }}</strong></p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('admin.login') }}" class="btn cmn-btn w-100" id="adminBlockedBackBtn">
                                    <i class="las la-arrow-left me-2"></i>@lang('Back to login')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
(function() {
    function pad(n) { return n < 10 ? '0' + n : n; }
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    function updateTime() {
        var d = new Date();
        var h = d.getHours(), m = d.getMinutes(), s = d.getSeconds();
        var ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        var timeStr = pad(h) + ':' + pad(m) + ':' + pad(s) + ' ' + ampm;
        var dateStr = pad(d.getDate()) + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        var dateEl = document.getElementById('adminBlockedCurrentDate');
        var timeEl = document.getElementById('adminBlockedCurrentTime');
        if (timeEl) timeEl.textContent = timeStr;
        if (dateEl) dateEl.textContent = dateStr;
    }
    updateTime();
    setInterval(updateTime, 1000);
})();
</script>
@endpush
@endsection
