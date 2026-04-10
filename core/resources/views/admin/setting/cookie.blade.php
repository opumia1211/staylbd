@extends('admin.layouts.app')
@section('panel')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-0">
            <div>
                <h4 class="mb-1 fw-bold">@lang('GDPR & Site Messages')</h4>
                <p class="text-muted small mb-0">@lang('Cookie consent banner and custom messages across public & user pages.')</p>
            </div>
            <a href="{{ route('cookie.policy') }}" target="_blank" rel="noopener" class="btn btn-outline--primary btn-sm">
                <i class="las la-external-link-alt me-1"></i> @lang('View policy page')
            </a>
        </div>
    </div>

    {{-- Cookie consent – compact card --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-0">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold"><i class="las la-cookie-bite text--primary me-2"></i>@lang('Cookie consent')</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.setting.cookie.submit') }}" method="post">
                    @csrf
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-semibold mb-1">@lang('Status')</label>
                            <input type="checkbox" data-width="100%" data-height="44" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('On')" data-off="@lang('Off')" @if(@$cookie->data_values->status) checked @endif name="status">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-semibold mb-1">@lang('Position')</label>
                            <select class="form-select form-select-sm" name="banner_position">
                                <option value="bottom" @selected((@$cookie->data_values->banner_position ?? 'bottom') == 'bottom')>@lang('Bottom')</option>
                                <option value="top" @selected(@$cookie->data_values->banner_position == 'top')>@lang('Top')</option>
                                <option value="bottom-left" @selected(@$cookie->data_values->banner_position == 'bottom-left')>@lang('Bottom Left')</option>
                                <option value="bottom-right" @selected(@$cookie->data_values->banner_position == 'bottom-right')>@lang('Bottom Right')</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-semibold mb-1">@lang('Style')</label>
                            <select class="form-select form-select-sm" name="banner_style">
                                <option value="compact" @selected((@$cookie->data_values->banner_style ?? 'compact') == 'compact')>@lang('Compact')</option>
                                <option value="expanded" @selected(@$cookie->data_values->banner_style == 'expanded')>@lang('Expanded')</option>
                                <option value="minimal" @selected(@$cookie->data_values->banner_style == 'minimal')>@lang('Minimal')</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small fw-semibold mb-1">@lang('Logo box')</label>
                            <select class="form-select form-select-sm" name="logo_box_style">
                                <option value="light" @selected((@$cookie->data_values->logo_box_style ?? 'light') === 'light')>@lang('Light')</option>
                                <option value="brand" @selected(@$cookie->data_values->logo_box_style === 'brand')>@lang('Brand color')</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold mb-1">@lang('Banner message')</label>
                        <textarea class="form-control" rows="3" required name="short_desc" placeholder="@lang('We use cookies...')">{{ @$cookie->data_values->short_desc }}</textarea>
                        <div class="row g-2 mt-2">
                            <div class="col-6 col-md-3"><input type="text" class="form-control form-control-sm" name="link_text" value="{{ @$cookie->data_values->link_text ?? __('learn more') }}" placeholder="@lang('Link text')"></div>
                            <div class="col-6 col-md-3"><input type="text" class="form-control form-control-sm" name="allow_btn_text" value="{{ @$cookie->data_values->allow_btn_text ?? __('Allow') }}" placeholder="@lang('Allow')"></div>
                            <div class="col-6 col-md-3"><input type="text" class="form-control form-control-sm" name="decline_btn_text" value="{{ @$cookie->data_values->decline_btn_text ?? __('Decline') }}" placeholder="@lang('Decline')"></div>
                            <div class="col-6 col-md-3">
                                <select class="form-select form-select-sm" name="show_decline_btn">
                                    <option value="1" @selected((@$cookie->data_values->show_decline_btn ?? 1) != 0)>@lang('Show Decline')</option>
                                    <option value="0" @selected(@$cookie->data_values->show_decline_btn == 0)>@lang('Hide Decline')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold mb-1">@lang('Cookie policy (full text)')</label>
                        <textarea class="form-control nicEdit" rows="6" name="description" id="cookieDescription">@php echo @$cookie->data_values->description @endphp</textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6 col-md-3"><label class="form-label small mb-1">@lang('Expiry (days)')</label><input type="number" class="form-control form-control-sm" name="cookie_expiry_days" min="1" max="365" value="{{ @$cookie->data_values->cookie_expiry_days ?? 365 }}"></div>
                        <div class="col-6 col-md-3"><label class="form-label small mb-1">@lang('Delay (sec)')</label><input type="number" class="form-control form-control-sm" name="show_delay" min="0" max="60" value="{{ @$cookie->data_values->show_delay ?? 2 }}"></div>
                        <div class="col-6 col-md-3"><label class="form-label small mb-1">@lang('Footer link')</label><select class="form-select form-select-sm" name="show_preferences_link"><option value="1" @selected((@$cookie->data_values->show_preferences_link ?? 1) != 0)>@lang('Yes')</option><option value="0" @selected(@$cookie->data_values->show_preferences_link == 0)>@lang('No')</option></select></div>
                        <div class="col-6 col-md-3"><label class="form-label small mb-1">@lang('Link text')</label><input type="text" class="form-control form-control-sm" name="preferences_link_text" value="{{ @$cookie->data_values->preferences_link_text ?? __('Cookie Preferences') }}"></div>
                    </div>
                    <button type="submit" class="btn btn--primary btn-sm"><i class="las la-save me-1"></i>@lang('Save cookie settings')</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Custom site messages – with + Add --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold"><i class="las la-bullhorn text--primary me-2"></i>@lang('Custom site messages')</h6>
                <button type="button" class="btn btn--primary btn-sm py-1 px-2" data-bs-toggle="modal" data-bs-target="#customMessageModal" data-action="add" title="@lang('Add message')">
                    <i class="las la-plus"></i>
                </button>
            </div>
            <div class="card-body p-0">
                @if($customMessages->isEmpty())
                    <div class="p-4 text-center text-muted small">
                        <i class="las la-inbox fs-2 d-block mb-2 opacity-50"></i>
                        @lang('No custom messages yet.')<br>
                        <button type="button" class="btn btn-link btn-sm p-0 mt-1" data-bs-toggle="modal" data-bs-target="#customMessageModal" data-action="add">@lang('Add one')</button>
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($customMessages as $msg)
                            @php $dv = $msg->data_values; @endphp
                            <li class="list-group-item d-flex align-items-start gap-2 py-3">
                                <div class="flex-grow-1 min-w-0">
                                    <span class="d-block text-truncate small">{{ Str::limit($dv->message ?? '', 50) }}</span>
                                    <span class="badge bg-light text-dark me-1 mt-1">{{ $dv->position ?? 'banner_center' }}</span>
                                    <span class="badge bg-light text-dark">{{ $dv->show_on ?? 'all' }}</span>
                                    @if(($dv->status ?? 0) != \App\Constants\Status::ENABLE)
                                        <span class="badge bg-secondary">@lang('Off')</span>
                                    @endif
                                </div>
                                <div class="btn-group btn-group-sm flex-shrink-0">
                                    <button type="button" class="btn btn-outline--primary btn-sm py-0 px-2 edit-custom-msg" data-bs-toggle="modal" data-bs-target="#customMessageModal" data-action="edit" data-id="{{ $msg->id }}" data-message="{{ e($dv->message ?? '') }}" data-link_url="{{ e($dv->link_url ?? '') }}" data-link_text="{{ e($dv->link_text ?? '') }}" data-show_on="{{ $dv->show_on ?? 'all' }}" data-position="{{ $dv->position ?? 'banner_center' }}" data-route_filter="{{ e($dv->route_filter ?? '') }}" data-status="{{ $dv->status ?? 0 }}" title="@lang('Edit')"><i class="las la-pen"></i></button>
                                    <form action="{{ route('admin.setting.cookie.custom_message.delete', $msg->id) }}" method="post" class="d-inline" onsubmit="return confirm('@lang('Remove this message?')');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline--danger btn-sm py-0 px-2" title="@lang('Delete')"><i class="las la-trash"></i></button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal: Add / Edit custom message (moved to body by JS so it opens above backdrop) --}}
