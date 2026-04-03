@extends('admin.layouts.app')
@section('panel')
@php $general = $general ?? gs(); @endphp
<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card b-radius--10 border-0 shadow-sm">
            <div class="card-header bg--primary py-3 px-4">
                <h5 class="mb-0 text-white"><i class="las la-truck me-2"></i> {{ $zone ? __('Edit Delivery Zone') : __('Add Delivery Zone') }}</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ $zone ? route('admin.locations.delivery.update', $zone->id) : route('admin.locations.delivery.store') }}" method="POST">
                    @csrf
                    @if($zone) @method('PUT') @endif
                    <div class="mb-3">
                        <label class="form-label">@lang('Thana') <span class="text-danger">*</span></label>
                        <select name="thana_id" class="form-select" required {{ $zone ? 'readonly disabled' : '' }}>
                            <option value="">@lang('Select Thana')</option>
                            @foreach($thanas as $t)
                                <option value="{{ $t->id }}" {{ old('thana_id', $zone->thana_id ?? '') == $t->id ? 'selected' : '' }}>{{ $t->district->name_en ?? '' }} → {{ $t->name_en }}</option>
                            @endforeach
                        </select>
                        @if($zone)
                            <input type="hidden" name="thana_id" value="{{ $zone->thana_id }}">
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Delivery Charge') <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" name="delivery_charge" required value="{{ old('delivery_charge', $zone ? getAmount($zone->delivery_charge) : '0') }}">
                        <small class="text-muted">{{ $general->cur_sym ?? '৳' }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">@lang('Estimated Days')</label>
                        <input type="text" class="form-control" name="estimated_days" maxlength="50" value="{{ old('estimated_days', $zone->estimated_days ?? '') }}" placeholder="e.g. 2-3 days">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn--primary">{{ $zone ? __('Update') : __('Add') }}</button>
                        <a href="{{ route('admin.locations.index') }}" class="btn btn-outline--secondary">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
