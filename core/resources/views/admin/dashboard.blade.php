@extends('admin.layouts.app')

@section('panel')
@php
    $d = $dashboard ?? [];
    $alerts = $d['alerts'] ?? [
        'pending_payments'     => $deposit['total_deposit_pending'] ?? 0,
        'pending_orders'       => $order['pending_order'] ?? 0,
        'pending_tickets'      => $widget['ticket_pending'] ?? 0,
        'low_stock_count'      => $widget['low_stock_count'] ?? 0,
        'unread_notifications' => $widget['unread_notifications'] ?? 0,
        'pending_reports'      => $widget['report_pending'] ?? 0,
    ];
    $revenue        = $d['revenue_overview'] ?? [];
    $productModule  = $d['product'] ?? [];
    $orderModule    = $d['order'] ?? [];
    $paymentModule  = $d['payment'] ?? [];
    $userModule     = $d['user'] ?? [];
    $deliveryModule = $d['delivery'] ?? [];
    $supportModule  = $d['support'] ?? [];
    $systemModule   = $d['system'] ?? [];
    $securityModule = $d['security'] ?? [];
    $reportModule   = $d['report'] ?? [];
    $courierModule  = $d['courier'] ?? [];
    $subscriberModule = $d['subscriber'] ?? [];
    $adminUser      = auth()->guard('admin')->user();
    $canSeeSecurity = $adminUser && (method_exists($adminUser, 'isOwner') && $adminUser->isOwner() || (method_exists($adminUser, 'isSuperAdmin') && $adminUser->isSuperAdmin()));
    $general        = $general ?? null;
    $systemInfo     = $general && isset($general->system_info) ? @json_decode($general->system_info) : null;
    $sysVersion     = $systemInfo->version ?? null;
    $sysDetails     = $systemInfo->details ?? null;
    $sysMessages    = $systemInfo->message ?? [];
    $currentVersion = function_exists('systemDetails') ? (systemDetails()['version'] ?? 0) : 0;
    $monthlyAmounts = $monthlyDepositAmounts ?? [];
    $monthsLabels   = $months ?? collect([]);
    $curSym         = optional($general)->cur_sym ?? '';
    $curText        = optional($general)->cur_text ?? '';
    $weekChange     = $revenue['revenue_week_change_percent'] ?? 0;
    $todayVsYest    = $revenue['revenue_today_vs_yesterday_percent'] ?? 0;
    $subGrowth      = $subscriberModule['subscriber_growth_percent'] ?? 0;
@endphp

{{-- ── System alerts ────────────────────────────────────────── --}}
@if ($sysVersion !== null && $sysDetails !== null && $sysVersion > $currentVersion)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2" role="alert">
            <div>
                <strong>@lang('New Version Available')</strong>
                <span class="badge bg-dark ms-2">@lang('Version') {{ $sysVersion }}</span>
                <p class="mb-0 mt-1 small opacity-90">{{ $sysDetails }}</p>
            </div>
        </div>
    </div>
</div>
@endif

@if (is_array($sysMessages) && count($sysMessages) > 0)
<div class="row mb-2">
    <div class="col-12">
        @foreach ($sysMessages as $msg)
        <div class="alert alert-primary d-flex align-items-center justify-content-between" role="alert">
            <span>{{ is_string($msg) ? $msg : '' }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="@lang('Close')"></button>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ── Welcome banner + quick-action buttons ───────────────── --}}
<div class="row mb-6">
    <div class="col-12">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title mb-1">
                            @lang('Welcome back'),
                            <span class="text-primary">{{ auth('admin')->user()->name ?? 'Admin' }}</span> 🎉
                        </h5>
                        <p class="card-subtitle mb-4">{{ now()->format('l, F j, Y · H:i') }}</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ url()->current() }}?refresh=1" class="btn btn-sm btn-outline-secondary">
                                <i class="icon-base bx bx-refresh me-1"></i>@lang('Refresh')
                            </a>
                            <a href="{{ route('admin.product.hub') }}" class="btn btn-sm btn-outline-primary">
                                <i class="icon-base bx bx-grid-alt me-1"></i>@lang('Product Center')
                            </a>
                            <a href="{{ route('admin.category.hub') }}" class="btn btn-sm btn-outline-primary">
                                <i class="icon-base bx bx-category me-1"></i>@lang('Category Center')
                            </a>
                            <a href="{{ route('admin.product.create') }}" class="btn btn-sm btn-primary">
                                <i class="icon-base bx bx-plus me-1"></i>@lang('Add Product')
                            </a>
                            <a href="{{ route('admin.product.general.create') }}" class="btn btn-sm btn-info">
                                <i class="icon-base bx bx-cloud-upload me-1"></i>@lang('Quick Upload')
                            </a>
                            <a href="{{ route('admin.orders.pending') }}" class="btn btn-sm btn-outline-warning">
                                <i class="icon-base bx bx-time me-1"></i>@lang('Pending Orders')
                                <span class="badge bg-warning text-dark ms-1" data-stat="pending_orders">{{ $alerts['pending_orders'] }}</span>
                            </a>
                            <a href="{{ route('admin.deposit.pending') }}" class="btn btn-sm btn-outline-info">
                                <i class="icon-base bx bx-wallet me-1"></i>@lang('Pending Payments')
                                <span class="badge bg-info ms-1" data-stat="pending_payments">{{ $alerts['pending_payments'] }}</span>
                            </a>
                            <a href="{{ route('admin.ticket.pending') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="icon-base bx bx-envelope me-1"></i>@lang('Messages')
                                <span class="badge bg-secondary ms-1" data-stat="pending_tickets">{{ $alerts['pending_tickets'] }}</span>
                            </a>
                            <a href="{{ route('admin.product.index') }}?low_stock=1" class="btn btn-sm btn-outline-danger">
                                <i class="icon-base bx bx-error-alt me-1"></i>@lang('Low Stock')
                                <span class="badge bg-danger ms-1" data-stat="low_stock_count">{{ $alerts['low_stock_count'] }}</span>
                            </a>
                            <a href="{{ route('admin.report.transaction') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="icon-base bx bx-transfer me-1"></i>@lang('Reports')
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="icon-base bx bx-list-ul me-1"></i>@lang('All Orders')
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center pb-0 d-none d-md-flex align-items-end justify-content-end pe-6">
                    <i class="icon-base bx bx-store" style="font-size: 8rem; color: var(--bs-primary); opacity: .15;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Revenue Overview — 5 stat cards ────────────────────── --}}
