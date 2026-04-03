@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card b-radius--10 border-0 shadow-sm">
            <div class="card-header bg--primary py-3 px-4">
                <h5 class="mb-0 text-white"><i class="las la-map-marked-alt me-2"></i> {{ $thana ? __('Edit Thana') : __('Add Thana') }}</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ $thana ? route('admin.locations.thana.update', $thana->id) : route('admin.locations.thana.store') }}" method="POST">
                    @csrf
                    @if($thana) @method('PUT') @endif
                    <div class="mb-3">
                        <label class="form-label">@lang('District') <span class="text-danger">*</span></label>
                        <select name="district_id" class="form-select" required>
                            <option value="">@lang('Select District')</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}" {{ old('district_id', $thana->district_id ?? '') == $d->id ? 'selected' : '' }}>{{ $d->division->name_en ?? '' }} → {{ $d->name_en }} / {{ $d->name_bn }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (English)') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name_en" required maxlength="150" value="{{ old('name_en', $thana->name_en ?? '') }}" placeholder="e.g. Dhanmondi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Name (Bangla)')</label>
                        <input type="text" class="form-control" name="name_bn" maxlength="150" value="{{ old('name_bn', $thana->name_bn ?? '') }}" placeholder="e.g. ধানমন্ডি">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Postal Code')</label>
                        <input type="text" class="form-control" name="postal_code" maxlength="20" value="{{ old('postal_code', $thana->postal_code ?? '') }}" placeholder="e.g. 1209">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn--primary">{{ $thana ? __('Update') : __('Add') }}</button>
                        <a href="{{ route('admin.locations.index') }}" class="btn btn-outline--secondary">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
