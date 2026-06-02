{{--
Contact Form Fields
Subject (Category), Name, Email
--}}

<div class="contact-panel-fields-two-rows mb-1" id="contactPanelFormFields">
    <div class="row g-1">
        {{-- Subject/Category Dropdown --}}
        <div class="col-6 form-group">
            <label class="form-label mb-0.5 px-1 text-[8px] uppercase font-bold text-slate-400" for="contactPanelSubject">@lang('Subject')</label>
            <div class="relative">
                <select name="subject" id="contactPanelSubject" class="form-control form-control-sm text-[10px] h-7 appearance-none pr-5 py-0 px-2" required
                    aria-label="@lang('Subject')">
                    <option value="">@lang('Select')</option>
                    <option value="Live Chat Message" selected>@lang('Live Chat')</option>
                    <option value="General Inquiry">@lang('General')</option>
                    <option value="Report a Problem">@lang('Report')</option>
                    <option value="Order Support">@lang('Order')</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-1.5 pointer-events-none text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>
        </div>

        {{-- Name Field --}}
        <div class="col-6 form-group">
            <label class="form-label mb-0.5 px-1 text-[8px] uppercase font-bold text-slate-400" for="contactPanelName">@lang('Name')</label>
            <input type="text" name="name" id="contactPanelName" class="form-control form-control-sm text-[10px] h-7 py-0 px-2 shadow-none"
                value="{{ old('name', optional(auth()->user())->fullname ?? '') }}" placeholder="@lang('Name')"
                required autocomplete="name" aria-label="@lang('Name')">
        </div>

        {{-- Email Field --}}
        <div class="col-12 form-group mt-1">
            <label class="form-label mb-0.5 px-1 text-[8px] uppercase font-bold text-slate-400" for="contactPanelEmail">@lang('Email')</label>
            <input type="email" name="email" id="contactPanelEmail" class="form-control form-control-sm text-[10px] h-7 py-0 px-2 shadow-none"
                value="{{ old('email', optional(auth()->user())->email ?? '') }}" placeholder="@lang('Email')"
                required autocomplete="email" aria-label="@lang('Email')">
        </div>
    </div>
</div>