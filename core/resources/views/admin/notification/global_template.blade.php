@extends('admin.layouts.app')
@section('panel')
@php
    $hasNotificationLogo = \Schema::hasColumn('general_settings', 'notification_logo');
    $notificationLogoUrl = $notificationLogoUrl ?? null;
    $hasGlobalShortcodes = isset($general->global_shortcodes) && is_object($general->global_shortcodes) && count((array)$general->global_shortcodes) > 0;
@endphp

{{-- inline style moved to critical-admin.css --}}


<div class="notif-global">
    <div class="row">
        {{-- Branding / Notification image (changeable per project name) --}}
        @if($hasNotificationLogo)
        <div class="col-12 notif-section">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">@lang('Branding / Notification Image')</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">@lang('This image can be used in emails and notifications. Change it when this project runs under a different brand or name.')</p>
                    <div class="d-flex flex-wrap align-items-start gap-4">
                        <div class="notif-logo-wrap">
                            @if($notificationLogoUrl)
                                <img src="{{ $notificationLogoUrl }}?v={{ time() }}" alt="@lang('Notification logo')" width="240" height="120">
                            @else
                                <div class="p-4 text-center text-muted small">@lang('No image set')</div>
                            @endif
                        </div>
                        <div class="text-muted small">@lang('Use the form below to upload or remove the image, then click Save Global Template.')</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Short codes reference – compact table --}}
        <div class="col-12 notif-section">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">@lang('Short Codes')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive table-responsive--sm">
                        <table class="table align-items-center table--light mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('Short Code')</th>
                                    <th>@lang('Description')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                <tr>
                                    <td><span class="short-codes">@{{fullname}}</span></td>
                                    <td>@lang('Full Name of User')</td>
                                </tr>
                                <tr>
                                    <td><span class="short-codes">@{{username}}</span></td>
                                    <td>@lang('Username of User')</td>
                                </tr>
                                <tr>
                                    <td><span class="short-codes">@{{message}}</span></td>
                                    <td>@lang('Message')</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if($hasGlobalShortcodes)
        <div class="col-12 notif-section">
            <h6 class="mb-2">@lang('Global Short Codes')</h6>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive table-responsive--sm">
                        <table class="table align-items-center table--light mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('Short Code')</th>
                                    <th>@lang('Description')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @foreach($general->global_shortcodes as $shortCode => $codeDetails)
                                @php
                                    $shortCodeDisplay = '@{{ ' . e($shortCode) . ' ' . chr(125) . chr(125);
                                @endphp
                                <tr>
                                    <td><span class="short-codes">{{ $shortCodeDisplay }}</span></td>
                                    <td>{{ __($codeDetails) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Main form: Email + SMS – one card, grid on desktop --}}
        <div class="col-12 notif-section">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">@lang('Global Template')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.setting.notification.global.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="notif-grid-2">
                            <div>
                                <h6 class="mb-3">@lang('Email')</h6>
                                <div class="form-group mb-3">
                                    <label class="form-label">@lang('Email Sent From')</label>
                                    <input type="text" class="form-control" placeholder="@lang('Email address')" name="email_from" value="{{ $general->email_from }}" required maxlength="40">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">@lang('Email Body')</label>
                                    <textarea name="email_template" rows="8" class="form-control nicEdit" placeholder="@lang('Your email template')">{{ $general->email_template }}</textarea>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-3">@lang('SMS')</h6>
                                <div class="form-group mb-3">
                                    <label class="form-label">@lang('SMS Sent From')</label>
                                    <input type="text" class="form-control" placeholder="@lang('SMS Sent From')" name="sms_from" value="{{ $general->sms_from }}" required maxlength="40">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">@lang('SMS Body')</label>
                                    <textarea name="sms_body" rows="6" class="form-control" placeholder="@lang('SMS Body')" required>{{ $general->sms_body }}</textarea>
                                </div>
                            </div>
                        </div>
                        @if($hasNotificationLogo)
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Notification / Branding Image')</label>
                            <input type="file" name="notification_logo" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                            @if($notificationLogoUrl ?? null)
                            <div class="form-check mt-2">
                                <input type="checkbox" name="remove_notification_logo" id="removeNotifLogoMain" value="1" class="form-check-input">
                                <label for="removeNotifLogoMain" class="form-check-label small">@lang('Remove current branding image')</label>
                            </div>
                            @endif
                        </div>
                        @endif
                        <button type="submit" class="btn btn--primary">@lang('Save Global Template')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
