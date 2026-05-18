@extends('admin.layouts.auth')
@section('content')
@php
    $captchaCode = $captchaCode ?? null;
    $useImageCaptcha = $useImageCaptcha ?? false;
    $passResetLockoutUntil = (int) ($passResetLockoutUntil ?? 0);
    $passResetFormLocked = $passResetLockoutUntil > time();

    // Define assetBase in child view to ensure availability
    $assetBase = str_replace('/core/public', '', url('/'));
    if (str_ends_with($assetBase, '/')) {
        $assetBase = rtrim($assetBase, '/');
    }
@endphp
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Recover Account -->
        <div class="card overflow-hidden">
          <div class="card-body py-4 px-4">
            <!-- Logo -->
            <div class="app-brand justify-content-center mb-3">
              <a href="{{ route('home') }}" class="app-brand-link gap-2 transition-transform hover:scale-105">
                <span class="app-brand-logo demo">
                  @php $adminLoginLogo = getLogo('logo_dark') ?: getLogo('logo'); @endphp
                  <img src="{{ str_replace(url('/') . '/', $assetBase . '/', $adminLoginLogo) }}" alt="logo" class="h-8 w-auto">
                </span>
              </a>
            </div>
            <!-- /Logo -->
            <div class="text-center mb-4">
                <h5 class="mb-0 fw-bold text-danger">@lang('Account Recovery')</h5>
                <p class="mb-0 text-muted x-small">@lang('Enter email to reset your password')</p>
            </div>

            @if($passResetFormLocked)
            <div id="adminPassResetCountdownWrap" class="mb-3 p-3 bg-lighter rounded text-center border" data-retry-at="{{ $passResetLockoutUntil }}">
                <div class="d-flex align-items-center justify-center gap-2 text-danger fw-bold mb-1">
                    <i class="las la-clock fs-5"></i>
                    <span id="adminPassResetCountdown" class="small">--:--</span>
                </div>
                <p class="x-small text-muted mb-0">@lang('Too many attempts. Please wait.')</p>
            </div>
            @endif

            <form action="{{ route('admin.password.reset') }}" method="POST" autocomplete="off" id="adminPasswordResetForm" @if($passResetFormLocked) data-blocked="1" @endif>
                @csrf
              <div class="mb-4 form-control-validation">
                <label for="adminResetEmail" class="form-label text-[10px] uppercase fw-bold text-muted mb-1">@lang('Registered Email')</label>
                <input type="email" class="form-control" id="adminResetEmail" name="email" value="{{ old('email') }}" placeholder="@lang('Email address')" required autocomplete="email" @if($passResetFormLocked) disabled @endif />
              </div>

              @if($captchaCode || $useImageCaptcha)
              <div class="mb-4 form-control-validation">
                <label class="form-label text-[10px] uppercase fw-bold text-muted mb-1">@lang('Verification')</label>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="captcha-img-wrap bg-lighter rounded px-3 py-1 flex-grow-1 text-center border">
                        @if($useImageCaptcha)
                            <img src="{{ route('admin.login.captcha.image') }}?t={{ time() }}" alt="" id="adminResetCaptchaImage" class="h-px-25 rounded">
                        @else
                            <span class="text-brand-teal fw-bold fs-6" id="adminCaptchaCode">{{ $captchaCode }}</span>
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-icon btn-sm" id="adminResetCaptchaRefresh" title="@lang('Refresh')" @if($passResetFormLocked) disabled @endif>
                        <i class="las la-redo-alt"></i>
                    </button>
                </div>
                <input type="text" name="admin_login_captcha" id="adminResetCaptchaInput" class="form-control form-control-sm text-center tracking-widest" placeholder="@lang('Enter Code')" required autocomplete="off" maxlength="10" @if($passResetFormLocked) disabled @endif>
              </div>
              @endif

              <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="policy_confirm" id="policyConfirm" required />
                    <label class="form-check-label x-small" for="policyConfirm"> @lang('I confirm this account recovery request') </label>
                </div>
              </div>

              <div class="mb-3">
                <button class="btn btn-primary d-grid w-100 shadow-sm py-2" type="submit" id="adminPasswordResetBtn">@lang('Send Reset Link')</button>
              </div>

              <div class="text-center mt-3">
                <a href="{{ route('admin.login') }}" class="btn btn-link text-brand-teal fw-bold p-0">
                  <i class="icon-base bx bx-chevron-left"></i>
                  @lang('Go Back to Login Page')
                </a>
              </div>
            </form>

          </div>
        </div>
        <!-- /Recover Account -->
      </div>
    </div>
  </div>

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var countdownWrap = document.getElementById('adminPassResetCountdownWrap');
    var countdownEl = document.getElementById('adminPassResetCountdown');
    if (countdownWrap && countdownEl) {
        var retryAt = parseInt(countdownWrap.getAttribute('data-retry-at'), 10);
        function pad(n) { return n < 10 ? '0' + n : n; }
        function tick() {
            var now = Math.floor(Date.now() / 1000);
            var left = retryAt - now;
            if (left <= 0) {
                countdownEl.textContent = '0:00';
                location.reload();
                return;
            }
            var m = Math.floor(left / 60);
            var s = left % 60;
            countdownEl.textContent = pad(m) + ':' + pad(s);
        }
        tick();
        setInterval(tick, 1000);
    }

    var form = document.getElementById('adminPasswordResetForm');
    var btn = document.getElementById('adminPasswordResetBtn');
    if (btn && form) {
        form.addEventListener('submit', function(e) {
            if (form.getAttribute('data-blocked') === '1') {
                e.preventDefault();
                return;
            }
            if (btn.getAttribute('data-submitting') === '1') return;
            btn.setAttribute('data-submitting', '1');
            btn.disabled = true;
            btn.innerHTML = '<i class="las la-spinner la-spin me-2"></i> @lang("Please wait...")';
        });
    }
    var refreshBtn = document.getElementById('adminResetCaptchaRefresh');
    var imgEl = document.getElementById('adminResetCaptchaImage');
    var codeEl = document.getElementById('adminResetCaptchaCode');
    var inputEl = document.getElementById('adminResetCaptchaInput');
    if (refreshBtn) {
        var refreshHtml = refreshBtn.innerHTML;
        refreshBtn.addEventListener('click', function() {
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = '&hellip;';
            if (imgEl) {
                imgEl.src = '{{ route('admin.login.captcha.image') }}?t=' + Date.now();
                if (inputEl) inputEl.value = '';
                refreshBtn.disabled = false;
                refreshBtn.innerHTML = refreshHtml;
            } else {
                fetch('{{ route('admin.login.captcha.refresh') }}', { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data && data.code && codeEl) codeEl.textContent = data.code;
                        if (inputEl) inputEl.value = '';
                    })
                    .catch(function() {})
                    .finally(function() {
                        refreshBtn.disabled = false;
                        refreshBtn.innerHTML = refreshHtml;
                    });
            }
        });
    }
});
</script>
@endpush
@endsection
