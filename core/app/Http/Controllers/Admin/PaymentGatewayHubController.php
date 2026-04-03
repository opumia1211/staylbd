<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gateway;
use App\Models\Deposit;
use App\Constants\Status;
use Illuminate\Support\Facades\DB;

class PaymentGatewayHubController extends Controller
{
    /**
     * Single entry: Payment Gateways hub at /payment-gateways
     * Links to Automatic, Manual, Autopay gateways + Deposit list + Analytics.
     */
    public function index()
    {
        $pageTitle = __('Payment Gateways');
        $automaticCount = Gateway::automatic()->count();
        $manualCount = Gateway::manual()->count();
        $autopayCount = Gateway::where('code', '>=', 2000)->count();
        $totalPayments = Deposit::where('order_id', '>', 0)->where('status', Status::PAYMENT_SUCCESS)->count();
        $pendingPayments = Deposit::where('order_id', '>', 0)->whereIn('status', [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])->count();

        return view('admin.gateways.hub', compact(
            'pageTitle',
            'automaticCount',
            'manualCount',
            'autopayCount',
            'totalPayments',
            'pendingPayments'
        ));
    }
}
