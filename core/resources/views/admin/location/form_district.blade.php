@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card b-radius--10 border-0 shadow-sm">
            <div class="card-header bg--primary py-3 px-4">
                <h5 class="mb-0 text-white"><i class="las la-map-marked-alt me-2"></i> {{ $district ? __('Edit District') : __('Add District') }}</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ $district ? route('admin.locations.district.update', $district->id) : route('admin.locations.district.store') }}" method="POST">
                    @csrf
                    @if($district) @method('PUT') @endif
                    <div class="mb-3">
                        <label class="form-label">@lang('Division') <span class="text-danger">*</span></label>
                        <select name="division_id" class="form-select" required>
                            <option value="">@lang('Select Division')</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}" {{ old('division_id', $district->division_id ?? '') == $div->id ? 'selected' : '' }}>{{ $div->name_en }} / {{ $div->name_bn }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (English)') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name_en" required maxlength="100" value="{{ old('name_en', $district->name_en ?? '') }}" placeholder="e.g. Dhaka">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (Bangla)')</label>
                        <input type="text" class="form-control" name="name_bn" maxlength="100" value="{{ old('name_bn', $district->name_bn ?? '') }}" placeholder="e.g. ঢাকা">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn--primary">{{ $district ? __('Update') : __('Add') }}</button>
                        <a href="{{ route('admin.locations.index') }}" class="btn btn-outline--secondary">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
