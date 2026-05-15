@extends('admin.layouts.app')

@section('panel')
<div class="container-xxl flex-grow-1 container-p-y p-0">
    <div class="row g-4">
        {{-- ── Header & Navigation ── --}}
        <div class="col-12">
            <div class="card border-0 shadow-none bg-label-primary overflow-hidden">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary text-white p-2 rounded-circle shadow-sm" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <i class="las la-cookie-bite fs-3"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-primary">@lang('GDPR Cookie & Site Intelligence')</h5>
                            <small class="text-muted">@lang('Manage user consent, data privacy policies, and strategic site-wide announcements.')</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('cookie.policy') }}" target="_blank" rel="noopener" class="btn btn-white btn-sm border shadow-sm">
                            <i class="las la-external-link-alt me-1"></i> @lang('View Live Policy')
                        </a>
                        <button type="button" class="btn btn-primary btn-sm shadow-sm" onclick="document.getElementById('cookieSettingsForm').submit()">
                            <i class="las la-save me-1"></i> @lang('Save Changes')
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Main Configuration ── --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-bottom bg-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold"><i class="las la-cog me-2 text-primary"></i>@lang('Consent Banner Configuration')</h6>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="cookieStatusToggle" name="status" form="cookieSettingsForm" @if(@$cookie->data_values->status) checked @endif>
                        <label class="form-check-label small fw-bold text-muted" for="cookieStatusToggle">@lang('Banner Active')</label>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.setting.cookie.submit') }}" method="post" id="cookieSettingsForm">
                        @csrf
                        <input type="hidden" name="status" id="cookieStatusHidden" value="{{ @$cookie->data_values->status ? 1 : 0 }}">
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">@lang('Banner Position')</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="las la-arrows-alt"></i></span>
                                    <select class="form-select" name="banner_position">
                                        <option value="bottom" @selected((@$cookie->data_values->banner_position ?? 'bottom') == 'bottom')>@lang('Bottom Full Width')</option>
                                        <option value="top" @selected(@$cookie->data_values->banner_position == 'top')>@lang('Top Full Width')</option>
                                        <option value="bottom-left" @selected(@$cookie->data_values->banner_position == 'bottom-left')>@lang('Floating Bottom Left')</option>
                                        <option value="bottom-right" @selected(@$cookie->data_values->banner_position == 'bottom-right')>@lang('Floating Bottom Right')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">@lang('Visual Style')</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="las la-palette"></i></span>
                                    <select class="form-select" name="banner_style">
                                        <option value="compact" @selected((@$cookie->data_values->banner_style ?? 'compact') == 'compact')>@lang('Compact (Modern)')</option>
                                        <option value="expanded" @selected(@$cookie->data_values->banner_style == 'expanded')>@lang('Expanded (Classic)')</option>
                                        <option value="minimal" @selected(@$cookie->data_values->banner_style == 'minimal')>@lang('Minimalist (Sleek)')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">@lang('Identity Integration')</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="las la-certificate"></i></span>
                                    <select class="form-select" name="logo_box_style">
                                        <option value="light" @selected((@$cookie->data_values->logo_box_style ?? 'light') === 'light')>@lang('White Contrast')</option>
                                        <option value="brand" @selected(@$cookie->data_values->logo_box_style === 'brand')>@lang('Brand Identity')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">@lang('Short Message (Banner)')</label>
                            <textarea class="form-control" rows="2" name="short_desc" required placeholder="@lang('We use cookies to improve your experience...')">{{ @$cookie->data_values->short_desc }}</textarea>
                            <div class="form-text text-muted small">@lang('Keep this under 160 characters for best mobile visibility.')</div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <label class="form-label fw-bold small">@lang('Learn More Link')</label>
                                <input type="text" class="form-control" name="link_text" value="{{ @$cookie->data_values->link_text ?? __('learn more') }}" placeholder="@lang('e.g. Learn More')">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-bold small">@lang('Accept Button')</label>
                                <input type="text" class="form-control" name="allow_btn_text" value="{{ @$cookie->data_values->allow_btn_text ?? __('Allow') }}" placeholder="@lang('e.g. Accept All')">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-bold small">@lang('Decline Button')</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="decline_btn_text" value="{{ @$cookie->data_values->decline_btn_text ?? __('Decline') }}" placeholder="@lang('e.g. Decline')">
                                    <select class="form-select" name="show_decline_btn" style="max-width: 100px;">
                                        <option value="1" @selected((@$cookie->data_values->show_decline_btn ?? 1) != 0)>@lang('Show')</option>
                                        <option value="0" @selected(@$cookie->data_values->show_decline_btn == 0)>@lang('Hide')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">@lang('Full Legal Policy Content')</label>
                            <div class="border rounded bg-light p-1">
                                <textarea class="form-control nicEdit" rows="8" name="description" id="cookieDescription">@php echo @$cookie->data_values->description @endphp</textarea>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">@lang('Expiry (Days)')</label>
                                <input type="number" class="form-control" name="cookie_expiry_days" min="1" max="365" value="{{ @$cookie->data_values->cookie_expiry_days ?? 365 }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">@lang('Appearance Delay (s)')</label>
                                <input type="number" class="form-control" name="show_delay" min="0" max="60" value="{{ @$cookie->data_values->show_delay ?? 2 }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">@lang('Footer Trigger')</label>
                                <select class="form-select" name="show_preferences_link">
                                    <option value="1" @selected((@$cookie->data_values->show_preferences_link ?? 1) != 0)>@lang('Enabled')</option>
                                    <option value="0" @selected(@$cookie->data_values->show_preferences_link == 0)>@lang('Disabled')</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">@lang('Trigger Link Text')</label>
                                <input type="text" class="form-control" name="preferences_link_text" value="{{ @$cookie->data_values->preferences_link_text ?? __('Cookie Preferences') }}">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Sidebar: Strategic Messages ── --}}
        <div class="col-xl-4 col-lg-5">
            {{-- Pulse Metrics (Visual) --}}
            <div class="card border-0 shadow-sm bg-primary mb-4 overflow-hidden">
                <div class="card-body p-4 text-white position-relative">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="text-white mb-0 fw-bold">@lang('Privacy Insights')</h5>
                        <i class="las la-shield-alt fs-2 opacity-50"></i>
                    </div>
                    <p class="small opacity-75 mb-0">@lang('User consent improves data quality and trust.')</p>
                    <div class="mt-3 pt-3 border-top border-white border-opacity-10 d-flex justify-content-between align-items-center">
                        <div class="text-center">
                            <div class="fw-bold fs-5">98%</div>
                            <div class="tiny opacity-75">@lang('Accept Rate')</div>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold fs-5">1.2k</div>
                            <div class="tiny opacity-75">@lang('Consents (24h)')</div>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold fs-5">0</div>
                            <div class="tiny opacity-75">@lang('Compliance Risks')</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom bg-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold"><i class="las la-bullhorn me-2 text-primary"></i>@lang('Site-Wide Intelligence')</h6>
                    <button type="button" class="btn btn-label-primary btn-sm" data-bs-toggle="modal" data-bs-target="#customMessageModal" data-action="add">
                        <i class="las la-plus me-1"></i> @lang('Add')
                    </button>
                </div>
                <div class="card-body p-0">
                    @if($customMessages->isEmpty())
                        <div class="p-5 text-center">
                            <div class="avatar bg-label-secondary p-3 mx-auto mb-3" style="width: 60px; height: 60px;">
                                <i class="las la-inbox fs-2"></i>
                            </div>
                            <h6 class="mb-1 fw-bold">@lang('Empty Dashboard')</h6>
                            <p class="text-muted small mb-0">@lang('No custom strategic messages are active.')</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($customMessages as $msg)
                                @php $dv = $msg->data_values; @endphp
                                <div class="list-group-item p-4 border-bottom-dashed">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="bg-lighter p-2 rounded-3 text-primary">
                                            <i class="las {{ ($dv->position ?? '') == 'top_bar' ? 'la-arrow-up' : (($dv->position ?? '') == 'bottom_bar' ? 'la-arrow-down' : 'la-expand-arrows-alt') }} fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <span class="badge {{ ($dv->status ?? 0) == Status::ENABLE ? 'bg-label-success' : 'bg-label-secondary' }} tiny fw-bold">
                                                    {{ ($dv->status ?? 0) == Status::ENABLE ? __('ACTIVE') : __('DISABLED') }}
                                                </span>
                                                <div class="dropdown">
                                                    <button class="btn btn-icon btn-sm btn-outline-secondary border-0 p-0" type="button" data-bs-toggle="dropdown">
                                                        <i class="las la-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                        <li><a class="dropdown-item edit-custom-msg" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#customMessageModal" data-action="edit" data-id="{{ $msg->id }}" data-message="{{ e($dv->message ?? '') }}" data-link_url="{{ e($dv->link_url ?? '') }}" data-link_text="{{ e($dv->link_text ?? '') }}" data-show_on="{{ $dv->show_on ?? 'all' }}" data-position="{{ $dv->position ?? 'banner_center' }}" data-route_filter="{{ e($dv->route_filter ?? '') }}" data-status="{{ $dv->status ?? 0 }}"><i class="las la-edit me-2"></i> @lang('Edit Details')</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('admin.setting.cookie.custom_message.delete', $msg->id) }}" method="post" onsubmit="return confirm('@lang('Permanently remove this intelligence node?')');">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-danger"><i class="las la-trash-alt me-2"></i> @lang('Delete Node')</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <p class="text-dark small mb-2 fw-medium" style="line-height: 1.4;">{{ Str::limit($dv->message ?? '', 80) }}</p>
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="badge bg-lighter text-muted tiny">@lang('Target'): {{ ucfirst($dv->show_on ?? 'all') }}</span>
                                                <span class="badge bg-lighter text-muted tiny">@lang('UI Zone'): {{ str_replace('_', ' ', ucfirst($dv->position ?? 'center')) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Intelligence Node Modal (Add/Edit) ── --}}
