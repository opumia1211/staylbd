@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        @include('admin.partials.profile_sidebar')

        <div class="col-xl-9 col-lg-8 mb-30">
            <div class="card border-0 shadow-sm b-radius--10">
                <div class="card-body">
                    <h5 class="card-title mb-4 border-bottom pb-2">@lang('Profile Information')</h5>

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xl-6 col-lg-12 col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold">@lang('Profile Image')</label>
                                    <div class="image-upload">
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                @php
                                                    $previewImg = $admin->image
                                                        ? getImageWebP(getFilePath('adminProfile') . '/' . $admin->image, getFileSize('adminProfile'))
                                                        : getImage('', '400x400');
                                                @endphp
                                                <div class="profilePicPreview" style="background-image: url({{ $previewImg }})">
                                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" class="profilePicUpload" name="image" id="profilePicUpload1" accept=".png,.jpg,.jpeg,.webp,.svg">
                                                <label for="profilePicUpload1" class="bg--success">@lang('Upload Image')</label>
                                                <small class="mt-2 d-block text-muted">@lang('Images: PNG, JPG, WebP, SVG. Resized to') {{ getFileSize('adminProfile') }}@lang('px. Uploaded images are automatically converted to WebP to save space.')</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-12 col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold">@lang('Name') <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="name" value="{{ old('name', $admin->name) }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label fw-semibold">@lang('Email') <span class="text-danger">*</span></label>
                                    <input class="form-control" type="email" name="email" value="{{ old('email', $admin->email) }}" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary h-45 px-4">@lang('Update Profile')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.password') }}" class="btn btn-sm btn-outline--primary"><i class="las la-key me-1"></i>@lang('Password')</a>
@endpush

@push('script')
<script>
(function() {
    var upload = document.getElementById('profilePicUpload1');
    if (upload) {
        var block = upload.closest('.image-upload');
        var preview = block ? block.querySelector('.profilePicPreview') : null;
        if (preview) {
            upload.addEventListener('change', function(e) {
                var f = e.target.files[0];
                if (!f) return;
                var r = new FileReader();
                r.onload = function() { preview.style.backgroundImage = 'url(' + r.result + ')'; };
                r.readAsDataURL(f);
            });
        }
        var removeBtn = block ? block.querySelector('.remove-image') : null;
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                if (preview) preview.style.backgroundImage = 'url({{ getImage("", "400x400") }}';
                if (upload) upload.value = '';
            });
        }
    }
})();
</script>
@endpush
