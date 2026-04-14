<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ShippingRuleController extends Controller
{
    public function index()
    {
        $pageTitle = __('Shipping Rules');
        if (!Schema::hasTable('shipping_rules')) {
            $notify[] = ['info', __('Run one-time setup:') . ' php artisan migrate'];
            return redirect()->route('admin.shipping.index')->withNotify($notify);
        }
        $rule = ShippingRule::first();
        if (!$rule) {
            $rule = new ShippingRule([
                'free_shipping_min_amount' => null,
                'cod_extra_charge' => 0,
                'express_extra_charge' => 0,
                'international_enabled' => true,
                'header_notice_text' => 'Cash on Delivery available nationwide',
            ]);
            $rule->id = 0;
        }
        return view('admin.shipping.rules', compact('pageTitle', 'rule'));
    }

    public function update(Request $request)
    {
        if (!Schema::hasTable('shipping_rules')) {
            $notify[] = ['error', 'Shipping rules table is missing.'];
            return back()->withNotify($notify);
        }
        $request->validate([
            'free_shipping_min_amount' => 'nullable|numeric|min:0',
            'cod_extra_charge' => 'required|numeric|min:0',
            'express_extra_charge' => 'required|numeric|min:0',
            'international_enabled' => 'nullable|in:0,1',
            'header_notice_text' => 'nullable|string|max:255',
        ]);

        $rule = ShippingRule::first();
        if (!$rule) {
            $rule = new ShippingRule();
        }
        $rule->free_shipping_min_amount = $request->free_shipping_min_amount ?: null;
        $rule->cod_extra_charge = $request->cod_extra_charge ?? 0;
        $rule->express_extra_charge = $request->express_extra_charge ?? 0;
        $rule->international_enabled = (bool) $request->international_enabled;
        $rule->header_notice_text = trim((string) $request->input('header_notice_text', 'Cash on Delivery available nationwide'));
        $rule->save();

        ShippingRule::clearCache();

        $notify[] = ['success', 'Shipping rules updated successfully.'];
        return back()->withNotify($notify);
    }
}
