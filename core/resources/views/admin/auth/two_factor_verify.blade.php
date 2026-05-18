@extends('admin.layouts.auth')
@section('content')
@php
    $twoFaSubtitle = $pageTitle ?? __('Two-Factor Authentication');
    $adminLoginLogo = getLogo('logo_dark') ?: getLogo('logo');
    
    // Define assetBase in child view to ensure availability
    $assetBase = str_replace('/core/public', '', url('/'));
    if (str_ends_with($assetBase, '/')) {
        $assetBase = rtrim($assetBase, '/');
    }
@endphp

<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- 2FA Verification -->
        <div class="card overflow-hidden">
          <div class="card-body py-4 px-4">
            <!-- Logo -->
            <div class="app-brand justify-content-center mb-3">
              <a href="{{ route('home') }}" class="app-brand-link gap-2 transition-transform hover:scale-105">
                <span class="app-brand-logo demo">
                  <img src="{{ str_replace(url('/') . '/', $assetBase . '/', $adminLoginLogo) }}" alt="logo" class="h-8 w-auto">
                </span>
              </a>
            </div>
            <!-- /Logo -->
            <div class="text-center mb-4">
                <h5 class="mb-0 fw-bold">@lang('2FA Verification')</h5>
                <p class="mb-0 text-muted x-small">@lang('Enter your security code to continue')</p>
            </div>

            <form id="admin2faVerifyForm" action="{{ route('admin.2fa.verify.submit') }}" method="POST" autocomplete="off">
                @csrf
              
              <div class="mb-3 form-control-validation">
                <label for="admin2faCode" class="form-label text-[10px] uppercase fw-bold text-muted mb-1">@lang('Authentication Code')</label>
                <input type="text" 
                       name="code" 
                       id="admin2faCode" 
                       class="form-control text-center fs-4 tracking-widest fw-bold" 
                       placeholder="000000" 
                       maxlength="6" 
                       inputmode="numeric" 
                       pattern="[0-9]*" 
                       autocomplete="one-time-code" 
                       autofocus />
                <p class="text-[10px] text-muted mt-1 text-center">@lang('6-digit code from your app')</p>
              </div>

              <div class="divider my-4">
                <div class="divider-text text-muted x-small uppercase">@lang('Or Use Recovery Code')</div>
              </div>

              <div class="mb-4 form-control-validation">
                <label for="admin2faRecovery" class="form-label text-[10px] uppercase fw-bold text-muted mb-1">@lang('Recovery Code')</label>
                <input type="text" 
                       name="recovery_code" 
                       id="admin2faRecovery" 
                       class="form-control" 
                       placeholder="@lang('One-time backup code')" 
                       autocomplete="off" />
              </div>

              <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="policy_confirm" id="policyConfirm" required />
                    <label class="form-check-label x-small" for="policyConfirm"> @lang('Confirm my 2FA identity') </label>
                </div>
              </div>

              <div class="mb-3">
                <button class="btn btn-primary d-grid w-100 shadow-sm py-2" type="submit" id="admin2faVerifyBtn">@lang('Verify & Continue')</button>
              </div>

              <div class="text-center">
                <a href="{{ route('admin.logout') }}" class="btn btn-link text-muted x-small p-0">
                  <i class="icon-base bx bx-chevron-left"></i>
                  @lang('Cancel and Logout')
                </a>
              </div>
            </form>

          </div>
        </div>
        <!-- /2FA Verification -->
      </div>
    </div>
  </div>

@push('script')
<script>
(function() {
    function init() {
        var form = document.getElementById('admin2faVerifyForm');
        var btn = document.getElementById('admin2faVerifyBtn');
        if (form && btn) {
            form.addEventListener('submit', function() {
                if (btn.getAttribute('data-submitting') === '1') return;
                btn.setAttribute('data-submitting', '1');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> ' + @json(__('Verifying...'));
            });
        }
        var otp = document.getElementById('admin2faCode');
        if (otp) {
            otp.addEventListener('input', function() {
                otp.value = otp.value.replace(/\D/g, '').slice(0, 6);
            });
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endpush
@endsection
