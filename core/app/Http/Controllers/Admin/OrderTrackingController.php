<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderShipmentTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderTrackingController extends Controller
{
    public function store(Request $request, $orderId)
    {
        $request->validate([
            'status'          => 'required|in:processing,picked,in_transit,out_for_delivery,delivered',
            'location_name'   => 'nullable|string|max:200',
            'location_address'=> 'nullable|string|max:500',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'notes'           => 'nullable|string|max:500',
            'tracking_number' => 'nullable|string|max:100',
            'courier_name'    => 'nullable|string|max:100',
            'tracking_link'   => 'nullable|url|max:500',
            'notify_user'     => 'nullable|boolean',
        ]);

        $order = Order::with('user')->findOrFail($orderId);

        $statusLabel = OrderShipmentTracking::statusLabels()[$request->status] ?? $request->status;
        $track = OrderShipmentTracking::create([
            'order_id'         => $order->id,
            'status'           => $request->status,
            'location_name'    => $request->location_name ?: null,
            'location_address' => $request->location_address ?: null,
            'latitude'         => $request->latitude ?: null,
            'longitude'        => $request->longitude ?: null,
            'notes'            => $request->notes ?: null,
            'tracking_number'  => $request->tracking_number ?: null,
            'courier_name'     => $request->courier_name ?: null,
            'tracking_link'    => $request->tracking_link ?: null,
        ]);

        if ($request->boolean('notify_user') && $order->user) {
            $message = __('Your order') . ' #' . $order->order_no . ' - ' . __($statusLabel);
            if ($request->location_name) {
                $message .= ' | ' . $request->location_name;
            }
            if ($request->notes) {
                $message .= ' | ' . Str::limit($request->notes, 80);
            }
            notify($order->user, 'ORDER_STATUS', [
                'method_name' => $message,
                'user_name'   => $order->user->username,
                'order_no'    => $order->order_no,
                'total'       => showAmount($order->total),
                'link'        => route('user.order.detail', $order->id),
            ]);
        }

        $notify[] = ['success', __('Tracking update added successfully.')];
        return back()->withNotify($notify);
    }

    public function destroy($orderId, $id = null)
    {
        $track = OrderShipmentTracking::where('order_id', $orderId)->findOrFail($id);
        $track->delete();
        $notify[] = ['success', __('Tracking update removed.')];
        return back()->withNotify($notify);
    }
}
