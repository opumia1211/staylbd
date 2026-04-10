@extends('admin.layouts.app')

@section('panel')
    <div class="mb-10">
        <ul class="flex flex-wrap items-center gap-2.5 w-fit max-w-full" role="tablist">
            @foreach($categories as $key => $label)
                <li class="nav-item">
                    <a class="px-6 py-2.5 rounded-[18px] text-[13px] font-bold transition-all duration-300 {{ $currentCategory === $key ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'text-slate-500 hover:bg-white hover:text-indigo-600 hover:shadow-sm' }}" href="{{ route('admin.extensions.index', ['category' => $key]) }}">
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    @php
        $filteredExtensions = $currentCategory === 'all'
            ? $extensions
            : $extensions->filter(fn($e) => $e->category === $currentCategory);
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($filteredExtensions as $extension)
            @php $category = $extension->category; @endphp
            <div class="group extension-card-item" data-name="{{ strtolower(__($extension->name)) }}" data-category="{{ strtolower($category) }}">
                <div class="bg-white rounded-[20px] p-5 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 h-full flex flex-col">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center p-2 flex-shrink-0 group-hover:bg-indigo-50 transition-colors">
                            <img src="{{ getImage(getFilePath('extensions') .'/'. $extension->image, getFileSize('extensions')) }}" 
                                 alt="{{ __($extension->name) }}" class="max-w-full max-h-full object-contain">
                        </div>
                        <div class="min-w-0 flex-grow">
                            <h6 class="text-slate-800 font-bold text-[15px] mb-1 truncate">{{ __($extension->name) }}</h6>
                            <span class="inline-block px-2 py-0.5 rounded-full bg-indigo-600 text-white text-[10px] font-bold uppercase tracking-wider mb-2">
                                {{ $category }}
                            </span>
                            <p class="text-slate-500 text-xs leading-relaxed line-clamp-2">{{ __($extension->description) }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-auto">
                        <div class="mb-4">
                            @php 
                                $badgeClass = $extension->status == Status::ENABLE 
                                    ? 'bg-emerald-50 text-emerald-600 border-emerald-100' 
                                    : 'bg-orange-50 text-orange-600 border-orange-100';
                                $statusText = $extension->status == Status::ENABLE ? 'Enabled' : 'Disabled';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg border text-[11px] font-bold {{ $badgeClass }}">
                                {{ __($statusText) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2">
                            <button type="button" class="editBtn flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-indigo-200 bg-white text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all text-xs font-bold"
                                    data-name="{{ __($extension->name) }}"
                                    data-shortcode="{{ json_encode($extension->shortcode ?? (object)[]) }}"
                                    data-action="{{ route('admin.extensions.update', $extension->id) }}">
                                <i class="la la-cogs text-sm"></i> @lang('Configure')
                            </button>
                            <button type="button" class="helpBtn flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-800 hover:text-white transition-all text-xs font-bold"
                                    data-description="{{ __($extension->description) }}"
                                    data-support="{{ __($extension->support) }}">
                                <i class="la la-question text-sm"></i> @lang('Help')
                            </button>
                            @if($extension->status == Status::DISABLE)
                                <button type="button" class="confirmationBtn flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-emerald-200 bg-white text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all text-xs font-bold"
                                        data-action="{{ route('admin.extensions.status', $extension->id) }}"
                                        data-question="@lang('Are you sure to enable this extension?')">
                                    <i class="la la-eye text-sm"></i> @lang('Enable')
                                </button>
                            @else
                                <button type="button" class="confirmationBtn flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-orange-200 bg-white text-orange-600 hover:bg-orange-600 hover:text-white transition-all text-xs font-bold"
                                        data-action="{{ route('admin.extensions.status', $extension->id) }}"
                                        data-question="@lang('Are you sure to disable this extension?')">
                                    <i class="la la-eye-slash text-sm"></i> @lang('Disable')
                                </button>
                            @endif
                            <button type="button" class="confirmationBtn flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-rose-200 bg-white text-rose-600 hover:bg-rose-600 hover:text-white transition-all text-xs font-bold"
                                    data-action="{{ route('admin.extensions.delete', $extension->id) }}"
                                    data-question="@lang('Are you sure to remove this extension? This cannot be undone.')">
                                <i class="la la-trash text-sm"></i> @lang('Remove')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card b-radius--10">
                    <div class="card-body text-center py-5">
                        <i class="las la-puzzle-piece fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">@lang('No extensions found.')</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Configure Modal --}}
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Configure Extension'): <span class="extension-name"></span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body extension-modal-body"></div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary" id="editBtn">@lang('Save')</button>
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Help Modal --}}
    <div id="helpModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Need Help')?</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body help-modal-body"></div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="flex flex-wrap items-center justify-end gap-3">
        <a href="{{ route('admin.extensions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 transition-all shadow-sm shadow-indigo-100">
            <i class="las la-plus text-lg"></i> @lang('Add Extension')
        </a>
        <div class="relative group min-w-[260px]">
            <input type="text" name="search_table" class="w-full pl-4 pr-10 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all extension-search" placeholder="@lang('Search extensions')...">
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                <i class="las la-search text-lg"></i>
            </span>
        </div>
    </div>
@endpush

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            function escapeHtml(text) {
                if (!text) return '';
                var div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            $(document).on('click', '.editBtn', function () {
                var modal = $('#editModal');
                var shortcode = $(this).data('shortcode');

                modal.find('.extension-name').text($(this).data('name'));
                modal.find('form').attr('action', $(this).data('action'));

                var html = '';
                $.each(shortcode, function (key, item) {
                    var title = item.title || key;
                    var value = (typeof item.value !== 'undefined') ? item.value : '';
                    var useTextarea = (key + '').toLowerCase().indexOf('script') >= 0 ||
                        (key + '').toLowerCase().indexOf('code') >= 0 ||
                        (value + '').length > 120;
                    var inputTag = useTextarea
                        ? '<textarea name="' + key + '" class="form-control" rows="6" placeholder="--">' + escapeHtml(value) + '</textarea>'
                        : '<input type="text" name="' + key + '" class="form-control" placeholder="--" value="' + escapeHtml(value) + '">';
                    html += '<div class="form-group mb-3"><label class="form-label fw-bold">' + escapeHtml(title) + '</label><div>' + inputTag + '</div></div>';
                });
                modal.find('.extension-modal-body').html(html);
                modal.modal('show');
            });

            $(document).on('click', '.helpBtn', function () {
                var modal = $('#helpModal');
                var path = "{{ asset(getFilePath('extensions')) }}";
                modal.find('.help-modal-body').html('<div class="mb-2">' + $(this).data('description') + '</div>');
                if ($(this).data('support') && $(this).data('support') !== 'na') {
                    modal.find('.help-modal-body').append('<img src="' + path + '/' + $(this).data('support') + '" class="img-fluid mt-2" alt="Help">');
                }
                modal.modal('show');
            });

            $('.extension-search').on('keyup', function () {
                var q = $(this).val().toLowerCase();
                $('.extension-card-item').each(function () {
                    var name = $(this).data('name') || '';
                    var cat = $(this).data('category') || '';
                    var desc = $(this).find('.extension-desc').text().toLowerCase() || '';
                    var show = !q || name.indexOf(q) >= 0 || cat.indexOf(q) >= 0 || desc.indexOf(q) >= 0;
                    $(this).toggle(show);
                });
            });
        })(jQuery);
    </script>
@endpush
