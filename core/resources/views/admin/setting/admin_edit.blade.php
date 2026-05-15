@extends('admin.layouts.app')
@php
    $hasRole = $hasRole ?? \Illuminate\Support\Facades\Schema::hasColumn('admins', 'role');
    $hasMobile = $hasMobile ?? \Illuminate\Support\Facades\Schema::hasColumn('admins', 'mobile');
    $hasSections = $hasSections ?? \Illuminate\Support\Facades\Schema::hasColumn('admins', 'allowed_sections');
    $adminSections = $adminSections ?? config('admin_sections.sections', []);
    $sectionRoutes = $sectionRoutes ?? config('admin_sections.section_routes', []);
    $showSectionsUI = !empty($adminSections);
    $allowed = $admin->allowed_sections ?? [];
    $currentAdmin = auth()->guard('admin')->user();
    $isOwner = $currentAdmin && method_exists($currentAdmin, 'isOwner') && $currentAdmin->isOwner();
    $canResetPassword = $isOwner && ($admin->role ?? 'admin') !== 'owner' && $admin->id != ($currentAdmin->id ?? null);
@endphp

@section('panel')
<div class="container-xxl flex-grow-1 container-p-y p-0">
    {{-- Breadcrumbs & Header --}}
    <div class="row align-items-center mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.setting.admin.index') }}">@lang('Admin Management')</a></li>
                    <li class="breadcrumb-item active">@lang('Edit Administrator')</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">@lang('Update Details') — {{ $admin->name ?? $admin->username }}</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.setting.admin.index') }}" class="btn btn-label-secondary btn-sm"><i class="las la-arrow-left me-1"></i>@lang('Back to List')</a>
        </div>
    </div>

    @if(session('reset_admin_credentials'))
    @php $cred = session('reset_admin_credentials'); @endphp
    <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-center" role="alert">
        <div class="avatar bg-info text-white me-3 rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="las la-key"></i>
        </div>
        <div>
            <h6 class="alert-heading mb-1 text-info fw-bold">@lang('Password Reset Successful')</h6>
            <p class="mb-0 small">
                <strong>@lang('New Password'):</strong> <code class="bg-white px-2 py-0 rounded" id="editResetPass">{{ $cred['password'] }}</code>
                <button type="button" class="btn btn-sm btn-link py-0 text-info fw-bold" onclick="navigator.clipboard.writeText('{{ $cred['password'] }}')">@lang('Copy')</button>
            </p>
        </div>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-bottom py-3 px-4 bg-white">
                    <h6 class="mb-0 fw-bold"><i class="las la-user-edit me-2 text-primary"></i>@lang('Account Information')</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.setting.admin.update', $admin->id) }}" id="adminEditForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">@lang('Full Name')</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $admin->name) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">@lang('Username')</label>
                                <input type="text" class="form-control" name="username" value="{{ old('username', $admin->username) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">@lang('Email Address')</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', $admin->email) }}" required>
                            </div>
                            @if($hasMobile)
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">@lang('Phone Number')</label>
                                <input type="text" class="form-control" name="mobile" value="{{ old('mobile', $admin->mobile) }}">
                            </div>
                            @endif

                            @if($showSectionsUI)
                            <div class="col-12 mt-4">
                                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                                    <div>
                                        <h6 class="mb-0 text-dark fw-bold">@lang('Module Access Control')</h6>
                                        <small class="text-muted">@lang('Grant or revoke access to specific administrative sections.')</small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-label-secondary" data-edit-sections="all">@lang('Select All')</button>
                                        <button type="button" class="btn btn-label-secondary" data-edit-sections="none">@lang('Clear All')</button>
                                    </div>
                                </div>
                                
                                @if(($admin->role ?? '') === 'owner')
                                <div class="alert alert-label-primary border-0 mb-3">
                                    <i class="las la-info-circle me-2"></i> @lang('Owner account has unrestricted access to all modules.')
                                </div>
                                @endif

                                <div class="row g-3">
                                    @foreach($adminSections as $key => $label)
                                    <div class="col-md-4 col-lg-3">
                                        <div class="form-check p-3 border rounded h-100 hover-bg-light transition-all">
                                            @if(($admin->role ?? '') === 'owner')
                                            <input type="checkbox" class="form-check-input ms-0" checked disabled>
                                            <label class="form-check-label ms-2 fw-medium text-primary">{{ __($label) }}</label>
                                            @else
                                            <input class="form-check-input ms-0 edit-section-cb" type="checkbox" name="allowed_sections[]" value="{{ $key }}" id="edit_sec_{{ $key }}" {{ in_array($key, is_array(old('allowed_sections')) ? old('allowed_sections') : $allowed) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2 fw-medium text-dark" for="edit_sec_{{ $key }}">{{ __($label) }}</label>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="col-12 mt-4 pt-2 border-top d-flex gap-2 justify-content-end">
                                <a href="{{ route('admin.setting.admin.index') }}" class="btn btn-label-secondary px-4">@lang('Cancel')</a>
                                <button type="submit" class="btn btn-primary px-5 shadow-sm"><i class="las la-save me-2"></i> @lang('Update Profile')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($canResetPassword)
            <div class="card border-0 shadow-sm border-start border-warning border-3" id="forgotPasswordSection">
                <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h6 class="mb-1 fw-bold text-dark"><i class="las la-key me-2 text-warning"></i>@lang('Security: Remote Password Reset')</h6>
                        <p class="mb-0 text-muted small">@lang('As an Owner, you can force a password reset for this administrator.')</p>
                    </div>
                    <form method="POST" action="{{ route('admin.setting.admin.reset.password', $admin->id) }}" class="d-flex gap-2">
                        @csrf
                        <input type="password" class="form-control form-control-sm" name="new_password" required placeholder="@lang('New Password')" style="width: 160px;">
                        <input type="password" class="form-control form-control-sm" name="new_password_confirmation" required placeholder="@lang('Confirm')" style="width: 160px;">
                        <button type="submit" class="btn btn-warning btn-sm fw-bold">@lang('Reset Now')</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .breadcrumb-style1 .breadcrumb-item a { color: #8592a3; font-weight: 500; }
    .breadcrumb-style1 .breadcrumb-item.active { color: #696cff; font-weight: 600; }
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .alert-label-primary { background-color: #f0f7ff; color: #696cff; }
    .hover-bg-light:hover { background-color: #f0f7ff !important; cursor: pointer; }
    .transition-all { transition: all 0.2s ease-in-out; }
</style>
@endsection

@push('script')
<script>
    $(function() {
        $('[data-edit-sections="all"]').click(function() { $('.edit-section-cb:not([disabled])').prop('checked', true); });
        $('[data-edit-sections="none"]').click(function() { $('.edit-section-cb:not([disabled])').prop('checked', false); });
    });
</script>
@endpush
ent.querySelectorAll('#adminEditForm .edit-section-cb:not([disabled])').forEach(function(c) { c.checked = false; }); });
    });
})();
</script>
@endpush
