@extends($activeTemplate . 'layouts.master')
@section('dashboard_page_title')
    @include($activeTemplate . 'partials.dashboard_page_header', ['title' => __('Profile'), 'subtitle' => __('Manage your account and addresses')])
@endsection
@section('content')
@php
    $profileGroups = [];
    if (function_exists('registrationFieldsListGrouped')) {
        $grouped = registrationFieldsListGrouped();
        foreach ($grouped as $groupKey => $group) {
            if (in_array($groupKey, ['security', 'legal'], true)) continue;
            $group = (array) $group;
            $fields = $group['fields'] ?? [];
            $enabled = [];
            foreach ($fields as $fkey => $label) {
                if (in_array($fkey, ['captcha', 'password', 'agree', 'referBy'], true)) continue;
                if (function_exists('isProfileFieldEnabled') && isProfileFieldEnabled($fkey)) {
                    $enabled[$fkey] = $label;
                } elseif (in_array($fkey, ['firstname', 'lastname', 'username', 'email', 'mobile', 'country', 'address', 'city', 'state', 'zip', 'division', 'district', 'thana', 'telegram', 'whatsapp', 'profile_photo'], true)) {
                    $enabled[$fkey] = $label;
                }
            }
            if (!empty($enabled)) {
                $profileGroups[$groupKey] = array_merge($group, ['fields' => $enabled]);
            }
        }
    }
@endphp
@php
    $userAddress = $user->address ?? (object)[];
    $userKyc = $user->kyc_data ?? (object)[];
