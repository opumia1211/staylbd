<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Models\SearchLog;
use App\Models\Transaction;
use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function transaction(Request $request)
    {
        $pageTitle = __('Transaction Logs');

        $remarks = Transaction::select('remark')->distinct()->whereNotNull('remark')->where('remark', '!=', '')->orderBy('remark')->pluck('remark');

        $baseQuery = Transaction::query()->orderBy('id', 'desc')->with('user');
        $baseQuery->searchable(['trx', 'user:username']);
        $baseQuery->filter(['trx_type', 'remark']);
        $baseQuery->dateFilter();

        $perPage = in_array((int) $request->per_page, [10, 20, 50, 100, 200], true) ? (int) $request->per_page : getPaginate();
        $transactions = $baseQuery->paginate($perPage)->withQueryString();

        $todayStart = Carbon::today();
        $weekStart = Carbon::now()->subDays(7);
        $stats = [
            'total' => Transaction::count(),
            'today' => Transaction::where('created_at', '>=', $todayStart)->count(),
            'week' => Transaction::where('created_at', '>=', $weekStart)->count(),
            'credit' => Transaction::where('trx_type', '+')->where('created_at', '>=', $todayStart)->sum('amount'),
            'debit' => Transaction::where('trx_type', '-')->where('created_at', '>=', $todayStart)->sum('amount'),
        ];

        $emptyMessage = __('No transactions found.');

        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'remarks', 'stats', 'emptyMessage'));
    }

    /**
     * Export transaction logs as CSV.
     */
    public function exportTransaction(Request $request): StreamedResponse
    {
        $query = Transaction::query()->orderBy('id', 'desc')->with('user');
        $query->searchable(['trx', 'user:username']);
        $query->filter(['trx_type', 'remark']);
        $query->dateFilter();

        $general = gs();
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="transaction_logs_' . date('Y-m-d_His') . '.csv"',
        ];

        return response()->stream(function () use ($query, $general) {
            $stream = fopen('php://output', 'w');
            fprintf($stream, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($stream, ['User', 'Username', 'TRX', 'Type', 'Remark', 'Amount', 'Currency', 'Details', 'Transacted']);

            $query->chunk(500, function ($list) use ($stream, $general) {
                foreach ($list as $trx) {
                    fputcsv($stream, [
                        $trx->user?->fullname ?? '—',
                        $trx->user?->username ?? '—',
                        $trx->trx ?? '—',
                        $trx->trx_type ?? '—',
                        $trx->remark ?? '—',
                        $trx->amount ?? '0',
                        $general->cur_text ?? 'BDT',
                        $trx->details ?? '—',
                        $trx->created_at?->format('Y-m-d H:i:s') ?? '—',
                    ]);
                }
            });

            fclose($stream);
        }, 200, $headers);
    }

    /**
     * Bulk delete transactions: selected, or by count (10/20/50/100/200/all).
     */
    public function bulkDeleteTransactions(Request $request)
    {
        $request->validate(['action' => 'required|in:selected,count,all', 'limit' => 'nullable|in:10,20,50,100,200,all']);
        $query = Transaction::query()->orderBy('id', 'desc');
        $query->searchable(['trx', 'user:username']);
        $query->filter(['trx_type', 'remark']);
        $query->dateFilter();
        if ($request->action === 'selected') {
            $ids = is_array($request->ids ?? []) ? array_filter(array_map('intval', $request->ids)) : [];
            $deleted = Transaction::whereIn('id', $ids)->delete();
        } elseif ($request->action === 'count') {
            $limit = $request->input('limit', '10');
            $q = (clone $query)->orderBy('id');
            $toDelete = $limit === 'all' ? $q->get() : $q->limit((int) $limit)->get();
            $deleted = $toDelete->isEmpty() ? 0 : Transaction::whereIn('id', $toDelete->pluck('id'))->delete();
        } else {
            $deleted = $query->orderBy('id')->delete();
        }
        $notify[] = ['success', __(':count transaction(s) deleted.', ['count' => $deleted])];
        return back()->withNotify($notify);
    }

    public function loginHistory(Request $request)
    {
        $pageTitle = 'User Login History';

        $baseQuery = UserLogin::query()->orderBy('id', 'desc')->with('user');
        $baseQuery->searchable(['user:username']);
        $baseQuery->dateFilter('created_at');

        $loginLogs = $baseQuery->paginate(getPaginate())->withQueryString();

        // Stats for dashboard
        $todayStart = Carbon::today();
        $weekStart = Carbon::now()->subDays(7);
        $stats = [
            'today' => UserLogin::where('created_at', '>=', $todayStart)->count(),
            'week' => UserLogin::where('created_at', '>=', $weekStart)->count(),
            'unique_ips' => UserLogin::select('user_ip')->distinct()->count('user_ip'),
        ];

        return view('admin.reports.logins', compact('pageTitle', 'loginLogs', 'stats'));
    }

    public function loginIpHistory($ip)
    {
        $pageTitle = 'Login by IP - ' . $ip;
        $loginLogs = UserLogin::where('user_ip', $ip)->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        $stats = null;
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs', 'ip', 'stats'));
    }

    /**
     * Export login history as CSV.
     */
    public function exportLoginHistory(Request $request): StreamedResponse
    {
        $query = UserLogin::query()->orderBy('id', 'desc')->with('user');
        $query->searchable(['user:username']);
        $query->dateFilter('created_at');

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="login_history_' . date('Y-m-d_His') . '.csv"',
        ];

        return response()->stream(function () use ($query) {
            $stream = fopen('php://output', 'w');
            fprintf($stream, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($stream, ['User', 'Username', 'Login At', 'IP', 'Location', 'City', 'Country', 'Browser', 'OS']);

            $query->chunk(500, function ($logs) use ($stream) {
                foreach ($logs as $log) {
                    fputcsv($stream, [
                        $log->user?->fullname ?? '—',
                        $log->user?->username ?? '—',
                        $log->created_at?->format('Y-m-d H:i:s') ?? '—',
                        $log->user_ip ?? '—',
                        $log->location_display ?? '—',
                        $log->city ?? '—',
                        $log->country ?? '—',
                        $log->browser ?? '—',
                        $log->os ?? '—',
                    ]);
                }
            });

            fclose($stream);
        }, 200, $headers);
    }

    /**
     * Bulk delete login history: selected, or by count (10/20/50/100/200/all).
     */
    public function bulkDeleteLoginHistory(Request $request)
    {
        $request->validate(['action' => 'required|in:selected,count,all', 'limit' => 'nullable|in:10,20,50,100,200,all']);
        $query = UserLogin::query()->orderBy('id', 'desc');
        $query->searchable(['user:username']);
        $query->dateFilter('created_at');
        if ($request->action === 'selected') {
            $ids = is_array($request->ids ?? []) ? array_filter(array_map('intval', $request->ids)) : [];
            $deleted = UserLogin::whereIn('id', $ids)->delete();
        } elseif ($request->action === 'count') {
            $limit = $request->input('limit', '10');
            $q = (clone $query)->orderBy('id');
            $toDelete = $limit === 'all' ? $q->get() : $q->limit((int) $limit)->get();
            $deleted = $toDelete->isEmpty() ? 0 : UserLogin::whereIn('id', $toDelete->pluck('id'))->delete();
        } else {
            $deleted = $query->orderBy('id')->delete();
        }
        $notify[] = ['success', __(':count login record(s) deleted.', ['count' => $deleted])];
        return back()->withNotify($notify);
    }

    public function notificationHistory(Request $request)
    {
        $pageTitle = __('Notification History');

        $baseQuery = NotificationLog::query()->orderBy('id', 'desc')->with('user');
        $baseQuery->searchable(['user:username', 'sent_to', 'subject']);
        $baseQuery->dateFilter('created_at');

        if ($request->filled('notification_type')) {
            $baseQuery->where('notification_type', $request->notification_type);
        }

        $perPage = in_array((int) $request->per_page, [10, 20, 50, 100], true) ? (int) $request->per_page : getPaginate();
        $logs = $baseQuery->paginate($perPage)->withQueryString();

        $todayStart = Carbon::today();
        $weekStart = Carbon::now()->subDays(7);
        $stats = [
            'total' => NotificationLog::count(),
            'today' => NotificationLog::where('created_at', '>=', $todayStart)->count(),
            'week' => NotificationLog::where('created_at', '>=', $weekStart)->count(),
            'email' => NotificationLog::where('notification_type', 'email')->count(),
            'sms' => NotificationLog::where('notification_type', 'sms')->count(),
            'push' => NotificationLog::where('notification_type', 'push')->count(),
        ];

        $emptyMessage = __('No notifications found.');

        return view('admin.reports.notification_history', compact('pageTitle', 'logs', 'stats', 'emptyMessage'));
    }

    public function exportNotificationHistory(Request $request): StreamedResponse
    {
        $query = NotificationLog::query()->orderBy('id', 'desc')->with('user');
        $query->searchable(['user:username', 'sent_to', 'subject']);
        $query->dateFilter('created_at');

        if ($request->filled('notification_type')) {
            $query->where('notification_type', $request->notification_type);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="notification_history_' . date('Y-m-d_His') . '.csv"',
        ];

        return response()->stream(function () use ($query) {
            $stream = fopen('php://output', 'w');
            fprintf($stream, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($stream, ['ID', 'User', 'Username', 'Type', 'Sender', 'Sent To', 'Subject', 'Sent At', 'Status']);

            $query->chunk(500, function ($logs) use ($stream) {
                foreach ($logs as $log) {
                    fputcsv($stream, [
                        $log->id,
                        $log->user?->fullname ?? '—',
                        $log->user?->username ?? '—',
                        $log->notification_type ?? '—',
                        $log->sender ?? '—',
                        $log->sent_to ?? '—',
                        Str::limit($log->subject ?? '—', 80),
                        $log->created_at?->format('Y-m-d H:i:s') ?? '—',
                        'Sent',
                    ]);
                }
            });

            fclose($stream);
        }, 200, $headers);
    }

    /**
     * Bulk delete notification history: selected, or by count (10/20/50/100/200/all).
     */
    public function bulkDeleteNotificationHistory(Request $request)
    {
        $request->validate(['action' => 'required|in:selected,count,all', 'limit' => 'nullable|in:10,20,50,100,200,all']);
        $query = NotificationLog::query()->orderBy('id', 'desc');
        $query->searchable(['user:username', 'sent_to', 'subject']);
        $query->dateFilter('created_at');
        if ($request->filled('notification_type')) {
            $query->where('notification_type', $request->notification_type);
        }
        if ($request->action === 'selected') {
            $ids = is_array($request->ids ?? []) ? array_filter(array_map('intval', $request->ids)) : [];
            $deleted = NotificationLog::whereIn('id', $ids)->delete();
        } elseif ($request->action === 'count') {
            $limit = $request->input('limit', '10');
            $q = (clone $query)->orderBy('id');
            $toDelete = $limit === 'all' ? $q->get() : $q->limit((int) $limit)->get();
            $deleted = $toDelete->isEmpty() ? 0 : NotificationLog::whereIn('id', $toDelete->pluck('id'))->delete();
        } else {
            $deleted = $query->orderBy('id')->delete();
        }
        $notify[] = ['success', __(':count notification(s) deleted.', ['count' => $deleted])];
        return back()->withNotify($notify);
    }

    public function emailDetails($id){
        $pageTitle = 'Email Details';
        $email = NotificationLog::findOrFail($id);
        return view('admin.reports.email_details', compact('pageTitle','email'));
    }

    /**
     * User Search Analytics – all searches from user site header (universal search) are logged here.
     */
    public function searchAnalytics(Request $request)
    {
        $pageTitle = __('User Search Analytics');

        $baseQuery = SearchLog::query()->orderBy('id', 'desc')->with('user');

        if ($request->filled('visitor')) {
            $v = $request->visitor;
            if (str_starts_with($v, 'user_')) {
                $uid = (int) str_replace('user_', '', $v);
                if ($uid) $baseQuery->where('user_id', $uid);
            } elseif (str_starts_with($v, 'ip_')) {
                $ip = str_replace('ip_', '', $v);
                if ($ip !== '') $baseQuery->whereNull('user_id')->where('ip', $ip);
            }
        }
        if ($request->filled('search')) {
            $kw = '%' . trim($request->search) . '%';
            $baseQuery->where(function ($q) use ($kw) {
                $q->where('query', 'LIKE', $kw)
                  ->orWhereHas('user', fn($u) => $u->where('username', 'LIKE', $kw)->orWhere('firstname', 'LIKE', $kw)->orWhere('lastname', 'LIKE', $kw));
            });
        }
        if ($request->filled('date')) {
            try {
                $dateStr = preg_replace('/\s+/', ' ', trim($request->date));
                $parts = preg_split('/\s*-\s*/', $dateStr, 2);
                $start = Carbon::parse(trim($parts[0]))->startOfDay();
                $end = isset($parts[1]) ? Carbon::parse(trim($parts[1]))->endOfDay() : $start->copy()->endOfDay();
                $baseQuery->whereBetween('created_at', [$start, $end]);
            } catch (\Throwable $e) {
                // ignore invalid date
            }
        }
        $validSources = ['universal', 'voice', 'image', 'products_page', 'filter'];
        if ($request->filled('source') && in_array($request->source, $validSources, true)) {
            $baseQuery->where('source', $request->source);
        }

        $perPage = in_array((int) $request->per_page, [10, 20, 50, 100, 200], true) ? (int) $request->per_page : getPaginate();
        $logs = $baseQuery->paginate($perPage)->withQueryString();

        $todayStart = Carbon::today();
        $weekStart = Carbon::now()->subDays(7);
        $stats = [
            'total' => SearchLog::count(),
            'today' => SearchLog::where('created_at', '>=', $todayStart)->count(),
            'week' => SearchLog::where('created_at', '>=', $weekStart)->count(),
            'unique_queries' => SearchLog::select('query')->distinct()->count('query'),
            'zero_result_count' => SearchLog::where('results_count', 0)->where('query', '!=', '')->count(),
        ];

        $topQueries = SearchLog::select('query', DB::raw('COUNT(*) as search_count'))
            ->where('query', '!=', '')
            ->groupBy('query')
            ->orderByDesc('search_count')
            ->limit(30)
            ->get();

        $zeroResultQueries = SearchLog::select('query', DB::raw('COUNT(*) as cnt'))
            ->where('results_count', 0)
            ->where('query', '!=', '')
            ->groupBy('query')
            ->orderByDesc('cnt')
            ->limit(20)
            ->get();

        $searchesByDate = SearchLog::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as cnt'))
            ->where('created_at', '>=', Carbon::now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $rankingBySource = SearchLog::select('source', DB::raw('COUNT(*) as cnt'))
            ->where('query', '!=', '')
            ->groupBy('source')
            ->orderByDesc('cnt')
            ->get();

        $visitors = $this->getSearchVisitorProfiles();

        $emptyMessage = __('No search logs found.');
        $storageUrl = asset('storage');

        return view('admin.reports.search_analytics', compact('pageTitle', 'logs', 'stats', 'emptyMessage', 'topQueries', 'zeroResultQueries', 'searchesByDate', 'rankingBySource', 'visitors', 'storageUrl'));
    }

    /**
     * Visitor profiles: each person (user or guest by IP) with total searches and last search.
     */
    protected function getSearchVisitorProfiles(): \Illuminate\Support\Collection
    {
        $byUser = SearchLog::select('user_id', DB::raw('COUNT(*) as total'), DB::raw('MAX(created_at) as last_at'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->with('user:id,username,firstname,lastname')
            ->get()
            ->map(function ($r) {
                $name = $r->user->username ?? trim(($r->user->firstname ?? '') . ' ' . ($r->user->lastname ?? '')) ?: 'User#' . $r->user_id;
                return (object)['type' => 'user', 'id' => 'user_' . $r->user_id, 'label' => $name, 'total' => $r->total, 'last_at' => $r->last_at];
            });

        $byIp = SearchLog::select('ip', DB::raw('COUNT(*) as total'), DB::raw('MAX(created_at) as last_at'))
            ->whereNull('user_id')
            ->whereNotNull('ip')
            ->groupBy('ip')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => (object)['type' => 'guest', 'id' => 'ip_' . $r->ip, 'label' => __('Guest') . ' (' . $r->ip . ')', 'total' => $r->total, 'last_at' => $r->last_at]);

        return $byUser->concat($byIp)->take(100);
    }

    /**
     * Bulk delete search logs: selected ids, or by count (10/20/50/100), or all (with optional filters).
     */
    public function deleteSearchLogs(Request $request)
    {
        $request->validate([
            'action' => 'required|in:selected,count,all',
            'limit'  => 'nullable|in:10,20,50,100,200,all',
        ]);

        $query = SearchLog::query();
        if ($request->filled('search')) {
            $kw = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($kw) {
                $q->where('query', 'LIKE', $kw)->orWhereHas('user', fn($u) => $u->where('username', 'LIKE', $kw)->orWhere('firstname', 'LIKE', $kw)->orWhere('lastname', 'LIKE', $kw));
            });
        }
        if ($request->filled('date')) {
            try {
                $dateStr = preg_replace('/\s+/', ' ', trim($request->date));
                $parts = preg_split('/\s*-\s*/', $dateStr, 2);
                $start = Carbon::parse(trim($parts[0]))->startOfDay();
                $end = isset($parts[1]) ? Carbon::parse(trim($parts[1]))->endOfDay() : $start->copy()->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Throwable $e) {
            }
        }
        if ($request->filled('source') && in_array($request->source, ['universal', 'voice', 'image', 'products_page', 'filter'], true)) {
            $query->where('source', $request->source);
        }
        if ($request->filled('visitor')) {
            $v = $request->visitor;
            if (str_starts_with($v, 'user_')) {
                $uid = (int) str_replace('user_', '', $v);
                if ($uid) {
                    $query->where('user_id', $uid);
                }
            } elseif (str_starts_with($v, 'ip_')) {
                $ip = str_replace('ip_', '', $v);
                if ($ip !== '') {
                    $query->whereNull('user_id')->where('ip', $ip);
                }
            }
        }

        if ($request->action === 'selected') {
            $ids = $request->input('ids', []);
            $ids = is_array($ids) ? array_filter(array_map('intval', $ids)) : [];
            SearchLog::whereIn('id', $ids)->get()->each(function ($log) {
                $this->deleteSearchLogImage($log);
            });
            $deleted = SearchLog::whereIn('id', $ids)->delete();
        } elseif ($request->action === 'count') {
            $limit = $request->input('limit', '10');
            if ($limit === 'all') {
                $toDelete = $query->orderBy('id')->get();
            } else {
                $n = (int) $limit;
                $toDelete = $query->orderBy('id')->limit($n)->get();
            }
            foreach ($toDelete as $log) {
                $this->deleteSearchLogImage($log);
            }
            $deleted = $toDelete->isEmpty() ? 0 : SearchLog::whereIn('id', $toDelete->pluck('id'))->delete();
        } else {
            // all: use same filtered query
            $query->orderBy('id');
            $query->get()->each(function ($log) {
                $this->deleteSearchLogImage($log);
            });
            $deleted = $query->delete();
        }

        $notify[] = ['success', __(':count search log(s) deleted.', ['count' => $deleted])];
        return back()->withNotify($notify);
    }

    protected function deleteSearchLogImage(SearchLog $log): void
    {
        if (!$log->image_path) return;
        try {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($log->image_path);
        } catch (\Throwable $e) {
            // ignore
        }
    }

    /**
     * Export search analytics as CSV.
     */
    public function exportSearchAnalytics(Request $request): StreamedResponse
    {
        $query = SearchLog::query()->orderBy('id', 'desc')->with('user');
        if ($request->filled('search')) {
            $kw = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($kw) {
                $q->where('query', 'LIKE', $kw)->orWhereHas('user', fn($u) => $u->where('username', 'LIKE', $kw)->orWhere('firstname', 'LIKE', $kw)->orWhere('lastname', 'LIKE', $kw));
            });
        }
        if ($request->filled('date')) {
            try {
                $dateStr = preg_replace('/\s+/', ' ', trim($request->date));
                $parts = preg_split('/\s*-\s*/', $dateStr, 2);
                $start = Carbon::parse(trim($parts[0]))->startOfDay();
                $end = isset($parts[1]) ? Carbon::parse(trim($parts[1]))->endOfDay() : $start->copy()->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Throwable $e) { }
        }
        if ($request->filled('source') && in_array($request->source, ['universal', 'voice', 'image', 'products_page', 'filter'], true)) {
            $query->where('source', $request->source);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="user_search_analytics_' . date('Y-m-d_His') . '.csv"',
        ];

        return response()->stream(function () use ($query) {
            $stream = fopen('php://output', 'w');
            fprintf($stream, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($stream, ['Query', 'User', 'Username', 'IP', 'Results', 'Source', 'Searched At']);

            $query->chunk(500, function ($logs) use ($stream) {
                foreach ($logs as $log) {
                    fputcsv($stream, [
                        $log->query ?? '—',
                        $log->user?->fullname ?? __('Guest'),
                        $log->user?->username ?? '—',
                        $log->ip ?? '—',
                        $log->results_count ?? 0,
                        $log->source ?? 'universal',
                        $log->created_at?->format('Y-m-d H:i:s') ?? '—',
                    ]);
                }
            });

            fclose($stream);
        }, 200, $headers);
    }
}
