@extends('admin.layouts.app')

@section('panel')
{{-- Page header --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card b-radius--10 border-0 shadow-sm">
            <div class="card-body py-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h5 class="mb-1 fw-bold">@lang('Courier Service Management')</h5>
                        <p class="text-muted small mb-0">@lang('Connect & manage courier APIs. Add API Key, Secret Key & Token for Bangladesh, India, Pakistan & worldwide.')</p>
                    </div>
                    <div class="d-flex gap-2 flex-shrink-0">
                        <a href="{{ route('admin.api.courier.logs') }}" class="btn btn-sm btn-outline--info"><i class="las la-list-alt"></i> @lang('Logs')</a>
                        <a href="{{ route('admin.api.courier.reports') }}" class="btn btn-sm btn-outline--dark"><i class="las la-chart-bar"></i> @lang('Reports')</a>
                        <button type="button" class="btn btn--primary btn-sm" data-toggle="modal" data-target="#addCourierModal">
                            <i class="las la-plus"></i> @lang('Add Courier')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stats: Total, Success, Failed, Pending, Returns, Configured --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--primary rounded me-2 p-2"><i class="las la-shipping-fast text-white"></i></div>
                <div class="min-w-0">
                    <h6 class="text-muted mb-0 small text-uppercase">@lang('Total')</h6>
                    <h5 class="mb-0">{{ number_format($stats['total'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--success rounded me-2 p-2"><i class="las la-check-circle text-white"></i></div>
                <div class="min-w-0">
                    <h6 class="text-muted mb-0 small text-uppercase">@lang('Success')</h6>
                    <h5 class="mb-0">{{ number_format($stats['success'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--danger rounded me-2 p-2"><i class="las la-times-circle text-white"></i></div>
                <div class="min-w-0">
                    <h6 class="text-muted mb-0 small text-uppercase">@lang('Failed')</h6>
                    <h5 class="mb-0">{{ number_format($stats['failed'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--warning rounded me-2 p-2"><i class="las la-clock text-white"></i></div>
                <div class="min-w-0">
                    <h6 class="text-muted mb-0 small text-uppercase">@lang('Pending')</h6>
                    <h5 class="mb-0">{{ number_format($stats['pending'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--dark rounded me-2 p-2"><i class="las la-undo text-white"></i></div>
                <div class="min-w-0">
                    <h6 class="text-muted mb-0 small text-uppercase">@lang('Returns')</h6>
                    <h5 class="mb-0">{{ number_format($stats['returns'] ?? 0) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card b-radius--10 border-0 shadow-sm h-100">
            <div class="card-body py-3 d-flex align-items-center">
                <div class="widget-icon bg--info rounded me-2 p-2"><i class="las la-truck text-white"></i></div>
                <div class="min-w-0">
                    <h6 class="text-muted mb-0 small text-uppercase">@lang('Connected')</h6>
                    <h5 class="mb-0">{{ count($providers ?? []) }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Connected services table --}}
<div class="card b-radius--10 border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 py-3">
        <h5 class="card-title mb-0">@lang('Connected Courier Services')</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table--light style--two mb-0">
                <thead>
                    <tr>
                        <th>@lang('Service')</th>
                        <th>@lang('Country / Region')</th>
                        <th>@lang('API')</th>
                        <th>@lang('Status')</th>
                        <th width="200">@lang('Actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($providers ?? [] as $p)
                    @php
                        $driver = isset($drivers[$p->type]) ? $drivers[$p->type] : null;
                        $configured = $driver ? $driver->isConfigured($p) : (!empty(trim($p->url ?? '')));
                        $name = $p->name ?? ucfirst($p->type ?? '');
                        $countryRegion = trim($p->country_code ?? '') . (trim($p->region ?? '') ? ' / ' . $p->region : '');
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="bg--primary bg-opacity-10 rounded p-2">
                                    <i class="las {{ $driver && method_exists($driver, 'getIcon') ? $driver->getIcon() : 'la-truck' }} text--primary"></i>
                                </span>
                                <strong>{{ $name }}</strong>
                            </div>
                        </td>
                        <td>{{ $countryRegion ?: '—' }}</td>
                        <td>
                            @if($configured)
                                <span class="badge badge--success">@lang('Ready')</span>
                            @else
                                <span class="badge badge--warning">@lang('Set API')</span>
                            @endif
                        </td>
                        <td>
                            <label class="mb-0 d-inline-flex align-items-center">
                                <input type="checkbox" class="status-toggle" data-id="{{ $p->id }}" data-name="{{ $name }}" {{ $p->status ? 'checked' : '' }}>
                                <span class="ms-1 small">{{ $p->status ? __('On') : __('Off') }}</span>
                            </label>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn--primary edit-btn" data-id="{{ $p->id }}"><i class="las la-edit"></i></button>
                            @if($configured)
                            <button type="button" class="btn btn-sm btn-outline--info test-btn" data-id="{{ $p->id }}" data-name="{{ $name }}"><i class="las la-plug"></i></button>
                            @endif
                            <a href="{{ route('admin.orders.bulk.courier', $p->type) }}" class="btn btn-sm btn-outline--success" title="@lang('Bulk Send')"><i class="las la-paper-plane"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="las la-truck fs-1 d-block mb-2 opacity-50"></i>
                            @lang('No courier added. Click "Add Courier" to connect a service.')
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Courier Modal --}}
<div class="modal fade" id="addCourierModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="las la-plus"></i> @lang('Add Courier Service')</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs nav-tabs--primary mb-3" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#addPredefinedTab">@lang('From list')</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#addCustomTab">@lang('Custom')</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="addPredefinedTab">
                        @if(!empty($addableTypes ?? []))
                        <form action="{{ route('admin.api.courier.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>@lang('Courier')</label>
                                <select name="type" class="form-control" required>
                                    <option value="">@lang('Choose...')</option>
                                    @foreach($addableTypes as $type => $label)
                                    <option value="{{ $type }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>@lang('Display name')</label>
                                <input type="text" class="form-control" name="name" placeholder="@lang('Optional')">
                            </div>
                            <div class="form-group">
                                <label>@lang('Country code')</label>
                                <input type="text" class="form-control" name="country_code" value="BD" maxlength="10">
                            </div>
                            <button type="submit" class="btn btn--primary">@lang('Add')</button>
                        </form>
                        @else
                        <p class="text-muted mb-0">@lang('All predefined couriers added. Use Custom tab for more.')</p>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="addCustomTab">
                        <form action="{{ route('admin.api.courier.store.custom') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>@lang('Company name')</label>
                                <input type="text" class="form-control" name="name" required placeholder="e.g. RedX, DHL">
                            </div>
                            <div class="form-group">
                                <label>@lang('Country')</label>
                                <select name="country_code" class="form-control">
                                    <option value="BD">@lang('Bangladesh')</option>
                                    <option value="IN">@lang('India')</option>
                                    <option value="PK">@lang('Pakistan')</option>
                                    <option value="LK">@lang('Sri Lanka')</option>
                                    <option value="NP">@lang('Nepal')</option>
                                    <option value="US">@lang('USA')</option>
                                    <option value="GB">@lang('UK')</option>
                                    <option value="OTHER">@lang('Other')</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>@lang('Region')</label>
                                <select name="region" class="form-control">
                                    <option value="">—</option>
                                    <option value="BD">@lang('Bangladesh')</option>
                                    <option value="ASIA">@lang('Asia')</option>
                                    <option value="GLOBAL">@lang('Worldwide')</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn--primary">@lang('Add')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="{{ route('admin.api.courier.update') }}" id="editForm">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-cog"></i> @lang('Edit Courier & API')</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>@lang('Display name')</label>
                            <input type="text" class="form-control" name="name" id="edit_name">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>@lang('Country')</label>
                            <input type="text" class="form-control" name="country_code" id="edit_country" maxlength="10">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>@lang('Region')</label>
                            <select class="form-control" name="region" id="edit_region">
                                <option value="">—</option>
                                <option value="BD">@lang('Bangladesh')</option>
                                <option value="ASIA">@lang('Asia')</option>
                                <option value="GLOBAL">@lang('Worldwide')</option>
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <label>@lang('API URL')</label>
                            <input type="text" class="form-control" name="url" id="edit_url" placeholder="https://api.example.com">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>@lang('Token / API Key')</label>
                            <input type="text" class="form-control" name="token" id="edit_token">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>@lang('API Key') <small class="text-muted">(@lang('Optional'))</small></label>
                            <input type="text" class="form-control" name="api_key" id="edit_api_key">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>@lang('Secret Key') <small class="text-muted">(@lang('Optional'))</small></label>
                            <input type="text" class="form-control" name="secret_key" id="edit_secret_key">
                        </div>
                        <div class="col-md-6 form-group d-flex align-items-end gap-3 pt-2">
                            <input type="hidden" name="show_to_user" value="0">
                            <label class="mb-0"><input type="checkbox" name="status" id="edit_status" value="1"> @lang('Enable')</label>
                            <label class="mb-0"><input type="checkbox" name="show_to_user" id="edit_show_to_user" value="1"> @lang('Show to user')</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--primary">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.api.courier.logs') }}" class="btn btn-sm btn-outline--info">@lang('Logs')</a>
    <a href="{{ route('admin.api.courier.reports') }}" class="btn btn-sm btn-outline--dark">@lang('Reports')</a>
@endpush

@push('script')
<script>
jQuery(function($) {
    var editUrlTemplate = "{{ route('admin.api.courier.edit.json', ['id' => '__ID__']) }}";
    var testConnectionUrlTemplate = "{{ route('admin.api.courier.test', ['id' => '__ID__']) }}";
    $(document).on('click', '.edit-btn', function() {
        var id = $(this).data('id'), btn = $(this);
        btn.prop('disabled', true);
        $.get(editUrlTemplate.replace('__ID__', id), function(d) {
            $('#edit_id').val(d.id || '');
            $('#edit_name').val(d.name || '');
            $('#edit_country').val(d.country_code || 'BD');
            $('#edit_region').val(d.region || '');
            $('#edit_url').val(d.url || '');
            $('#edit_token').val(d.token || '');
            $('#edit_api_key').val(d.api_key || '');
            $('#edit_secret_key').val(d.secret_key || '');
            $('#edit_status').prop('checked', !!d.status);
            $('#edit_show_to_user').prop('checked', !!d.show_to_user);
            $('#editModal').modal('show');
        }).fail(function() {
            if (typeof notify === 'function') notify('error', '@lang("Failed to load courier data")');
        }).always(function() { btn.prop('disabled', false); });
    });
    $(document).on('click', '.test-btn', function() {
        var id = this.dataset.id, btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        $.ajax({
            url: testConnectionUrlTemplate.replace('__ID__', id),
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            success: function(r) {
                alert(r.success ? ('@lang("Connection successful.")\n' + (r.message || '')) : ('@lang("Connection failed.")\n' + (r.message || '')));
            },
            error: function() { alert('@lang("Request failed.")'); },
            complete: function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="las la-plug"></i>';
            }
        });
    });
    $(document).on('change', '.status-toggle', function() {
        var id = $(this).data('id'), name = $(this).data('name'), chk = this.checked, toggle = this;
        var fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('id', id);
        fd.append('status', chk ? '1' : '0');
        $.ajax({
            url: '{{ route("admin.api.courier.update") }}',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function() {
                if (typeof notify === 'function') notify('success', name + ' ' + (chk ? '@lang("enabled")' : '@lang("disabled")'));
                else window.location.reload();
            },
            error: function() {
                toggle.checked = !chk;
                if (typeof notify === 'function') notify('error', '@lang("Update failed")');
            }
        });
    });
});
</script>
@endpush
