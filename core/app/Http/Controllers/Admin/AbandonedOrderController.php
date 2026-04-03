<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCart;
use App\Jobs\SendAbandonedCartReminderJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AbandonedOrderController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Abandoned Carts & Incomplete Orders');

        if (!Schema::hasTable('abandoned_carts')) {
            $abandonedCarts = new \Illuminate\Pagination\LengthAwarePaginator([], 0, getPaginate());
            $stats = ['total' => 0, 'potential_value' => 0, 'with_contact' => 0];
            $emptyMessage = __('The abandoned_carts table does not exist. Please run the migration or create the table manually. See database/patches/create_abandoned_carts_table.sql');
            $breadcrumb = [
                ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['label' => 'Manage Orders', 'url' => route('admin.orders.index')],
                'Abandoned Carts',
            ];
            return view('admin.abandoned_orders.index', compact('pageTitle', 'abandonedCarts', 'stats', 'emptyMessage', 'breadcrumb'));
        }

        $query = AbandonedCart::with('user')
            ->whereIn('status', [AbandonedCart::STATUS_PENDING, AbandonedCart::STATUS_ABANDONED])
            ->orderByDesc('last_activity_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('email', 'like', '%' . $s . '%')
                    ->orWhere('mobile', 'like', '%' . $s . '%')
                    ->orWhere('session_id', 'like', '%' . $s . '%')
                    ->orWhereHas('user', function ($uq) use ($s) {
                        $uq->where('username', 'like', '%' . $s . '%')
                            ->orWhere('email', 'like', '%' . $s . '%')
                            ->orWhere('mobile', 'like', '%' . $s . '%');
                    });
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('last_activity_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('last_activity_at', '<=', $request->date_to);
        }

        $perPage = (int) $request->get('per_page', getPaginate());
        $abandonedCarts = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total' => AbandonedCart::whereIn('status', [AbandonedCart::STATUS_PENDING, AbandonedCart::STATUS_ABANDONED])->count(),
            'potential_value' => AbandonedCart::whereIn('status', [AbandonedCart::STATUS_PENDING, AbandonedCart::STATUS_ABANDONED])->sum('cart_value'),
            'with_contact' => AbandonedCart::whereIn('status', [AbandonedCart::STATUS_PENDING, AbandonedCart::STATUS_ABANDONED])
                ->where(function ($q) {
                    $q->whereNotNull('email')->where('email', '!=', '')
                        ->orWhereNotNull('mobile')->where('mobile', '!=', '');
                })->count(),
        ];

        $emptyMessage = __('No abandoned carts found.');
        $breadcrumb = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Manage Orders', 'url' => route('admin.orders.index')],
            'Abandoned Carts',
        ];
        return view('admin.abandoned_orders.index', compact('pageTitle', 'abandonedCarts', 'stats', 'emptyMessage', 'breadcrumb'));
    }

    public function sendReminder($id)
    {
        if (!Schema::hasTable('abandoned_carts')) {
            $notify[] = ['error', __('The abandoned_carts table does not exist. Please run the migration first.')];
            return back()->withNotify($notify);
        }
        $abandoned = AbandonedCart::whereIn('status', [AbandonedCart::STATUS_PENDING, AbandonedCart::STATUS_ABANDONED])->findOrFail($id);
        SendAbandonedCartReminderJob::dispatch($abandoned);
        $abandoned->update(['reminder_sent_at' => now()]);
        $notify[] = ['success', __('Reminder has been queued to send.')];
        return back()->withNotify($notify);
    }

    public function settings()
    {
        $pageTitle = __('Abandoned Cart Settings');
        $general = gs();
        $breadcrumb = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Manage Orders', 'url' => route('admin.orders.index')],
            ['label' => 'Abandoned Carts', 'url' => route('admin.abandoned-orders.index')],
            ['label' => 'Abandoned Cart Settings'],
        ];
        return view('admin.abandoned_orders.settings', compact('pageTitle', 'general', 'breadcrumb'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'abandoned_cart_inactivity_minutes' => 'required|integer|in:15,30,60,360',
            'abandoned_cart_reminder_email' => 'nullable|boolean',
            'abandoned_cart_reminder_sms' => 'nullable|boolean',
            'abandoned_cart_cleanup_days' => 'required|integer|min:7|max:90',
        ]);
        $general = gs();
        if (Schema::hasColumn('general_settings', 'abandoned_cart_inactivity_minutes')) {
            $general->abandoned_cart_inactivity_minutes = $request->abandoned_cart_inactivity_minutes;
        }
        if (Schema::hasColumn('general_settings', 'abandoned_cart_reminder_email')) {
            $general->abandoned_cart_reminder_email = $request->boolean('abandoned_cart_reminder_email');
        }
        if (Schema::hasColumn('general_settings', 'abandoned_cart_reminder_sms')) {
            $general->abandoned_cart_reminder_sms = $request->boolean('abandoned_cart_reminder_sms');
        }
        if (Schema::hasColumn('general_settings', 'abandoned_cart_cleanup_days')) {
            $general->abandoned_cart_cleanup_days = $request->abandoned_cart_cleanup_days;
        }
        $general->save();
        $notify[] = ['success', __('Abandoned cart settings updated.')];
        return back()->withNotify($notify);
    }
}