<div class="row mb-6">
    {{-- Today Revenue --}}
    <div class="col-xl col-md-4 col-sm-6 col-12 mb-4 mb-xl-0">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="icon-base bx bx-wallet icon-26px"></i>
                        </span>
                    </div>
                    <a href="{{ route('admin.report.transaction') }}" class="btn btn-sm btn-label-primary px-2 py-1 rounded-pill small">@lang('View')</a>
                </div>
                <p class="mb-1">@lang("Today's Revenue")</p>
                <h4 class="card-title mb-3">{{ $curSym }}{{ showAmount($widget['today_revenue'] ?? 0) }}</h4>
                <small class="{{ $todayVsYest >= 0 ? 'text-success' : 'text-danger' }} fw-medium">
                    <i class="icon-base bx {{ $todayVsYest >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}"></i>
                    {{ $todayVsYest >= 0 ? '+' : '' }}{{ $todayVsYest }}% @lang('vs yesterday')
                </small>
            </div>
        </div>
    </div>

    {{-- Orders Today --}}
    <div class="col-xl col-md-4 col-sm-6 col-12 mb-4 mb-xl-0">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="icon-base bx bx-cart icon-26px"></i>
                        </span>
                    </div>
                    <a href="{{ route('admin.orders.pending') }}" class="btn btn-sm btn-label-warning px-2 py-1 rounded-pill small">@lang('View')</a>
                </div>
                <p class="mb-1">@lang('Orders Today')</p>
                <h4 class="card-title mb-3">{{ $widget['orders_today'] ?? 0 }}</h4>
                <small class="{{ $weekChange >= 0 ? 'text-success' : 'text-danger' }} fw-medium">
                    <i class="icon-base bx {{ $weekChange >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}"></i>
                    {{ $weekChange >= 0 ? '+' : '' }}{{ $weekChange }}% @lang('this week')
                </small>
            </div>
        </div>
    </div>

    {{-- New Customers Today --}}
    <div class="col-xl col-md-4 col-sm-6 col-12 mb-4 mb-xl-0">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="icon-base bx bx-user-plus icon-26px"></i>
                        </span>
                    </div>
                    <a href="{{ route('admin.users.all') }}" class="btn btn-sm btn-label-success px-2 py-1 rounded-pill small">@lang('View')</a>
                </div>
                <p class="mb-1">@lang('New Customers Today')</p>
                <h4 class="card-title mb-3">{{ $widget['new_users_today'] ?? 0 }}</h4>
                <small class="text-body-secondary fw-medium">
                    <i class="icon-base bx bx-group"></i> @lang('Total'): {{ ($userModule['total_users'] ?? $widget['total_users']) ?? 0 }}
                </small>
            </div>
        </div>
    </div>

    {{-- Total Products --}}
    <div class="col-xl col-md-4 col-sm-6 col-12 mb-4 mb-xl-0">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="icon-base bx bx-package icon-26px"></i>
                        </span>
                    </div>
                    <a href="{{ route('admin.product.index') }}" class="btn btn-sm btn-label-info px-2 py-1 rounded-pill small">@lang('View')</a>
                </div>
                <p class="mb-1">@lang('Total Products')</p>
                <h4 class="card-title mb-3">{{ ($productModule['total_products'] ?? $widget['total_product']) ?? 0 }}</h4>
                <small class="text-danger fw-medium">
                    <i class="icon-base bx bx-error-alt"></i> @lang('Low Stock'): {{ ($productModule['low_stock_products_count'] ?? $widget['low_stock_count']) ?? 0 }}
                </small>
            </div>
        </div>
    </div>

    {{-- Total Payment --}}
    <div class="col-xl col-md-4 col-sm-6 col-12 mb-4 mb-xl-0">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-danger">
                            <i class="icon-base bx bx-dollar-circle icon-26px"></i>
                        </span>
                    </div>
                    <a href="{{ route('admin.deposit.list') }}" class="btn btn-sm btn-label-danger px-2 py-1 rounded-pill small">@lang('View')</a>
                </div>
                <p class="mb-1">@lang('Total Payment')</p>
                <h4 class="card-title mb-3">{{ $curSym }}{{ showAmount($paymentModule['total_deposit_amount'] ?? $deposit['total_deposit_amount'] ?? 0) }}</h4>
                <small class="text-warning fw-medium">
                    <i class="icon-base bx bx-time"></i> @lang('Pending'): {{ ($paymentModule['pending_payments'] ?? $deposit['total_deposit_pending']) ?? 0 }}
                </small>
            </div>
        </div>
    </div>
