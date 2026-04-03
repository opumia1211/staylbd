<form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}">
    @csrf
    <input type="hidden" name="section" value="support_center">
    <div class="row g-2">
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Enable Support Center in Footer')</label>
                <select name="enabled" class="form-select form-select-sm">
                    <option value="1" {{ (optional($supportCenter)->data_values->enabled ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                    <option value="0" {{ !(optional($supportCenter)->data_values->enabled ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Support Email (shown to users)')</label>
                <input type="email" name="support_email" class="form-control form-control-sm" value="{{ optional($supportCenter)->data_values->support_email ?? '' }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Help Center URL')</label>
                <input type="url" name="help_center_url" class="form-control form-control-sm" value="{{ optional($supportCenter)->data_values->help_center_url ?? '' }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Return Policy URL')</label>
                <input type="url" name="return_policy_url" class="form-control form-control-sm" value="{{ optional($supportCenter)->data_values->return_policy_url ?? '' }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Refund Policy URL')</label>
                <input type="url" name="refund_policy_url" class="form-control form-control-sm" value="{{ optional($supportCenter)->data_values->refund_policy_url ?? '' }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Track Order URL')</label>
                <input type="url" name="track_order_url" class="form-control form-control-sm" value="{{ optional($supportCenter)->data_values->track_order_url ?? '' }}" placeholder="{{ route('user.order.index') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Show Support Ticket Link')</label>
                <select name="support_ticket_enabled" class="form-select form-select-sm">
                    <option value="1" {{ (optional($supportCenter)->data_values->support_ticket_enabled ?? 1) ? 'selected' : '' }}>@lang('Yes')</option>
                    <option value="0" {{ !(optional($supportCenter)->data_values->support_ticket_enabled ?? 1) ? 'selected' : '' }}>@lang('No')</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Live Chat Button (Enable)')</label>
                <select name="live_chat_enabled" class="form-select form-select-sm">
                    <option value="0" {{ !(optional($supportCenter)->data_values->live_chat_enabled ?? 0) ? 'selected' : '' }}>@lang('No')</option>
                    <option value="1" {{ (optional($supportCenter)->data_values->live_chat_enabled ?? 0) ? 'selected' : '' }}>@lang('Yes')</option>
                </select>
                <small class="text-muted d-block">@lang('If you use a live chat widget, enable here.')</small>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn--primary btn-sm mt-2">@lang('Save Support Center')</button>
</form>
