@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-12">
        {{-- Quick Stats --}}
        <div class="row mb-4">
            <div class="col-sm-6 col-lg-4">
                <div class="card bg--primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-white mb-0">{{ $languages->count() }}</h4>
                                <small class="text-white opacity-75">@lang('Total Languages')</small>
                            </div>
                            <i class="las la-language me-2" style="font-size: 2rem; opacity: 0.6;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card bg--success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white mb-0">{{ $defaultLang ? __($defaultLang->name) : '-' }}</h6>
                                <small class="text-white opacity-75">@lang('Default Language')</small>
                            </div>
                            <i class="las la-check-circle me-2" style="font-size: 2rem; opacity: 0.6;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Deployment Note --}}
        <div class="alert alert-info mb-4">
            <h6 class="alert-heading"><i class="las la-info-circle me-1"></i>@lang('About This Page')</h6>
            <p class="mb-0 small">@lang('Languages and translations are stored in the database and JSON files in') <code>resources/lang/</code>. @lang('When you deploy to server, these move with your project. You can add or edit languages from this panel on the server—no extra setup needed.')</p>
        </div>

        {{-- Keyword Notice --}}
        <div class="card bl--5-primary mb-4">
            <div class="card-body">
                <p class="text--primary mb-0 small">@lang('While you are adding a new keyword, it will only add to this current language only. Please be careful on entering a keyword, please make sure there is no extra space. It needs to be exact and case-sensitive.')</p>
            </div>
        </div>

        {{-- Languages Table --}}
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two custom-data-table">
                        <thead>
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Code')</th>
                                <th>@lang('Default')</th>
                                <th>@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($languages as $item)
                                <tr>
                                    <td>{{ __($item->name) }}</td>
                                    <td><strong><code>{{ $item->code }}</code></strong></td>
                                    <td>
                                        @if($item->is_default == Status::YES)
                                            <span class="badge bg-success">@lang('Default')</span>
                                        @else
                                            <span class="badge bg-secondary">@lang('Selectable')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="button--group d-flex flex-wrap gap-1">
                                            <a href="{{ route('admin.language.key', $item->id) }}" class="btn btn-sm btn-outline--success" title="@lang('Translate')">
                                                <i class="la la-language"></i> @lang('Translate')
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-outline--primary editBtn" data-url="{{ route('admin.language.manage.update', $item->id) }}" data-lang="{{ json_encode($item->only('name', 'text_align', 'is_default')) }}" title="@lang('Edit')">
                                                <i class="la la-pen"></i> @lang('Edit')
                                            </a>
                                            @if($item->id != 1)
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to remove this language from this system?')" data-action="{{ route('admin.language.manage.delete', $item->id) }}" title="@lang('Remove')">
                                                    <i class="la la-trash"></i> @lang('Remove')
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center py-5" colspan="100%">{{ __($emptyMessage ?? 'No language added yet') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Add Language Modal --}}
        <div class="modal fade" id="createModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">@lang('Add New Language')</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="post" action="{{ route('admin.language.manage.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label>@lang('Language Name')</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required placeholder="e.g. Bengali">
                            </div>
                            <div class="form-group mb-3">
                                <label>@lang('Language Code')</label>
                                <input type="text" class="form-control" name="code" value="{{ old('code') }}" required placeholder="e.g. bn" maxlength="10">
                                <small class="text-muted">@lang('Short code (e.g. en, bn). Used for') <code>resources/lang/{code}.json</code></small>
                            </div>
                            <div class="form-group">
                                <label class="d-flex align-items-center gap-2">
                                    <input type="checkbox" data-width="100%" data-height="40px" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('SET')" data-off="@lang('UNSET')" name="is_default">
                                    @lang('Default Language')
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Language Modal --}}
        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">@lang('Edit Language')</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="post" id="editLangForm">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label>@lang('Language Name')</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="form-group">
                                <label class="d-flex align-items-center gap-2">
                                    <input type="checkbox" data-width="100%" data-height="40px" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('SET')" data-off="@lang('UNSET')" name="is_default">
                                    @lang('Default Language')
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Language Keywords Modal --}}
        <div class="modal fade" id="getLangModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">@lang('Language Keywords')</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">@lang('All of the possible language keywords are available here. However, some keywords may be missing due to variations in the database. If you encounter any missing keywords, you can add them manually.')</p>
                        <p class="text--primary mb-3">@lang('You can import these keywords from the translate page of any language as well.')</p>
                        <div class="form-group copy-texts-wrapper position-relative">
                            <div class="copy-texts">
                                <span class="copy">@lang('Copy')</span>
                            </div>
                            <textarea class="form-control langKeys key-added" id="langKeys" rows="25" readonly></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a class="btn btn-sm btn-outline--primary" data-bs-toggle="modal" data-bs-target="#createModal"><i class="las la-plus"></i>@lang('Add New')</a>
    <a class="btn btn-sm btn-outline--info keyBtn" data-bs-toggle="modal" data-bs-target="#getLangModal"><i class="las la-code"></i>@lang('Language Keywords')</a>
@endpush

@push('style')
<style>
.copy-texts-wrapper:hover .copy-texts { visibility: visible; opacity: 1; }
.copy-texts { position: absolute; left: 0; top: 0; z-index: 99; background: #0000004d; width: 100%; height: 100%; border-radius: 5px; display: flex; justify-content: center; align-items: center; visibility: hidden; opacity: 0; transition: .3s; cursor: pointer; }
.copy-texts .copy { color: #fff; font-size: 40px; }
</style>
@endpush

@push('script')
<script>
(function($){
    "use strict";
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
            if (toggleInput.data('bs.toggle')) toggleInput.bootstrapToggle('on');
        } else {
            toggleInput.prop('checked', false);
            if (toggleInput.data('bs.toggle')) toggleInput.bootstrapToggle('off');
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
            setTimeout(function() { $('.copy-texts .copy').text('{{ __("Copy") }}'); }, 2000);
        }
    });
})(jQuery);
</script>
@endpush
