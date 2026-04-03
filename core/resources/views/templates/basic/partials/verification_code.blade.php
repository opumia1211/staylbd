<div class="mb-3">
    <label>@lang('Verification Code')</label>
    <div class="verification-code">
        <input type="text" name="code" id="verification-code" class="form-control overflow-hidden" required autocomplete="off">
        <div class="boxes">
            <span>-</span>
            <span>-</span>
            <span>-</span>
            <span>-</span>
            <span>-</span>
            <span>-</span>
        </div>
    </div>
</div>

@push('script')
    <script>
        $('#verification-code').on('input', function () {
            $(this).val(function(i, val){
                if (val.length >= 6) {
                    $('.submit-form').find('button[type=submit]').html('<svg class="ui-icon ui-icon--spin" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 2a10 10 0 1 0 10 10"></path></svg>');
                    $('.submit-form').submit()
                }
                if(val.length > 6){
                    return val.substring(0, val.length - 1);
                }
                return val;
            });
            for (let index = $(this).val().length; index >= 0 ; index--) {
                $($('.boxes span')[index]).html('');
            }
        });
    </script>
@endpush
