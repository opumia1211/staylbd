@extends('admin.layouts.app')
@section('panel')
<div class="space-y-8">
    {{-- Quick Stats: Total Languages & Default --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5">
            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-3xl">
                <i class="las la-language"></i>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">@lang('Total Languages')</span>
                <span class="text-2xl font-black text-slate-800">{{ $languages->count() }}</span>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-5">
            <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-3xl">
                <i class="las la-check-circle"></i>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">@lang('Default Language')</span>
                <span class="text-xl font-bold text-slate-800">{{ $defaultLang ? __($defaultLang->name) : '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Info Notes --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-indigo-50/50 border border-indigo-100 p-5 rounded-2xl flex gap-4">
            <i class="las la-info-circle text-2xl text-indigo-500 mt-0.5"></i>
            <div>
                <h6 class="text-sm font-bold text-indigo-900 mb-1">@lang('About This Page')</h6>
                <p class="text-[12px] leading-relaxed text-indigo-700/80">@lang('All translations move with your project. You can manage them directly from this panel—no code setup needed.')</p>
            </div>
        </div>
        <div class="bg-amber-50/50 border border-amber-100 p-5 rounded-2xl flex gap-4">
            <i class="las la-exclamation-triangle text-2xl text-amber-500 mt-0.5"></i>
            <div>
                <h6 class="text-sm font-bold text-amber-900 mb-1">@lang('Keyword Notice')</h6>
                <p class="text-[12px] leading-relaxed text-amber-700/80">@lang('Keywords are case-sensitive. Please make sure there is no extra space when adding new translation keys.')</p>
            </div>
        </div>
    </div>

    {{-- Main Languages Table --}}
    <div class="bg-white rounded-[24px] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">@lang('Language Name')</th>
                        <th class="px-8 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">@lang('Short Code')</th>
                        <th class="px-8 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center">@lang('Availability')</th>
                        <th class="px-8 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-right">@lang('Actions')</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($languages as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-5">
                                <span class="text-[14px] font-bold text-slate-700">{{ __($item->name) }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <code class="px-2.5 py-1 bg-slate-100 rounded-md text-[12px] font-bold text-indigo-600 border border-slate-200/50">{{ $item->code }}</code>
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if($item->is_default == Status::YES)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[11px] font-black border border-emerald-100 uppercase tracking-tighter">
                                        <i class="las la-check-circle"></i> @lang('Default')
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-400 rounded-full text-[11px] font-bold border border-slate-200 uppercase tracking-tighter">
                                        @lang('Selectable')
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.language.key', $item->id) }}" class="btn inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-600 text-white rounded-xl text-[11px] font-bold hover:bg-indigo-700 transition-all shadow-sm shadow-indigo-100" title="@lang('Translate')">
                                        <i class="la la-language text-base"></i> @lang('Translate')
                                    </a>
                                    <button class="btn inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 text-slate-600 rounded-xl text-[11px] font-bold hover:bg-slate-200 transition-all editBtn" 
                                            data-url="{{ route('admin.language.manage.update', $item->id) }}" 
                                            data-lang="{{ json_encode($item->only('name', 'text_align', 'is_default')) }}" 
                                            title="@lang('Edit')">
                                        <i class="la la-pen text-base"></i>
                                    </button>
                                    @if($item->id != 1)
                                        <button class="btn inline-flex items-center gap-2 px-3.5 py-2 bg-rose-50 text-rose-600 rounded-xl text-[11px] font-bold hover:bg-rose-600 hover:text-white transition-all confirmationBtn" 
                                                data-question="@lang('Are you sure to remove this language?')" 
                                                data-action="{{ route('admin.language.manage.delete', $item->id) }}" 
                                                title="@lang('Remove')">
                                            <i class="la la-trash text-base"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-8 py-12 text-center text-slate-400 font-medium" colspan="100%">
                                <div class="flex flex-col items-center gap-3 opacity-60">
                                    <i class="las la-language text-5xl"></i>
                                    <span>{{ __($emptyMessage ?? 'No language added yet') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>

{{-- Add Language Modal --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-[20px] shadow-2xl overflow-hidden">
            <div class="modal-header border-0 bg-slate-50 px-6 py-4">
                <h4 class="text-lg font-bold text-slate-800">@lang('Add New Language')</h4>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="{{ route('admin.language.manage.store') }}">
                @csrf
                <div class="modal-body p-6 space-y-5">
                    <div class="space-y-2">
                        <label class="text-[13px] font-bold text-slate-600">@lang('Language Name') <span class="text-rose-500">*</span></label>
                        <input type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all" name="name" value="{{ old('name') }}" required placeholder="e.g. Bengali">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[13px] font-bold text-slate-600">@lang('Language Code') <span class="text-rose-500">*</span></label>
                        <input type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all" name="code" value="{{ old('code') }}" required placeholder="e.g. bn" maxlength="10">
                        <p class="text-[10px] text-slate-400 font-medium">@lang('Short code (e.g. en, bn). Used for resources/lang/{code}.json')</p>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="space-y-0.5">
                            <h6 class="text-[13px] font-bold text-slate-700">@lang('Default Language')</h6>
                            <p class="text-[11px] text-slate-400">@lang('Set as the system primary language')</p>
                        </div>
                        <div class="form-check form-switch m-0 p-0">
                            <input class="form-check-input w-12 h-6 cursor-pointer border-slate-300 text-indigo-600 focus:ring-0 appearance-none bg-slate-200 rounded-full checked:bg-indigo-600 transition-all !m-0" type="checkbox" name="is_default" role="switch">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-6 bg-slate-50">
                    <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">@lang('Submit and Create')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Language Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-[20px] shadow-2xl overflow-hidden">
            <div class="modal-header border-0 bg-slate-50 px-6 py-4">
                <h4 class="text-lg font-bold text-slate-800">@lang('Edit Language')</h4>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="editLangForm">
                @csrf
                <div class="modal-body p-6 space-y-5">
                    <div class="space-y-2">
                        <label class="text-[13px] font-bold text-slate-600">@lang('Language Name') <span class="text-rose-500">*</span></label>
                        <input type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all" name="name" required>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="space-y-0.5">
                            <h6 class="text-[13px] font-bold text-slate-700">@lang('Default Language')</h6>
                            <p class="text-[11px] text-slate-400">@lang('Set as the system primary language')</p>
                        </div>
                        <div class="form-check form-switch m-0 p-0">
                            <input class="form-check-input w-12 h-6 cursor-pointer border-slate-300 text-indigo-600 focus:ring-0 appearance-none bg-slate-200 rounded-full checked:bg-indigo-600 transition-all !m-0" type="checkbox" name="is_default" role="switch">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-6 bg-slate-50">
                    <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">@lang('Update Language')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Language Keywords Modal --}}
<div class="modal fade" id="getLangModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 rounded-[24px] shadow-2xl overflow-hidden">
            <div class="modal-header border-0 bg-slate-50 px-8 py-5">
                <h4 class="text-xl font-bold text-slate-800">@lang('Language Keywords')</h4>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-8">
                <p class="mb-4 text-sm text-slate-500">@lang('All possible language keywords are available here. Manual additions are possible if needed.')</p>
                <div class="relative">
                    <div class="absolute top-4 right-4 z-10">
                        <button class="copy-texts bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                            <span class="copy text-white">@lang('Copy All')</span>
                        </button>
                    </div>
                    <textarea class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-6 text-sm text-slate-600 font-mono outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" id="langKeys" rows="18" readonly></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="javascript:void(0)" class="btn inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all shadow-sm shadow-indigo-100" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="las la-plus text-base"></i> @lang('Add New')
    </a>
    <a href="javascript:void(0)" class="btn inline-flex items-center gap-2 px-4 py-2 bg-white text-slate-600 border border-slate-200 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all keyBtn" data-bs-toggle="modal" data-bs-target="#getLangModal">
        <i class="las la-code text-base text-indigo-500"></i> @lang('Language Keywords')
    </a>
@endpush

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
(function($){
    "use strict";

    // Move modals to body to prevent stacking context issues (clipping/not clickable)
    $('.modal').appendTo('body');

    $('.editBtn').on('click', function () {
        var modalEl = document.getElementById('editModal');
        var modal = $(modalEl);
        var url = $(this).data('url');
        var lang = $(this).data('lang');
        modal.find('form').attr('action', url);
        modal.find('input[name=name]').val(lang.name);
        
        var toggleInput = modal.find('input[name=is_default]');
        if (lang.is_default == 1) {
            toggleInput.prop('checked', true);
        } else {
            toggleInput.prop('checked', false);
        }

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
        } else {
            modal.modal('show');
        }
    });

    $('.keyBtn').on('click', function () {
        var $ta = $('#langKeys');
        $ta.val('{{ __("Loading...") }}');
        $.ajax({
            url: "{{ route('admin.language.get.key') }}",
            type: 'GET',
            dataType: 'text',
            success: function (data) {
                $ta.val(data || '');
            },
            error: function() {
                $ta.val('');
                if (typeof notify === 'function') notify('error', '{{ __("Could not load keywords.") }}');
            }
        });
    });

    $('.copy-texts').on('click', function () {
        var el = document.getElementById("langKeys");
        if (el) {
            el.select();
            el.setSelectionRange(0, 99999);
            try { navigator.clipboard.writeText(el.value); } catch(e) { document.execCommand("copy"); }
            $(this).find('.copy').text('{{ __("Copied") }}');
            setTimeout(function() { $('.copy-texts .copy').text('{{ __("Copy All") }}'); }, 2000);
        }
    });
})(jQuery);
</script>
@endpush
