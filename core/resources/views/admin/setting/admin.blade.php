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
    {{-- Header --}}
    <div class="row align-items-center mb-4">
        <div class="col">
            <h4 class="mb-1">@lang('Admin Management')</h4>
            <p class="text-muted small mb-0">@lang('Owner has full control. Assign sections; new features can be controlled from here.')</p>
        </div>
        <div class="col-auto">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.setting.system.configuration') }}" class="btn btn-outline--primary btn-sm"><i class="las la-cog me-1"></i>@lang('User Panel')</a>
                <a href="{{ route('admin.maintenance.dashboard') }}" class="btn btn-outline--info btn-sm"><i class="las la-tools me-1"></i>@lang('Maintenance')</a>
                <a href="{{ route('admin.security.dashboard') }}" class="btn btn-outline--warning btn-sm"><i class="las la-shield-alt me-1"></i>@lang('Security')</a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-light mb-0 h-100">
                <div class="card-body py-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg--primary bg-opacity-25 p-2"><i class="las la-users text--primary fs-5"></i></div>
                    <div><h5 class="mb-0">{{ $total }}</h5><span class="small text-muted">@lang('Total')</span></div>
                </div>
            </div>
        </div>
        @if($hasRole)
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-light mb-0 h-100">
                <div class="card-body py-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-25 p-2"><i class="las la-crown text-primary fs-5"></i></div>
                    <div><h5 class="mb-0">{{ $ownerCount }}</h5><span class="small text-muted">Owner</span></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-light mb-0 h-100">
                <div class="card-body py-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-25 p-2"><i class="las la-user-shield text-info fs-5"></i></div>
                    <div><h5 class="mb-0">{{ $superCount }}</h5><span class="small text-muted">Super Admin</span></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-light mb-0 h-100">
                <div class="card-body py-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-secondary bg-opacity-25 p-2"><i class="las la-user text-secondary fs-5"></i></div>
                    <div><h5 class="mb-0">{{ $adminCount }}</h5><span class="small text-muted">Admin</span></div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Add New Admin --}}
    <div class="card border-0 shadow-sm mb-4 admin-add-card">
        <div class="card-header admin-add-card-header py-3 px-4">
            <h6 class="mb-0 admin-add-card-title"><i class="las la-plus-circle me-2"></i>@lang('Add New Admin')</h6>
        </div>
        <div class="card-body p-4">
            @if(!$canManage)
            <div class="alert alert-warning py-2 mb-3 small">
                <i class="las la-lock me-1"></i> @lang('Only the project Owner can add or manage admins.')
            </div>
            @endif
            @if(!$hasAllowedSections && $showSectionsUI)
            <div class="alert alert-info py-2 mb-3 small">
                <i class="las la-info-circle me-1"></i> @lang('Run') <code>php artisan migrate</code> @lang('or run DB patch to enable Role & Section access.')
            </div>
            @endif

            {{-- Show credentials once after adding admin --}}
            @if(session('new_admin_credentials'))
            @php $cred = session('new_admin_credentials'); @endphp
            <div class="alert alert-success border-success mb-4 shadow-sm credentials-once-box" role="alert">
                <h6 class="alert-heading"><i class="las la-key me-2"></i>@lang('Login credentials — save or copy now (shown once)')</h6>
                <div class="row g-2 small">
                    <div class="col-12 col-md-6"><strong>@lang('Name'):</strong> {{ $cred['name'] ?? '—' }}</div>
                    <div class="col-12 col-md-6"><strong>@lang('Email'):</strong> <code>{{ $cred['email'] ?? '—' }}</code></div>
                    <div class="col-12 col-md-6"><strong>@lang('Username'):</strong> <code>{{ $cred['username'] ?? '—' }}</code></div>
                    <div class="col-12 col-md-6"><strong>@lang('Password'):</strong> <code class="cred-password" id="newAdminPassword">{{ $cred['password'] ?? '—' }}</code> <button type="button" class="btn btn-sm btn-outline-secondary py-0 ms-1" onclick="navigator.clipboard && navigator.clipboard.writeText(document.getElementById('newAdminPassword').innerText)">@lang('Copy')</button></div>
                </div>
                <p class="mb-0 mt-2 small text-muted">@lang('Share these with the admin securely. This message will not show again.')</p>
            </div>
            @endif

            {{-- Show credentials once after Owner reset password --}}
            @if(session('reset_admin_credentials'))
            @php $cred = session('reset_admin_credentials'); @endphp
            <div class="alert alert-info border-info mb-4 shadow-sm credentials-once-box" role="alert">
                <h6 class="alert-heading"><i class="las la-unlock me-2"></i>@lang('New password set — share with admin (shown once)')</h6>
                <div class="row g-2 small">
                    <div class="col-12 col-md-6"><strong>@lang('Admin'):</strong> {{ $cred['name'] ?? '—' }} ({{ $cred['email'] ?? '—' }})</div>
                    <div class="col-12 col-md-6"><strong>@lang('New password'):</strong> <code class="cred-password" id="resetAdminPassword">{{ $cred['password'] ?? '—' }}</code> <button type="button" class="btn btn-sm btn-outline-secondary py-0 ms-1" onclick="navigator.clipboard && navigator.clipboard.writeText(document.getElementById('resetAdminPassword').innerText)">@lang('Copy')</button></div>
                </div>
                <p class="mb-0 mt-2 small text-muted">@lang('This message will not show again.')</p>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.setting.admin.store') }}" id="createAdminForm" class="admin-form">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-12"><span class="text-muted small fw-semibold">@lang('Name, Username, Email, Phone')</span></div>
                    <div class="col-6 col-md-3">
                        <input type="text" class="form-control form-control-sm" name="name" value="{{ old('name') }}" required maxlength="40" placeholder="@lang('Name') *">
                    </div>
                    <div class="col-6 col-md-3">
                        <input type="text" class="form-control form-control-sm" name="username" value="{{ old('username') }}" required maxlength="40" placeholder="@lang('Username') *">
                    </div>
                    <div class="col-6 col-md-3">
                        <input type="email" class="form-control form-control-sm" name="email" value="{{ old('email') }}" required placeholder="@lang('Email') *">
                    </div>
                    @if($hasMobile)
                    <div class="col-6 col-md-3">
                        <input type="text" class="form-control form-control-sm" name="mobile" value="{{ old('mobile') }}" maxlength="20" placeholder="@lang('Phone')">
                    </div>
                    @endif
                </div>
                @if(\Illuminate\Support\Facades\Schema::hasColumn('admins', 'admin_notes'))
                <div class="row g-3 mb-3">
                    <div class="col-12"><span class="text-muted small fw-semibold">@lang('Admin Notes') (Internal - recovery info, backup email, etc.)</span></div>
                    <div class="col-12">
                        <textarea class="form-control form-control-sm" name="admin_notes" rows="2" maxlength="500" placeholder="@lang('Optional: recovery email, backup info...')">{{ old('admin_notes') }}</textarea>
                    </div>
                </div>
                @endif
                @error('name')<span class="text-danger small d-block">{{ $message }}</span>@enderror
                @error('username')<span class="text-danger small d-block">{{ $message }}</span>@enderror
                @error('email')<span class="text-danger small d-block">{{ $message }}</span>@enderror

                <div class="row g-3 mb-3">
                    <div class="col-12"><span class="text-muted small fw-semibold">@lang('Password & Re-password')</span> <label class="ms-2 small fw-normal"><input type="checkbox" id="showPasswordToggle" class="form-check-input"> @lang('Show password')</label></div>
                    <div class="col-6 col-md-3">
                        <input type="password" class="form-control form-control-sm" name="password" id="createPassword" required autocomplete="new-password" placeholder="@lang('Password') *">
                    </div>
                    <div class="col-6 col-md-3">
                        <input type="password" class="form-control form-control-sm" name="password_confirmation" id="createPasswordConfirm" required placeholder="@lang('Re-password') *">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark mb-2">@lang('Assign role')</label>
                        <select class="form-select" name="role" id="adminRoleSelect" required style="max-width: 220px;">
                            <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="support" {{ old('role') === 'support' ? 'selected' : '' }}>Support</option>
                        </select>
                        <small class="text-muted d-block mt-1">@lang('Owner is fixed; only one per project. Assign Super Admin, Admin, Manager or Support.')</small>
                    </div>
                </div>
                @error('password')<span class="text-danger small d-block">{{ $message }}</span>@enderror

                @if($showSectionsUI)
                <div class="mb-3 mt-3 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <label class="small fw-semibold text-dark mb-0">@lang('Which sections can this admin access?') <span class="text-muted fw-normal">(@lang('নতুন ফিচার যোগ করলে এখানে অটো যুক্ত হবে — নিচে লিংক দিয়ে সরাসরি যেতে পারবেন')</span></label>
                        <span class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-select-sections="all">@lang('All')</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-select-sections="none">@lang('None')</button>
                        </span>
                    </div>
                    <div class="admin-sections-box border rounded p-3 admin-sections-inner">
                        <div class="row g-2">
                            @foreach($adminSections as $key => $label)
                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="form-check d-flex align-items-center gap-2 flex-wrap py-1">
                                    <input class="form-check-input mt-0" type="checkbox" name="allowed_sections[]" value="{{ $key }}" id="sec_{{ $key }}" {{ is_array(old('allowed_sections')) && in_array($key, old('allowed_sections')) ? 'checked' : '' }}>
                                    <label class="form-check-label admin-section-label mb-0 flex-grow-1" for="sec_{{ $key }}">{{ __($label) }}</label>
                                    @if(!empty($sectionRoutes[$key]) && \Illuminate\Support\Facades\Route::has($sectionRoutes[$key]))
                                    <a href="{{ route($sectionRoutes[$key]) }}" class="admin-section-link" target="_blank" title="@lang('Open this feature')">→</a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-3 pt-2 border-top">
                    <button type="submit" class="btn btn--primary" id="saveAdminBtn" @if(!$canManage) disabled @endif><i class="las la-save me-1"></i>@lang('Save Admin')</button>
                    <span class="small text-muted ms-2">@lang('নিয়োগের পর ইমেইল, ইউজারনেম, পাসওয়ার্ডসহ সব তথ্য একবার দেখা যাবে। সেভ করুন।')</span>
                </div>
            </form>
        </div>
    </div>

    {{-- New feature control: how to add --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light py-2 px-3 d-flex align-items-center justify-content-between">
            <span class="small fw-semibold"><i class="las la-puzzle-piece me-1 text-info"></i> @lang('নতুন ফিচার যুক্ত করলে কন্ট্রোল এখানে যুক্ত করতে')</span>
            <button class="btn btn-sm btn-outline-secondary py-0" type="button" data-bs-toggle="collapse" data-bs-target="#addFeaturesInfo" aria-expanded="false">@lang('Show')</button>
        </div>
        <div class="collapse" id="addFeaturesInfo">
            <div class="card-body py-3 px-3 small">
                <p class="mb-2">@lang('নতুন অ্যাডমিন/ইউজার প্যানেল ফিচার যোগ করলে এই লিস্টে অটো কন্ট্রোল পেতে:')</p>
                <ol class="mb-0 ps-3">
                    <li><code>config/admin_sections_registered.php</code> এ <code>'feature_key' => 'Display Label'</code> যোগ করুন</li>
                    <li><code>config/admin_sections.php</code> এ <code>section_routes</code> ও <code>route_to_section</code> এ ম্যাপিং যোগ করুন</li>
                </ol>
                <p class="mb-0 mt-2 text-muted">@lang('সেকশন লিস্টে নতুন আইটেম দেখা যাবে ও লিংক দিয়ে সরাসরি সেই ফিচারে যাওয়া যাবে।')</p>
            </div>
        </div>
    </div>

    {{-- Access Matrix: Collapsed by default --}}
    @if($hasRole)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-light py-2 px-3 d-flex align-items-center justify-content-between">
            <span class="small fw-semibold"><i class="las la-tasks me-1 text--primary"></i> @lang('Access by Role')</span>
            <button class="btn btn-sm btn-outline-secondary py-0" type="button" data-bs-toggle="collapse" data-bs-target="#accessMatrix" aria-expanded="false">@lang('Show')</button>
        </div>
        <div class="collapse" id="accessMatrix">
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light"><tr><th class="py-1 px-2 small">@lang('Feature')</th><th class="text-center py-1"><span class="badge bg-primary">Owner</span></th><th class="text-center py-1"><span class="badge bg-info">Super</span></th><th class="text-center py-1"><span class="badge bg-secondary">Admin</span></th></tr></thead>
                    <tbody>
                        <tr><td class="py-1 px-2 small">@lang('Dashboard & Reports')</td><td class="text-center"><i class="las la-check text-success"></i></td><td class="text-center"><i class="las la-check text-success"></i></td><td class="text-center"><i class="las la-check text-success"></i></td></tr>
                        <tr><td class="py-1 px-2 small">@lang('Users, Orders, Products')</td><td class="text-center"><i class="las la-check text-success"></i></td><td class="text-center"><i class="las la-check text-success"></i></td><td class="text-center"><i class="las la-check text-success"></i></td></tr>
                        <tr><td class="py-1 px-2 small">@lang('Admin Management')</td><td class="text-center"><i class="las la-check text-success"></i></td><td class="text-center"><i class="las la-times text-muted"></i></td><td class="text-center"><i class="las la-times text-muted"></i></td></tr>
                        <tr><td class="py-1 px-2 small">@lang('Reset admin password')</td><td class="text-center"><i class="las la-check text-success"></i></td><td class="text-center"><i class="las la-times text-muted"></i></td><td class="text-center"><i class="las la-times text-muted"></i></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- All Admins Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light py-3 px-4 d-flex align-items-center justify-content-between">
            <span class="fw-semibold"><i class="las la-list me-2"></i>@lang('All Admins')</span>
            <span class="badge bg--primary rounded-pill">{{ $total }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 admin-list-table">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3">#</th>
                            <th class="py-3 px-3">@lang('Name')</th>
                            <th class="py-3 px-3">@lang('Email')</th>
                            @if($hasMobile)<th class="py-3 px-3">@lang('Phone')</th>@endif
                            <th class="py-3 px-3">@lang('Username')</th>
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('admins', 'admin_notes'))<th class="py-3 px-3">@lang('Notes')</th>@endif
                            @if($hasRole)<th class="py-3 px-3">@lang('Role')</th>@endif
                            @if($showSectionsUI)<th class="py-3 px-3">@lang('Access')</th>@endif
                            @if($canManage)<th class="py-3 px-3 text-end">@lang('Action')</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                        <tr>
                            <td class="py-3 px-3 fw-medium">{{ $admin->id }}</td>
                            <td class="py-3 px-3">{{ $admin->name ?? '—' }}</td>
                            <td class="py-3 px-3"><span class="text-break">{{ $admin->email ?? '—' }}</span></td>
                            @if($hasMobile)<td class="py-3 px-3">{{ $admin->mobile ?? '—' }}</td>@endif
                            <td class="py-3 px-3"><code class="small">{{ $admin->username ?? '—' }}</code></td>
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('admins', 'admin_notes'))
                            <td class="py-3 px-3 small text-muted" title="{{ $admin->admin_notes ?? '' }}">{{ Str::limit($admin->admin_notes ?? '—', 25) }}</td>
                            @endif
                            @if($hasRole)
                            <td class="py-3 px-3">
                                @php $r = $admin->role ?? 'admin'; @endphp
                                @if($r === 'owner')<span class="badge bg-primary rounded-pill">Owner</span>
                                @elseif($r === 'super_admin')<span class="badge bg-info rounded-pill">Super</span>
                                @else<span class="badge bg-secondary rounded-pill">Admin</span>@endif
                            </td>
                            @endif
                            @if($showSectionsUI)
                            <td class="py-3 px-3 small">
                                @if(($admin->role ?? '') === 'owner')<span class="badge bg-primary">@lang('All')</span>
                                @else
                                    @php $secs = $admin->allowed_sections ?? []; @endphp
                                    @if(empty($secs))<span class="text-muted">—</span>@else<span class="text-break">{{ implode(', ', array_map(function($k) use ($adminSections) { return __($adminSections[$k] ?? $k); }, $secs)) }}</span>@endif
                                @endif
                            </td>
                            @endif
                            @if($canManage)
                            <td class="py-3 px-3 text-end">
                                <a href="{{ route('admin.setting.admin.edit', $admin->id) }}" class="btn btn-outline--primary btn-sm py-0" title="@lang('Edit')"><i class="las la-edit"></i></a>
                                @if($isOwner && ($admin->role ?? '') !== 'owner' && $admin->id != auth()->guard('admin')->id())
                                <button type="button" class="btn btn-outline-warning btn-sm py-0" title="@lang('Reset password (Owner only)')" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" data-admin-id="{{ $admin->id }}" data-admin-name="{{ $admin->name ?? $admin->username }}"><i class="las la-key"></i></button>
                                @endif
                                @if(($admin->role ?? 'admin') !== 'owner' && $admin->id != auth()->guard('admin')->id())
                                <form method="POST" action="{{ route('admin.setting.admin.role', $admin->id) }}" class="d-inline">
                                    @csrf
                                    <select name="role" class="form-select form-select-sm d-inline-block w-auto py-0" onchange="this.form.submit()">
                                        <option value="admin" @selected(($admin->role ?? '') === 'admin')>Admin</option>
                                        <option value="super_admin" @selected(($admin->role ?? '') === 'super_admin')>Super</option>
                                    </select>
                                </form>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            @php $colspan = 4 + ($hasMobile ? 1 : 0) + (\Illuminate\Support\Facades\Schema::hasColumn('admins', 'admin_notes') ? 1 : 0) + ($hasRole ? 1 : 0) + ($showSectionsUI ? 1 : 0) + ($canManage ? 1 : 0); @endphp
                            <td colspan="{{ $colspan }}" class="text-center text-muted py-4 small">@lang('No admins.') @if($canManage) @lang('Add one above.') @endif</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Reset password modal (Owner only) --}}
    @if($isOwner)
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel"><i class="las la-key me-2"></i>@lang('Reset admin password')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="resetPasswordForm" method="POST" action="">
                    @csrf
                    <div class="modal-body">
                        <p class="small text-muted mb-3">@lang('Only Owner can reset passwords. The new password will be shown once after save.')</p>
                        <p class="mb-2"><strong>@lang('Admin'):</strong> <span id="resetAdminName">—</span></p>
                        <div class="mb-2">
                            <label class="form-label small">@lang('New password') *</label>
                            <input type="password" class="form-control form-control-sm" name="new_password" id="resetNewPassword" required autocomplete="new-password" placeholder="@lang('New password')">
                        </div>
                        <div class="mb-0">
                            <label class="form-label small">@lang('Confirm new password') *</label>
                            <input type="password" class="form-control form-control-sm" name="new_password_confirmation" required placeholder="@lang('Confirm')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--primary btn-sm">@lang('Reset password')</button>
                    </div>
                </form>
            </div>
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
    var form = document.getElementById('createAdminForm');
    if (form) {
        form.querySelectorAll('[data-select-sections="all"]').forEach(function(btn) {
            btn.addEventListener('click', function() { form.querySelectorAll('input[name="allowed_sections[]"]').forEach(function(c) { c.checked = true; }); });
        });
        form.querySelectorAll('[data-select-sections="none"]').forEach(function(btn) {
            btn.addEventListener('click', function() { form.querySelectorAll('input[name="allowed_sections[]"]').forEach(function(c) { c.checked = false; }); });
        });
    }
    var showToggle = document.getElementById('showPasswordToggle');
    if (showToggle) {
        showToggle.addEventListener('change', function() {
            var type = this.checked ? 'text' : 'password';
            var p = document.getElementById('createPassword'); var c = document.getElementById('createPasswordConfirm');
            if (p) p.type = type; if (c) c.type = type;
        });
    }
    var modal = document.getElementById('resetPasswordModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn) return;
            var id = btn.getAttribute('data-admin-id'); var name = btn.getAttribute('data-admin-name') || '—';
            var formEl = document.getElementById('resetPasswordForm');
            var nameEl = document.getElementById('resetAdminName');
            if (formEl && id) formEl.action = '{{ route("admin.setting.admin.reset.password", ["id" => "__ID__"]) }}'.replace('__ID__', id);
            if (nameEl) nameEl.textContent = name;
            if (document.getElementById('resetNewPassword')) document.getElementById('resetNewPassword').value = '';
        });
    }
})();
</script>
@endpush
