<p class="text-muted small mb-2">@lang('Show a professional return request form in the footer. Submissions create a support ticket.')</p>
<form method="POST" action="{{ route('admin.frontend.sections.footer.saveReturnPolicy') }}">
    @csrf
    <div class="row g-2">
        <div class="col-md-4">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Show Return Policy Form in Footer')</label>
                <select name="show_form" class="form-select form-select-sm">
                    <option value="1" {{ (optional($returnPolicy)->data_values->show_form ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                    <option value="0" {{ !(optional($returnPolicy)->data_values->show_form ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                </select>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Form Title')</label>
                <input type="text" name="form_title" class="form-control form-control-sm" value="{{ optional($returnPolicy)->data_values->form_title ?? __('Product Return Request') }}">
            </div>
        </div>
        <div class="col-12">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Success Message (after submit)')</label>
                <input type="text" name="success_message" class="form-control form-control-sm" value="{{ optional($returnPolicy)->data_values->success_message ?? __('We have received your return request. Our team will contact you shortly.') }}">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn--primary btn-sm mt-2">@lang('Save Return Policy Settings')</button>
</form>
