<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Deposit;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ManageUsersController extends Controller
{

    public function allUsers()
    {
        $pageTitle = 'All Users';
        $users     = $this->userData();
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function activeUsers(Request $request)
    {
        $pageTitle = __('Active Users');
        $query     = User::active()->searchable(['username', 'email', 'firstname', 'lastname', 'mobile']);

        if ($request->filled('date')) {
            try {
                $date    = explode('-', $request->date);
                $start   = \Carbon\Carbon::parse(trim($date[0]))->startOfDay();
                $end     = isset($date[1]) ? \Carbon\Carbon::parse(trim($date[1]))->endOfDay() : $start->copy()->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Exception $e) {
                // ignore invalid date
            }
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('firstname', 'asc')->orderBy('lastname', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('firstname', 'desc')->orderBy('lastname', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        if ($request->filled('has_orders') && $request->has_orders === '1') {
            $query->whereExists(function ($q) {
                $q->selectRaw(1)->from('orders')->whereColumn('orders.user_id', 'users.id');
            });
        }

        $perPage = (int) $request->get('per_page', getPaginate());
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : getPaginate();
        $query->withMax('loginLogs', 'created_at');

        $users = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total'       => User::active()->count(),
            'new_week'    => User::active()->where('created_at', '>=', now()->subDays(7))->count(),
            'new_month'   => User::active()->where('created_at', '>=', now()->subDays(30))->count(),
            'with_orders' => User::active()->whereExists(function ($q) {
                $q->selectRaw(1)->from('orders')->whereColumn('orders.user_id', 'users.id');
            })->count(),
        ];

        $listType   = 'active';
        $emptyMessage = __('No active users found.');

        return view('admin.users.list', compact('pageTitle', 'users', 'listType', 'stats', 'emptyMessage'));
    }

    public function activeUsersExport(Request $request)
    {
        $query = User::active()->searchable(['username', 'email', 'firstname', 'lastname', 'mobile'])->orderBy('id', 'desc');

        if ($request->filled('date')) {
            try {
                $date  = explode('-', $request->date);
                $start = \Carbon\Carbon::parse(trim($date[0]))->startOfDay();
                $end   = isset($date[1]) ? \Carbon\Carbon::parse(trim($date[1]))->endOfDay() : $start->copy()->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Exception $e) {
                //
            }
        }

        $users    = $query->limit(10000)->get();
        $filename = 'active-users-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($users) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, [__('Name'), __('Username'), __('Email'), __('Mobile'), __('Country'), __('Joined At')]);
            foreach ($users as $u) {
                fputcsv($out, [
                    $u->fullname,
                    $u->username ?? '',
                    $u->email ?? '',
                    $u->mobile ?? '',
                    $u->country_code ?? '',
                    $u->created_at ? $u->created_at->format('Y-m-d H:i') : '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bannedUsers(Request $request)
    {
        $pageTitle = __('Banned Users');
        $query     = User::banned()->searchable(['username', 'email', 'firstname', 'lastname', 'mobile']);

        if ($request->filled('date')) {
            try {
                $date  = explode('-', $request->date);
                $start = \Carbon\Carbon::parse(trim($date[0]))->startOfDay();
                $end   = isset($date[1]) ? \Carbon\Carbon::parse(trim($date[1]))->endOfDay() : $start->copy()->endOfDay();
                $query->whereBetween('updated_at', [$start, $end]);
            } catch (\Exception $e) {
                //
            }
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'oldest') {
            $query->orderBy('updated_at', 'asc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('firstname', 'asc')->orderBy('lastname', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('firstname', 'desc')->orderBy('lastname', 'desc');
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        $users = $query->paginate(getPaginate())->withQueryString();

        $stats = [
            'total'         => User::banned()->count(),
            'recent_week'   => User::banned()->where('updated_at', '>=', now()->subDays(7))->count(),
            'recent_month'  => User::banned()->where('updated_at', '>=', now()->subDays(30))->count(),
        ];

        $listType     = 'banned';
        $emptyMessage = __('No banned users found.');

        return view('admin.users.list', compact('pageTitle', 'users', 'listType', 'stats', 'emptyMessage'));
    }

    public function bannedUsersExport(Request $request)
    {
        $query = User::banned()->searchable(['username', 'email', 'firstname', 'lastname', 'mobile'])->orderBy('updated_at', 'desc');

        if ($request->filled('date')) {
            try {
                $date  = explode('-', $request->date);
                $start = \Carbon\Carbon::parse(trim($date[0]))->startOfDay();
                $end   = isset($date[1]) ? \Carbon\Carbon::parse(trim($date[1]))->endOfDay() : $start->copy()->endOfDay();
                $query->whereBetween('updated_at', [$start, $end]);
            } catch (\Exception $e) {
                //
            }
        }

        $users    = $query->limit(10000)->get();
        $filename = 'banned-users-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($users) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, [__('Name'), __('Username'), __('Email'), __('Mobile'), __('Ban Reason'), __('Updated At')]);
            foreach ($users as $u) {
                fputcsv($out, [
                    $u->fullname,
                    $u->username ?? '',
                    $u->email ?? '',
                    $u->mobile ?? '',
                    $u->ban_reason ?? '',
                    $u->updated_at ? $u->updated_at->format('Y-m-d H:i') : '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function emailUnverifiedUsers(Request $request)
    {
        $pageTitle = __('Email Unverified Users');
        $query     = User::emailUnverified()->searchable(['username', 'email', 'firstname', 'lastname', 'mobile']);

        if ($request->filled('date')) {
            try {
                $date  = explode('-', $request->date);
                $start = \Carbon\Carbon::parse(trim($date[0]))->startOfDay();
                $end   = isset($date[1]) ? \Carbon\Carbon::parse(trim($date[1]))->endOfDay() : $start->copy()->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Exception $e) {
                //
            }
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('firstname', 'asc')->orderBy('lastname', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('firstname', 'desc')->orderBy('lastname', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $users = $query->paginate(getPaginate())->withQueryString();

        $stats = [
            'total'     => User::emailUnverified()->count(),
            'new_week'  => User::emailUnverified()->where('created_at', '>=', now()->subDays(7))->count(),
            'new_month' => User::emailUnverified()->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        $listType     = 'emailUnverified';
        $emptyMessage = __('No email unverified users found.');
        return view('admin.users.list', compact('pageTitle', 'users', 'listType', 'stats', 'emptyMessage'));
    }

    public function emailUnverifiedExport(Request $request)
    {
        $query = User::emailUnverified()->searchable(['username', 'email', 'firstname', 'lastname', 'mobile'])->orderBy('id', 'desc');
        if ($request->filled('date')) {
            try {
                $date  = explode('-', $request->date);
                $start = \Carbon\Carbon::parse(trim($date[0]))->startOfDay();
                $end   = isset($date[1]) ? \Carbon\Carbon::parse(trim($date[1]))->endOfDay() : $start->copy()->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Exception $e) {
                //
            }
        }
        $users    = $query->limit(10000)->get();
        $filename = 'email-unverified-users-' . date('Y-m-d-His') . '.csv';
        $headers  = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="' . $filename . '"'];
        $callback = function () use ($users) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, [__('Name'), __('Username'), __('Email'), __('Mobile'), __('Joined At')]);
            foreach ($users as $u) {
                fputcsv($out, [$u->fullname, $u->username ?? '', $u->email ?? '', $u->mobile ?? '', $u->created_at ? $u->created_at->format('Y-m-d H:i') : '']);
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function emailVerifiedUsers()
    {
        $pageTitle = 'Email Verified Users';
        $users     = $this->userData('emailVerified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function mobileUnverifiedUsers(Request $request)
    {
        $pageTitle = __('Mobile Unverified Users');
        $query     = User::mobileUnverified()->searchable(['username', 'email', 'firstname', 'lastname', 'mobile']);

        if ($request->filled('date')) {
            try {
                $date  = explode('-', $request->date);
                $start = \Carbon\Carbon::parse(trim($date[0]))->startOfDay();
                $end   = isset($date[1]) ? \Carbon\Carbon::parse(trim($date[1]))->endOfDay() : $start->copy()->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Exception $e) {
                //
            }
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('firstname', 'asc')->orderBy('lastname', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('firstname', 'desc')->orderBy('lastname', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $users = $query->paginate(getPaginate())->withQueryString();

        $stats = [
            'total'     => User::mobileUnverified()->count(),
            'new_week'  => User::mobileUnverified()->where('created_at', '>=', now()->subDays(7))->count(),
            'new_month' => User::mobileUnverified()->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        $listType     = 'mobileUnverified';
        $emptyMessage = __('No mobile unverified users found.');
        return view('admin.users.list', compact('pageTitle', 'users', 'listType', 'stats', 'emptyMessage'));
    }

    public function mobileUnverifiedExport(Request $request)
    {
        $query = User::mobileUnverified()->searchable(['username', 'email', 'firstname', 'lastname', 'mobile'])->orderBy('id', 'desc');
        if ($request->filled('date')) {
            try {
                $date  = explode('-', $request->date);
                $start = \Carbon\Carbon::parse(trim($date[0]))->startOfDay();
                $end   = isset($date[1]) ? \Carbon\Carbon::parse(trim($date[1]))->endOfDay() : $start->copy()->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } catch (\Exception $e) {
                //
            }
        }
        $users    = $query->limit(10000)->get();
        $filename = 'mobile-unverified-users-' . date('Y-m-d-His') . '.csv';
        $headers  = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="' . $filename . '"'];
        $callback = function () use ($users) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, [__('Name'), __('Username'), __('Email'), __('Mobile'), __('Joined At')]);
            foreach ($users as $u) {
                fputcsv($out, [$u->fullname, $u->username ?? '', $u->email ?? '', $u->mobile ?? '', $u->created_at ? $u->created_at->format('Y-m-d H:i') : '']);
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function mobileVerifiedUsers()
    {
        $pageTitle = 'Mobile Verified Users';
        $users     = $this->userData('mobileVerified');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    public function withBalance()
    {
        $pageTitle = 'Users with Balance';
        $users     = $this->userData('withBalance');
        return view('admin.users.list', compact('pageTitle', 'users'));
    }

    protected function userData($scope = null)
    {
        if ($scope) {
            $users = User::$scope();
        } else {
            $users = User::query();
        }

        return $users->searchable(['username', 'email', 'firstname', 'lastname', 'mobile'])->orderBy('id', 'desc')->paginate(getPaginate());
    }

    public function detail($id)
    {
        $user      = User::findOrFail($id);
        $pageTitle = 'User Detail - ' . $user->username;

        $breadcrumb = [
            ['label' => 'Manage Customer', 'url' => null],
            ['label' => 'All', 'url' => route('admin.users.all')],
            ['label' => 'User Detail - ' . $user->username, 'url' => null],
        ];

        $totalDeposit       = Deposit::where('user_id', $user->id)->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
        $totalTransaction   = Transaction::where('user_id', $user->id)->count();
        $order['total']     = Order::where('user_id', $user->id)->count();
        $order['pending']   = Order::pending()->where('user_id', $user->id)->count();
        $order['shipped']   = Order::shipped()->where('user_id', $user->id)->count();
        $order['confirmed'] = Order::confirmed()->where('user_id', $user->id)->count();
        $order['delivered'] = Order::delivered()->where('user_id', $user->id)->count();
        $order['canceled']  = Order::cancel()->where('user_id', $user->id)->count();
        $order['ticket']    = SupportTicket::where('user_id', $user->id)->count();

        $userCarts    = Cart::where('user_id', $user->id)->with('product')->orderBy('id', 'desc')->limit(20)->get();
        $userWishlist = Wishlist::where('user_id', $user->id)->with('product')->orderBy('id', 'desc')->limit(20)->get();

        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view('admin.users.detail', compact('pageTitle', 'breadcrumb', 'user', 'order', 'totalDeposit', 'totalTransaction', 'countries', 'userCarts', 'userWishlist'));
    }

    public function update(Request $request, $id)
    {
        $user         = User::findOrFail($id);
        $countryData  = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryArray = (array) $countryData;
        $countries    = implode(',', array_keys($countryArray));

        $countryCode = $request->country;
        $country     = $countryData->$countryCode->country;
        $dialCode    = $countryData->$countryCode->dial_code;

        $request->validate([
            'firstname' => 'required|string|max:40',
            'lastname'  => 'required|string|max:40',
            'email'     => 'required|email|string|max:40|unique:users,email,' . $user->id,
            'mobile'    => 'required|string|max:40|unique:users,mobile,' . $user->id,
            'country'   => 'required|in:' . $countries,
        ]);
        $user->mobile       = $dialCode . $request->mobile;
        $user->country_code = $countryCode;
        $user->firstname    = $request->firstname;
        $user->lastname     = $request->lastname;
        $user->email        = $request->email;
        $user->address      = [
            'address' => $request->address,
            'city'    => $request->city,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'country' => @$country,
        ];
        $user->ev = $request->ev ? Status::VERIFIED : Status::UNVERIFIED;
        $user->sv = $request->sv ? Status::VERIFIED : Status::UNVERIFIED;
        $user->ts = $request->ts ? Status::ENABLE : Status::DISABLE;
        $user->save();

        $notify[] = ['success', 'User details updated successfully'];
        return back()->withNotify($notify);
    }

    public function login($id)
    {
        Auth::loginUsingId($id);
        return to_route('user.home');
    }

    public function status(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->status == Status::USER_ACTIVE) {
            $request->validate([
                'reason' => 'required|string|max:255',
            ]);
            $user->status     = Status::USER_BAN;
            $user->ban_reason = $request->reason;
            $notify[]         = ['success', 'User banned successfully'];
        } else {
            $user->status     = Status::USER_ACTIVE;
            $user->ban_reason = null;
            $notify[]         = ['success', 'User unbanned successfully'];
        }

        $user->save();
        return back()->withNotify($notify);
    }

    public function showNotificationSingleForm($id)
    {
        $user    = User::findOrFail($id);
        $general = gs();

        if (!$general->en && !$general->sn) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.users.detail', $user->id)->withNotify($notify);
        }

        $notificationHistory = NotificationLog::where('user_id', $id)->orderBy('id', 'desc')->limit(20)->get();
        $userTickets = SupportTicket::where('user_id', $id)
            ->with(['supportMessage' => fn ($q) => $q->with('admin')->orderBy('id', 'asc')])
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
        $pageTitle           = 'Send Notification to ' . $user->username;
        return view('admin.users.notification_single', compact('pageTitle', 'user', 'general', 'notificationHistory', 'userTickets'));
    }

    public function sendNotificationSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'subject' => 'required|string|max:255',
            'link'    => 'nullable|string|max:500',
        ]);

        $user = User::findOrFail($id);
        $subject = trim($request->subject);
        $messageText = trim($request->message);

        // Create a support ticket + message so it appears in user's Messages page (/message)
        $ticket = new SupportTicket();
        $ticket->user_id    = $user->id;
        $ticket->ticket    = rand(100000, 999999);
        $ticket->name      = $user->fullname;
        $ticket->email     = $user->email ?? '';
        $ticket->subject   = $subject;
        $ticket->last_reply = now();
        $ticket->status    = Status::TICKET_ANSWER;
        $ticket->priority  = Status::PRIORITY_MEDIUM;
        if (\Illuminate\Support\Facades\Schema::hasColumn('support_tickets', 'channel')) {
            $ticket->channel = SupportTicket::CHANNEL_WEB;
        }
        $ticket->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->admin_id          = Auth::guard('admin')->id();
        $message->message           = $messageText;
        $message->save();

        // Notify user (appears in Notifications) and link to this message
        $shortCodes = [
            'subject' => $subject,
            'message' => $messageText,
            'link'    => route('message.view', $ticket->ticket),
        ];
        if ($request->filled('link')) {
            $link = trim($request->link);
            if (!preg_match('#^https?://#i', $link)) {
                $link = url('/') . '/' . ltrim($link, '/');
            }
            $shortCodes['link'] = $link;
        }
        notify($user, 'DEFAULT', $shortCodes);
        $notify[] = ['success', __('Notification sent. It appears in user Notifications and in Messages.')];
        return back()->withNotify($notify);
    }

    public function deleteNotificationLog($userId, $logId)
    {
        $user = User::findOrFail($userId);
        $log  = NotificationLog::where('user_id', $userId)->where('id', $logId)->firstOrFail();
        $log->delete();
        $notify[] = ['success', __('Notification deleted.')];
        return back()->withNotify($notify);
    }

    public function showNotificationAllForm()
    {
        $general = gs();

        if (!$general->en && !$general->sn) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        $users     = User::active()->count();
        $pageTitle = 'Notification to Verified Users';
        return view('admin.users.notification_all', compact('pageTitle', 'users'));
    }

    public function sendNotificationAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'subject' => 'required',
        ]);

        if ($validator->fails()) return response()->json(['error' => $validator->errors()->all()]);
        $users = User::oldest()->active()->skip($request->start)->limit($request->batch)->get();

        foreach ($users as $user) {
            notify($user, 'DEFAULT', [
                'subject' => $request->subject,
                'message' => $request->message,
            ]);
        }

        return response()->json([
            'total_sent' => $users->count(),
        ]);
    }

    public function notificationLog($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = __('Notifications Sent to') . ' ' . $user->username;
        $logs = NotificationLog::where('user_id', $id)->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
        $emptyMessage = __('No notifications found.');
        return view('admin.reports.notification_history', compact('pageTitle', 'logs', 'user', 'emptyMessage'));
    }
}
