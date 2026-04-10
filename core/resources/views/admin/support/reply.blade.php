@extends('admin.layouts.app')

@section('panel')
<div class="space-y-6">
    {{-- Subject Filters --}}
    @if(!empty($byUser) && isset($ticket->user_id))
        @php
            $subjects = ['Live Chat Message', 'General Inquiry', 'Report a Problem', 'Order Support'];
            $currentSubject = $subjectFilter ?? null;
        @endphp
        <div class="flex flex-wrap items-center gap-2.5">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest px-1">@lang('Topics'):</span>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.ticket.view.user', $ticket->user_id) }}" class="px-4 py-1.5 rounded-full text-[11px] font-bold transition-all duration-300 {{ !$currentSubject ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white text-slate-500 border border-slate-200 hover:border-indigo-400 hover:text-indigo-600' }}">@lang('All')</a>
                @foreach($subjects as $sub)
                    <a href="{{ route('admin.ticket.view.user', $ticket->user_id) }}?subject={{ urlencode($sub) }}" class="px-4 py-1.5 rounded-full text-[11px] font-bold transition-all duration-300 {{ $currentSubject === $sub ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white text-slate-500 border border-slate-200 hover:border-indigo-400 hover:text-indigo-600' }}">{{ __($sub) }}</a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Chat Header --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl">
                <i class="las la-envelope-open"></i>
            </div>
            <div>
                <h6 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    @if(!empty($byUser))
                        @lang('Conversation with') {{ $ticket->user->fullname ?? $ticket->name }}
                    @else
                        #{{ $ticket->ticket }} — {{ $ticket->subject }}
                    @endif
                    @php echo $ticket->statusBadge; @endphp
                </h6>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[11px] text-slate-400 font-medium">@lang('Customer'):</span>
                    <span class="text-[11px] font-bold text-indigo-500 hover:underline">
                        {{ $ticket->name }} @if($ticket->user_id)<a href="{{ route('admin.users.detail', $ticket->user_id) }}">(@lang('View Profile'))</a>@endif
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($ticket->status != Status::TICKET_CLOSE)
                <button class="btn inline-flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-600 rounded-xl text-xs font-bold hover:bg-rose-600 hover:text-white transition-all shadow-sm shadow-rose-50" type="button" data-bs-toggle="modal" data-bs-target="#DelModal">
                    <i class="las la-times-circle text-base"></i> @lang('Close Ticket')
                </button>
            @endif
            <a href="{{ route('admin.ticket.index') }}" class="btn inline-flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition-all border border-slate-200">
                <i class="las la-arrow-left text-base"></i> @lang('Back')
            </a>
        </div>
    </div>

    {{-- Chat Thread Body --}}
    <div class="bg-white rounded-[24px] shadow-sm border border-slate-100 overflow-hidden flex flex-col h-[700px]">
        <div class="flex-1 overflow-y-auto p-8 space-y-8" id="adminChatThread">
            <div id="adminChatMessages" class="space-y-8">
                @php
                    $tz = config('app.timezone', 'UTC');
                    $lastDate = '';
                @endphp
                @forelse($messages as $message)
                    @php
                        $dt = $message->created_at->timezone($tz);
                        $dateLabel = $dt->isToday() ? __('Today') : ($dt->isYesterday() ? __('Yesterday') : $dt->format('d M Y'));
                    @endphp
                    @if($dateLabel !== $lastDate)
                        @php $lastDate = $dateLabel; @endphp
                        <div class="flex justify-center">
                            <span class="bg-slate-100 text-slate-400 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest border border-slate-200/50">{{ $dateLabel }}</span>
                        </div>
                    @endif

                    <div class="flex group {{ $message->admin_id ? 'justify-end' : 'justify-start' }}" data-msg-id="{{ $message->id }}">
                        <div class="flex flex-col max-w-[80%] {{ $message->admin_id ? 'items-end' : 'items-start' }}">
                            {{-- Bubble Meta --}}
                            <div class="flex items-center gap-2 mb-1.5 px-2">
                                <span class="text-[11px] font-bold text-slate-400">{{ $message->admin_id ? ($message->admin->name ?? 'Staff') : ($message->ticket->name ?? $ticket->name) }}</span>
                                <span class="text-[10px] text-slate-300">{{ $dt->format('g:i A') }}</span>
                            </div>
                            
                            {{-- Message Content --}}
                            <div class="relative p-4 rounded-2xl text-[13px] leading-relaxed shadow-sm {{ $message->admin_id ? 'bg-indigo-600 text-white rounded-tr-none shadow-indigo-100' : 'bg-slate-50 text-slate-700 rounded-tl-none border border-slate-100' }}">
                                {!! nl2br(e($message->message)) !!}

                                @if($message->attachments->count() > 0)
                                    <div class="mt-4 grid grid-cols-2 gap-2">
                                        @foreach($message->attachments as $att)
                                            @php
                                                $attExt = strtolower(pathinfo($att->attachment ?? '', PATHINFO_EXTENSION));
                                                $isImage = in_array($attExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $imgUrl = $isImage ? route('admin.ticket.download', encrypt($att->id)) . '?inline=1' : null;
                                            @endphp
                                            @if($isImage && $imgUrl)
                                                <a href="{{ $imgUrl }}" target="_blank" class="block rounded-lg overflow-hidden border border-black/10 hover:opacity-90 transition-opacity">
                                                    <img src="{{ $imgUrl }}" class="w-full h-24 object-cover" alt="Attachment">
                                                </a>
                                            @else
                                                <a href="{{ route('admin.ticket.download', encrypt($att->id)) }}" class="flex items-center gap-2 p-2 rounded-lg bg-black/5 hover:bg-black/10 transition-colors text-[10px] truncate max-w-full">
                                                    <i class="las la-paperclip text-sm"></i> File {{ $loop->iteration }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Msg Actions (Show on group hover) --}}
                            <div class="flex items-center gap-3 mt-2 px-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <label class="flex items-center gap-1.5 cursor-pointer text-[10px] font-bold text-slate-400 hover:text-indigo-500">
                                    <input type="checkbox" class="admin-msg-bulk-cb rounded w-3 h-3 border-slate-300 text-indigo-600 focus:ring-0" name="message_ids[]" value="{{ $message->id }}">
                                    @lang('Select')
                                </label>
                                <button type="button" class="btn text-[10px] font-bold text-rose-400 hover:text-rose-600 inline-flex items-center gap-1 confirmationBtn" data-question="@lang('Delete this message?')" data-action="{{ route('admin.ticket.delete', $message->id) }}">
                                    <i class="las la-trash text-sm"></i> @lang('Delete')
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full opacity-30 gap-3">
                        <i class="las la-comments text-7xl"></i>
                        <p class="font-bold text-slate-400">@lang('No messages exchanged yet.')</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Bulk Bar & Reply Box --}}
        <div class="bg-slate-50/80 border-t border-slate-100 p-6">
            @if($messages->count() > 0)
                <div class="flex flex-wrap items-center gap-6 mb-6">
                    <form action="{{ route('admin.ticket.bulk-delete') }}" method="post" class="flex flex-wrap items-center gap-4" id="adminBulkDeleteForm">
                        @csrf
                        <input type="hidden" name="{{ !empty($byUser) ? 'user_id' : 'ticket_id' }}" value="{{ !empty($byUser) ? $ticket->user_id : $ticket->id }}">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-500 cursor-pointer">
                            <input type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-0" id="adminSelectAllMsgs"> @lang('Select All')
                        </label>
                        <div class="flex items-center gap-2">
                            <select name="delete_last" class="bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-[11px] font-bold text-slate-600 outline-none focus:ring-2 focus:ring-indigo-500/20">
                                <option value="">@lang('Delete Last...')</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <button type="submit" name="action" value="last" class="btn p-2 bg-rose-50 text-rose-600 rounded-lg border border-rose-100 hover:bg-rose-600 hover:text-white transition-all"><i class="las la-trash text-lg"></i></button>
                        </div>
                        <button type="submit" name="action" value="selected" class="btn px-4 py-2 bg-rose-600 text-white rounded-xl text-[11px] font-bold hover:bg-rose-700 transition-all shadow-sm shadow-rose-100">@lang('Delete Selected')</button>
                    </form>
                </div>
            @endif

            @if($ticket->status != Status::TICKET_CLOSE)
                <form action="{{ route('admin.ticket.reply', $ticket->id) }}" method="post" enctype="multipart/form-data" id="adminChatReplyForm">
                    @csrf
                    <div class="flex flex-col gap-3">
                        <div class="relative group">
                            <textarea name="message" id="adminReplyMessage" rows="3" class="w-full bg-white border border-slate-200 rounded-2xl p-4 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all resize-none shadow-sm" placeholder="@lang('Write your response here...')"></textarea>
                            <div class="absolute bottom-3 right-3 flex items-center gap-2">
                                <label class="btn flex items-center gap-2 px-3 py-1.5 bg-slate-50 text-slate-500 rounded-lg text-xs font-bold border border-slate-200 hover:border-indigo-400 hover:text-indigo-600 cursor-pointer transition-all">
                                    <i class="las la-paperclip text-base"></i> @lang('Attach')
                                    <input type="file" name="attachments[]" class="hidden" id="adminReplyAttach" multiple>
                                </label>
                                <button type="submit" class="btn flex items-center gap-2 px-6 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                                    <i class="las la-paper-plane text-base"></i> @lang('Send Message')
                                </button>
                            </div>
                        </div>
                        <div id="adminReplyFileList" class="flex flex-wrap gap-2 px-1"></div>
                        <p class="text-[10px] text-slate-400 px-1"><i class="las la-info-circle"></i> @lang('Supported formats: JPG, PNG, PDF, DOCX (Max 5 files).')</p>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

    <div class="modal fade" id="DelModal" tabindex="-1" aria-labelledby="delModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Close Support Ticket')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">@lang('Are you sure you want to close this ticket?')</p>
                </div>
                <div class="modal-footer">
                    <form method="post" action="{{ route('admin.ticket.close', $ticket->id) }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="replayTicket" value="2">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.ticket.index') }}" />