</div>

{{-- ── Business snapshot (all modules at a glance) ───────────── --}}
<div class="row mb-6 g-3">
    @php
    $snapshot = [
        ['label' => 'Customers', 'val' => $userModule['total_users'] ?? $widget['total_users'] ?? 0, 'icon' => 'bx-user', 'color' => 'primary', 'url' => route('admin.users.all')],
        ['label' => 'Online Now', 'val' => $userModule['live_online_users'] ?? 0, 'icon' => 'bx-broadcast', 'color' => 'success', 'url' => route('admin.report.activity.live')],
        ['label' => 'Subscribers', 'val' => $subscriberModule['total_subscriber'] ?? $productModule['total_subscriber'] ?? 0, 'icon' => 'bx-envelope', 'color' => 'info', 'url' => route('admin.subscriber.index')],
        ['label' => 'Open Tickets', 'val' => $supportModule['open_tickets'] ?? $widget['ticket_pending'] ?? 0, 'icon' => 'bx-support', 'color' => 'warning', 'url' => route('admin.ticket.pending')],
        ['label' => 'Orders Today', 'val' => $orderModule['orders_today'] ?? 0, 'icon' => 'bx-cart', 'color' => 'danger', 'url' => route('admin.orders.index')],
        ['label' => 'Shipping Methods', 'val' => $productModule['total_shipping_methods'] ?? 0, 'icon' => 'bx-package', 'color' => 'secondary', 'url' => route('admin.shipping.index')],
    ];
    @endphp
    @foreach($snapshot as $snap)
    <div class="col-6 col-md-4 col-xl-2">
        <a href="{{ $snap['url'] }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body py-3 text-center">
                    <i class="icon-base {{ $snap['icon'] }} text-{{ $snap['color'] }} mb-1" style="font-size:1.5rem;"></i>
                    <div class="fw-bold fs-5 text-dark">{{ $snap['val'] }}</div>
                    <small class="text-muted">@lang($snap['label'])</small>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- ── Charts Row: Monthly Sale + Transactions + Orders + Sales ─ --}}
<div class="row mb-6">
    {{-- Monthly Bar Chart --}}
    <div class="col-xxl-8 col-12 mb-4 mb-xxl-0">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-1">@lang('Monthly Sale Report')</h5>
                    <p class="card-subtitle">@lang('Last 12 Month')</p>
                </div>
            </div>
            <div class="card-body">
                <div id="apex-bar-chart"></div>
            </div>
        </div>
    </div>

    {{-- Orders Overview Summary --}}
    <div class="col-xxl-4 col-12">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">@lang('Orders Overview')</h5>
                <p class="card-subtitle">@lang('Current statistics')</p>
            </div>
            <div class="card-body">
                <ul class="p-0 m-0 list-unstyled">
                    @php
                    $orderItems = [
                        ['label' => 'Total Orders',     'value' => ($orderModule['total_orders'] ?? $order['total_order']) ?? 0,      'color' => 'primary',  'icon' => 'bx-list-ul',       'route' => route('admin.orders.index')],
                        ['label' => 'Pending',          'value' => ($orderModule['pending_orders'] ?? $order['pending_order']) ?? 0,   'color' => 'warning',  'icon' => 'bx-time',          'route' => route('admin.orders.pending')],
                        ['label' => 'Confirmed',        'value' => ($orderModule['confirmed_orders'] ?? $order['confirmed_order']) ?? 0,'color' => 'info',    'icon' => 'bx-check-double',  'route' => route('admin.orders.confirmed')],
                        ['label' => 'Shipped',          'value' => ($orderModule['shipped_orders'] ?? $order['shipped_order']) ?? 0,   'color' => 'secondary','icon' => 'bx-car',           'route' => route('admin.orders.shipped')],
                        ['label' => 'Delivered',        'value' => ($orderModule['delivered_orders'] ?? $order['delivered_order']) ?? 0,'color' => 'success', 'icon' => 'bx-check-circle',  'route' => route('admin.orders.delivered')],
                        ['label' => 'Rejected/Canceled','value' => ($orderModule['rejected_orders'] ?? $order['rejected_order']) ?? 0, 'color' => 'danger',   'icon' => 'bx-x-circle',      'route' => route('admin.orders.cancel')],
                    ];
                    $totalOrders = max(1, ($orderModule['total_orders'] ?? $order['total_order']) ?? 1);
                    @endphp
                    @foreach($orderItems as $oi)
                    <li class="d-flex mb-4">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-{{ $oi['color'] }}">
                                <i class="icon-base bx {{ $oi['icon'] }}"></i>
                            </span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <a href="{{ $oi['route'] }}" class="text-body-secondary text-decoration-none small">@lang($oi['label'])</a>
                                <h6 class="mb-0">{{ $oi['value'] }}</h6>
                            </div>
                            <div class="user-progress">
                                <div class="progress" style="width:80px; height:6px;">
                                    <div class="progress-bar bg-{{ $oi['color'] }}" style="width:{{ min(100, round($oi['value']/$totalOrders*100)) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- ── Transactions line + Orders line ────────────────────── --}}
<div class="row mb-6">
    <div class="col-md-6 mb-4 mb-md-0">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">@lang('Transactions Report')</h5>
                <p class="card-subtitle">@lang('Last 30 Days')</p>
            </div>
            <div class="card-body pt-2">
                <div id="apex-line"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">@lang('Orders History')</h5>
                <p class="card-subtitle">@lang('Last 30 Days')</p>
            </div>
            <div class="card-body pt-2">
                <div id="deposit-line"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── Sales line + Product Overview ──────────────────────── --}}
