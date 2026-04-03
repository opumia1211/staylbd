@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="d-flex justify-content-center card--wrapper">
        <div class="verification-code-wrapper custom--card">
            <div class="verification-area">
                <h5 class="pb-3 text-center border-bottom">@lang('2FA Verification')</h5>
                <form action="{{ route('user.go2fa.verify') }}" method="POST" class="submit-form">
                    @csrf

                    @include($activeTemplate . 'partials.verification_code')

                    <div class="form--group">
                        <button type="submit" class="btn btn--base cmn--btn w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
