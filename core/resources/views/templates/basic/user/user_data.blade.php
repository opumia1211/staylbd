@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <div class="row justify-content-center card--wrapper">
        <div class="col-md-8 col-lg-7 col-xl-6">
            <div class="card custom--card">
                <div class="card-header">
                    <h5 class="card-title">{{ __($pageTitle) }}</h5>
                    <p class="mb-0 small text-muted">@lang('Complete all required fields to continue. Required for social login users.')</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.data.submit') }}" id="userDataForm">
                        @csrf
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('First Name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form--control" name="firstname" value="{{ old('firstname', $user->firstname) }}" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Last Name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form--control" name="lastname" value="{{ old('lastname', $user->lastname) }}" required>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Country') <span class="text-danger">*</span></label>
                                <select name="country" class="form-control form--control form-select user-data-country" required>
                                    @foreach ($countries as $key => $country)
                                        <option data-mobile_code="{{ $country->dial_code }}" data-code="{{ $key }}" value="{{ $country->country }}" {{ (old('country', optional($user->address)->country) == $country->country) ? 'selected' : '' }}>{{ __($country->country) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Mobile') <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text mobile-code-display bg--base text--white"></span>
                                    <input type="hidden" name="mobile_code" class="user-data-mobile-code">
                                    <input type="hidden" name="country_code" class="user-data-country-code">
                                    <input type="number" name="mobile" value="{{ old('mobile') }}" class="form-control form--control" placeholder="@lang('Without country code')" required>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Age') <span class="text-danger">*</span></label>
                                <input type="number" name="age" value="{{ old('age', $user->age) }}" class="form-control form--control" min="13" max="120" required>
                                <small class="text-muted">@lang('Minimum 13 years')</small>
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Address')</label>
                                <input type="text" class="form-control form--control" name="address" value="{{ old('address', optional($user->address)->address ?? '') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('State')</label>
                                <input type="text" class="form-control form--control" name="state" value="{{ old('state', optional($user->address)->state ?? '') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Zip Code')</label>
                                <input type="text" class="form-control form--control" name="zip" value="{{ old('zip', optional($user->address)->zip ?? '') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('City')</label>
                                <input type="text" class="form-control form--control" name="city" value="{{ old('city', optional($user->address)->city ?? '') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn cmn--btn btn--base w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
(function() {
    function updateMobileCode() {
        var sel = document.querySelector('.user-data-country');
        if (!sel || !sel.options[sel.selectedIndex]) return;
        var opt = sel.options[sel.selectedIndex];
        var code = opt.getAttribute('data-code');
        var dial = opt.getAttribute('data-mobile_code');
        document.querySelectorAll('.user-data-mobile-code').forEach(function(el) { el.value = dial || ''; });
        document.querySelectorAll('.user-data-country-code').forEach(function(el) { el.value = code || ''; });
        document.querySelectorAll('.mobile-code-display').forEach(function(el) { el.textContent = dial ? '+' + dial : ''; });
    }
    document.querySelectorAll('.user-data-country').forEach(function(el) {
        el.addEventListener('change', updateMobileCode);
    });
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', updateMobileCode);
    else updateMobileCode();
})();
</script>
@endpush
