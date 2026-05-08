{{--
Contact Form Fields
Subject (Category), Name, Email
--}}

<div class="contact-panel-fields-two-rows flex flex-col gap-3 mb-4" id="contactPanelFormFields">
    <div class="contact-panel-fields-row stayl-flex-wrap-nowrap">
        {{-- Subject/Category Dropdown --}}
        <div class="form-group mb-0 contact-panel-field-half flex-1 min-w-0">
            <label class="form-label block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 px-1" for="contactPanelSubject">@lang('Subject')</label>
            <select name="subject" id="contactPanelSubject" class="form-control form-control-sm w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 focus:outline-none transition-all duration-300 text-xs text-slate-700 appearance-none" required
                aria-label="@lang('Subject')">
                <option value="">@lang('Select subject')</option>
                <option value="Live Chat Message" selected>@lang('Live Chat Message')</option>
                <option value="General Inquiry">@lang('General Inquiry')</option>
                <option value="Report a Problem">@lang('Report a Problem')</option>
                <option value="Order Support">@lang('Order Support')</option>
            </select>
        </div>

        {{-- Name Field --}}
        <div class="form-group mb-0 contact-panel-field-half flex-1 min-w-0">
            <label class="form-label block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 px-1" for="contactPanelName">@lang('Name')</label>
            <input type="text" name="name" id="contactPanelName" class="form-control form-control-sm w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 focus:outline-none transition-all duration-300 text-xs text-slate-700 placeholder-slate-400 shadow-sm"
                value="{{ old('name', optional(auth()->user())->fullname ?? '') }}" placeholder="@lang('Your name')"
                required autocomplete="name" aria-label="@lang('Your name')">
        </div>
    </div>

    <div class="contact-panel-fields-row flex gap-3">
        {{-- Email Field --}}
        <div class="form-group mb-0 contact-panel-field-full flex-1 min-w-0">
            <label class="form-label block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 px-1" for="contactPanelEmail">@lang('Email')</label>
            <input type="email" name="email" id="contactPanelEmail" class="form-control form-control-sm w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 focus:outline-none transition-all duration-300 text-xs text-slate-700 placeholder-slate-400 shadow-sm"
                value="{{ old('email', optional(auth()->user())->email ?? '') }}" placeholder="@lang('Your email')"
                required autocomplete="email" aria-label="@lang('Your email')">
        </div>
    </div>
</div>