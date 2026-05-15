@extends('admin.layouts.auth')
@section('content')
@php
    $adminLoginLogo = getLogo('logo_dark') ?: getLogo('logo');
    $adminSiteName = $general->site_name ?? gs('site_name');
    $captchaCode = $captchaCode ?? null;
    $showCaptcha = !empty($captchaCode) || !empty($useImageCaptcha ?? false);
@endphp

<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Login -->
        <div class="card px-sm-6 px-0">
          <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center mb-6">
              <a href="{{ route('home') }}" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                  <img src="{{ $adminLoginLogo }}" alt="logo" class="w-px-150">
                </span>
              </a>
            </div>
            <!-- /Logo -->
            <h4 class="mb-1">@lang('Welcome to') {{ $adminSiteName }}!</h4>
            <p class="mb-6">Please sign-in to your account</p>

            <form id="formAuthentication" class="mb-6" action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
              <div class="mb-6 form-control-validation">
                <label for="adminLoginUsername" class="form-label">@lang('Email or Username')</label>
                <input type="text" class="form-control" id="adminLoginUsername" name="username" value="{{ old('username') }}" placeholder="@lang('Enter your email or username')" autofocus required />
              </div>
              <div class="mb-6 form-password-toggle form-control-validation">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="adminPassword">@lang('Password')</label>
                  <a href="{{ route('admin.password.reset') }}">
                    <small>@lang('Forgot Password?')</small>
                  </a>
                </div>
                <div class="input-group input-group-merge">
                  <input type="password" id="adminPassword" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required />
                  <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                </div>
              </div>

              @if($showCaptcha)
              <div class="mb-6 form-control-validation">
                <label class="form-label">@lang('Verify You Are Human')</label>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="captcha-img-wrap bg-lighter rounded px-3 py-2 flex-grow-1 text-center border">
                        @if($useImageCaptcha ?? false)
                            <img src="{{ route('admin.login.captcha.image') }}?t={{ time() }}" alt="captcha" id="adminCaptchaImage" class="h-px-30">
                        @else
                            <span class="text-primary fw-bold fs-5" id="adminCaptchaCode">{{ $captchaCode }}</span>
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-icon" id="adminCaptchaRefresh" title="@lang('Refresh Code')">
                        <i class="las la-sync-alt"></i>
                    </button>
                </div>
                <input type="text" name="admin_login_captcha" id="adminCaptchaInput" class="form-control" placeholder="@lang('Enter Code')" required autocomplete="off">
              </div>
              @endif

              <div class="mb-7">
                <div class="d-flex justify-content-between">
                  <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
                    <label class="form-check-label" for="remember"> @lang('Remember Me') </label>
                  </div>
                </div>
              </div>
              <div class="mb-6">
                <button class="btn btn-primary d-grid w-100" type="submit" id="adminLoginBtn">@lang('Login')</button>
              </div>
            </form>

          </div>
        </div>
        <!-- /Login -->
      </div>
    </div>
  </div>

@push('script')
<script src="{{ asset('assets/js/pages-auth.js') }}"></script>
<script>
(function() {
    'use strict';
    document.addEventListener('DOMContentLoaded', function() {
        // Captcha Refresh Logic (Specific to Laravel App)
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
