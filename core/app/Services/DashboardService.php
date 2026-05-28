<?php

namespace App\Services;

use App\Constants\Status;
use App\Models\Admin;
use App\Models\AdminLockout;
use App\Models\AdminNotification;
use App\Models\AdminReport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Deposit;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\SecurityEvent;
use App\Models\ShippingMethod;
use App\Models\Subcategory;
use App\Models\Subscriber;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Centralized dashboard statistics.
 * All dashboard counts and aggregates go through this service so new features
 * can be reflected by extending these methods.
 *
 * Key consistency: Use the same key names in modules and in dashboard.blade.php
 * (e.g. low_stock_products_count in productModule + low_stock_count in widget for alerts).
 * Document any alias in getRealtimeStats() when merging into 'widget' or 'dashboard'.
 */
class DashboardService
{
    /** Cache TTL for heavy aggregates (seconds). */
    public const CACHE_TTL = 90;

    /** Live/real-time stats cache TTL (shorter). */
    public const LIVE_CACHE_TTL = 30;

    /** Consider user "online" if session activity within this many seconds. */
    public const ONLINE_SECONDS = 300;

    public function __construct(
        protected ?string $cachePrefix = 'admin.dashboard'
    ) {
    }

    /**
     * Get all dashboard data for full page load (realtime + charts).
     * Use getLiveStats() for AJAX refresh.
     */
    public function getFullDashboard(bool $skipChartCache = false): array
    {
        $realtime = $this->getRealtimeStats();
        $charts = $skipChartCache
            ? $this->getChartData()
            : $this->getChartDataCached();
        return array_merge($realtime, $charts);
    }

    /**
     * Stats suitable for AJAX refresh (counts and key metrics only; JSON-serializable).
     */
    public function getLiveStats(): array
    {
        $cacheKey = $this->cachePrefix . '.live';
        $ttl = (int) config('optimization.admin.live_stats_cache_ttl', self::LIVE_CACHE_TTL);
        if ($ttl < 1) {
            $ttl = 1;
        }
        return Cache::remember($cacheKey, $ttl, function () {
            $moduleIsolation = (bool) config('features.resilience.module_isolation', true);
            if (! $moduleIsolation) {
                return array_merge(
                    $this->productModule(),
                    $this->orderModule(),
                    $this->paymentModule(),
                    $this->userModule(),
                    $this->deliveryModule(),
                    $this->supportModule(),
                    $this->systemModule(),
                    $this->securityModule(),
                    $this->reportModule(),
                    $this->courierModule(),
                    $this->subscriberModule(),
                    $this->criticalAlerts(),
                    $this->revenueOverview()
                );
            }

            return array_merge(
                $this->safeModule('product', fn () => $this->productModule()),
                $this->safeModule('order', fn () => $this->orderModule()),
                $this->safeModule('payment', fn () => $this->paymentModule()),
                $this->safeModule('user', fn () => $this->userModule()),
                $this->safeModule('delivery', fn () => $this->deliveryModule()),
                $this->safeModule('support', fn () => $this->supportModule()),
                $this->safeModule('system', fn () => $this->systemModule()),
                $this->safeModule('security', fn () => $this->securityModule()),
                $this->safeModule('report', fn () => $this->reportModule()),
                $this->safeModule('courier', fn () => $this->courierModule()),
                $this->safeModule('subscriber', fn () => $this->subscriberModule()),
                $this->safeModule('alerts', fn () => $this->criticalAlerts()),
                $this->safeModule('revenue', fn () => $this->revenueOverview())
            );
        });
    }

