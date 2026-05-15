<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\DB;

/**
 * Business Intelligence Dashboard: Revenue trends, CLV, Conversion, Funnel Analysis.
 */
class BusinessDashboardService
{
    /**
     * Get Daily Revenue for last 30 days.
     */
    public function getRevenueTrends(int $days = 30): array
    {
        return Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as total')
        )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();
    }

    /**
     * Calculate Conversion Rate (Orders / Unique Visitors).
     */
    public function getConversionRate(): float
    {
        $visitors = UserActivityLog::distinct('session_id')->count();
        $orders = Order::count();
        
        if ($visitors === 0) return 0.0;
        return round(($orders / $visitors) * 100, 2);
    }

    /**
     * Calculate average Customer Lifetime Value (CLV).
     */
    public function getAverageCLV(): float
    {
        $avgOrderValue = Order::avg('total') ?? 0;
        $purchaseFrequency = Order::count() / max(1, User::count());
        
        return round($avgOrderValue * $purchaseFrequency, 2);
    }

    /**
     * Funnel Analysis: Visitors -> View Product -> Add to Cart -> Purchase.
     */
    public function getFunnelAnalysis(): array
    {
        $totalSessions = UserActivityLog::distinct('session_id')->count();
        
        $productViews = UserActivityLog::where('action_type', UserActivityLog::PRODUCT_VIEW)
            ->distinct('session_id')
            ->count();
            
        $cartAdds = UserActivityLog::where('action_type', UserActivityLog::CART_ADD)
            ->distinct('session_id')
            ->count();
            
        $purchases = Order::distinct('user_id')->count(); // Approximation by session mapping if active

        return [
            'sessions' => $totalSessions,
            'view_product' => $productViews,
            'add_to_cart' => $cartAdds,
            'purchase' => $purchases,
            'dropoff_view' => $totalSessions > 0 ? round((1 - ($productViews / $totalSessions)) * 100, 1) : 0,
            'dropoff_cart' => $productViews > 0 ? round((1 - ($cartAdds / $productViews)) * 100, 1) : 0,
            'dropoff_purchase' => $cartAdds > 0 ? round((1 - ($purchases / $cartAdds)) * 100, 1) : 0,
        ];
    }

    /**
     * Get Top 5 Performing Products.
     */
    public function getTopProducts(int $limit = 5)
    {
        return \App\Models\OrderDetail::select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(quantity * price) as revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->limit($limit)
            ->get();
    }

    /**
     * Get Revenue Distribution by Category.
     */
    public function getCategoryDistribution()
    {
        return DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_details.quantity * order_details.price) as revenue'))
            ->groupBy('categories.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();
    }

    /**
     * Calculate Customer Retention Rate.
     */
    public function getRetentionRate(): float
    {
        $totalCustomers = User::count();
        if ($totalCustomers === 0) return 0;
        
        $returningCustomers = Order::select('user_id')
            ->groupBy('user_id')
            ->having(DB::raw('count(id)'), '>', 1)
            ->get()
            ->count();
            
        return round(($returningCustomers / $totalCustomers) * 100, 1);
    }

    /**
     * Get Monthly Revenue Growth.
     */
    public function getRevenueGrowth(): float
    {
        $thisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');
            
        $lastMonth = Order::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total');
            
        if ($lastMonth == 0) return $thisMonth > 0 ? 100 : 0;
        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    /**
     * Get Hourly Sales for the last 24 hours (Real-time feel).
     */
    public function getHourlySales(): array
    {
        return Order::select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('SUM(total) as total')
        )
            ->where('created_at', '>=', now()->subDay())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('total', 'hour')
            ->toArray();
    }

    /**
     * Get Top Search Keywords from activity logs.
     */
    public function getTopKeywords(int $limit = 5)
    {
        return UserActivityLog::whereIn('action_type', [
                UserActivityLog::SEARCH_TEXT, 
                UserActivityLog::SEARCH_VOICE, 
                UserActivityLog::SEARCH_IMAGE
            ])
            ->select('description', DB::raw('COUNT(*) as count'))
            ->groupBy('description')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                $item->action_details = $item->description; // Mapping for frontend compatibility
                return $item;
            });
    }
}
