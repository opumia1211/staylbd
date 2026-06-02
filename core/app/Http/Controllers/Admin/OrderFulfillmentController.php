<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderOperationsService;
use Illuminate\Http\Request;

class OrderFulfillmentController extends Controller
{
    public function __construct(protected OrderOperationsService $ops)
    {
    }

    public function index(Request $request)
    {
        $pageTitle = __('Fulfillment Queue');
        $tab = $request->get('tab', 'queue');
        $sla = $this->ops->slaSettings();

        $query = Order::with(['user'])->latest('id');

        if ($tab === 'sla' && $sla['enabled']) {
            $pendingCut = now()->subHours($sla['pending_hours']);
            $fulfillCut = now()->subHours($sla['fulfillment_hours']);
            $query->where(function ($q) use ($pendingCut, $fulfillCut) {
                $q->where(function ($q2) use ($pendingCut) {
                    $q2->where('order_status', Status::ORDER_PENDING)->where('created_at', '<', $pendingCut);
                })->orWhere(function ($q3) use ($fulfillCut) {
                    $q3->whereIn('order_status', [
                        Status::ORDER_CONFIRMED,
                        Status::ORDER_PROCESSING,
                        Status::ORDER_PACKAGING,
                    ])->where('updated_at', '<', $fulfillCut);
                });
            });
        } elseif ($tab === 'returns') {
            $query->where('order_status', Status::ORDER_RETURNED);
        } elseif ($tab === 'failed') {
            $query->whereIn('order_status', [Status::ORDER_DELIVERY_FAILED, Status::ORDER_RETURNED]);
        } else {
            $query->whereIn('order_status', [
                Status::ORDER_PENDING,
                Status::ORDER_CONFIRMED,
                Status::ORDER_PROCESSING,
                Status::ORDER_PACKAGING,
            ]);
        }

        if ($request->filled('search')) {
            $query->searchable(['order_no', 'guest_name', 'guest_phone', 'user:username']);
        }

        $orders = $query->paginate(getPaginate())->withQueryString();

        $counts = [
            'queue' => $this->ops->fulfillmentQueueCount(),
            'sla' => $this->ops->slaOverdueCount(),
            'returns' => Order::where('order_status', Status::ORDER_RETURNED)->count(),
        ];

        return view('admin.orders.fulfillment', compact('pageTitle', 'orders', 'tab', 'sla', 'counts'));
    }
}
