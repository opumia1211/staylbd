<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderAutomationLog;
use App\Models\OrderAutomationSetting;
use App\Models\OrderChannel;
use App\Services\OrderOperationsService;
use Illuminate\Support\Facades\Schema;

class OrderManagementHubController extends Controller
{
    public function __construct(protected OrderOperationsService $ops)
    {
    }

    public function index()
    {
        $pageTitle = __('Order Center');

        $statusCounts = $this->ops->statusCounts(60);
        $todayStart = now()->startOfDay();

        $stats = [
            'today_count' => Order::where('created_at', '>=', $todayStart)->count(),
            'today_value' => (float) Order::where('created_at', '>=', $todayStart)->sum('total'),
            'month_count' => Order::where('created_at', '>=', now()->startOfMonth())->count(),
            'month_value' => (float) Order::where('created_at', '>=', now()->startOfMonth())->sum('total'),
        ];

        $channelsAvailable = Schema::hasTable('order_channels');
        $channelsCount = $channelsAvailable ? OrderChannel::count() : 0;
        $activeChannels = $channelsAvailable ? OrderChannel::where('is_active', true)->count() : 0;
        $importedViaChannel = $channelsAvailable
            ? Order::whereNotNull('external_order_ref')->count()
            : 0;

        $automationAvailable = Schema::hasTable('order_automation_settings');
        $automationSettings = $automationAvailable ? OrderAutomationSetting::current() : null;
        $automationEnabled = $automationSettings?->is_enabled ?? false;
        $recentAutomation = $automationAvailable
            ? OrderAutomationLog::latest('id')->limit(5)->get()
            : collect();

        $slaOverdue = $this->ops->slaOverdueCount();
        $fulfillmentQueue = $this->ops->fulfillmentQueueCount();
        $featureMatrix = $this->ops->featureMatrix();
        $featuresEnabled = collect($featureMatrix)->where('enabled', true)->count();
        $featuresTotal = count($featureMatrix);

        $modules = [
            [
                'title' => __('Fulfillment Queue'),
                'description' => __('To-process list, SLA overdue, returns'),
                'route' => 'admin.orders.fulfillment',
                'icon' => 'tasks',
                'color' => 'danger',
                'count' => $fulfillmentQueue,
                'badge' => $slaOverdue > 0 ? $slaOverdue . ' SLA' : null,
            ],
            [
                'title' => __('Order Automation'),
                'description' => __('Auto-confirm, cancel stale, cron workflow'),
                'route' => 'admin.orders.automation.index',
                'icon' => 'robot',
                'color' => 'primary',
                'badge' => $automationEnabled ? __('ON') : __('OFF'),
            ],
            [
                'title' => __('All Orders'),
                'description' => __('Search, bulk status, courier'),
                'route' => 'admin.orders.index',
                'icon' => 'list-alt',
                'color' => 'dark',
                'count' => $statusCounts['all'],
            ],
            [
                'title' => __('Order Channels'),
                'description' => __('WooCommerce, Shopify, webhook import'),
                'route' => 'admin.orders.channels.index',
                'icon' => 'project-diagram',
                'color' => 'info',
                'count' => $channelsCount,
            ],
            [
                'title' => __('Import / Export'),
                'description' => __('CSV for partners & accounting'),
                'route' => 'admin.orders.import-export',
                'icon' => 'file-export',
                'color' => 'success',
            ],
            [
                'title' => __('Abandoned Carts'),
                'description' => __('Recovery & reminders'),
                'route' => 'admin.abandoned-orders.index',
                'icon' => 'shopping-cart',
                'color' => 'warning',
            ],
            [
                'title' => __('Courier Hub'),
                'description' => __('Pathao, Steadfast, bulk ship'),
                'route' => 'admin.api.courier.manage',
                'icon' => 'truck',
                'color' => 'secondary',
            ],
            [
                'title' => __('Delivery Scan'),
                'description' => __('QR confirm monitoring'),
                'route' => 'admin.notifications.delivery.scan',
                'icon' => 'qrcode',
                'color' => 'info',
            ],
        ];

        $statusLinks = [
            ['label' => __('Pending'), 'route' => 'admin.orders.pending', 'count' => $statusCounts['pending'], 'variant' => 'warning'],
            ['label' => __('Confirmed'), 'route' => 'admin.orders.confirmed', 'count' => $statusCounts['confirmed'], 'variant' => 'info'],
            ['label' => __('Processing'), 'route' => 'admin.orders.processing', 'count' => $statusCounts['processing'], 'variant' => 'primary'],
            ['label' => __('Packaging'), 'route' => 'admin.orders.packaging', 'count' => $statusCounts['packaging'], 'variant' => 'secondary'],
            ['label' => __('Shipped'), 'route' => 'admin.orders.shipped', 'count' => $statusCounts['shipped'], 'variant' => 'secondary'],
            ['label' => __('Delivered'), 'route' => 'admin.orders.delivered', 'count' => $statusCounts['delivered'], 'variant' => 'success'],
            ['label' => __('Canceled'), 'route' => 'admin.orders.cancel', 'count' => $statusCounts['cancel'], 'variant' => 'danger'],
        ];

        if (($statusCounts['returned'] ?? 0) > 0) {
            $statusLinks[] = [
                'label' => __('Returned'),
                'route' => 'admin.orders.fulfillment',
                'route_params' => ['tab' => 'returns'],
                'count' => $statusCounts['returned'],
                'variant' => 'dark',
            ];
        }

        return view('admin.orders.hub', compact(
            'pageTitle',
            'statusCounts',
            'stats',
            'modules',
            'statusLinks',
            'channelsAvailable',
            'channelsCount',
            'activeChannels',
            'importedViaChannel',
            'automationAvailable',
            'automationEnabled',
            'automationSettings',
            'recentAutomation',
            'slaOverdue',
            'fulfillmentQueue',
            'featureMatrix',
            'featuresEnabled',
            'featuresTotal'
        ));
    }
}
