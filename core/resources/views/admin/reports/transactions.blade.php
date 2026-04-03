@extends('admin.layouts.app')

@section('panel')
{{-- Stats cards --}}
@if(isset($stats) && $stats)
<div class="row mb-4">
    <div class="col-md-2 col-6">
        <div class="card b-radius--10 border--primary">
            <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small">@lang('Total')</span>
                    <h4 class="mb-0 mt-1">{{ $stats['total'] ?? 0 }}</h4>
                </div>
                <i class="las la-list text--primary" style="font-size: 1.5rem;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card b-radius--10 border--success">
            <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small">@lang('Today')</span>
                    <h4 class="mb-0 mt-1">{{ $stats['today'] ?? 0 }}</h4>
                </div>
                <i class="las la-calendar-day text--success" style="font-size: 1.5rem;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card b-radius--10 border--info">
            <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small">@lang('Last 7 Days')</span>
                    <h4 class="mb-0 mt-1">{{ $stats['week'] ?? 0 }}</h4>
                </div>
                <i class="las la-calendar-week text--info" style="font-size: 1.5rem;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card b-radius--10 border--success">
            <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small">@lang('Credit') (Today)</span>
                    <h4 class="mb-0 mt-1 text--success">{{ showAmount($stats['credit'] ?? 0) }}</h4>
                </div>
                <i class="las la-arrow-up text--success" style="font-size: 1.5rem;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card b-radius--10 border--danger">
            <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small">@lang('Debit') (Today)</span>
                    <h4 class="mb-0 mt-1 text--danger">{{ showAmount($stats['debit'] ?? 0) }}</h4>
                </div>
                <i class="las la-arrow-down text--danger" style="font-size: 1.5rem;"></i>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Filter bar --}}
