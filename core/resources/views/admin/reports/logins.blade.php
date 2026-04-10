@extends('admin.layouts.app')

@section('panel')
    {{-- Stats cards (only on main login history page) --}}
    @if(isset($stats) && $stats)
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card b-radius--10 border--primary">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">@lang('Logins Today')</span>
                        <h4 class="mb-0 mt-1">{{ $stats['today'] ?? 0 }}</h4>
                    </div>
                    <i class="las la-sign-in-alt text--primary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card b-radius--10 border--success">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">@lang('Last 7 Days')</span>
                        <h4 class="mb-0 mt-1">{{ $stats['week'] ?? 0 }}</h4>
                    </div>
                    <i class="las la-calendar-week text--success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card b-radius--10 border--info">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">@lang('Unique IPs')</span>
                        <h4 class="mb-0 mt-1">{{ $stats['unique_ips'] ?? 0 }}</h4>
                    </div>
                    <i class="las la-network-wired text--info" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filter bar (login history page only) --}}
    @if(request()->routeIs('admin.report.login.history'))
    <div class="card responsive-filter-card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.report.login.history') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">@lang('Username')</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="@lang('Search by username')">
                </div>
                <div class="col-md-3">
                    <label class="form-label">@lang('Date Range')</label>
                    <input type="text" name="date" value="{{ request('date') }}" class="form-control datepicker-here" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position="bottom right" placeholder="@lang('Start - End')" autocomplete="off">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn--primary btn-sm w-100"><i class="las la-filter"></i> @lang('Filter')</button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin.report.login.history') }}" class="btn btn-outline--secondary btn-sm w-100">@lang('Reset')</a>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin.report.login.history.export') }}?{{ request()->getQueryString() }}" class="btn btn-outline--info btn-sm w-100"><i class="las la-file-csv"></i> @lang('Export CSV')</a>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if(request()->routeIs('admin.report.login.history'))
    <form id="login-bulk-form" action="{{ route('admin.report.login.history.bulk_delete') }}" method="POST" class="mb-3">
        @csrf
        <input type="hidden" name="action" value="selected" id="login-bulk-action">
        <input type="hidden" name="limit" value="" id="login-bulk-limit">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <input type="hidden" name="date" value="{{ request('date') }}">
        <div class="d-flex flex-wrap gap-2 align-items-center small">
            <button type="submit" class="btn btn-sm btn-outline--danger" id="login-delete-selected" disabled>@lang('Delete selected')</button>
            <span class="text-muted">@lang('or delete by count'):</span>
            <select class="form-select form-select-sm d-inline-block" id="login-delete-count" style="width:auto;max-width:5rem;">
                <option value="">—</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="all">@lang('All')</option>
            </select>
            <button type="button" class="btn btn-sm btn-outline--danger" id="login-delete-by-count" disabled>@lang('Delete')</button>
        </div>
    </form>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two table-hover">
                            <thead>
                                <tr>
                                    @if(request()->routeIs('admin.report.login.history'))
                                    <th style="width:2rem;"><input type="checkbox" id="login-select-all" class="form-check-input" title="@lang('Select all')"></th>
                                    @endif
                                    <th>@lang('User')</th>
                                    <th>@lang('Login at')</th>
                                    <th>@lang('IP')</th>
                                    <th>@lang('Location')</th>
                                    <th>@lang('Browser / OS')</th>
                                    <th class="text-end">@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loginLogs as $log)
                                <tr>
                                    @if(request()->routeIs('admin.report.login.history'))
                                    <td><input type="checkbox" form="login-bulk-form" name="ids[]" value="{{ $log->id }}" class="form-check-input login-row-cb"></td>
                                    @endif
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <span class="fw-bold d-block">{{ @$log->user->fullname ?? '—' }}</span>
                                                <a href="{{ route('admin.users.detail', $log->user_id) }}" class="small text--primary"><span>@</span>{{ @$log->user->username ?? '—' }}</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="d-block">{{ showDateTime($log->created_at) }}</span>
                                        <span class="small text-muted">{{ diffForHumans($log->created_at) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.report.login.ipHistory', [$log->user_ip]) }}" class="fw-bold text--primary">{{ $log->user_ip }}</a>
                                    </td>
                                    <td>
                                        <span class="location-display">{{ $log->location_display }}</span>
                                        @if($log->latitude && $log->longitude)
                                        <span class="small text-muted d-block">{{ $log->latitude }}, {{ $log->longitude }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="d-block">{{ __($log->browser ?? '—') }}</span>
                                        <span class="small text-muted">{{ __($log->os ?? '—') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="button--group d-flex justify-content-end gap-1 flex-wrap">
                                            @if($log->user_id)
                                            <a href="{{ route('admin.users.detail', $log->user_id) }}" class="btn btn-sm btn-outline--primary" title="@lang('View User')"><i class="las la-user"></i></a>
                                            <a href="{{ route('admin.report.login.history') }}?search={{ @$log->user->username }}" class="btn btn-sm btn-outline--info" title="@lang('Logins by this user')"><i class="las la-history"></i></a>
                                            @endif
                                            <a href="{{ route('admin.report.login.ipHistory', [$log->user_ip]) }}" class="btn btn-sm btn-outline--secondary" title="@lang('Logins from this IP')"><i class="las la-network-wired"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="{{ request()->routeIs('admin.report.login.history') ? 7 : 6 }}">{{ __($emptyMessage ?? 'No login records found.') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($loginLogs->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($loginLogs) }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @if(request()->routeIs('admin.report.login.history'))
        <x-search-form placeholder="@lang('Enter Username')" />
    @endif
    @if(request()->routeIs('admin.report.login.ipHistory'))
        <a href="https://www.ip2location.com/{{ $ip ?? '' }}" target="_blank" rel="noopener" class="btn btn--primary btn-sm"><i class="las la-external-link-alt"></i> @lang('Lookup IP') {{ $ip ?? '' }}</a>
        <a href="{{ route('admin.report.login.history') }}" class="btn btn-outline--secondary btn-sm"><i class="las la-arrow-left"></i> @lang('Back to History')</a>
    @endif
@endpush

@if(request()->routeIs('admin.report.login.history'))
@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush
@push('script')
<script>
(function($){
    "use strict";
    if ($('.datepicker-here').length && !$('.datepicker-here').first().data('datepicker')) {
        $('.datepicker-here').datepicker();
    }
})(jQuery);
</script>
<script>
(function(){
    if (!document.getElementById('login-bulk-form')) return;
    var f = document.getElementById('login-bulk-form');
    var sel = document.getElementById('login-delete-selected');
    var cnt = document.getElementById('login-delete-count');
    var btn = document.getElementById('login-delete-by-count');
    var all = document.getElementById('login-select-all');
    function up() {
        var n = document.querySelectorAll('.login-row-cb:checked').length;
        if (sel) sel.disabled = n === 0;
        if (all) all.checked = document.querySelectorAll('.login-row-cb').length > 0 && document.querySelectorAll('.login-row-cb').length === n;
    }
    if (all) all.addEventListener('change', function(){ document.querySelectorAll('.login-row-cb').forEach(function(c){ c.checked = all.checked; }); up(); });
    document.querySelectorAll('.login-row-cb').forEach(function(c){ c.addEventListener('change', up); });
    if (cnt) cnt.addEventListener('change', function(){ if(btn) btn.disabled = !this.value; });
    if (btn && f) btn.addEventListener('click', function(){
        var limit = cnt ? cnt.value : '';
        if (!limit) return;
        if (!confirm(limit === 'all' ? '{{ __("Delete ALL login records (with current filters)?") }}' : '{{ __("Delete first") }} ' + limit + '?')) return;
        document.getElementById('login-bulk-action').value = 'count';
        document.getElementById('login-bulk-limit').value = limit;
        f.submit();
    });
    f.addEventListener('submit', function(e){
        if (document.getElementById('login-bulk-action').value === 'selected' && document.querySelectorAll('.login-row-cb:checked').length === 0) { e.preventDefault(); return false; }
    });
    up();
})();
</script>
@endpush
@endif