    /**
     * Lightweight live feed for admin dashboard activity widget.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLiveFeed(int $maxItems = 10): array
    {
        $maxItems = max(1, min($maxItems, 20));
        $key = $this->cachePrefix . '.live.feed.' . $maxItems;

        return Cache::remember($key, 5, function () use ($maxItems) {
            $items = $this->recentActivity($maxItems);
            $out = [];

            foreach ($items as $item) {
                $type = (string) ($item['type'] ?? '');
                $model = $item['model'] ?? null;
                if (! $model) {
                    continue;
                }

                if ($type === 'order') {
                    $out[] = [
                        'type' => 'order',
                        'id' => (int) $model->id,
                        'title' => 'Order ' . (string) ($model->order_no ?? '#'),
                        'subtitle' => optional($model->created_at)->diffForHumans() ?? 'just now',
                        'url' => route('admin.orders.detail', (int) $model->id),
                    ];
                    continue;
                }

                if ($type === 'user') {
                    $out[] = [
                        'type' => 'user',
                        'id' => (int) $model->id,
                        'title' => (string) ($model->username ?? ('User #' . (int) $model->id)),
                        'subtitle' => optional($model->created_at)->diffForHumans() ?? 'just now',
                        'url' => route('admin.users.detail', (int) $model->id),
                    ];
                    continue;
                }

                if ($type === 'deposit') {
                    $out[] = [
                        'type' => 'deposit',
                        'id' => (int) $model->id,
                        'title' => 'Payment #' . (int) $model->id,
                        'subtitle' => optional($model->created_at)->diffForHumans() ?? 'just now',
                        'url' => route('admin.deposit.list'),
                    ];
                }
            }

            return $out;
        });
    }

    /** Product module stats. */
    public function productModule(): array
    {
        return Cache::remember($this->cachePrefix . '.product', self::CACHE_TTL, function () {
            $total = Product::count();
            $active = Product::active()->count();
            $draft = Product::inactive()->count();
            $lowStock = (int) Product::where('quantity', '<=', 5)->where('quantity', '>=', 0)->count();
            $outOfStock = (int) Product::where('quantity', '<=', 0)->count();
            $featured = (int) Product::where('featured_product', Status::YES)->count();
            return [
                'total_products' => $total,
                'active_products' => $active,
                'draft_products' => $draft,
                'low_stock_products_count' => $lowStock,
                'out_of_stock_products' => $outOfStock,
                'featured_products' => $featured,
                'total_category' => Category::count(),
                'total_subcategory' => Subcategory::count(),
                'total_brands' => Brand::count(),
                'total_coupon' => Coupon::count(),
                'popup_ads_count' => \App\Models\PopupAd::count(),
                'product_today_deals' => (int) Product::where('today_deals', Status::YES)->count(),
                'total_subscriber' => Subscriber::count(),
                'total_shipping_methods' => ShippingMethod::count(),
            ];
        });
    }

    /** Order module stats. */
    public function orderModule(): array
    {
        return Cache::remember($this->cachePrefix . '.order', self::CACHE_TTL, function () {
            return [
                'total_orders' => Order::count(),
                'pending_orders' => Order::pending()->count(),
                'confirmed_orders' => Order::confirmed()->count(),
                'processing_orders' => Order::confirmed()->count(), // same as confirmed in this schema
                'shipped_orders' => Order::shipped()->count(),
                'delivered_orders' => Order::delivered()->count(),
                'cancelled_orders' => Order::cancel()->count(),
                'rejected_orders' => Order::cancel()->count(),
                'orders_today' => Order::where('created_at', '>=', Carbon::today())->count(),
            ];
        });
    }

    /** Payment module stats. */
    public function paymentModule(): array
    {
        return Cache::remember($this->cachePrefix . '.payment', self::CACHE_TTL, function () {
            $todayStart = Carbon::today();
            $monthStart = Carbon::now()->startOfMonth();
            $yearStart = Carbon::now()->startOfYear();
            $totalRevenue = Order::where('order_status', Status::ORDER_DELIVERED)->sum('total');
            $todayRevenue = Order::where('order_status', Status::ORDER_DELIVERED)
                ->where('created_at', '>=', $todayStart)->sum('total');
            $monthlyRevenue = Order::where('order_status', Status::ORDER_DELIVERED)
                ->where('created_at', '>=', $monthStart)->sum('total');
            $yearlyRevenue = Order::where('order_status', Status::ORDER_DELIVERED)
                ->where('created_at', '>=', $yearStart)->sum('total');
            return [
                'total_revenue' => $totalRevenue + 0,
                'today_revenue' => $todayRevenue + 0,
                'monthly_revenue' => $monthlyRevenue + 0,
                'yearly_revenue' => $yearlyRevenue + 0,
                'pending_payments' => Deposit::pending()->count(),
                'failed_payments' => Deposit::rejected()->count(),
                'refunded_payments' => 0, // extend when refund tracking exists
                'payment_gateway_charges' => Deposit::successful()->sum('charge') + 0,
                'total_deposit_amount' => Deposit::successful()->sum('amount') + 0,
                'total_deposit_rejected' => Deposit::rejected()->count(),
                'total_deposit_charge' => Deposit::successful()->sum('charge') + 0,
            ];
        });
    }

