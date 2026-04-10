@extends('admin.layouts.app')

@section('panel')
<div class="dashboard-glass" id="business-insights-app">

    {{-- 1. AI Assistant Insight Bar --}}
    <div class="dashboard-section">
        <div class="card border-0 bg-gradient-to-r from-slate-900 to-slate-800 text-white shadow-xl rounded-2xl overflow-hidden">
            <div class="card-body p-4 d-flex align-items-center gap-4">
                <div class="flex-shrink-0 bg-white/10 p-3 rounded-xl backdrop-blur-md">
                    <i class="las la-robot fs-1 text-emerald-400"></i>
                </div>
                <div>
                    <h5 class="text-white mb-1 fw-bold">@lang('AI Strategic Advisor')</h5>
                    <p class="mb-0 opacity-90 italic fs-6">"{{ $aiSummary }}"</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Core Business Metrics --}}
    <div class="dashboard-section">
        <h2 class="dashboard-section__title">@lang('Strategic Performance Metrics')</h2>
        <div class="dashboard-grid dashboard-grid--stats">
            <div class="card border-0 shadow-sm rounded-xl">
                <div class="card-body text-center p-4">
                    <div class="mb-3"><i class="las la-sync-alt text-emerald-500 fs-1"></i></div>
                    <h3 class="fw-extrabold text-slate-900 mb-1">{{ $conversionRate }}%</h3>
                    <p class="text-slate-500 small font-semibold uppercase tracking-wider mb-0">@lang('Conversion Rate')</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-xl">
                <div class="card-body text-center p-4">
                    <div class="mb-3"><i class="las la-user-tag text-sky-500 fs-1"></i></div>
                    <h3 class="fw-extrabold text-slate-900 mb-1">{{ $general->cur_sym }}{{ showAmount($averageCLV) }}</h3>
                    <p class="text-slate-500 small font-semibold uppercase tracking-wider mb-0">@lang('Avg. Customer Lifetime Value')</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-xl">
                <div class="card-body text-center p-4">
                    <div class="mb-3"><i class="las la-funnel-dollar text-orange-500 fs-1"></i></div>
                    <h3 class="fw-extrabold text-slate-900 mb-1">{{ $funnel['dropoff_purchase'] }}%</h3>
                    <p class="text-slate-500 small font-semibold uppercase tracking-wider mb-0">@lang('Cart Abandonment Rate')</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Revenue Trends Chart --}}
    <div class="dashboard-section">
        <div class="card border-0 shadow-sm rounded-2xl">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold text-slate-900 mb-0">@lang('Revenue Trajectory') <small class="text-slate-400 fw-normal ms-2">(@lang('Last 30 Days'))</small></h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div id="revenue-trend-chart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    {{-- 4. Funnel Visualization --}}
    <div class="dashboard-section">
        <div class="card border-0 shadow-sm rounded-2xl">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold text-slate-900 mb-0">@lang('Conversion Funnel Analysis')</h5>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="funnel-container py-4">
                            @php
                                $steps = [
                                    ['label' => 'Total Sessions', 'count' => $funnel['sessions'], 'color' => 'bg-slate-200'],
                                    ['label' => 'Product Discovery', 'count' => $funnel['view_product'], 'color' => 'bg-sky-100'],
                                    ['label' => 'Intent (Add to Cart)', 'count' => $funnel['add_to_cart'], 'color' => 'bg-emerald-100'],
                                    ['label' => 'Revenue (Purchase)', 'count' => $funnel['purchase'], 'color' => 'bg-emerald-500 text-white'],
                                ];
                                $max = max(1, $funnel['sessions']);
                            @endphp

                            @foreach($steps as $step)
                                <div class="funnel-step mb-3 d-flex align-items-center">
                                    <div class="funnel-label fw-bold text-slate-700" style="width: 180px;">@lang($step['label'])</div>
                                    <div class="flex-grow-1">
                                        <div class="progress rounded-pill overflow-hidden" style="height: 32px; background: transparent;">
                                            <div class="progress-bar {{ $step['color'] }} shadow-sm" role="progressbar" 
                                                 style="width: {{ ($step['count'] / $max) * 100 }}%;" 
                                                 aria-valuenow="{{ $step['count'] }}" aria-valuemin="0" aria-valuemax="{{ $max }}">
                                                <span class="ms-3 fw-bold">{{ $step['count'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="ps-lg-5 border-start-lg">
                            <h6 class="fw-bold text-slate-900 mb-4">@lang('Optimization Opportunities')</h6>
                            <div class="mb-4">
                                <span class="d-block text-slate-500 small fw-bold uppercase">@lang('Discovery Dropoff')</span>
                                <div class="d-flex align-items-center gap-2">
                                    <h4 class="mb-0 fw-extrabold text-rose-500">{{ $funnel['dropoff_view'] }}%</h4>
                                    <p class="mb-0 small text-slate-400">@lang('users leave before viewing a product')</p>
                                </div>
                            </div>
                            <div class="mb-0">
                                <span class="d-block text-slate-500 small fw-bold uppercase">@lang('Checkout Friction')</span>
                                <div class="d-flex align-items-center gap-2">
                                    <h4 class="mb-0 fw-extrabold text-rose-500">{{ $funnel['dropoff_purchase'] }}%</h4>
                                    <p class="mb-0 small text-slate-400">@lang('intent sessions fail to convert')</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-to-r { background-image: linear-gradient(to right, var(--tw-gradient-stops)); }
    .from-slate-900 { --tw-gradient-from: #0f172a; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgb(15 23 42 / 0)); }
    .to-slate-800 { --tw-gradient-to: #1e293b; }
    .text-emerald-400 { color: #34d399; }
    .bg-emerald-500 { background-color: #10b981; }
    .text-rose-500 { color: #f43f5e; }
</style>
@endsection

@push('script')
<script src="{{ asset('assets/global/js/apexcharts.min.js') }}"></script>
<script>
    "use strict";
    (function () {
        const trendData = @json($revenueTrends);
        const dates = Object.keys(trendData);
        const amounts = Object.values(trendData);

        const options = {
            series: [{
                name: 'Revenue',
                data: amounts
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                zoom: { enabled: false },
                sparkline: { enabled: false }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3, colors: ['#10b981'] },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100],
                    colorStops: [
                        { offset: 0, color: '#10b981', opacity: 0.4 },
                        { offset: 100, color: '#10b981', opacity: 0 }
                    ]
                }
            },
            xaxis: {
                categories: dates,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#94a3b8', fontSize: '12px' } }
            },
            yaxis: {
                labels: {
                    style: { colors: '#94a3b8', fontSize: '12px' },
                    formatter: function (v) { return "{{ $general->cur_sym }}" + v.toLocaleString(); }
                }
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            tooltip: {
                theme: 'dark',
                y: { formatter: function (v) { return "{{ $general->cur_sym }}" + v.toLocaleString(); } }
            }
        };

        const chart = new ApexCharts(document.querySelector("#revenue-trend-chart"), options);
        chart.render();
    })();
</script>
@endpush
