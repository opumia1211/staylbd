<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function home()
    {
        $pageTitle = 'Dashboard';
        $user      = auth()->user();

        // Real-time: no cache so dashboard always shows latest (orders, cart, wishlist, notifications, messages)
        $orders = Order::where('user_id', $user->id)
            ->with(['deposit', 'orderDetail.product'])
            ->latest()
            ->take(5)
            ->get();

        $result = Order::where('user_id', $user->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN order_status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN order_status = ? THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN order_status = ? THEN 1 ELSE 0 END) as shipped,
                SUM(CASE WHEN order_status = ? THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN order_status = ? THEN 1 ELSE 0 END) as cancelled
            ', [
                Status::ORDER_PENDING,
                Status::ORDER_CONFIRMED,
                Status::ORDER_SHIPPED,
                Status::ORDER_DELIVERED,
                Status::ORDER_CANCEL
            ])
            ->first();

        $order = [
            'total'     => (int) ($result->total ?? 0),
            'pending'   => (int) ($result->pending ?? 0),
            'confirmed' => (int) ($result->confirmed ?? 0),
            'shipped'   => (int) ($result->shipped ?? 0),
            'delivered' => (int) ($result->delivered ?? 0),
            'cancelled' => (int) ($result->cancelled ?? 0),
        ];

        $supportCount = SupportTicket::where('user_id', $user->id)->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count();
        $cartCount    = Cart::where('user_id', $user->id)->count();
        $wishlistCount = Wishlist::where('user_id', $user->id)->count();
        $unreadNotifications = NotificationLog::where('user_id', $user->id)->whereNull('read_at')->count();

        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle', 'orders', 'order', 'supportCount', 'cartCount', 'wishlistCount', 'unreadNotifications'));
    }

    public function notifications()
    {
        $pageTitle = 'Notifications';
        $userId = auth()->id();
        // Mark all as read when user enters notifications page (signal clears)
        NotificationLog::where('user_id', $userId)->whereNull('read_at')->update(['read_at' => now()]);
        $notifications = NotificationLog::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());
        return view($this->activeTemplate . 'user.notifications', compact('pageTitle', 'notifications'));
    }

    /** Mark one notification as read and redirect to its click_url (or back to list). */
    public function notificationRead($id)
    {
        $log = NotificationLog::where('user_id', auth()->id())->where('id', $id)->firstOrFail();
        $log->update(['read_at' => $log->read_at ?? now()]);
        $url = !empty($log->click_url) ? $log->click_url : route('user.notifications');
        return redirect($url);
    }

    /** Mark all notifications as read (badge clears). */
    public function notificationReadAll()
    {
        NotificationLog::where('user_id', auth()->id())->whereNull('read_at')->update(['read_at' => now()]);
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => __('All marked as read.')]);
        }
        return back()->withNotify(['success', __('All notifications marked as read.')]);
    }

    /** Remove/clear all read notifications (or all if empty). */
    public function notificationClearAll()
    {
        $query = NotificationLog::where('user_id', auth()->id());
        $query->delete();
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => __('All notifications cleared.')]);
        }
        return back()->withNotify(['success', __('All notifications cleared.')]);
    }

    public function transactions()
    {
        $pageTitle    = 'Payments';
        $remarks      = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view($this->activeTemplate . 'user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function attachmentDownload($fileHash)
    {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general   = gs();
        $title     = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype  = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData()
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $pageTitle = 'Complete Your Profile';
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view($this->activeTemplate . 'user.user_data', compact('pageTitle', 'user', 'countries'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $countryData = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',', array_column($countryData, 'dial_code'));
        $countries = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'firstname'   => 'required|string|max:100',
            'lastname'    => 'required|string|max:100',
            'country'     => 'required|string|in:'.$countries,
            'country_code'=> 'required|in:'.$countryCodes,
            'mobile_code' => 'required|in:'.$mobileCodes,
            'mobile'      => 'required|regex:/^([0-9]*)$/',
            'age'         => 'required|integer|min:13|max:120',
        ]);

        $exist = \App\Models\User::where('mobile', $request->mobile_code.$request->mobile)->where('id', '!=', $user->id)->first();
        if ($exist) {
            $notify[] = ['error', 'The mobile number is already in use.'];
            return back()->withNotify($notify)->withInput();
        }

        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->country_code = $request->country_code;
        $user->mobile = $request->mobile_code.$request->mobile;
        $user->age = (int) $request->age;
        $user->address = [
            'country' => $request->country,
            'address' => $request->address ?? '',
            'state'   => $request->state ?? '',
            'zip'     => $request->zip ?? '',
            'city'    => $request->city ?? '',
        ];
        $user->profile_complete = Status::YES;
        $user->save();

        $notify[] = ['success', 'Profile completed successfully.'];
        return to_route('user.home')->withNotify($notify);
    }
}
