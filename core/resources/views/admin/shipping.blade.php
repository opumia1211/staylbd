@extends('admin.layouts.app')

@section('panel')
    @php
        $hasExpressCol = \Illuminate\Support\Facades\Schema::hasColumn('shipping_methods', 'is_express');
        $totalMethods = \App\Models\ShippingMethod::count();
        $activeMethods = \App\Models\ShippingMethod::where('status', 1)->count();
        $expressMethods = $hasExpressCol ? \App\Models\ShippingMethod::where('is_express', 1)->count() : 0;
        $hasZonesTable = $hasZonesTable ?? \Illuminate\Support\Facades\Schema::hasTable('shipping_zones');
    @endphp

    {{-- Nav --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a href="{{ route('admin.shipping.index') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-th-large"></i> @lang('Hub')</a>
                <a href="{{ route('admin.shipping.zones.index') }}" class="btn btn-sm btn-outline-primary"><i class="las la-map-marked-alt"></i> @lang('Zones')</a>
                <span class="btn btn-sm btn-success text-white"><i class="las la-shipping-fast"></i> @lang('Methods')</span>
                <a href="{{ route('admin.shipping.rules.index') }}" class="btn btn-sm btn-outline-info text-dark"><i class="las la-cog"></i> @lang('Rules')</a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card b-radius--10 border shadow-sm bg-white">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small">@lang('Total Methods')</span>
                        <h4 class="mb-0 mt-1 text-dark">{{ $totalMethods }}</h4>
                    </div>
                    <i class="las la-truck text-primary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card b-radius--10 border shadow-sm bg-white">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small">@lang('Active')</span>
                        <h4 class="mb-0 mt-1 text-dark">{{ $activeMethods }}</h4>
                    </div>
                    <i class="las la-check-circle text-success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card b-radius--10 border shadow-sm bg-white">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small">@lang('Express')</span>
                        <h4 class="mb-0 mt-1 text-dark">{{ $expressMethods }}</h4>
                    </div>
                    <i class="las la-bolt text-info" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <form action="{{ route('admin.shipping.methods.index') }}" method="GET" class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <label class="mb-0 text-secondary small">@lang('Status')</label>
                <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">@lang('All')</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>@lang('Enabled')</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>@lang('Disabled')</option>
                </select>
                @if($zones->isNotEmpty())
                <label class="mb-0 text-secondary small">@lang('Zone')</label>
                <select name="zone_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">@lang('All Zones')</option>
                    @foreach($zones as $z)
                        <option value="{{ $z->id }}" {{ request('zone_id') == $z->id ? 'selected' : '' }}>{{ $z->name }} ({{ $z->type }})</option>
                    @endforeach
                </select>
                @endif
                <label class="mb-0 text-secondary small">@lang('Express')</label>
                <select name="express" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">@lang('All')</option>
                    <option value="1" {{ request('express') === '1' ? 'selected' : '' }}>@lang('Yes')</option>
                    <option value="0" {{ request('express') === '0' ? 'selected' : '' }}>@lang('No')</option>
                </select>
                <label class="mb-0 text-dark small fw-medium">@lang('Per page')</label>
                <select name="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    @foreach([10, 20, 50, 100] as $n)
                        <option value="{{ $n }}" {{ (int)request('per_page', getPaginate()) === $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
                <span class="text-dark small ms-2">@lang('Total'): <strong>{{ $shippings->total() }}</strong></span>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-md-12">
            <div class="card b-radius--10 border shadow-sm">
                <div class="card-header border-bottom bg-white">
                    <h5 class="mb-0 text-dark fw-semibold">@lang('Shipping Methods')</h5>
                    <p class="text-muted small mb-0 mt-1">@lang('Price & delivery time')</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-dark">@lang('Name')</th>
                                    @if($hasZonesTable)<th class="text-dark">@lang('Zone')</th>@endif
                                    <th class="text-dark">@lang('Price')</th>
                                    <th class="text-dark">@lang('Est. Days')</th>
                                    <th class="text-dark">@lang('Status')</th>
                                    <th class="text-dark">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shippings as $shipping)
                                    <tr>
                                        <td class="text-dark">
                                            {{ __($shipping->name) }}
                                            @if($hasExpressCol && $shipping->is_express)
                                                <span class="badge bg-info text-white">@lang('Express')</span>
                                            @endif
                                        </td>
                                        @if($hasZonesTable)
                                        <td>
                                            @if(isset($shipping->zone) && $shipping->zone)
                                                <span class="badge bg-dark text-white">{{ $shipping->zone->name }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        @endif
                                        <td class="text-dark">{{ $general->cur_sym }}{{ showAmount($shipping->price) }}</td>
                                        <td class="text-dark">{{ $shipping->estimated_days ?? '—' }}</td>
                                        <td>@php echo $shipping->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                @php $shippingResource = $shipping->only(['id','name','price','shipping_zone_id','estimated_days','courier_name','is_express']); @endphp
                                                <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-resource='@json($shippingResource)' data-modal_title="@lang('Edit Shipping Method')">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </button>

                                                @if ($shipping->status == Status::DISABLE)
                                                    <button type="button" class="btn btn-sm btn-outline--success confirmationBtn" data-action="{{ route('admin.shipping.status', $shipping->id) }}" data-question="@lang('Are you sure to enable this shipping method?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.shipping.status', $shipping->id) }}" data-question="@lang('Are you sure to disable this shipping method?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center py-4" colspan="{{ $hasZonesTable ? 6 : 5 }}">
                                            {{ __($emptyMessage ?? 'No shipping methods found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($shippings->hasPages())
                    <div class="card-footer py-4 border-top bg-white">
                        {{ paginateLinks($shippings) }}
                    </div>
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
                        <li>Add methods (name, price, ETA). Link to zone. Mark express.</li>
                        <li>Filter by status, zone, express. Edit or enable/disable.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    {{-- Create or Update Modal --}}
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="cuModalTitle" data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.shipping.store') }}" method="POST" data-base-action="{{ route('admin.shipping.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                @if($hasZonesTable)
                                <div class="form-group">
                                    <label>@lang('Zone') (optional)</label>
                                    <select name="shipping_zone_id" class="form-select">
                                        <option value="">— @lang('Legacy / No zone') —</option>
                                        @foreach(\App\Models\ShippingZone::where('status', 1)->orderBy('type')->orderBy('name')->get() as $z)
                                            <option value="{{ $z->id }}">{{ $z->name }} ({{ $z->type }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Amount') / @lang('Base Price')</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="price" min="0" value="{{ old('price') }}" required>
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Estimated Days') (e.g. 2-3 Days)</label>
                                    <input type="text" class="form-control" name="estimated_days" value="{{ old('estimated_days') }}" placeholder="2-3 Days">
                                </div>
                                <div class="form-group">
                                    <label>@lang('Courier Name')</label>
                                    <input type="text" class="form-control" name="courier_name" value="{{ old('courier_name') }}" placeholder="e.g. Steadfast, Pathao">
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_express" value="1" id="is_express">
                                    <label class="form-check-label" for="is_express">@lang('Express delivery')</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="@lang('Search by name...')" />
    <button type="button" class="btn btn-sm btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New Shipping Method')">
        <i class="las la-plus"></i> @lang('Add New')
    </button>
@endpush

@push('style')
    <style>
        #cuModal { z-index: 10600 !important; position: fixed !important; }
        #cuModal .modal-dialog { z-index: 10602 !important; }
        body.modal-open .modal-backdrop { z-index: 10598 !important; }
        .shipping-hub-icon { min-width: 56px; text-align: center; }
        .hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; }
        .shipping-hub-icon.bg--primary.bg-opacity-10 { background: rgba(13, 110, 253, 0.12); }
        .shipping-hub-icon.bg--info.bg-opacity-10 { background: rgba(23, 162, 184, 0.12); }
        .shipping-hub-icon.bg--primary.bg-opacity-25 { background: rgba(13, 110, 253, 0.2); }
        .card.bg--primary.bg-opacity-5 { background: rgba(13, 110, 253, 0.06); }
    </style>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('script')
    <script>
        $(function() {
            var m = document.getElementById('cuModal');
            if (m && m.parentNode && m.parentNode !== document.body) document.body.appendChild(m);
        });
    </script>
@endpush