<div class="card responsive-filter-card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.report.transaction') }}" method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label">@lang('TRX / Username')</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="@lang('Search')">
            </div>
            <div class="col-md-2">
                <label class="form-label">@lang('Type')</label>
                <select name="trx_type" class="form-control">
                    <option value="">@lang('All')</option>
                    <option value="+" {{ request('trx_type') === '+' ? 'selected' : '' }}>@lang('Credit')</option>
                    <option value="-" {{ request('trx_type') === '-' ? 'selected' : '' }}>@lang('Debit')</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">@lang('Remark')</label>
                <select name="remark" class="form-control">
                    <option value="">@lang('All')</option>
                    @foreach($remarks ?? [] as $r)
                    <option value="{{ $r }}" {{ request('remark') == $r ? 'selected' : '' }}>{{ __($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">@lang('Date Range')</label>
                <input type="text" name="date" value="{{ request('date') }}" class="form-control datepicker-here" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position="bottom right" placeholder="@lang('Start - End')" autocomplete="off">
            </div>
            <div class="col-md-1">
                <label class="form-label">@lang('Per page')</label>
                <select name="per_page" class="form-control">
                    @foreach([10, 20, 50, 100, 200] as $n)
                    <option value="{{ $n }}" {{ request('per_page') == $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2 flex-wrap">
                <button type="submit" class="btn btn--primary btn-sm"><i class="las la-filter"></i> @lang('Filter')</button>
                <a href="{{ route('admin.report.transaction') }}" class="btn btn-outline--secondary btn-sm">@lang('Reset')</a>
                <a href="{{ route('admin.report.transaction.export') }}?{{ request()->getQueryString() }}" class="btn btn-outline--info btn-sm"><i class="las la-file-csv"></i> @lang('Export CSV')</a>
            </div>
        </form>
    </div>
</div>

{{-- Bulk delete --}}
<form id="trx-bulk-form" action="{{ route('admin.report.transaction.bulk_delete') }}" method="POST" class="mb-3">
    @csrf
    <input type="hidden" name="action" value="selected" id="trx-bulk-action">
    <input type="hidden" name="limit" value="" id="trx-bulk-limit">
    <input type="hidden" name="search" value="{{ request('search') }}">
    <input type="hidden" name="trx_type" value="{{ request('trx_type') }}">
    <input type="hidden" name="remark" value="{{ request('remark') }}">
    <input type="hidden" name="date" value="{{ request('date') }}">
    <div class="d-flex flex-wrap gap-2 align-items-center small">
        <button type="submit" class="btn btn-sm btn-outline--danger" id="trx-delete-selected" disabled>@lang('Delete selected')</button>
        <span class="text-muted">@lang('or delete by count'):</span>
        <select class="form-select form-select-sm d-inline-block" id="trx-delete-count" style="width:auto;max-width:5rem;">
            <option value="">—</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="200">200</option>
            <option value="all">@lang('All')</option>
        </select>
        <button type="button" class="btn btn-sm btn-outline--danger" id="trx-delete-by-count" disabled>@lang('Delete')</button>
    </div>
</form>

<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two table-hover">
                            <thead>
                                <tr>
                                    <th style="width:2rem;"><input type="checkbox" id="trx-select-all" class="form-check-input" title="@lang('Select all')"></th>
                                    <th>@lang('User')</th>
                                <th>@lang('TRX')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Transacted')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Details')</th>
                                <th class="text-end">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                            <tr>
                                <td><input type="checkbox" form="trx-bulk-form" name="ids[]" value="{{ $trx->id }}" class="form-check-input trx-row-cb"></td>
                                <td>
                                    @if($trx->user)
                                    <span class="fw-bold d-block">{{ $trx->user->fullname ?? '—' }}</span>
                                    <a href="{{ route('admin.report.transaction') }}?search={{ $trx->user->username }}"><span>@</span>{{ $trx->user->username }}</a>
                                    @else
                                    <span class="text-muted">@lang('System')</span>
                                    @endif
                                </td>
                                <td><strong>{{ $trx->trx }}</strong></td>
                                <td>
                                    @if(($trx->trx_type ?? '') === '+')
                                    <span class="badge badge--success">@lang('Credit')</span>
                                    @elseif(($trx->trx_type ?? '') === '-')
                                    <span class="badge badge--danger">@lang('Debit')</span>
                                    @else
                                    <span class="badge badge--secondary">{{ $trx->trx_type ?? '—' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="d-block">{{ showDateTime($trx->created_at) }}</span>
                                    <span class="small text-muted">{{ diffForHumans($trx->created_at) }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold {{ ($trx->trx_type ?? '') === '+' ? 'text--success' : 'text--danger' }}">
                                        {{ ($trx->trx_type ?? '') === '+' ? '+' : '-' }}{{ showAmount($trx->amount) }} {{ __($general->cur_text) }}
                                    </span>
                                </td>
                                <td><span title="{{ __($trx->details ?? '') }}">{{ \Illuminate\Support\Str::limit(__($trx->details ?? '—'), 40) }}</span></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline--primary trxDetailBtn" data-bs-toggle="modal" data-bs-target="#trxDetailModal"
                                        data-user="{{ $trx->user ? $trx->user->fullname . ' (@' . $trx->user->username . ')' : __('System') }}"
                                        data-trx="{{ $trx->trx }}"
                                        data-type="{{ $trx->trx_type ?? '—' }}"
                                        data-remark="{{ __($trx->remark ?? '—') }}"
                                        data-amount="{{ showAmount($trx->amount) }} {{ __($general->cur_text) }}"
                                        data-details="{{ __($trx->details ?? '—') }}"
                                        data-created="{{ showDateTime($trx->created_at) }}"
                                        data-post="{{ $trx->post_balance ?? '—' }}">
                                        <i class="las la-desktop"></i> @lang('Detail')
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center py-4" colspan="8">{{ __($emptyMessage ?? 'No transactions found.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($transactions->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($transactions) }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Transaction Detail Modal (Bootstrap 5) --}}
<div class="modal fade" id="trxDetailModal" tabindex="-1" aria-labelledby="trxDetailModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trxDetailModalLabel">@lang('Transaction Details')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 140px;">@lang('User')</td>
                        <td class="trx-detail-user fw-bold"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">@lang('TRX')</td>
                        <td class="trx-detail-trx font-monospace"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">@lang('Type')</td>
                        <td class="trx-detail-type"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">@lang('Remark')</td>
                        <td class="trx-detail-remark"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">@lang('Amount')</td>
                        <td class="trx-detail-amount fw-bold"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">@lang('Post Balance')</td>
                        <td class="trx-detail-post"></td>
                    </tr>
                    <tr>
                        <td class="text-muted">@lang('Date')</td>
                        <td class="trx-detail-created"></td>
                    </tr>
                    <tr>
                        <td class="text-muted align-top">@lang('Details')</td>
                        <td class="trx-detail-details"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="@lang('TRX / Username')" />
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
<script>
(function() {
    "use strict";

    var modal = document.getElementById('trxDetailModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (!btn || !btn.classList.contains('trxDetailBtn')) return;
            var wrap = function(v) { return v || '—'; };
            modal.querySelector('.trx-detail-user').textContent = wrap(btn.getAttribute('data-user'));
            modal.querySelector('.trx-detail-trx').textContent = wrap(btn.getAttribute('data-trx'));
            modal.querySelector('.trx-detail-type').textContent = wrap(btn.getAttribute('data-type'));
            modal.querySelector('.trx-detail-remark').textContent = wrap(btn.getAttribute('data-remark'));
            modal.querySelector('.trx-detail-amount').textContent = wrap(btn.getAttribute('data-amount'));
            modal.querySelector('.trx-detail-post').textContent = wrap(btn.getAttribute('data-post'));
            modal.querySelector('.trx-detail-created').textContent = wrap(btn.getAttribute('data-created'));
            modal.querySelector('.trx-detail-details').textContent = wrap(btn.getAttribute('data-details'));
        });
    }

    if (document.querySelector('.datepicker-here') && typeof jQuery !== 'undefined') {
        var $dp = jQuery('.datepicker-here');
        if ($dp.length && !$dp.first().data('datepicker')) {
            $dp.datepicker();
        }
    }
})();
</script>
<script>
(function(){
    var f = document.getElementById('trx-bulk-form');
    var sel = document.getElementById('trx-delete-selected');
    var cnt = document.getElementById('trx-delete-count');
    var btn = document.getElementById('trx-delete-by-count');
    var all = document.getElementById('trx-select-all');
    function up() {
        var n = document.querySelectorAll('.trx-row-cb:checked').length;
        if (sel) sel.disabled = n === 0;
        if (all) all.checked = document.querySelectorAll('.trx-row-cb').length > 0 && document.querySelectorAll('.trx-row-cb').length === n;
    }
    if (all) all.addEventListener('change', function(){ document.querySelectorAll('.trx-row-cb').forEach(function(c){ c.checked = all.checked; }); up(); });
    document.querySelectorAll('.trx-row-cb').forEach(function(c){ c.addEventListener('change', up); });
    if (cnt) cnt.addEventListener('change', function(){ if(btn) btn.disabled = !this.value; });
    if (btn && f) btn.addEventListener('click', function(){
        var limit = cnt ? cnt.value : '';
        if (!limit) return;
        if (!confirm(limit === 'all' ? '{{ __("Delete ALL transactions (with current filters)?") }}' : '{{ __("Delete first") }} ' + limit + '?')) return;
        document.getElementById('trx-bulk-action').value = 'count';
        document.getElementById('trx-bulk-limit').value = limit;
        f.submit();
    });
    f.addEventListener('submit', function(e){
        if (document.getElementById('trx-bulk-action').value === 'selected' && document.querySelectorAll('.trx-row-cb:checked').length === 0) { e.preventDefault(); return false; }
    });
    up();
})();
</script>
@endpush