<div class="modal fade" id="customMessageModal" tabindex="-1" aria-labelledby="customMessageModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="position: relative; z-index: 1061;">
            <div class="modal-header">
                <h5 class="modal-title" id="customMessageModalLabel">@lang('Add custom message')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="customMessageForm" method="post" action="{{ route('admin.setting.cookie.custom_message.store') }}">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">@lang('Message')</label>
                        <textarea class="form-control" name="message" id="msg_message" rows="3" required placeholder="@lang('Your announcement or notice...')"></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label fw-semibold">@lang('Link URL')</label>
                            <input type="url" class="form-control form-control-sm" name="link_url" id="msg_link_url" placeholder="https://...">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">@lang('Link text')</label>
                            <input type="text" class="form-control form-control-sm" name="link_text" id="msg_link_text" placeholder="@lang('Read more')" value="@lang('Read more')">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">@lang('Show on')</label>
                            <select class="form-select form-select-sm" name="show_on" id="msg_show_on">
                                <option value="public_only">@lang('Public pages only')</option>
                                <option value="user_only">@lang('User / profile pages only')</option>
                                <option value="all">@lang('All pages')</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">@lang('Position')</label>
                            <select class="form-select form-select-sm" name="position" id="msg_position">
                                <option value="top_bar">@lang('Top bar')</option>
                                <option value="bottom_bar">@lang('Bottom bar')</option>
                                <option value="banner_center">@lang('Banner / center')</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">@lang('Specific routes (optional)')</label>
                            <input type="text" class="form-control form-control-sm" name="route_filter" id="msg_route_filter" placeholder="@lang('e.g. home, user.dashboard')">
                            <small class="text-muted">@lang('Leave empty for all routes in the selected scope.')</small>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="status" id="msg_status" value="1" checked>
                                <label class="form-check-label" for="msg_status">@lang('Active')</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary btn-sm"><i class="las la-save me-1"></i>@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
