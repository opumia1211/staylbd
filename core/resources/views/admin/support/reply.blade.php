@extends('admin.layouts.app')

@section('panel')
<div class="support-reply-panel">
    <div class="row">
        <div class="col-lg-12">
            {{-- Subject tabs (view by user only) --}}
            @if(!empty($byUser) && isset($ticket->user_id))
                @php
                    $subjects = ['Live Chat Message', 'General Inquiry', 'Report a Problem', 'Order Support'];
                    $currentSubject = $subjectFilter ?? null;
                @endphp
                <div class="reply-subject-bar mb-3">
                    <span class="reply-subject-label">@lang('Subject'):</span>
                    <div class="reply-subject-pills">
                        <a href="{{ route('admin.ticket.view.user', $ticket->user_id) }}" class="reply-pill {{ !$currentSubject ? 'active' : '' }}">@lang('All')</a>
                        @foreach($subjects as $sub)
                            <a href="{{ route('admin.ticket.view.user', $ticket->user_id) }}?subject={{ urlencode($sub) }}" class="reply-pill {{ $currentSubject === $sub ? 'active' : '' }}">{{ __($sub) }}</a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Header: compact one row --}}
            <div class="reply-header-card card b-radius--10 mb-3">
                <div class="card-body py-2 px-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            @if(isset($hasChannelColumn) && $hasChannelColumn)
                                @php $ch = $ticket->channel ?? 'web'; @endphp
                                @if($ch == 'web')<span class="badge badge--info" title="@lang('Web')"><i class="las la-globe"></i></span>
                                @elseif($ch == 'telegram')<span class="badge badge--primary"><i class="fab fa-telegram"></i></span>
                                @elseif($ch == 'whatsapp')<span class="badge badge--success"><i class="fab fa-whatsapp"></i></span>
                                @elseif($ch == 'email')<span class="badge badge--warning"><i class="las la-envelope"></i></span>
                                @else<span class="badge badge--dark"><i class="las la-link"></i></span>
                                @endif
                            @endif
                            @php echo $ticket->statusBadge; @endphp
                            @if(!empty($byUser))
                                <span class="fw-semibold">@lang('Conversation')</span>
                                <span class="text-muted small">— {{ $ticket->user->fullname ?? $ticket->user->username ?? $ticket->name }}@if($ticket->user_id) <a href="{{ route('admin.users.detail', $ticket->user_id) }}" class="text--base">&#64;{{ $ticket->user->username ?? '' }}</a>@endif</span>
                            @else
                                <span class="fw-semibold">#{{ $ticket->ticket }}</span>
                                <span class="text-muted small">{{ $ticket->subject }} — {{ $ticket->name }}@if($ticket->user_id) <a href="{{ route('admin.users.detail', $ticket->user_id) }}" class="text--base">&#64;{{ $ticket->user->username ?? '' }}</a>@endif</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($ticket->status != Status::TICKET_CLOSE)
                                <button class="btn btn--danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#DelModal"><i class="las la-times-circle"></i> @lang('Close')</button>
                            @endif
                            <a href="{{ route('admin.ticket.index') }}" class="btn btn--outline-secondary btn-sm"><i class="las la-arrow-left"></i> @lang('Back')</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chat thread + Reply area --}}
            <div class="card b-radius--10 admin-chat-card">
                <div class="admin-chat-thread" id="adminChatThread">
                    <div class="admin-chat-messages" id="adminChatMessages">
                        @php
                            $tz = config('app.timezone', 'UTC');
                            $lastDate = '';
                        @endphp
                        @forelse($messages as $message)
                            @php
                                $dt = $message->created_at->timezone($tz);
                                $dateLabel = $dt->isToday() ? __('Today') : ($dt->isYesterday() ? __('Yesterday') : $dt->format('d/m/Y'));
                            @endphp
                            @if($dateLabel !== $lastDate)
                                @php $lastDate = $dateLabel; @endphp
                                <div class="admin-chat-date-divider">
                                    <span>{{ $dateLabel }}</span>
                                </div>
                            @endif
                            <div class="admin-chat-msg {{ $message->admin_id ? 'admin-chat-msg--admin' : 'admin-chat-msg--user' }}" data-msg-id="{{ $message->id }}">
                                <div class="admin-chat-msg-head d-flex align-items-center justify-content-between flex-wrap gap-1">
                                    <span class="admin-chat-msg-name">{{ $message->admin_id ? ($message->admin->name ?? 'Staff') : ($message->ticket->name ?? $ticket->name) }}</span>
                                    <span class="admin-chat-msg-time" title="{{ $dt->format('Y-m-d H:i:s') }}">{{ $dt->format('d M Y, g:i A') }}</span>
                                </div>
                                <div class="admin-chat-msg-text">{{ nl2br(e($message->message)) }}</div>
                                @if($message->attachments->count() > 0)
                                    <div class="admin-chat-msg-att mt-2 d-flex flex-wrap align-items-center gap-2">
                                        @foreach($message->attachments as $k => $att)
                                            @php
                                                $attExt = strtolower(pathinfo($att->attachment ?? '', PATHINFO_EXTENSION));
                                                $isImage = in_array($attExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $imgUrl = $isImage ? route('admin.ticket.download', encrypt($att->id)) . '?inline=1' : null;
                                            @endphp
                                            @if($isImage && $imgUrl)
                                                <a href="{{ $imgUrl }}" target="_blank" class="admin-chat-att-thumb" title="@lang('View full size')">
                                                    <img src="{{ $imgUrl }}" alt="@lang('Photo') {{ $k + 1 }}" loading="lazy">
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.ticket.download', encrypt($att->id)) }}{{ $isImage ? '?inline=1' : '' }}" target="_blank" class="btn btn-sm btn--outline-primary me-1 mb-1" title="{{ $isImage ? __('View image') : __('Download') }}">
                                                <i class="las {{ $isImage ? 'la-image' : 'la-paperclip' }}"></i> @lang('Attachment') {{ $k + 1 }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="admin-chat-msg-actions mt-1 d-flex align-items-center gap-2">
                                    <label class="mb-0 small d-flex align-items-center gap-1">
                                        <input type="checkbox" class="admin-msg-bulk-cb form-check-input" name="message_ids[]" value="{{ $message->id }}">
                                        <span>@lang('Select')</span>
                                    </label>
                                    <button type="button" class="btn btn-sm btn--danger confirmationBtn" data-question="@lang('Are you sure to delete this message?')" data-action="{{ route('admin.ticket.delete', $message->id) }}">
                                        <i class="las la-trash"></i> @lang('Delete')
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="las la-comment-slash font-size--48px opacity-50"></i>
                                <p class="mt-2 mb-0">@lang('No messages yet.')</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Bulk delete: Select messages + Delete last 50/100/200/300/400/500 --}}
                @if($messages->count() > 0)
                    <div class="admin-chat-bulk-bar px-3 py-2 border-top">
                        @if(!empty($byUser))
                            <p class="small text-muted mb-2">@lang('All messages from this user are shown above. Replies go to the same conversation.')</p>
                        @endif
                        <form action="{{ route('admin.ticket.bulk-delete') }}" method="post" class="admin-bulk-form d-flex flex-wrap align-items-center gap-3" id="adminBulkDeleteForm">
                            @csrf
                            @if(!empty($byUser) && $ticket->user_id)
                                <input type="hidden" name="user_id" value="{{ $ticket->user_id }}">
                            @else
                                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                            @endif
                            <label class="mb-0 small d-flex align-items-center gap-1">
                                <input type="checkbox" class="form-check-input" id="adminSelectAllMsgs"> @lang('Select all')
                            </label>
                            <button type="submit" class="btn btn-sm btn--danger" name="action" value="selected" id="adminBulkDeleteSelected"><i class="las la-trash"></i> @lang('Delete selected')</button>
                            <span class="text-muted small">|</span>
                            <label class="mb-0 small">@lang('Delete last'):</label>
                            <select name="delete_last" class="form-select form-select-sm admin-bulk-select">
                                <option value="">—</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                                <option value="300">300</option>
                                <option value="400">400</option>
                                <option value="500">500</option>
                                <option value="1000">1000</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn--danger" name="action" value="last"><i class="las la-trash"></i></button>
                        </form>
                    </div>
                @endif

                @if($ticket->status != Status::TICKET_CLOSE)
                    <div class="admin-chat-reply-bar">
                        <form action="{{ route('admin.ticket.reply', $ticket->id) }}" method="post" enctype="multipart/form-data" class="admin-chat-reply-form" id="adminChatReplyForm">
                            @csrf
                            <div class="admin-chat-reply-inner">
                                <div class="admin-chat-reply-input-wrap">
                                    <textarea name="message" id="adminReplyMessage" class="form-control" rows="2" placeholder="@lang('Type your reply...')" required></textarea>
                                    <div class="admin-chat-reply-actions">
                                        <label class="btn btn-sm btn--outline-secondary mb-0 me-2 cursor-pointer">
                                            <i class="las la-paperclip"></i> @lang('Attach')
                                            <input type="file" name="attachments[]" class="d-none" id="adminReplyAttach" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" multiple>
                                        </label>
                                        <button type="submit" class="btn btn--primary btn-sm" name="replayTicket" value="1">
                                            <i class="las la-paper-plane"></i> @lang('Send')
                                        </button>
                                    </div>
                                </div>
                                <div id="adminReplyFileList" class="small text-muted mt-1"></div>
                                <p class="small text-muted mb-0 mt-1">@lang('Allowed'): jpg, jpeg, png, pdf, doc, docx. @lang('Max 5 files.')</p>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
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
<style>
.support-reply-panel { --reply-radius: 10px; }
.reply-subject-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; padding: 8px 12px; border-radius: var(--reply-radius); background: rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.06); }
.reply-subject-label { font-size: 0.8rem; font-weight: 600; color: #6c757d; }
.reply-subject-pills { display: flex; flex-wrap: wrap; gap: 6px; }
.reply-pill { padding: 5px 10px; border-radius: 6px; font-size: 0.8rem; text-decoration: none; color: #5a6c7d; background: #fff; border: 1px solid rgba(0,0,0,0.08); transition: all .15s; }
.reply-pill:hover { background: rgba(0,0,0,0.05); color: #1e3a5f; }
.reply-pill.active { background: var(--base); color: #fff; border-color: var(--base); }
.reply-header-card .card-body { padding: 10px 14px; }
.admin-bulk-form .admin-bulk-select { width: 90px; }
.admin-chat-bulk-bar { background: rgba(0,0,0,0.02); }
.admin-chat-card { display: flex; flex-direction: column; min-height: 400px; max-height: 65vh; overflow: hidden; border-radius: var(--reply-radius); }
.admin-chat-thread { flex: 1; overflow-y: auto; padding: 14px; background: #f5f5f0; }
.admin-chat-messages { display: flex; flex-direction: column; gap: 10px; }
.admin-chat-date-divider { display: flex; align-items: center; gap: 10px; margin: 14px 0 8px; }
.admin-chat-date-divider::before, .admin-chat-date-divider::after { content: ''; flex: 1; height: 1px; background: rgba(0,0,0,0.1); }
.admin-chat-date-divider span { font-size: 0.7rem; color: #6c757d; padding: 3px 10px; border-radius: 6px; background: rgba(0,0,0,0.06); }
.admin-chat-msg { max-width: 85%; padding: 10px 14px; border-radius: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.admin-chat-msg--user { background: #fff; margin-right: auto; border-top-left-radius: 4px; border: 1px solid rgba(0,0,0,0.06); }
.admin-chat-msg--admin { background: #e8f5e9; margin-left: auto; border-top-right-radius: 4px; border: 1px solid rgba(0,0,0,0.05); }
.admin-chat-msg-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.admin-chat-msg-name { font-weight: 600; font-size: 0.8rem; color: #1e3a5f; }
.admin-chat-msg-time { font-size: 0.7rem; color: #6c757d; }
.admin-chat-msg-text { font-size: 0.9rem; color: #212529; word-break: break-word; white-space: pre-wrap; line-height: 1.45; }
.admin-chat-msg-actions { opacity: 0.9; }
.admin-chat-msg--sending { opacity: 0.85; }
.admin-chat-reply-bar { flex-shrink: 0; padding: 12px 14px; background: #fff; border-top: 1px solid rgba(0,0,0,0.08); }
.admin-chat-reply-inner { max-width: 100%; }
.admin-chat-reply-input-wrap { display: flex; flex-direction: column; gap: 8px; }
.admin-chat-reply-input-wrap textarea { resize: none; min-height: 56px; max-height: 120px; border-radius: 10px; border: 1px solid rgba(0,0,0,0.1); }
.admin-chat-reply-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
.admin-chat-att-thumb { display: inline-block; width: 56px; height: 56px; border-radius: 8px; overflow: hidden; border: 1px solid rgba(0,0,0,0.08); flex-shrink: 0; }
.admin-chat-att-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.cursor-pointer { cursor: pointer; }
</style>
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
