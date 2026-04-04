@php
    $registerContent = getContent('register.content', true);
    if (!$registerContent || !isset($registerContent->data_values)) {
        $registerContent = (object)['data_values' => (object)['heading' => __('Register'), 'subheading' => '']];
    }
    $policyPages = getContent('policy_pages.element', false, null, true);
    if (!isset($countries)) {
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
    }
    $general = $general ?? gs();
@endphp
<div class="account-header account-header--compact">
    <h5 class="title mb-0">{{ __(@$registerContent->data_values->heading) }}</h5>
    @php $regSub = trim((string)@$registerContent->data_values->subheading ?? ''); @endphp
    @if($regSub !== '' && stripos($regSub, 'Lorem ipsum') === false)
        <p class="mb-0 fs--14px mt-1">{{ __($regSub) }}</p>
    @endif
</div>
<form action="{{ route('user.register') }}" method="POST" class="auth-form-placeholder {{ isRegistrationFieldEnabled('captcha') ? 'verify-gcaptcha' : '' }} register-form-pro register-form-compact" id="modalRegisterForm" autocomplete="off" {{ isRegistrationFieldEnabled('profile_photo') ? 'enctype="multipart/form-data"' : '' }}>
    @csrf
    <input type="hidden" name="redirect" value="{{ request()->input('redirect', route('user.home')) }}">
    @if (isRegistrationFieldEnabled('referBy') && session()->get('reference'))
        <input type="hidden" name="reference" value="{{ session()->get('reference') }}">
    @endif
    @if (isRegistrationFieldEnabled('firstname'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Name')</label>
        <input type="text" class="form-control form--control" name="firstname" value="{{ old('firstname') }}" placeholder="@lang('Name')" required maxlength="100" autocomplete="name">
    </div>
    @endif
    @if (isRegistrationFieldEnabled('username'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Username')</label>
        <div class="input-group username-input-wrap">
            <input type="text" class="form-control form--control checkUser" id="modal_register_username" name="username" value="" {{ isRegistrationFieldEnabled('username') ? 'required' : '' }} minlength="6" maxlength="30" placeholder="@lang('Username')" autocomplete="username">
            <span class="username-available-tick d-none" aria-hidden="true" title="@lang('Available')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path fill="#047857" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            </span>
        </div>
        <small class="text-danger usernameExist"></small>
        @if($errors->has('username'))
            <div class="invalid-feedback d-block">{{ $errors->first('username') }}</div>
        @endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('email'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('E-Mail Address')</label>
        <input type="text" class="form-control form--control" name="email" value="" {{ isRegistrationFieldEnabled('email') ? 'required' : '' }} placeholder="@lang('Email or Mobile number')" autocomplete="email">
        @if($errors->has('email'))
            <div class="invalid-feedback d-block">{{ $errors->first('email') }}</div>
        @endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('country'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Country')</label>
        <select name="country" class="form-control form--control form-select" {{ isRegistrationFieldEnabled('country') ? 'required' : '' }}>
            <option value="">@lang('Select Country')</option>
            @foreach ($countries as $key => $country)
                <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}" @selected(old('country') == $country->country)>{{ __($country->country) }}</option>
            @endforeach
        </select>
    </div>
    @endif
    @if (isRegistrationFieldEnabled('mobile'))
    @php
        $countryDialMap = [];
        $dialToCountry = [];
        foreach ($countries as $k => $v) {
            $countryDialMap[$k] = $v->dial_code;
            if (!isset($dialToCountry[$v->dial_code])) {
                $dialToCountry[$v->dial_code] = $k;
            }
        }
        $asiaFirst = ['BD','IN','PK','LK','NP','BT','MV','AF','MM','TH','KH','LA','VN','ID','MY','SG','PH','BN','TL','CN','JP','KR','KP','TW','MN','KZ','UZ','TM','TJ','KG','RU','AM','AZ','GE','TR','IR','IQ','SA','AE','YE','OM','KW','BH','QA','JO','SY','LB','IL','PS'];
        $countriesArr = (array) $countries;
        uksort($countriesArr, function ($a, $b) use ($asiaFirst) {
            $pa = array_search($a, $asiaFirst);
            $pb = array_search($b, $asiaFirst);
            if ($pa !== false && $pb !== false) return $pa - $pb;
            if ($pa !== false) return -1;
            if ($pb !== false) return 1;
            return strcmp($a, $b);
        });
    @endphp
    <div class="form-group">
        <label class="form--label sr-only">@lang('Phone number')</label>
        <div class="input-group phone-country-input-group" data-country-dial="{{ json_encode($countryDialMap) }}" data-dial-to-country="{{ json_encode($dialToCountry) }}">
            <input type="text" class="form-control form--control phone-country-search" id="phone-country-search" style="max-width: 100px;" placeholder="@lang('Select or type code')" value="{{ old('country_code') && old('mobile_code') ? old('country_code').' +'.old('mobile_code') : '' }}" autocomplete="off" aria-label="@lang('Country code')" list="country-codes-datalist" {{ isRegistrationFieldEnabled('mobile') ? 'required' : '' }}>
            <datalist id="country-codes-datalist">
                @foreach ($countriesArr as $key => $country)
                    <option value="{{ $key }} +{{ $country->dial_code }}">
                @endforeach
            </datalist>
            <input type="hidden" name="country_code" value="{{ old('country_code') }}">
            <input type="hidden" name="mobile_code" value="{{ old('mobile_code') }}">
            <input type="number" name="mobile" value="{{ old('mobile') }}" class="form-control form--control" placeholder="@lang('Phone number')" {{ isRegistrationFieldEnabled('mobile') ? 'required' : '' }} autocomplete="tel">
        </div>
        @if($errors->has('mobile'))
            <div class="invalid-feedback d-block">{{ $errors->first('mobile') }}</div>
        @endif
        @if($errors->has('country_code'))
            <div class="invalid-feedback d-block">{{ $errors->first('country_code') }}</div>
        @endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('address'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Address')</label>
        <input type="text" class="form-control form--control" name="address" value="{{ old('address') }}" placeholder="@lang('Street address')" {{ isRegistrationFieldEnabled('address') ? 'required' : '' }} maxlength="255">
        @if($errors->has('address'))<div class="invalid-feedback d-block">{{ $errors->first('address') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('city'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('City')</label>
        <input type="text" class="form-control form--control" name="city" value="{{ old('city') }}" placeholder="@lang('City')" {{ isRegistrationFieldEnabled('city') ? 'required' : '' }} maxlength="100">
        @if($errors->has('city'))<div class="invalid-feedback d-block">{{ $errors->first('city') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('state'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('State / Province')</label>
        <input type="text" class="form-control form--control" name="state" value="{{ old('state') }}" placeholder="@lang('State / Province')" {{ isRegistrationFieldEnabled('state') ? 'required' : '' }} maxlength="100">
        @if($errors->has('state'))<div class="invalid-feedback d-block">{{ $errors->first('state') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('zip'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('ZIP / Postal code')</label>
        <input type="text" class="form-control form--control" name="zip" value="{{ old('zip') }}" placeholder="@lang('ZIP / Postal code')" {{ isRegistrationFieldEnabled('zip') ? 'required' : '' }} maxlength="20">
        @if($errors->has('zip'))<div class="invalid-feedback d-block">{{ $errors->first('zip') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('division') || isRegistrationFieldEnabled('district') || isRegistrationFieldEnabled('thana'))
    <div class="form-group">
        @if (isRegistrationFieldEnabled('division'))
        <input type="text" class="form-control form--control mb-1" name="division" value="{{ old('division') }}" placeholder="@lang('Division (Bangladesh)')" maxlength="100">
        @endif
        @if (isRegistrationFieldEnabled('district'))
        <input type="text" class="form-control form--control mb-1" name="district" value="{{ old('district') }}" placeholder="@lang('District (Bangladesh)')" maxlength="100">
        @endif
        @if (isRegistrationFieldEnabled('thana'))
        <input type="text" class="form-control form--control" name="thana" value="{{ old('thana') }}" placeholder="@lang('Thana (Bangladesh)')" maxlength="100">
        @endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('alternate_phone'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Alternate phone')</label>
        <input type="text" class="form-control form--control" name="alternate_phone" value="{{ old('alternate_phone') }}" placeholder="@lang('Alternate phone')" {{ isRegistrationFieldEnabled('alternate_phone') ? 'required' : '' }} maxlength="30">
        @if($errors->has('alternate_phone'))<div class="invalid-feedback d-block">{{ $errors->first('alternate_phone') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('age'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Age')</label>
        <input type="number" class="form-control form--control" name="age" value="{{ old('age') }}" placeholder="@lang('Your age (min 13)')" min="13" max="120" {{ isRegistrationFieldEnabled('age') ? 'required' : '' }}>
        @if($errors->has('age'))<div class="invalid-feedback d-block">{{ $errors->first('age') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('gender'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Gender')</label>
        <select name="gender" class="form-control form--control form-select" {{ isRegistrationFieldEnabled('gender') ? 'required' : '' }}>
            <option value="">@lang('Select Gender')</option>
            <option value="male" @selected(old('gender') == 'male')>@lang('Male')</option>
            <option value="female" @selected(old('gender') == 'female')>@lang('Female')</option>
            <option value="other" @selected(old('gender') == 'other')>@lang('Other')</option>
        </select>
        @if($errors->has('gender'))<div class="invalid-feedback d-block">{{ $errors->first('gender') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('date_of_birth'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Date of birth')</label>
        <input type="date" class="form-control form--control" name="date_of_birth" value="{{ old('date_of_birth') }}" {{ isRegistrationFieldEnabled('date_of_birth') ? 'required' : '' }}>
        @if($errors->has('date_of_birth'))<div class="invalid-feedback d-block">{{ $errors->first('date_of_birth') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('occupation'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Occupation')</label>
        <input type="text" class="form-control form--control" name="occupation" value="{{ old('occupation') }}" placeholder="@lang('Occupation')" {{ isRegistrationFieldEnabled('occupation') ? 'required' : '' }} maxlength="100">
        @if($errors->has('occupation'))<div class="invalid-feedback d-block">{{ $errors->first('occupation') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('company_name'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Company name')</label>
        <input type="text" class="form-control form--control" name="company_name" value="{{ old('company_name') }}" placeholder="@lang('Company name')" {{ isRegistrationFieldEnabled('company_name') ? 'required' : '' }} maxlength="150">
        @if($errors->has('company_name'))<div class="invalid-feedback d-block">{{ $errors->first('company_name') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('website'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Website')</label>
        <input type="url" class="form-control form--control" name="website" value="{{ old('website') }}" placeholder="@lang('Website')" {{ isRegistrationFieldEnabled('website') ? 'required' : '' }} maxlength="255">
        @if($errors->has('website'))<div class="invalid-feedback d-block">{{ $errors->first('website') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('tax_id'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Tax ID / VAT number')</label>
        <input type="text" class="form-control form--control" name="tax_id" value="{{ old('tax_id') }}" placeholder="@lang('Tax ID / VAT number')" {{ isRegistrationFieldEnabled('tax_id') ? 'required' : '' }} maxlength="50">
        @if($errors->has('tax_id'))<div class="invalid-feedback d-block">{{ $errors->first('tax_id') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('nid_number'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('NID / Passport number')</label>
        <input type="text" class="form-control form--control" name="nid_number" value="{{ old('nid_number') }}" placeholder="@lang('NID / Passport number')" {{ isRegistrationFieldEnabled('nid_number') ? 'required' : '' }} maxlength="50">
        @if($errors->has('nid_number'))<div class="invalid-feedback d-block">{{ $errors->first('nid_number') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('preferred_language'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Preferred language')</label>
        <input type="text" class="form-control form--control" name="preferred_language" value="{{ old('preferred_language') }}" placeholder="@lang('Preferred language')" {{ isRegistrationFieldEnabled('preferred_language') ? 'required' : '' }} maxlength="40">
        @if($errors->has('preferred_language'))<div class="invalid-feedback d-block">{{ $errors->first('preferred_language') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('profile_photo'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('Profile photo')</label>
        <input type="file" class="form-control form--control" name="profile_photo" accept="image/*">
        @if($errors->has('profile_photo'))<div class="invalid-feedback d-block">{{ $errors->first('profile_photo') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('newsletter_subscribe'))
    <div class="form-group form-check">
        <input type="checkbox" name="newsletter_subscribe" id="modal_newsletter" class="form-check-input" value="1" {{ old('newsletter_subscribe') ? 'checked' : '' }}>
        <label for="modal_newsletter" class="form-check-label">@lang('Subscribe to newsletter')</label>
    </div>
    @endif
    @if (isRegistrationFieldEnabled('how_heard'))
    <div class="form-group">
        <label class="form--label sr-only">@lang('How did you hear about us?')</label>
        <select name="how_heard" class="form-control form--control form-select" {{ isRegistrationFieldEnabled('how_heard') ? 'required' : '' }}>
            <option value="">@lang('Select')</option>
            <option value="search" @selected(old('how_heard') == 'search')>@lang('Search engine')</option>
            <option value="friend" @selected(old('how_heard') == 'friend')>@lang('Friend / Referral')</option>
            <option value="social" @selected(old('how_heard') == 'social')>@lang('Social media')</option>
            <option value="ad" @selected(old('how_heard') == 'ad')>@lang('Advertisement')</option>
            <option value="other" @selected(old('how_heard') == 'other')>@lang('Other')</option>
        </select>
        @if($errors->has('how_heard'))<div class="invalid-feedback d-block">{{ $errors->first('how_heard') }}</div>@endif
    </div>
    @endif
    @if (isRegistrationFieldEnabled('password'))
    <div class="form-group password-field">
        <label class="form--label sr-only" for="modal_register_password">@lang('Password')</label>
        <div class="password-input-wrap">
            <input id="modal_register_password" type="password" class="form-control form--control {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password" required placeholder="@lang('Password')" autocomplete="new-password">
            <button type="button" class="password-toggle" onclick="togglePassword('modal_register_password'); this.querySelector('.pwd-icon-show').classList.toggle('d-none'); this.querySelector('.pwd-icon-hide').classList.toggle('d-none');" title="@lang('Show password')" aria-label="@lang('Show password')">
                <span class="pwd-icon-show" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
                <span class="pwd-icon-hide d-none" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg></span>
            </button>
        </div>
        @if($errors->has('password'))
            <div class="invalid-feedback d-block">{{ $errors->first('password') }}</div>
        @endif
    </div>
    <div class="form-group password-field">
        <label class="form--label sr-only" for="modal_register_password_confirm">@lang('Confirm Password')</label>
        <div class="password-input-wrap">
            <input id="modal_register_password_confirm" type="password" class="form-control form--control" name="password_confirmation" required placeholder="@lang('Confirm Password')" autocomplete="new-password">
            <button type="button" class="password-toggle" onclick="togglePassword('modal_register_password_confirm'); this.querySelector('.pwd-icon-show').classList.toggle('d-none'); this.querySelector('.pwd-icon-hide').classList.toggle('d-none');" title="@lang('Show password')" aria-label="@lang('Show password')">
                <span class="pwd-icon-show" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
                <span class="pwd-icon-hide d-none" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg></span>
            </button>
        </div>
    </div>
    @endif
    @if (isRegistrationFieldEnabled('captcha'))
        <x-captcha />
    @endif
    @if(($general->agree ?? false) && isRegistrationFieldEnabled('agree'))
        <div class="form-group form-check">
            <input type="checkbox" id="modal_agree" name="agree" class="form-check-input" required>
            <label for="modal_agree" class="form-check-label">@lang('I agree with')</label>
        </div>
    @endif
    <div class="form-group">
        <button type="submit" class="auth-btn">@lang('Register')</button>
    </div>
    <p class="mb-0 small">@lang('Already have an account?') <a href="{{ route('user.login') }}" class="switch-auth">@lang('Login')</a></p>
</form>
