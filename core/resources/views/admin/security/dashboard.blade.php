@extends('admin.layouts.app')

@section('panel')

  <div class="row">
    {{-- ── Security Hero Card ──────────────── --}}
    <div class="col-md-12 col-xxl-4 mb-6">
      <div class="card h-100">
        <div class="d-flex align-items-end row">
          <div class="col-7">
            <div class="card-body">
              <h5 class="card-title mb-1 text-nowrap">@lang('Security Dashboard') 🗝️</h5>
              <p class="card-subtitle text-nowrap mb-3 small">@lang('System integrity check')</p>

              <h4 class="card-title text-{{ ($stats['critical_24h'] ?? 0) > 0 ? 'danger' : 'primary' }} mb-1">
                {{ ($stats['critical_24h'] ?? 0) > 0 ? ($stats['critical_24h']) . ' ' . __('Alerts') : __('System Secure') }}
              </h4>
              <p class="mb-4 small">
                {{ ($stats['failed_logins_24h'] ?? 0) }} @lang('failed login attempts today.')
              </p>

              <div class="d-flex gap-2">
                <form action="{{ route('admin.security.run.scan') }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-primary">@lang('Run Scan')</button>
                </form>
                <a href="{{ route('admin.profile') }}" class="btn btn-sm btn-label-secondary">@lang('My 2FA')</a>
              </div>
            </div>
          </div>
          <div class="col-5">
            <div class="card-body pb-0 px-0 px-md-6 text-end">
              <img src="{{ asset('assets/img/illustrations/prize-light.png') }}" height="140" alt="Security Badge" />
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Statistics Card ── --}}
    <div class="col-xxl-8 mb-6">
      <div class="card h-100">
        <div class="card-body row g-4 p-0">
          <div class="col-md-6 card-separator">
            <div class="p-6">
              <div class="card-title d-flex align-items-start justify-content-between mb-4">
                <h5 class="mb-0">@lang('Login Failures')</h5>
                <small class="text-body-secondary">@lang('Last 24h')</small>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <div class="mt-auto">
                  <h3 class="mb-2">{{ $stats['failed_logins_24h'] ?? 0 }}</h3>
                  <span class="badge bg-label-{{ ($stats['failed_logins_24h'] ?? 0) > 0 ? 'danger' : 'success' }} mb-0">
                    <i class="bx {{ ($stats['failed_logins_24h'] ?? 0) > 0 ? 'bx-error' : 'bx-check-circle' }} me-1"></i>
                    {{ ($stats['failed_logins_24h'] ?? 0) > 0 ? __('Risk Detected') : __('No Risk') }}
                  </span>
                </div>
                <div class="avatar avatar-md bg-label-danger rounded p-2">
                  <i class="bx bx-user-voice fs-3"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="p-6">
              <div class="card-title d-flex align-items-start justify-content-between mb-4">
                <h5 class="mb-0">@lang('Admin Sessions')</h5>
                <small class="text-body-secondary">@lang('Live')</small>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <div class="mt-auto">
                  <h3 class="mb-2">{{ count($active_admin_sessions ?? []) }}</h3>
                  <span class="badge bg-label-info mb-0">
                    <i class="bx bx-globe me-1"></i> @lang('Active Now')
                  </span>
                </div>
                <div class="avatar avatar-md bg-label-info rounded p-2">
                  <i class="bx bx-desktop fs-3"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── 4 Mini Cards ── --}}
    <div class="col-lg-12 col-xxl-4">
      <div class="row">
        {{-- Critical --}}
        <div class="col-xxl-6 col-md-3 col-sm-6 col-12 mb-6">
          <div class="card h-100">
            <div class="card-body">
              <div class="card-title d-flex align-items-center justify-content-between mb-4">
                <div class="avatar flex-shrink-0">
                  <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-bolt-circle"></i></span>
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm btn-icon border-0" type="button" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="javascript:void(0);">@lang('Refresh')</a></li>
                  </ul>
                </div>
              </div>
              <p class="mb-1 text-body-secondary small">@lang('Critical')</p>
              <h4 class="card-title mb-3">{{ $stats['critical_24h'] ?? 0 }}</h4>
              <small class="text-danger fw-medium"><i class="bx bx-trending-up me-1"></i>24h</small>
            </div>
          </div>
        </div>
        {{-- Lockouts --}}
        <div class="col-xxl-6 col-md-3 col-sm-6 col-12 mb-6">
          <div class="card h-100">
            <div class="card-body">
              <div class="avatar flex-shrink-0 mb-4">
                <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-lock-alt"></i></span>
              </div>
              <p class="mb-1 text-body-secondary small">@lang('Lockouts')</p>
              <h4 class="card-title mb-3">{{ $stats['lockout_count'] ?? 0 }}</h4>
              <small class="text-warning fw-medium"><i class="bx bx-timer me-1"></i>@lang('Active')</small>
            </div>
          </div>
        </div>
        {{-- 2FA --}}
        <div class="col-xxl-6 col-md-3 col-sm-6 col-12 mb-6">
          <div class="card h-100">
            <div class="card-body">
              <div class="avatar flex-shrink-0 mb-4">
                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-shield-alt"></i></span>
              </div>
              <p class="mb-1 text-body-secondary small">@lang('Active 2FA')</p>
              <h4 class="card-title mb-3">{{ $stats['admin_with_2fa'] ?? 0 }}</h4>
              <small class="text-success fw-medium"><i class="bx bx-check-shield me-1"></i>@lang('Verified')</small>
            </div>
          </div>
        </div>
        {{-- Rate Limit --}}
        <div class="col-xxl-6 col-md-3 col-sm-6 col-12 mb-6">
          <div class="card h-100">
            <div class="card-body">
              <div class="avatar flex-shrink-0 mb-4">
                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-traffic-cone"></i></span>
              </div>
              <p class="mb-1 text-body-secondary small">@lang('Rate Limits')</p>
              <h4 class="card-title mb-3">{{ $rate_limit_triggers ?? 0 }}</h4>
              <small class="text-info fw-medium"><i class="bx bx-info-circle me-1"></i>@lang('Triggers')</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Security Settings Card ── --}}
    <div class="col-md-12 col-xxl-8 mb-6">
      <div class="card h-100">
        <div class="row row-bordered g-0">
          <div class="col-md-7">
            <div class="card-header border-bottom-0">
              <h5 class="card-title mb-1">@lang('Configuration Status')</h5>
              <p class="card-subtitle small">@lang('Core security toggle and environment flags')</p>
            </div>
            <div class="card-body pt-0">
              <div class="table-responsive text-nowrap">
                <table class="table table-borderless table-sm">
                  <tbody>
                    <tr>
                      <td class="ps-0 py-3"><span class="text-body-secondary">Environment (APP_ENV)</span></td>
                      <td class="pe-0 py-3 text-end">
                        <span class="badge bg-label-{{ ($security_config['app_env'] ?? '') === 'production' ? 'success' : 'warning' }}">
                          {{ ucfirst($security_config['app_env'] ?? 'N/A') }}
                        </span>
                      </td>
                    </tr>
                    <tr>
                      <td class="ps-0 py-3"><span class="text-body-secondary">Debug (APP_DEBUG)</span></td>
                      <td class="pe-0 py-3 text-end">
                        <span class="badge bg-label-{{ ($security_config['app_debug'] ?? false) ? 'danger' : 'success' }}">
                          {{ ($security_config['app_debug'] ?? false) ? 'ENABLED' : 'DISABLED' }}
                        </span>
                      </td>
                    </tr>
                    <tr>
                      <td class="ps-0 py-3"><span class="text-body-secondary">@lang('IP Whitelisting')</span></td>
                      <td class="pe-0 py-3 text-end">
                        <div class="form-check form-switch d-inline-block security-toggle" data-key="ip_whitelist_enabled">
                          <input class="form-check-input" type="checkbox" {{ ($security_config['ip_whitelist'] ?? false) ? 'checked' : '' }}>
                        </div>
                      </td>
                    </tr>
                    <tr>
                       <td class="ps-0 py-3"><span class="text-body-secondary">@lang('Login Captcha')</span></td>
                       <td class="pe-0 py-3 text-end">
                         <div class="form-check form-switch d-inline-block security-toggle" data-key="admin_login_captcha">
                           <input class="form-check-input" type="checkbox" {{ ($security_config['admin_captcha'] ?? true) ? 'checked' : '' }}>
                         </div>
                       </td>
                    </tr>
                    <tr>
                        <td class="ps-0 py-3"><span class="text-body-secondary">@lang('Global Admin 2FA')</span></td>
                        <td class="pe-0 py-3 text-end">
                          <div class="form-check form-switch d-inline-block security-toggle" data-key="admin_two_factor_enabled">
                            <input class="form-check-input" type="checkbox" {{ ($security_config['admin_two_factor'] ?? true) ? 'checked' : '' }}>
                          </div>
                        </td>
                     </tr>
                    <tr>
                      <td class="ps-0 py-3">
                         <span class="text-body-secondary">@lang('Admin Access URL')</span>
                      </td>
                      <td class="pe-0 py-3 text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                           <code class="bg-label-primary px-2 py-1 rounded small">/{{ $security_config['admin_prefix'] ?? 'admin' }}</code>
                           <button class="btn btn-xs btn-label-primary p-1" data-bs-toggle="modal" data-bs-target="#adminPrefixModal"><i class="bx bx-edit-alt"></i></button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="col-md-5">
            <div class="card-body p-6 text-center d-flex flex-column align-items-center justify-content-center h-100 border-start">
               <div class="avatar avatar-xl mb-4">
                 <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-shield-quarter icon-32px"></i></span>
               </div>
               <h5 class="mb-2">@lang('Security Health')</h5>
               @php 
                $score = 100;
                if(config('app.debug')) $score -= 30;
                if(config('app.env') !== 'production') $score -= 20;
                if(!request()->secure()) $score -= 20;
               @endphp
               <div class="display-6 fw-bold text-primary mb-2">{{ $score }}%</div>
               <p class="text-body-secondary small mb-4">@lang('Configuration rating')</p>
               <button class="btn btn-sm btn-primary w-100">@lang('Download Report')</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Recommendations ── --}}
    <div class="col-md-6 col-lg-4 mb-6">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1">@lang('Action Required')</h5>
            <p class="card-subtitle small">@lang('Security tasks')</p>
          </div>
        </div>
        <div class="card-body">
          <ul class="p-0 m-0">
            @forelse($recommendations ?? [] as $r)
            <li class="d-flex align-items-center mb-5">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-{{ $r['level'] ?? 'secondary' }}">
                  <i class="bx bx-{{ ($r['level'] ?? '') === 'danger' ? 'error' : 'info-circle' }}"></i>
                </span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0 small fw-medium">{{ $r['msg'] ?? '' }}</h6>
                </div>
              </div>
            </li>
            @empty
            <li class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3"><span class="avatar-initial rounded bg-label-success"><i class="bx bx-check-circle"></i></span></div>
              <h6 class="mb-0 fw-medium">@lang('System is optimized.')</h6>
            </li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    {{-- ── Threats List ── --}}
    <div class="col-md-6 col-lg-4 mb-6">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">@lang('Threat Activity')</h5>
        </div>
        <div class="card-body pt-4">
          <ul class="p-0 m-0">
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3"><span class="avatar-initial rounded bg-label-danger"><i class="bx bx-scan"></i></span></div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="d-block text-body-secondary">@lang('Suspicious')</small>
                  <h6 class="fw-normal mb-0">@lang('Malicious IPs')</h6>
                </div>
                <div class="user-progress"><h6 class="fw-bold mb-0">{{ count($suspicious_ips ?? []) }}</h6></div>
              </div>
            </li>
            <li class="d-flex align-items-center mb-6">
              <div class="avatar flex-shrink-0 me-3"><span class="avatar-initial rounded bg-label-warning"><i class="bx bx-lock-open"></i></span></div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="d-block text-body-secondary">@lang('Blocked')</small>
                  <h6 class="fw-normal mb-0">@lang('Active Bans')</h6>
                </div>
                <div class="user-progress"><h6 class="fw-bold mb-0">{{ $stats['lockout_count'] ?? 0 }}</h6></div>
              </div>
            </li>
            <li class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3"><span class="avatar-initial rounded bg-label-success"><i class="bx bx-list-check"></i></span></div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="d-block text-body-secondary">@lang('Safe')</small>
                  <h6 class="fw-normal mb-0">@lang('Whitelist')</h6>
                </div>
                <div class="user-progress"><h6 class="fw-bold mb-0">{{ $security_config['whitelist_count'] ?? 0 }}</h6></div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    {{-- ── Recent Logins ── --}}
    <div class="col-md-12 col-lg-4 mb-6">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
          <h5 class="card-title m-0 me-2">@lang('Security Timeline')</h5>
        </div>
        <div class="card-body pt-2">
          <ul class="timeline mb-0">
            @forelse($failed_logins->take(4) as $e)
            <li class="timeline-item timeline-item-transparent {{ $loop->last ? 'border-0 pb-0' : '' }}">
              <span class="timeline-point timeline-point-danger"></span>
              <div class="timeline-event">
                <div class="timeline-header mb-1">
                  <h6 class="mb-0 small fw-bold">@lang('Failed Login')</h6>
                  <small class="text-body-secondary">{{ $e->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-1 small">IP: <code>{{ $e->ip_address }}</code></p>
                <small class="text-body-secondary">{{ $e->payload['username'] ?? 'Unknown' }}</small>
              </div>
            </li>
            @empty
            <li class="timeline-item"><p class="small">@lang('No incidents recorded.')</p></li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

  </div>

  {{-- ── Full Table Card ── --}}
  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="card-title mb-0">@lang('Security Audit Trail')</h5>
      <button class="btn btn-sm btn-label-secondary">@lang('View All Logs')</button>
    </div>
    <div class="table-responsive">
      <table class="table table-hover table-sm">
        <thead class="table-light">
          <tr>
            <th class="ps-4">@lang('Configuration Key')</th>
            <th>@lang('Performed By')</th>
            <th>@lang('Value Transition')</th>
            <th class="pe-4 text-end">@lang('Timestamp')</th>
          </tr>
        </thead>
        <tbody>
          @foreach($audit_logs->take(10) as $log)
          <tr>
            <td class="ps-4 fw-medium">{{ $log->setting_key }}</td>
            <td>
              <div class="d-flex align-items-center">
                <div class="avatar avatar-xs me-2"><span class="avatar-initial rounded-circle bg-label-primary fs-tiny">{{ substr($log->admin->name ?? 'S', 0, 1) }}</span></div>
                <span>{{ $log->admin->name ?? 'System' }}</span>
              </div>
            </td>
            <td>
              <div class="d-flex align-items-center gap-1">
                 <span class="text-muted small">{{ $log->old_value ?? 'NULL' }}</span>
                 <i class="bx bx-right-arrow-alt text-muted"></i>
                 <span class="text-success small fw-bold">{{ $log->new_value ?? 'NULL' }}</span>
              </div>
            </td>
            <td class="pe-4 text-end text-body-secondary small">{{ $log->created_at->format('M d, Y H:i') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>


  {{-- Change Prefix Modal --}}
  <div class="modal fade" id="adminPrefixModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form action="{{ route('admin.security.update.admin.prefix') }}" method="POST" class="modal-content">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">@lang('Security — Change Admin URL')</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning d-flex" role="alert">
            <span class="badge badge-center rounded-pill bg-warning border-0 me-3"><i class="bx bx-error"></i></span>
            <div class="d-flex flex-column">
              <h6 class="alert-heading mb-1">@lang('Critical Action')</h6>
              <span>@lang('This will immediately change your administrative access URL. You will be logged out.')</span>
            </div>
          </div>
          <div class="mb-3">
             <label class="form-label">@lang('New Access Prefix')</label>
             <div class="input-group input-group-merge">
                <span class="input-group-text">/</span>
                <input type="text" name="prefix" class="form-control" value="{{ $security_config['admin_prefix'] }}" required>
             </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
          <button type="submit" class="btn btn-primary">@lang('Apply New Prefix')</button>
        </div>
      </form>
    </div>
  </div>

@endsection

@push('script')
<script>
$(function(){
  $('.security-toggle input').on('change', function(){
    let $el = $(this);
    let key = $el.closest('.security-toggle').data('key');
    let val = $el.prop('checked') ? 1 : 0;
    
    $.post("{{ route('admin.security.toggle') }}", {
      _token: "{{ csrf_token() }}",
      key: key,
      value: val
    });
  });
});
</script>
@endpush