<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderAutomationLog;
use App\Models\OrderAutomationSetting;
use App\Services\OrderAutomationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class OrderAutomationController extends Controller
{
    public function __construct(protected OrderAutomationService $automation)
    {
    }

    public function index()
    {
        if (!$this->automation->isAvailable()) {
            $notify[] = ['info', __('Run php artisan migrate to enable Order Automation.')];
            return redirect()->route('admin.orders.hub')->withNotify($notify);
        }

        $pageTitle = __('Order Automation');
        $settings = OrderAutomationSetting::current();
        $logs = OrderAutomationLog::with('order')->latest('id')->paginate(20);

        return view('admin.orders.automation.index', compact('pageTitle', 'settings', 'logs'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'auto_cancel_unpaid_days' => 'required|integer|min:1|max:90',
            'run_interval_minutes' => 'required|integer|min:5|max:1440',
            'sla_pending_hours' => 'required|integer|min:1|max:168',
            'sla_fulfillment_hours' => 'required|integer|min:1|max:336',
        ]);

        $settings = OrderAutomationSetting::current();
        $settings->fill([
            'is_enabled' => $request->boolean('is_enabled'),
            'auto_confirm_paid' => $request->boolean('auto_confirm_paid'),
            'auto_processing_after_confirm' => $request->boolean('auto_processing_after_confirm'),
            'auto_cancel_unpaid_enabled' => $request->boolean('auto_cancel_unpaid_enabled'),
            'auto_cancel_unpaid_days' => (int) $request->auto_cancel_unpaid_days,
            'notify_customer_on_auto' => $request->boolean('notify_customer_on_auto'),
            'notify_admin_new_order' => $request->boolean('notify_admin_new_order'),
            'channel_import_enabled' => $request->boolean('channel_import_enabled'),
            'run_interval_minutes' => (int) $request->run_interval_minutes,
            'sla_pending_hours' => (int) $request->sla_pending_hours,
            'sla_fulfillment_hours' => (int) $request->sla_fulfillment_hours,
            'sla_alerts_enabled' => $request->boolean('sla_alerts_enabled'),
        ]);
        $settings->save();

        app(\App\Services\OrderOperationsService::class)->clearCountCache();

        $notify[] = ['success', __('Order automation settings saved.')];
        return back()->withNotify($notify);
    }

    public function runNow()
    {
        $settings = OrderAutomationSetting::current();
        $result = $this->automation->run($settings, true);

        $notify[] = ['success', __('Automation completed: :c confirmed, :p processing, :x cancelled.', [
            'c' => $result['confirmed'],
            'p' => $result['processing'],
            'x' => $result['cancelled'],
        ])];

        return back()->withNotify($notify);
    }
}
