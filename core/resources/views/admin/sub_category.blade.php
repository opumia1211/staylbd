@extends('admin.layouts.app')
@section('panel')
    {{-- Toolbar: per page, sort, total --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <label class="mb-0 text-muted small">@lang('Per page')</label>
                    <select id="adminSubcatPerPage" class="form-select form-select-sm" style="width: auto;">
                        @php $currentPerPage = (int) request('per_page', getPaginate()); @endphp
                        @foreach([10, 20, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ $currentPerPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    <label class="mb-0 text-muted small ms-2">@lang('Sort')</label>
                    <select id="adminSubcatSort" class="form-select form-select-sm" style="width: auto;">
                        <option value="name_asc" {{ ($sortBy ?? 'name') == 'name' && ($sortDir ?? 'asc') == 'asc' ? 'selected' : '' }}>@lang('Name A–Z')</option>
                        <option value="name_desc" {{ ($sortBy ?? 'name') == 'name' && ($sortDir ?? '') == 'desc' ? 'selected' : '' }}>@lang('Name Z–A')</option>
                        <option value="products_count_desc" {{ ($sortBy ?? '') == 'products_count' && ($sortDir ?? '') == 'desc' ? 'selected' : '' }}>@lang('Products (high first)')</option>
                        <option value="products_count_asc" {{ ($sortBy ?? '') == 'products_count' && ($sortDir ?? '') == 'asc' ? 'selected' : '' }}>@lang('Products (low first)')</option>
                        <option value="created_at_desc" {{ ($sortBy ?? '') == 'created_at' && ($sortDir ?? '') == 'desc' ? 'selected' : '' }}>@lang('Newest first')</option>
                        <option value="created_at_asc" {{ ($sortBy ?? '') == 'created_at' && ($sortDir ?? '') == 'asc' ? 'selected' : '' }}>@lang('Oldest first')</option>
                    </select>
                </div>
                <div class="text-muted small">
                    @lang('Total'): <strong>{{ $subcategories->total() }}</strong> @lang('subcategories')
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk action bar --}}
    <div id="subcatBulkBar" class="row mb-3 d-none">
        <div class="col-12">
            <div class="card b-radius--10 border-primary">
                <div class="card-body py-2 d-flex flex-wrap align-items-center gap-2">
                    <span class="text-muted small"><span id="subcatBulkCount">0</span> @lang('selected')</span>
                    <button type="button" class="btn btn-sm btn-outline--primary" id="subcatBulkEnable">@lang('Enable')</button>
                    <button type="button" class="btn btn-sm btn-outline--danger" id="subcatBulkDisable">@lang('Disable')</button>
                    <button type="button" class="btn btn-sm btn-outline--dark" id="subcatBulkClear">@lang('Clear selection')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th style="width: 36px;">
                                        <input type="checkbox" class="form-check-input" id="subcatSelectAll" title="@lang('Select all')">
                                    </th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Category')</th>
                                    <th class="text-center">@lang('Products')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subcategories as $subcategory)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input subcat-row-check" value="{{ $subcategory->id }}">
                                        </td>
                                        <td><strong>{{ __($subcategory->name) }}</strong></td>
                                        <td>{{ __($subcategory->category->name ?? '') }}</td>
                                        <td class="text-center">
                                            <span class="text-muted">{{ $subcategory->products_count ?? 0 }}</span>
                                        </td>
                                        <td>@php echo $subcategory->statusBadge; @endphp</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline--primary dropdown-toggle" type="button" id="subcatAction{{ $subcategory->id }}" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                                    <i class="las la-ellipsis-v"></i> @lang('Action')
                                                </button>
                                                <div class="dropdown-menu p-0 dropdown-menu-end" aria-labelledby="subcatAction{{ $subcategory->id }}">
                                                    @php
                                                        $subcatResource = [
                                                            'id' => $subcategory->id,
                                                            'name' => $subcategory->name,
                                                            'category_id' => $subcategory->category_id,
                                                        ];
                                                    @endphp
                                                    <button type="button" class="dropdown-item editBtn cuModalBtn" data-resource='@json($subcatResource)' data-modal_title="@lang('Edit Subcategory')">
                                                        <i class="la la-pencil"></i> @lang('Edit')
                                                    </button>
                                                    <a class="dropdown-item" href="{{ route('admin.category.index') }}">
                                                        <i class="las la-stream"></i> @lang('Categories')
                                                    </a>
                                                    @if (!$subcategory->status)
                                                        <button type="button" class="dropdown-item text--primary confirmationBtn" data-action="{{ route('admin.subcategory.status', $subcategory->id) }}" data-question="@lang('Are you sure to enable this subcategory?')">
                                                            <i class="la la-eye"></i> @lang('Enable')
                                                        </button>
                                                    @else
                                                        <button type="button" class="dropdown-item text--danger confirmationBtn" data-action="{{ route('admin.subcategory.status', $subcategory->id) }}" data-question="@lang('Are you sure to disable this subcategory?')">
                                                            <i class="la la-eye-slash"></i> @lang('Disable')
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5 text-muted" colspan="6">
                                            <i class="las la-folder-open fa-3x mb-2 d-block"></i>
                                            {{ __($emptyMessage) }}
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('Add New Subcategory')">
                                                    <i class="las la-plus"></i> @lang('Add New')
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($subcategories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($subcategories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    {{-- Create or Update Modal --}}
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="subcatModalTitle" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subcatModalTitle"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form id="adminSubcatForm" action="{{ route('admin.subcategory.store') }}" method="POST" data-base-action="{{ route('admin.subcategory.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('Select Category') <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control" id="subcatCategoryId" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ __($category->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Name') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by name or category..." />
    <button type="button" class="btn btn-sm btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New Subcategory')">
        <i class="las la-plus"></i> @lang('Add New')
    </button>
@endpush

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('script')
<script>
(function($) {
    "use strict";

    function ensureSubcatModalInBody() {
        var modal = document.getElementById('cuModal');
        if (modal && modal.parentNode && modal.parentNode !== document.body) {
            document.body.appendChild(modal);
        }
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensureSubcatModalInBody);
    } else {
        ensureSubcatModalInBody();
    }
    $(window).on('load', ensureSubcatModalInBody);

    $('#cuModal').on('hidden.bs.modal', function() {
        $('#cuModal').find('[name=name]').val('');
        $('#cuModal').find('#subcatCategoryId').val('');
        var baseAction = $('#adminSubcatForm').data('base-action');
        if (baseAction) $('#adminSubcatForm').attr('action', baseAction);
    });

    function currentParams() {
        return Object.fromEntries(new URLSearchParams(window.location.search).entries());
    }
    function goWithParams(extra) {
        var params = currentParams();
        for (var k in extra) params[k] = extra[k];
        var q = new URLSearchParams(params).toString();
        window.location = (q ? '?' + q : window.location.pathname);
    }
    $('#adminSubcatPerPage').on('change', function() { goWithParams({ per_page: $(this).val(), page: 1 }); });
    $('#adminSubcatSort').on('change', function() {
        var v = $(this).val();
        var parts = v.split('_');
        var dir = parts.pop();
        var by = parts.join('_');
        goWithParams({ sort_by: by, sort_dir: dir, page: 1 });
    });

    function getSelectedIds() {
        return $('.subcat-row-check:checked').map(function() { return $(this).val(); }).get();
    }
    function updateBulkBar() {
        var n = getSelectedIds().length;
        $('#subcatBulkCount').text(n);
        if (n) $('#subcatBulkBar').removeClass('d-none'); else $('#subcatBulkBar').addClass('d-none');
    }
    $('#subcatSelectAll').on('change', function() {
        $('.subcat-row-check').prop('checked', $(this).prop('checked'));
        updateBulkBar();
    });
    $(document).on('change', '.subcat-row-check', updateBulkBar);

    function bulkSubmit(url, value) {
        var ids = getSelectedIds();
        if (!ids.length) return;
        var f = $('<form method="POST" action="' + url + '"></form>');
        f.append($('<input type="hidden" name="_token">').val($('meta[name="csrf-token"]').attr('content')));
        ids.forEach(function(id) { f.append($('<input type="hidden" name="ids[]">').val(id)); });
        f.append($('<input type="hidden" name="value">').val(value));
        $('body').append(f);
        f.submit();
    }
    $('#subcatBulkEnable').on('click', function() { bulkSubmit('{{ route('admin.subcategory.bulk.status') }}', 1); });
    $('#subcatBulkDisable').on('click', function() { bulkSubmit('{{ route('admin.subcategory.bulk.status') }}', 0); });
    $('#subcatBulkClear').on('click', function() {
        $('.subcat-row-check').prop('checked', false);
        $('#subcatSelectAll').prop('checked', false);
        updateBulkBar();
    });
})(jQuery);
</script>
@endpush
