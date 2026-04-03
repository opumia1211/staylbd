@extends('admin.layouts.app')

@section('panel')
    @if(isset($stats) && $stats)
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card b-radius--10 border--primary">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">@lang('Total')</span>
                        <h4 class="mb-0 mt-1">{{ $stats['total'] ?? 0 }}</h4>
                    </div>
                    <i class="las la-list text--primary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card b-radius--10 border--success">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">@lang('Today')</span>
                        <h4 class="mb-0 mt-1">{{ $stats['today'] ?? 0 }}</h4>
                    </div>
                    <i class="las la-calendar-day text--success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card b-radius--10 border--info">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">@lang('Last 7 Days')</span>
                        <h4 class="mb-0 mt-1">{{ $stats['week'] ?? 0 }}</h4>
                    </div>
                    <i class="las la-calendar-week text--info" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card responsive-filter-card mb-4">
        <div class="card-body">
            <form action="{{ route($routeName) }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">@lang('Search')</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="@lang('Description or username')">
                </div>
                <div class="col-md-2">
                    <label class="form-label">@lang('Action')</label>
                    <select name="action_type" class="form-control">
                        <option value="">@lang('All')</option>
                        @foreach(\App\Models\UserActivityLog::actionTypeLabels() as $value => $label)
                        <option value="{{ $value }}" {{ request('action_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">@lang('IP')</label>
                    <input type="text" name="ip_address" value="{{ request('ip_address') }}" class="form-control" placeholder="@lang('IP address')">
                </div>
                <div class="col-md-2">
                    <label class="form-label">@lang('Country')</label>
                    <input type="text" name="country" value="{{ request('country') }}" class="form-control" placeholder="@lang('Country')">
                </div>
                <div class="col-md-2">
                    <label class="form-label">@lang('Date Range')</label>
                    <input type="text" name="date" value="{{ request('date') }}" class="form-control datepicker-here" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position="bottom right" placeholder="@lang('Start - End')" autocomplete="off">
                </div>
                <div class="col-md-1">
                    <label class="form-label">@lang('Per page')</label>
                    <select name="per_page" class="form-control">
                        @foreach([10, 20, 50, 100, 200] as $n)
                        <option value="{{ $n }}" {{ (int)request('per_page') === $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn--primary btn-sm w-100"><i class="las la-filter"></i></button>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <a href="{{ route($exportRouteName) }}?{{ request()->getQueryString() }}" class="btn btn-outline--info btn-sm w-100" title="@lang('Export CSV')"><i class="las la-file-csv"></i></a>
                </div>
            </form>
        </div>
    </div>

    @php
        $isCompareRoute = isset($routeName) && $routeName === 'admin.report.activity.compare';
    @endphp

    {{-- Bulk delete activity logs: all report types (selected or by count 10/20/50/100/200/All) --}}
    <form id="activityBulkDeleteForm" action="{{ route('admin.report.activity.bulk_delete') }}" method="POST" class="mb-2">
        @csrf
        <input type="hidden" name="action" value="selected" id="activityBulkAction">
        <input type="hidden" name="limit" value="" id="activityBulkLimit">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <input type="hidden" name="action_type" value="{{ request('action_type') }}">
        <input type="hidden" name="ip_address" value="{{ request('ip_address') }}">
        <input type="hidden" name="country" value="{{ request('country') }}">
        <input type="hidden" name="date" value="{{ request('date') }}">
        @if(isset($actionTypes) && is_array($actionTypes))
            @foreach($actionTypes as $at)
                <input type="hidden" name="action_types[]" value="{{ $at }}">
            @endforeach
        @endif
        <div class="card b-radius--10 mb-2 border-0 shadow-sm">
            <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center gap-2 small">
                <button type="submit" class="btn btn-sm btn-outline--danger" id="activityDeleteSelectedBtn" disabled>@lang('Delete selected')</button>
                <span class="text-muted">@lang('or delete by count'):</span>
                <select class="form-select form-select-sm d-inline-block" id="activityDeleteCountSelect" style="width: auto; max-width: 5rem;">
                    <option value="">—</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="all">@lang('All')</option>
                </select>
                <button type="button" class="btn btn-sm btn-outline--danger" id="activityDeleteByCountBtn" disabled>@lang('Delete')</button>
            </div>
        </div>
    </form>

    @if($isCompareRoute)
    <form action="{{ route('admin.report.activity.compare.bulk_delete') }}" method="POST" id="compareBulkForm">
        @csrf
        <div id="compareIdsHolder"></div>
        <div class="card b-radius--10 mb-2 border-0 shadow-sm">
            <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center gap-2 small">
                <span class="text-muted">@lang('Remove from compare list'):</span>
                <button type="button" class="btn btn-sm btn-outline--secondary" id="compareBulkDeleteBtn" onclick="submitCompareBulkRemove();">
                    <i class="las la-trash-alt"></i> @lang('Bulk Remove')
                </button>
            </div>
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
                                    <th style="width:2rem;">
                                        <input type="checkbox" id="selectAllActivity" class="form-check-input" title="@lang('Select all')">
                                    </th>
                                    <th>@lang('Action')</th>
                                    <th>@lang('Description')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('IP')</th>
                                    <th>@lang('Device')</th>
                                    <th>@lang('Location')</th>
                                    <th>@lang('At')</th>
                                    <th class="text-end">@lang('Link')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr class="{{ in_array($log->action_type, ['login_failed', 'payment_failure']) ? 'table-warning' : '' }}">
                                    <td>
                                        <input type="checkbox" form="activityBulkDeleteForm" name="ids[]" value="{{ $log->id }}" class="form-check-input activity-row-cb">
                                    </td>
                                    <td><span class="badge badge--primary">{{ $log->action_type }}</span></td>
                                    <td><span class="text-break">{{ \Illuminate\Support\Str::limit($log->description ?? '—', 60) }}</span></td>
                                    <td>
                                        @if($log->user_id)
                                        <a href="{{ route('admin.users.detail', $log->user_id) }}">{{ $log->user->fullname ?? '—' }}</a>
                                        <span class="small d-block">@{{ $log->user->username ?? '—' }}</span>
                                        @else
                                        <span class="text-muted">@lang('Guest')</span>
                                        @endif
                                    </td>
                                    <td><span class="font-monospace small">{{ $log->ip_address ?? '—' }}</span></td>
                                    <td>{{ $log->device ?? '—' }}</td>
                                    <td>{{ $log->city ? $log->city . ', ' : '' }}{{ $log->country ?? '—' }}</td>
                                    <td>
                                        <span class="d-block">{{ showDateTime($log->created_at) }}</span>
                                        <span class="small text-muted">{{ diffForHumans($log->created_at) }}</span>
                                    </td>
                                    <td class="text-end">
                                        @if($log->model_type === 'product' && $log->model_id)
                                        <a href="{{ product_detail_url_for_id((int) $log->model_id) }}" target="_blank" class="btn btn-sm btn-outline--primary"><i class="las la-external-link-alt"></i></a>
                                        @elseif($log->model_type === 'order' && $log->model_id)
                                        <a href="{{ route('admin.orders.detail', $log->model_id) }}" class="btn btn-sm btn-outline--info"><i class="las la-list"></i></a>
                                        @elseif($log->user_id)
                                        <a href="{{ route('admin.users.detail', $log->user_id) }}" class="btn btn-sm btn-outline--secondary"><i class="las la-user"></i></a>
                                        @endif
                                        @if($isCompareRoute && $log->model_type === 'product')
                                        <form action="{{ route('admin.report.activity.compare.delete') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $log->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline--danger" onclick="return confirm('@lang('Remove this item from user compare list?')');">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="9">{{ __($emptyMessage ?? 'No activity found.') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($logs->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($logs) }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}">
@endpush
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

    // Activity bulk delete: select all, toggle Delete selected, delete by count
    var selectAllActivity = document.getElementById('selectAllActivity');
    var activityDeleteSelectedBtn = document.getElementById('activityDeleteSelectedBtn');
    var activityDeleteCountSelect = document.getElementById('activityDeleteCountSelect');
    var activityDeleteByCountBtn = document.getElementById('activityDeleteByCountBtn');
    var activityBulkDeleteForm = document.getElementById('activityBulkDeleteForm');
    var activityBulkAction = document.getElementById('activityBulkAction');
    var activityBulkLimit = document.getElementById('activityBulkLimit');

    function updateActivityDeleteState() {
        var checked = document.querySelectorAll('.activity-row-cb:checked').length;
        if (activityDeleteSelectedBtn) activityDeleteSelectedBtn.disabled = checked === 0;
        if (selectAllActivity) {
            var all = document.querySelectorAll('.activity-row-cb');
            selectAllActivity.checked = all.length > 0 && all.length === checked;
        }
    }

    if (selectAllActivity) {
        selectAllActivity.addEventListener('change', function() {
            document.querySelectorAll('.activity-row-cb').forEach(function(cb) { cb.checked = selectAllActivity.checked; });
            updateActivityDeleteState();
        });
    }
    document.querySelectorAll('.activity-row-cb').forEach(function(cb) {
        cb.addEventListener('change', updateActivityDeleteState);
    });

    if (activityDeleteCountSelect) {
        activityDeleteCountSelect.addEventListener('change', function() {
            if (activityDeleteByCountBtn) activityDeleteByCountBtn.disabled = !this.value;
        });
    }
    if (activityDeleteByCountBtn && activityBulkDeleteForm) {
        activityDeleteByCountBtn.addEventListener('click', function() {
            var limit = activityDeleteCountSelect ? activityDeleteCountSelect.value : '';
            if (!limit) return;
            var msg = limit === 'all' ? '{{ __("Delete ALL activity logs (with current filters)?") }}' : '{{ __("Delete first") }} ' + limit + ' {{ __("logs (with current filters)?") }}';
            if (!confirm(msg)) return;
            if (activityBulkAction) activityBulkAction.value = 'count';
            if (activityBulkLimit) activityBulkLimit.value = limit;
            activityBulkDeleteForm.submit();
        });
    }
    if (activityBulkDeleteForm) {
        activityBulkDeleteForm.addEventListener('submit', function(e) {
            if (activityBulkAction && activityBulkAction.value === 'selected') {
                if (document.querySelectorAll('.activity-row-cb:checked').length === 0) { e.preventDefault(); return false; }
            }
        });
    }
    updateActivityDeleteState();

    // Compare: Bulk Remove – copy selected activity ids into compare form and submit
    function submitCompareBulkRemove() {
        var checked = document.querySelectorAll('.activity-row-cb:checked');
        if (!checked.length) return;
        if (!confirm('{{ __("Remove selected items from user compare lists?") }}')) return;
        var holder = document.getElementById('compareIdsHolder');
        var form = document.getElementById('compareBulkForm');
        if (!holder || !form) return;
        holder.innerHTML = '';
        checked.forEach(function(cb) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'ids[]';
            inp.value = cb.value;
            holder.appendChild(inp);
        });
        form.submit();
    }
})(jQuery);
</script>
@endpush
