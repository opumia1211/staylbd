<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Gateway;
use Illuminate\Support\Facades\DB;

class PaymentGatewayHubController extends Controller
{
    /**
     * Finance center: gateways, payments, analytics — single professional hub.
     */
    public function index()
    {
        $pageTitle = __('Payment Center');
        $general = gs();

        $automaticGateways = Gateway::automatic()->with('currencies')->get();
        $manualGateways = Gateway::manual()->get();
        $autopayGateways = Gateway::where('code', '>=', 2000)->get();

        $automaticCount = $automaticGateways->count();
        $manualCount = $manualGateways->count();
        $autopayCount = $autopayGateways->count();
        $activeAutomatic = $automaticGateways->where('status', Status::ENABLE)->count();
        $activeManual = $manualGateways->where('status', Status::ENABLE)->count();
        $activeAutopay = $autopayGateways->where('status', Status::ENABLE)->count();
        $totalActiveGateways = $activeAutomatic + $activeManual + $activeAutopay;

        $orderDeposits = Deposit::where('order_id', '>', 0);
        $todayStart = now()->startOfDay();

        $stats = [
            'total_revenue' => (float) (clone $orderDeposits)->where('status', Status::PAYMENT_SUCCESS)->sum('final_amo'),
            'today_revenue' => (float) (clone $orderDeposits)->where('status', Status::PAYMENT_SUCCESS)->where('created_at', '>=', $todayStart)->sum('final_amo'),
            'month_revenue' => (float) (clone $orderDeposits)->where('status', Status::PAYMENT_SUCCESS)->where('created_at', '>=', now()->startOfMonth())->sum('final_amo'),
            'successful_count' => (clone $orderDeposits)->where('status', Status::PAYMENT_SUCCESS)->count(),
            'pending_count' => (clone $orderDeposits)->whereIn('status', [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])->count(),
            'pending_amount' => (float) (clone $orderDeposits)->whereIn('status', [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])->sum('final_amo'),
            'rejected_count' => (clone $orderDeposits)->where('status', Status::PAYMENT_REJECT)->count(),
            'initiated_count' => (clone $orderDeposits)->where('status', Status::PAYMENT_INITIATE)->count(),
        ];

        $attemptCount = (clone $orderDeposits)->count();
        $stats['success_rate'] = $attemptCount > 0
            ? round($stats['successful_count'] / $attemptCount * 100, 1)
            : 0;

        $recentPending = Deposit::with(['user', 'gateway'])
            ->where('order_id', '>', 0)
            ->whereIn('status', [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])
            ->latest('id')
            ->limit(8)
            ->get();

        $topGateways = Deposit::where('order_id', '>', 0)
            ->where('status', Status::PAYMENT_SUCCESS)
            ->where('created_at', '>=', now()->subDays(30))
            ->select('method_code', DB::raw('COUNT(*) as tx_count'), DB::raw('SUM(final_amo) as revenue'))
            ->groupBy('method_code')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $gatewayNames = Gateway::whereIn('code', $topGateways->pluck('method_code'))->pluck('name', 'code');

        $modules = [
            [
                'title' => __('Automatic Gateways'),
                'description' => __('bKash, Nagad, Stripe, PayPal — API keys, webhooks, fees'),
                'route' => 'admin.gateway.automatic.index',
                'icon' => 'credit-card',
                'color' => 'primary',
                'count' => $automaticCount,
                'active' => $activeAutomatic,
                'badge' => __('API'),
            ],
            [
                'title' => __('Manual / Bank Transfer'),
                'description' => __('Bank account, mobile banking instructions — admin approval'),
                'route' => 'admin.gateway.manual.index',
                'route_secondary' => 'admin.gateway.manual.create',
                'secondary_label' => __('Add new'),
                'icon' => 'university',
                'color' => 'success',
                'count' => $manualCount,
                'active' => $activeManual,
                'badge' => __('Manual'),
            ],
            [
                'title' => __('Autopay & SMS Bridge'),
                'description' => __('External redirect or app/SMS payment confirmation'),
                'route' => 'admin.gateway.autopay.index',
                'icon' => 'external-link-alt',
                'color' => 'info',
                'count' => $autopayCount,
                'active' => $activeAutopay,
                'badge' => __('Auto'),
            ],
            [
                'title' => __('Payment Analytics'),
                'description' => __('Success rate, gateway revenue, failed transactions'),
                'route' => 'admin.payment.analytics',
                'icon' => 'chart-line',
                'color' => 'secondary',
                'count' => null,
                'active' => null,
                'badge' => __('Insights'),
            ],
            [
                'title' => __('COD Settings'),
                'description' => __('Cash on delivery limits, charges, availability'),
                'route' => 'admin.shipping.cod.index',
                'icon' => 'money-bill-wave',
                'color' => 'warning',
                'count' => null,
                'active' => null,
                'badge' => __('COD'),
            ],
            [
                'title' => __('Footer Payment Icons'),
                'description' => __('Visa, bKash logos shown on storefront footer'),
                'route' => 'admin.frontend.sections.footer.section',
                'route_params' => ['section' => 'payment-shipping'],
                'icon' => 'credit-card',
                'color' => 'dark',
                'count' => null,
                'active' => null,
                'badge' => __('Display'),
            ],
        ];

        $quickLinks = [
            ['label' => __('All Payments'), 'route' => 'admin.deposit.list', 'icon' => 'list-ul', 'variant' => 'outline-primary'],
            ['label' => __('Pending'), 'route' => 'admin.deposit.pending', 'icon' => 'clock', 'variant' => 'outline-warning', 'badge' => $stats['pending_count']],
            ['label' => __('Successful'), 'route' => 'admin.deposit.successful', 'icon' => 'check-circle', 'variant' => 'outline-success'],
            ['label' => __('Rejected'), 'route' => 'admin.deposit.rejected', 'icon' => 'times-circle', 'variant' => 'outline-danger'],
            ['label' => __('Add Manual Gateway'), 'route' => 'admin.gateway.manual.create', 'icon' => 'plus-circle', 'variant' => 'primary'],
            ['label' => __('Transactions'), 'route' => 'admin.report.transaction', 'icon' => 'history', 'variant' => 'outline-secondary'],
        ];

        $totalPayments = $stats['successful_count'];

        return view('admin.gateways.hub', compact(
            'pageTitle',
            'general',
            'totalPayments',
            'automaticCount',
            'manualCount',
            'autopayCount',
            'activeAutomatic',
            'activeManual',
            'activeAutopay',
            'totalActiveGateways',
            'stats',
            'recentPending',
            'topGateways',
            'gatewayNames',
            'modules',
            'quickLinks'
        ));
    }
}
