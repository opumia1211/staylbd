@extends('admin.layouts.app')
@section('panel')
    <div class="admin-notify-all">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="mb-0 fw-bold d-flex align-items-center">
                            <i class="las la-paper-plane me-2 admin-notify-all__header-icon"></i>
                            @lang('Send Notification to Verified Users')
                        </h5>
                        <p class="text-muted small mb-0 mt-1">@lang('Notification will be sent via Email') @if($general->en)<span class="badge bg-warning text-dark">@lang('Email')</span>@endif @if($general->sn)<span class="badge bg-info">@lang('SMS')</span>@endif</p>
                    </div>
                    <form class="notify-form admin-notify-all__form" action="#" method="post">
                        @csrf
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label admin-notify-all__label">@lang('Subject') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control admin-notify-all__input" name="subject" placeholder="@lang('Email subject')" required maxlength="255">
                                </div>
                                <div class="col-12">
                                    <label class="form-label admin-notify-all__label">@lang('Message') <span class="text-danger">*</span></label>
                                    <textarea class="form-control nicEdit admin-notify-all__textarea" name="message" rows="8" placeholder="@lang('Type your message here...')"></textarea>
                                </div>
                                <div class="col-12">
                                    <hr class="my-2">
                                    <p class="small text-muted mb-3">@lang('Batch settings')</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label admin-notify-all__label">@lang('Start From') <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control admin-notify-all__input" name="start_form" min="0" placeholder="0" value="0" required>
                                    <small class="text-muted">@lang('User index to start from')</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label admin-notify-all__label">@lang('Per Batch') <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control admin-notify-all__input" name="batch" min="1" placeholder="@lang('How many user')" required>
                                        <span class="input-group-text">@lang('User')</span>
                                    </div>
                                    <small class="text-muted">@lang('Users per batch')</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label admin-notify-all__label">@lang('Cooling Period') <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control admin-notify-all__input" name="cooling_time" min="1" placeholder="@lang('Seconds')" required>
                                        <span class="input-group-text">@lang('Seconds')</span>
                                    </div>
                                    <small class="text-muted">@lang('Wait between batches')</small>
                                </div>
                                <div class="col-12">
                                    <p class="small mb-0 text-muted">@lang('Total verified users') : <strong>{{ $users }}</strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top py-3">
                            <button type="submit" class="btn btn--primary btn-lg w-100 w-md-auto admin-notify-all__submit">
                                <i class="las la-paper-plane me-1"></i> @lang('Send Notification')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="notificationSending" data-bs-backdrop="static" tabindex="-1" aria-labelledby="notificationSendingLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content admin-notify-all__modal">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="notificationSendingLabel">@lang('Notification Sending')</h5>
                </div>
                <div class="modal-body">
                    <p class="text-danger text-center fw-semibold admin-notify-all__modal-warning">@lang('Don\'t close or refresh the window till finish')</p>
                    <div class="mail-wrapper text-center my-4">
                        <div class="sendingIcon mail-icon world-icon"><i class="las la-globe"></i></div>
                        <div class="coolingIcon mail-icon world-icon"><i class="fas fa-spinner fa-spin"></i></div>
                        <div class="sendingIcon mailsent">
                            <div class="envelope">
                                <i class="line line1"></i>
                                <i class="line line2"></i>
                                <i class="line line3"></i>
                                <i class="icon fa fa-envelope"></i>
                            </div>
                        </div>
                        <div class="sendingIcon mail-icon"><i class="las la-envelope-open-text"></i></div>
                    </div>
                    <div class="mt-3">
                        <div class="progress admin-notify-all__progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                        <p class="mt-2 mb-0">@lang('Email sent') <span class="sent">0</span> @lang('users out of') {{ $users }} @lang('users')</p>
                        <div class="finalStatistics d-none mt-3">
                            <div class="mail-icon text-success fw-bold text-center">
                                <i class="fas fa-check-circle"></i> @lang('Done')
                            </div>
                            <ul class="list-group list-group-flush mt-2">
                                <li class="list-group-item d-flex justify-content-between align-items-center">@lang('Start From') <span class="fw-bold startFrom">0</span></li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">@lang('Ended at') <span class="fw-bold sent">0</span></li>
                            </ul>
                        </div>
                        <h4 class="text-primary remainingTime d-none text-center mt-3"></h4>
                        <div class="mt-3">
                            <p class="sentStatistics text-center mb-2">@lang('Email sent') <span class="startFrom">0</span> @lang('to') <span class="sent">-</span> @lang('users')</p>
                            <p class="text-center sentStatistics">
                                <button type="button" class="btn btn-danger stopSending"><i class="la la-power-off me-1"></i>@lang('Stop')</button>
                            </p>
                            <div class="modelCloseButton d-none text-end mt-3">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">@lang('Close')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <span class="text-muted small">
        @lang('Notification will send via')
        @if($general->en)<span class="badge bg-warning text-dark">@lang('Email')</span>@endif
        @if($general->sn)<span class="badge bg-info">@lang('SMS')</span>@endif
    </span>
