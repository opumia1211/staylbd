@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a href="{{ route('admin.shipping.zones.index') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-arrow-left"></i> @lang('Back')</a>
                <a href="{{ route('admin.shipping.methods.index') }}" class="btn btn-sm btn-outline-primary"><i class="las la-shipping-fast"></i> @lang('Methods')</a>
                <a href="{{ route('admin.shipping.rules.index') }}" class="btn btn-sm btn-outline-info text-dark"><i class="las la-cog"></i> @lang('Rules')</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border shadow-sm b-radius--10">
                <div class="card-header border-bottom bg-white">
                    <h5 class="mb-0 text-dark fw-semibold">{{ isset($zone) ? __('Edit Zone') : __('New Zone') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ isset($zone) ? route('admin.shipping.zones.update', $zone->id) : route('admin.shipping.zones.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-medium">@lang('Zone Name')</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $zone->name ?? '') }}" required placeholder="e.g. Dhaka Metro, India">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-medium">@lang('Type')</label>
                                <select name="type" class="form-select" required>
                                    <option value="national" @selected(old('type', $zone->type ?? '') === 'national')>@lang('National') (BD)</option>
                                    <option value="international" @selected(old('type', $zone->type ?? '') === 'international')>@lang('International')</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-dark fw-medium">@lang('Base Price') ({{ __($general->cur_text) }})</label>
                                <input type="number" step="0.01" class="form-control" name="base_price" value="{{ old('base_price', $zone->base_price ?? 0) }}" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-dark fw-medium">@lang('Est. Days')</label>
                                <input type="text" class="form-control" name="estimated_days" value="{{ old('estimated_days', $zone->estimated_days ?? '') }}" placeholder="3-5">
                            </div>
                            <div class="col-md-4 d-flex align-items-end pb-2">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="free_shipping" value="0">
                                    <input type="checkbox" class="form-check-input" name="free_shipping" value="1" id="zone_free" @checked(old('free_shipping', $zone->free_shipping ?? false))>
                                    <label class="form-check-label text-dark" for="zone_free">@lang('Free shipping') (campaign)</label>
                                </div>
                            </div>
                            @if(isset($zone) && \Illuminate\Support\Facades\Schema::hasColumn('shipping_zones', 'cod_enabled'))
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="cod_enabled" value="0">
                                    <input type="checkbox" class="form-check-input" name="cod_enabled" value="1" id="zone_cod" @checked(old('cod_enabled', $zone->cod_enabled ?? true))>
                                    <label class="form-check-label text-dark" for="zone_cod">@lang('COD enabled for this zone')</label>
                                </div>
                            </div>
                            @endif
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary text-white"><i class="las la-save"></i> @lang('Save Zone')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(isset($zone))
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card border shadow-sm b-radius--10">
                <div class="card-header border-bottom bg-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 text-dark fw-semibold">@lang('Countries')</h5>
                    @if($zone->type === 'international')
                    <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="collapse" data-bs-target="#addCountryForm" aria-expanded="false"><i class="las la-plus"></i> @lang('Add')</button>
                    @endif
                </div>
                <div class="card-body">
                    @if($zone->type === 'international')
                    <div class="collapse mb-3" id="addCountryForm">
                        <form action="{{ route('admin.shipping.zones.country.add', $zone->id) }}" method="POST" class="row g-2">
                            @csrf
                            <div class="col-4"><input type="text" name="country_iso" class="form-control form-control-sm" placeholder="e.g. US, IN" maxlength="2" required></div>
                            <div class="col-4"><input type="text" name="country_name" class="form-control form-control-sm" placeholder="Name (opt)"></div>
                            <div class="col-3"><input type="number" step="0.01" name="shipping_price" class="form-control form-control-sm" placeholder="Charge (opt)" min="0"></div>
                            <div class="col-1"><button type="submit" class="btn btn-sm btn-primary text-white w-100"><i class="las la-plus"></i></button></div>
                        </form>
                    </div>
                    @endif
                    <ul class="list-group list-group-flush">
                        @forelse($zone->countries as $c)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-dark">{{ $c->country_iso }} — {{ $c->country_name ?? $c->country_iso }} @if($c->shipping_price !== null)<small class="text-muted">({{ $general->cur_sym }}{{ showAmount($c->shipping_price) }})</small>@endif</span>
                                <form action="{{ route('admin.shipping.zones.country.remove', [$zone->id, $c->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger confirmationBtn" data-question="@lang('Remove?')"><i class="las la-trash"></i></button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item text-muted px-0">@lang('No countries.') @if($zone->type === 'international') @lang('Use Add above.') @endif</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border shadow-sm b-radius--10">
                <div class="card-header border-bottom bg-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 text-dark fw-semibold">@lang('Areas') (National)</h5>
                    @if($zone->type === 'national')
                    <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="collapse" data-bs-target="#addAreaForm" aria-expanded="false"><i class="las la-plus"></i> @lang('Add')</button>
                    @endif
                </div>
                <div class="card-body">
                    @if($zone->type === 'national')
                    <div class="collapse mb-3" id="addAreaForm">
                        <form action="{{ route('admin.shipping.zones.area.add', $zone->id) }}" method="POST" class="row g-2">
                            @csrf
                            <div class="col-12"><input type="text" name="area_name" class="form-control form-control-sm" placeholder="@lang('Area name') (e.g. Dhaka Metro)" required></div>
                            <div class="col-12"><input type="text" name="district_names" class="form-control form-control-sm" placeholder="@lang('Districts comma-separated')"></div>
                            <div class="col-5"><input type="number" step="0.01" name="shipping_price" class="form-control form-control-sm" placeholder="Charge (opt)" min="0"></div>
                            <div class="col-4 d-flex align-items-center">
                                <div class="form-check form-switch mb-0">
                                    <input type="hidden" name="free_shipping" value="0">
                                    <input type="checkbox" class="form-check-input" name="free_shipping" value="1" id="area_free">
                                    <label class="form-check-label small" for="area_free">@lang('Free')</label>
                                </div>
                            </div>
                            <div class="col-3"><button type="submit" class="btn btn-sm btn-primary text-white w-100"><i class="las la-plus"></i></button></div>
                        </form>
                    </div>
                    @endif
                    <ul class="list-group list-group-flush">
                        @forelse($zone->areas as $a)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-dark">{{ $a->area_name }} @if(!empty($a->district_names))<small class="text-muted">({{ is_array($a->district_names) ? implode(', ', $a->district_names) : $a->district_names }})</small>@endif
                                    @if($a->free_shipping)<span class="badge bg-success text-white ms-1">Free</span>@elseif($a->shipping_price !== null)<small class="text-muted">{{ $general->cur_sym }}{{ showAmount($a->shipping_price) }}</small>@endif
                                </span>
                                <form action="{{ route('admin.shipping.zones.area.remove', [$zone->id, $a->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger confirmationBtn" data-question="@lang('Remove?')"><i class="las la-trash"></i></button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item text-muted px-0">@lang('No areas.') @if($zone->type === 'national') @lang('Use Add above.') @endif</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
    <x-confirmation-modal />
@endsection
