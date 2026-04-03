<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\CodSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CodSettingsController extends Controller
{
    public function index()
    {
        $pageTitle = __('COD (Cash on Delivery) Settings');
        if (!Schema::hasTable('cod_settings')) {
            $notify[] = ['info', __('Run migrations:') . ' php artisan migrate'];
            return redirect()->route('admin.shipping.index')->withNotify($notify);
        }
        $cod = CodSettings::first();
        if (!$cod) {
            $cod = new CodSettings([
                'cod_enabled' => true,
                'cod_min_order' => 500,
                'cod_max_order' => 20000,
                'cod_charge_type' => Status::COD_CHARGE_FLAT,
                'cod_charge_value' => 50,
                'cod_free_above' => 2000,
                'cod_otp_required' => false,
                'cod_otp_expire_minutes' => 10,
                'cod_auto_cancel_hours' => 24,
                'cod_failed_disable_count' => 2,
                'cod_new_customer_max' => 5000,
            ]);
            $cod->id = 0;
        }
        return view('admin.shipping.cod', compact('pageTitle', 'cod'));
    }

    public function update(Request $request)
    {
        if (!Schema::hasTable('cod_settings')) {
            $notify[] = ['error', 'COD settings table is missing.'];
            return back()->withNotify($notify);
        }
        $request->validate([
            'cod_enabled' => 'nullable|in:0,1',
            'cod_min_order' => 'nullable|numeric|min:0',
            'cod_max_order' => 'nullable|numeric|min:0',
            'cod_charge_type' => 'required|in:1,2',
            'cod_charge_value' => 'nullable|numeric|min:0',
            'cod_free_above' => 'nullable|numeric|min:0',
            'cod_otp_required' => 'nullable|in:0,1',
            'cod_otp_expire_minutes' => 'nullable|integer|min:5|max:60',
            'cod_auto_cancel_hours' => 'nullable|integer|min:1|max:168',
            'cod_failed_disable_count' => 'nullable|integer|min:0|max:10',
            'cod_new_customer_max' => 'nullable|numeric|min:0',
        ]);

        $cod = CodSettings::first();
        if (!$cod) {
            $cod = new CodSettings();
        }
        $cod->cod_enabled = (bool) $request->cod_enabled;
        $cod->cod_min_order = $request->cod_min_order ?: 0;
        $cod->cod_max_order = $request->cod_max_order ?: 0;
        $cod->cod_charge_type = (int) $request->cod_charge_type;
        $cod->cod_charge_value = $request->cod_charge_value ?: 0;
        $cod->cod_free_above = $request->cod_free_above ?: 0;
        $cod->cod_otp_required = (bool) $request->cod_otp_required;
        $cod->cod_otp_expire_minutes = $request->cod_otp_expire_minutes ?: 10;
        $cod->cod_auto_cancel_hours = $request->cod_auto_cancel_hours ?: 24;
        $cod->cod_failed_disable_count = $request->cod_failed_disable_count ?? 2;
        $cod->cod_new_customer_max = $request->cod_new_customer_max ?: 0;
        $cod->save();

        CodSettings::clearCache();

        $notify[] = ['success', __('COD settings updated successfully.')];
        return back()->withNotify($notify);
    }
}
