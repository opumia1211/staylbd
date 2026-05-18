@extends('admin.layouts.auth')
@section('content')
@php
    $recoverySubtitle = __('Recovery codes');
    $adminLoginLogo = getLogo('logo_dark') ?: getLogo('logo');
    
    // Define assetBase in child view to ensure availability
    $assetBase = str_replace('/core/public', '', url('/'));
    if (str_ends_with($assetBase, '/')) {
        $assetBase = rtrim($assetBase, '/');
    }
@endphp

@push('style')
<style>
  .authentication-basic .authentication-inner {
    max-width: 490px !important;
  }
  .bg-lighter {
    background-color: #f8fafc !important;
  }
  .text-warning-800 {
    color: #854d0e !important;
  }
</style>
@endpush

<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Recovery Codes Card -->
        <div class="card overflow-hidden">
          <div class="card-body py-4 px-4">
            
            <!-- Logo & Title Inline/Compact -->
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
              <div class="d-flex align-items-center gap-2">
                <img src="{{ str_replace(url('/') . '/', $assetBase . '/', $adminLoginLogo) }}" alt="logo" class="h-6 w-auto">
                <h6 class="mb-0 fw-bold text-dark fs-6">@lang('Recovery Codes')</h6>
              </div>
              <span class="badge bg-label-danger px-2 py-1 x-small fw-bold">@lang('Backup')</span>
            </div>

            <!-- Shield Header / Guidance -->
            <div class="alert alert-warning border-0 shadow-none d-flex gap-2 align-items-start mb-3 py-2 px-3 rounded-2" style="background-color: #fffbeb;">
                <i class="la la-shield-alt text-warning fs-3 mt-0.5" style="color: #d97706 !important;"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-0.5 fs-7 text-warning-800">@lang('Save Your Recovery Codes')</h6>
                    <p class="mb-0 x-small text-muted" style="line-height: 1.35;">@lang('Use one code per sign-in if you lose your authenticator. Each code works only once.')</p>
                </div>
            </div>

            <!-- Warning Box -->
            <div class="alert alert-danger border-0 d-flex gap-2 align-items-center mb-3 py-2 px-3 rounded-2" style="background-color: #fef2f2;">
                <i class="la la-exclamation-triangle text-danger fs-5" style="color: #ef4444 !important;"></i>
                <p class="mb-0 x-small fw-semibold text-danger" style="line-height: 1.25;">
                    <strong>@lang('Shown once:')</strong> @lang('Copy or print now and store offline. You cannot view them again.')
                </p>
            </div>

            <!-- Recovery Codes 2-Column Grid -->
            <div class="bg-lighter rounded-3 border p-3 mb-3 text-center" id="recoveryCodesBox">
                <div class="row g-2">
                    @foreach($codes as $code)
                        <div class="col-6">
                            <code class="d-block py-2 px-2 bg-white rounded border fs-6 fw-bold tracking-widest text-dark select-all">{{ $code }}</code>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions: Copy & Print Inline -->
            <div class="d-flex gap-2 mb-3">
                <button type="button"
                        class="btn btn-outline-secondary flex-grow-1 py-2 px-3 fw-bold d-inline-flex align-items-center justify-content-center gap-1.5"
                        id="copyRecoveryCodes"
                        data-codes="{{ e(json_encode(implode("\n", $codes))) }}"
                        style="border-radius: 8px !important; font-size: 13px;">
                    <i class="la la-copy fs-5"></i> @lang('Copy All')
                </button>
                <button type="button" 
                        class="btn btn-outline-secondary flex-grow-1 py-2 px-3 fw-bold d-inline-flex align-items-center justify-content-center gap-1.5" 
                        id="printRecoveryCodes"
                        style="border-radius: 8px !important; font-size: 13px;">
                    <i class="la la-print fs-5"></i> @lang('Print Codes')
                </button>
            </div>

            <!-- Continue Button -->
            <div class="border-top pt-3">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary d-grid w-100 py-2.5 shadow-sm fw-bold d-inline-flex align-items-center justify-content-center gap-2" id="adminRecoveryContinueBtn" style="border-radius: 8px !important;">
                    <i class="la la-check-circle fs-5"></i> @lang("I've saved these — continue")
                </a>
            </div>

          </div>
        </div>
      </div>
    </div>
</div>

@push('script')
<script>
(function() {
    var copyBtn = document.getElementById('copyRecoveryCodes');
    var printBtn = document.getElementById('printRecoveryCodes');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            var raw = this.getAttribute('data-codes');
            try { raw = raw ? JSON.parse(raw) : ''; } catch (e) { raw = ''; }
            var codes = (raw || '').trim();
            if (!codes) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(codes).then(function() {
                    if (typeof notify === 'function') notify('success', @json(__('Recovery codes copied to clipboard.')));
                    else alert(@json(__('Recovery codes copied to clipboard.')));
                }).catch(function() { fallbackCopy(codes); });
            } else {
                fallbackCopy(codes);
            }
        });
    }
    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            if (typeof notify === 'function') notify('success', @json(__('Recovery codes copied.')));
            else alert(@json(__('Recovery codes copied.')));
        } catch (e) {}
        document.body.removeChild(ta);
    }
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            var box = document.getElementById('recoveryCodesBox');
            if (!box) return;
            var title = @json(__('Recovery Codes'));
            var h1 = @json(__('Save Your Recovery Codes'));
            var note = @json(__('Store these in a safe place. Each code works only once.'));
            var css = 'body{font:14px/1.45 system-ui,-apple-system,sans-serif;color:#111827;padding:24px;max-width:520px;margin:0 auto;}h1{font-size:15px;margin:0 0 6px;font-weight:600;}p{font-size:12px;color:#6b7280;margin:0 0 14px;}pre{font:12px/1.5 ui-monospace,Menlo,Consolas,monospace;margin:0;padding:12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;white-space:pre-wrap;word-break:break-all;}';
            var w = window.open('', '_blank');
            w.document.write('<html><head><title>' + title + '</title><style>' + css + '</style></head><body><h1>' + h1 + '</h1><p>' + note + '</p><pre>' + (box.innerText || box.textContent || '') + '</pre></body></html>');
            w.document.close();
            w.print();
            w.close();
        });
    }
})();
</script>
@endpush
@endsection