    /** User module stats. */
    public function userModule(): array
    {
        return Cache::remember($this->cachePrefix . '.user', self::CACHE_TTL, function () {
            $todayStart = Carbon::today();
            $monthStart = Carbon::now()->startOfMonth();
            $yearStart = Carbon::now()->startOfYear();
            $total = User::count();
            $todayReg = User::where('created_at', '>=', $todayStart)->count();
            $monthReg = User::where('created_at', '>=', $monthStart)->count();
            $yearReg = User::where('created_at', '>=', $yearStart)->count();
            $active = User::active()->count();
            $suspended = User::banned()->count();
            $loginToday = Schema::hasTable('user_logins')
                ? UserLogin::where('created_at', '>=', $todayStart)->count()
                : 0;
            $failedLoginAttempts = $this->failedLoginAttemptsCount();
            $liveOnline = $this->liveOnlineUsersCount();
            return [
                'total_users' => $total,
                'today_registrations' => $todayReg,
                'monthly_registrations' => $monthReg,
                'yearly_registrations' => $yearReg,
                'active_users' => $active,
                'suspended_users' => $suspended,
                'live_online_users' => $liveOnline,
                'total_login_today' => $loginToday,
                'failed_login_attempts' => $failedLoginAttempts,
                'verified_users' => $active,
                'email_unverified_users' => User::emailUnverified()->count(),
                'mobile_unverified_users' => User::mobileUnverified()->count(),
            ];
        });
    }

    /** Delivery module (derived from order status). */
    public function deliveryModule(): array
    {
        return Cache::remember($this->cachePrefix . '.delivery', self::CACHE_TTL, function () {
            $pending = Order::pending()->count() + Order::confirmed()->count() + Order::shipped()->count();
            $completed = Order::delivered()->count();
            return [
                'total_deliveries' => Order::count(),
                'pending_deliveries' => $pending,
                'completed_deliveries' => $completed,
                'returned_orders' => 0, // extend when returns are tracked
            ];
        });
    }

    /** Support module stats. */
    public function supportModule(): array
    {
        return Cache::remember($this->cachePrefix . '.support', self::CACHE_TTL, function () {
            $open = SupportTicket::whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count();
            $closed = SupportTicket::where('status', Status::TICKET_CLOSE)->count();
            $pending = SupportTicket::where('status', Status::TICKET_OPEN)->count();
            return [
                'total_tickets' => SupportTicket::count(),
                'open_tickets' => $open,
                'closed_tickets' => $closed,
                'pending_tickets' => $pending,
                'total_messages' => 0, // extend if ticket messages are counted separately
                'unread_messages' => 0,
            ];
        });
    }

    /** System health (server, DB, cache, storage). */
    public function systemModule(): array
    {
        return Cache::remember($this->cachePrefix . '.system', 60, function () {
            $dbOk = true;
            try {
                DB::connection()->getPdo();
            } catch (\Throwable $e) {
                $dbOk = false;
            }
            $cacheOk = true;
            try {
                Cache::put($this->cachePrefix . '.ping', 1, 5);
                Cache::get($this->cachePrefix . '.ping');
            } catch (\Throwable $e) {
                $cacheOk = false;
            }
            $storageUsage = 0;
            try {
                $used = disk_total_space(base_path()) - disk_free_space(base_path());
                $total = disk_total_space(base_path());
                $storageUsage = $total > 0 ? round(($used / $total) * 100, 1) : 0;
            } catch (\Throwable $e) {
                // ignore
            }
            return [
                'server_status' => 'ok',
                'database_status' => $dbOk ? 'ok' : 'error',
                'cache_status' => $cacheOk ? 'ok' : 'error',
                'storage_usage_percent' => $storageUsage,
                'security_alerts' => $this->securityAlertsCount(),
            ];
        });
    }

