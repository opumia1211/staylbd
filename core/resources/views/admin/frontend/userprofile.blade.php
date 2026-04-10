@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                <a href="{{ route('admin.frontend.sections.register') }}" class="btn btn-outline--primary btn-sm"><i class="las la-arrow-left me-1"></i> @lang('Back to Registration control')</a>
                <a href="{{ route('user.profile.setting') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline--success btn-sm">
                    <i class="las la-external-link-alt me-1"></i> @lang('View user profile page') <span class="d-none d-md-inline">({{ url('/user/profile-setting') }})</span>
                </a>
            </div>
        </div>

        <form action="{{ route('admin.frontend.sections.userprofile.save') }}" method="POST" id="userprofileForm">
            @csrf
            <div class="card border-0 shadow-sm mb-2">
                <div class="card-header bg--warning text-dark py-2 px-3 d-flex align-items-center">
                    <i class="las la-user-edit me-2 fs-5"></i>
                    <span class="fw-semibold">@lang('User profile control')</span>
                </div>
                <div class="card-body p-3">
                    <p class="text-muted small mb-2">@lang('Select which fields users can edit on their profile after account creation. When you tick and save, those fields will appear on the user profile page and data will be saved to the database.')</p>
                    <p class="text-muted small mb-3">@lang('User profile page'): <code>{{ url('/user/profile-setting') }}</code> — @lang('Location tracking and live location are not shown on the user profile; only the fields below and delivery address are shown.')</p>
                    @foreach(registrationFieldsListGrouped() as $groupKey => $group)
                    <div class="registration-field-group mb-3 pb-3 border-bottom border-light">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-semibold text-dark"><i class="{{ $group['icon'] }} me-1 text-warning"></i>{{ $group['title'] }}</span>
                            <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2 profile-group-toggle">@lang('Toggle')</button>
                        </div>
                        <div class="reg-fields-grid">
                            @foreach($group['fields'] as $fkey => $label)
                            @if($fkey === 'captcha' || $fkey === 'password' || $fkey === 'agree' || $fkey === 'referBy') @continue @endif
                            <label class="reg-field-item" for="profile_field_{{ $fkey }}">
                                <input type="hidden" name="profile_fields[{{ $fkey }}]" value="0">
                                <input type="checkbox" class="form-check-input profile-field-cb" name="profile_fields[{{ $fkey }}]" value="1" id="profile_field_{{ $fkey }}" {{ isProfileFieldEnabled($fkey) ? 'checked' : '' }} data-field="{{ $fkey }}">
                                <span class="reg-field-label">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    <div class="pt-2">
                        <button type="submit" class="btn btn--warning btn-sm"><i class="las la-save me-1"></i> @lang('Save profile fields')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
(function () {
    'use strict';
    document.querySelectorAll('.profile-group-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var group = this.closest('.registration-field-group');
            var checkboxes = group.querySelectorAll('.profile-field-cb');
            var allChecked = Array.prototype.every.call(checkboxes, function (c) { return c.checked; });
            checkboxes.forEach(function (c) { c.checked = !allChecked; });
        });
    });
})();
</script>
@endpush
@endsection
