@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h5 class="mb-0 text-dark fw-bold">@lang('Order Center')</h5>
                <p class="text-muted small mb-0 mt-1">@lang('Professional order management — automation, channels, bulk actions, courier.')</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.orders.fulfillment') }}" class="btn btn-sm btn-danger"><i class="las la-tasks"></i> @lang('Fulfillment') @if(($slaOverdue ?? 0) > 0)<span class="badge bg-white text-danger ms-1">{{ $slaOverdue }}</span>@endif</a>
                @if($automationAvailable ?? false)
                <a href="{{ route('admin.orders.automation.index') }}" class="btn btn-sm btn-primary"><i class="las la-robot"></i> @lang('Automation') @if($automationEnabled ?? false)<span class="badge bg-white text-primary ms-1">ON</span>@endif</a>
                @endif
                <a href="{{ route('admin.orders.export', ['scope' => 'pending']) }}" class="btn btn-sm btn-outline-success"><i class="las la-file-export"></i> @lang('Export CSV')</a>
                <a href="{{ route('admin.orders.import-export') }}" class="btn btn-sm btn-outline-primary"><i class="las la-file-import"></i> @lang('Import')</a>
                @if($channelsAvailable ?? false)
                <a href="{{ route('admin.orders.channels.create') }}" class="btn btn-sm btn-primary"><i class="las la-plus"></i> @lang('Add Channel')</a>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('All Orders')</span>
                    <span class="fw-bold fs-4 text-primary">{{ $statusCounts['all'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Today')</span>
                    <span class="fw-bold fs-4 text-dark">{{ $stats['today_count'] ?? 0 }}</span>
                    <small class="d-block text-muted mt-1">{{ showAmount($stats['today_value'] ?? 0) }}</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Pending')</span>
                    <span class="fw-bold fs-4 text-warning">{{ $statusCounts['pending'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Fulfillment Queue')</span>
                    <span class="fw-bold fs-4 text-danger">{{ $fulfillmentQueue ?? 0 }}</span>
                    @if(($slaOverdue ?? 0) > 0)
                    <small class="d-block text-danger mt-1">{{ $slaOverdue }} @lang('SLA overdue')</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h6 class="mb-1 text-dark fw-semibold">@lang('OMS feature coverage')</h6>
                        <span class="text-muted small">{{ $featuresEnabled ?? 0 }} / {{ $featuresTotal ?? 0 }} @lang('modules active in this install')</span>
                    </div>
                    <div class="progress flex-grow-1" style="max-width:280px;height:8px;">
                        <div class="progress-bar bg-success" style="width:{{ $featuresTotal ? round(100 * $featuresEnabled / $featuresTotal) : 0 }}%"></div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-2">
                        @foreach($featureMatrix ?? [] as $feat)
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-start gap-2 small p-2 rounded border {{ $feat['enabled'] ? 'bg-white' : 'bg-light opacity-75' }}">
                                <i class="las {{ $feat['enabled'] ? 'la-check-circle text-success' : 'la-times-circle text-muted' }} mt-1"></i>
                                <div class="flex-grow-1">
                                    <span class="text-dark">{{ $feat['label'] }}</span>
                                    @if($feat['enabled'] && !empty($feat['route']))
                                    <a href="{{ route($feat['route']) }}" class="d-block text-primary">@lang('Open')</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @foreach($modules as $module)
            @php $color = $module['color'] ?? 'primary'; @endphp
            <div class="col-md-4">
                <a href="{{ route($module['route'], $module['route_params'] ?? []) }}" class="text-decoration-none d-block h-100">
                    <div class="card border shadow-sm h-100 bg-white">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-3 p-3 bg-{{ $color }} @if(in_array($color, ['warning','info'])) text-dark @else text-white @endif">
                                    <i class="las la-{{ $module['icon'] }} fs-2"></i>
                                </div>
                                @if(isset($module['count']))
                                    <span class="badge bg-{{ $color }}">{{ $module['count'] }}</span>
                                @elseif(!empty($module['badge']))
                                    <span class="badge bg-label-secondary">{{ $module['badge'] }}</span>
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

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border bg-light">
                <div class="card-body py-3">
                    <h6 class="text-dark fw-semibold mb-2">@lang('Order statuses')</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($statusLinks as $link)
                            <a href="{{ route($link['route'], $link['route_params'] ?? []) }}" class="btn btn-sm btn-outline-{{ $link['variant'] }}">
                                {{ $link['label'] }}
                                @if(($link['count'] ?? 0) > 0)
                                    <span class="badge bg-{{ $link['variant'] }} ms-1">{{ $link['count'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(($automationAvailable ?? false) && ($recentAutomation ?? collect())->isNotEmpty())
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between">
                    <h6 class="mb-0 text-dark fw-semibold"><i class="las la-robot text-primary"></i> @lang('Recent automation')</h6>
                    <a href="{{ route('admin.orders.automation.index') }}" class="btn btn-sm btn-outline-primary">@lang('Settings')</a>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($recentAutomation as $log)
                    <li class="list-group-item small d-flex justify-content-between">
                        <span>{{ $log->message }}</span>
                        <span class="text-muted">{{ $log->created_at->diffForHumans() }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 bg-white shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-dark fw-semibold mb-2">@lang('External stores & marketplaces')</h6>
                    <ul class="mb-0 ps-3 text-secondary small" style="line-height:1.7;">
                        <li><strong class="text-dark">@lang('Import'):</strong> @lang('Connect WooCommerce, Shopify, or custom API — orders arrive via webhook or CSV.')</li>
                        <li><strong class="text-dark">@lang('Export'):</strong> @lang('Download orders as CSV for accounting or push to partner systems.')</li>
                        <li><strong class="text-dark">@lang('Webhook'):</strong> @lang('Each channel gets a secure URL; send JSON with external_id, customer, total.')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
