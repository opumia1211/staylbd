@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        <form action="{{ route('admin.frontend.sections.district.update') }}" method="POST">
            @csrf
            <input type="hidden" name="district_id" value="{{ $districtId }}">

            {{-- 1. Selection Hierarchy --}}
            <div class="card b-radius--10 border-0 shadow-sm mb-4">
                <div class="card-header bg--primary py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white"><i class="las la-globe-asia me-2"></i> @lang('Location Hierarchy Management')</h5>
                    <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="collapse" data-bs-target="#settingsCollapse">
                        <i class="las la-cog"></i> @lang('Settings')
                    </button>
                </div>
                
                <div class="collapse" id="settingsCollapse">
                    <div class="card-body bg-light border-bottom p-4">
                        <h6 class="mb-3"><i class="las la-tag me-1"></i> @lang('Checkout Field Labels')</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small">@lang('Label EN')</label>
                                <input type="text" class="form-control form-control-sm" name="label_en" value="{{ $labels->label_en }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">@lang('Label BN')</label>
                                <input type="text" class="form-control form-control-sm" name="label_bn" value="{{ $labels->label_bn }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">@lang('Help Text EN')</label>
                                <input type="text" class="form-control form-control-sm" name="help_en" value="{{ $labels->help_en }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">@lang('Help Text BN')</label>
                                <input type="text" class="form-control form-control-sm" name="help_bn" value="{{ $labels->help_bn }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4 align-items-end">
                        {{-- Step 1: Country --}}
                        <div class="col-md-3">
                            <label class="form-label fw-bold">1. @lang('Select Country')</label>
                            <select id="selCountry" class="form-select form-select-lg border--primary-light shadow-none">
                                <option value="BD" selected>🇧🇩 @lang('Bangladesh')</option>
                            </select>
                            <small class="text-muted">@lang('Current scope: Bangladesh only')</small>
                        </div>

                        {{-- Step 2: Division --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold">2. @lang('Select Division') / বিভাগ</label>
                            <select id="selDivision" class="form-select form-select-lg border--primary-light shadow-none">
                                <option value="">— @lang('Select Division') —</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}" data-url="{{ route('admin.frontend.sections.district', ['division_id' => $div->id]) }}" {{ $divisionId == $div->id ? 'selected' : '' }}>{{ $div->name_en }} / {{ $div->name_bn }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Step 3: District --}}
                        <div class="col-md-5">
                            <label class="form-label fw-bold">3. @lang('Select District') / জেলা</label>
                            <select id="selDistrict" class="form-select form-select-lg border--primary-light shadow-none">
                                <option value="">— @lang('Select District') —</option>
                                @foreach($districts->where('division_id', $divisionId) as $d)
                                    <option value="{{ $d->id }}" data-url="{{ route('admin.frontend.sections.district', ['division_id' => $divisionId, 'district_id' => $d->id]) }}" {{ $districtId == $d->id ? 'selected' : '' }}>{{ $d->name_en }} / {{ $d->name_bn }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Thana Management --}}
            @if($selectedDistrict)
                <div class="card b-radius--10 border-0 shadow-sm mb-4">
                    <div class="card-header bg--dark py-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-10 p-2 rounded">
                                <i class="las la-map-pin text-white fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-white">{{ $selectedDistrict->name_en }} / {{ $selectedDistrict->name_bn }}</h5>
                                <span class="text-white-50 small">@lang('Managing Thanas / Areas')</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn--success" id="btnAddThana">
                            <i class="las la-plus"></i> @lang('Add New Thana')
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px;">
                            <table class="table table--light style--two mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">@lang('SL')</th>
                                        <th>@lang('Thana Name (English)')</th>
                                        <th>@lang('Thana Name (Bangla)')</th>
                                        <th style="width: 100px;">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody id="thanaRows">
                                    @forelse($thanas as $i => $t)
                                        <tr class="thana-row">
                                            <td class="fw-bold SL">{{ $i + 1 }}</td>
                                            <td>
                                                <input type="text" class="form-control" name="thanas[{{ $i }}][en]" value="{{ $t->name_en }}" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="thanas[{{ $i }}][bn]" value="{{ $t->name_bn }}" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline--danger removeThanaBtn">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-row">
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="las la-exclamation-circle fs-1 d-block mb-2 opacity-50"></i>
                                                @lang('No thanas found for this district. Click "Add New Thana" to start.')
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent py-4 px-4 text-end">
                        <button type="submit" class="btn btn--primary btn-lg px-5">
                            <i class="las la-save me-1"></i> @lang('Save Changes')
                        </button>
                    </div>
                </div>
            @else
                <div class="card b-radius--10 border-0 shadow-sm mb-4">
                    <div class="card-body p-5 text-center">
                        <div class="mb-3">
                            <i class="las la-map-marked-alt text--primary opacity-25" style="font-size: 80px;"></i>
                        </div>
                        <h4 class="text-muted">@lang('Select a District to Manage Thanas')</h4>
                        <p class="text-muted px-md-5">@lang('Once you select a district from the dropdown above, you will be able to add, edit, or remove thanas/areas for that specific district.')</p>
                    </div>
                </div>
            @endif
        </form>

        {{-- 3. Quick Edit --}}
        @if($useDb && $divisions->isNotEmpty())
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card b-radius--10 border-0 shadow-sm">
                        <div class="card-header bg-white py-3 px-4 border-0">
                            <h6 class="mb-0 fw-bold"><i class="las la-edit me-1"></i> @lang('Quick Edit: Divisions')</h6>
                        </div>
                        <div class="card-body p-0">
                            <form action="{{ route('admin.frontend.sections.district.update') }}" method="POST">
                                @csrf
                                <div class="table-responsive" style="max-height: 300px;">
                                    <table class="table table--light style--two table-sm mb-0">
                                        <thead>
                                            <tr><th>EN</th><th>BN</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach($divisions as $div)
                                                <tr>
                                                    <td><input type="text" class="form-control form-control-sm" name="divisions[{{ $div->id }}][en]" value="{{ $div->name_en }}"></td>
                                                    <td><input type="text" class="form-control form-control-sm" name="divisions[{{ $div->id }}][bn]" value="{{ $div->name_bn }}"></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3 bg-light text-end">
                                    <button type="submit" class="btn btn--primary btn-sm">@lang('Update Divisions')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card b-radius--10 border-0 shadow-sm">
                        <div class="card-header bg-white py-3 px-4 border-0">
                            <h6 class="mb-0 fw-bold"><i class="las la-edit me-1"></i> @lang('Quick Edit: Districts')</h6>
                        </div>
                        <div class="card-body p-0">
                            @if($divisionId)
                                <form action="{{ route('admin.frontend.sections.district.update') }}" method="POST">
                                    @csrf
                                    <div class="table-responsive" style="max-height: 300px;">
                                        <table class="table table--light style--two table-sm mb-0">
                                            <thead>
                                                <tr><th>EN</th><th>BN</th></tr>
                                            </thead>
                                            <tbody>
                                                @foreach($districts->where('division_id', $divisionId) as $d)
                                                    <tr>
                                                        <td><input type="text" class="form-control form-control-sm" name="districts[{{ $d->id }}][en]" value="{{ $d->name_en }}"></td>
                                                        <td><input type="text" class="form-control form-control-sm" name="districts[{{ $d->id }}][bn]" value="{{ $d->name_bn }}"></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="p-3 bg-light text-end">
                                        <button type="submit" class="btn btn--primary btn-sm">@lang('Update Districts')</button>
                                    </div>
                                </form>
                            @else
                                <div class="p-5 text-center text-muted small">@lang('Select a division above to edit district names.')</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Thana Row Template --}}
