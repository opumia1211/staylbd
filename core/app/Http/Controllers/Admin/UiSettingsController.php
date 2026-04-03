<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class UiSettingsController extends Controller
{
    public function index()
    {
        if (!Schema::hasTable('ui_settings')) {
            return redirect()->route('admin.dashboard')->withErrors(['message' => __('Run: php artisan migrate')]);
        }
        $pageTitle = __('UI & Theme Settings');
        $ui = UiSetting::getSettings();
        return view('admin.ui-settings.index', compact('pageTitle', 'ui'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_card_bg' => 'nullable|string|max:30',
            'product_button_color' => 'nullable|string|max:30',
            'product_buy_now_color' => 'nullable|string|max:30',
            'product_buy_now_hover' => 'nullable|string|max:30',
            'product_price_color' => 'nullable|string|max:30',
            'header_bg' => 'nullable|string|max:30',
            'footer_bg' => 'nullable|string|max:30',
            'rating_color' => 'nullable|string|max:30',
            'discount_badge_color' => 'nullable|string|max:30',
            'stock_color' => 'nullable|string|max:30',
            'shipping_badge_color' => 'nullable|string|max:30',
            'theme_template' => 'nullable|string|in:default,template_2,template_3|max:50',
        ]);

        $hex = function ($v) {
            if (!$v || !is_string($v)) return null;
            $v = trim($v);
            if (preg_match('/^#?[a-fA-F0-9]{3,8}$/', $v)) return (strpos($v, '#') === 0 ? $v : '#' . $v);
            return strlen($v) <= 30 ? $v : null;
        };

        $ui = UiSetting::find(1) ?? new UiSetting(['id' => 1]);
        $ui->product_card_bg = $hex($request->product_card_bg) ?? '#ffffff';
        $ui->product_button_color = $hex($request->product_button_color) ?? '#1f2937';
        $ui->product_buy_now_color = $hex($request->product_buy_now_color) ?? '#0e9f90';
        $ui->product_buy_now_hover = $hex($request->product_buy_now_hover) ?? '#0c8a7d';
        $ui->product_price_color = $hex($request->product_price_color) ?? $ui->product_buy_now_color ?? '#0e9f90';
        $ui->header_bg = $hex($request->header_bg);
        $ui->footer_bg = $hex($request->footer_bg);
        $ui->rating_color = $hex($request->rating_color) ?? '#f59e0b';
        $ui->discount_badge_color = $hex($request->discount_badge_color) ?? '#dc2626';
        $ui->stock_color = $hex($request->stock_color) ?? '#16a34a';
        $ui->shipping_badge_color = $hex($request->shipping_badge_color) ?? '#2563eb';
        $ui->theme_template = $request->theme_template ?? 'default';
        $ui->save();

        $notify[] = ['success', __('UI settings updated. Frontend will use new colors and theme.')];
        return back()->withNotify($notify);
    }
}
