@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        <div class="card b-radius--10 border-0 shadow-sm mb-4">
            <div class="card-header bg--primary py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="mb-0 text-white"><i class="las la-map-marked-alt me-2"></i> @lang('Location Management') — 🇧🇩 @lang('Bangladesh')</h5>
                <div class="d-flex flex-wrap gap-1">
                    <a href="{{ route('admin.frontend.sections.district') }}" class="btn btn-sm btn-outline-light">@lang('Thana by District (Legacy)')</a>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs nav-tabs--location mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-divisions">@lang('Divisions') ({{ $divisions->count() }})</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-districts">@lang('Districts') ({{ $districts->count() }})</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-thanas">@lang('Thanas') ({{ $thanas->count() }})</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-delivery">@lang('Delivery Charges') ({{ $deliveryZones->count() }})</a>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Divisions --}}
            <div class="tab-pane fade show active" id="tab-divisions">
                <div class="card b-radius--10 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold">@lang('Divisions') / বিভাগ <span class="badge bg--primary ms-1">{{ $divisions->count() }} @lang('total')</span></h6>
                        <a href="{{ route('admin.locations.division.create') }}" class="btn btn--primary btn-sm">
                            <i class="las la-plus"></i> @lang('Add Division')
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table--light style--two mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:50px">#</th>
                                        <th>@lang('Name (EN)')</th>
                                        <th>@lang('Name (BN)')</th>
                                        <th style="width:100px">@lang('Status')</th>
                                        <th style="width:140px">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($divisions as $d)
                                    <tr>
                                        <td class="fw-medium">{{ $loop->iteration }}</td>
                                        <td>{{ $d->name_en }}</td>
                                        <td>{{ $d->name_bn }}</td>
                                        <td>
                                            @if(\Illuminate\Support\Facades\Schema::hasColumn('divisions', 'status'))
                                            <form action="{{ route('admin.locations.division.toggle', $d->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $d->status ? 'btn--success' : 'btn--danger' }}">{{ $d->status ? __('Active') : __('Inactive') }}</button>
                                            </form>
                                            @else
                                            <span class="badge bg--success">@lang('Active')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.locations.division.edit', $d->id) }}" class="btn btn-sm btn-outline--primary"><i class="las la-edit"></i></a>
                                            <form action="{{ route('admin.locations.division.destroy', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('Delete this division?')');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline--danger"><i class="las la-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">@lang('No divisions. Click "Add Division" to add.')</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Districts --}}
            <div class="tab-pane fade" id="tab-districts">
                <div class="card b-radius--10 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold">@lang('Districts') / জেলা <span class="badge bg--primary ms-1">{{ $districts->count() }} @lang('total')</span></h6>
                        <a href="{{ route('admin.locations.district.create') }}" class="btn btn--primary btn-sm">
                            <i class="las la-plus"></i> @lang('Add District')
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table--light style--two mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:50px">#</th>
                                        <th>@lang('Division')</th>
                                        <th>@lang('Name (EN)')</th>
                                        <th>@lang('Name (BN)')</th>
                                        <th style="width:100px">@lang('Status')</th>
                                        <th style="width:140px">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($districts as $d)
                                    <tr>
                                        <td class="fw-medium">{{ $loop->iteration }}</td>
                                        <td>{{ $d->division->name_en ?? '-' }}</td>
                                        <td>{{ $d->name_en }}</td>
                                        <td>{{ $d->name_bn }}</td>
                                        <td>
                                            @if(\Illuminate\Support\Facades\Schema::hasColumn('districts', 'status'))
                                            <form action="{{ route('admin.locations.district.toggle', $d->id) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-sm {{ $d->status ? 'btn--success' : 'btn--danger' }}">{{ $d->status ? __('Active') : __('Inactive') }}</button></form>
                                            @else
                                            <span class="badge bg--success">@lang('Active')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.locations.district.edit', $d->id) }}" class="btn btn-sm btn-outline--primary"><i class="las la-edit"></i></a>
                                            <form action="{{ route('admin.locations.district.destroy', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('Delete this district?')');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline--danger"><i class="las la-trash"></i></button></form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">@lang('No districts. Add divisions first, then "Add District".')</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thanas --}}
            <div class="tab-pane fade" id="tab-thanas">
                <div class="card b-radius--10 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold">@lang('Thanas') / থানা <span class="badge bg--primary ms-1">{{ $thanas->count() }} @lang('total')</span></h6>
                        <a href="{{ route('admin.locations.thana.create') }}" class="btn btn--primary btn-sm">
                            <i class="las la-plus"></i> @lang('Add Thana')
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height:400px">
                            <table class="table table--light style--two mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:50px">#</th>
                                        <th>@lang('District')</th>
                                        <th>@lang('Name (EN)')</th>
                                        <th>@lang('Name (BN)')</th>
                                        <th>@lang('Postal Code')</th>
                                        <th style="width:100px">@lang('Status')</th>
                                        <th style="width:140px">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($thanas as $t)
                                    <tr>
                                        <td class="fw-medium">{{ $loop->iteration }}</td>
                                        <td>{{ $t->district->name_en ?? '-' }}</td>
                                        <td>{{ $t->name_en }}</td>
                                        <td>{{ $t->name_bn }}</td>
                                        <td>{{ $t->postal_code ?? '-' }}</td>
                                        <td>
                                            @if(\Illuminate\Support\Facades\Schema::hasColumn('thanas', 'status'))
                                            <form action="{{ route('admin.locations.thana.toggle', $t->id) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-sm {{ $t->status ? 'btn--success' : 'btn--danger' }}">{{ $t->status ? __('Active') : __('Inactive') }}</button></form>
                                            @else
                                            <span class="badge bg--success">@lang('Active')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.locations.thana.edit', $t->id) }}" class="btn btn-sm btn-outline--primary"><i class="las la-edit"></i></a>
                                            <form action="{{ route('admin.locations.thana.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('Delete this thana?')');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline--danger"><i class="las la-trash"></i></button></form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">@lang('No thanas. Add districts first, then "Add Thana".')</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Delivery Zones --}}
            <div class="tab-pane fade" id="tab-delivery">
                <div class="card b-radius--10 border-0 shadow-sm">
                    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold">@lang('Delivery Charges by Thana') <span class="badge bg--primary ms-1">{{ $deliveryZones->count() }} @lang('total')</span></h6>
                        <a href="{{ route('admin.locations.delivery.create') }}" class="btn btn--primary btn-sm">
                            <i class="las la-plus"></i> @lang('Add Delivery Zone')
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table--light style--two mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:50px">#</th>
                                        <th>@lang('Thana')</th>
                                        <th>@lang('District')</th>
                                        <th>@lang('Charge')</th>
                                        <th>@lang('Est. Days')</th>
                                        <th style="width:100px">@lang('Status')</th>
                                        <th style="width:140px">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deliveryZones as $z)
                                    <tr>
                                        <td class="fw-medium">{{ $loop->iteration }}</td>
                                        <td>{{ $z->thana->name_en ?? '-' }}</td>
                                        <td>{{ $z->thana->district->name_en ?? '-' }}</td>
                                        <td>{{ $general->cur_sym ?? '৳' }}{{ getAmount($z->delivery_charge) }}</td>
                                        <td>{{ $z->estimated_days ?? '-' }}</td>
                                        <td>
                                            <form action="{{ route('admin.locations.delivery.toggle', $z->id) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-sm {{ $z->status ? 'btn--success' : 'btn--danger' }}">{{ $z->status ? __('Active') : __('Inactive') }}</button></form>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.locations.delivery.edit', $z->id) }}" class="btn btn-sm btn-outline--primary"><i class="las la-edit"></i></a>
                                            <form action="{{ route('admin.locations.delivery.destroy', $z->id) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('Delete this delivery zone?')');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline--danger"><i class="las la-trash"></i></button></form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">@lang('No delivery zones. Add thanas first, then "Add Delivery Zone".')</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

