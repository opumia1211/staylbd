@extends('admin.layouts.app')

@section('panel')
    @php $emptyMessage = $emptyMessage ?? __('No automatic gateways found.'); @endphp
    <div class="row">
        <div class="col-12 mb-4">
            <div class="row g-3">
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 gateway-stat-card">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="gateway-stat-icon rounded-circle bg--primary bg-opacity-10 d-flex align-items-center justify-content-center">
                                <i class="las la-credit-card fs-3 text--primary"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block">@lang('Total Gateways')</span>
                                <span class="fw-bold fs-5">{{ $gateways->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 gateway-stat-card">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="gateway-stat-icon rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center">
                                <i class="las la-check-circle fs-3 text-success"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block">@lang('Enabled')</span>
                                <span class="fw-bold fs-5">{{ $gateways->where('status', Status::ENABLE)->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 gateway-stat-card">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="gateway-stat-icon rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center">
                                <i class="las la-coins fs-3 text-secondary"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block">@lang('Total Currencies')</span>
                                <span class="fw-bold fs-5">{{ $gateways->sum(fn($g) => $g->currencies->count()) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 gateway-stat-card">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="gateway-stat-icon rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center">
                                <i class="las la-globe fs-3 text-info"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block">@lang('Supported')</span>
                                <span class="fw-bold fs-5">{{ $gateways->sum(function($g) { return is_array($g->supported_currencies) ? count($g->supported_currencies) : collect($g->supported_currencies)->count(); }) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm b-radius--10">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="las la-list me-2"></i>@lang('Automatic Payment Gateways')</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle custom-data-table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('Gateway')</th>
                                    <th>@lang('Supported Currency')</th>
                                    <th>@lang('Enabled Currency')</th>
                                    <th>@lang('Status')</th>
                                    <th class="text-end">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gateways->sortBy('alias') as $gateway)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-medium">{{ __($gateway->name) }}</span>
                                            </div>
                                        </td>
                                        <td>{{ collect($gateway->supported_currencies)->count() }}</td>
                                        <td>{{ $gateway->currencies->count() }}</td>
                                        <td>@php echo $gateway->statusBadge; @endphp</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.gateway.automatic.edit', $gateway->alias) }}" class="btn btn-outline--primary">
                                                    <i class="las la-pen"></i> @lang('Edit')
                                                </a>
                                                @if($gateway->status == Status::DISABLE)
                                                    <button class="btn btn-outline--success confirmationBtn" data-question="@lang('Are you sure to enable this gateway?')" data-action="{{ route('admin.gateway.automatic.status',$gateway->id) }}">
                                                        <i class="las la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to disable this gateway?')" data-action="{{ route('admin.gateway.automatic.status',$gateway->id) }}">
                                                        <i class="las la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center text-muted py-5" colspan="5">
                                            <i class="las la-credit-card fs-1 opacity-50 d-block mb-2"></i>
                                            {{ __($emptyMessage) }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="d-inline">
        <div class="input-group">
            <input type="text" name="search_table" class="form-control bg--white" placeholder="@lang('Search')...">
            <button class="btn btn--primary input-group-text"><i class="las la-search"></i></button>
        </div>
    </div>
@endpush

@push('style')
<style>
.gateway-stat-icon { width: 48px; height: 48px; }
.gateway-stat-card .card-body { padding: 1rem 1.25rem; }
</style>
@endpush
