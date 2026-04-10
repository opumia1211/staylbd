@extends('admin.layouts.app')

@section('panel')
<div class="search-analytics-panel">
    <p class="alert alert--primary mb-4 d-flex align-items-center flex-wrap gap-2">
        <i class="las la-info-circle"></i>
        <span>@lang('All searches and filters from the public site') (header, products page, filter) @lang('are logged here.') @lang('Guest and logged-in users both appear; each visitor has a profile.')</span>
    </p>

    @if(isset($visitors) && $visitors->isNotEmpty())
    <div class="card b-radius--10 mb-4">
        <div class="card-header bg-transparent py-3">
            <h5 class="mb-0">@lang('Visitor profiles') — @lang('View searches by person')</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--light style--two mb-0">
                    <thead><tr><th class="border-0">@lang('Visitor')</th><th class="border-0">@lang('Searches')</th><th class="border-0">@lang('Last search')</th><th class="border-0">@lang('Action')</th></tr></thead>
                    <tbody>
                        @foreach($visitors as $v)
                        <tr>
                            <td>{{ $v->label }}</td>
                            <td>{{ $v->total }}</td>
                            <td class="small">{{ $v->last_at ? \Carbon\Carbon::parse($v->last_at)->format('M d, H:i') : '—' }}</td>
                            <td><a href="{{ route('admin.report.search.analytics', ['visitor' => $v->id]) }}" class="btn btn-sm btn-outline--primary">@lang('View')</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if(isset($stats) && $stats)
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card b-radius--10 border--primary h-100">
                <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="text-muted small">@lang('Total')</span>
                    <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                    <i class="las la-search text--primary opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card b-radius--10 border--success h-100">
                <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="text-muted small">@lang('Today')</span>
                    <h4 class="mb-0">{{ $stats['today'] ?? 0 }}</h4>
                    <i class="las la-calendar-day text--success opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card b-radius--10 border--info h-100">
                <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="text-muted small">@lang('7 Days')</span>
                    <h4 class="mb-0">{{ $stats['week'] ?? 0 }}</h4>
                    <i class="las la-calendar-week text--info opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card b-radius--10 border--warning h-100">
                <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="text-muted small">@lang('Unique')</span>
                    <h4 class="mb-0">{{ $stats['unique_queries'] ?? 0 }}</h4>
                    <i class="las la-tags text--warning opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Rankings: multiple cards in a grid --}}
    <div class="row g-3 mb-4">
        @if(isset($topQueries) && $topQueries->isNotEmpty())
        <div class="col-12 col-lg-6">
            <div class="card b-radius--10 h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0">@lang('Ranking') 1 — @lang('Top keywords by count')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead><tr><th class="border-0">#</th><th class="border-0">@lang('Keyword')</th><th class="text-end border-0">@lang('Count')</th></tr></thead>
                            <tbody>
                                @foreach($topQueries as $i => $row)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><span class="text-break">{{ $row->query ?: '—' }}</span></td>
                                    <td class="text-end">{{ $row->search_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(isset($zeroResultQueries) && $zeroResultQueries->isNotEmpty())
        <div class="col-12 col-lg-6">
            <div class="card b-radius--10 border--warning h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0">@lang('Ranking') 2 — @lang('Zero result searches')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead><tr><th class="border-0">@lang('Keyword')</th><th class="text-end border-0">@lang('Times')</th></tr></thead>
                            <tbody>
                                @foreach($zeroResultQueries as $row)
                                <tr><td class="text-break">{{ $row->query ?: '—' }}</td><td class="text-end">{{ $row->cnt }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(isset($rankingBySource) && $rankingBySource->isNotEmpty())
        <div class="col-12 col-lg-6">
            <div class="card b-radius--10 h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0">@lang('Ranking') 3 — @lang('By source')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead><tr><th class="border-0">@lang('Source')</th><th class="text-end border-0">@lang('Count')</th></tr></thead>
                            <tbody>
                                @foreach($rankingBySource as $row)
                                <tr>
                                    <td>
                                        @if($row->source === 'voice')
                                            <span class="badge badge--info">@lang('Voice')</span>
                                        @elseif($row->source === 'image')
                                            <span class="badge badge--warning">@lang('Image')</span>
                                        @elseif($row->source === 'products_page')
                                            <span class="badge badge--success">@lang('Products page')</span>
                                        @elseif($row->source === 'filter')
                                            <span class="badge badge--secondary">@lang('Filter')</span>
                                        @else
                                            <span class="badge badge--primary">@lang('Header')</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ $row->cnt }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(isset($searchesByDate) && $searchesByDate->isNotEmpty())
        <div class="col-12 col-lg-6">
            <div class="card b-radius--10 h-100">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0">@lang('Ranking') 4 — @lang('Searches by date') (14 @lang('days'))</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead><tr><th class="border-0">@lang('Date')</th><th class="text-end border-0">@lang('Count')</th></tr></thead>
                            <tbody>
                                @foreach($searchesByDate->sortByDesc('date')->values() as $row)
                                <tr><td>{{ $row->date ?? '' }}</td><td class="text-end">{{ $row->cnt ?? 0 }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Filter bar - compact --}}
    <div class="card b-radius--10 mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.report.search.analytics') }}" method="GET" class="row g-2 g-md-3 align-items-end flex-wrap">
                <div class="col-12 col-sm-6 col-md-2 col-lg-2">
                    <label class="form-label small mb-0">@lang('Keyword / User')</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="@lang('Query or user')">
                </div>
                <div class="col-6 col-md-2 col-lg-2">
                    <label class="form-label small mb-0">@lang('Visitor')</label>
                    <select name="visitor" class="form-control form-control-sm">
                        <option value="">@lang('All')</option>
                        @if(isset($visitors))
                            @foreach($visitors as $v)
                            <option value="{{ $v->id }}" {{ request('visitor') === $v->id ? 'selected' : '' }}>{{ \Illuminate\Support\Str::limit($v->label, 28) }} ({{ $v->total }})</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-6 col-md-2 col-lg-2">
                    <label class="form-label small mb-0">@lang('Source')</label>
                    <select name="source" class="form-control form-control-sm">
                        <option value="">@lang('All')</option>
                        <option value="universal" {{ request('source') === 'universal' ? 'selected' : '' }}>@lang('Header')</option>
                        <option value="products_page" {{ request('source') === 'products_page' ? 'selected' : '' }}>@lang('Products page')</option>
                        <option value="filter" {{ request('source') === 'filter' ? 'selected' : '' }}>@lang('Filter')</option>
                        <option value="voice" {{ request('source') === 'voice' ? 'selected' : '' }}>@lang('Voice')</option>
                        <option value="image" {{ request('source') === 'image' ? 'selected' : '' }}>@lang('Image')</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 col-lg-2">
                    <label class="form-label small mb-0">@lang('Date')</label>
                    <input type="text" name="date" value="{{ request('date') }}" class="form-control form-control-sm datepicker-here" data-range="true" data-multiple-dates-separator=" - " data-language="en" placeholder="@lang('Range')" autocomplete="off">
                </div>
                <div class="col-4 col-md-1 col-lg-1">
                    <label class="form-label small mb-0">@lang('Per page')</label>
                    <select name="per_page" class="form-control form-control-sm">
                        @foreach([10, 20, 50, 100, 200] as $n)
                        <option value="{{ $n }}" {{ (int)request('per_page') === $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 col-md-2 col-lg-2">
                    <button type="submit" class="btn btn--primary btn-sm w-100"><i class="las la-filter"></i> @lang('Filter')</button>
                </div>
                <div class="col-4 col-md-1 col-lg-1">
                    <a href="{{ route('admin.report.search.analytics') }}" class="btn btn-outline--secondary btn-sm w-100">@lang('Reset')</a>
                </div>
                <div class="col-12 col-md-1 col-lg-1">
                    <a href="{{ route('admin.report.search.analytics.export') }}?{{ request()->getQueryString() }}" class="btn btn-outline--info btn-sm w-100" title="@lang('Export CSV')"><i class="las la-file-csv"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk delete: select rows + choose count 10/20/50/100/All, compact buttons --}}
    <form id="search-log-bulk-form" action="{{ route('admin.report.search.analytics.delete') }}" method="POST" class="mb-3">
        @csrf
        <input type="hidden" name="action" value="selected" id="bulk-action-input">
        <input type="hidden" name="limit" value="" id="bulk-limit-input">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <input type="hidden" name="source" value="{{ request('source') }}">
        <input type="hidden" name="date" value="{{ request('date') }}">
        <input type="hidden" name="visitor" value="{{ request('visitor') }}">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <button type="submit" class="btn btn-sm btn-outline--danger" id="bulk-delete-selected" disabled>@lang('Delete selected')</button>
            <span class="text-muted small">@lang('or delete by count'):</span>
            <select class="form-select form-select-sm d-inline-block" id="bulk-delete-count-select" style="width: auto; max-width: 6rem;">
                <option value="">—</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="all">@lang('All')</option>
            </select>
            <button type="button" class="btn btn-sm btn-outline--danger" id="bulk-delete-by-count" disabled>@lang('Delete')</button>
        </div>
    </form>

    {{-- Logs table: checkbox, image, query, copy, open link --}}
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--light style--two table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="border-0" style="width:2rem;">
                                <input type="checkbox" id="select-all-logs" class="form-check-input" title="@lang('Select all')">
                            </th>
                            <th class="border-0">@lang('Image')</th>
                            <th>@lang('Searched text / Filter')</th>
                            <th>@lang('User')</th>
                            <th>@lang('Results')</th>
                            <th>@lang('Source')</th>
                            <th>@lang('Time')</th>
                            <th class="text-end">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="align-middle">
                                <input type="checkbox" class="form-check-input log-row-cb" name="ids[]" value="{{ $log->id }}" form="search-log-bulk-form">
                            </td>
                            <td class="align-middle">
                                @if(!empty($log->image_path) && isset($storageUrl))
                                    <a href="{{ $storageUrl }}/{{ $log->image_path }}" target="_blank" rel="noopener" class="d-inline-block"><img src="{{ $storageUrl }}/{{ $log->image_path }}" alt="" class="rounded" style="max-width:48px;max-height:48px;object-fit:cover;"></a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="search-query-text text-break" data-query="{{ e($log->query ?? '') }}">{{ $log->query ?: '—' }}</span>
                                    <button type="button" class="btn btn-sm btn-outline--primary copy-query-btn" data-query="{{ e($log->query ?? '') }}" title="@lang('Copy')"><i class="las la-copy"></i></button>
                                </div>
                            </td>
                            <td class="align-middle">
                                @if($log->user_id)
                                    <a href="{{ route('admin.users.detail', $log->user_id) }}" class="text--primary">{{ $log->user->username ?? $log->user->fullname ?? '—' }}</a>
                                @else
                                    <span class="text-muted">@lang('Guest')</span>
                                @endif
                            </td>
                            <td class="align-middle">{{ $log->results_count ?? 0 }}</td>
                            <td class="align-middle">
                                @if($log->source === 'voice')
                                    <span class="badge badge--info">@lang('Voice')</span>
                                @elseif($log->source === 'image')
                                    <span class="badge badge--warning">@lang('Image')</span>
                                @elseif($log->source === 'products_page')
                                    <span class="badge badge--success">@lang('Products')</span>
                                @elseif($log->source === 'filter')
                                    <span class="badge badge--secondary">@lang('Filter')</span>
                                @else
                                    <span class="badge badge--primary">@lang('Header')</span>
                                @endif
                            </td>
                            <td class="align-middle small">
                                <span class="d-block">{{ showDateTime($log->created_at) }}</span>
                                <span class="text-muted">{{ diffForHumans($log->created_at) }}</span>
                            </td>
                            <td class="align-middle text-end">
                                @php
                                    $canOpenAsSearch = $log->query && !preg_match('/^(Cat:|Brand:|Price:)/', $log->query);
                                    $productsUrl = route('products') . ( $canOpenAsSearch ? '?search=' . urlencode($log->query) : '' );
                                @endphp
                                <a href="{{ $productsUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline--info" title="@lang('Open on site')"><i class="las la-external-link-alt"></i></a>
                                @if($log->user_id)
                                    <a href="{{ route('admin.users.detail', $log->user_id) }}" class="btn btn-sm btn-outline--primary" title="@lang('User')"><i class="las la-user"></i></a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center py-5 text-muted" colspan="8">
                                {{ __($emptyMessage ?? 'No search logs found.') }}
                                <div class="small mt-2">@lang('Searches and filters from the public site will appear here.')</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer py-3">
            {{ paginateLinks($logs) }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="@lang('Search query or username')" />
@endpush

@push('style')

{{-- inline style moved to critical-admin.css --}}

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
    document.querySelectorAll('.copy-query-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var q = this.getAttribute('data-query') || '';
            if (!q) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(q).then(function() {
                    var icon = btn.querySelector('i');
                    if (icon) { icon.className = 'las la-check'; setTimeout(function() { icon.className = 'las la-copy'; }, 1500); }
                });
            } else {
                var ta = document.createElement('textarea');
                ta.value = q;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                var icon = btn.querySelector('i');
                if (icon) { icon.className = 'las la-check'; setTimeout(function() { icon.className = 'las la-copy'; }, 1500); }
            }
        });
    });
    var bulkForm = document.getElementById('search-log-bulk-form');
    if (bulkForm) {
        var selectAll = document.getElementById('select-all-logs');
        var rowCbs = document.querySelectorAll('.log-row-cb');
        var deleteSelectedBtn = document.getElementById('bulk-delete-selected');
        var bulkActionInput = document.getElementById('bulk-action-input');
        var bulkLimitInput = document.getElementById('bulk-limit-input');
        var countSelect = document.getElementById('bulk-delete-count-select');
        var deleteByCountBtn = document.getElementById('bulk-delete-by-count');
        function toggleDeleteBtn() {
            var any = document.querySelectorAll('.log-row-cb:checked').length > 0;
            if (deleteSelectedBtn) deleteSelectedBtn.disabled = !any;
        }
        if (countSelect) {
            countSelect.addEventListener('change', function() {
                if (deleteByCountBtn) deleteByCountBtn.disabled = !this.value;
            });
        }
        if (deleteByCountBtn) {
            deleteByCountBtn.addEventListener('click', function() {
                var limit = countSelect ? countSelect.value : '';
                if (!limit) return;
                var msg = limit === 'all'
                    ? '{{ __("Delete ALL search logs (with current filters)? This cannot be undone.") }}'
                    : '{{ __("Delete first") }} ' + limit + ' {{ __("search logs (with current filters)?") }}';
                if (!confirm(msg)) return;
                if (bulkActionInput) bulkActionInput.value = 'count';
                if (bulkLimitInput) bulkLimitInput.value = limit;
                bulkForm.submit();
            });
        }
        if (selectAll) selectAll.addEventListener('change', function() { rowCbs.forEach(function(cb) { cb.checked = selectAll.checked; }); toggleDeleteBtn(); });
        rowCbs.forEach(function(cb) { cb.addEventListener('change', toggleDeleteBtn); });
        bulkForm.addEventListener('submit', function(e) {
            if (bulkActionInput && bulkActionInput.value === 'selected') {
                if (document.querySelectorAll('.log-row-cb:checked').length === 0) { e.preventDefault(); return false; }
            }
        });
    }
})(jQuery);
</script>
@endpush
