@extends('admin.layouts.app')

@push('style')
<style>
    /* Add New Brand মডাল ভাসমান – সাইডবার/হেডারের উপরে, হিডেন হবে না */
    #cuModal { z-index: 10600 !important; position: fixed !important; }
    #cuModal .modal-dialog { z-index: 10602 !important; position: relative; }
    #cuModal .modal-content { position: relative; z-index: 1; pointer-events: auto; }
    body.modal-open .modal-backdrop { z-index: 10598 !important; }
    /* ব্র্যান্ড ইমেজ টেবিলে স্কয়ার, একটু বড় – কোন ফটো আপলোড হয়েছে স্পষ্ট বোঝা যাবে */
    .brand-img-cell { display: inline-block; }
    .brand-img-cell .brand-thumb-square { width: 80px; height: 80px; border-radius: 0; overflow: hidden; border: 1px solid rgba(0,0,0,0.08); background: #f8f9fa; display: flex; align-items: center; justify-content: center; }
    .brand-img-cell .brand-thumb-square img { width: 100%; height: 100%; object-fit: contain; display: block; }
</style>
@endpush

@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Featured')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $brand)
                                    <tr>
                                        <td>{{ __($brand->name) }}</td>
                                        <td>
                                            <div class="brand-img-cell">
                                                <div class="brand-thumb-square">
                                                    <img src="{{ $brand->imageShow() }}" alt="{{ __($brand->name) }}">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php echo $brand->statusBadge; @endphp
                                        </td>
                                        <td>
                                            @if ($brand->featured)
                                                <span class="badge badge--primary">@lang('Yes')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('No')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--primary" data-bs-toggle="dropdown">
                                                <i class="las la-ellipsis-v"></i> @lang('Action')
                                            </button>
                                            <div class="dropdown-menu p-0">
                                                @php
                                                    $brand->image_with_path = $brand->imageShow();
                                                @endphp

                                                <button type="button" class="dropdown-item editBtn cuModalBtn" data-resource="{{ $brand }}" data-modal_title="@lang('Edit Brand')">
                                                    <i class="las la-pen"></i> @lang('Edit')
                                                </button>

                                                @if (!$brand->status)
                                                    <button type="button" class="dropdown-item text--primary confirmationBtn" data-action="{{ route('admin.brand.status', $brand->id) }}" data-question="@lang('Are you sure to enable this brand?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button" class="dropdown-item text--danger confirmationBtn" data-action="{{ route('admin.brand.status', $brand->id) }}" data-question="@lang('Are you sure to disable this brand?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif

                                                @if (!$brand->featured)
                                                    <button type="button" class=" dropdown-item text--info confirmationBtn" data-action="{{ route('admin.brand.featured', $brand->id) }}" data-question="@lang('Are you sure to featured this brand?')">
                                                        <i class="las la-check-circle"></i> @lang('Featured')
                                                    </button>
                                                @else
                                                    <button type="button" class=" dropdown-item text--warning confirmationBtn" data-action="{{ route('admin.brand.featured', $brand->id) }}" data-question="@lang('Are you sure to not featured this brand?')">
                                                        <i class="las la-times-circle"></i> @lang('Unfeatured')
                                                    </button>
                                                @endif
                                                <div class="dropdown-divider"></div>
                                                <button type="button" class="dropdown-item text--danger confirmationBtn" data-action="{{ route('admin.brand.delete', $brand->id) }}" data-question="@lang('Are you sure to delete this brand? Image and database record will be removed.')">
                                                    <i class="las la-trash"></i> @lang('Delete')
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">
                                            {{ __($emptyMessage) }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($brands->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($brands) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    {{-- Create or Update Modal --}}
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.brand.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Image')</label>
                                    <div class="image-upload">
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview" style="background-image: url({{ getImage(getFilePath('brand'), getFileSize('brand')) }})">
                                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" class="profilePicUpload" name="image" id="profilePicUpload2" accept=".png,.jpg,.jpeg,.webp,.svg" required>
                                                <label for="profilePicUpload2" class="bg--primary">@lang('Upload Image')</label>
                                                <small class="mt-2">@lang('Supported files'): <b>PNG, JPG, WebP, SVG.</b> @lang('Image will be resized into ') <span>{{ __(getFileSize('brand')) }}</span> @lang('px') (SVG unchanged)</small>
                                            </div>
                                        </div>
                                    </div>
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
    <x-search-form placeholder="Search by name..." />
    <button type="button" class="btn btn-sm btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New Brand')">
        <i class="las la-plus"></i> @lang('Add New')
    </button>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.editBtn').on('click', function() {
                $('#cuModal').find('[name=image]').removeAttr('required');
                $('#cuModal').find('[name=image]').closest('.form-group').find('label').first().removeClass('required');
            });

            var placeHolderImage = "{{ getImage(getFilePath('brand'), getFileSize('brand')) }}";

            $('#cuModal').on('hidden.bs.modal', function() {
                $('#cuModal').find('.profilePicPreview').css({
                    'background-image': `url(${placeHolderImage})`
                });
                $('#cuModal').find('[name=image]').attr('required', 'required');
                $('#cuModal').find('[name=image]').closest('.form-group').find('label').first().addClass('required');
            });
        })(jQuery);
    </script>
@endpush