(function() {
    var modal = document.getElementById('customMessageModal');
    if (!modal) return;
    // Move modal to body so it appears above backdrop (fixes black screen when parent has stacking context)
    if (modal.parentNode && modal.parentNode !== document.body) {
        document.body.appendChild(modal);
    }
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
            document.getElementById('msg_link_text').value = btn.getAttribute('data-link_text') || '{{ __("Read more") }}';
            document.getElementById('msg_show_on').value = btn.getAttribute('data-show_on') || 'all';
            document.getElementById('msg_position').value = btn.getAttribute('data-position') || 'banner_center';
            document.getElementById('msg_route_filter').value = btn.getAttribute('data-route_filter') || '';
            document.getElementById('msg_status').checked = (btn.getAttribute('data-status') === '1');
            document.getElementById('customMessageModalLabel').textContent = '{{ __("Edit custom message") }}';
        } else {
            editId.value = '';
            form.action = '{{ route("admin.setting.cookie.custom_message.store") }}';
            var m = form.querySelector('input[name="_method"]'); if (m) m.remove();
            document.getElementById('msg_message').value = '';
            document.getElementById('msg_link_url').value = '';
            document.getElementById('msg_link_text').value = '{{ __("Read more") }}';
            document.getElementById('msg_show_on').value = 'all';
            document.getElementById('msg_position').value = 'banner_center';
            document.getElementById('msg_route_filter').value = '';
            document.getElementById('msg_status').checked = true;
            document.getElementById('customMessageModalLabel').textContent = '{{ __("Add custom message") }}';
        }
    });
})();
</script>
@endpush