@endphp
<div class="profile-page profile-page--compact">
    <form action="{{ route('user.profile.setting') }}" method="post" enctype="multipart/form-data" class="profile-edit-form">
        @csrf
        <div class="row g-1 g-md-2">
            <div class="col-lg-8">
                {{-- 1. Header: Avatar + Name + Username --}}
                <div class="profile-header-card card border-0 shadow-sm mb-1 mb-md-2 profile-section">
                    <div class="card-body py-2 px-2 d-flex align-items-center gap-2">
                        <label class="profile-avatar-wrap d-inline-block mb-0 flex-shrink-0" for="profile-image" title="@lang('Upload photo (PNG/JPG, max 2MB)')">
                            <img class="profile-avatar-img rounded-circle" src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) }}" alt="{{ $user->fullname }}" loading="lazy" width="40" height="40" @if(!$user->image) style="display:none;" @endif>
                            <span class="profile-avatar-placeholder rounded-circle" @if($user->image) style="display:none;" @endif>@include($activeTemplate . 'partials.icon', ['name' => 'camera'])</span>
                        </label>
                        <input type="file" name="image" id="profile-image" class="d-none" accept="image/png,image/jpeg,image/jpg">
                        <div class="min-w-0 flex-grow-1">
                            <h6 class="mb-0 profile-name">{{ $user->firstname }} {{ $user->lastname }}</h6>
                            <p class="text-muted small mb-0 profile-username">{{ '@' . $user->username }}</p>
                            <button type="button" class="btn btn-link btn-sm text-muted p-0 remove-profile-img" style="font-size:0.7rem" title="@lang('Remove photo')">@include($activeTemplate . 'partials.icon', ['name' => 'times']) @lang('Remove')</button>
                        </div>
                    </div>
                </div>

                @auth
                {{-- Header hides mobile notification icon; quick access from profile --}}
                <div class="card border-0 shadow-sm mb-1 mb-md-2 profile-section">
                    <div class="card-body py-2 px-2 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <div class="d-flex align-items-center gap-2 min-w-0">
                            @include($activeTemplate . 'partials.icon', ['name' => 'bell', 'class' => 'text-primary flex-shrink-0'])
                            <span class="fw-semibold small mb-0">@lang('Notifications')</span>
                            @if(($userNotificationCount ?? 0) > 0)
                                <span class="badge bg-danger rounded-pill">{{ $userNotificationCount > 99 ? '99+' : $userNotificationCount }}</span>
                            @endif
                        </div>
                        <a href="{{ route('user.notifications') }}" class="btn btn-sm btn-outline-primary flex-shrink-0" data-dashboard-link="1">@lang('View')</a>
                    </div>
                </div>
                @endauth

                {{-- 2. Basic Information --}}
                <div class="profile-card card border-0 shadow-sm mb-1 mb-md-2 profile-section">
                    <div class="card-header bg-light border-0 profile-card-header px-2">
                        <h6 class="card-title mb-0 profile-section-title">@include($activeTemplate . 'partials.icon', ['name' => 'user', 'class' => 'me-1 text-primary'])@lang('Basic Information')</h6>
                    </div>
                    <div class="card-body profile-card-body px-2">
                        <div class="row g-1 g-md-2">
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0 fw-semibold">@lang('First Name')</label>
                                <input type="text" class="form-control form-control-sm" name="firstname" value="{{ $user->firstname }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0 fw-semibold">@lang('Last Name')</label>
                                <input type="text" class="form-control form-control-sm" name="lastname" value="{{ $user->lastname }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0 fw-semibold">@lang('E-mail')</label>
                                <input type="email" class="form-control form-control-sm {{ $errors->has('email') ? 'is-invalid' : '' }}" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0 fw-semibold">@lang('Username')</label>
                                <input type="text" class="form-control form-control-sm {{ $errors->has('username') ? 'is-invalid' : '' }}" name="username" value="{{ old('username', $user->username) }}" minlength="6" maxlength="40" pattern="[a-z0-9_]+" required>
                                @error('username')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Contact & Location (Default address) --}}
                <div class="profile-card card border-0 shadow-sm mb-1 mb-md-2 profile-section">
                    <div class="card-header bg-light border-0 profile-card-header px-2">
                        <h6 class="card-title mb-0 profile-section-title">@include($activeTemplate . 'partials.icon', ['name' => 'map-marker-alt', 'class' => 'me-1 text-primary'])@lang('Contact & Location')</h6>
                    </div>
                    <div class="card-body profile-card-body px-2">
                        <div class="row g-1 g-md-2">
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0 fw-semibold">@lang('Mobile')</label>
                                <input type="text" class="form-control form-control-sm {{ $errors->has('mobile') ? 'is-invalid' : '' }}" name="mobile" value="{{ old('mobile', $user->mobile) }}" placeholder="01XXXXXXXXX" maxlength="30">
                                @error('mobile')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            @if(function_exists('isProfileFieldEnabled') && isProfileFieldEnabled('alternate_phone'))
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0">@lang('Alternate phone')</label>
                                <input type="text" class="form-control form-control-sm" name="alternate_phone" value="{{ old('alternate_phone', $userKyc->alternate_phone ?? '') }}" maxlength="30">
                            </div>
                            @endif
                            <div class="col-12"><hr class="my-1"></div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small mb-0">@lang('Country')</label>
                                <select name="country" id="profileCountry" class="form-select form-select-sm">
                                    <option value="">@lang('Select')</option>
                                    @foreach ($countries as $c)
                                        @php $countryName = is_object($c) ? ($c->country ?? '') : ($c['country'] ?? ''); @endphp
                                        <option value="{{ $countryName }}" @selected((optional($userAddress)->country ?? '') == $countryName)>{{ __($countryName) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @php $isBd = stripos(optional($userAddress)->country ?? '', 'Bangladesh') !== false; @endphp
                            <div class="col-12 col-md-4 profile-bd-field" id="profileWrapDivision" style="{{ $isBd ? '' : 'display:none' }}">
                                <label class="form-label small mb-0">@lang('Division')</label>
                                <select name="division" id="profileDivision" class="form-select form-select-sm">
                                    <option value="">@lang('Select')</option>
                                    @foreach ($divisionList as $div)
                                        @php $divId = is_array($div) ? ($div['id'] ?? '') : ($div->id ?? ''); $nameEn = is_array($div) ? ($div['name_en'] ?? '') : ($div->name_en ?? ''); $nameBn = is_array($div) ? ($div['name_bn'] ?? '') : ($div->name_bn ?? ''); @endphp
                                        <option value="{{ $nameEn }}" data-id="{{ $divId }}" @selected((optional($userAddress)->division ?? '') == $nameEn)>{{ $nameEn }}@if($nameBn) / {{ $nameBn }}@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 profile-bd-field" id="profileWrapDistrict" style="{{ $isBd ? '' : 'display:none' }}">
                                <label class="form-label small mb-0">@lang('District')</label>
                                <select id="profileDistrict" class="form-select form-select-sm" name="city">
                                    <option value="">@lang('Select division first')</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4 profile-bd-field" id="profileWrapThana" style="{{ $isBd ? '' : 'display:none' }}">
                                <label class="form-label small mb-0">@lang('Thana')</label>
                                <select name="thana" id="profileThana" class="form-select form-select-sm">
                                    <option value="">@lang('Select district first')</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4 profile-other-field" id="profileWrapCityState" style="{{ $isBd ? 'display:none' : '' }}">
                                <label class="form-label small mb-0">@lang('City')</label>
                                <input type="text" class="form-control form-control-sm" name="city" id="profileCity" value="{{ optional($userAddress)->city }}" placeholder="@lang('City')">
                            </div>
                            <div class="col-12 col-md-4 profile-other-field" id="profileWrapState" style="{{ $isBd ? 'display:none' : '' }}">
                                <label class="form-label small mb-0">@lang('State')</label>
                                <input type="text" class="form-control form-control-sm" name="state" value="{{ optional($userAddress)->state }}" placeholder="@lang('State')">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small mb-0">@lang('Postal Code')</label>
                                <input type="text" class="form-control form-control-sm" name="zip" value="{{ optional($userAddress)->zip }}" placeholder="@lang('ZIP')">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0">@lang('Address')</label>
                                <input type="text" class="form-control form-control-sm" name="address" value="{{ optional($userAddress)->address }}" placeholder="@lang('Street, house no.')">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0">@lang('Address 2')</label>
                                <input type="text" class="form-control form-control-sm" name="address_2" value="{{ optional($userAddress)->address_2 ?? '' }}" placeholder="@lang('Optional')">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-card card border-0 shadow-sm mb-1 mb-md-2 profile-section">
                    <div class="card-header bg-light border-0 profile-card-header px-2">
                        <h6 class="card-title mb-0 profile-section-title">@include($activeTemplate . 'partials.icon', ['name' => 'comments', 'class' => 'me-1 text-primary'])@lang('Messaging')</h6>
                    </div>
                    <div class="card-body profile-card-body px-2">
                        <div class="row g-1 g-md-2">
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0 fw-semibold">@lang('WhatsApp Number')</label>
                                <input type="text" class="form-control form-control-sm" name="whatsapp_identity" maxlength="32" value="{{ old('whatsapp_identity', $user->whatsapp_identity ?: preg_replace('/\D+/', '', $user->mobile)) }}" placeholder="8801XXXXXXXXX">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0 fw-semibold">@lang('Telegram') <span class="text-muted" style="font-size:0.7rem">(without @)</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">@</span>
                                    <input type="text" class="form-control" name="telegram_username" maxlength="32" value="{{ old('telegram_username', $user->telegram_username) }}" placeholder="username">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch py-0">
                                    <input type="hidden" name="contact_channel_opt_in" value="0">
                                    <input class="form-check-input" type="checkbox" name="contact_channel_opt_in" id="contactChannelOptIn" value="1" {{ old('contact_channel_opt_in', $user->contact_channel_opt_in ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="contactChannelOptIn">@lang('Allow WhatsApp/Telegram in contact center')</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 5. Profile (optional fields – only if any enabled, same order as admin) --}}
                @if(function_exists('isProfileFieldEnabled') && (isProfileFieldEnabled('age') || isProfileFieldEnabled('gender') || isProfileFieldEnabled('date_of_birth') || isProfileFieldEnabled('occupation') || isProfileFieldEnabled('company_name') || isProfileFieldEnabled('website') || isProfileFieldEnabled('nid_number') || isProfileFieldEnabled('tax_id') || isProfileFieldEnabled('preferred_language')))
                <div class="profile-card card border-0 shadow-sm mb-1 mb-md-2 profile-section">
                    <div class="card-header bg-light border-0 profile-card-header px-2">
                        <h6 class="card-title mb-0 profile-section-title">@include($activeTemplate . 'partials.icon', ['name' => 'id-card', 'class' => 'me-1 text-primary'])@lang('Profile')</h6>
                    </div>
                    <div class="card-body profile-card-body px-2">
                        <div class="row g-1 g-md-2">
                            @if(isProfileFieldEnabled('age'))
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0">@lang('Age')</label>
                                <input type="number" class="form-control form-control-sm" name="age" value="{{ old('age', $user->age) }}" min="13" max="120" placeholder="@lang('Age')">
                            </div>
                            @endif
                            @if(isProfileFieldEnabled('gender'))
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0">@lang('Gender')</label>
                                <select name="gender" class="form-select form-select-sm">
                                    <option value="">@lang('Select')</option>
                                    <option value="male" @selected(old('gender', $user->gender) == 'male')>@lang('Male')</option>
                                    <option value="female" @selected(old('gender', $user->gender) == 'female')>@lang('Female')</option>
                                    <option value="other" @selected(old('gender', $user->gender) == 'other')>@lang('Other')</option>
                                </select>
                            </div>
                            @endif
                            @if(isProfileFieldEnabled('date_of_birth'))
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0">@lang('Date of birth')</label>
                                <input type="date" class="form-control form-control-sm" name="date_of_birth" value="{{ old('date_of_birth', $userKyc->date_of_birth ?? '') }}">
                            </div>
                            @endif
                            @if(isProfileFieldEnabled('occupation'))
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0">@lang('Occupation')</label>
                                <input type="text" class="form-control form-control-sm" name="occupation" value="{{ old('occupation', $userKyc->occupation ?? '') }}" maxlength="100" placeholder="@lang('Occupation')">
                            </div>
                            @endif
                            @if(isProfileFieldEnabled('company_name'))
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0">@lang('Company name')</label>
                                <input type="text" class="form-control form-control-sm" name="company_name" value="{{ old('company_name', $userKyc->company_name ?? '') }}" maxlength="150" placeholder="@lang('Company name')">
                            </div>
                            @endif
                            @if(isProfileFieldEnabled('website'))
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0">@lang('Website')</label>
                                <input type="url" class="form-control form-control-sm" name="website" value="{{ old('website', $userKyc->website ?? '') }}" maxlength="255" placeholder="https://">
                            </div>
                            @endif
                            @if(isProfileFieldEnabled('nid_number'))
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0">@lang('NID / Passport number')</label>
                                <input type="text" class="form-control form-control-sm" name="nid_number" value="{{ old('nid_number', $userKyc->nid_number ?? '') }}" maxlength="50" placeholder="@lang('NID / Passport number')">
                            </div>
                            @endif
                            @if(isProfileFieldEnabled('tax_id'))
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0">@lang('Tax ID / VAT number')</label>
                                <input type="text" class="form-control form-control-sm" name="tax_id" value="{{ old('tax_id', $userKyc->tax_id ?? '') }}" maxlength="50" placeholder="@lang('Tax ID / VAT number')">
                            </div>
                            @endif
                            @if(isProfileFieldEnabled('preferred_language'))
                            <div class="col-12 col-md-6">
                                <label class="form-label small mb-0">@lang('Preferred language')</label>
                                <select name="preferred_language" class="form-select form-select-sm">
                                    <option value="">@lang('Select')</option>
                                    <option value="en" @selected(old('preferred_language', $userKyc->preferred_language ?? '') == 'en')>English</option>
                                    <option value="bn" @selected(old('preferred_language', $userKyc->preferred_language ?? '') == 'bn')>বাংলা</option>
                                </select>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <div class="mb-1 mb-md-2">
                    <button type="submit" class="btn btn--base btn-sm">@include($activeTemplate . 'partials.icon', ['name' => 'save', 'class' => 'me-1'])@lang('Save profile')</button>
                </div>
            </div>

            {{-- Right: Account & security --}}
            <div class="col-lg-4">
                <div class="profile-card card border-0 shadow-sm mb-1 mb-md-2 profile-section">
                    <div class="card-header bg-light border-0 profile-card-header px-2">
                        <h6 class="card-title mb-0 profile-section-title">@include($activeTemplate . 'partials.icon', ['name' => 'cog', 'class' => 'me-1 text-primary'])@lang('Account & security')</h6>
                    </div>
                    <div class="card-body profile-card-body px-2">
                        <ul class="list-unstyled small mb-0" style="font-size:0.8rem">
                            <li class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">@lang('Username')</span><strong>{{ $user->username }}</strong></li>
                            <li class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">@lang('Language')</span>{{ config('app.locale') }}</li>
                            <li class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">@lang('Currency')</span>{{ $general->cur_text ?? $general->cur_sym ?? 'BDT' }}</li>
                            <li class="d-flex justify-content-between py-1"><span class="text-muted">@lang('Timezone')</span>{{ config('app.timezone') }}</li>
                        </ul>
                        <a href="{{ route('user.change.password') }}" class="btn btn-outline-primary btn-sm w-100 mt-2">@include($activeTemplate . 'partials.icon', ['name' => 'key', 'class' => 'me-1'])@lang('Change password')</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Saved addresses (compact) --}}
    <div class="profile-card card border-0 shadow-sm mb-2">
        <div class="card-header bg-light border-0 profile-card-header px-2 d-flex flex-wrap align-items-center justify-content-between gap-1">
            <h6 class="card-title mb-0 profile-section-title">@include($activeTemplate . 'partials.icon', ['name' => 'map-marker-alt', 'class' => 'me-1 text-primary'])@lang('Saved addresses')</h6>
            <button type="button" class="btn btn--base btn-sm py-0 px-2" data-bs-toggle="collapse" data-bs-target="#addAddressForm" aria-expanded="false">@include($activeTemplate . 'partials.icon', ['name' => 'plus', 'class' => 'me-1'])@lang('Add address')</button>
        </div>
        <div class="card-body profile-card-body px-2">
            @forelse($savedAddresses as $addr)
            <div class="saved-address-item border rounded-2 p-2 mb-2 bg-light bg-opacity-50">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                    <div class="saved-address-text small flex-grow-1">
                        @if($addr->label)<span class="badge bg--base me-1">{{ $addr->label }}</span>@endif
                        @if($addr->is_default)<span class="badge bg-success small">@lang('Default')</span>@endif
                        <div class="mt-1">{{ $addr->address_line }}{{ $addr->address_line_2 ? ', ' . $addr->address_line_2 : '' }}, @if($addr->division){{ $addr->division->name_en ?? '' }}, @endif @if($addr->district){{ $addr->district->name_en ?? '' }}, @endif @if($addr->thana){{ $addr->thana->name_en ?? '' }}, @endif {{ $addr->city }}, {{ $addr->country }}@if($addr->postal_code) ({{ $addr->postal_code }})@endif</div>
                    </div>
                    <div class="d-flex gap-1 flex-shrink-0">
                        @if(!$addr->is_default)
                        <form action="{{ route('user.saved.address.default', $addr->id) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-outline-primary py-0 px-1">@lang('Default')</button></form>
                        @endif
                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1" data-bs-toggle="collapse" data-bs-target="#editAddress{{ $addr->id }}">@lang('Edit')</button>
                        <form action="{{ route('user.saved.address.destroy', $addr->id) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('Remove this address?')');">@csrf<button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1">@lang('Delete')</button></form>
                    </div>
                </div>
                <div class="collapse mt-2" id="editAddress{{ $addr->id }}">
                    @php
                        $editDistricts = $addr->division_id && isset($districtsByDivision[$addr->division_id]) ? $districtsByDivision[$addr->division_id] : [];
                        $editDistrictName = optional($addr->district)->name_en ?? '';
                        $editThanas = ($editDistrictName !== '' && isset($thanasByDistrict[$editDistrictName])) ? $thanasByDistrict[$editDistrictName] : [];
                    @endphp
                    <form action="{{ route('user.saved.address.update', $addr->id) }}" method="POST" class="border rounded p-2 bg-white mt-2">
                        @csrf
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <label class="form-label small">@lang('Country') <span class="text-danger">*</span></label>
                                <select name="country" id="editCountry{{ $addr->id }}" class="form-select form-select-sm edit-address-country" data-addr-id="{{ $addr->id }}" required>
                                    <option value="">@lang('Select')</option>
                                    @foreach ($countries as $c)
                                        @php $cn = is_object($c) ? ($c->country ?? '') : ($c['country'] ?? ''); @endphp
                                        <option value="{{ $cn }}" @selected($addr->country == $cn)>{{ __($cn) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 edit-bd-wrap" id="editWrapDivision{{ $addr->id }}" style="{{ stripos($addr->country ?? '', 'Bangladesh') !== false ? '' : 'display:none' }}">
                                <label class="form-label small">@lang('Division')</label>
                                <select name="division_id" id="editDivisionId{{ $addr->id }}" class="form-select form-select-sm edit-division" data-addr-id="{{ $addr->id }}">
                                    <option value="">@lang('Select')</option>
                                    @foreach ($divisionList as $div)
                                        @php $did = is_array($div) ? ($div['id'] ?? '') : ($div->id ?? ''); $den = is_array($div) ? ($div['name_en'] ?? '') : ($div->name_en ?? ''); @endphp
                                        <option value="{{ $did }}" @selected($addr->division_id == $did)>{{ $den }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 edit-bd-wrap" id="editWrapDistrict{{ $addr->id }}" style="{{ stripos($addr->country ?? '', 'Bangladesh') !== false ? '' : 'display:none' }}">
                                <label class="form-label small">@lang('District')</label>
                                <select name="district_id" id="editDistrictId{{ $addr->id }}" class="form-select form-select-sm edit-district" data-addr-id="{{ $addr->id }}">
                                    <option value="">@lang('Select')</option>
                                    @foreach($editDistricts as $d)
                                        @php $dId = is_array($d) ? ($d['id'] ?? '') : ($d->id ?? ''); $dEn = is_array($d) ? ($d['en'] ?? '') : ($d->name_en ?? ''); @endphp
                                        <option value="{{ $dId }}" data-name="{{ $dEn }}" @selected($addr->district_id == $dId)>{{ $dEn }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 edit-bd-wrap" id="editWrapThana{{ $addr->id }}" style="{{ stripos($addr->country ?? '', 'Bangladesh') !== false ? '' : 'display:none' }}">
                                <label class="form-label small">@lang('Thana')</label>
                                <select name="thana_id" id="editThanaId{{ $addr->id }}" class="form-select form-select-sm">
                                    <option value="">@lang('Select')</option>
                                    @foreach($editThanas as $t)
                                        @php $tId = is_array($t) ? ($t['id'] ?? '') : ($t->id ?? ''); $tEn = is_array($t) ? ($t['en'] ?? $t['name_en'] ?? '') : ($t->name_en ?? ''); @endphp
                                        <option value="{{ $tId }}" @selected($addr->thana_id == $tId)>{{ $tEn }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 edit-city-wrap" id="editWrapCity{{ $addr->id }}" style="{{ stripos($addr->country ?? '', 'Bangladesh') !== false ? 'display:none' : '' }}">
                                <label class="form-label small">@lang('City')</label>
                                <input type="text" class="form-control form-control-sm" name="city" value="{{ $addr->city }}" placeholder="@lang('City')">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small">@lang('Address') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="address_line" value="{{ $addr->address_line }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small">@lang('Address 2')</label>
                                <input type="text" class="form-control form-control-sm" name="address_line_2" value="{{ $addr->address_line_2 }}" placeholder="@lang('Optional')">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small">@lang('Postal Code')</label>
                                <input type="text" class="form-control form-control-sm" name="postal_code" value="{{ $addr->postal_code }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small">@lang('Label')</label>
                                <input type="text" class="form-control form-control-sm" name="label" value="{{ $addr->label }}" placeholder="@lang('e.g. Home, Office')">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn--base btn-sm">@lang('Update address')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-muted small mb-0">@lang('No saved addresses yet. Add one below or save from checkout.')</p>
            @endforelse

            <div class="collapse mt-2" id="addAddressForm">
                <form action="{{ route('user.saved.address.store') }}" method="POST" class="border rounded p-2 bg-light mt-2">
                    @csrf
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label small">@lang('Country') <span class="text-danger">*</span></label>
                            <select name="country" id="savedCountry" class="form-select form-select-sm" required>
                                <option value="">@lang('Select')</option>
                                @foreach ($countries as $c)
                                    @php $cn = is_object($c) ? ($c->country ?? '') : ($c['country'] ?? ''); @endphp
                                    <option value="{{ $cn }}">{{ __($cn) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6" id="savedWrapDivision" style="display:none">
                            <label class="form-label small">@lang('Division')</label>
                            <select name="division_id" id="savedDivisionId" class="form-select form-select-sm">
                                <option value="">@lang('Select')</option>
                                @foreach ($divisionList as $div)
                                    @php $did = is_array($div) ? ($div['id'] ?? '') : ($div->id ?? ''); $den = is_array($div) ? ($div['name_en'] ?? '') : ($div->name_en ?? ''); @endphp
                                    <option value="{{ $did }}">{{ $den }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6" id="savedWrapDistrict" style="display:none">
                            <label class="form-label small">@lang('District')</label>
                            <select name="district_id" id="savedDistrictId" class="form-select form-select-sm">
                                <option value="">@lang('Select division first')</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6" id="savedWrapThana" style="display:none">
                            <label class="form-label small">@lang('Thana')</label>
                            <select name="thana_id" id="savedThanaId" class="form-select form-select-sm">
                                <option value="">@lang('Select district first')</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6" id="savedWrapCity" style="display:none">
                            <label class="form-label small">@lang('City')</label>
                            <input type="text" class="form-control form-control-sm" name="city" id="savedCity" placeholder="@lang('City')">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">@lang('Address') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="address_line" required placeholder="@lang('Street, house no.')">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">@lang('Address 2')</label>
                            <input type="text" class="form-control form-control-sm" name="address_line_2" placeholder="@lang('Optional')">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">@lang('Postal Code')</label>
                            <input type="text" class="form-control form-control-sm" name="postal_code" placeholder="ZIP">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">@lang('Label')</label>
                            <input type="text" class="form-control form-control-sm" name="label" placeholder="@lang('e.g. Home, Office')">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn--base btn-sm">@lang('Save address')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush

@push('script')
<script>
(function($) {
    "use strict";
    var districtsByDivision = @json($districtsByDivision ?? []);
    var thanasByDistrict = @json($thanasByDistrict ?? []);
    var profileSavedCity = @json($userAddress->city ?? '');
    var profileSavedThana = @json($userAddress->thana ?? '');

    function isBdCountry(val) {
        return (val || '').toLowerCase().indexOf('bangladesh') !== -1;
    }

    $('#profileCountry').on('change', function() {
        var isBd = isBdCountry($(this).val());
        if (isBd) {
            $('.profile-bd-field').show();
            $('.profile-other-field').hide();
            var divId = $('#profileDivision').find('option:selected').data('id');
            if (divId) fillProfileDistrict(divId);
        } else {
            $('.profile-bd-field').hide();
            $('.profile-other-field').show();
        }
    });
    function fillProfileDistrict(divisionId) {
        var $sel = $('#profileDistrict');
        $sel.empty().append('<option value="">@lang('Select district')</option>');
        if (!divisionId || !districtsByDivision[divisionId]) return;
        var list = districtsByDivision[divisionId];
        for (var i = 0; i < list.length; i++) {
            var en = list[i].en || '';
            var bn = list[i].bn || '';
            var opt = $('<option></option>').attr('value', en).text(en + (bn ? ' / ' + bn : ''));
            if (profileSavedCity === en) opt.prop('selected', true);
            $sel.append(opt);
        }
        if (profileSavedCity) $sel.val(profileSavedCity);
        fillProfileThana($sel.val());
    }
    function fillProfileThana(districtName) {
        var $sel = $('#profileThana');
        $sel.empty().append('<option value="">@lang('Select Thana')</option>');
        if (!districtName || !thanasByDistrict[districtName]) return;
        var list = thanasByDistrict[districtName];
        for (var i = 0; i < list.length; i++) {
            var en = list[i].en || list[i].name_en || '';
            var bn = list[i].bn || list[i].name_bn || '';
            var opt = $('<option></option>').attr('value', en).text(en + (bn ? ' / ' + bn : ''));
            if (profileSavedThana === en) opt.prop('selected', true);
            $sel.append(opt);
        }
        if (profileSavedThana) $sel.val(profileSavedThana);
    }
    $('#profileDivision').on('change', function() {
        fillProfileDistrict($(this).find('option:selected').data('id'));
        $('#profileThana').empty().append('<option value="">@lang('Select district first')</option>');
    });
    $('#profileDistrict').on('change', function() { fillProfileThana($(this).val()); });
    if (isBdCountry($('#profileCountry').val())) {
        var divId = $('#profileDivision').find('option:selected').data('id');
        if (divId) fillProfileDistrict(divId);
    }

    $('#profile-image').on('change', function() {
        if (this.files && this.files[0]) {
            var r = new FileReader();
            r.onload = function(e) {
                $('.profile-avatar-img').attr('src', e.target.result).show();
                $('.profile-avatar-placeholder').hide();
            };
            r.readAsDataURL(this.files[0]);
        }
    });
    $('.remove-profile-img').on('click', function() {
        $('#profile-image').val('');
        $('.profile-avatar-img').hide();
        $('.profile-avatar-placeholder').show();
    });

    function savedToggleCountry() {
        var isBd = isBdCountry($('#savedCountry').val());
        if (isBd) {
            $('#savedWrapDivision, #savedWrapDistrict, #savedWrapThana').show();
            $('#savedWrapCity').hide();
            var divId = $('#savedDivisionId').val();
            if (divId) fillSavedDistrict(divId);
        } else {
            $('#savedWrapDivision, #savedWrapDistrict, #savedWrapThana').hide();
            $('#savedWrapCity').show();
        }
    }
    function fillSavedDistrict(divisionId) {
        var $sel = $('#savedDistrictId');
        $sel.empty().append('<option value="">@lang('Select district')</option>');
        if (!divisionId || !districtsByDivision[divisionId]) return;
        var list = districtsByDivision[divisionId];
        for (var i = 0; i < list.length; i++) {
            var id = list[i].id;
            var en = list[i].en || '';
            var bn = list[i].bn || '';
            $sel.append($('<option></option>').attr('value', id).attr('data-name', en).text(en + (bn ? ' / ' + bn : '')));
        }
        fillSavedThana($sel.find('option:selected').data('name'));
    }
    function fillSavedThana(districtName) {
        var $sel = $('#savedThanaId');
        $sel.empty().append('<option value="">@lang('Select Thana')</option>');
        if (!districtName || !thanasByDistrict[districtName]) return;
        var list = thanasByDistrict[districtName];
        for (var i = 0; i < list.length; i++) {
            var id = list[i].id;
            var en = list[i].en || list[i].name_en || '';
            var bn = list[i].bn || list[i].name_bn || '';
            $sel.append($('<option></option>').attr('value', id).text(en + (bn ? ' / ' + bn : '')));
        }
    }
    $('#savedCountry').on('change', savedToggleCountry);
    $('#savedDivisionId').on('change', function() {
        fillSavedDistrict($(this).val());
        $('#savedThanaId').empty().append('<option value="">@lang('Select district first')</option>');
    });
    $('#savedDistrictId').on('change', function() {
        fillSavedThana($(this).find('option:selected').data('name'));
    });

    $('.edit-address-country').on('change', function() {
        var id = $(this).data('addr-id');
        var isBd = isBdCountry($(this).val());
        if (isBd) {
            $('#editWrapDivision' + id + ', #editWrapDistrict' + id + ', #editWrapThana' + id).show();
            $('#editWrapCity' + id).hide();
            var divId = $('#editDivisionId' + id).val();
            if (divId) fillEditDistrict(id, divId);
        } else {
            $('#editWrapDivision' + id + ', #editWrapDistrict' + id + ', #editWrapThana' + id).hide();
            $('#editWrapCity' + id).show();
        }
    });
    $('.edit-division').on('change', function() {
        var id = $(this).data('addr-id');
        fillEditDistrict(id, $(this).val());
        $('#editThanaId' + id).empty().append('<option value="">@lang('Select district first')</option>');
    });
    $('.edit-district').on('change', function() {
        var id = $(this).data('addr-id');
        fillEditThana(id, $(this).find('option:selected').data('name'));
    });
    function fillEditDistrict(addrId, divisionId) {
        var $sel = $('#editDistrictId' + addrId);
        $sel.empty().append('<option value="">@lang('Select district')</option>');
        if (!divisionId || !districtsByDivision[divisionId]) return;
        var list = districtsByDivision[divisionId];
        for (var i = 0; i < list.length; i++) {
            var id = list[i].id;
            var en = list[i].en || '';
            var bn = list[i].bn || '';
            $sel.append($('<option></option>').attr('value', id).attr('data-name', en).text(en + (bn ? ' / ' + bn : '')));
        }
    }
    function fillEditThana(addrId, districtName) {
        var $sel = $('#editThanaId' + addrId);
        $sel.empty().append('<option value="">@lang('Select Thana')</option>');
        if (!districtName || !thanasByDistrict[districtName]) return;
        var list = thanasByDistrict[districtName];
        for (var i = 0; i < list.length; i++) {
            var id = list[i].id;
            var en = list[i].en || list[i].name_en || '';
            var bn = list[i].bn || list[i].name_bn || '';
            $sel.append($('<option></option>').attr('value', id).text(en + (bn ? ' / ' + bn : '')));
        }
    }
})(jQuery);
</script>
@endpush
@endsection