@endpush

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
(function() {
    var ticketId = {{ (int) $ticket->id }};
    var byUser = {{ isset($byUser) && $byUser ? 'true' : 'false' }};
    var userId = {{ $ticket->user_id ?? 0 }};
    var subjectParam = {{ isset($subjectFilter) && $subjectFilter ? json_encode($subjectFilter) : 'null' }};
    var messagesUrl = byUser && userId
        ? ('{{ route("admin.ticket.messages.json.user", ["userId" => $ticket->user_id ?? 0]) }}' + (subjectParam ? '?subject=' + encodeURIComponent(subjectParam) : ''))
        : '{{ route("admin.ticket.messages.json", $ticket->id) }}';
    var thread = document.getElementById('adminChatThread');
    var messagesEl = document.getElementById('adminChatMessages');
    var form = document.getElementById('adminChatReplyForm');

    function scrollToBottom() {
        if (thread) { thread.scrollTop = thread.scrollHeight; }
    }
    if (thread) scrollToBottom();

    var attachInput = document.getElementById('adminReplyAttach');
    var fileListEl = document.getElementById('adminReplyFileList');
    if (attachInput && fileListEl) {
        attachInput.addEventListener('change', function() {
            fileListEl.innerHTML = '';
            for (var i = 0; i < this.files.length; i++) {
                var f = this.files[i];
                fileListEl.innerHTML += '<span class="badge badge--secondary me-1">' + f.name + '</span>';
            }
        });
    }

    function buildMessageHtml(m, skipDateDivider) {
        var html = '';
        if (!skipDateDivider && m.date_label) {
            html += '<div class="admin-chat-date-divider"><span>' + (m.date_label || '') + '</span></div>';
        }
        var cls = m.is_admin ? 'admin-chat-msg--admin' : 'admin-chat-msg--user';
        var name = (m.name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        var msg = (m.message || '').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
        var timeStr = m.created_at_full || m.created_at || '';
        html += '<div class="admin-chat-msg ' + cls + '" data-msg-id="' + (m.id || '') + '">';
        html += '<div class="admin-chat-msg-head"><span class="admin-chat-msg-name">' + name + '</span><span class="admin-chat-msg-time" title="' + (timeStr.replace(/"/g, '&quot;')) + '">' + timeStr + '</span></div>';
        html += '<div class="admin-chat-msg-text">' + msg + '</div>';
        if (m.attachments && m.attachments.length) {
            for (var j = 0; j < m.attachments.length; j++) {
                html += '<div class="admin-chat-msg-att mt-2"><a href="' + m.attachments[j] + '" target="_blank" class="btn btn-sm btn--outline-primary me-1 mb-1"><i class="las la-paperclip"></i> @lang("Attachment") ' + (j + 1) + '</a></div>';
            }
        }
        html += '</div>';
        return html;
    }

    var lastPollCount = 0;
    var lastPollId = 0;
    function pollMessages() {
        if (!messagesUrl || !messagesEl) return;
        fetch(messagesUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var list = data.messages || [];
                var lastId = list.length ? (list[list.length - 1].id) : 0;
                if (list.length === lastPollCount && lastId === lastPollId) return;
                lastPollCount = list.length;
                lastPollId = lastId;
                var empty = messagesEl.querySelector('.text-center.text-muted');
                if (list.length === 0) {
                    if (!empty) {
                        messagesEl.innerHTML = '<div class="text-center text-muted py-5"><i class="las la-comment-slash font-size--48px opacity-50"></i><p class="mt-2 mb-0">@lang("No messages yet.")</p></div>';
                    }
                    return;
                }
                var lastDate = '';
                var html = '';
                for (var i = 0; i < list.length; i++) {
                    var showDate = list[i].date_label && list[i].date_label !== lastDate;
                    if (showDate) lastDate = list[i].date_label;
                    html += buildMessageHtml(list[i], !showDate);
                }
                messagesEl.innerHTML = html;
                scrollToBottom();
            })
            .catch(function() {});
    }
    /* পোলিং: ইউজারের নতুন মেসেজ রিলোড ছাড়াই দেখাবে – ৩ সেকেন্ডে একবার */
    pollMessages();
    var pollInterval = setInterval(pollMessages, 3000);

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = form.querySelector('button[type="submit"]');
            var textarea = document.getElementById('adminReplyMessage');
            var msgText = textarea ? textarea.value.trim() : '';
            if (!msgText) return;
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="las la-spinner la-spin"></i> @lang("Sending...")'; }

            /* অপটিমিস্টিক আপডেট: মেসেজ সঙ্গে সঙ্গে থ্রেডে দেখাবে, রিলোড লাগবে না */
            var empty = messagesEl.querySelector('.text-center.text-muted');
            if (empty) messagesEl.innerHTML = '';
            var tempId = 'admin-msg-temp-' + Date.now();
            var tempHtml = '<div class="admin-chat-msg admin-chat-msg--admin admin-chat-msg--sending" id="' + tempId + '">';
            tempHtml += '<div class="admin-chat-msg-head"><span class="admin-chat-msg-name">{{ __("You") }}</span><span class="admin-chat-msg-time">...</span></div>';
            tempHtml += '<div class="admin-chat-msg-text">' + (msgText.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>')) + '</div></div>';
            messagesEl.insertAdjacentHTML('beforeend', tempHtml);
            scrollToBottom();
            textarea.value = '';
            if (attachInput) attachInput.value = '';
            if (fileListEl) fileListEl.innerHTML = '';

            var fd = new FormData(form);
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('replayTicket', '1');
            fd.append('message', msgText);
            fetch(form.action, { method: 'POST', body: fd, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var tempEl = document.getElementById(tempId);
                    if (tempEl) tempEl.remove();
                    if (data.success && data.message) {
                        messagesEl.insertAdjacentHTML('beforeend', buildMessageHtml(data.message, true));
                        scrollToBottom();
                        lastPollCount++;
                        lastPollId = data.message.id;
                    } else {
                        if (typeof notify === 'function') notify('error', (data && data.message) ? data.message : '{{ __("Send failed.") }}');
                        textarea.value = msgText;
                    }
                })
                .catch(function() {
                    var tempEl = document.getElementById(tempId);
                    if (tempEl) tempEl.remove();
                    if (typeof notify === 'function') notify('error', '{{ __("Send failed. Try again.") }}');
                    textarea.value = msgText;
                })
                .finally(function() {
                    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="las la-paper-plane"></i> @lang("Send")'; }
                });
        });
    }
    var selectAll = document.getElementById('adminSelectAllMsgs');
    var bulkForm = document.getElementById('adminBulkDeleteForm');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.admin-msg-bulk-cb').forEach(function(cb) { cb.checked = selectAll.checked; });
        });
    }
    if (bulkForm) {
        bulkForm.addEventListener('submit', function(e) {
            var action = (document.querySelector('button[type="submit"][name="action"]:focus') || bulkForm.querySelector('button[type="submit"][value="selected"]'));
            if (!action) action = e.submitter;
            var isSelected = action && action.value === 'selected';
            var isLast = action && action.value === 'last';
            if (isSelected) {
                var checked = bulkForm.querySelectorAll('.admin-msg-bulk-cb:checked');
                if (!checked.length) { e.preventDefault(); alert('{{ __("Please select at least one message.") }}'); return; }
                if (!confirm('{{ __("Delete selected messages?") }}')) { e.preventDefault(); return; }
            }
            if (isLast) {
                var lastVal = bulkForm.querySelector('select[name="delete_last"]').value;
                if (!lastVal) { e.preventDefault(); alert('{{ __("Please select how many to delete (50–500).") }}'); return; }
                if (!confirm('{{ __("Delete last N messages? This cannot be undone.") }}'.replace('N', lastVal))) { e.preventDefault(); return; }
            }
        });
    }
})();
</script>
@endpush
