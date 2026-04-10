@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        {{-- Quick Actions (single place per action) --}}
        <div class="card mb-4">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    @if(($active_lockouts ?? collect())->isNotEmpty())
                    <form action="{{ route('admin.security.clear.lockouts') }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Clear all admin lockouts?') }}');">
                        @csrf
                        <button type="submit" class="btn btn--warning btn-sm"><i class="las la-unlock me-1"></i>@lang('Clear Lockouts')</button>
                    </form>
                    @endif
                    <form action="{{ route('admin.security.run.scan') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn--primary btn-sm"><i class="las la-search me-1"></i>@lang('Run Security Scan')</button>
                    </form>
                    <a href="{{ route('admin.profile') }}" class="btn btn-outline--primary btn-sm"><i class="las la-user-cog me-1"></i>@lang('Profile & 2FA')</a>
                    <a href="{{ route('admin.setting.system.configuration') }}" class="btn btn-outline--info btn-sm"><i class="las la-cog me-1"></i>@lang('System Config')</a>
                </div>
            </div>
        </div>

        {{-- Summary Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <i class="las la-user-slash text-danger" style="font-size: 1.75rem;"></i>
                        <h4 class="mb-0 mt-1">{{ $stats['failed_logins_24h'] ?? 0 }}</h4>
                        <small class="text-muted">@lang('Failed Logins')</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <i class="las la-shield-alt text-warning" style="font-size: 1.75rem;"></i>
                        <h4 class="mb-0 mt-1">{{ $stats['payment_failures_24h'] ?? 0 }}</h4>
                        <small class="text-muted">@lang('Payment Failures')</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <i class="las la-exclamation-triangle text-danger" style="font-size: 1.75rem;"></i>
                        <h4 class="mb-0 mt-1">{{ $stats['critical_24h'] ?? 0 }}</h4>
                        <small class="text-muted">@lang('Critical Events')</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <i class="las la-users text-primary" style="font-size: 1.75rem;"></i>
                        <h4 class="mb-0 mt-1">{{ $stats['admin_with_2fa'] ?? 0 }}/{{ $stats['admin_count'] ?? 0 }}</h4>
                        <small class="text-muted">@lang('Admins with 2FA')</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <i class="las la-lock text-warning" style="font-size: 1.75rem;"></i>
                        <h4 class="mb-0 mt-1">{{ $stats['lockout_count'] ?? 0 }}</h4>
                        <small class="text-muted">@lang('Lockouts')</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <i class="las la-bolt text-info" style="font-size: 1.75rem;"></i>
                        <h4 class="mb-0 mt-1">{{ $rate_limit_triggers ?? 0 }}</h4>
                        <small class="text-muted">@lang('Rate Limited')</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status (read-only) + Editable toggles + Recommendations --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="las la-info-circle me-2"></i>@lang('Current Status')</h6>
                        <a href="{{ route('admin.setting.system.configuration') }}" class="btn btn-sm btn-outline--primary">@lang('System Config')</a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-hover mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 42%;">APP_ENV</td>
                                    <td><span class="badge bg-{{ ($security_config['app_env'] ?? '') === 'production' ? 'success' : 'warning' }}">{{ $security_config['app_env'] ?? 'N/A' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">APP_DEBUG</td>
                                    <td><span class="badge bg-{{ ($security_config['app_debug'] ?? false) ? 'danger' : 'success' }}">{{ ($security_config['app_debug'] ?? false) ? __('On') : __('Off') }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">SESSION_ENCRYPT</td>
                                    <td><span class="badge bg-{{ ($security_config['session_encrypt'] ?? false) ? 'success' : 'warning' }}">{{ ($security_config['session_encrypt'] ?? false) ? __('On') : __('Off') }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">HTTPS</td>
                                    <td><span class="badge bg-{{ ($security_config['https'] ?? false) ? 'success' : 'warning' }}">{{ ($security_config['https'] ?? false) ? __('Yes') : __('No') }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Admin URL</td>
                                    <td>
                                        <code>{{ $security_config['admin_prefix'] ?? 'admin' }}</code>
                                        @if($security_config['admin_prefix_ok'] ?? false)<span class="badge bg-success ms-1">@lang('Custom')</span>@endif
                                        <button type="button" class="btn btn-sm btn-outline--primary ms-1" data-bs-toggle="modal" data-bs-target="#adminPrefixModal">@lang('Change')</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">IP Whitelist</td>
                                    <td>
                                        <div class="form-check form-switch mb-0 security-toggle d-inline-block" data-key="ip_whitelist_enabled">
                                            <input class="form-check-input" type="checkbox" {{ ($security_config['ip_whitelist'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label small">{{ ($security_config['ip_whitelist'] ?? false) ? __('On') : __('Off') }}@if($security_config['ip_whitelist'] ?? false) ({{ $security_config['whitelist_count'] ?? 0 }})@endif</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Login Captcha</td>
                                    <td>
                                        <div class="form-check form-switch mb-0 security-toggle d-inline-block" data-key="admin_login_captcha">
                                            <input class="form-check-input" type="checkbox" {{ ($security_config['admin_captcha'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label small">{{ ($security_config['admin_captcha'] ?? true) ? __('On') : __('Off') }}</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">@lang('Login 2FA')</td>
                                    <td>
                                        <div class="form-check form-switch mb-0 security-toggle d-inline-block" data-key="admin_two_factor_enabled">
                                            <input class="form-check-input" type="checkbox" {{ ($security_config['admin_two_factor'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label small">{{ ($security_config['admin_two_factor'] ?? true) ? __('On') : __('Off') }}</label>
                                        </div>
                                        <div class="small text-muted mt-1">@lang('When off, password-only login (no OTP / setup). For development; enable for production.')</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="px-3 py-2 bg-light small text-muted border-top">
                            @lang('APP_ENV, APP_DEBUG, SESSION: edit .env. SSL & password:') <a href="{{ route('admin.setting.system.configuration') }}">@lang('System Config')</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header py-2">
                        <h6 class="mb-0"><i class="las la-lightbulb me-2"></i>@lang('Recommendations')</h6>
                    </div>
                    <div class="card-body p-0" style="max-height: 320px; overflow-y: auto;">
                        @forelse($recommendations ?? [] as $r)
                        <div class="border-bottom px-3 py-2 small">
                            <span class="badge bg-{{ $r['level'] ?? 'secondary' }} me-2">{{ ucfirst($r['level'] ?? '') }}</span>
                            {{ $r['msg'] ?? '' }}
                        </div>
                        @empty
                        <div class="px-3 py-4 text-muted small">@lang('No recommendations')</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Last 10 Security Changes (Audit Log) --}}
        @if(($audit_logs ?? collect())->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="las la-history me-2"></i>@lang('Last 10 Security Changes')</h6>
            </div>
            <div class="card-body p-0" style="max-height: 280px; overflow-y: auto;">
                @foreach($audit_logs as $log)
                <div class="border-bottom px-3 py-2 small">
                    <strong>{{ $log->setting_key }}</strong>
                    <span class="text-muted">{{ $log->old_value ?? '—' }} → {{ $log->new_value ?? '—' }}</span>
                    @if($log->admin)<span class="text-muted"> by {{ $log->admin->name ?? $log->admin->username }}</span>@endif
                    <span class="text-muted">{{ $log->created_at?->diffForHumans() }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Security Scan Output --}}
        @if(session('scan_output'))
        <div class="card mb-4 border-info">
            <div class="card-header bg-info py-2">
                <h6 class="text-white mb-0"><i class="las la-search me-2"></i>@lang('Security Scan Result')</h6>
            </div>
            <div class="card-body">
                <pre class="mb-0 small" style="white-space: pre-wrap; max-height: 200px; overflow-y: auto;">{{ session('scan_output') }}</pre>
            </div>
        </div>
        @endif

        {{-- Critical Events 24h --}}
        @if(($critical_events_24h ?? collect())->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header bg-danger py-2">
                <h6 class="text-white mb-0"><i class="las la-exclamation-circle me-2"></i>@lang('Critical Events') (24h)</h6>
            </div>
            <div class="card-body p-0" style="max-height: 180px; overflow-y: auto;">
                @foreach($critical_events_24h as $e)
                <div class="border-bottom px-3 py-2 small">
                    <span class="badge bg-{{ $e->severity === 'critical' ? 'danger' : 'warning' }}">{{ $e->severity }}</span>
                    <span class="text-muted">{{ $e->created_at->format('H:i d/m') }}</span>
                    {{ $e->event_type }} - {{ $e->ip_address ?? '-' }}
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Activity (24h) – one section --}}
        <div class="row mt-2">
            <div class="col-12">
                <h6 class="mb-3"><i class="las la-stream me-2"></i>@lang('Activity (24h)')</h6>
            </div>
        </div>
</div>

<div class="row g-3">
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-header py-2"><h6 class="mb-0 small">@lang('Failed Logins')</h6></div>
            <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                @forelse($failed_logins ?? [] as $e)
                    <div class="border-bottom px-3 py-2 small">
                        <span class="text-muted">{{ $e->created_at->format('H:i d/m') }}</span> {{ $e->ip_address ?? '-' }} @if(!empty($e->payload['username'])) <code>{{ $e->payload['username'] }}</code> @endif
                    </div>
                @empty
                    <div class="px-3 py-4 text-muted">@lang('No failed logins in last 24h')</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-header py-2"><h6 class="mb-0 small">@lang('2FA Failures')</h6></div>
            <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                @forelse($two_fa_failures ?? [] as $e)
                    <div class="border-bottom px-3 py-2 small">{{ $e->created_at->format('H:i d/m') }} {{ $e->ip_address ?? '-' }}</div>
                @empty
                    <div class="px-3 py-4 text-muted small">@lang('None')</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-header py-2"><h6 class="mb-0 small">@lang('Payment Failures')</h6></div>
            <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                @forelse($payment_signature_failures ?? [] as $e)
                    <div class="border-bottom px-3 py-2 small">{{ $e->created_at->format('H:i d/m') }} {{ $e->gateway ?? '-' }}</div>
                @empty
                    <div class="px-3 py-4 text-muted small">@lang('None')</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-header py-2"><h6 class="mb-0 small">@lang('Suspicious IPs')</h6></div>
            <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                @forelse($suspicious_ips ?? [] as $e)
                    <div class="border-bottom px-3 py-2 small">{{ $e->ip_address }} <span class="badge bg-danger">{{ $e->cnt }}</span></div>
                @empty
                    <div class="px-3 py-4 text-muted small">@lang('None')</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-header py-2"><h6 class="mb-0 small">@lang('Sessions')</h6></div>
            <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                @forelse($active_admin_sessions ?? [] as $s)
                    <div class="border-bottom px-3 py-2 small">{{ $s->admin->name ?? $s->admin->username ?? '?' }} {{ $s->last_activity_at?->diffForHumans() }}</div>
                @empty
                    <div class="px-3 py-4 text-muted small">@lang('None')</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-header py-2"><h6 class="mb-0 small">@lang('Lockouts')</h6></div>
            <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                @forelse($active_lockouts ?? [] as $l)
                    <div class="border-bottom px-3 py-2 small">{{ $l->ip_address }}</div>
                @empty
                    <div class="px-3 py-4 text-muted small">@lang('None')</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Admin Prefix Change Modal --}}
<div class="modal fade" id="adminPrefixModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Change Admin Prefix')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">@lang('Changing prefix will update .env and clear caches. You may need to re-login. Use only letters, numbers, underscore, hyphen.')</p>
                <div class="mb-3">
                    <label class="form-label">@lang('New prefix')</label>
                    <input type="text" id="newAdminPrefix" class="form-control" value="{{ $security_config['admin_prefix'] ?? 'admin' }}" placeholder="e.g. sajaladminopu" pattern="[a-zA-Z0-9_-]+" maxlength="60">
                    <span class="text-danger small" id="adminPrefixError"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                <button type="button" class="btn btn--primary" id="saveAdminPrefixBtn">@lang('Update & Redirect')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
(function() {
    var csrf = '{{ csrf_token() }}';
    var toggleUrl = '{{ route("admin.security.toggle") }}';
    var prefixUrl = '{{ route("admin.security.update.admin.prefix") }}';

    document.querySelectorAll('.security-toggle').forEach(function(wrap) {
        var input = wrap.querySelector('input[type="checkbox"]');
        var key = wrap.getAttribute('data-key');
        if (!input || !key) return;
        input.addEventListener('change', function() {
            var value = this.checked;
            var orig = this.checked;
            fetch(toggleUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ key: key, value: value, _token: csrf })
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.success) {
                    var label = wrap.querySelector('.form-check-label');
                    if (label) label.textContent = value ? '{{ __("On") }}' : '{{ __("Off") }}';
                } else {
                    input.checked = !value;
                }
            }).catch(function() { input.checked = !value; });
        });
    });

    var savePrefixBtn = document.getElementById('saveAdminPrefixBtn');
    var newPrefixInput = document.getElementById('newAdminPrefix');
    var prefixError = document.getElementById('adminPrefixError');
    if (savePrefixBtn && newPrefixInput) {
        savePrefixBtn.addEventListener('click', function() {
            var prefix = (newPrefixInput.value || '').trim();
            prefixError.textContent = '';
            if (!/^[a-zA-Z0-9_-]+$/.test(prefix)) {
                prefixError.textContent = '{{ __("Use only letters, numbers, underscore, hyphen.") }}';
                return;
            }
            if (prefix.toLowerCase() === 'admin') {
                prefixError.textContent = '{{ __("Use a custom prefix, not \"admin\".") }}';
                return;
            }
            savePrefixBtn.disabled = true;
            savePrefixBtn.textContent = '...';
            fetch(prefixUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ prefix: prefix, _token: csrf })
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data.success && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    prefixError.textContent = data.message || '{{ __("Update failed.") }}';
                    savePrefixBtn.disabled = false;
                    savePrefixBtn.textContent = '{{ __("Update & Redirect") }}';
                }
            }).catch(function() {
                prefixError.textContent = '{{ __("Request failed.") }}';
                savePrefixBtn.disabled = false;
                savePrefixBtn.textContent = '{{ __("Update & Redirect") }}';
            });
        });
    }
})();
</script>
@endpush
