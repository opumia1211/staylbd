<?php

namespace App\Modules\EmployeePerformanceReport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeePerformanceReportController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Employee Performance Report');
        $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        if (!\Schema::hasTable('admin_activity_logs')) {
            $performers = collect();
            return view('admin.reports.employee_performance', compact('pageTitle', 'dateFrom', 'dateTo', 'performers'));
        }

        if (!\Schema::hasColumn('admin_activity_logs', 'model')) {
            $performers = collect();
            return view('admin.reports.employee_performance', compact('pageTitle', 'dateFrom', 'dateTo', 'performers'));
        }

        $performers = AdminActivityLog::query()
            ->where('model', 'Order')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('admin_id')
            ->selectRaw('COUNT(*) as action_count')
            ->selectRaw('COUNT(DISTINCT model_id) as orders_handled')
            ->groupBy('admin_id')
            ->orderByDesc('orders_handled')
            ->get();

        $adminIds = $performers->pluck('admin_id')->unique()->filter()->values()->all();
        $admins = Admin::whereIn('id', $adminIds)->get()->keyBy('id');

        $performers = $performers->map(function ($row) use ($admins) {
            $row->admin = $admins->get($row->admin_id);
            return $row;
        });

        return view('admin.reports.employee_performance', compact('pageTitle', 'dateFrom', 'dateTo', 'performers'));
    }
}
