@extends('admin.layouts.app')

@section('panel')
<div class="container-xxl flex-grow-1 container-p-y p-0">
    <form action="" method="post" id="maintenanceForm">
        @csrf
        <div class="row g-4">
            {{-- ── 1. Strategic Control Center ── --}}
            <div class="col-12">
                <div class="card border-0 shadow-none bg-label-danger overflow-hidden">
                    <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-danger text-white p-2 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                <i class="las la-tools fs-3"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-danger">@lang('Maintenance Mode Architecture')</h5>
                                <p class="text-muted small mb-0">@lang('Safely disconnect public access for system upgrades, security patches, or architecture changes.')</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="form-check form-switch mb-0 bg-white px-3 py-1 rounded-pill border shadow-sm">
                                <input class="form-check-input ms-0 me-2" type="checkbox" id="maintenanceStatusSwitch" name="status" @if(@$general->maintenance_mode) checked @endif>
                                <label class="form-check-label small fw-bold text-dark" for="maintenanceStatusSwitch">@lang('Live Status')</label>
                            </div>
                            <button type="submit" class="btn btn-danger btn-sm shadow-sm px-4">
                                <i class="las la-save me-1"></i> @lang('Synchronize System')
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── 2. Access Intelligence (Whitelist) ── --}}
            <div class="col-xl-4 col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header border-bottom bg-white py-3 px-4">
                        <h6 class="mb-0 fw-bold"><i class="las la-shield-alt me-2 text-primary"></i>@lang('Bypass Intelligence')</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold small">@lang('Detected Node IP')</label>
                            <div class="input-group input-group-merge border rounded">
                                <span class="input-group-text bg-lighter border-0"><i class="las la-network-wired"></i></span>
                                <input type="text" class="form-control border-0 bg-lighter" value="{{ request()->ip() }}" readonly>
                            </div>
                            <div class="form-text tiny">@lang('This is your current connection ID.')</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small">@lang('Secure IP Whitelist')</label>
                            <div class="input-group input-group-merge mb-2">
                                <span class="input-group-text"><i class="las la-lock"></i></span>
                                <textarea class="form-control" name="ip_whitelist" id="ipWhitelist" rows="4" placeholder="127.0.0.1, 192.168.1.1">{{ @$maintenance->data_values->ip_whitelist }}</textarea>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" id="addCurrentIp">
                                <i class="las la-plus-circle me-1"></i> @lang('Authorize My Current IP')
                            </button>
                            <div class="bg-label-info p-2 rounded small">
                                <i class="las la-info-circle me-1"></i> @lang('Authorized nodes can bypass maintenance to verify changes live.')
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── 3. Brand Continuity & Message ── --}}
            <div class="col-xl-8 col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom bg-white py-3 px-4">
                        <h6 class="mb-0 fw-bold"><i class="las la-palette me-2 text-primary"></i>@lang('Public Narrative & Visuals')</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">@lang('Primary Headline')</label>
                                <input type="text" class="form-control" name="title" value="{{ @$maintenance->data_values->title }}" placeholder="@lang('e.g. System Evolution in Progress')">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">@lang('Sub-Headline')</label>
                                <input type="text" class="form-control" name="short_description" value="{{ @$maintenance->data_values->short_description }}" placeholder="@lang('e.g. We are optimizing our clusters for you.')">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small">@lang('Detailed Communication (HTML Support)')</label>
                            <div class="border rounded bg-light p-1">
                                <textarea class="form-control nicEdit" rows="8" name="description" id="descriptionEditor">@php echo @$maintenance->data_values->description @endphp</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── 4. Temporal Management (Countdown) ── --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom bg-white py-3 px-4">
                        <h6 class="mb-0 fw-bold"><i class="las la-hourglass-half me-2 text-primary"></i>@lang('Temporal Tracking')</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">@lang('Live Countdown')</label>
                                <select class="form-select" name="show_countdown">
                                    <option value="1" @selected((@$maintenance->data_values->show_countdown ?? 1) == 1)>@lang('Display Timer')</option>
                                    <option value="0" @selected(@$maintenance->data_values->show_countdown == 0)>@lang('Hide Timer')</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">@lang('Target Restoration Time')</label>
                                <input type="datetime-local" class="form-control" name="countdown_datetime" value="{{ @$maintenance->data_values->countdown_datetime }}">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-bold small">@lang('Status Progress Bar')</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="progress_percentage" min="0" max="100" value="{{ @$maintenance->data_values->progress_percentage ?? 50 }}" placeholder="50">
                                    <span class="input-group-text">%</span>
                                    <select class="form-select" name="show_progress_bar" style="max-width: 120px;">
                                        <option value="1" @selected((@$maintenance->data_values->show_progress_bar ?? 1) != 0)>@lang('Show')</option>
                                        <option value="0" @selected(@$maintenance->data_values->show_progress_bar == 0)>@lang('Hide')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── 5. Social & Contact Synergy ── --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom bg-white py-3 px-4">
                        <h6 class="mb-0 fw-bold"><i class="las la-share-alt me-2 text-primary"></i>@lang('Community Synergy')</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Facebook</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="lab la-facebook-f"></i></span>
                                    <input type="url" class="form-control" name="social_facebook" value="{{ @$maintenance->data_values->social_facebook }}" placeholder="https://facebook.com/staylbd">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Instagram</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="lab la-instagram"></i></span>
                                    <input type="url" class="form-control" name="social_instagram" value="{{ @$maintenance->data_values->social_instagram }}" placeholder="https://instagram.com/staylbd">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">@lang('Emergency Email')</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="las la-envelope"></i></span>
                                    <input type="email" class="form-control" name="contact_email" value="{{ @$maintenance->data_values->contact_email }}" placeholder="ops@staylbd.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">@lang('Support Hotline')</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="las la-phone"></i></span>
                                    <input type="text" class="form-control" name="contact_phone" value="{{ @$maintenance->data_values->contact_phone }}" placeholder="+880 1XXX XXXXXX">
                                </div>
                            </div>
                        </div>

                        <div class="bg-label-secondary p-3 rounded border-start border-4 border-secondary">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small mb-0">@lang('Visitor Notifications')</label>
                                    <select class="form-select form-select-sm mt-1" name="allow_email_signup">
                                        <option value="1" @selected((@$maintenance->data_values->allow_email_signup ?? 1) != 0)>@lang('Allow Email Signup')</option>
                                        <option value="0" @selected(@$maintenance->data_values->allow_email_signup == 0)>@lang('Disable Signup')</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-bold small mb-0">@lang('Notification Prompt')</label>
                                    <input type="text" class="form-control form-control-sm mt-1" name="email_signup_message" value="{{ @$maintenance->data_values->email_signup_message ?? __('Get notified when we\'re back!') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('style')
<style>
    .bg-label-danger { background-color: #ffe0db !important; color: #ff3e1d !important; }
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .bg-label-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; }
    .bg-lighter { background-color: #f8fafc !important; }
    .tiny { font-size: 0.75rem !important; }
</style>
@endpush

@push('script')
<script>
(function() {
    var addBtn = document.getElementById('addCurrentIp');
    var ipInput = document.getElementById('ipWhitelist');
    var currentIp = '{{ request()->ip() }}';
    if (addBtn && ipInput) {
        addBtn.addEventListener('click', function() {
            var val = (ipInput.value || '').trim();
            var ips = val ? val.split(',').map(function(s) { return s.trim(); }).filter(Boolean) : [];
            if (ips.indexOf(currentIp) === -1) {
                ips.push(currentIp);
                ipInput.value = ips.join(', ');
            }
        });
    }
})();
</script>
@endpush

