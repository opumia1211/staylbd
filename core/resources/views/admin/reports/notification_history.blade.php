@extends('admin.layouts.app')
@section('panel')
    {{-- Stats cards (only on main notification history page) --}}
    @if(isset($stats) && $stats)
    <div class="row mb-4">
        <div class="col-md-2 col-6">
            <div class="card b-radius--10 border--primary">
                <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">@lang('Total')</span>
                        <h4 class="mb-0 mt-1">{{ $stats['total'] ?? 0 }}</h4>
                    </div>
                    <i class="las la-bell text--primary" style="font-size: 1.5rem;"></i>
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
        <div class="col-md-2 col-6">
            <div class="card b-radius--10 border--warning">
                <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">@lang('Email')</span>
                        <h4 class="mb-0 mt-1">{{ $stats['email'] ?? 0 }}</h4>
                    </div>
                    <i class="las la-envelope text--warning" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card b-radius--10 border--dark">
                <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">@lang('SMS')</span>
                        <h4 class="mb-0 mt-1">{{ $stats['sms'] ?? 0 }}</h4>
                    </div>
                    <i class="las la-sms text--dark" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="card b-radius--10 border--danger">
                <div class="card-body py-3 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">@lang('Push')</span>
                        <h4 class="mb-0 mt-1">{{ $stats['push'] ?? 0 }}</h4>
                    </div>
                    <i class="las la-mobile-alt text--danger" style="font-size: 1.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Filter bar (only on main notification history page) --}}
    @if(request()->routeIs('admin.report.notification.history'))
    <div class="card responsive-filter-card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.report.notification.history') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">@lang('Username / Sent To')</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="@lang('Search username or recipient')">
                </div>
                <div class="col-md-2">
                    <label class="form-label">@lang('Type')</label>
                    <select name="notification_type" class="form-control">
                        <option value="">@lang('All')</option>
                        <option value="email" {{ request('notification_type') === 'email' ? 'selected' : '' }}>@lang('Email')</option>
                        <option value="sms" {{ request('notification_type') === 'sms' ? 'selected' : '' }}>@lang('SMS')</option>
                        <option value="push" {{ request('notification_type') === 'push' ? 'selected' : '' }}>@lang('Push')</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">@lang('Date Range')</label>
                    <input type="text" name="date" value="{{ request('date') }}" class="form-control datepicker-here" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position="bottom right" placeholder="@lang('Start - End')" autocomplete="off">
                </div>
                <div class="col-md-1">
                    <label class="form-label">@lang('Per page')</label>
                    <select name="per_page" class="form-control">
                        @foreach([10, 20, 50, 100] as $n)
                        <option value="{{ $n }}" {{ request('per_page') == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2 flex-wrap">
                    <button type="submit" class="btn btn--primary btn-sm"><i class="las la-filter"></i> @lang('Filter')</button>
                    <a href="{{ route('admin.report.notification.history') }}" class="btn btn-outline--secondary btn-sm">@lang('Reset')</a>
                    <a href="{{ route('admin.report.notification.history.export') }}?{{ request()->getQueryString() }}" class="btn btn-outline--info btn-sm"><i class="las la-file-csv"></i> @lang('Export CSV')</a>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Bulk delete (main notification history page) --}}
    @if(request()->routeIs('admin.report.notification.history'))
    <form id="notif-bulk-form" action="{{ route('admin.report.notification.history.bulk_delete') }}" method="POST" class="mb-3">
        @csrf
        <input type="hidden" name="action" value="selected" id="notif-bulk-action">
        <input type="hidden" name="limit" value="" id="notif-bulk-limit">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <input type="hidden" name="notification_type" value="{{ request('notification_type') }}">
        <input type="hidden" name="date" value="{{ request('date') }}">
        <div class="d-flex flex-wrap gap-2 align-items-center small">
            <button type="submit" class="btn btn-sm btn-outline--danger" id="notif-delete-selected" disabled>@lang('Delete selected')</button>
            <span class="text-muted">@lang('or delete by count'):</span>
            <select class="form-select form-select-sm d-inline-block" id="notif-delete-count" style="width:auto;max-width:5rem;">
                <option value="">—</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="all">@lang('All')</option>
            </select>
            <button type="button" class="btn btn-sm btn-outline--danger" id="notif-delete-by-count" disabled>@lang('Delete')</button>
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
                                    @if(request()->routeIs('admin.report.notification.history'))
                                    <th style="width:2rem;"><input type="checkbox" id="notif-select-all" class="form-check-input" title="@lang('Select all')"></th>
                                    @endif
                                    <th>@lang('User')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Sent')</th>
                                    <th>@lang('Sender')</th>
                                    <th>@lang('Subject')</th>
                                    <th class="text-end">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        @if(request()->routeIs('admin.report.notification.history'))
                                        <td><input type="checkbox" form="notif-bulk-form" name="ids[]" value="{{ $log->id }}" class="form-check-input notif-row-cb"></td>
                                        @endif
                                        <td>
                                            @if($log->user)
                                                <span class="fw-bold">{{ $log->user->fullname }}</span>
                                                <br>
                                                <span class="small">
                                                    <a href="{{ route('admin.users.detail', $log->user_id) }}"><span>@</span>{{ $log->user->username }}</a>
                                                </span>
                                            @else
                                                <span class="fw-bold">@lang('System')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php $type = strtolower($log->notification_type ?? 'other'); @endphp
                                            @if($type === 'email')
                                                <span class="badge badge--primary">@lang('Email')</span>
                                            @elseif($type === 'sms')
                                                <span class="badge badge--success">@lang('SMS')</span>
                                            @elseif($type === 'push')
                                                <span class="badge badge--info">@lang('Push')</span>
                                            @else
                                                <span class="badge badge--secondary">{{ __($log->notification_type ?? '—') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="d-block">{{ showDateTime($log->created_at) }}</span>
                                            <span class="small text-muted">{{ diffForHumans($log->created_at) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ __($log->sender ?? '—') }}</span>
                                        </td>
                                        <td>
                                            <span title="{{ $log->subject ?? '' }}">{{ \Illuminate\Support\Str::limit($log->subject ?? '—', 40) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline--primary notifyDetail" data-type="{{ $log->notification_type ?? '' }}" data-message="{{ $log->notification_type === 'email' ? route('admin.report.email.details', $log->id) : e($log->message ?? '') }}" data-sent_to="{{ e($log->sent_to ?? '—') }}">
                                                <i class="las la-desktop"></i> @lang('Detail')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center py-4" colspan="{{ request()->routeIs('admin.report.notification.history') ? 7 : 6 }}">{{ __($emptyMessage ?? 'No notifications found.') }}</td>
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

{{-- Detail Modal (Bootstrap 5 compatible) - z-index must be above backdrop so content is visible --}}
@push('style')
<style>
    #notifyDetailModal { z-index: 10610 !important; }
    #notifyDetailModal .modal-dialog { z-index: 10612 !important; position: relative; }
    body.modal-open .modal-backdrop { z-index: 10600 !important; }
</style>
@endpush
<div class="modal fade" id="notifyDetailModal" tabindex="-1" aria-labelledby="notifyDetailModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notifyDetailModalLabel">@lang('Notification Details')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3"><strong>@lang('To'):</strong> <span class="sent_to"></span></p>
                <div class="detail border rounded p-3" style="min-height: 200px; background: #f8f9fa;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    @if(@$user)
        <a href="{{ route('admin.users.notification.single', $user->id) }}" class="btn btn-outline--primary btn-sm"><i class="las la-paper-plane"></i> @lang('Send Notification')</a>
    @else
        <x-search-form placeholder="@lang('Search Username')" />
    @endif
@endpush

@if(request()->routeIs('admin.report.notification.history'))
@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}">
@endpush
@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush
@endif

@push('script')
<script>
(function() {
    "use strict";
    function initNotifyDetailModal() {
        var modalEl = document.getElementById('notifyDetailModal');
        if (!modalEl) return;

        // Move modal to end of body for proper stacking (above all wrappers/overlays)
        if (modalEl.parentNode !== document.body) {
            document.body.appendChild(modalEl);
        }

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.textContent = String(text || '');
            return div.innerHTML;
        }

        function showDetail(btn) {
            var type = (btn.getAttribute('data-type') || '').toLowerCase();
            var message = btn.getAttribute('data-message') || '';
            var sentTo = btn.getAttribute('data-sent_to') || '—';

            var detailEl = modalEl.querySelector('.detail');
            var sentToEl = modalEl.querySelector('.sent_to');
            if (sentToEl) sentToEl.textContent = sentTo;
            if (!detailEl) return;

            if (type === 'email' && message && message.indexOf('http') === 0) {
                detailEl.innerHTML = '<iframe src="' + escapeHtml(message) + '" height="450" width="100%" title="Email content" style="border: 0;"></iframe>';
            } else {
                detailEl.innerHTML = '<pre class="mb-0 small" style="white-space: pre-wrap; word-break: break-word; max-height: 400px; overflow-y: auto;">' + escapeHtml(message || 'No content') + '</pre>';
            }

            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    bsModal.show();
                } else if (typeof jQuery !== 'undefined') {
                    jQuery(modalEl).modal('show');
                }
            } catch (err) {
                console.error('Notify detail modal error:', err);
            }
        }

        // Delegated click - works for dynamically loaded content
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.notifyDetail');
            if (btn) {
                e.preventDefault();
                e.stopPropagation();
                showDetail(btn);
            }
        });

        modalEl.addEventListener('hidden.bs.modal', function() {
            var detailEl = modalEl.querySelector('.detail');
            if (detailEl) detailEl.innerHTML = '';
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNotifyDetailModal);
    } else {
        initNotifyDetailModal();
    }

    @if(request()->routeIs('admin.report.notification.history'))
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('.datepicker-here') && typeof jQuery !== 'undefined') {
                var $dp = jQuery('.datepicker-here');
                if ($dp.length && !$dp.first().data('datepicker')) {
                    $dp.datepicker();
                }
            }
        });
    } else if (document.querySelector('.datepicker-here') && typeof jQuery !== 'undefined') {
        var $dp = jQuery('.datepicker-here');
        if ($dp.length && !$dp.first().data('datepicker')) {
            $dp.datepicker();
        }
    }
    (function(){
        var f = document.getElementById('notif-bulk-form');
        if (!f) return;
        var sel = document.getElementById('notif-delete-selected');
        var cnt = document.getElementById('notif-delete-count');
        var btn = document.getElementById('notif-delete-by-count');
        var all = document.getElementById('notif-select-all');
        function up() {
            var n = document.querySelectorAll('.notif-row-cb:checked').length;
            if (sel) sel.disabled = n === 0;
            if (all) all.checked = document.querySelectorAll('.notif-row-cb').length > 0 && document.querySelectorAll('.notif-row-cb').length === n;
        }
        if (all) all.addEventListener('change', function(){ document.querySelectorAll('.notif-row-cb').forEach(function(c){ c.checked = all.checked; }); up(); });
        document.querySelectorAll('.notif-row-cb').forEach(function(c){ c.addEventListener('change', up); });
        if (cnt) cnt.addEventListener('change', function(){ if(btn) btn.disabled = !this.value; });
        if (btn && f) btn.addEventListener('click', function(){
            var limit = cnt ? cnt.value : '';
            if (!limit) return;
            if (!confirm(limit === 'all' ? '{{ __("Delete ALL notifications (with current filters)?") }}' : '{{ __("Delete first") }} ' + limit + '?')) return;
            document.getElementById('notif-bulk-action').value = 'count';
            document.getElementById('notif-bulk-limit').value = limit;
            f.submit();
        });
        f.addEventListener('submit', function(e){
            if (document.getElementById('notif-bulk-action').value === 'selected' && document.querySelectorAll('.notif-row-cb:checked').length === 0) { e.preventDefault(); return false; }
        });
        up();
    })();
    @endif
})();
</script>
@endpush