    /** Critical alerts for header (pending payments, orders, tickets, low stock, notifications, reports). */
    public function criticalAlerts(): array
    {
        return [
            'pending_payments' => Deposit::pending()->count(),
            'pending_orders' => Order::pending()->count(),
            'pending_tickets' => SupportTicket::where('status', Status::TICKET_OPEN)->count(),
            'low_stock_count' => (int) Product::where('quantity', '<=', 5)->where('quantity', '>=', 0)->count(),
            'unread_notifications' => AdminNotification::where('is_read', Status::NO)->count(),
            'pending_reports' => AdminReport::pending()->count(),
        ];
    }

    /** Revenue overview (today, week comparison, today vs yesterday). */
    public function revenueOverview(): array
    {
        $todayStart = Carbon::today();
        $yesterdayStart = Carbon::yesterday();
        $yesterdayEnd = Carbon::yesterday()->endOfDay();
        $thisWeekStart = Carbon::now()->startOfWeek();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        $todayRev = Order::where('order_status', Status::ORDER_DELIVERED)
            ->where('created_at', '>=', $todayStart)->sum('total');
        $yesterdayRev = Order::where('order_status', Status::ORDER_DELIVERED)
            ->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])->sum('total');
        $todayVsYesterdayPercent = $yesterdayRev > 0
            ? round((($todayRev - $yesterdayRev) / $yesterdayRev) * 100, 1)
            : ($todayRev > 0 ? 100 : 0);

        $thisWeekRev = Order::where('order_status', Status::ORDER_DELIVERED)
            ->where('created_at', '>=', $thisWeekStart)->sum('total');
        $lastWeekRev = Order::where('order_status', Status::ORDER_DELIVERED)
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->sum('total');
        $weekChange = $lastWeekRev > 0
            ? round((($thisWeekRev - $lastWeekRev) / $lastWeekRev) * 100, 1)
            : ($thisWeekRev > 0 ? 100 : 0);
        return [
            'revenue_today' => $todayRev + 0,
            'revenue_yesterday' => $yesterdayRev + 0,
            'revenue_today_vs_yesterday_percent' => $todayVsYesterdayPercent,
            'revenue_this_week' => $thisWeekRev + 0,
            'revenue_last_week' => $lastWeekRev + 0,
            'revenue_week_change_percent' => $weekChange,
        ];
    }

    /** Security module (failed logins 24h, locked accounts, 2FA coverage). SuperAdmin-only visibility in UI. */
    public function securityModule(): array
    {
        return Cache::remember($this->cachePrefix . '.security', self::CACHE_TTL, function () {
            $last24h = Carbon::now()->subDay();
            $failedLogins24h = 0;
            $lockoutCount = 0;
            $adminCount = Admin::count();
            $adminWith2fa = 0;
            $twoFaPercent = 0;
            if (Schema::hasTable('security_events')) {
                $failedLogins24h = (int) SecurityEvent::where('event_type', 'failed_admin_login')
                    ->where('created_at', '>', $last24h)->count();
            }
            if (Schema::hasTable('admin_lockouts')) {
                $lockoutCount = (int) AdminLockout::whereNotNull('locked_at')->where('locked_at', '>', now())->count();
            }
            $adminWith2fa = (int) Admin::whereNotNull('two_factor_confirmed_at')->whereNotNull('two_factor_secret')->count();
            if ($adminCount > 0) {
                $twoFaPercent = round(($adminWith2fa / $adminCount) * 100, 1);
            }
            return [
                'failed_logins_24h' => $failedLogins24h,
                'lockout_count' => $lockoutCount,
                'admin_count' => $adminCount,
                'admin_with_2fa' => $adminWith2fa,
                'two_fa_percent' => $twoFaPercent,
            ];
        });
    }

    /** Report module (counts for dashboard links: transaction, login, notification last 7 days). */
    public function reportModule(): array
    {
        return Cache::remember($this->cachePrefix . '.report', self::CACHE_TTL, function () {
            $weekStart = Carbon::now()->subDays(7);
            $transactionsWeek = Transaction::where('created_at', '>=', $weekStart)->count();
            $loginHistoryWeek = Schema::hasTable('user_logins')
                ? UserLogin::where('created_at', '>=', $weekStart)->count()
                : 0;
            $notificationHistoryWeek = Schema::hasTable('notification_logs')
                ? NotificationLog::where('created_at', '>=', $weekStart)->count()
                : 0;
            return [
                'transactions_week' => $transactionsWeek,
                'login_history_week' => $loginHistoryWeek,
                'notification_history_week' => $notificationHistoryWeek,
            ];
        });
    }

    /** Courier/API module (failed courier count for dashboard widget). */
    public function courierModule(): array
    {
        return Cache::remember($this->cachePrefix . '.courier', self::CACHE_TTL, function () {
            $failedCourier = 0;
            if (Schema::hasTable('courier_logs')) {
                $failedCourier = (int) DB::table('courier_logs')->where('status', 'failed')->count();
            }
            return ['failed_courier_count' => $failedCourier];
        });
    }

    /** Subscriber module (total + growth this week vs last week). */
    public function subscriberModule(): array
    {
        return Cache::remember($this->cachePrefix . '.subscriber', self::CACHE_TTL, function () {
            $total = Subscriber::count();
            $thisWeekStart = Carbon::now()->startOfWeek();
            $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
            $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
            $thisWeek = (int) Subscriber::where('created_at', '>=', $thisWeekStart)->count();
            $lastWeek = (int) Subscriber::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();
            $growthPercent = $lastWeek > 0
                ? round((($thisWeek - $lastWeek) / $lastWeek) * 100, 1)
                : ($thisWeek > 0 ? 100 : 0);
            return [
                'total_subscriber' => $total,
                'subscriber_this_week' => $thisWeek,
                'subscriber_last_week' => $lastWeek,
                'subscriber_growth_percent' => $growthPercent,
            ];
        });
    }

    public function recentOrders(int $limit = 10)
    {
        return Order::with('user')->latest()->take($limit)->get();
    }

    public function recentActivity(int $maxItems = 10): array
    {
        $items = [];
        foreach (Order::with('user:id,username')->latest()->take(5)->get(['id', 'order_no', 'total', 'order_status', 'user_id', 'created_at']) as $o) {
            $items[] = ['type' => 'order', 'model' => $o];
        }
        foreach (User::latest()->take(3)->get(['id', 'username', 'created_at']) as $u) {
            $items[] = ['type' => 'user', 'model' => $u];
        }
        foreach (\App\Models\Deposit::latest()->take(3)->get(['id', 'amount', 'status', 'created_at']) as $d) {
            $items[] = ['type' => 'deposit', 'model' => $d];
        }
        usort($items, fn ($a, $b) => $b['model']->created_at->getTimestamp() - $a['model']->created_at->getTimestamp());
        return array_slice($items, 0, $maxItems);
    }

    public function lowStockProducts(int $limit = 8)
    {
        return Product::where('quantity', '<=', 5)->where('quantity', '>=', 0)
            ->orderBy('quantity')->take($limit)->get(['id', 'name', 'quantity', 'product_sku']);
    }

    /** Chart data (monthly sales, transactions, orders/sales history, login charts). */
    public function getChartData(): array
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $chart = [
            'user_browser_counter' => collect([]),
            'user_os_counter' => collect([]),
            'user_country_counter' => collect([]),
        ];
        if (Schema::hasTable('user_logins')) {
            $loginData = UserLogin::where('created_at', '>=', $thirtyDaysAgo)->get(['browser', 'os', 'country']);
            $chart['user_browser_counter'] = $loginData->groupBy('browser')->map->count();
            $chart['user_os_counter'] = $loginData->groupBy('os')->map->count();
            $chart['user_country_counter'] = $loginData->groupBy('country')->map->count()->sort()->reverse()->take(5);
        }

        $plusTrx = \App\Models\Transaction::where('trx_type', '+')->where('created_at', '>=', $thirtyDaysAgo)
            ->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date")
            ->orderBy('created_at')->groupBy('date')->get();
        $minusTrx = \App\Models\Transaction::where('trx_type', '-')->where('created_at', '>=', $thirtyDaysAgo)
            ->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date")
            ->orderBy('created_at')->groupBy('date')->get();
        $trxReport = ['date' => dateSorting($plusTrx->pluck('date')->merge($minusTrx->pluck('date'))->unique()->values()->toArray())];

        $depositsMonth = Deposit::where('created_at', '>=', Carbon::now()->subYear())
            ->where('status', Status::PAYMENT_SUCCESS)
            ->selectRaw("SUM(CASE WHEN status = " . Status::PAYMENT_SUCCESS . " THEN amount END) as depositAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')->groupBy('months')->get();
        $report = ['months' => collect([]), 'deposit_month_amount' => collect([])];
        $depositsMonth->each(function ($d) use (&$report) {
            $report['months']->push($d->months);
            $report['deposit_month_amount']->push(getAmount($d->depositAmount));
        });
        $months = $report['months'];
        for ($i = 0; $i < $months->count(); ++$i) {
            $monthVal = Carbon::parse($months[$i]);
            if (isset($months[$i + 1])) {
                $monthValNext = Carbon::parse($months[$i + 1]);
                if ($monthValNext < $monthVal) {
                    $temp = $months[$i];
                    $months[$i] = Carbon::parse($months[$i + 1])->format('F-Y');
                    $months[$i + 1] = Carbon::parse($temp)->format('F-Y');
                } else {
                    $months[$i] = Carbon::parse($months[$i])->format('F-Y');
                }
            } else {
                $months[$i] = Carbon::parse($months[$i])->format('F-Y');
            }
        }
        $monthlyDepositAmounts = $report['deposit_month_amount']->values()->toArray();

        $deliveredRows = Order::where('created_at', '>=', $thirtyDaysAgo)
            ->where('order_status', Status::ORDER_DELIVERED)
            ->selectRaw('SUM(total) as totalAmount, DATE(created_at) as day')
            ->groupBy('day')->get();
        $delivered = [
            'per_day' => $deliveredRows->map(fn ($r) => date('d M', strtotime($r->day)))->values(),
            'per_day_amount' => $deliveredRows->map(fn ($r) => $r->totalAmount + 0)->values(),
        ];

        $orderRows = Order::where('created_at', '>=', $thirtyDaysAgo)
            ->selectRaw('COUNT(id) as totalOrder, DATE(created_at) as day')
            ->groupBy('day')->get();
        $orders = [
            'per_day' => $orderRows->map(fn ($r) => date('d M', strtotime($r->day)))->values(),
            'per_day_amount' => $orderRows->map(fn ($r) => $r->totalOrder + 0)->values(),
        ];

        return compact('chart', 'depositsMonth', 'months', 'monthlyDepositAmounts', 'delivered', 'trxReport', 'plusTrx', 'minusTrx', 'orders');
    }

    public function getChartDataCached(): array
    {
        return Cache::remember($this->cachePrefix . '.charts', self::CACHE_TTL, fn () => $this->getChartData());
    }

    /** Real-time stats for full page (no chart cache). */
    private function getRealtimeStats(): array
    {
        $product = $this->safeModule('product', fn () => $this->productModule(), []);
        $order = $this->safeModule('order', fn () => $this->orderModule(), []);
        $payment = $this->safeModule('payment', fn () => $this->paymentModule(), []);
        $user = $this->safeModule('user', fn () => $this->userModule(), []);
        $delivery = $this->safeModule('delivery', fn () => $this->deliveryModule(), []);
        $support = $this->safeModule('support', fn () => $this->supportModule(), []);
        $system = $this->safeModule('system', fn () => $this->systemModule(), []);
        $security = $this->safeModule('security', fn () => $this->securityModule(), []);
        $report = $this->safeModule('report', fn () => $this->reportModule(), []);
        $courier = $this->safeModule('courier', fn () => $this->courierModule(), []);
        $subscriber = $this->safeModule('subscriber', fn () => $this->subscriberModule(), []);
        $alerts = $this->safeModule('alerts', fn () => $this->criticalAlerts(), []);
        $revenue = $this->safeModule('revenue', fn () => $this->revenueOverview(), []);

        $widget = array_merge(
            $product,
            [
                'low_stock_count' => $product['low_stock_products_count'] ?? 0,
                'today_revenue' => $payment['today_revenue'],
                'orders_today' => $order['orders_today'],
                'new_users_today' => $user['today_registrations'],
                'revenue_this_week' => $revenue['revenue_this_week'],
                'revenue_last_week' => $revenue['revenue_last_week'],
                'unread_notifications' => $alerts['unread_notifications'],
                'report_pending' => $alerts['pending_reports'],
            ]
        );
        $deposit = [
            'total_deposit_amount' => $payment['total_deposit_amount'],
            'total_deposit_pending' => $payment['pending_payments'],
            'total_deposit_rejected' => $payment['total_deposit_rejected'],
            'total_deposit_charge' => $payment['total_deposit_charge'],
        ];
        $orderArr = [
            'total_order' => $order['total_orders'],
            'pending_order' => $order['pending_orders'],
            'confirmed_order' => $order['confirmed_orders'],
            'shipped_order' => $order['shipped_orders'],
            'delivered_order' => $order['delivered_orders'],
            'rejected_order' => $order['rejected_orders'],
        ];
        return [
            'widget' => $widget,
            'deposit' => $deposit,
            'order' => $orderArr,
            'dashboard' => [
                'product' => $product,
                'order' => $order,
                'payment' => $payment,
                'user' => $user,
                'delivery' => $delivery,
                'support' => $support,
                'system' => $system,
                'security' => $security,
                'report' => $report,
                'courier' => $courier,
                'subscriber' => $subscriber,
                'alerts' => $alerts,
                'revenue_overview' => $revenue,
            ],
            'recentOrders' => $this->recentOrders(),
            'recentOrdersForActivity' => Order::with('user:id,username')->latest()->take(5)->get(['id', 'order_no', 'total', 'order_status', 'user_id', 'created_at']),
            'recentUsersForActivity' => User::latest()->take(3)->get(['id', 'username', 'created_at']),
            'recentDepositsForActivity' => Deposit::latest()->take(3)->get(['id', 'amount', 'status', 'created_at']),
            'lowStockProducts' => $this->lowStockProducts(),
        ];
    }

    /**
     * Execute a dashboard module in isolation so one failure does not break others.
     *
     * @param  callable():array<string,mixed>  $resolver
     * @param  array<string,mixed>  $fallback
     * @return array<string,mixed>
     */
    private function safeModule(string $name, callable $resolver, array $fallback = []): array
    {
        try {
            return $resolver();
        } catch (\Throwable $e) {
            report($e);
            \Log::warning('Dashboard module failed in isolation', [
                'module' => $name,
                'message' => $e->getMessage(),
            ]);
            return $fallback;
        }
    }

    private function liveOnlineUsersCount(): int
    {
        if (!Schema::hasTable('sessions')) {
            return 0;
        }
        $cutoff = time() - self::ONLINE_SECONDS;
        return (int) DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', $cutoff)
            ->distinct()
            ->count('user_id');
    }

    private function failedLoginAttemptsCount(): int
    {
        if (!Schema::hasTable('security_events')) {
            return 0;
        }
        return (int) DB::table('security_events')
            ->where('event_type', 'failed_admin_login')
            ->where('created_at', '>', Carbon::now()->subDay())
            ->count();
    }

    private function securityAlertsCount(): int
    {
        if (!Schema::hasTable('security_events')) {
            return 0;
        }
        return (int) DB::table('security_events')
            ->where('created_at', '>', Carbon::now()->subDays(7))
            ->whereIn('severity', ['high', 'critical'])
            ->count();
    }

    /** Clear all dashboard caches (e.g. after refresh button). */
    public function clearCache(): void
    {
        $tags = [
            $this->cachePrefix . '.product',
            $this->cachePrefix . '.order',
            $this->cachePrefix . '.payment',
            $this->cachePrefix . '.user',
            $this->cachePrefix . '.delivery',
            $this->cachePrefix . '.support',
            $this->cachePrefix . '.system',
            $this->cachePrefix . '.security',
            $this->cachePrefix . '.report',
            $this->cachePrefix . '.courier',
            $this->cachePrefix . '.subscriber',
            $this->cachePrefix . '.charts',
            $this->cachePrefix . '.live',
        ];
        foreach ($tags as $key) {
            Cache::forget($key);
        }
        Cache::forget($this->cachePrefix . '.live.feed.10');
    }
}