<table class="d-none">
    <tbody id="thanaTemplate">
        <tr class="thana-row">
            <td class="fw-bold SL"></td>
            <td>
                <input type="text" class="form-control" name="thanas[__i__][en]" placeholder="English Name" required>
            </td>
            <td>
                <input type="text" class="form-control" name="thanas[__i__][bn]" placeholder="বাংলা নাম" required>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline--danger removeThanaBtn">
                    <i class="las la-trash"></i>
                </button>
            </td>
        </tr>
    </tbody>
</table>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.frontend.sections.district') }}" class="btn btn-outline--primary btn-sm me-1">@lang('Location Hub')</a>
    <a href="{{ route('admin.frontend.templates') }}" class="btn btn-outline--secondary btn-sm">@lang('Templates')</a>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";

        // Dropdown Chaining
        $('#selDivision, #selDistrict').on('change', function() {
            var url = $(this).find(':selected').data('url');
            if (url) window.location = url;
        });

        // Add Thana
        $('#btnAddThana').on('click', function() {
            var tbody = $('#thanaRows');
            var template = $('#thanaTemplate .thana-row').clone();
            var index = tbody.find('.thana-row').length;
            
            if(tbody.find('.empty-row').length > 0) {
                tbody.empty();
            }

            template.find('.SL').text(index + 1);
            template.find('input').each(function() {
                var name = $(this).attr('name').replace('__i__', index);
                $(this).attr('name', name);
            });
            tbody.append(template);
        });

        // Remove Thana
        $(document).on('click', '.removeThanaBtn', function() {
            $(this).closest('.thana-row').remove();
            $('#thanaRows .thana-row').each(function(i) {
                $(this).find('.SL').text(i + 1);
                $(this).find('input').each(function() {
                    var name = $(this).attr('name').replace(/thanas\[\d+\]/, 'thanas[' + i + ']');
                    $(this).attr('name', name);
                });
            });
            if($('#thanaRows .thana-row').length === 0) {
                $('#thanaRows').append('<tr class="empty-row"><td colspan="4" class="text-center py-5 text-muted"><i class="las la-exclamation-circle fs-1 d-block mb-2 opacity-50"></i>@lang("No thanas found. Click \"Add New Thana\" to start.")</td></tr>');
            }
        });
    })(jQuery);
</script>
@endpush

@push('style')
<style>
    .border--primary-light { border-color: #e2e8f0 !important; }
    .border--primary-light:focus { border-color: #4634ff !important; box-shadow: 0 0 0 0.25rem rgba(70, 52, 255, 0.1) !important; }
    .style--two thead th { background: #f8f9fa; color: #6c757d; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 12px 15px; }
    .style--two tbody td { padding: 12px 15px; vertical-align: middle; border-bottom: 1px solid #f1f1f1; }
    .card-header.bg--primary { background: linear-gradient(45deg, #4634ff, #7367f0) !important; }
    .card-header.bg--dark { background: #34495e !important; }
    .btn-lg { padding: 12px 30px; border-radius: 8px; font-weight: 600; }
    .form-select-lg { font-size: 0.95rem; }
</style>
@endpush