<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductReportController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Product Report');
        $tab = $request->get('tab', 'summary');
        $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $general = gs();

        $summary = [
            'total_products' => Product::count(),
            'active_products' => Product::active()->count(),
            'low_stock' => Product::where('quantity', '<=', 5)->where('quantity', '>=', 0)->count(),
            'out_of_stock' => Product::where('quantity', '<=', 0)->count(),
            'total_orders' => Order::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'delivered_orders' => Order::whereBetween('created_at', [$dateFrom, $dateTo])->where('order_status', Status::ORDER_DELIVERED)->count(),
            'revenue' => Order::whereBetween('created_at', [$dateFrom, $dateTo])->where('order_status', Status::ORDER_DELIVERED)->sum('total'),
        ];

        $bestSellers = OrderDetail::query()
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('orders.order_status', Status::ORDER_DELIVERED)
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->select('order_details.product_id', DB::raw('SUM(order_details.quantity) as total_qty'), DB::raw('SUM(order_details.quantity * order_details.price) as total_sales'))
            ->groupBy('order_details.product_id')
            ->orderByDesc('total_qty')
            ->take(50)
            ->get();

        $productIds = $bestSellers->pluck('product_id')->unique()->filter()->values()->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $bestSellers = $bestSellers->map(function ($row) use ($products) {
            $row->product = $products->get($row->product_id);
            return $row;
        });

        $stockReport = Product::query()
            ->orderBy('quantity')
            ->take(100)
            ->get(['id', 'name', 'product_sku', 'quantity', 'price']);

        $emptyMessage = __('No data.');
        return view('admin.reports.product_report', compact(
            'pageTitle', 'tab', 'summary', 'bestSellers', 'stockReport', 'dateFrom', 'dateTo', 'general', 'emptyMessage'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $bestSellers = OrderDetail::query()
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('orders.order_status', Status::ORDER_DELIVERED)
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->select('order_details.product_id', DB::raw('SUM(order_details.quantity) as total_qty'), DB::raw('SUM(order_details.quantity * order_details.price) as total_sales'))
            ->groupBy('order_details.product_id')
            ->orderByDesc('total_qty')
            ->get();

        $productIds = $bestSellers->pluck('product_id')->unique()->filter()->values()->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $general = gs();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="product_report_' . date('Y-m-d_His') . '.csv"',
        ];

        return response()->stream(function () use ($bestSellers, $products, $general) {
            $stream = fopen('php://output', 'w');
            fprintf($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($stream, ['Product ID', 'Product Name', 'Quantity Sold', 'Total Sales', 'Currency']);

            foreach ($bestSellers as $row) {
                $p = $products->get($row->product_id);
                fputcsv($stream, [
                    $row->product_id,
                    $p ? $p->name : '—',
                    $row->total_qty ?? 0,
                    $row->total_sales ?? 0,
                    $general->cur_sym ?? 'BDT',
                ]);
            }
            fclose($stream);
        }, 200, $headers);
    }
}