<div class="row mb-6">
    <div class="col-md-6 mb-4 mb-md-0">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">@lang('Sales History')</h5>
                <p class="card-subtitle">@lang('Last 30 Days')</p>
            </div>
            <div class="card-body pt-2">
                <div id="withdraw-line"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">@lang('Product Overview')</h5>
                <p class="card-subtitle">@lang('Catalog summary')</p>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @php
                    $prodStats = [
                        ['label'=>'Total',       'val'=>($productModule['total_products'] ?? $widget['total_products'] ?? 0),                   'color'=>'dark'],
                        ['label'=>'Active',      'val'=>($productModule['active_products'] ?? 0),                                           'color'=>'success'],
                        ['label'=>'Draft',       'val'=>($productModule['draft_products'] ?? 0),                                             'color'=>'secondary'],
                        ['label'=>'Low Stock',   'val'=>($productModule['low_stock_products_count'] ?? $widget['low_stock_count'] ?? 0),     'color'=>'warning'],
                        ['label'=>'Out of Stock','val'=>($productModule['out_of_stock_products'] ?? 0),                                       'color'=>'danger'],
                        ['label'=>'Featured',    'val'=>($productModule['featured_products'] ?? $widget['product_featured']) ?? 0,            'color'=>'warning'],
                        ['label'=>'Today Deals', 'val'=>($productModule['product_today_deals'] ?? 0),                                         'color'=>'info'],
                        ['label'=>'Categories',  'val'=>($productModule['total_category'] ?? $widget['total_category']) ?? 0,                 'color'=>'primary'],
                        ['label'=>'Subcategories','val'=>($productModule['total_subcategory'] ?? $widget['total_subcategory']) ?? 0,          'color'=>'info'],
                        ['label'=>'Brands',      'val'=>($productModule['total_brands'] ?? $widget['total_brands']) ?? 0,                    'color'=>'danger'],
                        ['label'=>'Coupons',     'val'=>($productModule['total_coupon'] ?? $widget['total_coupon']) ?? 0,                     'color'=>'primary'],
                        ['label'=>'Popup Ads',   'val'=>($productModule['popup_ads_count'] ?? 0),                                           'color'=>'secondary'],
                    ];
                    @endphp
                    @foreach($prodStats as $ps)
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-label-{{ $ps['color'] }} p-2">
                                <i class="icon-base bx bx-cube icon-xs"></i>
                            </span>
                            <div>
                                <small class="text-body-secondary d-block">@lang($ps['label'])</small>
                                <span class="fw-bold">{{ $ps['val'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Latest Orders + Recent Activity ────────────────────── --}}
<div class="row mb-6">
    <div class="col-lg-8 mb-4 mb-lg-0">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">@lang('Latest Orders')</h5>
                <a href="{{ route('admin.orders.pending') }}" class="btn btn-sm btn-primary">@lang('View Pending')</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-sm table-border-top-0 mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>@lang('Order No')</th>
                            <th>@lang('Price')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($recentOrders as $rorders)
                        <tr>
                            <td><span class="fw-medium">{{ $rorders->order_no }}</span></td>
                            <td>{{ showAmount($rorders->total) }} {{ __($curText) }}</td>
                            <td>{!! $rorders->ordersBadge ?? '' !!}</td>
                            <td>
                                <a href="{{ route('admin.orders.detail', $rorders->id) }}" class="btn btn-sm btn-icon btn-label-primary">
                                    <i class="icon-base bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-body-secondary py-4">
                                <i class="icon-base bx bx-package icon-lg d-block mb-2"></i>
                                {{ __($emptyMessage ?? 'No orders yet') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title m-0">@lang('Recent Activity')</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" id="recent-activity-live">
                    @php $shown = 0; $maxItems = 10; @endphp
                    @foreach($recentOrdersForActivity ?? [] as $o)
                        @if($shown >= $maxItems) @break @endif
                        <li class="list-group-item list-group-item-action px-4 py-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-warning">
                                        <i class="icon-base bx bx-cart icon-xs"></i>
                                    </span>
                                </span>
                                <div class="flex-grow-1">
                                    <a href="{{ route('admin.orders.detail', $o->id) }}" class="text-body text-decoration-none small fw-medium">
                                        @lang('Order') {{ $o->order_no }}
                                    </a>
                                    <small class="text-body-secondary d-block">{{ $o->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </li>
                        @php $shown++; @endphp
                    @endforeach
                    @foreach($recentUsersForActivity ?? [] as $u)
                        @if($shown >= $maxItems) @break @endif
                        <li class="list-group-item list-group-item-action px-4 py-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        <i class="icon-base bx bx-user-plus icon-xs"></i>
                                    </span>
                                </span>
                                <div class="flex-grow-1">
                                    <a href="{{ route('admin.users.detail', $u->id) }}" class="text-body text-decoration-none small fw-medium">
                                        {{ $u->username }}
                                    </a>
                                    <small class="text-body-secondary d-block">{{ $u->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </li>
                        @php $shown++; @endphp
                    @endforeach
                    @foreach($recentDepositsForActivity ?? [] as $dep)
                        @if($shown >= $maxItems) @break @endif
                        <li class="list-group-item list-group-item-action px-4 py-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        <i class="icon-base bx bx-dollar icon-xs"></i>
                                    </span>
                                </span>
                                <div class="flex-grow-1">
                                    <a href="{{ route('admin.deposit.list') }}" class="text-body text-decoration-none small fw-medium">
                                        {{ $curSym }}{{ showAmount($dep->amount) }}
                                    </a>
                                    <small class="text-body-secondary d-block">{{ $dep->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </li>
                        @php $shown++; @endphp
                    @endforeach
                    @if($shown == 0)
                    <li class="list-group-item text-center text-body-secondary py-5">@lang('No recent activity')</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- ── User Analytics + Payment Analytics ─────────────────── --}}
<div class="row mb-6">
    {{-- User Analytics --}}
    <div class="col-md-6 mb-4 mb-md-0">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">@lang('User Analytics')</h5>
                <a href="{{ route('admin.users.all') }}" class="btn btn-sm btn-label-primary">@lang('View All')</a>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @php
                    $userStats = [
                        ['label'=>'Total Customers',    'val'=>($userModule['total_users'] ?? $widget['total_users']) ?? 0,             'color'=>'primary','icon'=>'bx-group',       'route'=>route('admin.users.all')],
                        ['label'=>'Active',             'val'=>($userModule['active_users'] ?? $widget['verified_users']) ?? 0,         'color'=>'success','icon'=>'bx-user-check',  'route'=>route('admin.users.active')],
                        ['label'=>'Email Unverified',   'val'=>($userModule['email_unverified_users'] ?? $widget['email_unverified_users']) ?? 0,'color'=>'warning','icon'=>'bx-envelope','route'=>route('admin.users.email.unverified')],
                        ['label'=>'Mobile Unverified',  'val'=>($userModule['mobile_unverified_users'] ?? $widget['mobile_unverified_users']) ?? 0,'color'=>'danger','icon'=>'bx-phone','route'=>route('admin.users.mobile.unverified')],
                        ['label'=>'Live Online',        'val'=>($userModule['live_online_users'] ?? 0),                                 'color'=>'success','icon'=>'bx-circle',      'route'=>null],
                        ['label'=>'Logins Today',       'val'=>($userModule['total_login_today'] ?? 0),                                 'color'=>'info',   'icon'=>'bx-log-in',     'route'=>null],
                    ];
                    @endphp
                    @foreach($userStats as $us)
                    <div class="col-6">
                        <div class="card border shadow-none mb-0">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="avatar avatar-xs">
                                        <span class="avatar-initial rounded bg-label-{{ $us['color'] }}">
                                            <i class="icon-base bx {{ $us['icon'] }} icon-xs"></i>
                                        </span>
                                    </span>
                                    <small class="text-body-secondary">@lang($us['label'])</small>
                                </div>
                                @if($us['route'])
                                <a href="{{ $us['route'] }}" class="text-body text-decoration-none">
                                    <h5 class="mb-0">{{ $us['val'] }}</h5>
                                </a>
                                @else
                                <h5 class="mb-0">{{ $us['val'] }}</h5>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Analytics --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">@lang('Payment Analytics')</h5>
                <a href="{{ route('admin.deposit.list') }}" class="btn btn-sm btn-label-success">@lang('View All')</a>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @php
                    $payStats = [
                        ['label'=>'Total Payment',    'val'=>$curSym.showAmount($paymentModule['total_deposit_amount'] ?? $deposit['total_deposit_amount'] ?? 0), 'color'=>'success','icon'=>'bx-wallet',     'route'=>route('admin.deposit.list')],
                        ['label'=>'Pending Payment',  'val'=>($paymentModule['pending_payments'] ?? $deposit['total_deposit_pending']) ?? 0,                      'color'=>'warning','icon'=>'bx-time',       'route'=>route('admin.deposit.pending')],
                        ['label'=>'Rejected Payment', 'val'=>($paymentModule['total_deposit_rejected'] ?? $deposit['total_deposit_rejected']) ?? 0,               'color'=>'danger', 'icon'=>'bx-x-circle',   'route'=>route('admin.deposit.rejected')],
                        ['label'=>'Payment Charge',   'val'=>$curSym.showAmount($paymentModule['payment_gateway_charges'] ?? $deposit['total_deposit_charge'] ?? 0),'color'=>'info',  'icon'=>'bx-badge-check','route'=>route('admin.deposit.list')],
                    ];
                    @endphp
                    @foreach($payStats as $ps)
                    <div class="col-6">
                        <div class="card border shadow-none mb-0">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="avatar avatar-xs">
                                        <span class="avatar-initial rounded bg-label-{{ $ps['color'] }}">
                                            <i class="icon-base bx {{ $ps['icon'] }} icon-xs"></i>
                                        </span>
                                    </span>
                                    <small class="text-body-secondary">@lang($ps['label'])</small>
                                </div>
                                <a href="{{ $ps['route'] }}" class="text-body text-decoration-none">
                                    <h5 class="mb-0">{{ $ps['val'] }}</h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Support + Delivery + System Health ─────────────────── --}}
<div class="row mb-6">
    {{-- Support --}}
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">@lang('Support')</h5>
                <a href="{{ route('admin.ticket.index') }}" class="btn btn-sm btn-label-secondary">@lang('View')</a>
            </div>
            <div class="card-body">
                @php
                $supStats = [
                    ['label'=>'Total Tickets',       'val'=>($supportModule['total_tickets'] ?? 0),                            'color'=>'primary','route'=>route('admin.ticket.index')],
                    ['label'=>'Open / Pending',       'val'=>($supportModule['pending_tickets'] ?? $widget['ticket_pending']) ?? 0,'color'=>'warning','route'=>route('admin.ticket.pending')],
                    ['label'=>'Closed',               'val'=>($supportModule['closed_tickets'] ?? 0),                          'color'=>'success','route'=>route('admin.ticket.closed')],
                ];
                @endphp
                @foreach($supStats as $ss)
                <div class="d-flex align-items-center justify-content-between py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <span class="text-body-secondary">@lang($ss['label'])</span>
                    <a href="{{ $ss['route'] }}" class="badge bg-label-{{ $ss['color'] }} text-decoration-none">{{ $ss['val'] }}</a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Delivery --}}
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title m-0">@lang('Delivery Analytics')</h5>
            </div>
            <div class="card-body">
                @php
                $delStats = [
                    ['label'=>'Total Deliveries',    'val'=>($deliveryModule['total_deliveries'] ?? 0),    'color'=>'primary'],
                    ['label'=>'Pending Deliveries',  'val'=>($deliveryModule['pending_deliveries'] ?? 0),  'color'=>'warning'],
                    ['label'=>'Completed',           'val'=>($deliveryModule['completed_deliveries'] ?? 0),'color'=>'success'],
                ];
                @endphp
                @foreach($delStats as $ds)
                <div class="d-flex align-items-center justify-content-between py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <span class="text-body-secondary">@lang($ds['label'])</span>
                    <span class="badge bg-label-{{ $ds['color'] }}">{{ $ds['val'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- System Health --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">@lang('System Health')</h5>
                <a href="{{ route('admin.system.info') }}" class="btn btn-sm btn-label-secondary">@lang('Info')</a>
            </div>
            <div class="card-body">
                @php
                $sysStats = [
                    ['label'=>'Database', 'val'=>ucfirst($systemModule['database_status'] ?? 'ok'),        'color'=>'success'],
                    ['label'=>'Cache',    'val'=>ucfirst($systemModule['cache_status'] ?? 'ok'),           'color'=>'success'],
                    ['label'=>'Storage',  'val'=>($systemModule['storage_usage_percent'] ?? 0).'%',        'color'=>'warning'],
                ];
                @endphp
                @foreach($sysStats as $sys)
                <div class="d-flex align-items-center justify-content-between py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <span class="text-body-secondary">@lang($sys['label'])</span>
                    <span class="badge bg-label-{{ $sys['color'] }}">{{ $sys['val'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── Reports + Courier + Subscribers ────────────────────── --}}
<div class="row mb-6">
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title m-0">@lang('Reports Summary')</h5>
                <p class="card-subtitle">@lang('Last 7 days')</p>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                    <span class="text-body-secondary">@lang('Transactions')</span>
                    <a href="{{ route('admin.report.transaction') }}" class="badge bg-label-primary text-decoration-none">{{ $reportModule['transactions_week'] ?? 0 }}</a>
                </div>
                <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                    <span class="text-body-secondary">@lang('Login History')</span>
                    <a href="{{ route('admin.report.login.history') }}" class="badge bg-label-secondary text-decoration-none">{{ $reportModule['login_history_week'] ?? 0 }}</a>
                </div>
                <div class="d-flex align-items-center justify-content-between py-3">
                    <span class="text-body-secondary">@lang('Notifications')</span>
                    <a href="{{ route('admin.report.notification.history') }}" class="badge bg-label-info text-decoration-none">{{ $reportModule['notification_history_week'] ?? 0 }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4 mb-md-0">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title m-0">@lang('Subscribers')</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                    <span class="text-body-secondary">@lang('Total Subscribers')</span>
                    <a href="{{ route('admin.subscriber.index') }}" class="badge bg-label-primary text-decoration-none">{{ ($subscriberModule['total_subscriber'] ?? $productModule['total_subscriber'] ?? 0) }}</a>
                </div>
                <div class="d-flex align-items-center justify-content-between py-3">
                    <span class="text-body-secondary">@lang('Growth (week vs last)')</span>
                    <span class="badge bg-label-{{ $subGrowth >= 0 ? 'success' : 'danger' }}">
                        {{ $subGrowth >= 0 ? '+' : '' }}{{ $subGrowth }}%
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">@lang('Courier / API')</h5>
                <a href="{{ route('admin.api.courier.logs') }}" class="btn btn-sm btn-label-secondary">@lang('Logs')</a>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between py-3">
                    <span class="text-body-secondary">@lang('Failed Courier Requests')</span>
                    <span class="badge bg-label-danger">{{ $courierModule['failed_courier_count'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Security Overview (SuperAdmin only) ─────────────────── --}}
@if($canSeeSecurity)
<div class="row mb-6">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">@lang('Security Overview')</h5>
                <a href="{{ route('admin.security.dashboard') }}" class="btn btn-sm btn-label-danger">@lang('Security Panel')</a>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @php
                    $secStats = [
                        ['label'=>'Failed Logins (24h)', 'val'=>($securityModule['failed_logins_24h'] ?? 0),                                       'color'=>'danger'],
                        ['label'=>'Locked Accounts',     'val'=>($securityModule['lockout_count'] ?? 0),                                          'color'=>'warning'],
                        ['label'=>'2FA Enabled',         'val'=>($securityModule['admin_with_2fa'] ?? 0).'/'.($securityModule['admin_count'] ?? 0), 'color'=>'success'],
                        ['label'=>'2FA Coverage',        'val'=>($securityModule['two_fa_percent'] ?? 0).'%',                                      'color'=>'info'],
                    ];
                    @endphp
                    @foreach($secStats as $sc)
                    <div class="col-md-3 col-sm-6">
                        <div class="card border shadow-none mb-0">
                            <div class="card-body p-3 text-center">
                                <span class="badge bg-label-{{ $sc['color'] }} p-2 mb-2">
                                    <i class="icon-base bx bx-shield-alt icon-md"></i>
                                </span>
                                <h4 class="mb-1">{{ $sc['val'] }}</h4>
                                <small class="text-body-secondary">@lang($sc['label'])</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── Low Stock Alert ─────────────────────────────────────── --}}
@if(($widget['low_stock_products_count'] ?? $widget['low_stock_count'] ?? 0) > 0 && isset($lowStockProducts) && $lowStockProducts->isNotEmpty())
<div class="row mb-6">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">
                    <i class="icon-base bx bx-error-alt text-warning me-2"></i>
                    @lang('Low Stock Alert') — @lang('Top 5 products')
                </h5>
                <a href="{{ route('admin.product.index') }}?low_stock=1" class="btn btn-sm btn-outline-warning">@lang('View All')</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($lowStockProducts as $p)
                        <tr>
                            <td class="fw-medium">{{ $p->name }} @if($p->product_sku)<small class="text-body-secondary">({{ $p->product_sku }})</small>@endif</td>
                            <td><span class="badge bg-label-warning">@lang('Qty'): {{ $p->quantity }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.product.edit', $p->id) }}" class="btn btn-sm btn-label-primary">@lang('Edit')</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── Login Analytics: Browser / OS / Country ─────────────── --}}
<div class="row mb-6">
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">@lang('Login By Browser')</h5>
                <p class="card-subtitle">@lang('Last 30 days')</p>
            </div>
            <div class="card-body">
                <canvas id="userBrowserChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">@lang('Login By OS')</h5>
                <p class="card-subtitle">@lang('Last 30 days')</p>
            </div>
            <div class="card-body">
                <canvas id="userOsChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-1">@lang('Login By Country')</h5>
                <p class="card-subtitle">@lang('Last 30 days')</p>
            </div>
            <div class="card-body">
                <canvas id="userCountryChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script src="{{ asset('assets/global/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/chart.js.2.8.0.js') }}"></script>
    <script>
    "use strict";
    (function() {
        // ── Monthly Bar ──────────────────────────────────────────
        var monthsLabels   = @json($monthsLabels->toArray());
        var monthlyAmounts = @json($monthlyAmounts);
        if (monthlyAmounts.length === 0 && monthsLabels.length > 0) monthlyAmounts = monthsLabels.map(function() { return 0; });
        if (monthsLabels.length === 0) monthsLabels = [''];

        var barEl = document.querySelector("#apex-bar-chart");
        if (barEl) {
            new ApexCharts(barEl, {
                series: [{ name: 'Total Payment', data: monthlyAmounts }],
                chart: { type: 'bar', height: 300, toolbar: { show: false } },
                plotOptions: { bar: { horizontal: false, columnWidth: '45%', borderRadius: 4 } },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: { categories: monthsLabels },
                yaxis: { title: { text: "{{ $curSym }}" } },
                grid: { xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } },
                fill: { opacity: 1 },
                tooltip: { y: { formatter: function(v) { return "{{ $curSym }}" + v; } } },
                colors: ['#696cff']
            }).render();
        }

        // ── Sales line ───────────────────────────────────────────
        var deliveredPerDay    = @json(($delivered['per_day'] ?? collect())->toArray());
        var deliveredAmounts   = @json(($delivered['per_day_amount'] ?? collect())->toArray());
        if (deliveredPerDay.length === 0) deliveredPerDay = [''];
        if (deliveredAmounts.length === 0) deliveredAmounts = [0];

        var salesEl = document.querySelector("#withdraw-line");
        if (salesEl) {
            new ApexCharts(salesEl, {
                chart: { height: 250, type: 'area', toolbar: { show: false } },
                dataLabels: { enabled: false },
                series: [{ name: 'Sales', data: deliveredAmounts }],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.2 } },
                xaxis: { categories: deliveredPerDay },
                grid: { xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } },
                colors: ['#20c997']
            }).render();
        }

        // ── Orders line ──────────────────────────────────────────
        var ordersPerDay  = @json(($orders['per_day'] ?? collect())->toArray());
        var ordersAmounts = @json(($orders['per_day_amount'] ?? collect())->toArray());
        if (ordersPerDay.length === 0) ordersPerDay = [''];
        if (ordersAmounts.length === 0) ordersAmounts = [0];

        var ordersEl = document.querySelector("#deposit-line");
        if (ordersEl) {
            new ApexCharts(ordersEl, {
                chart: { height: 250, type: 'area', toolbar: { show: false } },
                dataLabels: { enabled: false },
                series: [{ name: 'Orders', data: ordersAmounts }],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.2 } },
                xaxis: { categories: ordersPerDay },
                grid: { xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } },
                colors: ['#ffab00']
            }).render();
        }

        // ── Transactions line ────────────────────────────────────
        var trxDates = @json($trxReport['date'] ?? []);
        var plusTrx  = @json(($plusTrx ?? collect())->keyBy('date')->toArray());
        var minusTrx = @json(($minusTrx ?? collect())->keyBy('date')->toArray());
        var plusArr  = (trxDates || []).map(function(d) { return (plusTrx[d] && plusTrx[d].amount) ? parseFloat(plusTrx[d].amount) : 0; });
        var minusArr = (trxDates || []).map(function(d) { return (minusTrx[d] && minusTrx[d].amount) ? parseFloat(minusTrx[d].amount) : 0; });
        if (trxDates.length === 0) { trxDates = ['']; plusArr = [0]; minusArr = [0]; }

        var trxEl = document.querySelector("#apex-line");
        if (trxEl) {
            new ApexCharts(trxEl, {
                chart: { height: 250, type: 'area', toolbar: { show: false } },
                dataLabels: { enabled: false },
                series: [{ name: 'Plus', data: plusArr }, { name: 'Minus', data: minusArr }],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.2 } },
                xaxis: { categories: trxDates },
                grid: { xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } },
                colors: ['#696cff', '#ff4c51']
            }).render();
        }

        // ── Doughnut charts ──────────────────────────────────────
        var chartColors = ['#696cff','#20c997','#ffab00','#ff4c51','#8592a3','#71dd37','#03c3ec','#fc7753'];

        function makeDoughnut(id, labels, data) {
            var el = document.getElementById(id);
            if (!el) return;
            new Chart(el, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{ data: data, backgroundColor: chartColors, borderWidth: 0 }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1,
                    scales: { xAxes: [{ display: false }], yAxes: [{ display: false }] },
                    legend: { display: false }
                }
            });
        }

        var browserLabels = @json(($chart['user_browser_counter'] ?? collect())->keys()->toArray());
        var browserData   = @json(($chart['user_browser_counter'] ?? collect())->values()->toArray());
        if (browserData.length === 0) { browserLabels = ['No data']; browserData = [1]; }
        makeDoughnut('userBrowserChart', browserLabels, browserData);

        var osLabels = @json(($chart['user_os_counter'] ?? collect())->keys()->toArray());
        var osData   = @json(($chart['user_os_counter'] ?? collect())->values()->toArray());
        if (osData.length === 0) { osLabels = ['No data']; osData = [1]; }
        makeDoughnut('userOsChart', osLabels, osData);

        var countryLabels = @json(($chart['user_country_counter'] ?? collect())->keys()->toArray());
        var countryData   = @json(($chart['user_country_counter'] ?? collect())->values()->toArray());
        if (countryData.length === 0) { countryLabels = ['No data']; countryData = [1]; }
        makeDoughnut('userCountryChart', countryLabels, countryData);

        // ── Real-time stats refresh (near real-time, adaptive) ───
        var statsUrl = "{{ route('admin.dashboard.stats') }}";
        var refreshIntervalMs = {{ (int) config('optimization.admin.dashboard_poll_interval_ms', 10000) }};
        if (!Number.isFinite(refreshIntervalMs) || refreshIntervalMs < 5000) refreshIntervalMs = 5000;
        var inFlight = false;
        var lastOkAt = 0;

        function iconByType(type) {
            if (type === 'order') return 'bx-cart';
            if (type === 'user') return 'bx-user-plus';
            return 'bx-dollar';
        }
        function colorByType(type) {
            if (type === 'order') return 'warning';
            if (type === 'user') return 'success';
            return 'primary';
        }
        var emptyActivityText = @json(__('No recent activity'));
        function renderLiveFeed(feed) {
            if (!Array.isArray(feed)) return;
            var host = document.getElementById('recent-activity-live');
            if (!host) return;
            if (feed.length === 0) {
                host.innerHTML = '<li class="list-group-item text-center text-body-secondary py-5">' + emptyActivityText + '</li>';
                return;
            }
            var html = '';
            feed.forEach(function(item) {
                var type = (item && item.type) ? item.type : 'deposit';
                var icon = iconByType(type);
                var color = colorByType(type);
                var title = (item && item.title) ? item.title : '';
                var subtitle = (item && item.subtitle) ? item.subtitle : '';
                var url = (item && item.url) ? item.url : '#';
                html += '' +
                    '<li class="list-group-item list-group-item-action px-4 py-3">' +
                    '  <div class="d-flex align-items-center">' +
                    '    <span class="avatar avatar-sm me-3">' +
                    '      <span class="avatar-initial rounded-circle bg-label-' + color + '">' +
                    '        <i class="icon-base bx ' + icon + ' icon-xs"></i>' +
                    '      </span>' +
                    '    </span>' +
                    '    <div class="flex-grow-1">' +
                    '      <a href="' + url + '" class="text-body text-decoration-none small fw-medium">' + title + '</a>' +
                    '      <small class="text-body-secondary d-block">' + subtitle + '</small>' +
                    '    </div>' +
                    '  </div>' +
                    '</li>';
            });
            host.innerHTML = html;
        }
        function refreshDashboardStats() {
            if (inFlight || !statsUrl) return;
            inFlight = true;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', statsUrl);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.onload = function() {
                inFlight = false;
                if (xhr.status !== 200) return;
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (!res.ok || !res.stats) return;
                    lastOkAt = Date.now();
                    var s = res.stats;
                    document.querySelectorAll('[data-stat]').forEach(function(el) {
                        var key = el.getAttribute('data-stat');
                        if (s[key] !== undefined) el.textContent = s[key];
                    });
                    renderLiveFeed(res.feed || []);
                } catch (e) {}
            };
            xhr.onerror = function() { inFlight = false; };
            xhr.ontimeout = function() { inFlight = false; };
            xhr.timeout = 7000;
            xhr.send();
        }
        if (statsUrl) {
            // fast initial sync when admin enters dashboard
            refreshDashboardStats();
            setInterval(refreshDashboardStats, refreshIntervalMs);
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    var staleMs = Date.now() - lastOkAt;
                    if (staleMs > refreshIntervalMs) refreshDashboardStats();
                }
            });
        }
    })();
    </script>
@endpush
