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
    <div class="row mb-2">
        <div class="col">
            <nav aria-label="breadcrumb" class="small"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.setting.admin.index') }}">@lang('Admin Management')</a></li><li class="breadcrumb-item active">@lang('Edit')</li></ol></nav>
            <h5 class="mb-0 mt-1">@lang('Edit') — {{ $admin->name ?? $admin->username }}</h5>
        </div>
        <div class="col-auto"><a href="{{ route('admin.setting.admin.index') }}" class="btn btn-outline-secondary btn-sm"><i class="las la-arrow-left me-1"></i>@lang('Back')</a></div>
    </div>

    @if(session('reset_admin_credentials'))
    @php $cred = session('reset_admin_credentials'); @endphp
    <div class="alert alert-info py-2 mb-2 small">
        <strong>@lang('New password'):</strong> <code id="editResetPassword">{{ $cred['password'] ?? '—' }}</code>
        <button type="button" class="btn btn-sm btn-outline-secondary py-0 ms-1" onclick="navigator.clipboard && navigator.clipboard.writeText(document.getElementById('editResetPassword').innerText)">@lang('Copy')</button>
    </div>
    @endif

    <div class="card border shadow-sm mb-2">
        <div class="card-header py-2 px-3 bg-light"><strong class="small">@lang('Update Admin')</strong></div>
        <div class="card-body p-3">
            @if(!$hasSections && $showSectionsUI)
            <div class="alert alert-info py-2 mb-2 small">@lang('Run') <code>php artisan migrate</code> @lang('to save section access.')</div>
            @endif
            <form method="POST" action="{{ route('admin.setting.admin.update', $admin->id) }}" id="adminEditForm">
                @csrf
                <div class="row g-2 mb-2">
                    <div class="col-6 col-md-3"><input type="text" class="form-control form-control-sm" name="name" value="{{ old('name', $admin->name) }}" required placeholder="@lang('Name') *"></div>
                    <div class="col-6 col-md-3"><input type="text" class="form-control form-control-sm" name="username" value="{{ old('username', $admin->username) }}" required placeholder="@lang('Username') *"></div>
                    <div class="col-6 col-md-3"><input type="email" class="form-control form-control-sm" name="email" value="{{ old('email', $admin->email) }}" required placeholder="@lang('Email') *"></div>
                    @if($hasMobile)<div class="col-6 col-md-3"><input type="text" class="form-control form-control-sm" name="mobile" value="{{ old('mobile', $admin->mobile) }}" placeholder="@lang('Phone')"></div>@endif
                </div>
                @if(\Illuminate\Support\Facades\Schema::hasColumn('admins', 'admin_notes'))
                <div class="mb-2"><textarea class="form-control form-control-sm" name="admin_notes" rows="1" maxlength="500" placeholder="@lang('Notes')">{{ old('admin_notes', $admin->admin_notes ?? '') }}</textarea></div>
                @endif
                <div class="row g-2 mb-2">
                    <div class="col-6 col-md-2"><input type="password" class="form-control form-control-sm" name="password" id="editPassword" placeholder="@lang('New password')"></div>
                    <div class="col-6 col-md-2"><input type="password" class="form-control form-control-sm" name="password_confirmation" id="editPasswordConfirm" placeholder="@lang('Confirm')"></div>
                    <div class="col-auto"><label class="form-check-label small"><input type="checkbox" id="showPasswordToggle" class="form-check-input"> @lang('Show')</label></div>
                    @if($canChangeRole ?? false)
                    <div class="col-6 col-md-2"><select class="form-select form-select-sm" name="role"><option value="admin" @selected(($admin->role ?? 'admin') === 'admin')>Admin</option><option value="super_admin" @selected(($admin->role ?? '') === 'super_admin')>Super Admin</option></select></div>
                    @endif
                </div>
                @error('password')<span class="text-danger small">{{ $message }}</span>@enderror

                {{-- Access sections: same as Add Admin — 3 columns, selectable checkboxes --}}
                @if($showSectionsUI)
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <label class="fw-semibold text-dark mb-0">@lang('Which sections can this admin access?') <span class="text-muted fw-normal small">(@lang('নতুন ফিচার যোগ করলে এখানে অটো যুক্ত হবে — নিচে লিংক দিয়ে সরাসরি যেতে পারবেন')</span></label>
                        <span class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-edit-sections="all">@lang('All')</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-edit-sections="none">@lang('None')</button>
                        </span>
                    </div>
                    @if(($admin->role ?? '') === 'owner')
                    <p class="small text-muted mb-2">@lang('Owner has full access to all sections.')</p>
                    @elseif(!($canEditSections ?? true))
                    <p class="small text-warning mb-2">@lang('Only the project Owner can change section access.')</p>
                    @endif
                    <div class="admin-sections-edit-box border rounded p-3 bg-white">
                        <div class="row g-2">
                            @foreach($adminSections as $key => $label)
                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="form-check d-flex align-items-center gap-2">
                                    @if(($admin->role ?? '') === 'owner')
                                    <input type="checkbox" class="form-check-input mt-0" checked disabled title="@lang('Owner has all')">
                                    <span class="form-check-label text-dark fw-semibold">{{ __($label) }}</span>
                                    @else
                                    <input type="checkbox" class="form-check-input mt-0 edit-section-cb" name="allowed_sections[]" value="{{ $key }}" id="edit_sec_{{ $key }}" {{ in_array($key, is_array(old('allowed_sections')) ? old('allowed_sections') : $allowed) ? 'checked' : '' }} @if(!($canEditSections ?? true)) disabled @endif>
                                    <label class="form-check-label text-dark fw-semibold flex-grow-1" for="edit_sec_{{ $key }}">{{ __($label) }}</label>
                                    @if(!empty($sectionRoutes[$key]) && \Illuminate\Support\Facades\Route::has($sectionRoutes[$key]))
                                    <a href="{{ route($sectionRoutes[$key]) }}" class="text-primary text-decoration-none small" target="_blank" title="@lang('Open')">→</a>
                                    @endif
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                    <button type="submit" class="btn btn--primary btn-sm"><i class="las la-save me-1"></i>@lang('Update')</button>
                    <a href="{{ route('admin.setting.admin.index') }}" class="btn btn-outline-secondary btn-sm">@lang('Cancel')</a>
                    @if($canResetPassword)
                    <a href="#forgotPasswordSection" class="btn btn-outline-warning btn-sm">@lang('Forgot Password')</a>
                    @endif
                    <a href="{{ url('/contact') }}" class="btn btn-outline-info btn-sm ms-auto" target="_blank">@lang('যোগাযোগ করুন')</a>
                </div>
            </form>
        </div>
    </div>

    @if($canResetPassword)
    <div class="card border shadow-sm mb-2 border-warning" id="forgotPasswordSection">
        <div class="card-header py-2 px-3 bg-warning bg-opacity-25"><strong class="small">@lang('Forgot Password?') (@lang('Owner only'))</strong></div>
        <div class="card-body p-2">
            <form method="POST" action="{{ route('admin.setting.admin.reset.password', $admin->id) }}" class="d-flex flex-wrap align-items-end gap-2">
                @csrf
                <input type="password" class="form-control form-control-sm" name="new_password" required placeholder="@lang('New password')" style="width:140px">
                <input type="password" class="form-control form-control-sm" name="new_password_confirmation" required placeholder="@lang('Confirm')" style="width:140px">
                <button type="submit" class="btn btn-warning btn-sm">@lang('Reset')</button>
            </form>
        </div>
    </div>
    @endif
@endsection

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
(function() {
    var t = document.getElementById('showPasswordToggle');
    if (t) t.addEventListener('change', function() {
        var type = t.checked ? 'text' : 'password';
        var p = document.getElementById('editPassword'), c = document.getElementById('editPasswordConfirm');
        if (p) p.type = type; if (c) c.type = type;
    });
    document.querySelectorAll('[data-edit-sections="all"]').forEach(function(btn) {
        btn.addEventListener('click', function() { document.querySelectorAll('#adminEditForm .edit-section-cb:not([disabled])').forEach(function(c) { c.checked = true; }); });
    });
    document.querySelectorAll('[data-edit-sections="none"]').forEach(function(btn) {
        btn.addEventListener('click', function() { document.querySelectorAll('#adminEditForm .edit-section-cb:not([disabled])').forEach(function(c) { c.checked = false; }); });
    });
})();
</script>
@endpush
