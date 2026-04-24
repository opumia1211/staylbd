<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * List all orders for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->with(['orderDetail', 'orderDetail.product'])
            ->orderBy('id', 'desc')
            ->paginate(10);
            
        return response()->json([
            'status' => 'success',
            'data' => [
                'orders' => $orders
            ]
        ]);
    }

    /**
     * Get details for a specific order
     */
    public function show($id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)
            ->with(['orderDetail', 'orderDetail.product', 'shipping', 'shippingZone'])
            ->find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => __('Order not found')
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'order' => $order
            ]
        ]);
    }
}