<div class="modal fade" id="customMessageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="customMessageModalLabel">@lang('Add Strategic Message')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="customMessageForm" method="post" action="{{ route('admin.setting.cookie.custom_message.store') }}">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id" value="">
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold small">@lang('Message Narrative')</label>
                        <textarea class="form-control" name="message" id="msg_message" rows="3" required placeholder="@lang('Flash sale starts in 1 hour! Get 20% off...')"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small">@lang('Actionable Link URL')</label>
                            <input type="url" class="form-control" name="link_url" id="msg_link_url" placeholder="https://staylbd.com/sale">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">@lang('Button Label')</label>
                            <input type="text" class="form-control" name="link_text" id="msg_link_text" placeholder="@lang('Shop Now')">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">@lang('Status')</label>
                            <select class="form-select" name="status" id="msg_status">
                                <option value="1">@lang('Active')</option>
                                <option value="0">@lang('Inactive / Draft')</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">@lang('Display Scope')</label>
                            <select class="form-select" name="show_on" id="msg_show_on">
                                <option value="all">@lang('All Pages')</option>
                                <option value="public_only">@lang('Public Facing Only')</option>
                                <option value="user_only">@lang('Authenticated Users')</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">@lang('UI Placement')</label>
                            <select class="form-select" name="position" id="msg_position">
                                <option value="banner_center">@lang('Strategic Center')</option>
                                <option value="top_bar">@lang('Floating Top')</option>
                                <option value="bottom_bar">@lang('Sticky Bottom')</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">@lang('Route Routing (Optional)')</label>
                            <input type="text" class="form-control" name="route_filter" id="msg_route_filter" placeholder="@lang('e.g. products, contact')">
                            <div class="form-text tiny">@lang('Comma separated values. Leave empty for global visibility.')</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 py-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="las la-save me-1"></i> @lang('Synchronize Node')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('style')
