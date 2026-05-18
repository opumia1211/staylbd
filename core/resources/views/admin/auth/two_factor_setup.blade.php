@extends('admin.layouts.auth')
@section('content')
@php
    $adminLoginLogo = getLogo('logo_dark') ?: getLogo('logo');
    $assetBase = str_replace('/core/public', '', url('/'));
    if (str_ends_with($assetBase, '/')) {
        $assetBase = rtrim($assetBase, '/');
    }
    $clearSetupCodeField = (bool) session()->pull('admin_2fa_setup_clear_code');
@endphp

@push('style')
<style>
  .authentication-basic .authentication-inner {
    max-width: 490px !important;
  }
  @media (min-width: 576px) {
    .border-end-sm {
      border-right: 1px solid rgba(0,0,0,0.08) !important;
    }
  }
  .compact-label {
    font-size: 9px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    color: #64748b !important;
    display: block !important;
  }
</style>
@endpush

<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- 2FA Setup Card -->
        <div class="card overflow-hidden">
          <div class="card-body py-3 px-4">
            <!-- Logo & Title Inline/Compact -->
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
              <div class="d-flex align-items-center gap-2">
                <img src="{{ str_replace(url('/') . '/', $assetBase . '/', $adminLoginLogo) }}" alt="logo" class="h-6 w-auto">
                <h6 class="mb-0 fw-bold text-dark fs-6">@lang('Two-Factor Setup')</h6>
              </div>
              <span class="badge bg-label-primary px-2 py-1 x-small fw-bold">@lang('Suraksha')</span>
            </div>

            <form action="{{ route('admin.2fa.setup.confirm') }}" method="POST" id="admin2faSetupForm" autocomplete="off" novalidate data-clear-code="{{ $clearSetupCodeField ? '1' : '' }}">
                @csrf
              
              <div class="d-flex flex-column flex-sm-row gap-3 align-items-stretch mb-3">
                  
                  <!-- Left Column: QR Code -->
                  <div class="d-flex flex-column align-items-center justify-content-center border-end-sm pe-sm-3 flex-shrink-0" style="min-width: 170px;">
                      <div id="twofa-qr-code" class="d-inline-block border p-1.5 rounded bg-white shadow-sm" aria-label="@lang('Authenticator QR code')"></div>
                      <div class="mt-2 text-center">
                          <a href="{{ route('admin.2fa.setup') }}" class="text-brand-teal x-small fw-semibold d-inline-flex align-items-center gap-1">
                              <i class="bx bx-refresh"></i> @lang('New QR Code')
                          </a>
                      </div>
                  </div>

                  <!-- Right Column: Verification & Keys -->
                  <div class="flex-grow-1 d-flex flex-column justify-content-between">
                      <!-- Manual Key Section -->
                      <div class="mb-2 bg-lighter p-2 rounded border">
                          <span class="compact-label mb-1">@lang('Manual key')</span>
                          <code class="fs-7 user-select-all fw-bold text-brand-teal d-block" style="word-break: break-all;" title="@lang('Copy into your authenticator app')">{{ $secret }}</code>
                      </div>

                      <!-- Input Code -->
                      <div class="mb-2 form-control-validation">
                        <label class="compact-label mb-1" for="admin2faSetupCode">@lang('Verification code')</label>
                        <input type="text"
                               name="code"
                               id="admin2faSetupCode"
                               class="form-control text-center tracking-widest fw-bold fs-5 py-1.5"
                               value=""
                               placeholder="000000"
                               title="@lang('6-digit code from your app')"
                               maxlength="6"
                               minlength="6"
                               inputmode="numeric"
                               pattern="[0-9]{6}"
                               required
                               autocomplete="off"
                               autofocus>
                      </div>
                      
                      <!-- Policy Check -->
                      <div class="mb-0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="policy_confirm" id="policyConfirm" required />
                            <label class="form-check-label x-small text-muted fw-semibold" for="policyConfirm" style="line-height: 1.25;"> @lang('I saved the key securely') </label>
                        </div>
                      </div>
                  </div>
              </div>

              <!-- Compact Horizontal Actions Row -->
              <div class="d-flex gap-2 border-top pt-2">
                <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm py-2" id="admin2faSetupBtn" data-submitting="0">
                    @lang('Enable 2FA')
                </button>
                <a href="{{ route('admin.logout') }}" class="btn btn-label-secondary py-2 px-3" style="font-weight: 600 !important; border-radius: 8px !important;">@lang('Cancel')</a>
              </div>
            </form>

          </div>
        </div>
        <!-- /2FA Setup Card -->
      </div>
    </div>
</div>

@push('script')
<script src="{{ asset('assets/admin/js/vendor/qrcode.min.js') }}?v={{ $assetVersion ?? config('app.version') }}"></script>
<script>
(function() {
    'use strict';
    var otpauthUrl = @json($otpauthUrl);
    var qrSize = 160;
    
    function initQr() {
        var el = document.getElementById('twofa-qr-code');
        if (typeof QRCode === 'undefined' || !el) return;
        el.innerHTML = '';
        try {
            var opts = { text: otpauthUrl, width: qrSize, height: qrSize };
            if (QRCode.CorrectLevel) {
                opts.correctLevel = QRCode.CorrectLevel.H;
            }
            new QRCode(el, opts);
        } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', function() {
        initQr();
        
        var form = document.getElementById('admin2faSetupForm');
        var policyCheck = document.getElementById('policyConfirm');
        var btn = document.getElementById('admin2faSetupBtn');
        var code = document.getElementById('admin2faSetupCode');
        
        if (code) {
            if (form && form.getAttribute('data-clear-code') === '1') {
                code.value = '';
                try { code.focus(); } catch (e) {}
            }
            code.addEventListener('input', function() {
                code.value = code.value.replace(/\D/g, '').slice(0, 6);
            });
        }
        
        if (form && btn && policyCheck) {
            form.addEventListener('submit', function(e) {
                if (!policyCheck.checked) {
                    e.preventDefault();
                    policyCheck.classList.add('is-invalid');
                    policyCheck.focus();
                    return false;
                }
                
                if (btn.getAttribute('data-submitting') === '1') {
                    e.preventDefault();
                    return false;
                }
                
                btn.setAttribute('data-submitting', '1');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> @lang("Verifying...")';
            });
            
            policyCheck.addEventListener('change', function() {
                if (this.checked) {
                    this.classList.remove('is-invalid');
                }
            });
        }
    });
    
    window.addEventListener('pageshow', function(ev) {
        if (!ev.persisted) return;
        var code = document.getElementById('admin2faSetupCode');
        var btn = document.getElementById('admin2faSetupBtn');
        if (code) code.value = '';
        if (btn) {
            btn.disabled = false;
            btn.setAttribute('data-submitting', '0');
            btn.textContent = @json(__('Enable 2FA'));
        }
    });
})();
</script>
@endpush
@endsection
