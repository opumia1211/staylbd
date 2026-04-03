@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0">@lang('Last 50 activities') — @lang('Auto-refresh every 15 seconds')</h5>
            <div>
                <a href="{{ route('admin.report.activity.all') }}" class="btn btn--primary btn-sm">@lang('Full Timeline')</a>
                <span class="badge badge--info ms-2" id="lastRefresh">@lang('Refreshed')</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('Action')</th>
                                    <th>@lang('Description')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('IP')</th>
                                    <th>@lang('Device')</th>
                                    <th>@lang('At')</th>
                                </tr>
                            </thead>
                            <tbody id="liveMonitorBody">
                                @forelse($logs as $log)
                                <tr class="{{ in_array($log->action_type, ['login_failed', 'payment_failure']) ? 'table-warning' : '' }}">
                                    <td><span class="badge badge--primary">{{ $log->action_type }}</span></td>
                                    <td><span class="text-break">{{ \Illuminate\Support\Str::limit($log->description ?? '—', 50) }}</span></td>
                                    <td>
                                        @if($log->user_id)
                                        <a href="{{ route('admin.users.detail', $log->user_id) }}">{{ $log->user->fullname ?? '—' }}</a>
                                        @else
                                        <span class="text-muted">@lang('Guest')</span>
                                        @endif
                                    </td>
                                    <td><span class="font-monospace small">{{ $log->ip_address ?? '—' }}</span></td>
                                    <td>{{ $log->device ?? '—' }}</td>
                                    <td>
                                        <span class="d-block">{{ showDateTime($log->created_at) }}</span>
                                        <span class="small text-muted">{{ diffForHumans($log->created_at) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="6">@lang('No activity yet.')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
(function(){
    "use strict";
    var interval = 15000;
    function refresh() {
        fetch('{{ route("admin.report.activity.live.data") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(function(r) { return r.json(); }).then(function(data) {
            if (data.html) {
                document.getElementById('liveMonitorBody').innerHTML = data.html;
            }
            document.getElementById('lastRefresh').textContent = new Date().toLocaleTimeString();
        }).catch(function() {});
    }
    setInterval(refresh, interval);
})();
</script>
@endpush
