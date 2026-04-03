@extends('admin.layouts.app')
@section('panel')
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body">
            <form action="{{ route('admin.abandoned-orders.settings.update') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Mark as abandoned after (inactivity)')</label>
                            <select name="abandoned_cart_inactivity_minutes" class="form-select">
                                <option value="15" {{ ($general->abandoned_cart_inactivity_minutes ?? 60) == 15 ? 'selected' : '' }}>15 @lang('minutes')</option>
                                <option value="30" {{ ($general->abandoned_cart_inactivity_minutes ?? 60) == 30 ? 'selected' : '' }}>30 @lang('minutes')</option>
                                <option value="60" {{ ($general->abandoned_cart_inactivity_minutes ?? 60) == 60 ? 'selected' : '' }}>1 @lang('hour')</option>
                                <option value="360" {{ ($general->abandoned_cart_inactivity_minutes ?? 60) == 360 ? 'selected' : '' }}>6 @lang('hours')</option>
                            </select>
                            <small class="text-muted">@lang('Carts with no activity for this period are considered abandoned.')</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Auto-cleanup (delete) carts older than')</label>
                            <input type="number" name="abandoned_cart_cleanup_days" class="form-control" value="{{ $general->abandoned_cart_cleanup_days ?? 30 }}" min="7" max="90">
                            <small class="text-muted">@lang('Days. Expired abandoned carts are removed by scheduled command.')</small>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="abandoned_cart_reminder_email" value="0">
                            <input type="checkbox" class="form-check-input" name="abandoned_cart_reminder_email" value="1" id="reminder_email" {{ ($general->abandoned_cart_reminder_email ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="reminder_email">@lang('Send email reminder when abandoned cart is detected')</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input type="hidden" name="abandoned_cart_reminder_sms" value="0">
                            <input type="checkbox" class="form-check-input" name="abandoned_cart_reminder_sms" value="1" id="reminder_sms" {{ ($general->abandoned_cart_reminder_sms ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="reminder_sms">@lang('Send SMS reminder (if mobile available)')</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn--primary">@lang('Save Settings')</button>
                    <a href="{{ route('admin.abandoned-orders.index') }}" class="btn btn-outline--secondary">@lang('Back to List')</a>
                </div>
            </form>
        </div>
    </div>
@endsection
