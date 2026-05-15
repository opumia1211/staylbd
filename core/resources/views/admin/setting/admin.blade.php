@extends('admin.layouts.app')
@php
    $hasRole = $hasRole ?? \Illuminate\Support\Facades\Schema::hasColumn('admins', 'role');
    $hasMobile = $hasMobile ?? \Illuminate\Support\Facades\Schema::hasColumn('admins', 'mobile');
    $hasAllowedSections = $hasAllowedSections ?? \Illuminate\Support\Facades\Schema::hasColumn('admins', 'allowed_sections');
    $currentAdmin = auth()->guard('admin')->user();
    $canManage = $currentAdmin && method_exists($currentAdmin, 'canManageAdmins') && $currentAdmin->canManageAdmins();
    $total = $admins->count();
    $ownerCount = $hasRole ? $admins->where('role', 'owner')->count() : 0;
    $superCount = $hasRole ? $admins->where('role', 'super_admin')->count() : 0;
    $adminCount = $hasRole ? $admins->where('role', 'admin')->count() : 0;
    $adminSections = $adminSections ?? config('admin_sections.sections', []);
    $sectionRoutes = $sectionRoutes ?? config('admin_sections.section_routes', []);
    $showSectionsUI = !empty($adminSections);
    $isOwner = $currentAdmin && method_exists($currentAdmin, 'isOwner') && $currentAdmin->isOwner();
@endphp

