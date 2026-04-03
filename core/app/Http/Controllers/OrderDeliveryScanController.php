<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class OrderDeliveryScanController extends Controller
{
    /**
     * Public URL when customer scans QR on invoice.
     * Notifies admin that customer has acknowledged/received delivery.
     * Admin notification link: Order detail page (e.g. /sajaladminopu/order/details/{id})
     */
    public function scanned(Request $request, string $token)
    {
        $order = Order::where('delivery_scan_token', $token)->with('user')->first();

        if (!$order) {
            return response()->view('errors.404', [], 404);
        }

        $justScanned = false;
        if (!$order->delivery_scanned_at) {
            $order->delivery_scanned_at = now();
            $order->save();

            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $order->user_id;
            $adminNotification->title = __('Customer :name scanned delivery QR for order :order', [
                'name' => $order->user->fullname ?? $order->user->username ?? 'N/A',
                'order' => $order->order_no,
            ]);
            $adminNotification->click_url = urlPath('admin.orders.detail', $order->id);
            $adminNotification->save();
            $justScanned = true;
        }

        return view('order_delivery_scanned', [
            'order' => $order,
            'justScanned' => $justScanned,
        ]);
    }

    /**
     * When delivery man scans QR: notify admin, notify customer, redirect to Google Maps with delivery address.
     * Admin notification link: same order detail page.
     */
    public function driverScanned(Request $request, string $token)
    {
        $order = Order::where('delivery_driver_scan_token', $token)->with('user')->first();

        if (!$order) {
            return response()->view('errors.404', [], 404);
        }

        $address = is_string($order->address) ? json_decode($order->address) : $order->address;
        $parts = [];
        if ($address && is_object($address)) {
            if (!empty($address->address)) $parts[] = $address->address;
            if (!empty($address->address_2)) $parts[] = $address->address_2;
            if (!empty($address->division)) $parts[] = $address->division;
            if (!empty($address->city)) $parts[] = $address->city;
            if (!empty($address->thana)) $parts[] = $address->thana;
            if (!empty($address->state)) $parts[] = $address->state;
            if (!empty($address->country)) $parts[] = $address->country;
            if (!empty($address->zip)) $parts[] = $address->zip;
        }
        $query = implode(', ', $parts);
        $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($query ?: 'Bangladesh');

        $justScanned = false;
        if (!$order->delivery_driver_scanned_at) {
            $order->delivery_driver_scanned_at = now();
            $order->save();

            $clientName = $order->user->fullname ?? $order->user->username ?? 'N/A';
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $order->user_id;
            $adminNotification->title = __('Product is with delivery man — Order :order | Client :name', [
                'order' => $order->order_no,
                'name' => $clientName,
            ]);
            $adminNotification->click_url = urlPath('admin.orders.detail', $order->id);
            $adminNotification->save();

            $user = $order->user;
            if ($user) {
                notify($user, 'DELIVERY_SCANNED_BY_DRIVER', [
                    'order_no' => $order->order_no,
                    'method_name' => __('Your product is nearby — delivery personnel has the order.'),
                    'map_link' => $mapsUrl,
                ], null, true);
            }
            $justScanned = true;
        }

        return redirect()->away($mapsUrl);
    }
}
