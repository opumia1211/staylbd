@extends('admin.layouts.app')

@section('panel')
@php
    $d = $dashboard ?? [];
    $alerts = $d['alerts'] ?? [
        'pending_payments' => $deposit['total_deposit_pending'] ?? 0,
        'pending_orders' => $order['pending_order'] ?? 0,
        'pending_tickets' => $widget['ticket_pending'] ?? 0,
        'low_stock_count' => $widget['low_stock_count'] ?? 0,
        'unread_notifications' => $widget['unread_notifications'] ?? 0,
        'pending_reports' => $widget['report_pending'] ?? 0,
    ];
    $revenue = $d['revenue_overview'] ?? [];
    $productModule = $d['product'] ?? [];
    $orderModule = $d['order'] ?? [];
    $paymentModule = $d['payment'] ?? [];
    $userModule = $d['user'] ?? [];
    $deliveryModule = $d['delivery'] ?? [];
    $supportModule = $d['support'] ?? [];
    $systemModule = $d['system'] ?? [];
    $securityModule = $d['security'] ?? [];
    $reportModule = $d['report'] ?? [];
    $courierModule = $d['courier'] ?? [];
    $subscriberModule = $d['subscriber'] ?? [];
    $adminUser = auth()->guard('admin')->user();
    $canSeeSecurity = $adminUser && (method_exists($adminUser, 'isOwner') && $adminUser->isOwner() || (method_exists($adminUser, 'isSuperAdmin') && $adminUser->isSuperAdmin()));
    $general = $general ?? null;
    $systemInfo = $general && isset($general->system_info) ? @json_decode($general->system_info) : null;
    $sysVersion = $systemInfo->version ?? null;
    $sysDetails = $systemInfo->details ?? null;
    $sysMessages = $systemInfo->message ?? [];
    $currentVersion = function_exists('systemDetails') ? (systemDetails()['version'] ?? 0) : 0;
    $monthlyAmounts = $monthlyDepositAmounts ?? [];
    $monthsLabels = $months ?? collect([]);
@endphp

