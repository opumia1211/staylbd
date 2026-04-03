<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frontend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class QuickOrderController extends Controller
{
    const QUICK_ORDER_FIELDS_KEY = 'quick_order.fields';
    const QUICK_ORDER_SETTINGS_KEY = 'quick_order.settings';

    /**
     * Show Quick Order control board: which fields to show on the public Quick Order form.
     */
    public function index()
    {
        $pageTitle = __('Quick Order Control');
        $fields = quickOrderFieldsList();
        $grouped = function_exists('quickOrderFieldsGrouped') ? quickOrderFieldsGrouped() : [];
        $enabled = getQuickOrderEnabledFields();
        $quickOrderUrl = route('user.guest.order');
        $enabledCount = count($enabled);
        $totalCount = count($fields);
        $settingsRow = Frontend::where('data_keys', self::QUICK_ORDER_SETTINGS_KEY)->orderBy('id', 'desc')->first();
        $settings = [
            'subtitle' => $settingsRow && isset($settingsRow->data_values->subtitle) && trim($settingsRow->data_values->subtitle) !== ''
                ? $settingsRow->data_values->subtitle
                : __('Place your order in seconds — no account needed. Our team will confirm by phone.'),
            'show_register_link' => $settingsRow && isset($settingsRow->data_values->show_register_link)
                ? (bool) $settingsRow->data_values->show_register_link
                : true,
        ];
        return view('admin.frontend.quickorder', compact('pageTitle', 'fields', 'grouped', 'enabled', 'quickOrderUrl', 'enabledCount', 'totalCount', 'settings'));
    }

    /**
     * Save selected Quick Order fields and header settings. Stores in Frontend table (and general_settings if column exists).
     */
    public function save(Request $request)
    {
        $request->validate([
            'quick_order_fields' => 'nullable|array',
            'quick_order_subtitle' => 'nullable|string|max:255',
            'show_register_link' => 'nullable|boolean',
        ]);
        $allowedKeys = array_keys(quickOrderFieldsList());
        $submitted = $request->input('quick_order_fields', []);
        $enabled = [];
        foreach ($allowedKeys as $key) {
            if (!empty($submitted[$key]) && (int) $submitted[$key] === 1) {
                $enabled[] = $key;
            }
        }
        if (empty($enabled)) {
            $enabled = ['guest_phone', 'guest_name', 'guest_address', 'guest_area_city', 'guest_delivery_note'];
        }

        $row = Frontend::where('data_keys', self::QUICK_ORDER_FIELDS_KEY)->orderBy('id', 'desc')->first();
        if (!$row) {
            $row = new Frontend();
            $row->data_keys = self::QUICK_ORDER_FIELDS_KEY;
        }
        $row->data_values = (object) ['fields' => $enabled];
        $row->save();

        if (Schema::hasColumn('general_settings', 'quick_order_fields')) {
            $general = gs();
            $general->quick_order_fields = json_encode($enabled);
            $general->save();
            Cache::forget('GeneralSetting');
        }

        $settingsRow = Frontend::where('data_keys', self::QUICK_ORDER_SETTINGS_KEY)->orderBy('id', 'desc')->first();
        if (!$settingsRow) {
            $settingsRow = new Frontend();
            $settingsRow->data_keys = self::QUICK_ORDER_SETTINGS_KEY;
        }
        $settingsRow->data_values = (object) [
            'subtitle' => $request->input('quick_order_subtitle') ?: __('Order in 10 seconds · No registration · We confirm by phone'),
            'show_register_link' => $request->boolean('show_register_link', true),
        ];
        $settingsRow->save();

        $notify[] = ['success', __('Quick Order settings saved. The public Quick Order page will show the updated fields and header.')];
        return redirect()->route('admin.frontend.quickorder')->withNotify($notify);
    }
}
