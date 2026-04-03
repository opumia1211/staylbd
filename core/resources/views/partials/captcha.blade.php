@php
	$customCaptcha = loadCustomCaptcha();
    $googleCaptcha = loadReCaptcha()
@endphp
@if($googleCaptcha)
    <div class="mb-3">
        @php echo $googleCaptcha @endphp
        <div class="g-recaptcha-error text-danger small mt-1" role="alert"></div>
    </div>
@endif

@if($customCaptcha)
    <div class="form-group">
        <div class="mb-2">
            @php echo $customCaptcha @endphp
        </div>
        <label class="form-label">@lang('Captcha')</label>
        <input type="text" name="captcha" class="form-control form--control {{ $class }}" required>
    </div>
@endif
@if($googleCaptcha)
@push('script')
    <script>
        (function($){
            "use strict";
            $(document).on('submit', '.verify-gcaptcha', function(){
                if (typeof grecaptcha === 'undefined' || !grecaptcha.getResponse) return true;
                var response = grecaptcha.getResponse();
                if (response.length === 0) {
                    var errEl = $(this).find('.g-recaptcha-error')[0] || document.getElementById('g-recaptcha-error');
                    if (errEl) errEl.innerHTML = '<span class="text-danger">@lang("Captcha field is required.")</span>';
                    return false;
                }
                return true;
            });
        })(jQuery);
    </script>
@endpush
@endif
