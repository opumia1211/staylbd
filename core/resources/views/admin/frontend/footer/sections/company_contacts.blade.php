<form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}">
    @csrf
    <input type="hidden" name="section" value="company_info">
    <div class="alert alert-info mb-2 py-2">
        @lang('These contact fields are shown in the footer About Us card under the address.')
    </div>
    <div class="row g-2">
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Footer Phone Number')</label>
                <input type="text" name="contact_phone" class="form-control form-control-sm" value="{{ optional($companyInfo)->data_values->contact_phone ?? '+1 202-555-0178' }}" placeholder="@lang('e.g. +1 202-555-0178')">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Footer Gmail / Email')</label>
                <input type="email" name="contact_email" class="form-control form-control-sm" value="{{ optional($companyInfo)->data_values->contact_email ?? 'support@staylbd.com' }}" placeholder="@lang('e.g. support@staylbd.com')">
            </div>
        </div>
    </div>
    <div class="mt-2">
        <button type="submit" class="btn btn--primary btn-sm">@lang('Save Company Contacts')</button>
    </div>
</form>
