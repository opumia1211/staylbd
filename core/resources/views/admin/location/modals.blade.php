@php $general = gs(); @endphp
{{-- Add Division --}}
<div class="modal fade" id="modalAddDivision" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.locations.division.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-plus me-1"></i> @lang('Add Division')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (English)') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name_en" required maxlength="100" placeholder="e.g. Dhaka">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (Bangla)')</label>
                        <input type="text" class="form-control" name="name_bn" maxlength="100" placeholder="e.g. ঢাকা">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary">@lang('Add')</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Edit Division --}}
<div class="modal fade" id="modalEditDivision" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditDivision" action="{{ route('admin.locations.division.update', 0) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-edit me-1"></i> @lang('Edit Division')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (English)') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name_en" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (Bangla)')</label>
                        <input type="text" class="form-control" name="name_bn" maxlength="100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary">@lang('Update')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add District --}}
<div class="modal fade" id="modalAddDistrict" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.locations.district.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-plus me-1"></i> @lang('Add District')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">@lang('Division') <span class="text-danger">*</span></label>
                        <select name="division_id" class="form-select" required>
                            <option value="">@lang('Select Division')</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}">{{ $div->name_en }} / {{ $div->name_bn }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (English)') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name_en" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (Bangla)')</label>
                        <input type="text" class="form-control" name="name_bn" maxlength="100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary">@lang('Add')</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Edit District --}}
<div class="modal fade" id="modalEditDistrict" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditDistrict" action="{{ route('admin.locations.district.update', 0) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-edit me-1"></i> @lang('Edit District')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">@lang('Division')</label>
                        <select name="division_id" class="form-select" required>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}">{{ $div->name_en }} / {{ $div->name_bn }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (English)') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name_en" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (Bangla)')</label>
                        <input type="text" class="form-control" name="name_bn" maxlength="100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary">@lang('Update')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Thana --}}
<div class="modal fade" id="modalAddThana" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.locations.thana.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-plus me-1"></i> @lang('Add Thana')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">@lang('District') <span class="text-danger">*</span></label>
                        <select name="district_id" class="form-select" required>
                            <option value="">@lang('Select District')</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}">{{ $d->division->name_en ?? '' }} → {{ $d->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (English)') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name_en" required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (Bangla)')</label>
                        <input type="text" class="form-control" name="name_bn" maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Postal Code')</label>
                        <input type="text" class="form-control" name="postal_code" maxlength="20" placeholder="e.g. 1000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary">@lang('Add')</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Edit Thana --}}
<div class="modal fade" id="modalEditThana" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditThana" action="{{ route('admin.locations.thana.update', 0) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-edit me-1"></i> @lang('Edit Thana')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">@lang('District') <span class="text-danger">*</span></label>
                        <select name="district_id" class="form-select" required>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}">{{ $d->division->name_en ?? '' }} → {{ $d->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (English)') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name_en" required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (Bangla)')</label>
                        <input type="text" class="form-control" name="name_bn" maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Postal Code')</label>
                        <input type="text" class="form-control" name="postal_code" maxlength="20">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary">@lang('Update')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Delivery Zone --}}
<div class="modal fade" id="modalAddDeliveryZone" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.locations.delivery.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-plus me-1"></i> @lang('Add Delivery Zone')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">@lang('Thana') <span class="text-danger">*</span></label>
                        <select name="thana_id" class="form-select" required>
                            <option value="">@lang('Select Thana')</option>
                            @foreach($thanas as $t)
                                <option value="{{ $t->id }}">{{ $t->district->name_en ?? '' }} → {{ $t->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Delivery Charge') <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" name="delivery_charge" required value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Estimated Days')</label>
                        <input type="text" class="form-control" name="estimated_days" maxlength="50" placeholder="e.g. 2-3 days">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary">@lang('Add')</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Edit Delivery Zone --}}
<div class="modal fade" id="modalEditDeliveryZone" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditDeliveryZone" action="{{ route('admin.locations.delivery.update', 0) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-edit me-1"></i> @lang('Edit Delivery Zone')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">@lang('Thana')</label>
                        <select name="thana_id" class="form-select" required>
                            @foreach($thanas as $t)
                                <option value="{{ $t->id }}">{{ $t->district->name_en ?? '' }} → {{ $t->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Delivery Charge') <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" name="delivery_charge" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Estimated Days')</label>
                        <input type="text" class="form-control" name="estimated_days" maxlength="50">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary">@lang('Update')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var formEditDivision = document.getElementById('formEditDivision');
    if (formEditDivision) {
        formEditDivision.addEventListener('submit', function() {
            var id = formEditDivision.querySelector('input[name=id]').value;
            if (id) formEditDivision.action = formEditDivision.action.replace(/\/0$/, '/' + id);
        });
    }
    var formEditDistrict = document.getElementById('formEditDistrict');
    if (formEditDistrict) {
        formEditDistrict.addEventListener('submit', function() {
            var id = formEditDistrict.querySelector('input[name=id]').value;
            if (id) formEditDistrict.action = formEditDistrict.action.replace(/\/0$/, '/' + id);
        });
    }
    var formEditThana = document.getElementById('formEditThana');
    if (formEditThana) {
        formEditThana.addEventListener('submit', function() {
            var id = formEditThana.querySelector('input[name=id]').value;
            if (id) formEditThana.action = formEditThana.action.replace(/\/0$/, '/' + id);
        });
    }
    var formEditDeliveryZone = document.getElementById('formEditDeliveryZone');
    if (formEditDeliveryZone) {
        formEditDeliveryZone.addEventListener('submit', function() {
            var id = formEditDeliveryZone.querySelector('input[name=id]').value;
            if (id) formEditDeliveryZone.action = formEditDeliveryZone.action.replace(/\/0$/, '/' + id);
        });
    }
});
</script>
