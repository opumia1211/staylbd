<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frontend;
use App\Services\HomepageDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomepageSectionController extends Controller
{
    public function index()
    {
        $pageTitle = __('Homepage Sections');
        $this->ensureHomeSectionSettingsExist();
        $this->ensureProductSliderSettingsExist();
        $data = $this->getHomeSectionData();
        $data['product_slider_settings'] = $this->getProductSliderData();
        $categories = \App\Models\Category::active()->orderBy('name')->get(['id', 'name']);
        return view('admin.frontend.homepage_sections.index', compact('pageTitle', 'data', 'categories'));
    }

    protected function ensureProductSliderSettingsExist(): void
    {
        if (Frontend::where('data_keys', 'product_slider.settings')->exists()) {
            return;
        }
        $row = new Frontend();
        $row->data_keys = 'product_slider.settings';
        $row->data_values = (object) getProductSliderSettingsDefaults();
        $row->save();
    }

    protected function getProductSliderData(): object
    {
        $row = Frontend::where('data_keys', 'product_slider.settings')->orderBy('id', 'desc')->first();
        $dv = $row && isset($row->data_values) ? (array) $row->data_values : [];
        return (object) array_merge(getProductSliderSettingsDefaults(), $dv);
    }

    public function saveProductSliderSettings(Request $request)
    {
        $row = Frontend::firstOrNew(['data_keys' => 'product_slider.settings']);
        $row->data_keys = 'product_slider.settings';
        $defaults = getProductSliderSettingsDefaults();
        $current = $row->data_values ? (array) $row->data_values : [];
        $current = array_merge($defaults, $current);

        $current['auto_scroll_enabled'] = (int) $request->input('auto_scroll_enabled', 0);
        $current['scroll_interval_seconds'] = max(2, min(30, (int) $request->input('scroll_interval_seconds', 5)));
        $current['scroll_animation_speed_ms'] = max(300, min(2000, (int) $request->input('scroll_animation_speed_ms', 600)));
        $current['products_per_row_desktop'] = max(3, min(8, (int) $request->input('products_per_row_desktop', 6)));
        $current['products_per_row_tablet'] = max(2, min(6, (int) $request->input('products_per_row_tablet', 4)));
        $current['products_per_row_mobile'] = max(1, min(3, (int) $request->input('products_per_row_mobile', 2)));
        foreach (['hot_deal', 'featured', 'new_arrivals', 'trending', 'best_selling', 'recommended'] as $section) {
            $key = $section . '_interval_seconds';
            $current[$key] = max(2, min(30, (int) $request->input($key, $current[$key] ?? 4)));
        }

        $row->data_values = (object) $current;
        $row->save();
        clearProductSliderCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', __('Product slider settings saved.')];
        return back()->withNotify($notify);
    }

    protected function ensureHomeSectionSettingsExist(): void
    {
        $exists = Frontend::where('data_keys', 'home_section.settings')->exists();
        if (!$exists) {
            $defaults = getHomeSectionSettingsDefaults();
            $row = new Frontend();
            $row->data_keys = 'home_section.settings';
            $row->data_values = (object) $defaults;
            $row->save();
        }
        $this->ensureQuickCategoryDefaults();
        $trustCount = Frontend::where('data_keys', 'home_section.trust')->count();
        if ($trustCount === 0) {
            $defaultTrust = [
                ['title' => 'Secure Payment', 'icon' => 'las la-lock', 'short_detail' => '100% secure payment', 'url' => '#', 'display_order' => 1],
                ['title' => 'Fast Delivery', 'icon' => 'las la-shipping-fast', 'short_detail' => 'Quick delivery', 'url' => '#', 'display_order' => 2],
                ['title' => 'Easy Return', 'icon' => 'las la-undo', 'short_detail' => 'Easy return policy', 'url' => '#', 'display_order' => 3],
                ['title' => 'Customer Satisfaction', 'icon' => 'las la-smile', 'short_detail' => 'Satisfaction guaranteed', 'url' => '#', 'display_order' => 4],
                ['title' => 'Authentic Product', 'icon' => 'las la-certificate', 'short_detail' => '100% authentic', 'url' => '#', 'display_order' => 5],
            ];
            foreach ($defaultTrust as $i => $item) {
                $f = new Frontend();
                $f->data_keys = 'home_section.trust';
                $f->data_values = (object) $item;
                $f->save();
            }
        }
    }

    protected function ensureQuickCategoryDefaults(): void
    {
        if (Frontend::where('data_keys', 'home_section.quick_category')->count() > 0) {
            return;
        }
        $defaults = [
            ['title' => 'Hot Deals', 'icon' => 'las la-bolt', 'link_type' => 'hot_deal', 'display_order' => 1],
            ['title' => 'Top Selling', 'icon' => 'las la-chart-line', 'link_type' => 'best_selling', 'display_order' => 2],
            ['title' => 'New Arrival', 'icon' => 'las la-star', 'link_type' => 'new_arrival', 'display_order' => 3],
            ['title' => 'Featured', 'icon' => 'las la-gem', 'link_type' => 'featured', 'display_order' => 4],
            ['title' => 'Discount', 'icon' => 'las la-tag', 'link_type' => 'discount', 'display_order' => 5],
        ];
        foreach ($defaults as $item) {
            $f = new Frontend();
            $f->data_keys = 'home_section.quick_category';
            $f->data_values = (object) $item;
            $f->save();
        }
    }

    protected function getHomeSectionData(): array
    {
        $settings = Frontend::where('data_keys', 'home_section.settings')->orderBy('id', 'desc')->first();
        $dv = $settings ? (array) ($settings->data_values ?? []) : [];
        $defaults = getHomeSectionSettingsDefaults();
        $settingsMerged = (object) array_merge($defaults, $dv);

        $trustElements = Frontend::where('data_keys', 'home_section.trust')
            ->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED) ASC')
            ->orderBy('id', 'asc')
            ->get();

        $quickServiceElements = Frontend::where('data_keys', 'home_section.quick_service')
            ->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED) ASC')
            ->orderBy('id', 'asc')
            ->get();

        $promoBannerElements = Frontend::where('data_keys', 'home_section.promo_banner')
            ->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED) ASC')
            ->orderBy('id', 'asc')
            ->get();

        $quickCategoryElements = Frontend::where('data_keys', 'home_section.quick_category')
            ->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED) ASC')
            ->orderBy('id', 'asc')
            ->get();

        return [
            'settings' => $settingsMerged,
            'settingsRow' => $settings,
            'trust_elements' => $trustElements,
            'quick_service_elements' => $quickServiceElements,
            'promo_banner_elements' => $promoBannerElements,
            'quick_category_elements' => $quickCategoryElements,
        ];
    }

    public function saveSettings(Request $request)
    {
        $row = Frontend::firstOrNew(['data_keys' => 'home_section.settings']);
        $row->data_keys = 'home_section.settings';
        $defaults = getHomeSectionSettingsDefaults();
        $current = $row->data_values ? (array) $row->data_values : [];
        $current = array_merge($defaults, $current);

        $booleans = [
            'power_zone_enabled', 'show_category_icons', 'show_flash_deals', 'show_trending', 'show_quick_services', 'show_promo_blocks', 'show_quick_category_boxes',
            'trust_section_enabled', 'social_proof_enabled', 'live_purchase_enabled', 'reviews_slider_enabled', 'top_rated_enabled',
            'recommendation_enabled', 'recently_viewed_enabled', 'similar_products_enabled',
            'sticky_cart_enabled', 'quick_view_enabled', 'wishlist_popup_enabled', 'compare_enabled', 'floating_cart_enabled',
            'conversion_enabled', 'limited_stock_enabled', 'only_x_left_enabled', 'people_viewing_enabled', 'recently_sold_enabled',
        ];
        foreach ($booleans as $key) {
            $current[$key] = (int) $request->input($key, 0);
        }
        $current['flash_sale_title'] = $request->input('flash_sale_title', $current['flash_sale_title'] ?? 'Flash Sale');
        $current['flash_sale_end_date'] = $request->input('flash_sale_end_date')
            ?: ($current['flash_sale_end_date'] ?? now()->endOfDay()->toIso8601String());
        $current['flash_deals_limit'] = (int) $request->input('flash_deals_limit', 8);
        $current['trending_limit'] = (int) $request->input('trending_limit', 8);
        $current['top_rated_limit'] = (int) $request->input('top_rated_limit', 8);
        $current['reviews_slider_limit'] = (int) $request->input('reviews_slider_limit', 6);

        $row->data_values = (object) $current;
        $row->save();
        clearHomeSectionCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', __('Homepage settings saved.')];
        return back()->withNotify($notify);
    }

    public function saveTrustElement(Request $request)
    {
        $id = $request->input('id');
        $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'short_detail' => 'nullable|string|max:500',
            'url' => 'nullable|string|max:500',
        ]);
        if ($id) {
            $item = Frontend::where('data_keys', 'home_section.trust')->findOrFail($id);
        } else {
            $item = new Frontend();
            $item->data_keys = 'home_section.trust';
            $maxOrder = (int) Frontend::where('data_keys', 'home_section.trust')->max(DB::raw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED)'));
            $item->data_values = (object) ['display_order' => $maxOrder + 1];
        }
        $dv = (array) ($item->data_values ?? []);
        $dv['title'] = $request->input('title');
        $dv['icon'] = $request->input('icon', 'las la-check-circle');
        $dv['short_detail'] = $request->input('short_detail', '');
        $dv['url'] = $request->input('url', '#');
        $dv['display_order'] = (int) ($dv['display_order'] ?? $item->id ?? 0);
        $item->data_values = (object) $dv;
        $item->save();
        clearHomeSectionCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', $id ? __('Trust item updated.') : __('Trust item added.')];
        return back()->withNotify($notify);
    }

    public function deleteTrustElement($id)
    {
        $item = Frontend::where('data_keys', 'home_section.trust')->findOrFail($id);
        $item->delete();
        clearHomeSectionCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', __('Trust item deleted.')];
        return back()->withNotify($notify);
    }

    public function saveQuickService(Request $request)
    {
        $id = $request->input('id');
        $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'url' => 'required|string|max:500',
        ]);
        if ($id) {
            $item = Frontend::where('data_keys', 'home_section.quick_service')->findOrFail($id);
        } else {
            $item = new Frontend();
            $item->data_keys = 'home_section.quick_service';
            $maxOrder = (int) Frontend::where('data_keys', 'home_section.quick_service')->max(DB::raw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED)'));
            $item->data_values = (object) ['display_order' => $maxOrder + 1];
        }
        $dv = (array) ($item->data_values ?? []);
        $dv['title'] = $request->input('title');
        $dv['icon'] = $request->input('icon', 'las la-link');
        $dv['url'] = $request->input('url');
        $dv['display_order'] = (int) ($dv['display_order'] ?? $item->id ?? 0);
        $item->data_values = (object) $dv;
        $item->save();
        clearHomeSectionCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', $id ? __('Quick service updated.') : __('Quick service added.')];
        return back()->withNotify($notify);
    }

    public function deleteQuickService($id)
    {
        $item = Frontend::where('data_keys', 'home_section.quick_service')->findOrFail($id);
        $item->delete();
        clearHomeSectionCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', __('Quick service deleted.')];
        return back()->withNotify($notify);
    }

    public function savePromoBanner(Request $request)
    {
        $id = $request->input('id');
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:500',
        ]);
        if ($id) {
            $item = Frontend::where('data_keys', 'home_section.promo_banner')->findOrFail($id);
        } else {
            $item = new Frontend();
            $item->data_keys = 'home_section.promo_banner';
            $maxOrder = (int) Frontend::where('data_keys', 'home_section.promo_banner')->max(DB::raw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED)'));
            $item->data_values = (object) ['display_order' => $maxOrder + 1];
        }
        $dv = (array) ($item->data_values ?? []);
        $dv['title'] = $request->input('title');
        $dv['subtitle'] = $request->input('subtitle', '');
        $dv['url'] = $request->input('url', '#');
        $dv['display_order'] = (int) ($dv['display_order'] ?? $item->id ?? 0);
        $item->data_values = (object) $dv;
        $item->save();
        clearHomeSectionCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', $id ? __('Promo banner updated.') : __('Promo banner added.')];
        return back()->withNotify($notify);
    }

    public function deletePromoBanner($id)
    {
        $item = Frontend::where('data_keys', 'home_section.promo_banner')->findOrFail($id);
        $item->delete();
        clearHomeSectionCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', __('Promo banner deleted.')];
        return back()->withNotify($notify);
    }

    public function saveQuickCategory(Request $request)
    {
        $id = $request->input('id');
        $request->validate([
            'title' => 'required|string|max:100',
            'icon' => 'nullable|string|max:80',
            'link_type' => 'required|in:hot_deal,best_selling,new_arrival,featured,discount,category,url',
            'category_id' => 'required_if:link_type,category|nullable|integer|exists:categories,id',
            'custom_url' => 'required_if:link_type,url|nullable|string|max:500',
        ]);
        if ($id) {
            $item = Frontend::where('data_keys', 'home_section.quick_category')->findOrFail($id);
        } else {
            $item = new Frontend();
            $item->data_keys = 'home_section.quick_category';
            $maxOrder = (int) Frontend::where('data_keys', 'home_section.quick_category')->max(DB::raw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED)'));
            $item->data_values = (object) ['display_order' => $maxOrder + 1];
        }
        $dv = (array) ($item->data_values ?? []);
        $dv['title'] = $request->input('title');
        $dv['icon'] = $request->input('icon', 'las la-th-large');
        $dv['link_type'] = $request->input('link_type');
        $dv['category_id'] = $request->input('link_type') === 'category' ? (int) $request->input('category_id') : null;
        $dv['custom_url'] = $request->input('link_type') === 'url' ? $request->input('custom_url') : null;
        $dv['display_order'] = (int) ($dv['display_order'] ?? $item->id ?? 0);
        $item->data_values = (object) $dv;
        $item->save();
        clearHomeSectionCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', $id ? __('Quick category updated.') : __('Quick category added.')];
        return back()->withNotify($notify);
    }

    public function deleteQuickCategory($id)
    {
        $item = Frontend::where('data_keys', 'home_section.quick_category')->findOrFail($id);
        $item->delete();
        clearHomeSectionCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', __('Quick category deleted.')];
        return back()->withNotify($notify);
    }
}
