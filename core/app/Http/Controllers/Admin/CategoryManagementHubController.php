<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\ProductAttribute;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Schema;

class CategoryManagementHubController extends Controller
{
    public function index()
    {
        $pageTitle = __('Category Center');

        $stats = [
            'categories' => Category::count(),
            'subcategories' => Subcategory::count(),
            'brands' => Brand::count(),
            'coupons' => Coupon::count(),
            'attributes' => Schema::hasTable('product_attributes')
                ? ProductAttribute::count()
                : 0,
        ];

        $modules = [
            [
                'title' => __('Categories'),
                'description' => __('Main catalog structure, images, homepage placement'),
                'route' => 'admin.category.index',
                'icon' => 'align-left',
                'color' => 'primary',
                'count' => $stats['categories'],
            ],
            [
                'title' => __('Subcategories'),
                'description' => __('Nested categories under main categories'),
                'route' => 'admin.subcategory.index',
                'icon' => 'align-center',
                'color' => 'info',
                'count' => $stats['subcategories'],
            ],
            [
                'title' => __('Brands'),
                'description' => __('Brand logos, featured brands, SEO'),
                'route' => 'admin.brand.index',
                'icon' => 'tags',
                'color' => 'success',
                'count' => $stats['brands'],
            ],
            [
                'title' => __('Product Attributes'),
                'description' => __('Size, color, material — reusable attribute sets'),
                'route' => 'admin.attributes.index',
                'icon' => 'sliders-h',
                'color' => 'warning',
                'count' => $stats['attributes'],
            ],
            [
                'title' => __('Category Attributes'),
                'description' => __('Assign attributes per category for variant forms'),
                'route' => 'admin.category.attributes.index',
                'icon' => 'tag',
                'color' => 'secondary',
            ],
            [
                'title' => __('Coupons'),
                'description' => __('Discount codes, limits and scheduling'),
                'route' => 'admin.coupon.index',
                'icon' => 'bullhorn',
                'color' => 'danger',
                'count' => $stats['coupons'],
            ],
            [
                'title' => __('Add Attribute'),
                'description' => __('Create size, color or custom variant attributes'),
                'route' => 'admin.attributes.create',
                'icon' => 'plus',
                'color' => 'dark',
            ],
            [
                'title' => __('Homepage Sections'),
                'description' => __('Place categories and products on the storefront home'),
                'route' => 'admin.frontend.sections.homepage',
                'icon' => 'th-large',
                'color' => 'info',
            ],
            [
                'title' => __('SEO Manager'),
                'description' => __('Category and brand SEO for search engines'),
                'route' => 'admin.seo',
                'icon' => 'globe',
                'color' => 'primary',
            ],
        ];

        return view('admin.category.hub', compact('pageTitle', 'stats', 'modules'));
    }
}
