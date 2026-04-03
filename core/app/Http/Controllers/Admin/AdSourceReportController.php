<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdSourceReportController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Ad Source Report');
        $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $baseQuery = Order::query()
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        $bySource = collect([]);
        if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'ad_source')) {
            $bySource = Order::query()
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw("COALESCE(NULLIF(TRIM(ad_source), ''), 'direct') as source")
                ->selectRaw('COUNT(*) as order_count')
                ->selectRaw('SUM(CASE WHEN order_status = ? THEN 1 ELSE 0 END) as delivered_count', [Status::ORDER_DELIVERED])
                ->selectRaw('SUM(CASE WHEN order_status = ? THEN total ELSE 0 END) as revenue', [Status::ORDER_DELIVERED])
                ->groupBy('source')
                ->orderByDesc('order_count')
                ->get();
        }

        $totalOrders = $baseQuery->count();
        $deliveredOrders = (clone $baseQuery)->where('order_status', Status::ORDER_DELIVERED)->count();
        $totalRevenue = (clone $baseQuery)->where('order_status', Status::ORDER_DELIVERED)->sum('total');

        $ordersList = Order::query()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('user:id,username,firstname,mobile')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $emptyMessage = __('No orders in this date range.');
        $general = gs();

        return view('admin.reports.ad_source', compact(
            'pageTitle', 'bySource', 'totalOrders', 'deliveredOrders', 'totalRevenue',
            'ordersList', 'dateFrom', 'dateTo', 'emptyMessage', 'general'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $orders = Order::query()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with('user:id,username,firstname,mobile')
            ->orderBy('created_at')
            ->get();

        $general = gs();
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ad_source_report_' . date('Y-m-d_His') . '.csv"',
        ];

        return response()->stream(function () use ($orders, $general) {
            $stream = fopen('php://output', 'w');
            fprintf($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($stream, ['Order No', 'Date', 'Ad Source', 'UTM Source', 'Customer', 'Phone', 'Total', 'Status', 'Currency']);

            foreach ($orders as $o) {
                fputcsv($stream, [
                    $o->order_no ?? '—',
                    $o->created_at?->format('Y-m-d H:i') ?? '—',
                    $o->ad_source ?? 'direct',
                    $o->utm_source ?? '—',
                    $o->user ? trim(($o->user->firstname ?? '') . ' ' . ($o->user->username ?? '')) : '—',
                    $o->user->mobile ?? '—',
                    $o->total ?? 0,
                    $o->order_status ?? '—',
                    $general->cur_sym ?? 'BDT',
                ]);
            }
            fclose($stream);
        }, 200, $headers);
    }
}
