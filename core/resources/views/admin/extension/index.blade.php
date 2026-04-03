@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-pills flex-wrap gap-2 extension-category-tabs" role="tablist">
                @foreach($categories as $key => $label)
                    <li class="nav-item">
                        <a class="nav-link {{ $currentCategory === $key ? 'active' : '' }}" href="{{ route('admin.extensions.index', ['category' => $key]) }}">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    @php
        $filteredExtensions = $currentCategory === 'all'
            ? $extensions
            : $extensions->filter(fn($e) => $e->category === $currentCategory);
    @endphp
    <div class="row extension-cards-row">
        @forelse($filteredExtensions as $extension)
            @php $category = $extension->category; @endphp
                <div class="col-lg-6 col-xl-4 extension-card-item" data-name="{{ strtolower(__($extension->name)) }}" data-category="{{ strtolower($category) }}">
                    <div class="card b-radius--10 h-100 extension-card">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-3">
                                <div class="extension-thumb flex-shrink-0">
                                    <img src="{{ getImage(getFilePath('extensions') .'/'. $extension->image, getFileSize('extensions')) }}" alt="{{ __($extension->name) }}" class="plugin_bg rounded">
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-1">{{ __($extension->name) }}</h6>
                                    <span class="badge bg--primary mb-2">{{ $category }}</span>
                                    <p class="text-muted small mb-2 extension-desc">{{ Str::limit(__($extension->description), 70) }}</p>
                                    @php echo $extension->statusBadge; @endphp
                                </div>
                            </div>
                            <div class="button--group mt-3 d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-sm btn-outline--primary editBtn"
                                        data-name="{{ __($extension->name) }}"
                                        data-shortcode="{{ json_encode($extension->shortcode ?? (object)[]) }}"
                                        data-action="{{ route('admin.extensions.update', $extension->id) }}">
                                    <i class="la la-cogs"></i> @lang('Configure')
                                </button>
                                <button type="button" class="btn btn-sm btn-outline--dark helpBtn"
                                        data-description="{{ __($extension->description) }}"
                                        data-support="{{ __($extension->support) }}">
                                    <i class="la la-question"></i> @lang('Help')
                                </button>
                                @if($extension->status == Status::DISABLE)
                                    <button type="button" class="btn btn-sm btn-outline--success confirmationBtn"
                                            data-action="{{ route('admin.extensions.status', $extension->id) }}"
                                            data-question="@lang('Are you sure to enable this extension?')">
                                        <i class="la la-eye"></i> @lang('Enable')
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                            data-action="{{ route('admin.extensions.status', $extension->id) }}"
                                            data-question="@lang('Are you sure to disable this extension?')">
                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                    </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                        data-action="{{ route('admin.extensions.delete', $extension->id) }}"
                                        data-question="@lang('Are you sure to remove this extension? This cannot be undone.')">
                                    <i class="la la-trash"></i> @lang('Remove')
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
    <div class="d-inline-flex flex-wrap gap-2 align-items-center">
        <a href="{{ route('admin.extensions.create') }}" class="btn btn--primary btn-sm">
            <i class="las la-plus"></i> @lang('Add Extension')
        </a>
        <div class="input-group extension-search-wrap" style="max-width: 260px;">
            <input type="text" name="search_table" class="form-control bg--white extension-search" placeholder="@lang('Search extensions')...">
            <button class="btn btn--primary input-group-text" type="button"><i class="fa fa-search"></i></button>
        </div>
    </div>
@endpush

@push('style')
<style>
.extension-card { transition: box-shadow 0.2s ease; }
.extension-card:hover { box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.08); }
.extension-thumb .plugin_bg { width: 48px; height: 48px; object-fit: contain; }
.extension-category-tabs .nav-link { border-radius: 8px; }
.extension-category-tabs .nav-link.active { font-weight: 600; }
</style>
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
