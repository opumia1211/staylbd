@extends('admin.layouts.app')

@section('panel')
    @php
        $emptyMessage = $emptyMessage ?? __('No manual gateways found. Add one to accept payments from any country or method.');
        $totalCount = $gateways->count();
        $activeCount = $gateways->where('status', Status::ENABLE)->count();
        $disabledCount = $gateways->where('status', Status::DISABLE)->count();
        $currencyCount = $gateways->filter(fn($g) => $g->singleCurrency && !empty($g->singleCurrency->currency))->pluck('singleCurrency.currency')->unique()->count();
    @endphp
    <div class="manual-gateway-page">
        {{-- Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm manual-stat-card h-100 overflow-hidden">
                    <div class="card-body d-flex align-items-center gap-3 p-3 p-md-4">
                        <div class="manual-stat-icon rounded-3 d-flex align-items-center justify-content-center bg--primary bg-opacity-10">
                            <i class="las la-money-bill-wave fs-2 text--primary"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <span class="text-muted small text-uppercase fw-semibold d-block">@lang('Manual Methods')</span>
                            <span class="fw-bold fs-4 text-dark">{{ $totalCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm manual-stat-card h-100 overflow-hidden">
                    <div class="card-body d-flex align-items-center gap-3 p-3 p-md-4">
                        <div class="manual-stat-icon rounded-3 d-flex align-items-center justify-content-center bg-success bg-opacity-10">
                            <i class="las la-check-circle fs-2 text-success"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <span class="text-muted small text-uppercase fw-semibold d-block">@lang('Active')</span>
                            <span class="fw-bold fs-4 text-dark">{{ $activeCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm manual-stat-card h-100 overflow-hidden">
                    <div class="card-body d-flex align-items-center gap-3 p-3 p-md-4">
                        <div class="manual-stat-icon rounded-3 d-flex align-items-center justify-content-center bg-secondary bg-opacity-10">
                            <i class="las la-pause-circle fs-2 text-secondary"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <span class="text-muted small text-uppercase fw-semibold d-block">@lang('Disabled')</span>
                            <span class="fw-bold fs-4 text-dark">{{ $disabledCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm manual-stat-card h-100 overflow-hidden">
                    <div class="card-body d-flex align-items-center gap-3 p-3 p-md-4">
                        <div class="manual-stat-icon rounded-3 d-flex align-items-center justify-content-center bg-info bg-opacity-10">
                            <i class="las la-coins fs-2 text-info"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <span class="text-muted small text-uppercase fw-semibold d-block">@lang('Currencies')</span>
                            <span class="fw-bold fs-4 text-dark">{{ $currencyCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main card --}}
        <div class="card border-0 shadow-sm manual-gateway-card">
            <div class="card-header bg-light border-0 py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0 fw-bold text-dark"><i class="las la-list-ul me-2 text--primary"></i>@lang('Manual Payment Gateways')</h5>
                    <span class="badge bg--primary bg-opacity-10 text--primary">{{ $totalCount }} @lang('total')</span>
                </div>
                <p class="mb-0 small text-muted d-none d-md-block">@lang('Accept any payment from anywhere in the world')</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 manual-gateway-table" id="manualGatewayTable">
                        <thead>
                            <tr>
                                <th class="border-0 bg-light fw-semibold text-muted">#</th>
                                <th class="border-0 bg-light fw-semibold text-muted">@lang('Method')</th>
                                <th class="border-0 bg-light fw-semibold text-muted">@lang('Currency')</th>
                                <th class="border-0 bg-light fw-semibold text-muted">@lang('Rate')</th>
                                <th class="border-0 bg-light fw-semibold text-muted">@lang('Limit')</th>
                                <th class="border-0 bg-light fw-semibold text-muted">@lang('Charge')</th>
                                <th class="border-0 bg-light fw-semibold text-muted">@lang('Status')</th>
                                <th class="border-0 bg-light fw-semibold text-muted text-end">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gateways as $index => $gateway)
                                @php $cur = $gateway->singleCurrency; @endphp
                                <tr class="manual-gateway-row" data-status="{{ $gateway->status }}">
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="manual-method-name fw-medium">{{ __($gateway->name) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($cur)
                                            <span class="badge bg-light text-dark border">{{ $cur->currency }}{!! $cur->symbol ? ' <span class="opacity-75">' . e($cur->symbol) . '</span>' : '' !!}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cur)
                                            <small class="text-muted">1 {{ __($general->cur_text ?? 'USD') }} = {{ getAmount($cur->rate) }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cur)
                                            <small>{{ getAmount($cur->min_amount) }} – {{ getAmount($cur->max_amount) }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cur)
                                            <small>{{ getAmount($cur->fixed_charge) }} + {{ getAmount($cur->percent_charge) }}%</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>@php echo $gateway->statusBadge; @endphp</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.gateway.manual.edit', $gateway->alias) }}" class="btn btn-outline--primary" title="@lang('Edit')">
                                                <i class="las la-pen"></i>
                                            </a>
                                            @if($gateway->status == Status::DISABLE)
                                                <button type="button" class="btn btn-outline--success confirmationBtn" data-question="@lang('Are you sure to enable this gateway?')" data-action="{{ route('admin.gateway.manual.status',$gateway->id) }}" title="@lang('Enable')">
                                                    <i class="las la-eye"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to disable this gateway?')" data-action="{{ route('admin.gateway.manual.status',$gateway->id) }}" title="@lang('Disable')">
                                                    <i class="las la-eye-slash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="manual-empty-state">
                                            <i class="las la-money-bill-wave manual-empty-icon"></i>
                                            <h6 class="mt-3 mb-2">@lang('No manual gateways yet')</h6>
                                            <p class="text-muted small mb-4">{{ __($emptyMessage) }}</p>
                                            <a href="{{ route('admin.gateway.manual.create') }}" class="btn btn--primary"><i class="las la-plus me-1"></i>@lang('Add Manual Gateway')</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap align-items-center gap-2">
        <div class="input-group input-group-sm search-form" style="max-width: 220px;">
            <span class="input-group-text bg-white border-end-0"><i class="las la-search text-muted"></i></span>
            <input type="text" name="search_table" class="form-control border-start-0 ps-0" placeholder="@lang('Search methods')..." id="searchManualGateway">
        </div>
        <a class="btn btn--primary btn-sm" href="{{ route('admin.gateway.manual.create') }}"><i class="las la-plus me-1"></i>@lang('Add New')</a>
    </div>
@endpush

@push('style')
<style>
.manual-gateway-page { padding-bottom: 1rem; }
.manual-stat-card { border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s; }
.manual-stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important; }
.manual-stat-icon { width: 48px; height: 48px; flex-shrink: 0; }
.manual-gateway-card { border-radius: 12px; overflow: hidden; }
.manual-gateway-table thead th { padding: 0.75rem 1rem; font-size: 0.75rem; }
.manual-gateway-table tbody td { padding: 0.75rem 1rem; vertical-align: middle; }
.manual-gateway-row:hover { background-color: rgba(0,0,0,0.02); }
.manual-empty-state { max-width: 320px; margin: 0 auto; }
.manual-empty-icon { font-size: 4rem; opacity: 0.2; color: var(--bs-primary); }
</style>
@endpush

@push('script')
<script>
(function() {
    var table = document.getElementById('manualGatewayTable');
    var search = document.getElementById('searchManualGateway');
    if (!table || !search) return;
    search.addEventListener('input', function() {
        var q = (this.value || '').toLowerCase();
        var rows = table.querySelectorAll('tbody tr.manual-gateway-row');
        rows.forEach(function(row) {
            var text = row.textContent.toLowerCase();
            row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
        });
    });
})();
</script>
@endpush
