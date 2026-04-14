<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UiSetting;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class UiSettingsController extends Controller
{
    private const TEMPLATE_KEYS = ['default', 'template_2', 'template_3', 'template_4', 'template_5', 'template_6', 'template_7', 'template_8'];

    public function index()
    {
        $pageTitle = __('UI & Theme Settings');
        $uiTableReady = UiSetting::isTableQueryable();
        $ui = UiSetting::getSettings();
        return view('admin.ui-settings.index', compact('pageTitle', 'ui', 'uiTableReady'));
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
            'header_top_bg' => 'nullable|string|max:30',
            'theme_template' => 'nullable|string|in:default,template_2,template_3,template_4,template_5,template_6,template_7,template_8|max:50',
            'ui_action' => 'nullable|string|in:save,apply_preset,reset_default',
            'preset_key' => 'nullable|string|max:50',
        ]);

        $hex = function ($v) {
            if (!$v || !is_string($v)) return null;
            $v = trim($v);
            if (preg_match('/^#?[a-fA-F0-9]{3,8}$/', $v)) return (strpos($v, '#') === 0 ? $v : '#' . $v);
            return strlen($v) <= 30 ? $v : null;
        };

        try {
            if (!UiSetting::isTableQueryable()) {
                $notify[] = ['error', __('UI settings table is missing or broken. Please run migration/repair first.')];
                return back()->withNotify($notify);
            }

            $ui = UiSetting::find(1) ?? new UiSetting(['id' => 1]);
            $action = (string) ($request->ui_action ?? 'save');

            if ($action === 'reset_default') {
                $this->applyPayload($ui, $this->defaultPayload(), $hex);
                $ui->save();
                $notify[] = ['success', __('UI reset done. Default colors restored.')];
                return back()->withNotify($notify);
            }

            if ($action === 'apply_preset') {
                $presetKey = (string) ($request->preset_key ?? '');
                $presetMap = $this->presetPayloads();
                if (!array_key_exists($presetKey, $presetMap)) {
                    $notify[] = ['error', __('Preset not found.')];
                    return back()->withNotify($notify);
                }
                $this->applyPayload($ui, $presetMap[$presetKey], $hex);
                $ui->save();
                $notify[] = ['success', __('Preset applied successfully.')];
                return back()->withNotify($notify);
            }

            $this->applyPayload($ui, [
                'product_card_bg' => $request->product_card_bg,
                'product_button_color' => $request->product_button_color,
                'product_buy_now_color' => $request->product_buy_now_color,
                'product_buy_now_hover' => $request->product_buy_now_hover,
                'product_price_color' => $request->product_price_color,
                'header_bg' => $request->header_bg,
                'footer_bg' => $request->footer_bg,
                'rating_color' => $request->rating_color,
                'discount_badge_color' => $request->discount_badge_color,
                'stock_color' => $request->stock_color,
                'shipping_badge_color' => $request->shipping_badge_color,
                'header_top_bg' => $request->header_top_bg,
                'theme_template' => $request->theme_template,
            ], $hex);
            $ui->save();
        } catch (QueryException $e) {
            $notify[] = ['error', __('Database error while saving UI settings. Please repair/migrate ui_settings table first.')];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', __('UI settings updated. Frontend will use new colors and theme.')];
        return back()->withNotify($notify);
    }

    private function applyPayload(UiSetting $ui, array $payload, callable $hex): void
    {
        $ui->product_card_bg = $hex($payload['product_card_bg'] ?? null) ?? '#ffffff';
        $ui->product_button_color = $hex($payload['product_button_color'] ?? null) ?? '#1f2937';
        $ui->product_buy_now_color = $hex($payload['product_buy_now_color'] ?? null) ?? '#0e9f90';
        $ui->product_buy_now_hover = $hex($payload['product_buy_now_hover'] ?? null) ?? '#0c8a7d';
        $ui->product_price_color = $hex($payload['product_price_color'] ?? null) ?? $ui->product_buy_now_color ?? '#0e9f90';
        $ui->header_bg = $hex($payload['header_bg'] ?? null);
        $ui->footer_bg = $hex($payload['footer_bg'] ?? null);
        $ui->rating_color = $hex($payload['rating_color'] ?? null) ?? '#f59e0b';
        $ui->discount_badge_color = $hex($payload['discount_badge_color'] ?? null) ?? '#dc2626';
        $ui->stock_color = $hex($payload['stock_color'] ?? null) ?? '#16a34a';
        $ui->shipping_badge_color = $hex($payload['shipping_badge_color'] ?? null) ?? '#2563eb';
        $ui->header_top_bg = $hex($payload['header_top_bg'] ?? null) ?? '#0f172a';

        $theme = (string) ($payload['theme_template'] ?? 'default');
        $ui->theme_template = in_array($theme, self::TEMPLATE_KEYS, true) ? $theme : 'default';
    }

    private function defaultPayload(): array
    {
        return [
            'product_card_bg' => '#ffffff',
            'product_button_color' => '#1f2937',
            'product_buy_now_color' => '#0e9f90',
            'product_buy_now_hover' => '#0c8a7d',
            'product_price_color' => '#0e9f90',
            'header_bg' => '#ffffff',
            'footer_bg' => '#0f172a',
            'rating_color' => '#f59e0b',
            'discount_badge_color' => '#dc2626',
            'stock_color' => '#16a34a',
            'shipping_badge_color' => '#2563eb',
            'header_top_bg' => '#0f172a',
            'theme_template' => 'default',
        ];
    }

    private function presetPayloads(): array
    {
        return [
            'clean_default' => $this->defaultPayload(),
            'ocean_pro' => [
                'product_card_bg' => '#f8fbff',
                'product_button_color' => '#0f172a',
                'product_buy_now_color' => '#0284c7',
                'product_buy_now_hover' => '#0369a1',
                'product_price_color' => '#0ea5e9',
                'header_bg' => '#ffffff',
                'footer_bg' => '#082f49',
                'rating_color' => '#f59e0b',
                'discount_badge_color' => '#e11d48',
                'stock_color' => '#16a34a',
                'shipping_badge_color' => '#2563eb',
                'header_top_bg' => '#0f172a',
                'theme_template' => 'template_2',
            ],
            'midnight_lux' => [
                'product_card_bg' => '#111827',
                'product_button_color' => '#f59e0b',
                'product_buy_now_color' => '#f97316',
                'product_buy_now_hover' => '#ea580c',
                'product_price_color' => '#fbbf24',
                'header_bg' => '#0b1220',
                'footer_bg' => '#020617',
                'rating_color' => '#f59e0b',
                'discount_badge_color' => '#ef4444',
                'stock_color' => '#22c55e',
                'shipping_badge_color' => '#38bdf8',
                'header_top_bg' => '#0f172a',
                'theme_template' => 'template_3',
            ],
            'emerald_market' => [
                'product_card_bg' => '#f7fffb',
                'product_button_color' => '#065f46',
                'product_buy_now_color' => '#0d9488',
                'product_buy_now_hover' => '#0f766e',
                'product_price_color' => '#0f766e',
                'header_bg' => '#ecfdf5',
                'footer_bg' => '#022c22',
                'rating_color' => '#f59e0b',
                'discount_badge_color' => '#dc2626',
                'stock_color' => '#16a34a',
                'shipping_badge_color' => '#0ea5e9',
                'header_top_bg' => '#0f172a',
                'theme_template' => 'template_4',
            ],
            'rose_studio' => [
                'product_card_bg' => '#fff7fb',
                'product_button_color' => '#be185d',
                'product_buy_now_color' => '#ec4899',
                'product_buy_now_hover' => '#db2777',
                'product_price_color' => '#be185d',
                'header_bg' => '#fff1f2',
                'footer_bg' => '#4a044e',
                'rating_color' => '#f59e0b',
                'discount_badge_color' => '#e11d48',
                'stock_color' => '#22c55e',
                'shipping_badge_color' => '#6366f1',
                'header_top_bg' => '#0f172a',
                'theme_template' => 'template_5',
            ],
            'arctic_glass' => [
                'product_card_bg' => '#eef4ff',
                'product_button_color' => '#1e3a8a',
                'product_buy_now_color' => '#2563eb',
                'product_buy_now_hover' => '#1d4ed8',
                'product_price_color' => '#1d4ed8',
                'header_bg' => '#f8fbff',
                'footer_bg' => '#0f1f3d',
                'rating_color' => '#f59e0b',
                'discount_badge_color' => '#dc2626',
                'stock_color' => '#16a34a',
                'shipping_badge_color' => '#2563eb',
                'header_top_bg' => '#0f172a',
                'theme_template' => 'template_6',
            ],
            'violet_glass' => [
                'product_card_bg' => '#f8f5ff',
                'product_button_color' => '#5b21b6',
                'product_buy_now_color' => '#7c3aed',
                'product_buy_now_hover' => '#6d28d9',
                'product_price_color' => '#7c3aed',
                'header_bg' => '#f6f3ff',
                'footer_bg' => '#2e1065',
                'rating_color' => '#f59e0b',
                'discount_badge_color' => '#e11d48',
                'stock_color' => '#16a34a',
                'shipping_badge_color' => '#4f46e5',
                'header_top_bg' => '#0f172a',
                'theme_template' => 'template_7',
            ],
            'graphite_clean' => [
                'product_card_bg' => '#f3f4f6',
                'product_button_color' => '#111827',
                'product_buy_now_color' => '#0f766e',
                'product_buy_now_hover' => '#115e59',
                'product_price_color' => '#0f766e',
                'header_bg' => '#ffffff',
                'footer_bg' => '#111827',
                'rating_color' => '#f59e0b',
                'discount_badge_color' => '#dc2626',
                'stock_color' => '#15803d',
                'shipping_badge_color' => '#2563eb',
                'header_top_bg' => '#0f172a',
                'theme_template' => 'template_8',
            ],
        ];
    }
}
