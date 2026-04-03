@extends('admin.layouts.app')

@section('panel')
<div class="support-ticket-panel">
    {{-- Status tabs: All | Pending | Closed | Answered --}}
    <div class="ticket-status-tabs mb-4">
        <nav class="ticket-tabs-nav">
            <a href="{{ route('admin.ticket.index') }}" class="ticket-tab {{ request()->routeIs('admin.ticket.index') && !request()->routeIs('admin.ticket.pending') && !request()->routeIs('admin.ticket.closed') && !request()->routeIs('admin.ticket.answered') ? 'active' : '' }}">
                <i class="las la-inbox"></i>
                <span>@lang('All Ticket')</span>
            </a>
            <a href="{{ route('admin.ticket.pending') }}" class="ticket-tab {{ request()->routeIs('admin.ticket.pending') ? 'active' : '' }}">
                <i class="las la-clock"></i>
                <span>@lang('Pending Ticket')</span>
                @if(isset($pendingTicketCount) && $pendingTicketCount > 0)
                    <span class="ticket-tab-badge">{{ $pendingTicketCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.ticket.closed') }}" class="ticket-tab {{ request()->routeIs('admin.ticket.closed') ? 'active' : '' }}">
                <i class="las la-times-circle"></i>
                <span>@lang('Closed Ticket')</span>
            </a>
            <a href="{{ route('admin.ticket.answered') }}" class="ticket-tab {{ request()->routeIs('admin.ticket.answered') ? 'active' : '' }}">
                <i class="las la-check-circle"></i>
                <span>@lang('Answered Ticket')</span>
            </a>
        </nav>
    </div>

    {{-- Stats: compact single row --}}
    <div class="ticket-stats row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="ticket-stat-card">
                <div class="ticket-stat-icon ticket-stat-icon--primary"><i class="las la-users"></i></div>
                <div class="ticket-stat-body">
                    <span class="ticket-stat-label">@lang('Conversations')</span>
                    <span class="ticket-stat-value">{{ $totalConversations ?? $items->total() }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="ticket-stat-card">
                <div class="ticket-stat-icon ticket-stat-icon--success"><i class="las la-comments"></i></div>
                <div class="ticket-stat-body">
                    <span class="ticket-stat-label">@lang('Total Messages')</span>
                    <span class="ticket-stat-value">{{ $totalMessages ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Info: one line, collapsible --}}
    <div class="ticket-info-bar mb-4">
        <details class="ticket-info-details">
            <summary class="ticket-info-summary">
                <i class="las la-info-circle"></i>
                <span>@lang('Support Ticket')</span>
                <small class="text-muted">— @lang('Live Chat, Inquiry, Order Support, WhatsApp, Telegram, Email')</small>
            </summary>
            <p class="ticket-info-text mb-0 small text-muted">@lang('Admin panel: messages older than 60 days are automatically deleted. User chat: only last 30 days are visible.')</p>
        </details>
    </div>

    {{-- Filters: one card, channel + subject --}}
    <div class="ticket-filters-card card b-radius--10 mb-4">
        <div class="card-body py-3">
            <div class="row g-3 align-items-center">
                @if(isset($hasChannelColumn) && $hasChannelColumn)
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted mb-1 d-block">@lang('Channel')</label>
                    <div class="ticket-filter-pills d-flex flex-wrap gap-1">
                        <a href="{{ request()->fullUrlWithQuery(['channel' => null, 'subject' => request('subject')]) }}" class="ticket-pill {{ !request('channel') ? 'active' : '' }}">@lang('All')</a>
                        <a href="{{ request()->fullUrlWithQuery(['channel' => 'web', 'subject' => request('subject')]) }}" class="ticket-pill {{ request('channel') == 'web' ? 'active' : '' }}"><i class="las la-globe"></i> Web</a>
                        <a href="{{ request()->fullUrlWithQuery(['channel' => 'telegram', 'subject' => request('subject')]) }}" class="ticket-pill {{ request('channel') == 'telegram' ? 'active' : '' }}"><i class="fab fa-telegram"></i></a>
                        <a href="{{ request()->fullUrlWithQuery(['channel' => 'whatsapp', 'subject' => request('subject')]) }}" class="ticket-pill {{ request('channel') == 'whatsapp' ? 'active' : '' }}"><i class="fab fa-whatsapp"></i></a>
                        <a href="{{ request()->fullUrlWithQuery(['channel' => 'email', 'subject' => request('subject')]) }}" class="ticket-pill {{ request('channel') == 'email' ? 'active' : '' }}"><i class="las la-envelope"></i></a>
                    </div>
                </div>
                @endif
                <div class="{{ (isset($hasChannelColumn) && $hasChannelColumn) ? 'col-12 col-md-6' : 'col-12' }}">
                    <label class="form-label small text-muted mb-1 d-block">@lang('Subject')</label>
                    @php $sub = request('subject'); @endphp
                    <div class="ticket-filter-pills d-flex flex-wrap gap-1">
                        <a href="{{ request()->fullUrlWithQuery(['subject' => null, 'channel' => request('channel')]) }}" class="ticket-pill {{ !$sub ? 'active' : '' }}">@lang('All')</a>
                        <a href="{{ request()->fullUrlWithQuery(['subject' => 'Live Chat Message', 'channel' => request('channel')]) }}" class="ticket-pill {{ $sub === 'Live Chat Message' ? 'active' : '' }}">@lang('Live Chat')</a>
                        <a href="{{ request()->fullUrlWithQuery(['subject' => 'General Inquiry', 'channel' => request('channel')]) }}" class="ticket-pill {{ $sub === 'General Inquiry' ? 'active' : '' }}">@lang('Inquiry')</a>
                        <a href="{{ request()->fullUrlWithQuery(['subject' => 'Report a Problem', 'channel' => request('channel')]) }}" class="ticket-pill {{ $sub === 'Report a Problem' ? 'active' : '' }}">@lang('Report')</a>
                        <a href="{{ request()->fullUrlWithQuery(['subject' => 'Order Support', 'channel' => request('channel')]) }}" class="ticket-pill {{ $sub === 'Order Support' ? 'active' : '' }}">@lang('Order')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk actions: compact bar --}}
    <div class="ticket-bulk-bar card b-radius--10 mb-4">
        <div class="card-body py-2 px-3">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="small fw-semibold text-muted">@lang('Bulk actions'):</span>
                <form action="{{ route('admin.ticket.bulk-delete-messages-global') }}" method="post" class="d-flex align-items-center gap-2" onsubmit="return confirm('{{ __('Delete last N messages from entire system? This cannot be undone.') }}'.replace('N', document.getElementById('globalDeleteLast').value));">
                    @csrf
                    <select name="delete_last" id="globalDeleteLast" class="form-select form-select-sm ticket-bulk-select" required>
                        <option value="">@lang('Delete last…')</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                        <option value="300">300</option>
                        <option value="400">400</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                    </select>
                    <span class="small text-muted">@lang('messages')</span>
                    <button type="submit" class="btn btn-sm btn--danger"><i class="las la-trash"></i></button>
                </form>
                <span class="text-muted small">|</span>
                <form action="{{ route('admin.ticket.bulk-delete-conversations') }}" method="post" id="bulkConvForm" class="d-flex align-items-center gap-2">
                    @csrf
                    <button type="button" class="btn btn-sm btn--outline-secondary" id="selectAllConvs">@lang('Select all')</button>
                    <button type="submit" class="btn btn-sm btn--danger" onclick="return confirm('{{ __('Delete selected conversations and all their messages? This cannot be undone.') }}');">
                        <i class="las la-trash"></i> @lang('Delete selected')
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card b-radius--10 ticket-table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--light ticket-table">
                    <thead>
                        <tr>
                            <th class="ticket-th-check"><input type="checkbox" class="form-check-input" id="selectAllConvsCheck" title="@lang('Select all')"></th>
                            @if(isset($hasChannelColumn) && $hasChannelColumn)<th class="ticket-th-channel">@lang('Channel')</th>@endif
                            <th>@lang('User / Contact')</th>
                            <th>@lang('Subjects')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Priority')</th>
                            <th>@lang('Last Reply')</th>
                            <th class="ticket-th-action">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr class="ticket-row">
                            <td class="ticket-td-check">
                                @if(!$item->is_guest && $item->user_id)
                                    <input type="checkbox" class="form-check-input conv-cb" name="user_ids[]" value="{{ $item->user_id }}" form="bulkConvForm">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            @if(isset($hasChannelColumn) && $hasChannelColumn)
                            <td class="ticket-td-channel">
                                @php $ch = $item->channel ?? 'web'; @endphp
                                @if($ch == 'web')<span class="badge badge--info" title="@lang('Web')"><i class="las la-globe"></i></span>
                                @elseif($ch == 'telegram')<span class="badge badge--primary"><i class="fab fa-telegram"></i></span>
                                @elseif($ch == 'whatsapp')<span class="badge badge--success"><i class="fab fa-whatsapp"></i></span>
                                @elseif($ch == 'email')<span class="badge badge--warning"><i class="las la-envelope"></i></span>
                                @else<span class="badge badge--dark"><i class="las la-link"></i></span>
                                @endif
                            </td>
                            @endif
                            <td>
                                <div class="ticket-user">
                                    <span class="fw-semibold">{{ $item->name }}</span>
                                    @if($item->email)<br><small class="text-muted">{{ $item->email }}</small>@endif
                                    @if($item->user_id)<br><a href="{{ route('admin.users.detail', $item->user_id) }}" class="small">@lang('View user')</a>@endif
                                </div>
                            </td>
                            <td>
                                <div class="ticket-subjects">
                                    @foreach(array_slice($item->subjects ?? [], 0, 3) as $s)
                                        <span class="badge badge--secondary me-1 mb-1">{{ strLimit($s, 16) }}</span>
                                    @endforeach
                                    @if(count($item->subjects ?? []) > 3)
                                        <span class="badge badge--dark">+{{ count($item->subjects) - 3 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>@php echo $item->status_badge ?? $item->statusBadge ?? ''; @endphp</td>
                            <td>
                                @if(isset($item->priority))
                                    @if($item->priority == Status::PRIORITY_LOW)<span class="badge badge--dark">@lang('Low')</span>
                                    @elseif($item->priority == Status::PRIORITY_MEDIUM)<span class="badge badge--warning">@lang('Medium')</span>
                                    @elseif($item->priority == Status::PRIORITY_HIGH)<span class="badge badge--danger">@lang('High')</span>
                                    @endif
                                @else
                                    <span class="badge badge--warning">@lang('Medium')</span>
                                @endif
                            </td>
                            <td><span class="text-muted small">{{ diffForHumans($item->last_reply) }}</span></td>
                            <td class="ticket-td-action">
                                @if(!$item->is_guest && $item->user_id)
                                    <a href="{{ route('admin.ticket.view.user', $item->user_id) }}" class="btn btn-sm btn--primary" title="@lang('Open')">
                                        <i class="las la-comments"></i> @lang('Open')
                                    </a>
                                @else
                                    <a href="{{ route('admin.ticket.view', $item->primary_ticket_id) }}" class="btn btn-sm btn--primary">
                                        <i class="las la-desktop"></i> @lang('Details')
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center text-muted py-5" colspan="{{ (isset($hasChannelColumn) && $hasChannelColumn) ? 8 : 7 }}">
                                <i class="las la-inbox font-size--36px opacity-50 d-block mb-2"></i>
                                {{ __($emptyMessage ?? 'No conversations found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($items->hasPages())
            <div class="card-footer py-3 border-top">
                {{ paginateLinks($items) }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
.support-ticket-panel { --ticket-radius: 10px; --ticket-space: 1rem; }
.ticket-status-tabs { margin-bottom: var(--ticket-space); }
.ticket-tabs-nav { display: flex; flex-wrap: wrap; gap: 6px; }
.ticket-tab { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-size: 0.875rem; font-weight: 500; color: #5a6c7d; text-decoration: none; background: rgba(0,0,0,0.04); transition: background .15s, color .15s; }
.ticket-tab:hover { background: rgba(0,0,0,0.08); color: #1e3a5f; }
.ticket-tab.active { background: var(--base); color: #fff; }
.ticket-tab-badge { background: #dc3545; color: #fff; font-size: 0.7rem; padding: 2px 6px; border-radius: 10px; margin-left: 4px; }
.ticket-stats .ticket-stat-card { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: var(--ticket-radius); background: #fff; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 2px rgba(0,0,0,0.04); }
.ticket-stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
.ticket-stat-icon--primary { background: rgba(79, 196, 247, 0.18); color: var(--base, #0d6efd); }
.ticket-stat-icon--success { background: rgba(40, 199, 111, 0.15); color: #28c76f; }
.ticket-stat-body { display: flex; flex-direction: column; gap: 2px; }
.ticket-stat-label { font-size: 0.75rem; color: #6c757d; }
.ticket-stat-value { font-size: 1.25rem; font-weight: 600; color: #1e3a5f; }
.ticket-info-bar { font-size: 0.875rem; }
.ticket-info-details { border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; padding: 8px 12px; background: rgba(13, 110, 253, 0.06); }
.ticket-info-summary { cursor: pointer; list-style: none; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.ticket-info-summary::-webkit-details-marker { display: none; }
.ticket-info-text { padding-top: 6px; }
.ticket-filters-card .ticket-filter-pills { display: flex; flex-wrap: wrap; gap: 6px; }
.ticket-pill { display: inline-flex; align-items: center; gap: 4px; padding: 5px 10px; border-radius: 6px; font-size: 0.8rem; text-decoration: none; color: #5a6c7d; background: rgba(0,0,0,0.05); transition: all .15s; }
.ticket-pill:hover { background: rgba(0,0,0,0.1); color: #1e3a5f; }
.ticket-pill.active { background: var(--base); color: #fff; }
.ticket-bulk-bar .ticket-bulk-select { width: 120px; }
.ticket-table-card .ticket-table { margin-bottom: 0; }
.ticket-table thead th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.03em; color: #6c757d; font-weight: 600; padding: 12px 14px; border-bottom: 1px solid rgba(0,0,0,0.06); }
.ticket-table tbody td { padding: 14px; vertical-align: middle; border-bottom: 1px solid rgba(0,0,0,0.05); }
.ticket-row:hover { background: rgba(0,0,0,0.02); }
.ticket-th-check, .ticket-td-check { width: 42px; text-align: center; }
.ticket-th-channel, .ticket-td-channel { width: 90px; }
.ticket-th-action, .ticket-td-action { white-space: nowrap; }
.ticket-user { line-height: 1.4; }
.ticket-subjects { line-height: 1.5; }
</style>
@endpush

@push('script')
<script>
(function() {
    var selectAllBtn = document.getElementById('selectAllConvs');
    var selectAllCheck = document.getElementById('selectAllConvsCheck');
    var checkboxes = document.querySelectorAll('.conv-cb');
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(function(cb) { cb.checked = true; });
            if (selectAllCheck) selectAllCheck.checked = true;
        });
    }
    if (selectAllCheck) {
        selectAllCheck.addEventListener('change', function() {
            checkboxes.forEach(function(cb) { cb.checked = selectAllCheck.checked; });
        });
    }
    var bulkForm = document.getElementById('bulkConvForm');
    if (bulkForm) {
        bulkForm.addEventListener('submit', function(e) {
            var checked = document.querySelectorAll('.conv-cb:checked');
            if (!checked.length) { e.preventDefault(); alert('{{ __("Please select at least one conversation.") }}'); }
        });
    }
})();
</script>
@endpush