@endpush

@push('style')
{{--
  Send Notification (All) - Flexible CSS. Override without breaking: set on .admin-notify-all:
  --notify-label-color, --notify-label-weight, --notify-input-radius, --notify-spacing
--}}
<style>
.admin-notify-all { --notify-label-color: #212529; --notify-label-weight: 600; --notify-input-radius: 0.375rem; --notify-spacing: 0.75rem; }
.admin-notify-all__header-icon { opacity: 0.85; }
.admin-notify-all__label { color: var(--notify-label-color); font-weight: var(--notify-label-weight); font-size: 0.9375rem; margin-bottom: 0.35rem; }
.admin-notify-all__input { border-radius: var(--notify-input-radius); }
.admin-notify-all__textarea { border-radius: var(--notify-input-radius); min-height: 8rem; }
.admin-notify-all__submit { min-height: 2.75rem; }
.admin-notify-all__modal .modal-header { padding: var(--notify-spacing) 1rem; }
.admin-notify-all__modal-warning { font-size: clamp(0.9rem, 2vw, 1rem); }
.admin-notify-all__progress { height: 0.5rem; border-radius: var(--notify-input-radius); }
.admin-notify-all .coolingIcon { margin: 0 auto; }
@media (min-width: 768px) {
    .admin-notify-all__submit { width: auto; }
}
</style>
@endpush

@push('script')
<script>
(function($){
    "use strict";
    var subject = null, message = null, start = null, perBatch = null, sendingStatus = true, coolingTime = null, _token = null;

    $('.notify-form').on('submit', function(e) {
        subject = $(this).find('[name=subject]').val();
        message = $(this).find('.nicEdit-main').length ? $(this).find('.nicEdit-main').html() : $(this).find('[name=message]').val();
        start = parseInt($(this).find('[name=start_form]').val(), 10) || 0;
        perBatch = parseInt($(this).find('[name=batch]').val(), 10);
        coolingTime = parseInt($(this).find('[name=cooling_time]').val(), 10);
        _token = $(this).find('[name=_token]').val();
        if ({{ $users }} <= 0) {
            notify('error', 'Users not found');
            return false;
        }
        if (!coolingTime || coolingTime < 1) {
            notify('error', "{{ __('Cooling period must be greater then zero') }}");
            return false;
        }
        if (!perBatch || perBatch < 1) {
            notify('error', "{{ __('Per batch must be greater then zero') }}");
            return false;
        }
        e.preventDefault();
        sendingStatus = true;
        $('.progress-bar').css('width', '0%').text('0%');
        $('.sent').text('-');
        $('.stopSending,.dontCloseWarning,.sentStatistics').removeClass('d-none');
        $('.finalStatistics,.modelCloseButton').addClass('d-none');
        $('#notificationSending').modal('show');
        $('.startFrom').text(start);
        postMail();
    });

    function postMail() {
        if (!sendingStatus) {
            $('.remainingTime,.coolingIcon,.dontCloseWarning,.sentStatistics').addClass('d-none');
            $('.finalStatistics,.modelCloseButton').removeClass('d-none');
            return;
        }
        $('.remainingTime').text('Cooling...');
        $('.remainingTime,.coolingIcon').addClass('d-none');
        $('.sendingIcon').removeClass('d-none');
        $.post("{{ route('admin.users.notification.all.send') }}", {
            subject: subject,
            _token: _token,
            start: start,
            batch: perBatch,
            message: message
        }, function(response) {
            $('.remainingTime').removeClass('d-none');
            $('.sendingIcon').addClass('d-none');
            $('.coolingIcon').removeClass('d-none');
            if (response.error) {
                (response.error || []).forEach(function(err) { notify('error', err); });
            } else {
                start += parseInt(response.total_sent, 10);
                $('.sent').text(start);
                if (!parseInt(response.total_sent, 10)) {
                    sendingStatus = false;
                    postMail();
                    return;
                }
                $('.sentStatistics').removeClass('d-none');
                setTimeout(function() {
                    clearInterval(interval);
                    postMail();
                }, coolingTime * 1000);
                var counter = coolingTime - 1, interval = setInterval(function() {
                    $('.remainingTime').text("Reloading after " + counter + " seconds");
                    counter--;
                    if (counter <= 0) clearInterval(interval);
                }, 1000);
            }
        });
    }

    $('.stopSending').on('click', function() {
        sendingStatus = false;
        notify('info', "{{ __('Notification sending will stop after this batch.') }}");
        $('.sentStatistics').addClass('d-none');
    });
})(jQuery);
</script>
@endpush
