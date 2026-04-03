@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-3">
        <div class="col-12">
            <x-back route="{{ route('admin.users.detail', $user->id) }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-2 mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0"><i class="las la-paper-plane me-2"></i>@lang('Send Notification')</h5>
                </div>
                <form action="{{ route('admin.users.notification.single.send', $user->id) }}" method="POST" id="notificationForm">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">@lang('Subject')</label>
                            <input type="text" class="form-control" name="subject" id="subject" placeholder="@lang('Enter subject')" required maxlength="255"/>
                            <div class="form-text">@lang('Quick subjects:')</div>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject" data-subject="{{ __('Order Update') }}">@lang('Order Update')</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject" data-subject="{{ __('Payment Received') }}">@lang('Payment Received')</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject" data-subject="{{ __('Account Notice') }}">@lang('Account Notice')</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-subject" data-subject="{{ __('Support Reply') }}">@lang('Support Reply')</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">@lang('Message')</label>
                            <textarea name="message" id="message" rows="6" class="form-control" placeholder="@lang('Type your message here...')" required></textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="form-text">@lang('Sent via:') @if($general->en) <span class="badge bg-warning text-dark">@lang('Email')</span> @endif @if($general->sn) <span class="badge bg-info">@lang('SMS')</span> @endif</span>
                                <span class="text-muted small" id="charCount">0</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">@lang('Link (optional)')</label>
                            <input type="text" class="form-control" name="link" id="link" placeholder="@lang('Where should user go when they click this notification?')" maxlength="500"/>
                            <div class="form-text">@lang('Examples:')</div>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-link" data-link="{{ route('user.home') }}">@lang('Dashboard')</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-link" data-link="{{ route('user.order.index') }}">@lang('Orders')</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-link" data-link="{{ route('message.index') }}">@lang('Support')</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-link" data-link="{{ route('user.profile.setting') }}">@lang('Profile')</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-link" data-link="{{ route('user.notifications') }}">@lang('Notifications')</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top py-3">
                        <button type="submit" class="btn btn--primary"><i class="las la-paper-plane me-1"></i>@lang('Send Notification')</button>
                        <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-outline-secondary">@lang('Back to User')</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-2 mb-4">
                <div class="card-header bg-transparent border-bottom py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="las la-user me-1"></i>{{ $user->username }}</h6>
                    <a href="{{ route('admin.users.notification.log', $user->id) }}" class="btn btn-sm btn-outline--primary py-0">@lang('Full history')</a>
                </div>
                <div class="card-body py-2 small text-muted">
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        @if($user->email)<span><i class="las la-envelope me-1"></i>{{ $user->email }}</span>@endif
                        @if($user->mobile)<span><i class="las la-phone me-1"></i>{{ $user->mobile }}</span>@endif
                    </div>
                    <p class="mb-0" style="font-size: 0.8rem;">@lang('Notifications will be sent to the channels enabled in settings.')</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Conversation: user's messages & replies (from /message) --}}
    @if(isset($userTickets) && $userTickets->isNotEmpty())
    <div class="card border-0 shadow-sm rounded-2 mt-4">
        <div class="card-header bg-transparent border-bottom py-2">
            <h6 class="mb-0"><i class="las la-comments me-1"></i>@lang('Conversation')</h6>
        </div>
        <div class="card-body p-0">
            @foreach($userTickets as $ticket)
            <div class="border-bottom p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold small">#{{ $ticket->ticket }} — {{ __($ticket->subject) }}</span>
                    <a href="{{ route('admin.ticket.view', $ticket->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2">@lang('Open')</a>
                </div>
                <div class="message-thread small mb-2" style="max-height:200px;overflow-y:auto">
                    @foreach($ticket->supportMessage as $msg)
                    <div class="py-1 {{ $msg->admin_id ? 'text-end text-primary' : '' }}">
                        <span class="text-muted" style="font-size:0.7rem">{{ $msg->created_at->format('d M Y, h:i A') }}</span>
                        @if($msg->admin_id)<span class="badge bg-light text-dark ms-1">@lang('Admin')</span>@endif
                        <p class="mb-0 mt-0">{{ \Illuminate\Support\Str::limit(strip_tags($msg->message ?? ''), 150) }}</p>
                    </div>
                    @endforeach
                </div>
                @if($ticket->status != \App\Constants\Status::TICKET_CLOSE)
                <form action="{{ route('admin.ticket.reply', $ticket->id) }}" method="POST" class="mt-2">
                    @csrf
                    <input type="hidden" name="redirect_to" value="{{ route('admin.users.notification.single', $user->id) }}">
                    <div class="input-group input-group-sm">
                        <textarea name="message" class="form-control" rows="2" placeholder="@lang('Reply...')" required></textarea>
                        <button type="submit" class="btn btn--primary">@lang('Reply')</button>
                    </div>
                </form>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Message History --}}
    <div class="card border-0 shadow-sm rounded-2">
        <div class="card-header bg-transparent border-bottom py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="las la-history me-1"></i>@lang('Message History')</h6>
            <span class="badge bg-secondary">{{ $notificationHistory->count() }} @lang('recent')</span>
        </div>
        <div class="card-body p-0">
            @if($notificationHistory->isEmpty())
                <div class="text-center py-5 text-muted small">
                    <i class="las la-inbox" style="font-size: 2.5rem;"></i>
                    <p class="mb-0 mt-2">@lang('No notifications sent yet.')</p>
                </div>
            @else
                <div class="list-group list-group-flush notification-history-list">
                    @foreach($notificationHistory as $log)
                        <div class="list-group-item border-0 border-bottom py-3 px-3 d-flex justify-content-between align-items-start gap-2">
                            <div class="min-w-0 flex-grow-1">
                                <h6 class="mb-1 fw-semibold small">{{ __($log->subject) }}</h6>
                                <p class="mb-0 text-dark small" style="font-size: 0.85rem; line-height: 1.45;">{{ \Illuminate\Support\Str::limit(strip_tags($log->message ?? ''), 200) }}</p>
                                <div class="mt-1 d-flex flex-wrap gap-2 small align-items-center">
                                    <span class="text-muted">{{ $log->created_at->format('d M Y, h:i A') }}</span>
                                    @if($log->notification_type)
                                        <span class="badge bg-light text-dark">{{ $log->notification_type }}</span>
                                    @endif
                                    @if($log->click_url)
                                        <a href="{{ $log->click_url }}" target="_blank" rel="noopener" class="text-primary" style="font-size: 0.8rem;">@lang('Link')</a>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.users.notification.log.delete', [$user->id, $log->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('Delete this notification?')');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="@lang('Delete')"><i class="las la-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <span class="text--primary small">@lang('Send via') @if($general->en) <span class="badge bg-warning text-dark">@lang('Email')</span> @endif @if($general->sn) <span class="badge bg-info">@lang('SMS')</span> @endif</span>
@endpush

@push('script')
<script>
(function($) {
    "use strict";
    var $msg = $('#message');
    var $subj = $('#subject');
    function updateCharCount() {
        var len = ($msg.val() || '').length;
        $('#charCount').text(len + ' @lang('characters')');
    }
    $msg.on('input', updateCharCount);
    updateCharCount();

    $('.quick-subject').on('click', function() {
        var s = $(this).data('subject');
        $subj.val(s).focus();
    });
    $('.quick-link').on('click', function() {
        var link = $(this).data('link');
        $('#link').val(link).focus();
    });
})(jQuery);
</script>
@endpush
