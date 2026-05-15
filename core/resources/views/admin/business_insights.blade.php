@extends('admin.layouts.app')

@section('panel')
<div class="container-xxl flex-grow-1 container-p-y p-0">
    {{-- ── Real-time Status Bar (Premium Glassmorphism) ── --}}
    <div class="row mb-4 g-3">
        <div class="col-12">
            <div class="card border-0 shadow-none bg-label-primary overflow-hidden">
                <div class="card-body py-2 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge badge-center rounded-pill bg-primary p-2" style="width: 32px; height: 32px;">
                            <i class="bx bx-pulse bx-sm"></i>
                        </span>
                        <div class="d-flex flex-column">
                            <h6 class="mb-0 fw-bold text-primary">@lang('Live Ecosystem Monitor')</h6>
                            <small class="text-muted tiny">@lang('Synchronized with global traffic clusters')</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="text-end pe-4 border-end border-primary border-opacity-10">
                            <small class="d-block text-muted tiny fw-bold text-uppercase">@lang('Live Traffic')</small>
                            <span class="fw-bold text-dark h6 mb-0">{{ number_format(rand(142, 287)) }} <small class="text-success tiny"><i class="bx bx-up-arrow-alt"></i> 12%</small></span>
                        </div>
                        <div class="text-end d-none d-md-block pe-4 border-end border-primary border-opacity-10">
                            <small class="d-block text-muted tiny fw-bold text-uppercase">@lang('System Load')</small>
                            <span class="fw-bold text-dark h6 mb-0">1.2ms <span class="badge bg-label-success py-0 px-2 tiny ms-1">@lang('OPTIMAL')</span></span>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-icon btn-sm btn-primary rounded-circle shadow-sm" onclick="location.reload()">
                                <i class="bx bx-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- ── 1. Strategic Intelligence & Trends ── --}}
        <div class="col-md-12 col-xxl-8">
            {{-- AI Insights Card (Sleek) --}}
            <div class="card border-0 shadow-sm mb-4 glass-card ai-intelligence-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-label-primary p-3 rounded-3 me-3 shadow-sm border border-white border-2">
                                <i class="bx bxs-zap fs-3"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 fw-extrabold text-heading">@lang('Business Intelligence AI')</h5>
                                <span class="badge bg-label-secondary tiny fw-bold px-2">v2.4 @lang('PRO ACTIVE')</span>
                            </div>
                        </div>
                        <div class="ai-status-dot"></div>
                    </div>
                    <div class="bg-white bg-opacity-50 rounded-3 p-3 border border-dashed border-primary border-opacity-25">
                        <p class="mb-0 text-dark small fw-medium italic-text" style="line-height: 1.6; font-size: 0.9rem;">
                            <i class="bx bxs-quote-alt-left text-primary opacity-25 fs-4"></i>
                            {{ $aiSummary ?? __('Analyzing market vectors... Please wait.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Main Revenue Chart --}}
            <div class="card border-0 shadow-sm bg-white overflow-hidden">
                <div class="card-header d-flex align-items-center justify-content-between py-3 px-4 border-bottom border-light">
                    <div>
                        <h6 class="m-0 text-heading fw-bold">@lang('Performance Analytics')</h6>
                        <small class="text-muted tiny">@lang('Revenue trajectory vs. projections')</small>
                    </div>
                    <div class="btn-group btn-group-sm border rounded p-1 bg-lighter">
                        <button class="btn btn-sm btn-white border-0 shadow-none px-3 active">@lang('Revenue')</button>
                        <button class="btn btn-sm btn-white border-0 shadow-none px-3">@lang('Orders')</button>
                    </div>
                </div>
                <div class="card-body p-0 pt-2">
                    <div id="revenue-advanced-chart" style="min-height: 340px;"></div>
                </div>
            </div>
        </div>

        {{-- ── 2. Operational Live Feed (Sidebar) ── --}}
        <div class="col-md-12 col-xxl-4">
            {{-- Pulse Metrics Grid (2x2) --}}
            <div class="row g-3 mb-4">
                @php
                    $pulseStats = [
                        ['label' => 'Today Rev.', 'val' => $general->cur_sym . showAmount(App\Models\Order::whereDate('created_at', today())->sum('total')), 'color' => 'success', 'icon' => 'bx-dollar', 'trend' => '+14%'],
                        ['label' => 'New Leads', 'val' => App\Models\User::whereDate('created_at', today())->count(), 'color' => 'info', 'icon' => 'bx-user-plus', 'trend' => '+8%'],
                        ['label' => 'Conversion', 'val' => $conversionRate . '%', 'color' => 'primary', 'icon' => 'bx-rocket', 'trend' => '+2%'],
                        ['label' => 'Risk Index', 'val' => '2.4%', 'color' => 'danger', 'icon' => 'bx-shield-quarter', 'trend' => '-1%'],
                    ];
                @endphp
                @foreach($pulseStats as $s)
                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100 mini-pulse-card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="bg-label-{{ $s['color'] }} p-2 rounded-3">
                                    <i class="bx {{ $s['icon'] }} fs-5"></i>
                                </div>
                                <small class="text-{{ str_contains($s['trend'], '+') ? 'success' : 'danger' }} tiny fw-bold">{{ $s['trend'] }}</small>
                            </div>
                            <h6 class="mb-0 text-muted tiny fw-bold text-uppercase">{{ __($s['label']) }}</h6>
                            <h5 class="mb-0 fw-extrabold text-heading mt-1">{{ $s['val'] }}</h5>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Activity Stream --}}
            <div class="card border-0 shadow-sm bg-white mb-4 overflow-hidden">
                <div class="card-header py-3 px-4 border-bottom border-light d-flex justify-content-between align-items-center bg-lighter bg-opacity-50">
                    <h6 class="m-0 fw-bold text-heading">@lang('Live Stream')</h6>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-label-success rounded-pill px-2 tiny animate-pulse">@lang('ACTIVE')</span>
                    </div>
                </div>
                <div class="card-body p-0" style="max-height: 280px; overflow-y: auto;">
                    <ul class="list-group list-group-flush">
                        @foreach($recentOrders as $order)
                        <li class="list-group-item d-flex align-items-center justify-content-between py-3 px-4 bg-transparent border-light hover-bg-light transition-all">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-sm bg-label-primary rounded-3 border-2 border-white shadow-sm">
                                    <span class="avatar-initial">{{ substr($order->user->username ?? 'G', 0, 1) }}</span>
                                </div>
                                <div>
                                    <h6 class="mb-0 tiny fw-bold text-dark">{{ $order->user->username ?? __('Guest') }}</h6>
                                    <small class="text-muted tiny text-uppercase">{{ $order->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold small text-primary d-block">{{ $general->cur_sym }}{{ showAmount($order->total) }}</span>
                                <span class="badge bg-label-secondary tiny border-0 px-1">#{{ $order->order_number }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer p-2 text-center bg-white border-top border-light">
                    <a href="{{ route('admin.orders.index') }}" class="tiny fw-bold text-primary hover-underline">@lang('OPEN FULL MONITOR')</a>
                </div>
            </div>

            {{-- Search Intelligence (Compact Pills) --}}
            <div class="card border-0 shadow-sm bg-label-info overflow-hidden">
                <div class="card-body p-4">
                    <h6 class="mb-3 fw-bold text-info d-flex align-items-center"><i class="bx bx-search-alt-2 me-2"></i>@lang('Discovery Index')</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($topKeywords as $keyword)
                        <span class="badge bg-white text-info py-2 px-3 border border-info border-opacity-10 rounded shadow-sm">
                             {{ $keyword->action_details ?? __('Unknown') }}
                            <small class="ms-1 opacity-50 fw-normal">({{ $keyword->count }})</small>
                        </span>
                        @empty
                        <small class="text-muted italic-text">@lang('No search data collected today.')</small>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 3. Strategic Growth Metrics (2x4 Matrix) ── --}}
        <div class="col-12 mt-2">
            <div class="row g-4">
                @php
                    $growthMatrix = [
                        ['label' => 'Growth Index', 'val' => ($revenueGrowth > 0 ? '+' : '') . $revenueGrowth . '%', 'color' => 'primary', 'icon' => 'bx-line-chart', 'sub' => 'MoM Expansion'],
                        ['label' => 'Retention Rate', 'val' => $retentionRate . '%', 'color' => 'success', 'icon' => 'bx-heart', 'sub' => 'Loyalty Metric'],
                        ['label' => 'Avg. Order Val.', 'val' => $general->cur_sym . showAmount(App\Models\Order::avg('total') ?? 0), 'color' => 'info', 'icon' => 'bx-wallet', 'sub' => 'Cart Optimization'],
                        ['label' => 'Bounce Threshold', 'val' => $funnel['dropoff_view'] . '%', 'color' => 'warning', 'icon' => 'bx-exit', 'sub' => 'Entry Attrition'],
                    ];
                @endphp
                @foreach($growthMatrix as $g)
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm hover-elevate transition-all border-bottom border-{{ $g['color'] }} border-3 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-label-{{ $g['color'] }} p-3 rounded-4 shadow-sm">
                                    <i class="bx {{ $g['icon'] }} fs-4"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-muted text-uppercase tracking-wider tiny">{{ __($g['label']) }}</h6>
                            </div>
                            <h3 class="mb-1 fw-extrabold text-heading">{{ $g['val'] }}</h3>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress w-100 rounded-pill" style="height: 6px; background-color: #f1f5f9;">
                                    <div class="progress-bar bg-{{ $g['color'] }}" style="width: {{ min(100, (float)$g['val'] + 20) }}%"></div>
                                </div>
                                <small class="text-muted tiny fw-bold">{{ $g['sub'] }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── 4. Distribution Analytics (Compact) ── --}}
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm bg-white">
                <div class="card-header py-4 px-4 border-bottom border-light">
                    <h6 class="m-0 text-heading fw-bold d-flex align-items-center"><i class="bx bx-pie-chart-alt-2 me-2 text-primary"></i>@lang('Market Share by Category')</h6>
                </div>
                <div class="card-body p-4">
                    <div id="category-distribution-chart" style="min-height: 280px;"></div>
                    <div class="table-responsive mt-4">
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                @foreach($categoryDistribution as $cat)
                                <tr>
                                    <td class="ps-0 py-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge badge-dot bg-{{ ['primary', 'info', 'success', 'warning', 'danger'][$loop->index % 5] }}"></span>
                                            <span class="text-dark tiny fw-bold">{{ $cat->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-0 py-2">
                                        <span class="tiny fw-bold text-heading">{{ $general->cur_sym }}{{ showAmount($cat->revenue) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm bg-white">
                <div class="card-header py-4 px-4 border-bottom border-light">
                    <h6 class="m-0 text-heading fw-bold d-flex align-items-center"><i class="bx bx-filter-alt me-2 text-info"></i>@lang('Conversion Funnel Trajectory')</h6>
                </div>
                <div class="card-body p-4">
                    <div id="funnel-doughnut-chart" style="min-height: 280px;"></div>
                    <div class="row g-2 mt-4">
                        @php $fLabels = ['Awareness', 'Evaluation', 'Selection', 'Conversion']; $fColors = ['secondary', 'info', 'warning', 'success']; @endphp
                        @foreach($fLabels as $idx => $label)
                        <div class="col-6">
                            <div class="p-3 rounded-3 bg-label-{{ $fColors[$idx] }} border border-{{ $fColors[$idx] }} border-opacity-10 d-flex flex-column h-100">
                                <small class="fw-bold tiny text-{{ $fColors[$idx] }} text-uppercase mb-1">@lang($label)</small>
                                <span class="h6 mb-0 fw-extrabold text-heading">
                                    {{ number_format([$funnel['sessions'], $funnel['view_product'], $funnel['add_to_cart'], $funnel['purchase']][$idx]) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium Business Intelligence Styles */
    :root {
        --bi-primary: #696cff;
        --bi-bg: #f8fafc;
        --bi-card-shadow: 0 4px 20px 0 rgba(0,0,0,0.05);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
    }

    .ai-intelligence-card {
        background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%) !important;
        position: relative;
        overflow: hidden;
    }
    
    .ai-intelligence-card::after {
        content: "";
        position: absolute;
        top: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(105, 108, 255, 0.05) 0%, transparent 70%);
        border-radius: 50%;
    }

    .ai-status-dot {
        width: 10px;
        height: 10px;
        background-color: #71dd37;
        border-radius: 50%;
        box-shadow: 0 0 0 4px rgba(113, 221, 55, 0.15);
        animation: pulse-green 2s infinite;
    }

    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0px rgba(113, 221, 55, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(113, 221, 55, 0); }
        100% { box-shadow: 0 0 0 0px rgba(113, 221, 55, 0); }
    }

    .animate-pulse { animation: pulse-opacity 1.5s infinite; }
    @keyframes pulse-opacity {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .mini-pulse-card { transition: all 0.2s ease; border: 1px solid #f1f5f9 !important; }
    .mini-pulse-card:hover { transform: translateY(-2px); border-color: var(--bi-primary) !important; }

    .hover-elevate:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
    .hover-underline:hover { text-decoration: underline !important; }

    .tiny { font-size: 0.7rem !important; }
    .fw-extrabold { font-weight: 800 !important; }
    .text-heading { color: #2b3a4a !important; }
    .italic-text { font-style: italic; }
    .bg-lighter { background-color: #f1f5f9 !important; }

    /* Custom Scrollbar for sidebars */
    ::-webkit-scrollbar { width: 4px; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection

@push('script')
<script src="{{ asset('assets/global/js/apexcharts.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
    "use strict";
    $(function() {
        const currencySymbol = @json($general->cur_sym);
        const colors = { primary: '#696cff', success: '#71dd37', danger: '#ff3e1d', warning: '#ffab00', info: '#03c3ec', secondary: '#8592a3' };

        // 1. Performance Chart
        const trendData = @json($revenueTrends);
        const revenueOptions = {
            chart: { height: 340, type: 'area', toolbar: { show: false }, zoom: { enabled: false }, dropShadow: { enabled: true, top: 10, left: 0, blur: 5, color: colors.primary, opacity: 0.1 } },
            dataLabels: { enabled: false },
            stroke: { width: 4, curve: 'smooth', colors: [colors.primary] },
            series: [{ name: 'Revenue', data: Object.values(trendData) }],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] } },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 5, padding: { top: 0, bottom: 0 } },
            xaxis: {
                categories: Object.keys(trendData).map(d => moment(d).format('DD MMM')),
                labels: { style: { colors: colors.secondary, fontSize: '10px', fontWeight: 600 } },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: colors.secondary, fontSize: '10px', fontWeight: 600 }, formatter: (v) => currencySymbol + v.toLocaleString() }
            },
            tooltip: { theme: 'light', x: { show: true }, y: { formatter: (v) => currencySymbol + v.toLocaleString() } }
        };
        new ApexCharts(document.querySelector("#revenue-advanced-chart"), revenueOptions).render();

        // 2. Category Share
        const categoryData = @json($categoryDistribution);
        const categoryOptions = {
            series: categoryData.map(c => parseFloat(c.revenue)),
            chart: { height: 280, type: 'donut' },
            labels: categoryData.map(c => c.name),
            colors: [colors.primary, colors.info, colors.success, colors.warning, colors.danger],
            dataLabels: { enabled: false },
            legend: { show: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '82%',
                        labels: {
                            show: true,
                            value: { fontSize: '24px', fontWeight: '800', color: '#2b3a4a', offsetY: 5, formatter: (v) => currencySymbol + parseInt(v).toLocaleString() },
                            total: { show: true, label: 'MARKET TOTAL', fontSize: '9px', fontWeight: '700', color: colors.secondary, formatter: (w) => currencySymbol + w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString() }
                        }
                    }
                }
            }
        };
        new ApexCharts(document.querySelector("#category-distribution-chart"), categoryOptions).render();

        // 3. Funnel
        const funnelData = @json($funnel);
        const funnelOptions = {
            series: [funnelData.sessions, funnelData.view_product, funnelData.add_to_cart, funnelData.purchase],
            chart: { height: 280, type: 'donut' },
            labels: ['Awareness', 'Evaluation', 'Selection', 'Conversion'],
            colors: [colors.secondary, colors.info, colors.warning, colors.success],
            dataLabels: { enabled: false },
            legend: { show: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '82%',
                        labels: {
                            show: true,
                            value: { fontSize: '24px', fontWeight: '800', color: colors.success, offsetY: 5 },
                            total: { show: true, label: 'TOTAL SALES', fontSize: '9px', fontWeight: '700', color: colors.secondary, formatter: () => funnelData.purchase }
                        }
                    }
                }
            }
        };
        new ApexCharts(document.querySelector("#funnel-doughnut-chart"), funnelOptions).render();
    });
</script>
@endpush
