@extends('admin.layouts.app')

@section('panel')
    {{-- Nav --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a href="{{ route('admin.shipping.index') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-th-large"></i> @lang('Hub')</a>
                <span class="btn btn-sm btn-primary text-white"><i class="las la-map-marked-alt"></i> @lang('Zones')</span>
                <a href="{{ route('admin.shipping.methods.index') }}" class="btn btn-sm btn-outline-primary"><i class="las la-shipping-fast"></i> @lang('Methods')</a>
                <a href="{{ route('admin.shipping.rules.index') }}" class="btn btn-sm btn-outline-info text-dark"><i class="las la-cog"></i> @lang('Rules')</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card b-radius--10 shadow-sm border">
                <div class="card-header border-bottom bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0 text-dark fw-semibold">@lang('All Zones')</h5>
                        <p class="text-muted small mb-0 mt-1">@lang('Delivery zones, countries & areas')</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-dark">@lang('Name')</th>
                                    <th class="text-dark">@lang('Type')</th>
                                    <th class="text-dark">@lang('Base Price')</th>
                                    <th class="text-dark">@lang('Countries')</th>
                                    <th class="text-dark">@lang('Areas')</th>
                                    <th class="text-dark">@lang('Methods')</th>
                                    <th class="text-dark">@lang('Status')</th>
                                    <th class="text-dark">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($zones as $zone)
                                    <tr>
                                        <td class="text-dark fw-medium">{{ __($zone->name) }} @if($zone->free_shipping ?? false)<span class="badge bg-success text-white ms-1">Free</span>@endif</td>
                                        <td><span class="badge {{ $zone->type === 'international' ? 'bg-info text-white' : 'bg-primary text-white' }}">{{ ucfirst($zone->type) }}</span></td>
                                        <td class="text-dark">{{ $general->cur_sym }}{{ showAmount($zone->base_price) }}</td>
                                        <td class="text-dark">{{ $zone->countries_count }}</td>
                                        <td class="text-dark">{{ $zone->areas_count }}</td>
                                        <td class="text-dark">{{ $zone->methods_count }}</td>
                                        <td>@php echo $zone->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.shipping.zones.edit', $zone->id) }}" class="btn btn-sm btn-outline-primary"><i class="la la-pencil"></i> @lang('Edit')</a>
                                                <button type="button" class="btn btn-sm btn-outline-{{ $zone->status == 1 ? 'danger' : 'success' }} confirmationBtn" data-action="{{ route('admin.shipping.zones.status', $zone->id) }}" data-question="{{ $zone->status == 1 ? __('Are you sure to disable this zone?') : __('Are you sure to enable this zone?') }}">
                                                    {{ $zone->status == 1 ? __('Disable') : __('Enable') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center py-4" colspan="8">@lang('No zones yet.') @lang('Add Zone') to add countries or areas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($zones->hasPages())
                    <div class="card-footer py-4 border-top bg-white">{{ paginateLinks($zones) }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- What you can do --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border bg-white shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-dark fw-semibold mb-2">@lang('What you can do')</h6>
                    <ul class="mb-0 ps-3 text-secondary small" style="line-height:1.6;">
                        <li>Create zones (National BD / International). Add countries or areas with <i class="las la-plus"></i>.</li>
                        <li>Set base price per zone; optional charge per country or per area. Free shipping per zone/area for campaigns.</li>
                        <li>Edit, enable or disable zones.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.shipping.zones.create') }}" class="btn btn-sm btn-primary text-white h-45"><i class="las la-plus"></i> @lang('Add Zone')</a>
@endpush
