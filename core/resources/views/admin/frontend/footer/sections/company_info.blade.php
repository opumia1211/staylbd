<form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}">
    @csrf
    <input type="hidden" name="section" value="company_info">
    <div class="row g-2">
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Show Company Block')</label>
                <select name="show" class="form-select form-select-sm">
                    <option value="1" {{ (optional($companyInfo)->data_values->show ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                    <option value="0" {{ !(optional($companyInfo)->data_values->show ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                </select>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('About Company (Short Description)')</label>
                <textarea name="about_text" class="form-control form-control-sm" rows="2">{{ optional($companyInfo)->data_values->about_text ?? '' }}</textarea>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Brand Mission / Trust Message')</label>
                <textarea name="mission_text" class="form-control form-control-sm" rows="2">{{ optional($companyInfo)->data_values->mission_text ?? '' }}</textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Company Registration Info')</label>
                <input type="text" name="registration_info" class="form-control form-control-sm" value="{{ optional($companyInfo)->data_values->registration_info ?? '' }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Business / Trade License')</label>
                <input type="text" name="business_license" class="form-control form-control-sm" value="{{ optional($companyInfo)->data_values->business_license ?? '' }}">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn--primary btn-sm mt-2">@lang('Save Company Info')</button>
</form>
