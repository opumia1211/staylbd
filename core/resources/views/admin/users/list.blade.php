@extends('admin.layouts.app')
@section('panel')
    @isset($listType)
    @if($listType === 'active' && isset($stats))
    {{-- Stats cards: Active Users page --}}
    <div class="row mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-3 bg--primary bg-opacity-10 p-2">
                            <i class="las la-users text--primary fs-4"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">@lang('Total Active')</div>
                        <div class="fw-bold fs-5">{{ number_format($stats['total']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-3 bg-success bg-opacity-10 p-2">
                            <i class="las la-user-plus text-success fs-4"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">@lang('New This Week')</div>
                        <div class="fw-bold fs-5">{{ number_format($stats['new_week']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-3 bg-info bg-opacity-10 p-2">
                            <i class="las la-calendar-plus text-info fs-4"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">@lang('New This Month')</div>
                        <div class="fw-bold fs-5">{{ number_format($stats['new_month']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-3 bg-warning bg-opacity-10 p-2">
                            <i class="las la-shopping-cart text-warning fs-4"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">@lang('With Orders')</div>
                        <div class="fw-bold fs-5">{{ number_format($stats['with_orders']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($listType === 'banned' && isset($stats))
    {{-- Stats cards: Banned Users page --}}
    <div class="row mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-3 bg-danger bg-opacity-10 p-2">
                            <i class="las la-user-slash text-danger fs-4"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">@lang('Total Banned')</div>
                        <div class="fw-bold fs-5">{{ number_format($stats['total']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-3 bg-warning bg-opacity-10 p-2">
                            <i class="las la-ban text-warning fs-4"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">@lang('Banned This Week')</div>
                        <div class="fw-bold fs-5">{{ number_format($stats['recent_week']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-3 bg-secondary bg-opacity-10 p-2">
                            <i class="las la-clock text-secondary fs-4"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">@lang('Banned This Month')</div>
                        <div class="fw-bold fs-5">{{ number_format($stats['recent_month']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($listType === 'emailUnverified' && isset($stats))
    {{-- Stats: Email Unverified --}}
    <div class="row mb-4 admin-users-stats">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="flex-shrink-0 me-3 admin-users-stats__icon-wrap admin-users-stats__icon-wrap--warning">
                        <i class="las la-envelope-open-text fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">@lang('Total')</div>
                        <div class="fw-bold fs-5">{{ number_format($stats['total']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="flex-shrink-0 me-3 admin-users-stats__icon-wrap admin-users-stats__icon-wrap--info">
                        <i class="las la-user-clock fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">@lang('New This Week')</div>
                        <div class="fw-bold fs-5">{{ number_format($stats['new_week']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="flex-shrink-0 me-3 admin-users-stats__icon-wrap admin-users-stats__icon-wrap--primary">
                        <i class="las la-calendar-plus fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">@lang('New This Month')</div>
                        <div class="fw-bold fs-5">{{ number_format($stats['new_month']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($listType === 'mobileUnverified' && isset($stats))
    {{-- Stats: Mobile Unverified --}}
    <div class="row mb-4 admin-users-stats">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="flex-shrink-0 me-3 admin-users-stats__icon-wrap admin-users-stats__icon-wrap--warning">
                        <i class="las la-mobile-alt fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">@lang('Total')</div>
                        <div class="fw-bold fs-5">{{ number_format($stats['total']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="flex-shrink-0 me-3 admin-users-stats__icon-wrap admin-users-stats__icon-wrap--info">
                        <i class="las la-user-clock fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">@lang('New This Week')</div>
                        <div class="fw-bold fs-5">{{ number_format($stats['new_week']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body py-3 d-flex align-items-center">
                    <div class="flex-shrink-0 me-3 admin-users-stats__icon-wrap admin-users-stats__icon-wrap--primary">
                        <i class="las la-calendar-plus fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">@lang('New This Month')</div>
                        <div class="fw-bold fs-5">{{ number_format($stats['new_month']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endisset

    <div class="card border-0 shadow-sm rounded-3 admin-users-card">
        @if(isset($listType) && $listType === 'active')
        <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h6 class="mb-0 fw-bold">@lang('Active Users')</h6>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <form method="get" action="{{ route('admin.users.active') }}" class="d-inline-flex flex-wrap gap-2 align-items-center admin-users-active-form">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <div class="d-flex flex-wrap align-items-center gap-1 me-2">
                        <span class="small text-muted">@lang('Quick date'):</span>
                        <a href="{{ route('admin.users.active', array_merge(request()->only(['search', 'sort', 'per_page', 'has_orders']), ['date' => now()->subDays(7)->format('Y-m-d') . ' - ' . now()->format('Y-m-d')])) }}" class="btn btn-sm btn-outline-secondary py-0 px-2">@lang('Last 7 days')</a>
                        <a href="{{ route('admin.users.active', array_merge(request()->only(['search', 'sort', 'per_page', 'has_orders']), ['date' => now()->subDays(30)->format('Y-m-d') . ' - ' . now()->format('Y-m-d')])) }}" class="btn btn-sm btn-outline-secondary py-0 px-2">@lang('Last 30 days')</a>
                        <a href="{{ route('admin.users.active', array_merge(request()->only(['search', 'sort', 'per_page', 'has_orders']), ['date' => now()->startOfMonth()->format('Y-m-d') . ' - ' . now()->format('Y-m-d')])) }}" class="btn btn-sm btn-outline-secondary py-0 px-2">@lang('This month')</a>
                        @if(request('date'))
                        <a href="{{ route('admin.users.active', request()->only(['search', 'sort', 'per_page', 'has_orders'])) }}" class="btn btn-sm btn-outline-danger py-0 px-2">@lang('Clear date')</a>
                        @endif
                    </div>
                    <label class="mb-0 small text-muted">@lang('Sort'):</label>
                    <select name="sort" class="form-select form-select-sm admin-users-filter-select" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>@lang('Newest first')</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>@lang('Oldest first')</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>@lang('Name A-Z')</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>@lang('Name Z-A')</option>
                    </select>
                    <label class="mb-0 small text-muted ms-2">@lang('Per page'):</label>
                    <select name="per_page" class="form-select form-select-sm admin-users-filter-select" onchange="this.form.submit()" style="min-width: 4rem;">
                        @foreach([10, 25, 50, 100] as $n)
                        <option value="{{ $n }}" {{ (int)request('per_page', getPaginate()) === $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    <label class="mb-0 small text-muted ms-2">@lang('Joined'):</label>
                    <input type="text" name="date" class="form-control form-control-sm admin-users-filter-date" placeholder="@lang('e.g. 2024-01-01 - 2024-12-31')" value="{{ request('date') }}" title="@lang('Format: YYYY-MM-DD - YYYY-MM-DD')">
                    <label class="mb-0 small text-muted ms-2 d-flex align-items-center gap-1">
                        <input type="checkbox" name="has_orders" value="1" {{ request('has_orders') === '1' ? 'checked' : '' }} onchange="this.form.submit()" class="form-check-input">
                        @lang('With orders')
                    </label>
                    <button type="submit" class="btn btn-sm btn-outline--primary">@lang('Apply')</button>
                </form>
                <a href="{{ route('admin.users.active.export', request()->only(['search', 'date'])) }}" class="btn btn-sm btn-outline--success"><i class="las la-file-csv me-1"></i>@lang('Export CSV')</a>
            </div>
        </div>
        @endif
        @if(isset($listType) && $listType === 'banned')
        <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h6 class="mb-0 fw-bold">@lang('Banned Users')</h6>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <form method="get" action="{{ route('admin.users.banned') }}" class="d-inline-flex flex-wrap gap-2 align-items-center">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <label class="mb-0 small text-muted">@lang('Sort'):</label>
                    <select name="sort" class="form-select form-select-sm admin-users-filter-select" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>@lang('Newest first')</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>@lang('Oldest first')</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>@lang('Name A-Z')</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>@lang('Name Z-A')</option>
                    </select>
                    <label class="mb-0 small text-muted ms-2">@lang('Updated'):</label>
                    <input type="text" name="date" class="form-control form-control-sm admin-users-filter-date" placeholder="@lang('e.g. 2024-01-01 - 2024-12-31')" value="{{ request('date') }}" title="@lang('Format: YYYY-MM-DD - YYYY-MM-DD')">
                    <button type="submit" class="btn btn-sm btn-outline--primary">@lang('Apply')</button>
                </form>
                <a href="{{ route('admin.users.banned.export', request()->only(['search', 'date'])) }}" class="btn btn-sm btn-outline--success"><i class="las la-file-csv me-1"></i>@lang('Export CSV')</a>
            </div>
        </div>
        @endif
        @if(isset($listType) && $listType === 'emailUnverified')
        <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h6 class="mb-0 fw-bold">@lang('Email Unverified Users')</h6>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <form method="get" action="{{ route('admin.users.email.unverified') }}" class="d-inline-flex flex-wrap gap-2 align-items-center">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <label class="mb-0 small text-muted">@lang('Sort'):</label>
                    <select name="sort" class="form-select form-select-sm admin-users-filter-select" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>@lang('Newest first')</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>@lang('Oldest first')</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>@lang('Name A-Z')</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>@lang('Name Z-A')</option>
                    </select>
                    <label class="mb-0 small text-muted ms-2">@lang('Joined'):</label>
                    <input type="text" name="date" class="form-control form-control-sm admin-users-filter-date" placeholder="@lang('e.g. 2024-01-01 - 2024-12-31')" value="{{ request('date') }}" title="@lang('Format: YYYY-MM-DD - YYYY-MM-DD')">
                    <button type="submit" class="btn btn-sm btn-outline--primary">@lang('Apply')</button>
                </form>
                <a href="{{ route('admin.users.email.unverified.export', request()->only(['search', 'date'])) }}" class="btn btn-sm btn-outline--success"><i class="las la-file-csv me-1"></i>@lang('Export CSV')</a>
            </div>
        </div>
        @endif
        @if(isset($listType) && $listType === 'mobileUnverified')
        <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h6 class="mb-0 fw-bold">@lang('Mobile Unverified Users')</h6>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <form method="get" action="{{ route('admin.users.mobile.unverified') }}" class="d-inline-flex flex-wrap gap-2 align-items-center">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <label class="mb-0 small text-muted">@lang('Sort'):</label>
                    <select name="sort" class="form-select form-select-sm admin-users-filter-select" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>@lang('Newest first')</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>@lang('Oldest first')</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>@lang('Name A-Z')</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>@lang('Name Z-A')</option>
                    </select>
                    <label class="mb-0 small text-muted ms-2">@lang('Joined'):</label>
                    <input type="text" name="date" class="form-control form-control-sm admin-users-filter-date" placeholder="@lang('e.g. 2024-01-01 - 2024-12-31')" value="{{ request('date') }}" title="@lang('Format: YYYY-MM-DD - YYYY-MM-DD')">
                    <button type="submit" class="btn btn-sm btn-outline--primary">@lang('Apply')</button>
                </form>
                <a href="{{ route('admin.users.mobile.unverified.export', request()->only(['search', 'date'])) }}" class="btn btn-sm btn-outline--success"><i class="las la-file-csv me-1"></i>@lang('Export CSV')</a>
            </div>
        </div>
        @endif
        @if(!isset($listType) || !in_array($listType, ['active', 'banned', 'emailUnverified', 'mobileUnverified']))
        <div class="card-header bg-transparent border-bottom py-3">
            <h6 class="mb-0 fw-bold">{{ $pageTitle ?? __('Users') }}</h6>
        </div>
        @endif

        <div class="card-body p-0">
            @if(isset($listType) && $listType === 'active' && $users->total() > 0)
            <div class="px-3 py-2 bg-light border-bottom small text-muted d-flex flex-wrap align-items-center justify-content-between gap-2">
                <span>@lang('Showing') {{ $users->firstItem() }} @lang('to') {{ $users->lastItem() }} @lang('of') {{ number_format($users->total()) }} @lang('results')</span>
                @if(request('has_orders') === '1')
                <span class="badge bg--primary">{{ __("Filter: With orders") }}</span>
                @endif
            </div>
            @endif
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two table-align-middle users-list-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 52px;">@lang('User')</th>
                            <th>@lang('Contact')</th>
                            @if(isset($listType) && $listType === 'active')
                            <th class="d-none d-md-table-cell">@lang('Country')</th>
                            <th class="d-none d-lg-table-cell">@lang('Last Login')</th>
                            @elseif(isset($listType) && $listType === 'banned')
                            <th class="d-none d-lg-table-cell">@lang('Country')</th>
                            <th class="d-none d-md-table-cell">@lang('Ban Reason')</th>
                            @else
                            <th>@lang('Country')</th>
                            @endif
                            <th>{{ isset($listType) && $listType === 'banned' ? __('Updated At') : __('Joined At') }}</th>
                            <th style="width: 160px;" class="text-end">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($user->image)
                                            <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) }}" alt="" class="rounded-circle object-fit-cover user-list-avatar">
                                        @else
                                            <div class="user-list-avatar user-list-avatar--initials rounded-circle d-flex align-items-center justify-content-center bg--primary text-white fw-bold">
                                                {{ strtoupper(substr($user->firstname ?? 'U', 0, 1) . substr($user->lastname ?? '', 0, 1)) ?: strtoupper(substr($user->username ?? 'U', 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <span class="fw-bold d-block text-truncate">{{ trim($user->fullname) ?: $user->username }}</span>
                                            <a href="{{ route('admin.users.detail', $user->id) }}" class="small text-muted text-truncate d-block">{{ '@' . ($user->username ?? '') }}</a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <a href="mailto:{{ $user->email }}" class="text-dark d-block text-truncate" title="{{ $user->email }}">{{ $user->email }}</a>
                                        @if($user->mobile)
                                            <a href="tel:{{ $user->mobile }}" class="text-muted d-block">{{ $user->mobile }}</a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </td>
                                @if(isset($listType) && $listType === 'banned')
                                <td class="d-none d-lg-table-cell">
                                    <span class="badge bg-light text-dark" title="{{ @$user->address->country ?? '' }}">{{ $user->country_code ?? '—' }}</span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="small text-muted ban-reason-cell" title="{{ $user->ban_reason ?? '' }}">{{ Str::limit($user->ban_reason ?? '—', 40) }}</span>
                                </td>
                                @else
                                <td class="d-none d-md-table-cell">
                                    <span class="badge bg-light text-dark" title="{{ @$user->address->country ?? '' }}">{{ $user->country_code ?? '—' }}</span>
                                </td>
                                @if(isset($listType) && $listType === 'active')
                                <td class="d-none d-lg-table-cell small text-muted">
                                    @if(!empty($user->login_logs_max_created_at))
                                        <span title="{{ showDateTime($user->login_logs_max_created_at, 'd M Y H:i') }}">{{ diffForHumans($user->login_logs_max_created_at) }}</span>
                                    @else
                                        <span>—</span>
                                    @endif
                                </td>
                                @endif
                                @endif
                                <td>
                                    @if(isset($listType) && $listType === 'banned')
                                    <span class="d-block">{{ showDateTime($user->updated_at, 'd M Y') }}</span>
                                    <span class="small text-muted">{{ diffForHumans($user->updated_at) }}</span>
                                    @else
                                    <span class="d-block">{{ showDateTime($user->created_at, 'd M Y') }}</span>
                                    <span class="small text-muted">{{ diffForHumans($user->created_at) }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-outline--primary" title="@lang('View details')" data-bs-toggle="tooltip"><i class="las la-desktop"></i></a>
                                        <a href="{{ route('admin.users.notification.single', $user->id) }}" class="btn btn-outline--info" title="@lang('Send notification')" data-bs-toggle="tooltip"><i class="las la-paper-plane"></i></a>
                                        @if($user->status == \App\Constants\Status::USER_ACTIVE)
                                        <a href="{{ route('admin.users.login', $user->id) }}" class="btn btn-outline--success" target="_blank" title="@lang('Login as user')" data-bs-toggle="tooltip"><i class="las la-sign-in-alt"></i></a>
                                        @endif
                                        @if($user->status == \App\Constants\Status::USER_BAN)
                                        <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Unban this user?') }}');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline--success" title="@lang('Unban')"><i class="las la-user-check"></i></button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center py-5" colspan="100%">
                                    <div class="empty-state-icon mb-3">
                                        <i class="las la-users fs-1 text-muted opacity-50"></i>
                                    </div>
                                    <p class="text-muted mb-0">{{ __($emptyMessage ?? 'No users found.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($users->hasPages())
            <div class="card-footer py-3 border-top">
                {{ paginateLinks($users) }}
            </div>
        @endif
    </div>
@endsection

@push('breadcrumb-plugins')
    @php
        $listFormAction = request()->url();
        if (isset($listType)) {
            if ($listType === 'active') $listFormAction = route('admin.users.active');
            elseif ($listType === 'banned') $listFormAction = route('admin.users.banned');
            elseif ($listType === 'emailUnverified') $listFormAction = route('admin.users.email.unverified');
            elseif ($listType === 'mobileUnverified') $listFormAction = route('admin.users.mobile.unverified');
        }
    @endphp
    <form method="get" class="d-flex flex-wrap gap-2 align-items-center" action="{{ $listFormAction }}">
        @if(isset($listType) && in_array($listType, ['active', 'banned', 'emailUnverified', 'mobileUnverified']))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="date" value="{{ request('date') }}">
            @if($listType === 'active')
            <input type="hidden" name="per_page" value="{{ request('per_page', getPaginate()) }}">
            @if(request('has_orders') === '1')<input type="hidden" name="has_orders" value="1">@endif
            @endif
        @endif
        <x-search-form placeholder="@lang('Username / Email / Name / Mobile')" />
    </form>
@endpush

@push('style')
{{--
  Admin Users List - Flexible CSS. To adjust without breaking layout, override these variables on .admin-users-card or body:
  --admin-users-radius, --admin-users-icon-size, --admin-users-th-color, --admin-users-avatar-size, --primary-rgb
--}}
<style>
/* Admin Users List - scoped to avoid breaking other pages */
.admin-users-card { border-radius: var(--admin-users-radius, 0.5rem); }
.admin-users-stats { gap: 0; }
.admin-users-stats__icon-wrap {
    width: var(--admin-users-icon-size, 2.5rem);
    height: var(--admin-users-icon-size, 2.5rem);
    border-radius: var(--admin-users-radius, 0.5rem);
    display: flex;
    align-items: center;
    justify-content: center;
}
.admin-users-stats__icon-wrap--primary { background: rgba(var(--primary-rgb, 33, 158, 188), 0.1); color: var(--primary, #219ebc); }
.admin-users-stats__icon-wrap--warning { background: rgba(255, 193, 7, 0.15); color: #b38600; }
.admin-users-stats__icon-wrap--info { background: rgba(23, 162, 184, 0.15); color: #0d6e7a; }
.admin-users-filter-select { width: auto; min-width: 8rem; }
.admin-users-filter-date { width: 100%; max-width: 12.5rem; }

.users-list-table { width: 100%; }
.users-list-table th { font-weight: 600; color: var(--admin-users-th-color, #374151); }
.users-list-table td { vertical-align: middle !important; }
.user-list-avatar { width: var(--admin-users-avatar-size, 2.5rem); height: var(--admin-users-avatar-size, 2.5rem); object-fit: cover; }
.user-list-avatar--initials { font-size: clamp(0.65rem, 2vw, 0.75rem); min-width: var(--admin-users-avatar-size, 2.5rem); min-height: var(--admin-users-avatar-size, 2.5rem); }
.ban-reason-cell { max-width: 12rem; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

@media (max-width: 767.98px) {
    .users-list-table .btn-group .btn { padding: 0.2rem 0.35rem; }
    .admin-users-filter-date { max-width: 100%; }
}
</style>
@endpush

