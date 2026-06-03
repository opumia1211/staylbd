<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Schema;

class ProductManagementHubController extends Controller
{
    public function index()
    {
        $pageTitle = __('Product Center');

        $lowStockThreshold = 5;
        $lowStockQuery = Product::query()->where('status', Status::ENABLE);
        if (Schema::hasColumn('products', 'low_stock_alert')) {
            $lowStockQuery->whereRaw('quantity <= COALESCE(low_stock_alert, ?)', [$lowStockThreshold]);
        } else {
            $lowStockQuery->where('quantity', '<=', $lowStockThreshold);
        }

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('status', Status::ENABLE)->count(),
            'low_stock' => (clone $lowStockQuery)->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
        ];

        $modules = [
            [
                'title' => __('All Products'),
                'description' => __('Search, filter, bulk edit and manage inventory'),
                'route' => 'admin.product.index',
                'icon' => 'boxes',
                'color' => 'primary',
                'count' => $stats['active'],
            ],
            [
                'title' => __('Add New Product'),
                'description' => __('Full product form — images, variants, SEO'),
                'route' => 'admin.product.create',
                'icon' => 'plus-circle',
                'color' => 'success',
            ],
            [
                'title' => __('Quick Upload'),
                'description' => __('Simplified upload for general / clothing products'),
                'route' => 'admin.product.general.create',
                'icon' => 'cloud-upload-alt',
                'color' => 'info',
            ],
            [
                'title' => __('Product Reviews'),
                'description' => __('Approve, feature and moderate customer reviews'),
                'route' => 'admin.product.reviews.index',
                'icon' => 'comment-dots',
                'color' => 'warning',
                'count' => $stats['pending_reviews'],
                'badge' => $stats['pending_reviews'] > 0 ? __('Pending') : null,
            ],
            [
                'title' => __('Low Stock Items'),
                'description' => __('Products at or below alert threshold'),
                'route' => 'admin.product.index',
                'route_query' => ['low_stock' => 1],
                'icon' => 'exclamation-triangle',
                'color' => 'warning',
                'count' => $stats['low_stock'],
            ],
            [
                'title' => __('Stock Alerts'),
                'description' => __('Configure and view stock alert rules'),
                'route' => 'admin.product.stock.alerts',
                'icon' => 'bell',
                'color' => 'danger',
            ],
            [
                'title' => __('Product Performance'),
                'description' => __('Sales, views and conversion analytics'),
                'route' => 'admin.report.product',
                'icon' => 'chart-bar',
                'color' => 'dark',
            ],
            [
                'title' => __('Stock & Order Messages'),
                'description' => __('Out-of-stock and order notification text'),
                'route' => 'admin.setting.stock.order.messages',
                'icon' => 'comment-alt',
                'color' => 'secondary',
            ],
            [
                'title' => __('Product Views'),
                'description' => __('Track which products customers view most'),
                'route' => 'admin.report.activity.product_views',
                'icon' => 'eye',
                'color' => 'info',
            ],
            [
                'title' => __('SEO Manager'),
                'description' => __('Meta titles, descriptions and search visibility'),
                'route' => 'admin.seo',
                'icon' => 'globe',
                'color' => 'primary',
            ],
            [
                'title' => __('Featured Products'),
                'description' => __('Highlight products on homepage and listings'),
                'route' => 'admin.product.feature.index',
                'icon' => 'certificate',
                'color' => 'warning',
            ],
            [
                'title' => __('Quick Deals'),
                'description' => __('Today’s deal badges and urgency pricing'),
                'route' => 'admin.product.todayDeal',
                'icon' => 'bolt',
                'color' => 'danger',
            ],
        ];

        return view('admin.product.hub', compact('pageTitle', 'stats', 'modules'));
    }
}
