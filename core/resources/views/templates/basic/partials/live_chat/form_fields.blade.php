{{--
Contact Form Fields
Subject (Category), Name, Email
--}}

<div class="contact-panel-fields-two-rows mb-2" id="contactPanelFormFields">
    <div class="contact-panel-fields-row stayl-flex-wrap-nowrap">
        {{-- Subject/Category Dropdown --}}
        <div class="form-group mb-2 contact-panel-field-half stayl-flex-1-min-0">
            <label class="form-label" for="contactPanelSubject">@lang('Subject')</label>
            <select name="subject" id="contactPanelSubject" class="form-control form-control-sm" required
                aria-label="@lang('Subject')">
                <option value="">@lang('Select subject')</option>
                <option value="Live Chat Message" selected>@lang('Live Chat Message')</option>
                <option value="General Inquiry">@lang('General Inquiry')</option>
                <option value="Report a Problem">@lang('Report a Problem')</option>
                <option value="Order Support">@lang('Order Support')</option>
            </select>
        </div>

        {{-- Name Field --}}
        <div class="form-group mb-2 contact-panel-field-half stayl-flex-1-min-0">
            <label class="form-label" for="contactPanelName">@lang('Name')</label>
            <input type="text" name="name" id="contactPanelName" class="form-control form-control-sm"
                value="{{ old('name', optional(auth()->user())->fullname ?? '') }}" placeholder="@lang('Your name')"
                required autocomplete="name" aria-label="@lang('Your name')">
        </div>
    </div>

    <div class="contact-panel-fields-row stayl-flex-wrap-nowrap">
        {{-- Email Field --}}
        <div class="form-group mb-2 contact-panel-field-full stayl-flex-1-min-0">
            <label class="form-label" for="contactPanelEmail">@lang('Email')</label>
            <input type="email" name="email" id="contactPanelEmail" class="form-control form-control-sm"
                value="{{ old('email', optional(auth()->user())->email ?? '') }}" placeholder="@lang('Your email')"
                required autocomplete="email" aria-label="@lang('Your email')">
        </div>
    </div>
</div>