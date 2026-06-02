@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h5 class="mb-0 text-dark fw-bold">@lang('Payment Center')</h5>
                <p class="text-muted small mb-0 mt-1">@lang('Gateways, deposits, analytics, COD & autopay in one place.')</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.gateway.manual.create') }}" class="btn btn-sm btn-primary"><i class="las la-plus"></i> @lang('Add Manual Gateway')</a>
                <a href="{{ route('admin.deposit.pending') }}" class="btn btn-sm btn-warning text-dark">
                    <i class="las la-clock"></i> @lang('Pending')
                    @if(($stats['pending_count'] ?? 0) > 0)
                        <span class="badge bg-dark ms-1">{{ $stats['pending_count'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.payment.analytics') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-chart-line"></i> @lang('Analytics')</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Total Revenue')</span>
                    <span class="fw-bold fs-4 text-primary">{{ showAmount($stats['total_revenue'] ?? 0) }}</span>
                    <small class="d-block text-muted mt-1">{{ $stats['successful_count'] ?? 0 }} @lang('successful payments')</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang("Today's Revenue")</span>
                    <span class="fw-bold fs-4 text-dark">{{ showAmount($stats['today_revenue'] ?? 0) }}</span>
                    <small class="d-block text-muted mt-1">@lang('This month'): {{ showAmount($stats['month_revenue'] ?? 0) }}</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Pending')</span>
                    <span class="fw-bold fs-4 text-warning">{{ $stats['pending_count'] ?? 0 }}</span>
                    <small class="d-block text-muted mt-1">{{ showAmount($stats['pending_amount'] ?? 0) }} @lang('awaiting')</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Success Rate')</span>
                    <span class="fw-bold fs-4 text-success">{{ $stats['success_rate'] ?? 0 }}%</span>
                    <small class="d-block text-muted mt-1">{{ $totalActiveGateways ?? 0 }} @lang('active gateways')</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @foreach($modules ?? [] as $module)
            @php
                $color = $module['color'] ?? 'primary';
                $routeParams = $module['route_params'] ?? [];
            @endphp
            <div class="col-md-4">
                <a href="{{ route($module['route'], $routeParams) }}" class="text-decoration-none d-block h-100">
                    <div class="card border shadow-sm h-100 gateway-hub-card bg-white">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-3 p-3 bg-{{ $color }} @if(in_array($color, ['warning','info'])) text-dark @else text-white @endif">
                                    <i class="las la-{{ $module['icon'] ?? 'credit-card' }} fs-2"></i>
                                </div>
                                @if(isset($module['count']))
                                    <span class="badge bg-{{ $color }} @if(in_array($color, ['warning','info'])) text-dark @endif">{{ $module['count'] }}</span>
                                @elseif(!empty($module['badge']))
                                    <span class="badge bg-label-{{ $color }}">{{ $module['badge'] }}</span>
                                @endif
                            </div>
                            <h6 class="card-title mb-2 text-dark fw-semibold">{{ $module['title'] }}</h6>
                            <p class="text-secondary small mb-0 flex-grow-1">{{ $module['description'] }}</p>
                            <span class="mt-3 small fw-semibold text-{{ $color }}">@lang('Open') <i class="las la-arrow-right ms-1"></i></span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    @if(!empty($recentPending) && $recentPending->isNotEmpty())
    <div class="row mt-4">
        <div class="col-lg-7">
            <div class="card border shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-dark fw-semibold">@lang('Recent Pending Payments')</h6>
                    <a href="{{ route('admin.deposit.pending') }}" class="btn btn-sm btn-outline-warning text-dark">@lang('View all')</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>@lang('TRX')</th><th>@lang('User')</th><th>@lang('Amount')</th><th></th></tr></thead>
                        <tbody>
                            @foreach($recentPending as $dep)
                            <tr>
                                <td><code class="small">{{ $dep->trx }}</code></td>
                                <td class="small">{{ $dep->user->username ?? '—' }}</td>
                                <td class="fw-semibold">{{ showAmount($dep->final_amo) }}</td>
                                <td><a href="{{ route('admin.deposit.details', $dep->id) }}" class="btn btn-xs btn-outline-primary btn-sm">@lang('Details')</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if(!empty($topGateways) && $topGateways->isNotEmpty())
        <div class="col-lg-5">
            <div class="card border shadow-sm h-100">
                <div class="card-header"><h6 class="mb-0 text-dark fw-semibold">@lang('Top Gateways (30 days)')</h6></div>
                <ul class="list-group list-group-flush">
                    @foreach($topGateways as $gw)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="small">{{ $gatewayNames[$gw->method_code] ?? __('Gateway') }} #{{ $gw->method_code }}</span>
                        <span class="badge bg-label-success">{{ showAmount($gw->revenue) }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border bg-light">
                <div class="card-body py-3">
                    <h6 class="text-dark fw-semibold mb-2">@lang('Quick links')</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($quickLinks ?? [] as $link)
                            <a href="{{ route($link['route']) }}" class="btn btn-sm btn-{{ $link['variant'] ?? 'outline-primary' }}">
                                <i class="las la-{{ $link['icon'] ?? 'link' }}"></i> {{ $link['label'] }}
                                @if(!empty($link['badge']))
                                    <span class="badge bg-danger ms-1">{{ $link['badge'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
