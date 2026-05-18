@extends('admin.layouts.auth')
@section('content')
@php
    $adminLoginLogo = getLogo('logo_dark') ?: getLogo('logo');
    $adminSiteName = $general->site_name ?? gs('site_name');
    $captchaCode = $captchaCode ?? null;
    $showCaptcha = !empty($captchaCode) || !empty($useImageCaptcha ?? false);
    
    // Define assetBase in child view to ensure availability
    $assetBase = str_replace('/core/public', '', url('/'));
    if (str_ends_with($assetBase, '/')) {
        $assetBase = rtrim($assetBase, '/');
    }
@endphp

<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Login -->
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
                <h5 class="mb-0 fw-bold">@lang('Welcome Back')</h5>
                <p class="mb-0 text-muted x-small">@lang('Sign in to manage your account')</p>
            </div>

            <form id="formAuthentication" action="{{ route('admin.login.submit') }}" method="POST" autocomplete="off">
                @csrf
              <div class="mb-3 form-control-validation">
                <label for="adminLoginUsername" class="form-label text-[10px] uppercase fw-bold text-muted mb-1">@lang('Credentials')</label>
                <input type="text" class="form-control" id="adminLoginUsername" name="username" value="" placeholder="@lang('Email or Username')" autofocus required autocomplete="new-username" />
              </div>
              <div class="mb-3 form-password-toggle form-control-validation">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="form-label text-[10px] uppercase fw-bold text-muted mb-0" for="adminPassword">@lang('Password')</label>
                  <a href="{{ route('admin.password.reset') }}" class="text-brand-teal x-small fw-semibold">
                    @lang('Forgot?')
                  </a>
                </div>
                <div class="input-group input-group-merge border rounded-2 overflow-hidden">
                  <input type="password" id="adminPassword" class="form-control border-0" name="password" placeholder="@lang('Enter password')" required autocomplete="new-password" />
                  <span class="input-group-text cursor-pointer bg-white border-0"><i class="icon-base bx bx-hide"></i></span>
                </div>
              </div>

              @if($showCaptcha)
              <div class="mb-3 form-control-validation">
                <label class="form-label text-[10px] uppercase fw-bold text-muted mb-1">@lang('Verification')</label>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="captcha-img-wrap bg-lighter rounded px-3 py-1 flex-grow-1 text-center border">
                        @if($useImageCaptcha ?? false)
                            <img src="{{ route('admin.login.captcha.image') }}?t={{ time() }}" alt="captcha" id="adminCaptchaImage" class="h-px-25 rounded">
                        @else
                            <span class="text-brand-teal fw-bold fs-6" id="adminCaptchaCode">{{ $captchaCode }}</span>
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-icon btn-sm" id="adminCaptchaRefresh" title="@lang('Refresh Code')">
                        <i class="las la-sync-alt"></i>
                    </button>
                </div>
                <input type="text" name="admin_login_captcha" id="adminCaptchaInput" class="form-control form-control-sm text-center tracking-widest" placeholder="@lang('Enter Code')" required autocomplete="off">
              </div>
              @endif

              <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="policy_confirm" id="policyConfirm" required />
                    <label class="form-check-label x-small" for="policyConfirm"> @lang('I confirm my identity and agree to login policy') </label>
                    <div class="invalid-feedback x-small">@lang('Please confirm to continue')</div>
                </div>
              </div>
              
              <div class="mb-0">
                <button class="btn btn-primary d-grid w-100 shadow-sm py-2" type="submit" id="adminLoginBtn">@lang('Login Now')</button>
              </div>
            </form>

          </div>
        </div>
        <!-- /Login -->
      </div>
    </div>
  </div>

@push('script')
<script>
(function() {
    'use strict';
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('formAuthentication');
        var policyCheck = document.getElementById('policyConfirm');
        var loginBtn = document.getElementById('adminLoginBtn');
        
        if (form && policyCheck) {
            form.addEventListener('submit', function(e) {
                if (!policyCheck.checked) {
                    e.preventDefault();
                    policyCheck.classList.add('is-invalid');
                    policyCheck.focus();
                    return false;
                }
                
                if (loginBtn) {
                    loginBtn.disabled = true;
                    loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> @lang("Logging in...")';
                }
            });
            
            policyCheck.addEventListener('change', function() {
                if (this.checked) {
                    this.classList.remove('is-invalid');
                }
            });
        }

        // Captcha Refresh Logic
        var refreshBtn = document.getElementById('adminCaptchaRefresh');
        var captchaImg = document.getElementById('adminCaptchaImage');
        var captchaCodeText = document.getElementById('adminCaptchaCode');
        
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                if (captchaImg) {
                    captchaImg.src = '{{ route("admin.login.captcha.image") }}?t=' + Date.now();
                } else if (captchaCodeText) {
                    fetch('{{ route("admin.login.captcha.refresh") }}', { 
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } 
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.code) captchaCodeText.textContent = data.code;
                    });
                }
            });
        }
    });
})();
</script>
@endpush
@endsection
