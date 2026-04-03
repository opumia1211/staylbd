<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courierapi;
use App\Services\Courier\CourierManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ApiIntegrationController extends Controller
{
    public function courier_manage(CourierManager $courierManager)
    {
        $pageTitle = 'Courier API Hub';

        if (!Schema::hasTable('courierapis')) {
            return view('admin.apiintegration.courier_setup_required')->with('pageTitle', $pageTitle);
        }

        try {
            $this->ensureDefaultProviders();
            $providers = Courierapi::orderBy('sort_order')->orderBy('type')->get();
            $drivers = $courierManager->getDrivers();
            $addableTypes = $courierManager->getAddableTypes();
            $stats = $this->courierStats();

            // Add global stats summary
            $globalStats = [
                'active' => $providers->where('status', true)->count(),
                'total_providers' => $providers->count(),
                'ready' => $providers->filter(fn($p) => isset($drivers[$p->type]) && $drivers[$p->type]->isConfigured($p))->count(),
            ];
        } catch (\Throwable $e) {
            $providers = collect([]);
            $drivers = [];
            $addableTypes = [];
            $stats = ['total' => 0, 'success' => 0, 'failed' => 0];
            $globalStats = ['active' => 0, 'total_providers' => 0, 'ready' => 0];
        }

        return view('admin.apiintegration.courier_manage', compact('pageTitle', 'providers', 'drivers', 'addableTypes', 'stats', 'globalStats'));
    }

    public function courier_store(Request $request, CourierManager $courierManager)
    {
        $addable = $courierManager->getAddableTypes();
        if (empty($addable)) {
            $notify[] = ['error', 'All available providers are already added.'];
            return back()->withNotify($notify);
        }
        $request->validate([
            'type' => 'required|string|in:' . implode(',', array_keys($addable)),
            'name' => 'nullable|string|max:100',
            'country_code' => 'nullable|string|max:10',
        ]);
        if (Courierapi::where('type', $request->type)->exists()) {
            $notify[] = ['error', 'This provider is already added.'];
            return back()->withNotify($notify);
        }
        $driver = $courierManager->driver($request->type);
        $name = $request->filled('name') ? $request->name : ($driver ? $driver->getName() : ucfirst($request->type));
        $country = $request->filled('country_code') ? $request->country_code : ($driver ? $driver->getCountryCode() : 'BD');
        $sortOrder = Courierapi::max('sort_order') + 1;
        Courierapi::create([
            'type' => $request->type,
            'name' => $name,
            'country_code' => $country,
            'status' => false,
            'sort_order' => $sortOrder,
        ]);
        Cache::forget('admin.courier.providers');
        $notify[] = ['success', 'Courier provider added. Configure API credentials below.'];
        return back()->withNotify($notify);
    }

    public function courier_store_custom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'country_code' => 'nullable|string|max:10',
        ]);
        $name = trim($request->name);
        $country = trim($request->country_code ?? 'BD') ?: 'BD';
        $region = trim($request->region ?? '');
        $type = \Illuminate\Support\Str::slug($name);
        if (strlen($type) < 2) {
            $type = 'custom_' . uniqid();
        }
        if (Courierapi::where('type', $type)->exists()) {
            $type = 'custom_' . substr(uniqid(), -6);
        }
        $sortOrder = (int) Courierapi::max('sort_order') + 1;
        Courierapi::create([
            'type' => $type,
            'name' => $name,
            'country_code' => $country,
            'region' => $region ?: null,
            'status' => false,
            'show_to_user' => false,
            'sort_order' => $sortOrder,
        ]);
        Cache::forget('admin.courier.providers');
        $notify[] = ['success', 'Custom API added. Use Edit to set API URL and Token.'];
        return back()->withNotify($notify);
    }

    protected function ensureDefaultProviders(): void
    {
        if (!Courierapi::where('type', 'steadfast')->exists()) {
            Courierapi::create(['type' => 'steadfast', 'name' => 'Steadfast Courier', 'country_code' => 'BD', 'status' => false, 'sort_order' => 1]);
        }
        if (!Courierapi::where('type', 'pathao')->exists()) {
            Courierapi::create(['type' => 'pathao', 'name' => 'Pathao', 'url' => 'https://api-hermes.pathao.com', 'country_code' => 'BD', 'status' => false, 'sort_order' => 2]);
        }
    }

    protected function courierStats(): array
    {
        $stats = ['total' => 0, 'success' => 0, 'failed' => 0, 'pending' => 0, 'returns' => 0];
        if (Schema::hasTable('courier_logs')) {
            try {
                $stats['total'] = DB::table('courier_logs')->count();
                $stats['success'] = DB::table('courier_logs')->where('status', 'success')->count();
                $stats['failed'] = DB::table('courier_logs')->where('status', 'failed')->count();
                $stats['pending'] = DB::table('courier_logs')->where('status', 'pending')->count();
                $stats['returns'] = Schema::hasColumn('courier_logs', 'return_status')
                    ? (int) DB::table('courier_logs')->where('return_status', 'returned')->count()
                    : 0;
            } catch (\Exception $e) {
            }
        }
        return $stats;
    }

    public function courier_edit_json($id)
    {
        $api = Courierapi::findOrFail($id);
        return response()->json([
            'id' => $api->id,
            'name' => $api->name ?? '',
            'country_code' => $api->country_code ?? 'BD',
            'region' => $api->region ?? '',
            'url' => $api->url ?? '',
            'token' => $api->token ?? '',
            'api_key' => $api->api_key ?? '',
            'secret_key' => $api->secret_key ?? '',
            'status' => (bool) $api->status,
            'show_to_user' => (bool) ($api->show_to_user ?? false),
        ]);
    }

    public function courier_update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:courierapis,id',
            'name' => 'nullable|string|max:100',
            'country_code' => 'nullable|string|max:10',
            'region' => 'nullable|string|max:20',
            'api_key' => 'nullable|string|max:255',
            'secret_key' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:500',
            'token' => 'nullable|string|max:500',
            'status' => 'nullable',
            'show_to_user' => 'nullable',
        ]);

        DB::transaction(function () use ($request) {
            $update_data = Courierapi::findOrFail($request->id);
            $allowed = ['name', 'country_code', 'region', 'api_key', 'secret_key', 'url', 'token'];
            $input = [];
            foreach ($allowed as $key) {
                if ($request->has($key)) {
                    $input[$key] = $request->input($key);
                }
            }
            if ($request->has('status')) {
                $input['status'] = $request->boolean('status');
            }
            if ($request->has('show_to_user')) {
                $input['show_to_user'] = $request->boolean('show_to_user');
            }
            $update_data->update($input);
        });

        Cache::forget('admin.courier.providers');
        $notify[] = ['success', 'Courier API updated successfully'];
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Courier API updated successfully']);
        }
        return back()->withNotify($notify);
    }

    public function courier_test_connection($id, CourierManager $courierManager)
    {
        $api = Courierapi::findOrFail($id);
        $driver = $courierManager->driver($api->type);
        if ($driver) {
            [$success, $message] = $driver->testConnection($api);
            return response()->json(['success' => $success, 'message' => $message]);
        }
        // Custom/unknown provider: generic GET to URL with optional Bearer token
        $url = trim($api->url ?? '');
        if ($url === '') {
            return response()->json(['success' => false, 'message' => __('Please set API URL in Edit.')]);
        }
        try {
            $headers = ['Accept' => 'application/json'];
            if (!empty(trim($api->token ?? ''))) {
                $headers['Authorization'] = 'Bearer ' . trim($api->token);
            }
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->withHeaders($headers)
                ->get(rtrim($url, '/') . '/');
            $ok = $response->successful() || $response->status() === 404;
            return response()->json([
                'success' => $ok,
                'message' => $ok ? __('Connection successful.') : ('HTTP ' . $response->status() . ': ' . substr($response->body(), 0, 150)),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function courier_logs(Request $request)
    {
        $pageTitle = 'Courier Logs';
        $logStats = ['total' => 0, 'success' => 0, 'failed' => 0, 'pending' => 0];

        if (!Schema::hasTable('courier_logs')) {
            return view('admin.apiintegration.courier_logs', compact('pageTitle', 'logStats'))->with('logs', new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20))->with('courierTypes', collect([]));
        }

        try {
            $logStats['total'] = DB::table('courier_logs')->count();
            $logStats['success'] = DB::table('courier_logs')->where('status', 'success')->count();
            $logStats['failed'] = DB::table('courier_logs')->where('status', 'failed')->count();
            $logStats['pending'] = DB::table('courier_logs')->where('status', 'pending')->count();

            $query = DB::table('courier_logs')
                ->leftJoin('orders', 'courier_logs.order_id', '=', 'orders.id')
                ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->select('courier_logs.*', 'orders.order_no', 'users.username');

            if ($request->filled('courier_type')) {
                $query->where('courier_logs.courier_type', $request->courier_type);
            }
            if ($request->filled('status') && in_array($request->status, ['success', 'failed', 'pending'])) {
                $query->where('courier_logs.status', $request->status);
            }
            if ($request->filled('date_from')) {
                $query->whereDate('courier_logs.created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('courier_logs.created_at', '<=', $request->date_to);
            }
            if ($request->filled('search')) {
                $q = '%' . $request->search . '%';
                $query->where(function ($qry) use ($q) {
                    $qry->where('orders.order_no', 'like', $q)
                        ->orWhere('users.username', 'like', $q)
                        ->orWhere('courier_logs.courier_order_id', 'like', $q);
                });
            }

            $logs = $query->orderBy('courier_logs.created_at', 'desc')->paginate($request->get('per_page', 20))->withQueryString();
            $courierTypes = DB::table('courier_logs')->distinct()->orderBy('courier_type')->pluck('courier_type');
        } catch (\Exception $e) {
            $logs = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $courierTypes = collect([]);
        }

        return view('admin.apiintegration.courier_logs', compact('pageTitle', 'logs', 'courierTypes', 'logStats'));
    }

    public function courier_logs_export(Request $request)
    {
        if (!Schema::hasTable('courier_logs')) {
            return back()->with('error', 'No data to export.');
        }
        $query = DB::table('courier_logs')
            ->leftJoin('orders', 'courier_logs.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select('courier_logs.id', 'orders.order_no', 'users.username', 'courier_logs.courier_type', 'courier_logs.courier_order_id', 'courier_logs.status', 'courier_logs.created_at');
        if ($request->filled('courier_type')) {
            $query->where('courier_logs.courier_type', $request->courier_type);
        }
        if ($request->filled('status')) {
            $query->where('courier_logs.status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('courier_logs.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('courier_logs.created_at', '<=', $request->date_to);
        }
        $rows = $query->orderBy('courier_logs.created_at', 'desc')->limit(5000)->get();

        $filename = 'courier_logs_' . date('Y-m-d_His') . '.csv';
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Order No', 'Customer', 'Courier Type', 'Courier Order ID', 'Status', 'Date']);
            foreach ($rows as $r) {
                fputcsv($out, [$r->id ?? '', $r->order_no ?? '', $r->username ?? '', $r->courier_type ?? '', $r->courier_order_id ?? '', $r->status ?? '', $r->created_at ?? '']);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function courier_log_retry($id)
    {
        if (!Schema::hasTable('courier_logs')) {
            return back()->with('error', 'Log not found.');
        }
        $log = DB::table('courier_logs')->where('id', $id)->where('status', 'failed')->first();
        if (!$log) {
            return back()->with('error', 'Log not found or cannot retry.');
        }
        return redirect()->route('admin.orders.bulk.courier', $log->courier_type)->with('retry_order_id', $log->order_id)->with('info', 'Go to Bulk Courier, select the order and send again.');
    }

    public function courier_reports(Request $request)
    {
        $pageTitle = 'Courier Reports';

        $totalOrders = $successfulOrders = $failedOrders = $pendingOrders = $returnCount = $paidCount = $unpaidCount = 0;
        $byCourierType = [];
        $dailyStats = collect([]);
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $dateRange = [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'];

        if (Schema::hasTable('courier_logs')) {
            try {
                $base = DB::table('courier_logs')->whereBetween('created_at', $dateRange);
                $totalOrders = (clone $base)->count();
                $successfulOrders = (clone $base)->where('status', 'success')->count();
                $failedOrders = (clone $base)->where('status', 'failed')->count();
                $pendingOrders = (clone $base)->where('status', 'pending')->count();
                $returnCount = Schema::hasColumn('courier_logs', 'return_status')
                    ? (int) (clone $base)->where('return_status', 'returned')->count()
                    : 0;
                if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'payment_status')) {
                    $paidCount = (clone $base)->join('orders', 'courier_logs.order_id', '=', 'orders.id')->where('orders.payment_status', 1)->count();
                    $unpaidCount = (clone $base)->join('orders', 'courier_logs.order_id', '=', 'orders.id')->where('orders.payment_status', 0)->count();
                }
                $types = DB::table('courier_logs')->whereBetween('created_at', $dateRange)->distinct()->pluck('courier_type');
                foreach ($types as $type) {
                    $byCourierType[$type] = (clone $base)->where('courier_type', $type)->count();
                }
                $dailyStats = DB::table('courier_logs')
                    ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
                    ->whereBetween('created_at', $dateRange)
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->get();
            } catch (\Exception $e) {
            }
        }

        return view('admin.apiintegration.courier_reports', compact('pageTitle', 'totalOrders', 'successfulOrders', 'failedOrders', 'pendingOrders', 'returnCount', 'paidCount', 'unpaidCount', 'byCourierType', 'dailyStats', 'dateFrom', 'dateTo'));
    }

    public function courier_reports_export(Request $request)
    {
        if (!Schema::hasTable('courier_logs')) {
            return back()->with('error', 'No data to export.');
        }
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $dailyStats = DB::table('courier_logs')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success'), DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed'))
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        $filename = 'courier_reports_' . $dateFrom . '_to_' . $dateTo . '.csv';
        return response()->streamDownload(function () use ($dailyStats) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Total', 'Success', 'Failed']);
            foreach ($dailyStats as $r) {
                fputcsv($out, [$r->date ?? '', $r->total ?? 0, $r->success ?? 0, $r->failed ?? 0]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
