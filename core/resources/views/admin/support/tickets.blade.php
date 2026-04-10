@extends('admin.layouts.app')

@section('panel')
<div class="support-ticket-panel">
    {{-- Status tabs: All | Pending | Closed | Answered --}}
    {{-- Status tabs: All | Pending | Closed | Answered --}}
    <div class="mb-8">
        <nav class="flex flex-wrap items-center gap-2.5 bg-slate-100/80 p-1.5 rounded-[22px] border border-slate-100 w-fit">
            <a href="{{ route('admin.ticket.index') }}" class="px-5 py-2.5 rounded-[18px] text-[13px] font-bold transition-all duration-300 {{ request()->routeIs('admin.ticket.index') && !request()->routeIs('admin.ticket.pending') && !request()->routeIs('admin.ticket.closed') && !request()->routeIs('admin.ticket.answered') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-500 hover:bg-white hover:text-indigo-600' }}">
                <i class="las la-inbox text-lg"></i>
                <span>@lang('All Ticket')</span>
            </a>
            <a href="{{ route('admin.ticket.pending') }}" class="px-5 py-2.5 rounded-[18px] text-[13px] font-bold transition-all duration-300 flex items-center gap-2 {{ request()->routeIs('admin.ticket.pending') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-500 hover:bg-white hover:text-indigo-600' }}">
                <i class="las la-clock text-lg"></i>
                <span>@lang('Pending')</span>
                @if(isset($pendingTicketCount) && $pendingTicketCount > 0)
                    <span class="bg-rose-500 text-white text-[10px] px-1.5 py-0.5 rounded-md">{{ $pendingTicketCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.ticket.answered') }}" class="px-5 py-2.5 rounded-[18px] text-[13px] font-bold transition-all duration-300 flex items-center gap-2 {{ request()->routeIs('admin.ticket.answered') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-500 hover:bg-white hover:text-indigo-600' }}">
                <i class="las la-check-circle text-lg"></i>
                <span>@lang('Answered')</span>
            </a>
            <a href="{{ route('admin.ticket.closed') }}" class="px-5 py-2.5 rounded-[18px] text-[13px] font-bold transition-all duration-300 flex items-center gap-2 {{ request()->routeIs('admin.ticket.closed') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-500 hover:bg-white hover:text-indigo-600' }}">
                <i class="las la-times-circle text-lg"></i>
                <span>@lang('Closed')</span>
            </a>
        </nav>
    </div>

    {{-- Stats: compact grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-2xl">
                <i class="las la-users"></i>
            </div>
            <div>
                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">@lang('Conversations')</span>
                <span class="text-xl font-bold text-slate-800">{{ $totalConversations ?? $items->total() }}</span>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-2xl">
                <i class="las la-comments"></i>
            </div>
            <div>
                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">@lang('Total Messages')</span>
                <span class="text-xl font-bold text-slate-800">{{ $totalMessages ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- Filters & Options --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 mb-8">
        <div class="flex flex-wrap items-center justify-between gap-6">
            <div class="flex flex-wrap items-center gap-6">
                @if(isset($hasChannelColumn) && $hasChannelColumn)
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-3 px-1">@lang('Channel')</label>
                    <div class="flex items-center gap-1.5 p-1 bg-slate-50 rounded-xl border border-slate-100">
                        <a href="{{ request()->fullUrlWithQuery(['channel' => null]) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ !request('channel') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">@lang('All')</a>
                        <a href="{{ request()->fullUrlWithQuery(['channel' => 'web']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ request('channel') == 'web' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Web</a>
                        <a href="{{ request()->fullUrlWithQuery(['channel' => 'telegram']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ request('channel') == 'telegram' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}"><i class="fab fa-telegram text-base"></i></a>
                        <a href="{{ request()->fullUrlWithQuery(['channel' => 'whatsapp']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ request('channel') == 'whatsapp' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}"><i class="fab fa-whatsapp text-base"></i></a>
                    </div>
                </div>
                @endif
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-3 px-1">@lang('Quick Bulk Delete')</label>
                    <form action="{{ route('admin.ticket.bulk-delete-messages-global') }}" method="post" class="flex items-center gap-2">
                        @csrf
                        <select name="delete_last" class="bg-slate-50 border border-slate-100 rounded-xl px-3 py-1.5 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                            <option value="">@lang('Last 20')</option>
                            <option value="50">@lang('Last 50')</option>
                            <option value="100">@lang('Last 100')</option>
                        </select>
                        <button type="submit" class="p-2 bg-rose-50 text-rose-600 rounded-xl border border-rose-100 hover:bg-rose-600 hover:text-white transition-all">
                            <i class="las la-trash-alt text-lg"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <form action="{{ route('admin.ticket.bulk-delete-conversations') }}" method="post" id="bulkConvForm" class="flex items-center gap-2">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700 transition-all shadow-sm shadow-rose-100">
                    <i class="las la-trash-alt text-base"></i> @lang('Delete Selected')
                </button>
            </form>
        </div>
    </div>

    {{-- Main Table Area --}}
    <div class="bg-white rounded-[24px] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider w-12 text-center">
                            <input type="checkbox" class="form-check-input rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20" id="selectAllConvsCheck">
                        </th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">@lang('Customer info')</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">@lang('Preview / Topics')</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center">@lang('Status & Activity')</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider text-right">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                        @forelse($items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4 text-center">
                                @if(!$item->is_guest && $item->user_id)
                                    <input type="checkbox" class="form-check-input conv-cb rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20" name="user_ids[]" value="{{ $item->user_id }}" form="bulkConvForm">
                                @else
                                    <span class="text-slate-200">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center font-bold text-sm">
                                        {{ substr($item->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-slate-700 leading-tight">{{ $item->name }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $item->email }}</div>
                                        @if($item->user_id)
                                            <a href="{{ route('admin.users.detail', $item->user_id) }}" class="inline-flex items-center text-[10px] font-bold text-indigo-500 hover:text-indigo-700 mt-1 uppercase tracking-tighter">@lang('View Profile')</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-[280px]">
                                    @foreach(array_slice($item->subjects ?? [], 0, 3) as $s)
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 text-[10px] font-bold border border-slate-200/50">{{ $s }}</span>
                                    @endforeach
                                    @if(count($item->subjects ?? []) > 3)
                                        <span class="inline-block px-1.5 py-0.5 rounded-md bg-indigo-50 text-indigo-600 text-[10px] font-black">+{{ count($item->subjects) - 3 }}</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 mt-2 italic line-clamp-1">
                                    {{ strLimit($item->last_message ?? '', 45) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    @php echo $item->status_badge ?? $item->statusBadge; @endphp
                                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">{{ diffForHumans($item->last_reply) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if(!$item->is_guest && $item->user_id)
                                        <a href="{{ route('admin.ticket.view.user', $item->user_id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-[11px] font-bold hover:bg-indigo-700 transition-all shadow-sm shadow-indigo-100" title="@lang('Open Chat')">
                                            <i class="las la-comments text-base"></i> @lang('Reply')
                                        </a>
                                    @else
                                        <a href="{{ route('admin.ticket.view', $item->primary_ticket_id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white rounded-lg text-[11px] font-bold hover:bg-black transition-all" title="@lang('View Details')">
                                            <i class="las la-desktop text-base"></i> @lang('Details')
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="px-6 py-12 text-center text-slate-400 font-medium" colspan="5">
                                <div class="flex flex-col items-center gap-3 opacity-60">
                                    <i class="las la-inbox text-5xl"></i>
                                    <span>{{ __($emptyMessage ?? 'No conversations tracked.') }}</span>
                                </div>
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

{{-- inline style moved to critical-admin.css --}}

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
