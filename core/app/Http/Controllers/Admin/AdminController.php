<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use App\Models\AdminNotification;
use App\Models\AdminReport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\Subcategory;
use App\Models\Subscriber;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLogin;
use App\Rules\FileTypeValidate;
use App\Services\DashboardService;
use App\Services\BusinessDashboardService;
use App\Services\AdminAiAssistantService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /** Dashboard cache TTL (seconds). Admin/session data must not be cached; only aggregated stats. */
    const DASHBOARD_CACHE_TTL = 90;

    /**
     * Admin dashboard – uses DashboardService; real-time counts, charts cached.
     */
    public function dashboard(DashboardService $dashboardService)
    {
        $pageTitle = 'Dashboard';
        $emptyMessage = __('No orders yet');

        if (request()->boolean('refresh')) {
            $dashboardService->clearCache();
        }

        try {
            $data = $dashboardService->getFullDashboard(request()->boolean('refresh'));
        } catch (\Throwable $e) {
            Log::warning('Admin dashboard data load failed: ' . $e->getMessage());
            $data = $this->getDashboardDataFallback();
        }

        $widget = $data['widget'] ?? [];
        $chart = $data['chart'] ?? [];
        $deposit = $data['deposit'] ?? [];
        $order = $data['order'] ?? [];
        $depositsMonth = $data['depositsMonth'] ?? collect([]);
        $months = $data['months'] ?? collect([]);
        $monthlyDepositAmounts = $data['monthlyDepositAmounts'] ?? [];
        $delivered = $data['delivered'] ?? ['per_day' => collect([]), 'per_day_amount' => collect([])];
        $trxReport = $data['trxReport'] ?? ['date' => []];
        $plusTrx = $data['plusTrx'] ?? collect([]);
        $minusTrx = $data['minusTrx'] ?? collect([]);
        $orders = $data['orders'] ?? ['per_day' => collect([]), 'per_day_amount' => collect([])];
        $recentOrders = $data['recentOrders'] ?? collect([]);
        $lowStockProducts = $data['lowStockProducts'] ?? collect([]);
        $recentOrdersForActivity = $data['recentOrdersForActivity'] ?? collect([]);
        $recentUsersForActivity = $data['recentUsersForActivity'] ?? collect([]);
        $recentDepositsForActivity = $data['recentDepositsForActivity'] ?? collect([]);
        $dashboard = $data['dashboard'] ?? [];

        return view('admin.dashboard', compact(
            'pageTitle', 'emptyMessage', 'widget', 'chart', 'deposit', 'depositsMonth', 'months', 'monthlyDepositAmounts',
            'delivered', 'trxReport', 'plusTrx', 'minusTrx', 'order', 'orders', 'recentOrders',
            'lowStockProducts', 'recentOrdersForActivity', 'recentUsersForActivity', 'recentDepositsForActivity',
            'dashboard'
        ));
    }

    /**
     * Advanced Business Intelligence & AI Insights.
     */
    public function businessInsights(BusinessDashboardService $biService, AdminAiAssistantService $aiService)
    {
        $pageTitle = 'Business Intelligence & AI Insights';
        
        $revenueTrends = $biService->getRevenueTrends();
        $conversionRate = $biService->getConversionRate();
        $averageCLV = $biService->getAverageCLV();
        $funnel = $biService->getFunnelAnalysis();
        $aiSummary = $aiService->getSalesInsights();
        $topProducts = $biService->getTopProducts();
        $categoryDistribution = $biService->getCategoryDistribution();
        $retentionRate = $biService->getRetentionRate();
        $revenueGrowth = $biService->getRevenueGrowth();

        // Real-time & Extra Metrics
        $recentOrders = Order::with('user')->latest()->limit(5)->get();
        $lowStockProducts = Product::where('quantity', '<=', 5)->orderBy('quantity')->limit(5)->get();
        $topKeywords = $biService->getTopKeywords(); // Fixed method name if needed or mocked

        return view('admin.business_insights', compact(
            'pageTitle', 'revenueTrends', 'conversionRate', 'averageCLV', 'funnel', 'aiSummary', 'topProducts',
            'categoryDistribution', 'retentionRate', 'revenueGrowth', 'recentOrders', 'lowStockProducts', 'topKeywords'
        ));
    }

    /**
     * JSON stats for dashboard live refresh (AJAX every 60s).
     */
    public function dashboardStats(DashboardService $dashboardService)
    {
        try {
            $stats = $dashboardService->getLiveStats();
            return response()->json(['ok' => true, 'stats' => $stats]);
        } catch (\Throwable $e) {
            Log::warning('Dashboard stats API failed: ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Unable to load stats'], 500);
        }
    }

    /**
     * Real-time stats: counts and recent items (no cache – reflects current DB).
     */
    private function getDashboardRealtime(): array
    {
        $todayStart = Carbon::today();
        $thisWeekStart = Carbon::now()->startOfWeek();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        // All counts from DB – no scope so every product/category is counted
        $widget = [
            'total_users'              => User::count(),
            'verified_users'           => User::active()->count(),
            'email_unverified_users'   => User::emailUnverified()->count(),
            'mobile_unverified_users'  => User::mobileUnverified()->count(),
            'total_product'            => Product::count(),
            'total_category'           => Category::count(),
            'total_brands'             => Brand::count(),
            'total_subcategory'        => Subcategory::count(),
            'total_coupon'             => Coupon::count(),
            'ticket_pending'           => SupportTicket::where('status', Status::TICKET_OPEN)->count(),
            'total_subscriber'         => Subscriber::count(),
            'total_shipping_methods'   => ShippingMethod::count(),
            'product_featured'        => Product::where('featured_product', Status::YES)->count(),
            'product_today_deals'      => Product::where('today_deals', Status::YES)->count(),
            'unread_notifications'     => AdminNotification::where('is_read', Status::NO)->count(),
            'report_pending'           => AdminReport::pending()->count(),
        ];

        $deposit = [
            'total_deposit_amount'  => Deposit::successful()->sum('amount'),
            'total_deposit_pending'  => Deposit::pending()->count(),
            'total_deposit_rejected' => Deposit::rejected()->count(),
            'total_deposit_charge'  => Deposit::successful()->sum('charge'),
        ];

        $order = [
            'total_order'      => Order::count(),
            'pending_order'    => Order::pending()->count(),
            'rejected_order'   => Order::cancel()->count(),
            'shipped_order'    => Order::shipped()->count(),
            'confirmed_order'  => Order::confirmed()->count(),
            'delivered_order'  => Order::delivered()->count(),
        ];

        $lowStockThreshold = 5;
        $widget['low_stock_count'] = Product::where('quantity', '<=', $lowStockThreshold)->where('quantity', '>=', 0)->count();
        $lowStockProducts = Product::where('quantity', '<=', $lowStockThreshold)->where('quantity', '>=', 0)
            ->orderBy('quantity')->take(8)->get(['id', 'name', 'quantity', 'product_sku']);

        $widget['orders_today'] = Order::where('created_at', '>=', $todayStart)->count();
        $widget['new_users_today'] = User::where('created_at', '>=', $todayStart)->count();
        $widget['today_revenue'] = Order::where('created_at', '>=', $todayStart)->where('order_status', Status::ORDER_DELIVERED)->sum('total');
        $widget['pending_payments_today'] = Deposit::where('created_at', '>=', $todayStart)->pending()->count();
        $widget['revenue_this_week'] = Order::where('created_at', '>=', $thisWeekStart)->where('order_status', Status::ORDER_DELIVERED)->sum('total');
        $widget['revenue_last_week'] = Order::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->where('order_status', Status::ORDER_DELIVERED)->sum('total');
        $widget['orders_this_week'] = Order::where('created_at', '>=', $thisWeekStart)->count();
        $widget['orders_last_week'] = Order::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();

        $recentOrders = Order::with('user')->latest()->take(10)->get();
        $recentOrdersForActivity = Order::with('user:id,username')->latest()->take(5)->get(['id', 'order_no', 'total', 'order_status', 'user_id', 'created_at']);
        $recentUsersForActivity = User::latest()->take(3)->get(['id', 'username', 'created_at']);
        $recentDepositsForActivity = Deposit::latest()->take(3)->get(['id', 'amount', 'status', 'created_at']);

        return compact(
            'widget', 'deposit', 'order', 'recentOrders', 'lowStockProducts',
            'recentOrdersForActivity', 'recentUsersForActivity', 'recentDepositsForActivity'
        );
    }

    /**
     * Chart data only (cached to reduce DB load).
     */
    private function getDashboardCharts(): array
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $userLoginData = UserLogin::where('created_at', '>=', $thirtyDaysAgo)->get(['browser', 'os', 'country']);
        $chart = [
            'user_browser_counter'  => $userLoginData->groupBy('browser')->map->count(),
            'user_os_counter'      => $userLoginData->groupBy('os')->map->count(),
            'user_country_counter' => $userLoginData->groupBy('country')->map->count()->sort()->reverse()->take(5),
        ];

        $plusTrx = Transaction::where('trx_type', '+')->where('created_at', '>=', $thirtyDaysAgo)
            ->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date")
            ->orderBy('created_at')->groupBy('date')->get();
        $minusTrx = Transaction::where('trx_type', '-')->where('created_at', '>=', $thirtyDaysAgo)
            ->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date")
            ->orderBy('created_at')->groupBy('date')->get();
        $trxReport = ['date' => dateSorting($plusTrx->pluck('date')->merge($minusTrx->pluck('date'))->unique()->values()->toArray())];

        $depositsMonth = Deposit::where('created_at', '>=', Carbon::now()->subYear())
            ->where('status', Status::PAYMENT_SUCCESS)
            ->selectRaw("SUM(CASE WHEN status = " . Status::PAYMENT_SUCCESS . " THEN amount END) as depositAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')->groupBy('months')->get();
        $report = ['months' => collect([]), 'deposit_month_amount' => collect([])];
        $depositsMonth->map(function ($d) use ($report) {
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
            'per_day'        => $deliveredRows->map(fn ($r) => date('d M', strtotime($r->day)))->values(),
            'per_day_amount' => $deliveredRows->map(fn ($r) => $r->totalAmount + 0)->values(),
        ];

        $orderRows = Order::where('created_at', '>=', $thirtyDaysAgo)
            ->selectRaw('COUNT(id) as totalOrder, DATE(created_at) as day')
            ->groupBy('day')->get();
        $orders = [
            'per_day'        => $orderRows->map(fn ($r) => date('d M', strtotime($r->day)))->values(),
            'per_day_amount' => $orderRows->map(fn ($r) => $r->totalOrder + 0)->values(),
        ];

        return compact(
            'chart', 'depositsMonth', 'months', 'monthlyDepositAmounts', 'delivered', 'trxReport',
            'plusTrx', 'minusTrx', 'orders'
        );
    }

    /**
     * Fallback when DB/cache fails – safe defaults so dashboard still renders.
     */
    private function getDashboardDataFallback(): array
    {
        $empty = collect([]);
        return [
            'widget' => [
                'total_users' => 0, 'verified_users' => 0, 'email_unverified_users' => 0, 'mobile_unverified_users' => 0,
                'total_product' => 0, 'total_category' => 0, 'total_brands' => 0, 'total_subcategory' => 0,
                'total_coupon' => 0, 'ticket_pending' => 0, 'total_subscriber' => 0, 'total_shipping_methods' => 0,
                'product_featured' => 0, 'product_today_deals' => 0, 'low_stock_count' => 0,
                'unread_notifications' => 0, 'report_pending' => 0,
                'orders_today' => 0, 'new_users_today' => 0, 'today_revenue' => 0, 'pending_payments_today' => 0,
                'revenue_this_week' => 0, 'revenue_last_week' => 0, 'orders_this_week' => 0, 'orders_last_week' => 0,
            ],
            'chart' => ['user_browser_counter' => $empty, 'user_os_counter' => $empty, 'user_country_counter' => $empty],
            'deposit' => ['total_deposit_amount' => 0, 'total_deposit_pending' => 0, 'total_deposit_rejected' => 0, 'total_deposit_charge' => 0],
            'order' => ['total_order' => 0, 'pending_order' => 0, 'rejected_order' => 0, 'shipped_order' => 0, 'confirmed_order' => 0, 'delivered_order' => 0],
            'depositsMonth' => $empty, 'months' => $empty, 'monthlyDepositAmounts' => [],
            'delivered' => ['per_day' => $empty, 'per_day_amount' => $empty],
            'trxReport' => ['date' => []], 'plusTrx' => $empty, 'minusTrx' => $empty,
            'orders' => ['per_day' => $empty, 'per_day_amount' => $empty],
            'recentOrders' => $empty, 'lowStockProducts' => $empty,
            'recentOrdersForActivity' => $empty, 'recentUsersForActivity' => $empty, 'recentDepositsForActivity' => $empty,
            'dashboard' => [
                'product' => [], 'order' => [], 'payment' => [], 'user' => [], 'delivery' => [],
                'support' => [], 'system' => ['database_status' => 'ok', 'cache_status' => 'ok', 'storage_usage_percent' => 0],
                'security' => ['failed_logins_24h' => 0, 'lockout_count' => 0, 'admin_count' => 0, 'admin_with_2fa' => 0, 'two_fa_percent' => 0],
                'report' => ['transactions_week' => 0, 'login_history_week' => 0, 'notification_history_week' => 0],
                'courier' => ['failed_courier_count' => 0],
                'subscriber' => ['total_subscriber' => 0, 'subscriber_growth_percent' => 0],
                'alerts' => [], 'revenue_overview' => ['revenue_today_vs_yesterday_percent' => 0],
            ],
        ];
    }

    public function profile()
    {
        $pageTitle = 'Profile';
        $admin     = auth('admin')->user();
        return view('admin.profile', compact('pageTitle', 'admin'));
    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required|string|max:191',
            'email' => 'required|email',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp', 'svg'])],
        ], [
            'image' => __('Profile image must be PNG, JPG, WebP or SVG. Images are automatically converted to WebP to save space.'),
        ]);
        $user = auth('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old         = $user->image;
                $user->image = fileUploader($request->image, getFilePath('adminProfile'), getFileSize('adminProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', __('Could not upload image. Please use PNG, JPG, WebP or SVG.')];
                return back()->withNotify($notify);
            }
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', __('Profile updated successfully.')];
        return to_route('admin.profile')->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $admin     = auth('admin')->user();
        return view('admin.password', compact('pageTitle', 'admin'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => ['required', 'string'],
            'password'     => ['required', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ], [
            'old_password.required' => __('Current password is required.'),
            'password.required'     => __('New password is required.'),
            'password.confirmed'    => __('New password confirmation does not match.'),
        ]);

        $user = auth('admin')->user();

        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', __('The current password you entered is incorrect. Please try again.')];
            return back()->withNotify($notify)->withInput($request->only('password', 'password_confirmation'));
        }

        $user->password = Hash::make($request->password);
        $user->force_password_change = false;
        $user->save();

        $notify[] = ['success', __('Your password has been updated successfully. Please use your new password for the next login.')];
        return to_route('admin.password')->withNotify($notify);
    }

    public function notifications()
    {
        $notifications = AdminNotification::orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        $pageTitle     = __('Notifications');
        $emptyMessage  = __('No notifications yet.');
        $breadcrumb = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Manage Orders'), 'url' => route('admin.orders.index')],
            ['label' => __('Notifications')],
        ];
        return view('admin.notifications', compact('pageTitle', 'notifications', 'emptyMessage', 'breadcrumb'));
    }

    /**
     * Delivery scan notifications: when delivery man or customer scans invoice QR.
     * Monitor and view these messages from this page.
     */
    public function deliveryScanNotifications()
    {
        $notifications = AdminNotification::query()
            ->where(function ($q) {
                $q->where('title', 'like', '%scanned%')
                    ->orWhere('title', 'like', '%delivery man%')
                    ->orWhere('title', 'like', '%Product is with%')
                    ->orWhere('title', 'like', '%Customer %');
            })
            ->orderBy('id', 'desc')
            ->with('user')
            ->paginate(getPaginate());
        $pageTitle = __('Delivery Scan Notifications');
        $emptyMessage = __('No delivery scan notifications yet.');
        return view('admin.notifications', compact('pageTitle', 'notifications', 'emptyMessage'));
    }

    public function notificationRead($id)
    {
        $notification          = AdminNotification::findOrFail($id);
        $notification->is_read = Status::YES;
        $notification->save();
        AdminNotification::clearNotificationCache();

        $url = $notification->click_url ?? '';
        $url = trim((string) $url);

        if ($url === '' || $url === '#') {
            return redirect()->to(url()->previous());
        }

        // Ensure full URL so redirect opens the correct admin section (order details, ticket view, deposit, etc.)
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = url($url);
        }

        return redirect()->to($url);
    }

    /**
     * Report & Request - Bug reports and feature requests (stored locally).
     * No manual edit needed when deploying - reports are admin-submitted and stored in DB.
     */
    public function requestReport(Request $request)
    {
        $pageTitle = 'Report & Request';
        $query = AdminReport::query()->latest();
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $reports = $query->paginate(15)->withQueryString();
        $stats = [
            'total' => AdminReport::count(),
            'bugs' => AdminReport::bug()->count(),
            'features' => AdminReport::feature()->count(),
            'pending' => AdminReport::pending()->count(),
        ];
        $emptyMessage = 'No reports yet';
        return view('admin.reports', compact('reports', 'pageTitle', 'stats', 'emptyMessage'));
    }

    public function reportSubmit(Request $request)
    {
        $request->validate([
            'type'    => 'required|in:bug,feature',
            'message' => 'required|string|max:5000',
        ]);

        $admin = auth()->guard('admin')->user();
        AdminReport::create([
            'type' => $request->type,
            'message' => $request->message,
            'status' => AdminReport::STATUS_PENDING,
            'admin_id' => $admin?->id,
            'admin_name' => $admin?->name ?? 'Admin',
            'page_url' => $request->header('referer'),
        ]);

        $notify[] = ['success', 'Report submitted successfully'];
        return back()->withNotify($notify);
    }

    public function reportStatusUpdate(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,read,resolved']);
        $report = AdminReport::findOrFail($id);
        $report->update(['status' => $request->status]);
        $notify[] = ['success', 'Status updated'];
        return back()->withNotify($notify);
    }

    public function reportDelete($id)
    {
        $report = AdminReport::findOrFail($id);
        $report->delete();
        $notify[] = ['success', 'Report deleted'];
        return back()->withNotify($notify);
    }

    public function readAll()
    {
        AdminNotification::where('is_read', Status::NO)->update([
            'is_read' => Status::YES,
        ]);
        AdminNotification::clearNotificationCache();
        $notify[] = ['success', 'Notifications read successfully'];
        return back()->withNotify($notify);
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        // Support relative path from public (e.g. assets/verify/filename.pdf)
        if (!preg_match('#^[a-z]:#i', $filePath) && !str_starts_with($filePath, '/')) {
            $filePath = public_path($filePath);
        }
        if (!is_file($filePath) || !is_readable($filePath)) {
            abort(404, 'File not found');
        }
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')) . '-attachment.' . $extension;
        $mimetype = mime_content_type($filePath);
        return response()->download($filePath, $title, ['Content-Type' => $mimetype]);
    }
}
