@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <a href="{{ route('admin.orders.hub') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="las la-arrow-left"></i> @lang('Order Center')</a>
                <h5 class="mb-0 text-dark fw-bold">@lang('Order Automation')</h5>
                <p class="text-muted small mb-0 mt-1">@lang('Auto-confirm paid orders, progress workflow, cancel stale unpaid — runs on schedule or manually.')</p>
            </div>
            <form action="{{ route('admin.orders.automation.run') }}" method="post" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm"><i class="las la-play"></i> @lang('Run Now')</button>
            </form>
        </div>
    </div>

    @if($settings->last_run_at)
    <div class="alert alert-info border-0 shadow-sm py-2 small mb-4">
        <i class="las la-clock"></i> @lang('Last run'): {{ $settings->last_run_at->diffForHumans() }}
        · @lang('Schedule'): <code>php artisan orders:automation-run</code> (@lang('every') {{ $settings->run_interval_minutes }} @lang('min'))
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0 text-dark fw-semibold">@lang('Automation Rules')</h6></div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.automation.update') }}" method="post">
                        @csrf
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_enabled" value="1" id="oaEnabled" @checked($settings->is_enabled)>
                            <label class="form-check-label fw-semibold" for="oaEnabled">@lang('Master switch — enable automation')</label>
                        </div>
                        <hr>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="auto_confirm_paid" value="1" id="oaConfirm" @checked($settings->auto_confirm_paid)>
                            <label class="form-check-label" for="oaConfirm">@lang('Auto-confirm orders when payment is successful')</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="auto_processing_after_confirm" value="1" id="oaProcess" @checked($settings->auto_processing_after_confirm)>
                            <label class="form-check-label" for="oaProcess">@lang('Move confirmed orders to Processing after 1 hour')</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="auto_cancel_unpaid_enabled" value="1" id="oaCancel" @checked($settings->auto_cancel_unpaid_enabled)>
                            <label class="form-check-label" for="oaCancel">@lang('Auto-cancel unpaid pending orders after')</label>
                        </div>
                        <div class="mb-3 ms-4">
                            <input type="number" name="auto_cancel_unpaid_days" class="form-control form-control-sm" style="max-width:6rem" min="1" max="90" value="{{ $settings->auto_cancel_unpaid_days }}">
                            <small class="text-muted">@lang('days')</small>
                        </div>
                        <hr>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="notify_customer_on_auto" value="1" id="oaNotifyCust" @checked($settings->notify_customer_on_auto)>
                            <label class="form-check-label" for="oaNotifyCust">@lang('Notify customer on automated status changes')</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="notify_admin_new_order" value="1" id="oaNotifyAdmin" @checked($settings->notify_admin_new_order)>
                            <label class="form-check-label" for="oaNotifyAdmin">@lang('Notify admin on new orders (existing system)')</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="channel_import_enabled" value="1" id="oaChannel" @checked($settings->channel_import_enabled)>
                            <label class="form-check-label" for="oaChannel">@lang('Allow order channel webhook imports')</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">@lang('Cron interval (minutes)')</label>
                            <input type="number" name="run_interval_minutes" class="form-control" min="5" max="1440" value="{{ $settings->run_interval_minutes }}">
                        </div>
                        <hr>
                        <h6 class="text-dark fw-semibold small mb-2">@lang('SLA thresholds')</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="sla_alerts_enabled" value="1" id="oaSlaAlert" @checked($settings->sla_alerts_enabled ?? true)>
                            <label class="form-check-label" for="oaSlaAlert">@lang('Log SLA overdue alerts on each run')</label>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small">@lang('Pending max (hours)')</label>
                                <input type="number" name="sla_pending_hours" class="form-control form-control-sm" min="1" max="168" value="{{ $settings->sla_pending_hours ?? 24 }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">@lang('Fulfillment max (hours)')</label>
                                <input type="number" name="sla_fulfillment_hours" class="form-control form-control-sm" min="1" max="336" value="{{ $settings->sla_fulfillment_hours ?? 48 }}">
                            </div>
                        </div>
                        <a href="{{ route('admin.orders.fulfillment', ['tab' => 'sla']) }}" class="btn btn-sm btn-outline-danger w-100 mb-3">@lang('View SLA overdue queue')</a>
                        <button type="submit" class="btn btn-primary w-100">@lang('Save Settings')</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-dark fw-semibold">@lang('Activity Log')</h6>
                </div>
                <div class="table-responsive" style="max-height: 520px;">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="sticky-top bg-white">
                            <tr><th>@lang('Time')</th><th>@lang('Action')</th><th>@lang('Message')</th></tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td class="small text-nowrap">{{ $log->created_at->format('M d H:i') }}</td>
                                <td><span class="badge bg-label-secondary">{{ $log->action }}</span></td>
                                <td class="small">{{ $log->message }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">@lang('No automation activity yet.')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($logs->hasPages())
                <div class="card-footer">{{ paginateLinks($logs) }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