<style>
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-lighter { background-color: #f8fafc !important; }
    .tiny { font-size: 0.75rem !important; }
    .border-bottom-dashed { border-bottom: 1px dashed #e2e8f0 !important; }
    .btn-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .btn-label-primary:hover { background-color: #696cff !important; color: #fff !important; }
</style>
@endpush

@push('script')
<script>
(function() {
    // Sync switch with hidden input for form submission consistency
    document.getElementById('cookieStatusToggle').addEventListener('change', function() {
        document.getElementById('cookieStatusHidden').value = this.checked ? 1 : 0;
    });

    var modal = document.getElementById('customMessageModal');
    if (!modal) return;
    
    modal.addEventListener('show.bs.modal', function(e) {
        var btn = e.relatedTarget;
        var action = btn ? btn.getAttribute('data-action') : 'add';
        var form = document.getElementById('customMessageForm');
        var editId = document.getElementById('edit_id');
        
        if (action === 'edit' && btn) {
            editId.value = btn.getAttribute('data-id') || '';
            form.action = '{{ route("admin.setting.cookie.custom_message.update", ["id" => "__ID__"]) }}'.replace('__ID__', editId.value);
            document.getElementById('msg_message').value = btn.getAttribute('data-message') || '';
            document.getElementById('msg_link_url').value = btn.getAttribute('data-link_url') || '';
            document.getElementById('msg_link_text').value = btn.getAttribute('data-link_text') || '';
            document.getElementById('msg_show_on').value = btn.getAttribute('data-show_on') || 'all';
            document.getElementById('msg_position').value = btn.getAttribute('data-position') || 'banner_center';
            document.getElementById('msg_route_filter').value = btn.getAttribute('data-route_filter') || '';
            document.getElementById('msg_status').value = btn.getAttribute('data-status') || '1';
            document.getElementById('customMessageModalLabel').textContent = '{{ __("Edit Intelligence Node") }}';
        } else {
            editId.value = '';
            form.action = '{{ route("admin.setting.cookie.custom_message.store") }}';
            document.getElementById('msg_message').value = '';
            document.getElementById('msg_link_url').value = '';
            document.getElementById('msg_link_text').value = '';
            document.getElementById('msg_show_on').value = 'all';
            document.getElementById('msg_position').value = 'banner_center';
            document.getElementById('msg_route_filter').value = '';
            document.getElementById('msg_status').value = '1';
            document.getElementById('customMessageModalLabel').textContent = '{{ __("Add Strategic Message") }}';
        }
    });
})();
</script>
@endpush
@endsection