@section('panel')
<div class="container-xxl flex-grow-1 container-p-y p-0">
    {{-- Header --}}
    <div class="row align-items-center mb-4">
        <div class="col">
            <h4 class="mb-1 fw-bold text-dark">@lang('Admin & Staff Management')</h4>
            <p class="text-muted small mb-0">@lang('Manage administrative access, assign roles, and control section-level permissions.')</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('admin.setting.system.configuration') }}" class="btn btn-label-primary btn-sm"><i class="las la-cog me-1"></i>@lang('Settings')</a>
            <a href="{{ route('admin.maintenance.dashboard') }}" class="btn btn-label-info btn-sm"><i class="las la-tools me-1"></i>@lang('Maintenance')</a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="text-muted small">@lang('Total Staff')</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2 fw-bold text-primary">{{ $total }}</h4>
                        </div>
                    </div>
                    <div class="avatar bg-label-primary p-2 rounded">
                        <i class="las la-users fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        @if($hasRole)
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="text-muted small">@lang('Owners')</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2 fw-bold text-warning">{{ $ownerCount }}</h4>
                        </div>
                    </div>
                    <div class="avatar bg-label-warning p-2 rounded">
                        <i class="las la-crown fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="text-muted small">@lang('Super Admins')</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2 fw-bold text-info">{{ $superCount }}</h4>
                        </div>
                    </div>
                    <div class="avatar bg-label-info p-2 rounded">
                        <i class="las la-user-shield fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="text-muted small">@lang('Standard Admins')</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2 fw-bold text-secondary">{{ $adminCount }}</h4>
                        </div>
                    </div>
                    <div class="avatar bg-label-secondary p-2 rounded">
                        <i class="las la-user fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Add New Admin Form --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between bg-white">
            <h6 class="mb-0 fw-bold"><i class="las la-plus-circle me-2 text-primary"></i>@lang('Register New Administrator')</h6>
            @if(!$canManage)
            <span class="badge bg-label-warning px-3 py-2"><i class="las la-lock me-1"></i> @lang('Read-Only Access')</span>
            @endif
        </div>
        <div class="card-body p-4">
            @if(session('new_admin_credentials'))
            @php $cred = session('new_admin_credentials'); @endphp
            <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" role="alert">
                <div class="avatar bg-success text-white me-3 rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                    <i class="las la-key"></i>
                </div>
                <div>
                    <h6 class="alert-heading mb-1 text-success fw-bold">@lang('New Admin Credentials — Save Now!')</h6>
                    <p class="mb-0 small">
                        <strong>@lang('Username'):</strong> <code class="bg-white px-2 py-0 rounded">{{ $cred['username'] }}</code> | 
                        <strong>@lang('Password'):</strong> <code class="bg-white px-2 py-0 rounded" id="newAdminPass">{{ $cred['password'] }}</code>
                        <button type="button" class="btn btn-sm btn-link py-0 text-success fw-bold" onclick="navigator.clipboard.writeText('{{ $cred['password'] }}')">@lang('Copy')</button>
                    </p>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.setting.admin.store') }}" id="createAdminForm">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">@lang('Full Name')</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">@lang('Username')</label>
                        <input type="text" class="form-control" name="username" value="{{ old('username') }}" required placeholder="e.g. admin_john">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">@lang('Email Address')</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required placeholder="john@example.com">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">@lang('Password')</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="createPassword" required placeholder="••••••••">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassVisibility()"><i class="las la-eye"></i></button>
                        </div>
                    </div>

                    @if($showSectionsUI)
                    <div class="col-12 mt-4">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                            <h6 class="mb-0 text-dark fw-bold">@lang('Feature Access Permissions')</h6>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-label-secondary" data-select-sections="all">@lang('Select All')</button>
                                <button type="button" class="btn btn-label-secondary" data-select-sections="none">@lang('Clear All')</button>
                            </div>
                        </div>
                        <div class="row g-3">
                            @foreach($adminSections as $key => $label)
                            <div class="col-md-4 col-lg-3">
                                <div class="form-check p-3 border rounded h-100 hover-bg-light transition-all">
                                    <input class="form-check-input ms-0" type="checkbox" name="allowed_sections[]" value="{{ $key }}" id="sec_{{ $key }}">
                                    <label class="form-check-label ms-2 fw-medium text-dark" for="sec_{{ $key }}">{{ __($label) }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="col-12 mt-4 pt-2 border-top text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm" id="saveAdminBtn" @if(!$canManage) disabled @endif>
                            <i class="las la-save me-2"></i> @lang('Register Administrator')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Admins Table --}}
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold"><i class="las la-list me-2 text-primary"></i>@lang('Active Administrators')</h6>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover mb-0">
                <thead class="bg-lighter">
                    <tr>
                        <th class="py-3">@lang('Administrator')</th>
                        <th class="py-3">@lang('Role')</th>
                        <th class="py-3">@lang('Access Level')</th>
                        <th class="py-3 text-end">@lang('Actions')</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($admins as $admin)
                    <tr>
                        <td class="py-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-label-secondary p-2 rounded-circle me-3">
                                    <span class="avatar-initial">{{ substr($admin->name ?? $admin->username, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $admin->name ?? '—' }}</h6>
                                    <small class="text-muted">{{ $admin->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php $r = $admin->role ?? 'admin'; @endphp
                            @if($r === 'owner')<span class="badge bg-label-primary px-3 py-2 rounded-pill fw-bold">Owner</span>
                            @elseif($r === 'super_admin')<span class="badge bg-label-info px-3 py-2 rounded-pill fw-bold">Super Admin</span>
                            @else<span class="badge bg-label-secondary px-3 py-2 rounded-pill fw-bold">Admin</span>@endif
                        </td>
                        <td>
                            @if($r === 'owner')
                            <span class="text-primary fw-bold small"><i class="las la-unlock me-1"></i> @lang('Full Access')</span>
                            @else
                            @php $secs = $admin->allowed_sections ?? []; @endphp
                            <span class="small text-muted">{{ count($secs) }} @lang('Modules Enabled')</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.setting.admin.edit', $admin->id) }}" class="btn btn-sm btn-icon btn-label-primary" title="@lang('Edit')"><i class="las la-edit"></i></a>
                                @if($isOwner && $r !== 'owner' && $admin->id != auth()->guard('admin')->id())
                                <button type="button" class="btn btn-sm btn-icon btn-label-warning" onclick="openResetModal('{{ $admin->id }}', '{{ $admin->name ?? $admin->username }}')"><i class="las la-key"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-5">@lang('No administrators found.')</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Reset Password Modal --}}
@if($isOwner)
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning py-3">
                <h5 class="modal-title text-white fw-bold"><i class="las la-key me-2"></i>@lang('Reset Admin Password')</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="resetPasswordForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <p class="mb-3 text-muted small">@lang('Performing a secure password reset for:') <strong class="text-dark" id="resetAdminName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">@lang('New Password')</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">@lang('Confirm New Password')</label>
                        <input type="password" class="form-control" name="new_password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold">@lang('Update Password')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
    .bg-lighter { background-color: #f8fafc !important; }
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-label-warning { background-color: #fff2e2 !important; color: #ffab00 !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .bg-label-secondary { background-color: #f0f2f4 !important; color: #8592a3 !important; }
    .hover-bg-light:hover { background-color: #f0f7ff !important; cursor: pointer; }
    .transition-all { transition: all 0.2s ease-in-out; }
</style>
@endsection

@push('script')
<script>
    function togglePassVisibility() {
        var p = document.getElementById('createPassword');
        p.type = p.type === 'password' ? 'text' : 'password';
    }

    function openResetModal(id, name) {
        var form = document.getElementById('resetPasswordForm');
        form.action = '{{ route("admin.setting.admin.reset.password", ["id" => "__ID__"]) }}'.replace('__ID__', id);
        document.getElementById('resetAdminName').innerText = name;
        new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
    }

    $(function() {
        $('[data-select-sections="all"]').click(function() { $('input[name="allowed_sections[]"]').prop('checked', true); });
        $('[data-select-sections="none"]').click(function() { $('input[name="allowed_sections[]"]').prop('checked', false); });
        
        $('#createAdminForm').submit(function() {
            var btn = $('#saveAdminBtn');
            btn.prop('disabled', true).html('<i class="las la-spinner la-spin me-2"></i> @lang("Registering...")');
        });
    });
</script>
@endpush
