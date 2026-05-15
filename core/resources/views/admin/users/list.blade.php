@extends('admin.layouts.app')

@section('panel')
    @isset($listType)
    {{-- Modernized Stats Cards with Standard Sneat Alignment --}}
    <div class="row g-6 mb-6">
        @php
            $stats_data = [];
            if ($listType === 'active' && isset($stats)) {
                $stats_data = [
                    ['label' => __('Total Active'), 'value' => number_format($stats['total']), 'icon' => 'bx-group', 'color' => 'primary'],
                    ['label' => __('New This Week'), 'value' => number_format($stats['new_week']), 'icon' => 'bx-user-plus', 'color' => 'success'],
                    ['label' => __('New This Month'), 'value' => number_format($stats['new_month']), 'icon' => 'bx-calendar-plus', 'color' => 'info'],
                    ['label' => __('With Orders'), 'value' => number_format($stats['with_orders']), 'icon' => 'bx-shopping-bag', 'color' => 'warning'],
                ];
            } elseif ($listType === 'banned' && isset($stats)) {
                $stats_data = [
                    ['label' => __('Total Banned'), 'value' => number_format($stats['total']), 'icon' => 'bx-user-x', 'color' => 'danger'],
                    ['label' => __('Banned This Week'), 'value' => number_format($stats['recent_week']), 'icon' => 'bx-block', 'color' => 'warning'],
                    ['label' => __('Banned This Month'), 'value' => number_format($stats['recent_month']), 'icon' => 'bx-time-five', 'color' => 'secondary'],
                ];
            } elseif (in_array($listType, ['emailUnverified', 'mobileUnverified']) && isset($stats)) {
                $icon = $listType === 'emailUnverified' ? 'bx-envelope-open' : 'bx-mobile-vibration';
                $stats_data = [
                    ['label' => __('Total Unverified'), 'value' => number_format($stats['total']), 'icon' => $icon, 'color' => 'warning'],
                    ['label' => __('New This Week'), 'value' => number_format($stats['new_week']), 'icon' => 'bx-user-plus', 'color' => 'info'],
                    ['label' => __('New This Month'), 'value' => number_format($stats['new_month']), 'icon' => 'bx-calendar-plus', 'color' => 'primary'],
                ];
            }
        @endphp

        @foreach($stats_data as $s)
            <div class="{{ count($stats_data) == 4 ? 'col-sm-6 col-xl-3' : 'col-sm-6 col-xl-4' }}">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="small fw-medium text-muted mb-1 d-block">{{ $s['label'] }}</span>
                                <div class="d-flex align-items-end mt-1">
                                    <h4 class="mb-0 me-2 fw-bold text-heading">{{ $s['value'] }}</h4>
                                </div>
                            </div>
                            <div class="avatar avatar-md">
                                <span class="avatar-initial rounded bg-label-{{ $s['color'] }} d-flex align-items-center justify-content-center">
                                    <i class="icon-base bx {{ $s['icon'] }} fs-4"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endisset

    {{-- Users List Card --}}
    <div class="card border-0 shadow-sm">
        @if(isset($listType))
        <div class="card-header border-bottom py-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                        <span class="avatar-initial rounded bg-label-primary d-flex align-items-center justify-content-center">
                            <i class="icon-base bx bx-user fs-4"></i>
                        </span>
                    </div>
                    <h6 class="m-0">
                        @if($listType === 'active') @lang('Active Customer Directory')
                        @elseif($listType === 'banned') @lang('Banned Users List')
                        @elseif($listType === 'emailUnverified') @lang('Email Verification Pending')
                        @elseif($listType === 'mobileUnverified') @lang('Mobile Verification Pending')
                        @else {{ $pageTitle }} @endif
                    </h6>
                </div>
                
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <form method="get" action="{{ url()->current() }}" class="d-flex flex-wrap align-items-center gap-2">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        
                        <div class="input-group input-group-sm w-auto">
                            <span class="input-group-text bg-label-secondary border-end-0 d-flex align-items-center justify-content-center" style="width: 40px;"><i class="icon-base bx bx-sort-alt-2"></i></span>
                            <select name="sort" class="form-select border-start-0 ps-1" onchange="this.form.submit()" style="min-width: 140px;">
                                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>@lang('Newest first')</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>@lang('Oldest first')</option>
                                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>@lang('Name A-Z')</option>
                                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>@lang('Name Z-A')</option>
                            </select>
                        </div>

                        <div class="input-group input-group-sm w-auto">
                            <span class="input-group-text bg-label-secondary border-end-0"><i class="icon-base bx bx-calendar"></i></span>
                            <input type="text" name="date" class="form-control form-control-sm border-start-0 ps-1 date-range" placeholder="@lang('Join Date')" value="{{ request('date') }}" style="max-width: 180px;">
                        </div>

                        @if($listType === 'active')
                            <div class="form-check form-check-sm mb-0">
                                <input type="checkbox" name="has_orders" value="1" {{ request('has_orders') === '1' ? 'checked' : '' }} onchange="this.form.submit()" class="form-check-input" id="hasOrdersCheck">
                                <label class="form-check-label small text-muted" for="hasOrdersCheck">@lang('Has Orders')</label>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-sm btn-label-primary px-3">@lang('Filter')</button>
                        @if(request()->anyFilled(['sort', 'date', 'has_orders']))
                            <a href="{{ url()->current() }}" class="btn btn-sm btn-label-secondary btn-icon" title="@lang('Reset')"><i class="icon-base bx bx-refresh"></i></a>
                        @endif
                    </form>
                    
                    @php
                        $exportRoute = 'admin.users.' . $listType . '.export';
                        if ($listType === 'emailUnverified') $exportRoute = 'admin.users.email.unverified.export';
                        elseif ($listType === 'mobileUnverified') $exportRoute = 'admin.users.mobile.unverified.export';
                    @endphp
                    <a href="{{ route($exportRoute, request()->only(['search', 'date'])) }}" class="btn btn-sm btn-label-success shadow-none"><i class="icon-base bx bx-export me-1"></i>@lang('Export')</a>
                </div>
            </div>
        </div>
        @endif

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-label-secondary border-top-0">
                        <tr>
                            <th class="ps-5 py-3">@lang('User Info')</th>
                            <th>@lang('Contact Details')</th>
                            <th class="d-none d-lg-table-cell">@lang('Location')</th>
                            <th class="d-none d-md-table-cell">{{ isset($listType) && $listType === 'banned' ? __('Updated') : __('Joined') }}</th>
                            <th class="text-center">@lang('Status')</th>
                            <th class="text-end pe-5">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($users as $user)
                            <tr>
                                <td class="ps-5 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md me-3">
                                            @if($user->image)
                                                <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) }}" alt="" class="rounded-circle object-fit-cover shadow-xs border">
                                            @else
                                                <span class="avatar-initial rounded-circle bg-label-{{ str_contains('aeiou', strtolower($user->firstname[0] ?? 'b')) ? 'primary' : 'info' }} shadow-xs border fw-bold text-uppercase d-flex align-items-center justify-content-center">
                                                    {{ substr($user->firstname ?? 'U', 0, 1) }}{{ substr($user->lastname ?? '', 0, 1) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.users.detail', $user->id) }}" class="fw-bold text-heading d-block line-height-1 mb-1 text-truncate" style="max-width: 200px;">{{ $user->fullname }}</a>
                                            <span class="small text-muted">{{ '@' . $user->username }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <a href="mailto:{{ $user->email }}" class="text-body fw-medium mb-1"><i class="icon-base bx bx-envelope me-1 opacity-50"></i>{{ $user->email }}</a>
                                        <span class="text-muted"><i class="icon-base bx bx-phone me-1 opacity-50"></i>{{ $user->mobile ?: __('No mobile') }}</span>
                                    </div>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="badge bg-label-secondary border text-body fw-medium me-2">{{ $user->country_code ?? '—' }}</span>
                                            <small class="text-muted text-truncate" style="max-width: 120px;">{{ @$user->address->country }}</small>
                                        </div>
                                        @if(isset($listType) && $listType === 'banned' && $user->ban_reason)
                                            <small class="text-danger text-truncate d-block" style="max-width: 180px;" title="{{ $user->ban_reason }}">
                                                <i class="icon-base bx bx-error-circle me-1"></i>{{ $user->ban_reason }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <div class="small">
                                        <span class="d-block text-heading fw-medium">{{ showDateTime($user->created_at, 'd M, Y') }}</span>
                                        <span class="text-muted">{{ diffForHumans($user->created_at) }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($user->status == \App\Constants\Status::USER_ACTIVE)
                                        <span class="badge bg-label-success rounded-pill px-3">@lang('Active')</span>
                                    @else
                                        <span class="badge bg-label-danger rounded-pill px-3" title="{{ $user->ban_reason }}">@lang('Banned')</span>
                                    @endif
                                </td>
                                <td class="text-end pe-5">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-sm btn-icon btn-label-primary shadow-none d-flex align-items-center justify-content-center" title="@lang('Settings')">
                                            <i class="icon-base bx bx-cog"></i>
                                        </a>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon btn-label-secondary shadow-none d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown">
                                                <i class="icon-base bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item d-flex align-items-center" href="{{ route('admin.users.notification.single', $user->id) }}"><i class="icon-base bx bx-send me-2"></i>@lang('Send Message')</a></li>
                                                @if($user->status == \App\Constants\Status::USER_ACTIVE)
                                                    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('admin.users.login', $user->id) }}" target="_blank"><i class="icon-base bx bx-log-in-circle me-2 text-success"></i>@lang('Login As User')</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center text-danger cuModalBtn" data-modal_title="@lang('Ban User:') {{ $user->username }}" data-action="{{ route('admin.users.status', $user->id) }}">
                                                            <i class="icon-base bx bx-user-x me-2"></i>@lang('Ban User')
                                                        </button>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{ route('admin.users.status', $user->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item d-flex align-items-center text-success btn-confirm" 
                                                                data-confirm-title="@lang('Unban User?')"
                                                                data-confirm-text="@lang('Are you sure you want to restore access for this user?')"
                                                                data-confirm-btn="@lang('Yes, Unban')"
                                                                data-confirm-icon="question">
                                                                <i class="icon-base bx bx-user-check me-2"></i>@lang('Unban User')
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center py-12" colspan="100%">
                                    <div class="avatar avatar-xl bg-label-secondary mx-auto mb-4">
                                        <span class="avatar-initial rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="icon-base bx bx-group fs-1"></i>
                                        </span>
                                    </div>
                                    <h5 class="text-muted">{{ __($emptyMessage ?? 'No users found.') }}</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($users->hasPages())
            <div class="card-footer py-4 border-top">
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
    <div class="d-flex align-items-center gap-2">
        <form method="get" action="{{ $listFormAction }}">
            @foreach(request()->except(['search', 'page']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <x-search-form placeholder="@lang('Username, Email...')" />
        </form>
    </div>
@endpush
