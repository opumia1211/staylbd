<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductComparison;
use App\Models\SearchLog;
use App\Models\UserActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserActivityReportController extends Controller
{
    protected const CACHE_TTL = 600;

    protected function baseQuery(Request $request, ?array $actionTypes = null)
    {
        $query = UserActivityLog::query()->orderBy('id', 'desc')->with('user');
        $query->searchable(['description', 'user:username']);
        $query->dateFilter('created_at');

        if ($actionTypes !== null && count($actionTypes) > 0) {
            $query->whereIn('action_type', $actionTypes);
        }
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'LIKE', '%' . $request->ip_address . '%');
        }
        if ($request->filled('country')) {
            $query->where('country', 'LIKE', '%' . $request->country . '%');
        }

        return $query;
    }

    protected function stats(?array $actionTypes = null): array
    {
        $cacheKey = 'activity_report_stats_' . ($actionTypes ? implode('_', $actionTypes) : 'all');
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($actionTypes) {
            $q = UserActivityLog::query();
            if ($actionTypes !== null && count($actionTypes) > 0) {
                $q->whereIn('action_type', $actionTypes);
            }
            $todayStart = Carbon::today();
            $weekStart = Carbon::now()->subDays(7);
            return [
                'total' => (clone $q)->count(),
                'today' => (clone $q)->where('created_at', '>=', $todayStart)->count(),
                'week' => (clone $q)->where('created_at', '>=', $weekStart)->count(),
            ];
        });
    }

    protected function reportPage(Request $request, string $pageTitle, ?array $actionTypes, string $routeName, string $exportRouteName)
    {
        $baseQuery = $this->baseQuery($request, $actionTypes);
        $perPage = in_array((int) $request->per_page, [10, 20, 50, 100, 200], true) ? (int) $request->per_page : getPaginate();
        $logs = $baseQuery->paginate($perPage)->withQueryString();
        $stats = $this->stats($actionTypes);
        $emptyMessage = __('No activity found.');
        return view('admin.reports.activity_log', compact('pageTitle', 'logs', 'stats', 'emptyMessage', 'routeName', 'exportRouteName', 'actionTypes'));
    }

    protected function exportCsv(Request $request, ?array $actionTypes, string $filenamePrefix): StreamedResponse
    {
        $query = $this->baseQuery($request, $actionTypes);
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filenamePrefix . '_' . date('Y-m-d_His') . '.csv"',
        ];
        return response()->stream(function () use ($query) {
            $stream = fopen('php://output', 'w');
            fprintf($stream, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($stream, ['Action', 'Description', 'User', 'Username', 'IP', 'Device', 'Browser', 'OS', 'Country', 'City', 'At']);
            $query->chunk(500, function ($logs) use ($stream) {
                foreach ($logs as $log) {
                    fputcsv($stream, [
                        $log->action_type ?? '—',
                        $log->description ?? '—',
                        $log->user?->fullname ?? __('Guest'),
                        $log->user?->username ?? '—',
                        $log->ip_address ?? '—',
                        $log->device ?? '—',
                        $log->browser ?? '—',
                        $log->os ?? '—',
                        $log->country ?? '—',
                        $log->city ?? '—',
                        $log->created_at?->format('Y-m-d H:i:s') ?? '—',
                    ]);
                }
            });
            fclose($stream);
        }, 200, $headers);
    }

    /**
     * Bulk delete user activity logs: selected ids, or by count (10/20/50/100/200/all), or all (with same filters as list).
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'action' => 'required|in:selected,count,all',
            'limit'  => 'nullable|in:10,20,50,100,200,all',
        ]);

        $actionTypes = $request->input('action_types');
        if (is_array($actionTypes) && count($actionTypes) > 0) {
            $actionTypes = array_values(array_filter(array_map('strval', $actionTypes)));
        } else {
            $actionTypes = null;
        }
        $query = $this->baseQuery($request, $actionTypes);

        if ($request->action === 'selected') {
            $ids = $request->input('ids', []);
            $ids = is_array($ids) ? array_filter(array_map('intval', $ids)) : [];
            $deleted = UserActivityLog::whereIn('id', $ids)->delete();
        } elseif ($request->action === 'count') {
            $limit = $request->input('limit', '10');
            if ($limit === 'all') {
                $toDelete = $query->orderBy('id')->get();
            } else {
                $n = (int) $limit;
                $toDelete = $query->orderBy('id')->limit($n)->get();
            }
            $deleted = $toDelete->isEmpty() ? 0 : UserActivityLog::whereIn('id', $toDelete->pluck('id'))->delete();
        } else {
            $query->orderBy('id');
            $deleted = $query->delete();
        }

        $notify[] = ['success', __(':count activity log(s) deleted.', ['count' => $deleted])];
        return back()->withNotify($notify);
    }

    public function search(Request $request)
    {
        $actionTypes = [UserActivityLog::SEARCH_TEXT, UserActivityLog::SEARCH_VOICE, UserActivityLog::SEARCH_IMAGE];
        return $this->reportPage($request, __('Search Reports'), $actionTypes, 'admin.report.activity.search', 'admin.report.activity.search.export');
    }

    public function searchExport(Request $request): StreamedResponse
    {
        return $this->exportCsv($request, [UserActivityLog::SEARCH_TEXT, UserActivityLog::SEARCH_VOICE, UserActivityLog::SEARCH_IMAGE], 'activity_search');
    }

    public function productViews(Request $request)
    {
        return $this->reportPage($request, __('Product View Reports'), [UserActivityLog::PRODUCT_VIEW], 'admin.report.activity.product_views', 'admin.report.activity.product_views.export');
    }

    public function productViewsExport(Request $request): StreamedResponse
    {
        return $this->exportCsv($request, [UserActivityLog::PRODUCT_VIEW], 'activity_product_views');
    }

    public function cart(Request $request)
    {
        return $this->reportPage($request, __('Cart Activity Reports'), [UserActivityLog::CART_ADD, UserActivityLog::CART_REMOVE], 'admin.report.activity.cart', 'admin.report.activity.cart.export');
    }

    public function cartExport(Request $request): StreamedResponse
    {
        return $this->exportCsv($request, [UserActivityLog::CART_ADD, UserActivityLog::CART_REMOVE], 'activity_cart');
    }

    public function wishlist(Request $request)
    {
        return $this->reportPage($request, __('Wishlist Reports'), [UserActivityLog::WISHLIST_ADD, UserActivityLog::WISHLIST_REMOVE], 'admin.report.activity.wishlist', 'admin.report.activity.wishlist.export');
    }

    public function wishlistExport(Request $request): StreamedResponse
    {
        return $this->exportCsv($request, [UserActivityLog::WISHLIST_ADD, UserActivityLog::WISHLIST_REMOVE], 'activity_wishlist');
    }

    public function compare(Request $request)
    {
        return $this->reportPage($request, __('Compare Reports'), [UserActivityLog::COMPARE_ADD, UserActivityLog::COMPARE_REMOVE], 'admin.report.activity.compare', 'admin.report.activity.compare.export');
    }

    public function compareExport(Request $request): StreamedResponse
    {
        return $this->exportCsv($request, [UserActivityLog::COMPARE_ADD, UserActivityLog::COMPARE_REMOVE], 'activity_compare');
    }

    /**
     * Remove comparison entries (ProductComparison rows) for a single
     * log entry selected from the Compare activity report.
     */
    public function compareDelete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:user_activity_logs,id',
        ]);

        $log = UserActivityLog::findOrFail($request->id);

        if ($log->model_type === 'product' && $log->model_id) {
            ProductComparison::where('product_id', $log->model_id)
                ->when($log->user_id, function ($q) use ($log) {
                    return $q->where('user_id', $log->user_id);
                })
                ->delete();
        }

        $notify[] = ['success', __('Compare list entries removed for this product (if any).')];
        return back()->withNotify($notify);
    }

    /**
     * Bulk remove comparison entries (ProductComparison rows) for
     * multiple selected log rows from the Compare activity report.
     */
    public function compareBulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $logs = UserActivityLog::whereIn('id', $request->ids)->get();

        foreach ($logs as $log) {
            if ($log->model_type === 'product' && $log->model_id) {
                ProductComparison::where('product_id', $log->model_id)
                    ->when($log->user_id, function ($q) use ($log) {
                        return $q->where('user_id', $log->user_id);
                    })
                    ->delete();
            }
        }

        $notify[] = ['success', __('Selected compare list entries have been removed (where they existed).')];
        return back()->withNotify($notify);
    }

    public function orders(Request $request)
    {
        return $this->reportPage($request, __('Order Activity Reports'), [UserActivityLog::ORDER_PLACE, UserActivityLog::ORDER_CANCEL], 'admin.report.activity.orders', 'admin.report.activity.orders.export');
    }

    public function ordersExport(Request $request): StreamedResponse
    {
        return $this->exportCsv($request, [UserActivityLog::ORDER_PLACE, UserActivityLog::ORDER_CANCEL], 'activity_orders');
    }

    /** Track Order – which order numbers were searched on the track order page (visible to admin). */
    public function trackOrder(Request $request)
    {
        return $this->reportPage($request, __('Track Order Searches'), [UserActivityLog::TRACK_ORDER], 'admin.report.activity.track_order', 'admin.report.activity.track_order.export');
    }

    public function trackOrderExport(Request $request): StreamedResponse
    {
        return $this->exportCsv($request, [UserActivityLog::TRACK_ORDER], 'activity_track_order');
    }

    public function payments(Request $request)
    {
        return $this->reportPage($request, __('Payment Reports'), [UserActivityLog::PAYMENT_ATTEMPT, UserActivityLog::PAYMENT_SUCCESS, UserActivityLog::PAYMENT_FAILURE], 'admin.report.activity.payments', 'admin.report.activity.payments.export');
    }

    public function paymentsExport(Request $request): StreamedResponse
    {
        return $this->exportCsv($request, [UserActivityLog::PAYMENT_ATTEMPT, UserActivityLog::PAYMENT_SUCCESS, UserActivityLog::PAYMENT_FAILURE], 'activity_payments');
    }

    public function login(Request $request)
    {
        return $this->reportPage($request, __('Login Activity Reports'), [UserActivityLog::LOGIN, UserActivityLog::LOGOUT, UserActivityLog::LOGIN_FAILED], 'admin.report.activity.login', 'admin.report.activity.login.export');
    }

    public function loginExport(Request $request): StreamedResponse
    {
        return $this->exportCsv($request, [UserActivityLog::LOGIN, UserActivityLog::LOGOUT, UserActivityLog::LOGIN_FAILED], 'activity_login');
    }

    public function registration(Request $request)
    {
        return $this->reportPage($request, __('Registration Reports'), [UserActivityLog::REGISTRATION], 'admin.report.activity.registration', 'admin.report.activity.registration.export');
    }

    public function registrationExport(Request $request): StreamedResponse
    {
        return $this->exportCsv($request, [UserActivityLog::REGISTRATION], 'activity_registration');
    }

    public function messages(Request $request)
    {
        return $this->reportPage($request, __('Contact & Live Chat Reports'), [UserActivityLog::CONTACT_SUBMIT, UserActivityLog::LIVE_CHAT], 'admin.report.activity.messages', 'admin.report.activity.messages.export');
    }

    public function messagesExport(Request $request): StreamedResponse
    {
        return $this->exportCsv($request, [UserActivityLog::CONTACT_SUBMIT, UserActivityLog::LIVE_CHAT], 'activity_messages');
    }

    public function location(Request $request)
    {
        return $this->reportPage($request, __('Location Tracking Reports'), null, 'admin.report.activity.location', 'admin.report.activity.location.export');
    }

    public function locationExport(Request $request): StreamedResponse
    {
        return $this->exportCsv($request, null, 'activity_location');
    }

    public function allActivity(Request $request)
    {
        return $this->reportPage($request, __('Full Activity Timeline'), null, 'admin.report.activity.all', 'admin.report.activity.all.export');
    }

    public function allActivityExport(Request $request): StreamedResponse
    {
        return $this->exportCsv($request, null, 'activity_all');
    }

    public function liveMonitor(Request $request)
    {
        $logs = UserActivityLog::query()->orderBy('id', 'desc')->with('user')->limit(50)->get();
        $pageTitle = __('Live Activity Monitor');
        return view('admin.reports.live_monitor', compact('pageTitle', 'logs'));
    }

    /**
     * Real-time analytics dashboard with cached widgets.
     */
    public function dashboard(Request $request)
    {
        $pageTitle = __('Activity Analytics Dashboard');
        $ttl = config('activity.cache_ttl', self::CACHE_TTL);

        $widgets = Cache::remember('activity_analytics_widgets', $ttl, function () {
            $todayStart = Carbon::today();
            $fiveMinAgo = Carbon::now()->subMinutes(5);
            $todayOrders = UserActivityLog::where('action_type', UserActivityLog::ORDER_PLACE)->where('created_at', '>=', $todayStart)->count();
            $todayProductViews = UserActivityLog::where('action_type', UserActivityLog::PRODUCT_VIEW)->where('created_at', '>=', $todayStart)->count();
            $todayUniqueUsers = UserActivityLog::where('created_at', '>=', $todayStart)->distinct('session_id')->count('session_id');
            $realtimeVisitors = UserActivityLog::where('created_at', '>=', $fiveMinAgo)->distinct('session_id')->count('session_id');
            $todayCartAdds = UserActivityLog::where('action_type', UserActivityLog::CART_ADD)->where('created_at', '>=', $todayStart)->count();
            $abandonedCart = max(0, $todayCartAdds - $todayOrders);
            $paymentAttempts = UserActivityLog::where('action_type', UserActivityLog::PAYMENT_ATTEMPT)->where('created_at', '>=', $todayStart)->count();
            $paymentSuccess = UserActivityLog::where('action_type', UserActivityLog::PAYMENT_SUCCESS)->where('created_at', '>=', $todayStart)->count();
            $paymentFailures = UserActivityLog::where('action_type', UserActivityLog::PAYMENT_FAILURE)->where('created_at', '>=', $todayStart)->count();
            $failedPaymentRate = $paymentAttempts > 0 ? round(($paymentFailures / $paymentAttempts) * 100, 1) : 0;
            $conversionRate = $todayProductViews > 0 ? round(($todayOrders / $todayProductViews) * 100, 2) : 0;
            $loginFailuresToday = UserActivityLog::where('action_type', UserActivityLog::LOGIN_FAILED)->where('created_at', '>=', $todayStart)->count();
            $countryWise = UserActivityLog::whereNotNull('country')->where('country', '!=', '')
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->select('country', DB::raw('count(*) as total'))
                ->groupBy('country')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            $zeroResultSearches = 0;
            $topSearchKeywords = [];
            if (class_exists(SearchLog::class) && \Illuminate\Support\Facades\Schema::hasTable('user_search_logs')) {
                $zeroResultSearches = SearchLog::where('created_at', '>=', $todayStart)->where('results_count', 0)->count();
                $topSearchKeywords = SearchLog::where('created_at', '>=', Carbon::now()->subDays(7))
                    ->select('query', DB::raw('count(*) as cnt'))
                    ->groupBy('query')
                    ->orderByDesc('cnt')
                    ->limit(10)
                    ->get();
            }

            return [
                'today_active_users' => $todayUniqueUsers,
                'realtime_visitors' => $realtimeVisitors,
                'conversion_rate' => $conversionRate,
                'today_orders' => $todayOrders,
                'abandoned_cart_count' => $abandonedCart,
                'failed_payment_rate' => $failedPaymentRate,
                'login_failures_today' => $loginFailuresToday,
                'country_wise' => $countryWise,
                'zero_result_searches' => $zeroResultSearches ?? 0,
                'top_search_keywords' => $topSearchKeywords ?? [],
            ];
        });

        return view('admin.reports.activity_analytics_dashboard', compact('pageTitle', 'widgets'));
    }

    /**
     * List auto-flagged suspicious activities (fraud detection).
     */
    public function suspicious(Request $request)
    {
        if (!class_exists(\App\Models\SuspiciousActivity::class) || !\Illuminate\Support\Facades\Schema::hasTable('suspicious_activities')) {
            abort(404);
        }
        $pageTitle = __('Suspicious Activities');
        $query = \App\Models\SuspiciousActivity::query()->orderBy('id', 'desc')->with(['user', 'activityLog']);
        if ($request->filled('resolved')) {
            $query->where('resolved', (int) $request->resolved);
        }
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }
        $list = $query->paginate(getPaginate())->withQueryString();
        return view('admin.reports.suspicious_activities', compact('pageTitle', 'list'));
    }

    public function liveMonitorData(Request $request)
    {
        $logs = UserActivityLog::query()->orderBy('id', 'desc')->with('user')->limit(50)->get();
        $html = view('admin.reports.partials.live_monitor_rows', compact('logs'))->render();
        return response()->json(['html' => $html]);
    }
}
