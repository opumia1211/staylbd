@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        @include('admin.partials.profile_sidebar')

        <div class="col-xl-9 col-lg-8 mb-30">
            <div class="card border-0 shadow-sm b-radius--10">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="mb-0">
                        <i class="las la-key me-2 text--primary"></i>@lang('Change Password')
                    </h5>
                    <p class="text-muted small mb-0 mt-1">@lang('Update your admin panel login password. You will need to use the new password on your next login.')</p>
                </div>
                <div class="card-body pt-2 px-4 pb-4">
                    <form action="{{ route('admin.password.update') }}" method="POST" id="adminPasswordForm" autocomplete="off">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold">@lang('Current Password') <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control form-control-lg @error('old_password') is-invalid @enderror"
                                       name="old_password"
                                       id="old_password"
                                       required
                                       autocomplete="current-password"
                                       placeholder="@lang('Enter your current password')">
                                <button type="button" class="input-group-text btn-password-toggle" data-target="old_password" title="@lang('Show/Hide')">
                                    <i class="las la-eye"></i>
                                </button>
                                @error('old_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold">@lang('New Password') <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                                       name="password"
                                       id="new_password"
                                       required
                                       autocomplete="new-password"
                                       placeholder="@lang('Enter new password')">
                                <button type="button" class="input-group-text btn-password-toggle" data-target="new_password" title="@lang('Show/Hide')">
                                    <i class="las la-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">@lang('Minimum 8 characters; mix of letters, numbers and symbols recommended.')</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold">@lang('Confirm New Password') <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control form-control-lg"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       required
                                       autocomplete="new-password"
                                       placeholder="@lang('Re-enter new password')">
                                <button type="button" class="input-group-text btn-password-toggle" data-target="password_confirmation" title="@lang('Show/Hide')">
                                    <i class="las la-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <button type="submit" class="btn btn--primary btn-lg px-4" id="submitPasswordBtn">
                                <i class="las la-check-circle me-1"></i>@lang('Update Password')
                            </button>
                            <a href="{{ route('admin.profile') }}" class="btn btn-outline--secondary btn-lg">@lang('Back to Profile')</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.profile') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-user me-1"></i>@lang('Profile Setting')
    </a>
@endpush

@push('script')
<script>
(function() {
    document.querySelectorAll('.btn-password-toggle').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-target');
            var input = document.getElementById(id);
            var icon = this.querySelector('i');
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) icon.classList.replace('la-eye', 'la-eye-slash');
            } else {
                input.type = 'password';
                if (icon) icon.classList.replace('la-eye-slash', 'la-eye');
            }
        });
    });
    var form = document.getElementById('adminPasswordForm');
    if (form) {
        form.addEventListener('submit', function() {
            var btn = document.getElementById('submitPasswordBtn');
            if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>@lang("Updating...")'; }
        });
    }
})();
</script>
@endpush
