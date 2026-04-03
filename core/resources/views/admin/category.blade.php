@extends('admin.layouts.app')
@section('panel')
    {{-- Toolbar: per page, sort, total count (advanced e‑commerce style) --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <label class="mb-0 text-muted small">@lang('Per page')</label>
                    <select id="adminCategoryPerPage" class="form-select form-select-sm" style="width: auto;">
                        @php $currentPerPage = (int) request('per_page', getPaginate()); @endphp
                        @foreach([10, 20, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ $currentPerPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    <label class="mb-0 text-muted small ms-2">@lang('Sort')</label>
                    <select id="adminCategorySort" class="form-select form-select-sm" style="width: auto;">
                        <option value="name_asc" {{ ($sortBy ?? 'name') == 'name' && ($sortDir ?? 'asc') == 'asc' ? 'selected' : '' }}>@lang('Name A–Z')</option>
                        <option value="name_desc" {{ ($sortBy ?? 'name') == 'name' && ($sortDir ?? '') == 'desc' ? 'selected' : '' }}>@lang('Name Z–A')</option>
                        <option value="products_count_desc" {{ ($sortBy ?? '') == 'products_count' && ($sortDir ?? '') == 'desc' ? 'selected' : '' }}>@lang('Products (high first)')</option>
                        <option value="products_count_asc" {{ ($sortBy ?? '') == 'products_count' && ($sortDir ?? '') == 'asc' ? 'selected' : '' }}>@lang('Products (low first)')</option>
                        <option value="subcategories_count_desc" {{ ($sortBy ?? '') == 'subcategories_count' && ($sortDir ?? '') == 'desc' ? 'selected' : '' }}>@lang('Subcategories (high first)')</option>
                        <option value="created_at_desc" {{ ($sortBy ?? '') == 'created_at' && ($sortDir ?? '') == 'desc' ? 'selected' : '' }}>@lang('Newest first')</option>
                        <option value="created_at_asc" {{ ($sortBy ?? '') == 'created_at' && ($sortDir ?? '') == 'asc' ? 'selected' : '' }}>@lang('Oldest first')</option>
                    </select>
                </div>
                <div class="text-muted small">
                    @lang('Total'): <strong>{{ $categories->total() }}</strong> @lang('categories')
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk action bar (hidden until selection) --}}
    <div id="categoryBulkBar" class="row mb-3 d-none">
        <div class="col-12">
            <div class="card b-radius--10 border-primary">
                <div class="card-body py-2 d-flex flex-wrap align-items-center gap-2">
                    <span class="text-muted small"><span id="categoryBulkCount">0</span> @lang('selected')</span>
                    <button type="button" class="btn btn-sm btn-outline--primary" id="categoryBulkEnable">@lang('Enable')</button>
                    <button type="button" class="btn btn-sm btn-outline--danger" id="categoryBulkDisable">@lang('Disable')</button>
                    <button type="button" class="btn btn-sm btn-outline--info" id="categoryBulkFeatured">@lang('Featured')</button>
                    <button type="button" class="btn btn-sm btn-outline--warning" id="categoryBulkUnfeatured">@lang('Unfeatured')</button>
                    <button type="button" class="btn btn-sm btn-outline--dark" id="categoryBulkClear">@lang('Clear selection')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center px-3 pt-3 pb-2 border-bottom">
                    <h6 class="mb-0">@lang('Categories')</h6>
                    <span class="badge bg--primary">
                        @lang('Total'):&nbsp;{{ $categories->total() }}
                    </span>
                </div>
                    <div class="table-responsive--sm">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th style="width: 36px;">
                                        <input type="checkbox" class="form-check-input" id="categorySelectAll" title="@lang('Select all')">
                                    </th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Featured')</th>
                                    <th>@lang('Publish')</th>
                                    <th class="text-center">@lang('Subcategories')</th>
                                    <th class="text-center">@lang('Products')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input category-row-check" value="{{ $category->id }}" data-status="{{ $category->status }}" data-featured="{{ $category->featured ? 1 : 0 }}">
                                        </td>
                                        <td>
                                            <strong>{{ __($category->name) }}</strong>
                                        </td>
                                        <td>
                                            <div class="category-img-cell">
                                                <div class="category-thumb-square">
                                                    <img src="{{ $category->imageShow() }}" alt="@lang('image')" loading="lazy">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php echo $category->statusBadge; @endphp
                                        </td>
                                        <td>
                                            @if ($category->featured)
                                                <span class="badge badge--primary">@lang('Yes')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('No')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php $pub = $category->publish_status ?? 'public'; $schedAt = $category->scheduled_at ?? null; @endphp
                                            @if ($pub === 'public')
                                                <span class="badge badge--success">@lang('Public')</span>
                                            @elseif ($pub === 'pending')
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @else
                                                <span class="badge badge--info">@lang('Scheduled')</span>
                                                @if ($schedAt)
                                                    <small class="d-block text-muted">{{ $schedAt->format('M d, Y H:i') }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (($category->subcategories_count ?? 0) > 0)
                                                <a href="{{ route('admin.subcategory.index') }}?search={{ urlencode($category->name) }}" class="badge bg-info text-decoration-none" title="@lang('View subcategories')">{{ $category->subcategories_count }}</a>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (($category->products_count ?? 0) > 0)
                                                <a href="{{ route('category.products', [slug($category->name), $category->id]) }}" target="_blank" rel="noopener" class="badge bg-success text-decoration-none" title="@lang('View products on site')">{{ $category->products_count }}</a>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline--primary dropdown-toggle" type="button" id="catAction{{ $category->id }}" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                                    <i class="las la-ellipsis-v"></i> @lang('Action')
                                                </button>
                                                <div class="dropdown-menu p-0 dropdown-menu-end" aria-labelledby="catAction{{ $category->id }}">
                                                @php
                                                    $catScheduled = isset($category->scheduled_at) && $category->scheduled_at ? $category->scheduled_at->format('Y-m-d\TH:i') : '';
                                                    $categoryResource = [
                                                        'id' => $category->id,
                                                        'name' => $category->name,
                                                        'image_with_path' => $category->imageShow(),
                                                        'publish_status' => $category->publish_status ?? 'public',
                                                        'scheduled_at' => $catScheduled,
                                                        'home_line' => $category->home_line ?? 1,
                                                        'home_order' => $category->home_order ?? 0,
                                                    ];
                                                @endphp
                                                <button type="button" class="dropdown-item editBtn cuModalBtn" data-resource='@json($categoryResource)' data-modal_title="@lang('Edit Category')">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </button>

                                                <a class="dropdown-item" href="{{ route('admin.subcategory.index') }}?search={{ urlencode($category->name) }}">
                                                    <i class="las la-list"></i> @lang('Subcategories')
                                                </a>

                                                @if (!$category->status)
                                                    <button type="button" class="dropdown-item text--primary confirmationBtn" data-action="{{ route('admin.category.status', $category->id) }}" data-question="@lang('Are you sure to enable this category?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button" class="dropdown-item text--danger confirmationBtn" data-action="{{ route('admin.category.status', $category->id) }}" data-question="@lang('Are you sure to disable this category?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif

                                                @if (!$category->featured)
                                                    <button type="button" class="dropdown-item text--info confirmationBtn" data-action="{{ route('admin.category.featured', $category->id) }}" data-question="@lang('Are you sure to featured this category?')">
                                                        <i class="las la-check-circle"></i> @lang('Featured')
                                                    </button>
                                                @else
                                                    <button type="button" class="dropdown-item text--warning confirmationBtn" data-action="{{ route('admin.category.featured', $category->id) }}" data-question="@lang('Are you sure to not featured this ?')">
                                                        <i class="las la-times-circle"></i> @lang('Unfeatured')
                                                    </button>
                                                @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5 text-muted" colspan="9">
                                            <i class="las la-folder-open fa-3x mb-2 d-block"></i>
                                            {{ __($emptyMessage) }}
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('Add New Category')">
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
                @if ($categories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($categories) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    {{-- Create or Update Modal --}}
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="cuModalTitle" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form id="adminCategoryForm" action="{{ route('admin.category.store') }}" method="POST" enctype="multipart/form-data" data-base-action="{{ route('admin.category.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('Name') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required />
                                </div>
                                <div class="form-group">
                                    <label>@lang('Image')</label>
                                    <div class="image-upload category-image-upload">
                                        <div class="thumb">
                                            <div class="avatar-preview mb-2 category-image-preview-wrap">
                                                <div class="profilePicPreview category-image-preview" id="categoryImagePreview" style="background-image: url('{{ getImage(getFilePath('category'), getFileSize('category')) }}')">
                                                    <div class="category-image-helper-overlay category-image-helper">
                                                        <div class="category-image-helper-icon">
                                                            <i class="las la-image"></i>
                                                        </div>
                                                        <div class="category-image-helper-text">
                                                            <div class="category-image-helper-title">
                                                                @lang('Upload category image')
                                                            </div>
                                                            <div class="category-image-helper-meta">
                                                                <span>PNG · JPG · WebP · SVG</span>
                                                                <span><span>{{ __(getFileSize('category')) }}</span> @lang('px square')</span>
                                                                <span>@lang('Auto resize & WebP for speed')</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="remove-image" aria-label="@lang('Remove')"><i class="fa fa-times"></i></button>
                                                </div>
                                                <span class="category-preview-loading d-none"><i class="las la-spinner la-spin"></i> @lang('Loading...')</span>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" class="profilePicUpload d-none" name="image" id="categoryImageInput" accept=".png,.jpg,.jpeg,.webp,.svg">
                                                <label for="categoryImageInput" class="bg--primary">@lang('Upload Image')</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Publish Status')</label>
                                    <select class="form-control" name="publish_status" id="categoryPublishStatus">
                                        <option value="public">@lang('Public')</option>
                                        <option value="pending">@lang('Pending')</option>
                                        <option value="scheduled">@lang('Scheduled')</option>
                                    </select>
                                    <small class="text-muted">@lang('Public') = live on site, @lang('Pending') = draft, @lang('Scheduled') = publish at date/time below.</small>
                                </div>
                                <div class="form-group" id="categoryScheduledAtWrap">
                                    <label>@lang('Scheduled at')</label>
                                    <input type="datetime-local" class="form-control" name="scheduled_at" id="categoryScheduledAt" value="{{ old('scheduled_at') }}" />
                                    <small class="text-muted">@lang('Leave empty for immediate or when using Pending.')</small>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Homepage line (row)')</label>
                                    <select name="home_line" class="form-control" id="categoryHomeLine">
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <small class="text-muted">@lang('Controls which Category row on homepage this category appears in.')</small>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Homepage order in line')</label>
                                    <input type="number" class="form-control" name="home_order" id="categoryHomeOrder" min="0" max="9999" value="0" />
                                    <small class="text-muted">@lang('Lower number = shown earlier inside its line.')</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45" id="categorySubmitBtn">
                            <span class="btn-text">@lang('Submit')</span>
                            <span class="btn-loading d-none"><i class="las la-spinner la-spin me-1"></i> @lang('Saving...')</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by name..." />
    <button type="button" class="btn btn-sm btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New Category')">
        <i class="las la-plus"></i> @lang('Add New')
    </button>
@endpush

@push('style')
<style>
    /* Category modal always on top - above backdrop so form is clickable */
    #cuModal { z-index: 10600 !important; position: fixed !important; }
    #cuModal .modal-dialog { z-index: 10602 !important; position: relative; max-width: 460px; }
    body.modal-open .modal-backdrop { z-index: 10598 !important; }
    #cuModal .modal-content { position: relative; z-index: 1; pointer-events: auto; border-radius: 14px; border: 0; box-shadow: 0 18px 45px rgba(15,23,42,0.28); }
    #cuModal .modal-body { padding: 14px 18px 8px; }
    #cuModal .modal-footer { padding: 8px 18px 14px; border-top: 0; }
    #cuModal .form-group { margin-bottom: 12px; }
    /* Category table: square small thumb (Daraz-style) – কোন ফটো আপলোড করা হয়েছে স্পষ্ট বোঝা যাবে */
    .category-img-cell { display: inline-block; }
    .category-img-cell .category-thumb-square {
        width: 80px;
        height: 80px;
        border-radius: 0;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.08);
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .category-img-cell .category-thumb-square img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }
    /* Square image preview in category modal – সিলেক্ট করলেই দেখা যাবে */
    .category-image-preview-wrap { display: block; position: relative; margin-bottom: 0.5rem; }
    .category-image-preview {
        display: block;
        width: 150px;
        height: 150px;
        min-width: 150px;
        min-height: 150px;
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #f1f5f9;
        border-radius: 10px;
        border: 1px solid rgba(0,0,0,.12);
        transition: background-image 0.15s ease;
        position: relative;
        overflow: hidden;
    }
    .category-image-preview.has-image { background-size: contain; }
    .category-image-helper-overlay {
        position: absolute;
        inset: 10px;
        border-radius: 8px;
        background: radial-gradient(circle at top, rgba(59,130,246,0.16), transparent 55%), rgba(15,23,42,0.96);
        color: #f9fafb;
        font-size: 12px;
        line-height: 1.4;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 6px 8px;
        pointer-events: none;
    }
    .category-image-helper-icon {
        width: 40px;
        height: 40px;
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 4px;
        background: rgba(15,23,42,0.85);
    }
    .category-image-helper-icon i {
        font-size: 20px;
    }
    .category-image-helper-title {
        font-weight: 600;
        margin-bottom: 2px;
    }
    .category-image-helper-meta {
        font-size: 11px;
        opacity: 0.9;
        display: flex;
        flex-direction: column;
        gap: 1px;
    }
    .category-image-preview.has-image .category-image-helper-overlay { display: none; }
    .category-image-preview .remove-image {
        position: absolute;
        top: 6px;
        right: 6px;
        z-index: 2;
    }
    #cuModal #categorySubmitBtn {
        border-radius: 999px;
        font-weight: 600;
        box-shadow: 0 12px 24px rgba(79,70,229,0.45);
        background: linear-gradient(90deg, #4f46e5, #6366f1);
        border: none;
        color: #ffffff !important;
    }
    #cuModal #categorySubmitBtn .btn-text,
    #cuModal #categorySubmitBtn .btn-loading {
        color: inherit;
    }
    #cuModal #categorySubmitBtn[disabled] {
        box-shadow: none;
        opacity: .75;
        background: #4f46e5;
        color: #e5e7eb !important;
    }
    .category-preview-loading {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        font-size: 12px;
        color: #64748b;
        pointer-events: none;
    }
</style>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            // Move category modal to body so it is above backdrop (gray overlay) and form is clickable
            function ensureCuModalInBody() {
                var modal = document.getElementById('cuModal');
                if (modal && modal.parentNode && modal.parentNode !== document.body) {
                    document.body.appendChild(modal);
                }
                // Remove any stale backdrops that might be blocking the page
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', ensureCuModalInBody);
            } else {
                ensureCuModalInBody();
            }
            $(window).on('load', function() { ensureCuModalInBody(); });

            $('.editBtn').on('click', function() {
                $('#cuModal').find('[name=image]').removeAttr('required');
                $('#cuModal').find('[name=image]').closest('.form-group').find('label').first().removeClass('required');
            });

            var placeHolderImage = "{{ getImage(getFilePath('category'), getFileSize('category')) }}";

            $('#cuModal').on('hidden.bs.modal', function() {
                var pe = document.getElementById('categoryImagePreview');
                if (pe) {
                    pe.style.backgroundImage = placeHolderImage ? 'url("' + String(placeHolderImage).replace(/"/g, '%22') + '")' : 'none';
                    pe.classList.remove('has-image');
                }
                $('#cuModal').find('.category-image-helper').removeClass('d-none');
                $('#cuModal').find('.category-preview-loading').addClass('d-none');
                $('#cuModal').find('[name=name]').val('');
                $('#cuModal').find('[name=image]').val('').attr('required', 'required');
                $('#cuModal').find('[name=image]').closest('.form-group').find('label').first().addClass('required');
                var baseAction = $('#adminCategoryForm').data('base-action');
                if (baseAction) $('#adminCategoryForm').attr('action', baseAction);
                $('#categoryPublishStatus').val('public');
                $('#categoryScheduledAt').val('');
            });

            function toggleScheduledAt() {
                var v = $('#categoryPublishStatus').val();
                $('#categoryScheduledAtWrap').toggle(v === 'scheduled');
            }
            $('#categoryPublishStatus').on('change', toggleScheduledAt);
            toggleScheduledAt();
            $('#cuModal').on('shown.bs.modal', toggleScheduledAt);

            // ছবি সিলেক্ট করামাত্র প্রিভিউ বক্সে দেখা যাবে (instant preview)
            $(document).on('change', '#cuModal #categoryImageInput', function () {
                var input = this;
                var previewEl = document.getElementById('categoryImagePreview');
                var loadingEl = document.querySelector('#cuModal .category-preview-loading');
                var helperEl = document.querySelector('#cuModal .category-image-helper');
                if (!input.files || !input.files[0] || !previewEl) return;
                if (loadingEl) loadingEl.classList.remove('d-none');
                if (helperEl) helperEl.classList.add('d-none');
                var reader = new FileReader();
                reader.onload = function (e) {
                    var dataUrl = e.target.result;
                    previewEl.style.backgroundImage = dataUrl ? 'url(' + dataUrl + ')' : 'none';
                    previewEl.classList.add('has-image');
                    if (loadingEl) loadingEl.classList.add('d-none');
                };
                reader.onerror = function() {
                    if (loadingEl) loadingEl.classList.add('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            });
            $(document).on('click', '#cuModal .remove-image', function (e) {
                e.preventDefault();
                var previewEl = document.getElementById('categoryImagePreview');
                var inputEl = document.getElementById('categoryImageInput');
                var helperEl = document.querySelector('#cuModal .category-image-helper');
                if (previewEl) {
                    previewEl.style.backgroundImage = placeHolderImage ? 'url("' + String(placeHolderImage).replace(/"/g, '%22') + '")' : 'none';
                    previewEl.classList.remove('has-image');
                }
                if (inputEl) inputEl.value = '';
                if (helperEl) helperEl.classList.remove('d-none');
            });
            // সাবমিটে লোডিং – আপলোড স্লো মনে না হয়
            $('#adminCategoryForm').on('submit', function() {
                var $btn = $('#categorySubmitBtn');
                $btn.prop('disabled', true).find('.btn-text').addClass('d-none').end().find('.btn-loading').removeClass('d-none');
            });

            // --- Per page (preserve search & sort) ---
            function currentParams() {
                var p = new URLSearchParams(window.location.search);
                return Object.fromEntries(p.entries());
            }
            function goWithParams(extra) {
                var params = currentParams();
                for (var k in extra) params[k] = extra[k];
                var q = new URLSearchParams(params).toString();
                window.location = (q ? '?' + q : window.location.pathname);
            }
            $('#adminCategoryPerPage').on('change', function() {
                goWithParams({ per_page: $(this).val(), page: 1 });
            });
            $('#adminCategorySort').on('change', function() {
                var v = $(this).val();
                var parts = v.split('_');
                var dir = parts.pop();
                var by = parts.join('_');
                goWithParams({ sort_by: by, sort_dir: dir, page: 1 });
            });

            // --- Bulk actions ---
            function getSelectedIds() {
                return $('.category-row-check:checked').map(function() { return $(this).val(); }).get();
            }
            function updateBulkBar() {
                var ids = getSelectedIds();
                var n = ids.length;
                $('#categoryBulkCount').text(n);
                if (n) $('#categoryBulkBar').removeClass('d-none'); else $('#categoryBulkBar').addClass('d-none');
            }
            $('#categorySelectAll').on('change', function() {
                $('.category-row-check').prop('checked', $(this).prop('checked'));
                updateBulkBar();
            });
            $(document).on('change', '.category-row-check', updateBulkBar);

            function bulkSubmit(url, value) {
                var ids = getSelectedIds();
                if (!ids.length) return;
                var f = $('<form method="POST" action="' + url + '"></form>');
                f.append($('<input type="hidden" name="_token">').val($('meta[name="csrf-token"]').attr('content')));
                ids.forEach(function(id) {
                    f.append($('<input type="hidden" name="ids[]">').val(id));
                });
                f.append($('<input type="hidden" name="value">').val(value));
                $('body').append(f);
                f.submit();
            }
            $('#categoryBulkEnable').on('click', function() { bulkSubmit('{{ route('admin.category.bulk.status') }}', 1); });
            $('#categoryBulkDisable').on('click', function() { bulkSubmit('{{ route('admin.category.bulk.status') }}', 0); });
            $('#categoryBulkFeatured').on('click', function() { bulkSubmit('{{ route('admin.category.bulk.featured') }}', 1); });
            $('#categoryBulkUnfeatured').on('click', function() { bulkSubmit('{{ route('admin.category.bulk.featured') }}', 0); });
            $('#categoryBulkClear').on('click', function() {
                $('.category-row-check').prop('checked', false);
                $('#categorySelectAll').prop('checked', false);
                updateBulkBar();
            });
        })(jQuery);
    </script>
@endpush
