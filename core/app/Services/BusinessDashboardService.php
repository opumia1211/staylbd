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
            DB::raw('SUM(total_amount) as total')
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
        $avgOrderValue = Order::avg('total_amount') ?? 0;
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
}
