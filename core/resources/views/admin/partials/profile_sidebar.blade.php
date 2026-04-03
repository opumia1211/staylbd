{{-- Shared sidebar for Profile & Password pages: same info + menu (Profile | Password | Logout) --}}
@php
    $admin = $admin ?? auth()->guard('admin')->user();
    $avatarUrl = $admin && $admin->image
        ? getImageWebP(getFilePath('adminProfile') . '/' . $admin->image, getFileSize('adminProfile'))
        : getImage('', '200x200');
    $onProfile = request()->routeIs('admin.profile*');
    $onPassword = request()->routeIs('admin.password*');
@endphp
<div class="col-xl-3 col-lg-4 mb-30">
    <div class="card b-radius--10 overflow-hidden border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="d-flex p-4 bg--primary align-items-center">
                <div class="avatar avatar--lg flex-shrink-0">
                    <img src="{{ $avatarUrl }}" alt="{{ $admin ? __($admin->name) : '' }}" class="rounded-circle">
                </div>
                <div class="ps-3 text--white">
                    <h5 class="mb-0 fw-bold">{{ $admin ? __($admin->name) : '' }}</h5>
                    <small class="opacity-75">{{ $admin ? $admin->email : '' }}</small>
                </div>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">@lang('Name')</span>
                    <span class="fw-semibold">{{ $admin ? __($admin->name) : '' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">@lang('Username')</span>
                    <span class="fw-semibold">{{ $admin ? $admin->username : '' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="text-muted">@lang('Email')</span>
                    <span class="fw-semibold text-break small">{{ $admin ? $admin->email : '' }}</span>
                </li>
            </ul>
            <div class="p-3 border-top bg-light">
                <p class="small text-muted mb-2">@lang('Account')</p>
                <nav class="nav flex-column gap-1">
                    <a href="{{ route('admin.profile') }}" class="nav-link rounded d-flex align-items-center px-3 py-2 {{ $onProfile ? 'bg--primary text-white' : 'text-dark' }}">
                        <i class="las la-user me-2"></i>@lang('Profile')
                    </a>
                    <a href="{{ route('admin.password') }}" class="nav-link rounded d-flex align-items-center px-3 py-2 {{ $onPassword ? 'bg--primary text-white' : 'text-dark' }}">
                        <i class="las la-key me-2"></i>@lang('Password')
                    </a>
                    <a href="{{ route('admin.logout') }}" class="nav-link rounded d-flex align-items-center px-3 py-2 text-danger">
                        <i class="las la-sign-out-alt me-2"></i>@lang('Logout')
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>
