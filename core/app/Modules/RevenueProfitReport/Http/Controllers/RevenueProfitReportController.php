<?php

namespace App\Modules\RevenueProfitReport\Http\Controllers;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueProfitReportController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Revenue & Profit Report');
        $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $revenue = (float) Order::query()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('order_status', Status::ORDER_DELIVERED)
            ->sum('total');

        $returnAmount = (float) Order::query()
            ->whereBetween('updated_at', [$dateFrom, $dateTo])
            ->where('order_status', Status::ORDER_RETURNED)
            ->sum('total');

        $productCost = 0;
        if (\Schema::hasTable('order_details') && \Schema::hasTable('products') && \Schema::hasColumn('products', 'buying_price')) {
            $row = Order::query()
                ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
                ->where('orders.order_status', Status::ORDER_DELIVERED)
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->selectRaw('SUM(COALESCE(products.buying_price, 0) * order_details.quantity) as cost')
                ->first();
            $productCost = (float) ($row->cost ?? 0);
        }

        $expenseTotal = 0;
        if (\Schema::hasTable('revenue_expenses')) {
            $expenseTotal = (float) DB::table('revenue_expenses')
                ->whereBetween('expense_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
                ->sum('amount');
        }

        $profit = $revenue - $returnAmount - $productCost - $expenseTotal;
        $general = gs();

        return view('admin.reports.revenue_profit', compact(
            'pageTitle', 'dateFrom', 'dateTo', 'revenue', 'returnAmount', 'productCost', 'expenseTotal', 'profit', 'general'
        ));
    }
}
