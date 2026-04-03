@extends('admin.layouts.app')
@section('panel')
<form action="" method="post" id="maintenanceForm">
@csrf
<div class="row mb-none-30">
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header bg--primary">
                <h5 class="text-white mb-0"><i class="las la-cog me-2"></i>@lang('Maintenance Mode Control')</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Status')</label>
                            <div class="mt-2">
                                <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disabled')" @if(@$general->maintenance_mode) checked @endif name="status">
                            </div>
                            <small class="text-muted">@lang('Enable to show maintenance page to visitors')</small>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Current IP')</label>
                            <input type="text" class="form-control" value="{{ request()->ip() }}" readonly>
                            <small class="text-muted">@lang('Add this to whitelist to bypass maintenance')</small>
                        </div>
                    </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="form-group">
                                <label class="fw-bold">@lang('IP Whitelist')</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="ip_whitelist" id="ipWhitelist" value="{{ @$maintenance->data_values->ip_whitelist }}" placeholder="127.0.0.1, ::1, 192.168.1.1">
                                    <button type="button" class="btn btn--primary" id="addCurrentIp" title="@lang('Add current IP')"><i class="las la-plus"></i></button>
                                </div>
                                <small class="text-muted">@lang('Comma separated IPs - these can access site during maintenance')</small>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-palette me-2"></i>@lang('Page Content & Appearance')</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Page Title')</label>
                            <input type="text" class="form-control" name="title" value="{{ @$maintenance->data_values->title }}" placeholder="@lang('e.g. We\'ll Be Back Soon!')">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Short Description')</label>
                            <input type="text" class="form-control" name="short_description" value="{{ @$maintenance->data_values->short_description }}" placeholder="@lang('e.g. We are upgrading our system for better experience')">
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label class="fw-bold">@lang('Full Description')</label>
                    <textarea class="form-control nicEdit" rows="8" name="description" id="descriptionEditor">@php echo @$maintenance->data_values->description @endphp</textarea>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-clock me-2"></i>@lang('Countdown & Progress')</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Show Countdown')</label>
                            <select class="form-select" name="show_countdown">
                                <option value="1" @selected((@$maintenance->data_values->show_countdown ?? 1) == 1)>@lang('Yes')</option>
                                <option value="0" @selected(@$maintenance->data_values->show_countdown == 0)>@lang('No')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Estimated Completion Time')</label>
                            <input type="datetime-local" class="form-control" name="countdown_datetime" value="{{ @$maintenance->data_values->countdown_datetime }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Progress Percentage')</label>
                            <input type="number" class="form-control" name="progress_percentage" min="0" max="100" value="{{ @$maintenance->data_values->progress_percentage ?? 50 }}" placeholder="50">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Show Progress Bar')</label>
                            <select class="form-select" name="show_progress_bar">
                                <option value="1" @selected((@$maintenance->data_values->show_progress_bar ?? 1) != 0)>@lang('Yes')</option>
                                <option value="0" @selected(@$maintenance->data_values->show_progress_bar == 0)>@lang('No')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Estimated Duration Text')</label>
                            <input type="text" class="form-control" name="estimated_duration" value="{{ @$maintenance->data_values->estimated_duration }}" placeholder="@lang('e.g. Approximately 2-3 hours')">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-share-alt me-2"></i>@lang('Social Links & Contact')</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">Facebook</label>
                            <input type="url" class="form-control" name="social_facebook" value="{{ @$maintenance->data_values->social_facebook }}" placeholder="https://facebook.com/yourpage">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">Twitter/X</label>
                            <input type="url" class="form-control" name="social_twitter" value="{{ @$maintenance->data_values->social_twitter }}" placeholder="https://twitter.com/yourpage">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">Instagram</label>
                            <input type="url" class="form-control" name="social_instagram" value="{{ @$maintenance->data_values->social_instagram }}" placeholder="https://instagram.com/yourpage">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">LinkedIn</label>
                            <input type="url" class="form-control" name="social_linkedin" value="{{ @$maintenance->data_values->social_linkedin }}" placeholder="https://linkedin.com/company/yourpage">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Contact Email')</label>
                            <input type="email" class="form-control" name="contact_email" value="{{ @$maintenance->data_values->contact_email }}" placeholder="support@example.com">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Contact Phone')</label>
                            <input type="text" class="form-control" name="contact_phone" value="{{ @$maintenance->data_values->contact_phone }}" placeholder="+880 1XXX-XXXXXX">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-bell me-2"></i>@lang('Email Notification')</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Allow Email Signup')</label>
                            <select class="form-select" name="allow_email_signup">
                                <option value="1" @selected((@$maintenance->data_values->allow_email_signup ?? 1) != 0)>@lang('Yes')</option>
                                <option value="0" @selected(@$maintenance->data_values->allow_email_signup == 0)>@lang('No')</option>
                            </select>
                            <small class="text-muted">@lang('Let visitors submit email for updates')</small>
                        </div>
                    </div>
                    <div class="col-md-8 col-sm-6 mb-3">
                        <div class="form-group">
                            <label class="fw-bold">@lang('Email Signup Message')</label>
                            <input type="text" class="form-control" name="email_signup_message" value="{{ @$maintenance->data_values->email_signup_message ?? __('Get notified when we\'re back!') }}" placeholder="@lang('e.g. Get notified when we\'re back!')">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <button type="submit" class="btn btn--primary w-100 h-45 btn-lg"><i class="las la-save me-2"></i>@lang('Save All Settings')</button>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

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