<div class="dashboard-glass" id="dashboard-app">
    @if ($sysVersion !== null && $sysDetails !== null && $sysVersion > $currentVersion)
        <div class="dashboard-section">
            <div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2" role="alert">
                <div>
                    <strong>@lang('New Version Available')</strong>
                    <span class="badge bg-dark ms-2">@lang('Version') {{ $sysVersion }}</span>
                    <p class="mb-0 mt-1 small opacity-90">{{ $sysDetails }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (is_array($sysMessages) && count($sysMessages) > 0)
        <div class="dashboard-section">
            @foreach ($sysMessages as $msg)
                <div class="alert alert-primary d-flex align-items-center justify-content-between" role="alert">
                    <span>{{ is_string($msg) ? $msg : '' }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="@lang('Close')"></button>
                </div>
            @endforeach
        </div>
    @endif

    {{-- 1. à¦²à¦¾à¦² à¦®à¦¾à¦°à§à¦• à¦œà¦¾à¦¯à¦¼à¦—à¦¾: Welcome + à¦¬à¦¾à¦Ÿà¦¨ à¦¦à§à¦‡ à¦²à¦¾à¦‡à¦¨à§‡, à¦¸à¦®à¦¸à§à¦¤ à¦¬à¦¾à¦Ÿà¦¨ à¦à¦• à¦¸à¦¾à¦‡à¦œ --}}
    <div class="dashboard-section dashboard-section--compact">
        <div class="card border-0 dashboard-card-premium dashboard-actions-card">
            <div class="card-body py-3 px-3">
                <div class="dashboard-welcome-row">
                    <span class="dashboard-welcome-text">@lang('Welcome back'), {{ auth('admin')->user()->name ?? 'Admin' }}</span>
                    <span class="dashboard-welcome-meta">{{ now()->format('l, F j, Y Â· H:i') }}</span>
                </div>
                <div class="dashboard-actions-two-rows">
                    <div class="dashboard-actions-line dashboard-actions-line-1">
                        <a href="{{ url()->current() }}?refresh=1" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none" title="@lang('Refresh')"><i class="las la-sync-alt"></i> @lang('Refresh')</a>
                        <a href="{{ route('admin.product.create') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none">@lang('Add Product')</a>
                        <a href="{{ route('admin.orders.pending') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none">@lang('Pending Orders')</a>
                        <a href="{{ route('admin.deposit.pending') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none">@lang('Pending Payments')</a>
                        <a href="{{ route('admin.ticket.pending') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none">@lang('Messages')</a>
                        <a href="{{ route('admin.category.index') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none">@lang('Categories')</a>
                        <a href="{{ route('admin.orders.index') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none"><i class="las la-list-alt"></i> @lang('All Orders')</a>
                        <a href="{{ route('admin.deposit.list') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none"><i class="fas fa-wallet"></i> @lang('Deposit')</a>
                    </div>
                    <div class="dashboard-actions-line dashboard-actions-line-2">
                        <a href="{{ route('admin.deposit.pending') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none"><i class="fas fa-spinner"></i> @lang('Pending Payments') <span class="badge bg-secondary rounded-pill" data-stat="pending_payments">{{ $alerts['pending_payments'] ?? 0 }}</span></a>
                        <a href="{{ route('admin.orders.pending') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none"><i class="las la-shopping-cart"></i> @lang('Pending Orders') <span class="badge bg-secondary rounded-pill" data-stat="pending_orders">{{ $alerts['pending_orders'] ?? 0 }}</span></a>
                        <a href="{{ route('admin.ticket.pending') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none"><i class="las la-envelope"></i> @lang('Pending Tickets') <span class="badge bg-secondary rounded-pill" data-stat="pending_tickets">{{ $alerts['pending_tickets'] ?? 0 }}</span></a>
                        <a href="{{ route('admin.product.index') }}?low_stock=1" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none"><i class="las la-exclamation-triangle"></i> @lang('Low Stock') <span class="badge bg-secondary rounded-pill" data-stat="low_stock_count">{{ $alerts['low_stock_count'] ?? 0 }}</span></a>
                        <a href="{{ route('admin.notifications') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none"><i class="las la-bell"></i> @lang('Unread') <span class="badge bg-secondary rounded-pill" data-stat="unread_notifications">{{ $alerts['unread_notifications'] ?? 0 }}</span></a>
                        <a href="{{ route('admin.request.report') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none"><i class="las la-flag"></i> @lang('Pending Reports') <span class="badge bg-secondary rounded-pill" data-stat="pending_reports">{{ $alerts['pending_reports'] ?? 0 }}</span></a>
                        <a href="{{ route('admin.subscriber.index') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none"><i class="las la-thumbs-up"></i> @lang('Subscribers')</a>
                        <a href="{{ route('admin.report.transaction') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none"><i class="las la-exchange-alt"></i> @lang('Reports')</a>
                        <a href="{{ route('admin.system.info') }}" class="dashboard-action-btn btn btn-outline-secondary text-decoration-none"><i class="las la-info-circle"></i> @lang('System Info')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Revenue Overview --}}
    <div class="dashboard-section">
        <h2 class="dashboard-section__title">@lang('Revenue Overview')</h2>
        <div class="dashboard-grid dashboard-grid--stats">
            <x-stat-card 
                title="Today's Revenue" 
                :value="(optional($general)->cur_sym ?? '') . showAmount($widget['today_revenue'] ?? 0)" 
                icon="fas fa-wallet" 
                :link="route('admin.report.transaction')" 
            />
            <x-stat-card 
                title="Orders Today" 
                :value="$widget['orders_today'] ?? 0" 
                icon="las la-shopping-cart" 
                :link="route('admin.orders.pending')" 
            />
            <x-stat-card 
                title="New Customers Today" 
                :value="$widget['new_users_today'] ?? 0" 
                icon="las la-user-plus" 
                :link="route('admin.users.all')" 
            />
            @php
                $weekChange = $revenue['revenue_week_change_percent'] ?? 0;
                $todayVsYesterday = $revenue['revenue_today_vs_yesterday_percent'] ?? 0;
            @endphp
            <x-stat-card 
                title="Revenue This Week vs Last" 
                :value="($weekChange >= 0 ? '+' : '') . $weekChange . '%'" 
                icon="las la-chart-line" 
            />
            <x-stat-card 
                title="Today vs Yesterday" 
                :value="($todayVsYesterday >= 0 ? '+' : '') . $todayVsYesterday . '%'" 
                icon="las la-calendar-day" 
            />
        </div>
    </div>

    @if($canSeeSecurity)
    {{-- Security Overview (SuperAdmin/Owner only) --}}
    <div class="dashboard-section">
        <h2 class="dashboard-section__title">@lang('Security Overview')</h2>
        <div class="dashboard-grid dashboard-grid--stats">
            <x-stat-card title="Failed Logins (24h)" :value="($securityModule['failed_logins_24h'] ?? 0)" icon="las la-user-lock" :link="route('admin.security.dashboard')" />
            <x-stat-card title="Locked Accounts" :value="($securityModule['lockout_count'] ?? 0)" icon="las la-lock" :link="route('admin.security.dashboard')" />
            <x-stat-card title="2FA Enabled" :value="($securityModule['admin_with_2fa'] ?? 0).'/'.($securityModule['admin_count'] ?? 0)" icon="las la-shield-alt" :link="route('admin.security.dashboard')" />
            <x-stat-card title="2FA Coverage" :value="($securityModule['two_fa_percent'] ?? 0).'%'" icon="las la-percent" :link="route('admin.security.dashboard')" />
        </div>
    </div>
    @endif

    {{-- Reports Summary Widget --}}
    <div class="dashboard-section">
        <h2 class="dashboard-section__title">@lang('Reports Summary')</h2>
        <div class="dashboard-grid dashboard-grid--stats">
            <x-stat-card title="Transactions (7d)" :value="($reportModule['transactions_week'] ?? 0)" icon="las la-exchange-alt" :link="route('admin.report.transaction')" />
            <x-stat-card title="Login History (7d)" :value="($reportModule['login_history_week'] ?? 0)" icon="las la-sign-in-alt" :link="route('admin.report.login.history')" />
            <x-stat-card title="Notifications (7d)" :value="($reportModule['notification_history_week'] ?? 0)" icon="las la-bell" :link="route('admin.report.notification.history')" />
        </div>
    </div>

    {{-- Courier / API Status Widget --}}
    <div class="dashboard-section">
        <h2 class="dashboard-section__title">@lang('Courier / API')</h2>
        <div class="dashboard-grid dashboard-grid--stats">
            <x-stat-card title="Failed Courier" :value="($courierModule['failed_courier_count'] ?? 0)" icon="las la-truck" :link="route('admin.api.courier.logs')" />
        </div>
    </div>

    {{-- 4. Orders Overview --}}
    <div class="dashboard-section">
        <h2 class="dashboard-section__title">@lang('Orders Overview')</h2>
        <div class="dashboard-grid dashboard-grid--stats">
            <x-stat-card title="Total Orders" :value="($orderModule['total_orders'] ?? $order['total_order']) ?? 0" icon="las la-list-alt" :link="route('admin.orders.index')" />
            <x-stat-card title="Pending Orders" :value="($orderModule['pending_orders'] ?? $order['pending_order']) ?? 0" icon="las la-spinner" :link="route('admin.orders.pending')" />
            <x-stat-card title="Confirmed Orders" :value="($orderModule['confirmed_orders'] ?? $order['confirmed_order']) ?? 0" icon="las la-check-double" :link="route('admin.orders.confirmed')" />
            <x-stat-card title="Shipped Orders" :value="($orderModule['shipped_orders'] ?? $order['shipped_order']) ?? 0" icon="las la-truck" :link="route('admin.orders.shipped')" />
            <x-stat-card title="Delivered Orders" :value="($orderModule['delivered_orders'] ?? $order['delivered_order']) ?? 0" icon="las la-check-circle" :link="route('admin.orders.delivered')" />
            <x-stat-card title="Rejected Orders" :value="($orderModule['rejected_orders'] ?? $order['rejected_order']) ?? 0" icon="las la-times-circle" :link="route('admin.orders.cancel')" />
        </div>
    </div>

    {{-- 5. Product Overview --}}
    <div class="dashboard-section">
        <h2 class="dashboard-section__title">@lang('Product Overview')</h2>
        <div class="dashboard-grid dashboard-grid--stats">
            <x-stat-card title="Total Products" :value="($productModule['total_products'] ?? $widget['total_product']) ?? 0" icon="las la-box" :link="route('admin.product.index')" />
            <x-stat-card title="Active Products" :value="($productModule['active_products'] ?? 0)" icon="las la-check-circle" :link="route('admin.product.index')" />
            <x-stat-card title="Draft Products" :value="($productModule['draft_products'] ?? 0)" icon="las la-file-alt" :link="route('admin.product.index')" />
            <x-stat-card title="Low Stock" :value="($productModule['low_stock_products_count'] ?? $widget['low_stock_count']) ?? 0" icon="las la-exclamation-triangle" :link="route('admin.product.index').'?low_stock=1'" />
            <x-stat-card title="Featured Products" :value="($productModule['featured_products'] ?? $widget['product_featured']) ?? 0" icon="las la-star" :link="route('admin.product.feature.index')" />
            <x-stat-card title="Categories" :value="($productModule['total_category'] ?? $widget['total_category']) ?? 0" icon="las la-folder" :link="route('admin.category.index')" />
            <x-stat-card title="Subcategories" :value="($productModule['total_subcategory'] ?? $widget['total_subcategory']) ?? 0" icon="las la-tags" :link="route('admin.subcategory.index')" />
            <x-stat-card title="Brands" :value="($productModule['total_brands'] ?? $widget['total_brands']) ?? 0" icon="las la-copyright" :link="route('admin.brand.index')" />
            <x-stat-card title="Coupons" :value="($productModule['total_coupon'] ?? $widget['total_coupon']) ?? 0" icon="las la-ticket-alt" :link="route('admin.coupon.index')" />
            <x-stat-card title="Popup Ads" :value="($productModule['popup_ads_count'] ?? 0)" icon="las la-ad" :link="route('admin.popup-ads.index')" />
        </div>
    </div>

    {{-- Subscriber Card + Growth --}}
    <div class="dashboard-section">
        <h2 class="dashboard-section__title">@lang('Subscribers')</h2>
        <div class="dashboard-grid dashboard-grid--stats">
            <x-stat-card title="Total Subscribers" :value="($subscriberModule['total_subscriber'] ?? $productModule['total_subscriber'] ?? 0)" icon="las la-thumbs-up" :link="route('admin.subscriber.index')" />
            @php $subGrowth = $subscriberModule['subscriber_growth_percent'] ?? 0; @endphp
            <div class="card border-0 dashboard-stat-card">
                <a href="{{ route('admin.subscriber.index') }}" class="text-decoration-none text-dark d-block p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-light p-2"><i class="las la-chart-line text-secondary"></i></div>
                        <div>
                            <span class="text-muted small d-block">@lang('Growth This Week vs Last')</span>
                            <span class="fw-bold {{ $subGrowth >= 0 ? 'text-success' : 'text-danger' }}">{{ $subGrowth >= 0 ? '+' : '' }}{{ $subGrowth }}%</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- 6. User Analytics --}}
    <div class="dashboard-section">
        <h2 class="dashboard-section__title">@lang('User Analytics')</h2>
        <div class="dashboard-grid dashboard-grid--stats">
            <x-stat-card title="Total Customers" :value="($userModule['total_users'] ?? $widget['total_users']) ?? 0" icon="las la-users" :link="route('admin.users.all')" />
            <x-stat-card title="Active Customers" :value="($userModule['active_users'] ?? $widget['verified_users']) ?? 0" icon="las la-user-check" :link="route('admin.users.active')" />
            <x-stat-card title="Email Unverified" :value="($userModule['email_unverified_users'] ?? $widget['email_unverified_users']) ?? 0" icon="lar la-envelope" :link="route('admin.users.email.unverified')" />
            <x-stat-card title="Mobile Unverified" :value="($userModule['mobile_unverified_users'] ?? $widget['mobile_unverified_users']) ?? 0" icon="las la-comment-slash" :link="route('admin.users.mobile.unverified')" />
            <x-stat-card title="Live Online Users" :value="($userModule['live_online_users'] ?? 0)" icon="las la-circle" />
            <x-stat-card title="Total Login Today" :value="($userModule['total_login_today'] ?? 0)" icon="las la-sign-in-alt" />
        </div>
    </div>

    {{-- 7. Payment Analytics --}}
    <div class="dashboard-section">
        <h2 class="dashboard-section__title">@lang('Payment Analytics')</h2>
        <div class="dashboard-grid dashboard-grid--stats">
            <x-stat-card title="Total Payment" :value="(optional($general)->cur_sym ?? '').showAmount($paymentModule['total_deposit_amount'] ?? $deposit['total_deposit_amount'] ?? 0)" icon="fas fa-hand-holding-usd" :link="route('admin.deposit.list')" />
            <x-stat-card title="Pending Payment" :value="($paymentModule['pending_payments'] ?? $deposit['total_deposit_pending']) ?? 0" icon="fas fa-spinner" :link="route('admin.deposit.pending')" />
            <x-stat-card title="Rejected Payment" :value="($paymentModule['total_deposit_rejected'] ?? $deposit['total_deposit_rejected']) ?? 0" icon="fas fa-ban" :link="route('admin.deposit.rejected')" />
            <x-stat-card title="Payment Charge" :value="(optional($general)->cur_sym ?? '').showAmount($paymentModule['payment_gateway_charges'] ?? $deposit['total_deposit_charge'] ?? 0)" icon="fas fa-percentage" :link="route('admin.deposit.list')" />
        </div>
    </div>

    {{-- 8. Delivery Analytics --}}
    <div class="dashboard-section">
        <h2 class="dashboard-section__title">@lang('Delivery Analytics')</h2>
        <div class="dashboard-grid dashboard-grid--stats">
            <x-stat-card title="Total Deliveries" :value="($deliveryModule['total_deliveries'] ?? 0)" icon="las la-truck" />
            <x-stat-card title="Pending Deliveries" :value="($deliveryModule['pending_deliveries'] ?? 0)" icon="las la-clock" />
            <x-stat-card title="Completed Deliveries" :value="($deliveryModule['completed_deliveries'] ?? 0)" icon="las la-check-double" />
        </div>
    </div>

    {{-- 9. Support Overview --}}
    <div class="dashboard-section">
        <h2 class="dashboard-section__title">@lang('Support Overview')</h2>
        <div class="dashboard-grid dashboard-grid--stats">
            <x-stat-card title="Total Tickets" :value="($supportModule['total_tickets'] ?? 0)" icon="las la-envelope" :link="route('admin.ticket.index')" />
            <x-stat-card title="Open / Pending Tickets" :value="($supportModule['pending_tickets'] ?? $widget['ticket_pending']) ?? 0" icon="las la-inbox" :link="route('admin.ticket.pending')" />
            <x-stat-card title="Closed Tickets" :value="($supportModule['closed_tickets'] ?? 0)" icon="las la-check-circle" :link="route('admin.ticket.closed')" />
        </div>
    </div>

    {{-- 10. System Health --}}
    <div class="dashboard-section">
        <h2 class="dashboard-section__title">@lang('System Health')</h2>
        <div class="dashboard-grid dashboard-grid--stats">
            <x-stat-card title="Database" :value="ucfirst($systemModule['database_status'] ?? 'ok')" icon="las la-database" />
            <x-stat-card title="Cache" :value="ucfirst($systemModule['cache_status'] ?? 'ok')" icon="las la-memory" />
            <x-stat-card title="Storage Usage" :value="($systemModule['storage_usage_percent'] ?? 0).'%'" icon="las la-hdd" />
            <x-stat-card title="System Info" value="View" icon="las la-info-circle" :link="route('admin.system.info')" />
        </div>
    </div>

    {{-- Low Stock Alert (if any) --}}
    @if(($widget['low_stock_products_count'] ?? $widget['low_stock_count'] ?? 0) > 0 && isset($lowStockProducts) && $lowStockProducts->isNotEmpty())
    <div class="dashboard-section">
        <div class="card border-0">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h6 class="mb-0 text-dark"><i class="las la-exclamation-triangle text-warning me-2"></i> @lang('Low Stock Alert') â€” @lang('Top 5 products by quantity')</h6>
                <a href="{{ route('admin.product.index') }}?low_stock=1" class="btn btn-sm btn-outline-secondary">@lang('View All')</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($lowStockProducts as $p)
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span class="text-dark">{{ $p->name }} @if($p->product_sku)<small class="text-muted">({{ $p->product_sku }})</small>@endif</span>
                        <span class="badge bg-warning text-dark">@lang('Qty'): {{ $p->quantity }}</span>
                        <a href="{{ route('admin.product.edit', $p->id) }}" class="btn btn-sm btn-outline-secondary">@lang('Edit')</a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- Charts --}}
    <div class="dashboard-section">
        <div class="dashboard-grid dashboard-grid--fluid-charts">
            <div class="card border-0 dashboard-grid--span-full">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0 text-dark">@lang('Monthly Sale Report') (@lang('Last 12 Month'))</h5>
                </div>
                <div class="card-body pt-0">
                    <div id="apex-bar-chart" class="dashboard-chart-slot"></div>
                </div>
            </div>
            <div class="card border-0">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0 text-dark">@lang('Transactions Report') (@lang('Last 30 Days'))</h5>
                </div>
                <div class="card-body pt-0">
                    <div id="apex-line" class="dashboard-chart-slot"></div>
                </div>
            </div>
            <div class="card border-0">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0 text-dark">@lang('Last 30 days Orders History')</h5>
                </div>
                <div class="card-body pt-0">
                    <div id="deposit-line" class="dashboard-chart-slot"></div>
                </div>
            </div>
            <div class="card border-0">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0 text-dark">@lang('Last 30 days Sales History')</h5>
                </div>
                <div class="card-body pt-0">
                    <div id="withdraw-line" class="dashboard-chart-slot"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Latest Orders + Recent Activity --}}
    <div class="dashboard-section">
        <div class="dashboard-grid dashboard-grid--split-2-1">
            <div class="card border-0">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="mb-0 text-dark">@lang('Latest Orders')</h5>
                    <a href="{{ route('admin.orders.pending') }}" class="btn btn-sm btn-outline-secondary">@lang('View Pending')</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-secondary">@lang('Order No')</th>
                                    <th class="text-secondary">@lang('Price')</th>
                                    <th class="text-secondary">@lang('Status')</th>
                                    <th class="text-secondary">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $rorders)
                                    <tr>
                                        <td class="text-dark"><span class="fw-medium">{{ $rorders->order_no }}</span></td>
                                        <td class="text-dark">{{ showAmount($rorders->total) }} {{ __(optional($general)->cur_text ?? '') }}</td>
                                        <td>{!! $rorders->ordersBadge ?? '' !!}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.detail', $rorders->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="las la-desktop"></i> @lang('Details')
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">{{ __($emptyMessage ?? 'No orders yet') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card border-0">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0 text-dark">@lang('Recent Activity')</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @php $shown = 0; $maxItems = 10; @endphp
                        @foreach($recentOrdersForActivity ?? [] as $o)
                            @if($shown >= $maxItems) @break @endif
                            <a href="{{ route('admin.orders.detail', $o->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 text-decoration-none text-dark">
                                <span><i class="las la-shopping-cart text-secondary me-2"></i> @lang('Order') {{ $o->order_no }}</span>
                                <small class="text-muted">{{ $o->created_at->diffForHumans() }}</small>
                            </a>
                            @php $shown++; @endphp
                        @endforeach
                        @foreach($recentUsersForActivity ?? [] as $u)
                            @if($shown >= $maxItems) @break @endif
                            <a href="{{ route('admin.users.detail', $u->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 text-decoration-none text-dark">
                                <span><i class="las la-user-plus text-secondary me-2"></i> {{ $u->username }}</span>
                                <small class="text-muted">{{ $u->created_at->diffForHumans() }}</small>
                            </a>
                            @php $shown++; @endphp
                        @endforeach
                        @foreach($recentDepositsForActivity ?? [] as $d)
                            @if($shown >= $maxItems) @break @endif
                            <a href="{{ route('admin.deposit.list') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 text-decoration-none text-dark">
                                <span><i class="fas fa-hand-holding-usd text-secondary me-2"></i> {{ optional($general)->cur_sym ?? '' }}{{ showAmount($d->amount) }}</span>
                                <small class="text-muted">{{ $d->created_at->diffForHumans() }}</small>
                            </a>
                            @php $shown++; @endphp
                        @endforeach
                        @if($shown == 0)
                            <div class="list-group-item text-center text-muted py-4">@lang('No recent activity')</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Login analytics --}}
    <div class="dashboard-section">
        <div class="dashboard-grid dashboard-grid--cols-3">
            <div class="card border-0">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0 text-dark">@lang('Login By Browser') (@lang('Last 30 days'))</h5>
                </div>
                <div class="card-body">
                    <canvas id="userBrowserChart" height="220"></canvas>
                </div>
            </div>
            <div class="card border-0">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0 text-dark">@lang('Login By OS') (@lang('Last 30 days'))</h5>
                </div>
                <div class="card-body">
                    <canvas id="userOsChart" height="220"></canvas>
                </div>
            </div>
            <div class="card border-0">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="mb-0 text-dark">@lang('Login By Country') (@lang('Last 30 days'))</h5>
                </div>
                <div class="card-body">
                    <canvas id="userCountryChart" height="220"></canvas>
                </div>
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
        var monthsLabels = @json($monthsLabels->toArray());
        var monthlyAmounts = @json($monthlyAmounts);
        if (monthlyAmounts.length === 0 && monthsLabels.length > 0) monthlyAmounts = monthsLabels.map(function() { return 0; });
        if (monthsLabels.length === 0) monthsLabels = [''];

        var barOptions = {
            series: [{ name: 'Total Payment', data: monthlyAmounts }],
            chart: { type: 'bar', height: 350, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 } },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: { categories: monthsLabels },
            yaxis: { title: { text: "{{ __(optional($general)->cur_sym ?? '') }}" } },
            grid: { xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } },
            fill: { opacity: 1 },
            tooltip: { y: { formatter: function(v) { return "{{ __(optional($general)->cur_sym ?? '') }}" + v; } } }
        };
        var barEl = document.querySelector("#apex-bar-chart");
        if (barEl) { var barChart = new ApexCharts(barEl, barOptions); barChart.render(); }

        var deliveredPerDay = @json(($delivered['per_day'] ?? collect())->toArray());
        var deliveredAmounts = @json(($delivered['per_day_amount'] ?? collect())->toArray());
        if (deliveredPerDay.length === 0) deliveredPerDay = [''];
        if (deliveredAmounts.length === 0) deliveredAmounts = [0];

        var salesOptions = {
            chart: { height: 350, type: 'area', toolbar: { show: false }, animations: { enabled: true } },
            dataLabels: { enabled: false },
            series: [{ name: 'Sales', data: deliveredAmounts }],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.2 } },
            xaxis: { categories: deliveredPerDay },
            grid: { xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } }
        };
        var salesEl = document.querySelector("#withdraw-line");
        if (salesEl) { var salesChart = new ApexCharts(salesEl, salesOptions); salesChart.render(); }

        var ordersPerDay = @json(($orders['per_day'] ?? collect())->toArray());
        var ordersAmounts = @json(($orders['per_day_amount'] ?? collect())->toArray());
        if (ordersPerDay.length === 0) ordersPerDay = [''];
        if (ordersAmounts.length === 0) ordersAmounts = [0];

        var ordersOptions = {
            chart: { height: 350, type: 'area', toolbar: { show: false }, animations: { enabled: true } },
            dataLabels: { enabled: false },
            series: [{ name: 'Orders', data: ordersAmounts }],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.2 } },
            xaxis: { categories: ordersPerDay },
            grid: { xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } }
        };
        var ordersEl = document.querySelector("#deposit-line");
        if (ordersEl) { var ordersChart = new ApexCharts(ordersEl, ordersOptions); ordersChart.render(); }

        var trxDates = @json($trxReport['date'] ?? []);
        var plusTrx = @json(($plusTrx ?? collect())->keyBy('date')->toArray());
        var minusTrx = @json(($minusTrx ?? collect())->keyBy('date')->toArray());
        var plusArr = (trxDates || []).map(function(d) { return (plusTrx[d] && plusTrx[d].amount) ? parseFloat(plusTrx[d].amount) : 0; });
        var minusArr = (trxDates || []).map(function(d) { return (minusTrx[d] && minusTrx[d].amount) ? parseFloat(minusTrx[d].amount) : 0; });
        if (trxDates.length === 0) { trxDates = ['']; plusArr = [0]; minusArr = [0]; }

        var trxOptions = {
            chart: { height: 350, type: 'area', toolbar: { show: false }, animations: { enabled: true } },
            dataLabels: { enabled: false },
            series: [ { name: 'Plus', data: plusArr }, { name: 'Minus', data: minusArr } ],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.2 } },
            xaxis: { categories: trxDates },
            grid: { xaxis: { lines: { show: false } }, yaxis: { lines: { show: false } } }
        };
        var trxEl = document.querySelector("#apex-line");
        if (trxEl) { var trxChart = new ApexCharts(trxEl, trxOptions); trxChart.render(); }

        var chartColors = ['#495057','#6c757d','#868e96','#adb5bd','#ced4da','#dee2e6','#e9ecef','#f1f3f5','#495057','#6c757d','#868e96','#adb5bd','#ced4da','#dee2e6','#e9ecef','#f1f3f5'];
        var browserLabels = @json(($chart['user_browser_counter'] ?? collect())->keys()->toArray());
        var browserData = @json(($chart['user_browser_counter'] ?? collect())->values()->toArray());
        if (browserData.length === 0) { browserLabels = ['No data']; browserData = [1]; }

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
        makeDoughnut('userBrowserChart', browserLabels, browserData);
        var osLabels = @json(($chart['user_os_counter'] ?? collect())->keys()->toArray());
        var osData = @json(($chart['user_os_counter'] ?? collect())->values()->toArray());
        if (osData.length === 0) { osLabels = ['No data']; osData = [1]; }
        makeDoughnut('userOsChart', osLabels, osData);
        var countryLabels = @json(($chart['user_country_counter'] ?? collect())->keys()->toArray());
        var countryData = @json(($chart['user_country_counter'] ?? collect())->values()->toArray());
        if (countryData.length === 0) { countryLabels = ['No data']; countryData = [1]; }
        makeDoughnut('userCountryChart', countryLabels, countryData);

        // Real-time: refresh every 45 seconds (AJAX, no full reload)
        var statsUrl = "{{ route('admin.dashboard.stats') }}";
        var refreshInterval = 45000;
        function refreshDashboardStats() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', statsUrl);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.onload = function() {
                if (xhr.status !== 200) return;
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (!res.ok || !res.stats) return;
                    var s = res.stats;
                    // Update alert badges
                    var badges = document.querySelectorAll('[data-stat]');
                    badges.forEach(function(el) {
                        var key = el.getAttribute('data-stat');
                        if (s[key] !== undefined) { el.textContent = s[key]; }
                    });
                    // Optional: update other data-stat-value elements if needed
                } catch (e) {}
            };
            xhr.send();
        }
        if (statsUrl && refreshInterval > 0) {
            setInterval(refreshDashboardStats, refreshInterval);
        }
    })();
    </script>
@endpush
