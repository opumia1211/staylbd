<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Constants\Status;
use Illuminate\Support\Facades\DB;

class PaymentAnalyticsController extends Controller
{
    public function index()
    {
        $pageTitle = __('Payment Analytics');

        $orderDeposits = Deposit::where('order_id', '>', 0);

        $totalOnline = (clone $orderDeposits)->where('status', Status::PAYMENT_SUCCESS)->sum('final_amo');
        $totalCount = (clone $orderDeposits)->where('status', Status::PAYMENT_SUCCESS)->count();
        $failedCount = (clone $orderDeposits)->where('status', Status::PAYMENT_REJECT)->count();
        $pendingCount = (clone $orderDeposits)->whereIn('status', [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])->count();
        $attemptCount = (clone $orderDeposits)->count();

        $successRate = $attemptCount > 0 ? round($totalCount / $attemptCount * 100, 1) : 0;
        $failedRate = $attemptCount > 0 ? round($failedCount / $attemptCount * 100, 1) : 0;

        $gatewayWise = Deposit::where('order_id', '>', 0)
            ->where('status', Status::PAYMENT_SUCCESS)
            ->select('method_code', DB::raw('COUNT(*) as count'), DB::raw('SUM(final_amo) as total'))
            ->groupBy('method_code')
            ->get();

        $gatewayNames = Gateway::whereIn('code', $gatewayWise->pluck('method_code'))->pluck('name', 'code');

        return view('admin.payment.analytics', compact(
            'pageTitle',
            'totalOnline',
            'totalCount',
            'failedCount',
            'pendingCount',
            'attemptCount',
            'successRate',
            'failedRate',
            'gatewayWise',
            'gatewayNames'
        ));
    }
}
