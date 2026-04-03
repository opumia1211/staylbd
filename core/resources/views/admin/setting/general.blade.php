@extends('admin.layouts.app')
@php
    $hasRole = \Illuminate\Support\Facades\Schema::hasColumn('admins', 'role');
    $currentAdmin = auth()->guard('admin')->user();
    $canManage = $hasRole && $currentAdmin && method_exists($currentAdmin, 'canManageAdmins') && $currentAdmin->canManageAdmins();
@endphp
@section('panel')
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-1">@lang('Admin Management')</h4>
            <p class="text-muted mb-0">@lang('Add new admins and manage roles. Owner and Super Admin can manage other admins.')</p>
        </div>
    </div>

    @if($hasRole)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light border-0">
                <div class="card-header py-2">
                    <h6 class="mb-0"><i class="las la-shield-alt me-1"></i> @lang('Role & Access')</h6>
                </div>
                <div class="card-body small">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-nowrap align-top pe-3"><strong class="text-primary">@lang('Owner')</strong></td>
                            <td>@lang('Full access. Can manage all admins, change roles, and all settings. Only one owner (first admin).')</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap align-top pe-3"><strong class="text-info">@lang('Super Admin')</strong></td>
                            <td>@lang('Can add new admins, change roles of other admins (except Owner), and access all admin features. Cannot change Owner.')</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap align-top pe-3"><strong>@lang('Admin')</strong></td>
                            <td>@lang('Normal admin. Full access to run the site but cannot add/remove admins or change roles.')</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($canManage)
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">@lang('Add New Admin')</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.setting.admin.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">@lang('Name')</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required maxlength="40">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Username')</label>
                            <input type="text" class="form-control" name="username" value="{{ old('username') }}" required maxlength="40">
                            @error('username')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Email')</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                            @error('email')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Password')</label>
                            <input type="password" class="form-control" name="password" required autocomplete="new-password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Confirm Password')</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                        @if($hasRole)
                        <div class="mb-3">
                            <label class="form-label">@lang('Role')</label>
                            <select class="form-select" name="role" required>
                                <option value="admin" @selected(old('role') === 'admin')>@lang('Admin')</option>
                                <option value="super_admin" @selected(old('role') === 'super_admin')>@lang('Super Admin')</option>
                            </select>
                        </div>
                        @endif
                        <button type="submit" class="btn btn--primary">@lang('Add Admin')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">@lang('All Admins')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('#')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Username')</th>
                                    <th>@lang('Email')</th>
                                    @if($hasRole)<th>@lang('Role')</th>@endif
                                    @if($canManage)<th>@lang('Action')</th>@endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $admin)
                                <tr>
                                    <td>{{ $admin->id }}</td>
                                    <td>{{ $admin->name ?? '-' }}</td>
                                    <td>{{ $admin->username ?? '-' }}</td>
                                    <td>{{ $admin->email ?? '-' }}</td>
                                    @if($hasRole)
                                    <td>
                                        @php $role = $admin->role ?? 'admin'; @endphp
                                        @if($role === 'owner')
                                            <span class="badge bg-primary">@lang('Owner')</span>
                                        @elseif($role === 'super_admin')
                                            <span class="badge bg-info">@lang('Super Admin')</span>
                                        @else
                                            <span class="badge bg-secondary">@lang('Admin')</span>
                                        @endif
                                    </td>
                                    @endif
                                    @if($canManage)
                                    <td>
                                        @if(($admin->role ?? 'admin') !== 'owner' && $admin->id != auth()->guard('admin')->id())
                                        <form method="POST" action="{{ route('admin.setting.admin.role', $admin->id) }}" class="d-inline">
                                            @csrf
                                            <select name="role" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                <option value="admin" @selected(($admin->role ?? '') === 'admin')>@lang('Admin')</option>
                                                <option value="super_admin" @selected(($admin->role ?? '') === 'super_admin')>@lang('Super Admin')</option>
                                            </select>
                                        </form>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr><td colspan="{{ $hasRole ? ($canManage ? 6 : 5) : 4 }}" class="text-center text-muted py-4">@lang('No admins.')</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info mb-0">
                <i class="las la-link me-1"></i>
                @lang('Site title, currency, timezone and today deal discount are now in')
                <a href="{{ route('admin.frontend.sections.general') }}" class="alert-link">@lang('Frontend General')</a>.
            </div>
        </div>
    </div>
@endsection
