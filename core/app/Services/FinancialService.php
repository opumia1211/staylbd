<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Deposit;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

/**
 * Financial Service: profitability tracking, expense analysis, and ROI calculation.
 */
class FinancialService
{
    /**
     * Get Net Profit Report for a date range.
     */
    public function getProfitReport(string $start, string $end): array
    {
        $revenue = Order::whereBetween('created_at', [$start, $end])
            ->where('payment_status', 1)
            ->sum('total_amount');
            
        // Expenses: Payment gateway charges + known operating costs
        $gatewayCharges = Deposit::whereBetween('created_at', [$start, $end])
            ->where('status', 1)
            ->sum('charge');
            
        // Assuming 5% miscellaneous operating expense (hosting, support, ads)
        $opex = $revenue * 0.05;
        
        // Cost of Goods Sold (Estimated at 50% for high-margin ecommerce)
        $cogs = $revenue * 0.50;

        $netProfit = $revenue - ($gatewayCharges + $opex + $cogs);

        return [
            'gross_revenue' => $revenue,
            'gateway_charges' => $gatewayCharges,
            'opex' => $opex,
            'cogs' => $cogs,
            'net_profit' => $netProfit,
            'margin_percent' => $revenue > 0 ? ($netProfit / $revenue) * 100 : 0
        ];
    }

    /**
     * ROI Calculation for Marketing Spend.
     */
    public function calculateMarketingROI(float $spend, float $ascribedRevenue): float
    {
        if ($spend <= 0) return 0;
        return (($ascribedRevenue - $spend) / $spend) * 100;
    }
}
