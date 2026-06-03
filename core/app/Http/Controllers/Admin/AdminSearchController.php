<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class AdminSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));
        
        // Allow single character search
        if (strlen($query) < 1) {
            return response()->json([
                'success' => true,
                'results' => []
            ]);
        }

        $results = [];
        $searchTerm = strtolower($query);

        // Search Products - Lightweight: only needed columns
        $products = Product::select('id', 'name', 'product_sku')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('product_sku', 'LIKE', "%{$query}%")
                    ->orWhere('summary', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->limit(12)
            ->get();

        foreach ($products as $product) {
            $results[] = [
                'type' => 'product',
                'title' => $product->name,
                'description' => 'Product' . ($product->product_sku ? ' - SKU: ' . $product->product_sku : ''),
                'url' => route('admin.product.edit', $product->id),
                'icon' => 'las la-box',
                'category' => 'Products'
            ];
        }

        // Search Users - Lightweight
        $users = User::select('id', 'username', 'email', 'firstname', 'lastname')
            ->where(function ($q) use ($query) {
                $q->where('username', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->orWhere('firstname', 'LIKE', "%{$query}%")
                    ->orWhere('lastname', 'LIKE', "%{$query}%");
            })
            ->limit(8)
            ->get();

        foreach ($users as $user) {
            $results[] = [
                'type' => 'user',
                'title' => $user->username . ' (' . $user->email . ')',
                'description' => 'User - ' . ($user->firstname ?? '') . ' ' . ($user->lastname ?? ''),
                'url' => route('admin.users.detail', $user->id),
                'icon' => 'las la-user',
                'category' => 'Users'
            ];
        }

        $categories = Category::select('id', 'name')
            ->where('name', 'LIKE', "%{$query}%")
            ->limit(8)
            ->get();

        foreach ($categories as $category) {
            $results[] = [
                'type' => 'category',
                'title' => $category->name,
                'description' => 'Category',
                'url' => route('admin.category.index'),
                'icon' => 'las la-align-left',
                'category' => 'Categories'
            ];
        }

        $brands = Brand::select('id', 'name')
            ->where('name', 'LIKE', "%{$query}%")
            ->limit(8)
            ->get();

        foreach ($brands as $brand) {
            $results[] = [
                'type' => 'brand',
                'title' => $brand->name,
                'description' => 'Brand',
                'url' => route('admin.brand.index'),
                'icon' => 'las la-tags',
                'category' => 'Brands'
            ];
        }

        $orders = Order::select('id', 'order_no', 'order_status')
            ->where('order_no', 'LIKE', "%{$query}%")
            ->limit(8)
            ->orderByDesc('id')
            ->get();

        foreach ($orders as $order) {
            $results[] = [
                'type' => 'order',
                'title' => 'Order #' . $order->order_no,
                'description' => 'Order - ' . ucfirst($order->order_status ?? ''),
                'url' => route('admin.orders.detail', $order->id),
                'icon' => 'las la-list-alt',
                'category' => 'Orders'
            ];
        }

        // Search Menu Items and Routes - COMPREHENSIVE LIST
        $menuItems = $this->searchMenuItems($searchTerm);
        $results = array_merge($results, $menuItems);

        // Search Frontend Sections
        $frontendSections = $this->searchFrontendSections($searchTerm);
        $results = array_merge($results, $frontendSections);

        // Search Settings and Features
        $settings = $this->searchSettings($searchTerm);
        $results = array_merge($results, $settings);

        // Drop duplicate URLs (same page from menu + section + keyword search)
        $seenUrls = [];
        $results = array_values(array_filter($results, function ($item) use (&$seenUrls) {
            $url = $item['url'] ?? '';
            if ($url === '') {
                return true;
            }
            if (isset($seenUrls[$url])) {
                return false;
            }
            $seenUrls[$url] = true;

            return true;
        }));

        // Fallback: when no or very few results, show suggested/popular links so "wrong" query still shows something
        if (count($results) < 5) {
            $suggested = $this->getSuggestedMenuItems();
            $seen = array_column($results, 'url');
            foreach ($suggested as $item) {
                if (!in_array($item['url'], $seen, true)) {
                    $results[] = $item;
                    $seen[] = $item['url'];
                }
            }
        }

        // Group results by category
        $groupedResults = [];
        foreach ($results as $result) {
            $category = $result['category'];
            if (!isset($groupedResults[$category])) {
                $groupedResults[$category] = [];
            }
            $groupedResults[$category][] = $result;
        }

        return response()->json([
            'success' => true,
            'results' => $groupedResults,
            'total' => count($results)
        ]);
    }

    /**
     * Suggested/popular menu items when search has no or few results (so wrong/typo queries still show something).
     */
    private function getSuggestedMenuItems(): array
    {
        $menus = self::getAdminMenuIndex();
        $popular = ['Dashboard', 'Manage Orders', 'All Orders', 'Manage Products', 'Manage Customer', 'Report', 'Transaction Log', 'General Setting', 'Manage Section', 'Payment Gateways', 'Coupon', 'Support Ticket'];
        $out = [];
        foreach ($menus as $m) {
            if (!in_array($m['title'], $popular, true)) {
                continue;
            }
            try {
                $url = isset($m['route_param']) ? route($m['route'], $m['route_param']) : route($m['route']);
                if (!empty($m['route_query']) && is_array($m['route_query'])) {
                    $url .= '?' . http_build_query($m['route_query']);
                }
            } catch (\Throwable $e) {
                continue;
            }
            $out[] = [
                'type' => 'menu',
                'title' => $m['title'],
                'description' => $m['category'],
                'url' => $url,
                'icon' => $m['icon'],
                'category' => 'Suggested'
            ];
        }
        return array_slice($out, 0, 12);
    }

    /**
     * Single source of truth: all admin panel menu/section/feature entries (sidebar + modules).
     * Used for search index and suggested fallback. Kept static for lightweight reuse.
     */
    public static function getAdminMenuIndex(): array
    {
        return [
            ['title' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'las la-home', 'category' => 'Navigation', 'keywords' => ['dashboard', 'home', 'main']],
            ['title' => 'Security', 'route' => 'admin.security.dashboard', 'icon' => 'las la-shield-alt', 'category' => 'System', 'keywords' => ['security', '2fa', 'lock']],
            ['title' => 'Manage Customer', 'route' => 'admin.users.all', 'icon' => 'las la-users', 'category' => 'Users', 'keywords' => ['customer', 'manage customer', 'user management']],
            ['title' => 'Active Users', 'route' => 'admin.users.active', 'icon' => 'las la-users', 'category' => 'Users', 'keywords' => ['active', 'active users', 'verified']],
            ['title' => 'Banned Users', 'route' => 'admin.users.banned', 'icon' => 'las la-users', 'category' => 'Users', 'keywords' => ['banned', 'blocked', 'suspended']],
            ['title' => 'All Users', 'route' => 'admin.users.all', 'icon' => 'las la-users', 'category' => 'Users', 'keywords' => ['all users', 'user list', 'users']],
            ['title' => 'Email Unverified Users', 'route' => 'admin.users.email.unverified', 'icon' => 'las la-users', 'category' => 'Users', 'keywords' => ['email unverified', 'email verification']],
            ['title' => 'Mobile Unverified Users', 'route' => 'admin.users.mobile.unverified', 'icon' => 'las la-users', 'category' => 'Users', 'keywords' => ['mobile unverified', 'mobile verification', 'phone']],
            ['title' => 'Email Verified Users', 'route' => 'admin.users.email.verified', 'icon' => 'las la-users', 'category' => 'Users', 'keywords' => ['email verified']],
            ['title' => 'Mobile Verified Users', 'route' => 'admin.users.mobile.verified', 'icon' => 'las la-users', 'category' => 'Users', 'keywords' => ['mobile verified']],
            ['title' => 'Notification to All Users', 'route' => 'admin.users.notification.all', 'icon' => 'las la-users', 'category' => 'Users', 'keywords' => ['notification all', 'send notification', 'broadcast']],
            ['title' => 'Subscribers', 'route' => 'admin.subscriber.index', 'icon' => 'las la-thumbs-up', 'category' => 'Users', 'keywords' => ['subscriber', 'subscribers', 'newsletter']],
            ['title' => 'Manage Orders', 'route' => 'admin.orders.index', 'icon' => 'las la-list-alt', 'category' => 'Orders', 'keywords' => ['manage orders', 'order management', 'ordr', 'oder']],
            ['title' => 'All Orders', 'route' => 'admin.orders.index', 'icon' => 'las la-list-alt', 'category' => 'Orders', 'keywords' => ['all orders', 'orders']],
            ['title' => 'Pending Orders', 'route' => 'admin.orders.pending', 'icon' => 'las la-list-alt', 'category' => 'Orders', 'keywords' => ['pending', 'pending orders', 'new orders']],
            ['title' => 'Confirmed Orders', 'route' => 'admin.orders.confirmed', 'icon' => 'las la-list-alt', 'category' => 'Orders', 'keywords' => ['confirmed', 'confirmed orders']],
            ['title' => 'Processing Orders', 'route' => 'admin.orders.processing', 'icon' => 'las la-cog', 'category' => 'Orders', 'keywords' => ['processing', 'processing orders']],
            ['title' => 'Packaging Orders', 'route' => 'admin.orders.packaging', 'icon' => 'las la-box', 'category' => 'Orders', 'keywords' => ['packaging', 'packaging orders', 'pack']],
            ['title' => 'Shipped Orders', 'route' => 'admin.orders.shipped', 'icon' => 'las la-list-alt', 'category' => 'Orders', 'keywords' => ['shipped', 'shipping', 'dispatched']],
            ['title' => 'Delivered Orders', 'route' => 'admin.orders.delivered', 'icon' => 'las la-list-alt', 'category' => 'Orders', 'keywords' => ['delivered', 'completed', 'delivery']],
            ['title' => 'Canceled Orders', 'route' => 'admin.orders.cancel', 'icon' => 'las la-list-alt', 'category' => 'Orders', 'keywords' => ['canceled', 'cancelled', 'cancelled orders']],
            ['title' => 'Delivery Scan Notifications', 'route' => 'admin.notifications.delivery.scan', 'icon' => 'las la-qrcode', 'category' => 'Orders', 'keywords' => ['delivery scan', 'qr scan', 'delivery notifications', 'monitoring']],
            ['title' => 'Manage Categories', 'route' => 'admin.category.index', 'icon' => 'las la-align-left', 'category' => 'Products', 'keywords' => ['category', 'categories', 'manage categories']],
            ['title' => 'Manage Subcategories', 'route' => 'admin.subcategory.index', 'icon' => 'las la-align-center', 'category' => 'Products', 'keywords' => ['subcategory', 'subcategories', 'sub category']],
            ['title' => 'Manage Brands', 'route' => 'admin.brand.index', 'icon' => 'las la-tags', 'category' => 'Products', 'keywords' => ['brand', 'brands', 'manage brands']],
            ['title' => 'Order Center', 'route' => 'admin.orders.hub', 'icon' => 'las la-th-large', 'category' => 'Orders', 'keywords' => ['order center', 'order hub', 'orders hub']],
            ['title' => 'Order Automation', 'route' => 'admin.orders.automation.index', 'icon' => 'las la-robot', 'category' => 'Orders', 'keywords' => ['order automation', 'automation']],
            ['title' => 'Fulfillment Queue', 'route' => 'admin.orders.fulfillment', 'icon' => 'las la-tasks', 'category' => 'Orders', 'keywords' => ['fulfillment', 'fulfillment queue']],
            ['title' => 'Order Channels', 'route' => 'admin.orders.channels.index', 'icon' => 'las la-project-diagram', 'category' => 'Orders', 'keywords' => ['order channels', 'channels']],
            ['title' => 'Import Export Orders', 'route' => 'admin.orders.import-export', 'icon' => 'las la-exchange-alt', 'category' => 'Orders', 'keywords' => ['import export', 'import orders']],
            ['title' => 'Payment Center', 'route' => 'admin.payment.gateways.hub', 'icon' => 'las la-th-large', 'category' => 'Payments', 'keywords' => ['payment center', 'gateways hub']],
            ['title' => 'Middle Banner', 'route' => 'admin.frontend.sections.middle_banner', 'icon' => 'las la-image', 'category' => 'Home', 'keywords' => ['middle banner']],
            ['title' => 'Bottom Banner', 'route' => 'admin.frontend.sections.bottom_banner', 'icon' => 'las la-image', 'category' => 'Home', 'keywords' => ['bottom banner']],
            ['title' => 'Product Center', 'route' => 'admin.product.hub', 'icon' => 'las la-th-large', 'category' => 'Products', 'keywords' => ['product center', 'upload hub', 'product hub']],
            ['title' => 'Category Center', 'route' => 'admin.category.hub', 'icon' => 'las la-th-large', 'category' => 'Categories', 'keywords' => ['category center', 'taxonomy hub']],
            ['title' => 'Quick Upload', 'route' => 'admin.product.general.create', 'icon' => 'las la-cloud-upload-alt', 'category' => 'Products', 'keywords' => ['quick upload', 'fast upload', 'general create']],
            ['title' => 'Homepage Ads', 'route' => 'admin.frontend.sections.homepageAds', 'icon' => 'las la-ad', 'category' => 'Home', 'keywords' => ['homepage ads', 'home ads', 'banner ads']],
            ['title' => 'Header Icons Upload', 'route' => 'admin.frontend.sections.headericons', 'icon' => 'las la-icons', 'category' => 'Home', 'keywords' => ['header icons', 'header upload']],
            ['title' => 'Banner Management', 'route' => 'admin.frontend.sections.banner', 'icon' => 'las la-image', 'category' => 'Home', 'keywords' => ['banner', 'banners', 'slider']],
            ['title' => 'Manage Products', 'route' => 'admin.product.index', 'icon' => 'las la-tshirt', 'category' => 'Products', 'keywords' => ['manage products', 'product management']],
            ['title' => 'All Products', 'route' => 'admin.product.index', 'icon' => 'las la-tshirt', 'category' => 'Products', 'keywords' => ['all products', 'products', 'product list']],
            ['title' => 'Create Product', 'route' => 'admin.product.create', 'icon' => 'las la-tshirt', 'category' => 'Products', 'keywords' => ['create product', 'add product', 'new product']],
            ['title' => 'Product Reviews', 'route' => 'admin.product.reviews.index', 'icon' => 'las la-comment-dots', 'category' => 'Products', 'keywords' => ['reviews', 'product reviews', 'customer reviews', 'ratings']],
            ['title' => 'Low Stock', 'route' => 'admin.product.index', 'route_query' => ['low_stock' => 1], 'icon' => 'las la-exclamation-triangle', 'category' => 'Products', 'keywords' => ['low stock', 'stock', 'inventory', 'out of stock']],
            ['title' => 'Featured Products', 'route' => 'admin.product.feature.index', 'icon' => 'las la-tshirt', 'category' => 'Products', 'keywords' => ['featured', 'featured products', 'feature']],
            ['title' => 'Hot Deal Products', 'route' => 'admin.product.hot', 'icon' => 'las la-tshirt', 'category' => 'Products', 'keywords' => ['hot deal', 'hot', 'hot products', 'deal']],
            ['title' => 'Today Deal Products', 'route' => 'admin.product.todayDeal', 'icon' => 'las la-tshirt', 'category' => 'Products', 'keywords' => ['today deal', 'today', 'daily deal']],
            ['title' => 'Trending Now', 'route' => 'admin.product.trending', 'icon' => 'las la-fire-alt', 'category' => 'Products', 'keywords' => ['trending', 'trending now', 'trending products']],
            ['title' => 'Best Selling', 'route' => 'admin.product.bestSelling', 'icon' => 'las la-tshirt', 'category' => 'Products', 'keywords' => ['best selling', 'bestseller']],
            ['title' => 'Top Feature Boxes', 'route' => 'admin.product.topbar.index', 'icon' => 'las la-th-large', 'category' => 'Products', 'keywords' => ['top feature', 'topbar', 'power zone']],
            ['title' => 'Offer Timers', 'route' => 'admin.offer-timers.index', 'icon' => 'las la-clock', 'category' => 'Products', 'keywords' => ['offer timer', 'offer timers', 'timer', 'discount offer', 'countdown', 'cart timer', 'special offer']],
            ['title' => 'Popup Ads', 'route' => 'admin.popup-ads.index', 'icon' => 'las la-window-maximize', 'category' => 'Products', 'keywords' => ['popup', 'popup ads', 'ad', 'modal']],
            ['title' => 'Product Attributes', 'route' => 'admin.attributes.index', 'icon' => 'las la-list', 'category' => 'Products', 'keywords' => ['attribute', 'attributes', 'product attribute']],
            ['title' => 'Category Attributes', 'route' => 'admin.category.attributes.index', 'icon' => 'las la-list', 'category' => 'Products', 'keywords' => ['category attribute']],
            ['title' => 'Coupon', 'route' => 'admin.coupon.index', 'icon' => 'las la-bullhorn', 'category' => 'Settings', 'keywords' => ['coupon', 'coupons', 'discount', 'promo', 'promotion']],
            ['title' => 'Shipping Method', 'route' => 'admin.shipping.index', 'icon' => 'las la-truck-moving', 'category' => 'Settings', 'keywords' => ['shipping', 'shipping method', 'delivery', 'shipping settings']],
            ['title' => 'Hub', 'route' => 'admin.shipping.index', 'icon' => 'las la-th-large', 'category' => 'Settings', 'keywords' => ['hub', 'shipping hub']],
            ['title' => 'Zones', 'route' => 'admin.shipping.zones.index', 'icon' => 'las la-map-marked-alt', 'category' => 'Settings', 'keywords' => ['zones', 'shipping zones']],
            ['title' => 'Methods', 'route' => 'admin.shipping.methods.index', 'icon' => 'las la-shipping-fast', 'category' => 'Settings', 'keywords' => ['methods', 'shipping methods']],
            ['title' => 'Rules', 'route' => 'admin.shipping.rules.index', 'icon' => 'las la-cog', 'category' => 'Settings', 'keywords' => ['rules', 'shipping rules']],
            ['title' => 'Courier API', 'route' => 'admin.api.courier.manage', 'icon' => 'las la-truck', 'category' => 'Settings', 'keywords' => ['courier', 'courier api', 'delivery api']],
            ['title' => 'Courier Settings', 'route' => 'admin.api.courier.manage', 'icon' => 'las la-cog', 'category' => 'Settings', 'keywords' => ['courier settings', 'courier configuration']],
            ['title' => 'Bulk Courier', 'route' => 'admin.orders.bulk.courier', 'route_param' => 'pathao', 'icon' => 'las la-shipping-fast', 'category' => 'Settings', 'keywords' => ['bulk courier', 'pathao', 'bulk shipping']],
            ['title' => 'Steadfast Courier', 'route' => 'admin.orders.bulk.courier', 'route_param' => 'steadfast', 'icon' => 'las la-truck', 'category' => 'Settings', 'keywords' => ['steadfast', 'steadfast courier']],
            ['title' => 'Courier Logs', 'route' => 'admin.api.courier.logs', 'icon' => 'las la-list-alt', 'category' => 'Reports', 'keywords' => ['courier logs', 'courier history']],
            ['title' => 'Courier Reports', 'route' => 'admin.api.courier.reports', 'icon' => 'las la-chart-bar', 'category' => 'Reports', 'keywords' => ['courier reports', 'courier analytics']],
            ['title' => 'Payment Gateways', 'route' => 'admin.gateway.automatic.index', 'icon' => 'las la-credit-card', 'category' => 'Settings', 'keywords' => ['payment', 'payment gateway', 'gateway', 'payment method', 'payment system']],
            ['title' => 'Payment Gateways Hub', 'route' => 'admin.payment.gateways.hub', 'icon' => 'las la-th-large', 'category' => 'Settings', 'keywords' => ['payment hub', 'gateways hub']],
            ['title' => 'Payment Analytics', 'route' => 'admin.payment.analytics', 'icon' => 'las la-chart-line', 'category' => 'Settings', 'keywords' => ['payment analytics', 'analytics']],
            ['title' => 'Automatic Gateways', 'route' => 'admin.gateway.automatic.index', 'icon' => 'las la-credit-card', 'category' => 'Settings', 'keywords' => ['automatic gateway', 'auto payment', 'online payment']],
            ['title' => 'Manual Gateways', 'route' => 'admin.gateway.manual.index', 'icon' => 'las la-credit-card', 'category' => 'Settings', 'keywords' => ['manual gateway', 'manual payment', 'offline payment']],
            ['title' => 'Autopay', 'route' => 'admin.gateway.autopay.index', 'icon' => 'las la-credit-card', 'category' => 'Settings', 'keywords' => ['autopay', 'auto pay']],
            ['title' => 'Payments', 'route' => 'admin.deposit.list', 'icon' => 'las la-file-invoice-dollar', 'category' => 'Payments', 'keywords' => ['payment', 'payments', 'deposit', 'deposits']],
            ['title' => 'Pending Payments', 'route' => 'admin.deposit.pending', 'icon' => 'las la-file-invoice-dollar', 'category' => 'Payments', 'keywords' => ['pending payment', 'pending deposit']],
            ['title' => 'Approved Payments', 'route' => 'admin.deposit.approved', 'icon' => 'las la-file-invoice-dollar', 'category' => 'Payments', 'keywords' => ['approved payment', 'approved deposit']],
            ['title' => 'Successful Payments', 'route' => 'admin.deposit.successful', 'icon' => 'las la-file-invoice-dollar', 'category' => 'Payments', 'keywords' => ['successful payment', 'successful deposit']],
            ['title' => 'Rejected Payments', 'route' => 'admin.deposit.rejected', 'icon' => 'las la-file-invoice-dollar', 'category' => 'Payments', 'keywords' => ['rejected payment', 'rejected deposit']],
            ['title' => 'Initiated Payments', 'route' => 'admin.deposit.initiated', 'icon' => 'las la-file-invoice-dollar', 'category' => 'Payments', 'keywords' => ['initiated payment', 'initiated deposit']],
            ['title' => 'All Payments', 'route' => 'admin.deposit.list', 'icon' => 'las la-file-invoice-dollar', 'category' => 'Payments', 'keywords' => ['all payments', 'all deposits']],
            ['title' => 'Support Ticket', 'route' => 'admin.ticket.index', 'icon' => 'las la-ticket', 'category' => 'Support', 'keywords' => ['ticket', 'support', 'support ticket', 'help']],
            ['title' => 'Pending Ticket', 'route' => 'admin.ticket.pending', 'icon' => 'las la-ticket', 'category' => 'Support', 'keywords' => ['pending ticket', 'new ticket']],
            ['title' => 'Closed Ticket', 'route' => 'admin.ticket.closed', 'icon' => 'las la-ticket', 'category' => 'Support', 'keywords' => ['closed ticket', 'resolved']],
            ['title' => 'Answered Ticket', 'route' => 'admin.ticket.answered', 'icon' => 'las la-ticket', 'category' => 'Support', 'keywords' => ['answered ticket', 'replied']],
            ['title' => 'All Ticket', 'route' => 'admin.ticket.index', 'icon' => 'las la-ticket', 'category' => 'Support', 'keywords' => ['all ticket', 'all tickets']],
            ['title' => 'Auto AI', 'route' => 'admin.autoai.index', 'icon' => 'las la-robot', 'category' => 'Support', 'keywords' => ['auto ai', 'autoai', 'auto response', 'bot']],
            ['title' => 'Report', 'route' => 'admin.report.transaction', 'icon' => 'las la-list', 'category' => 'Reports', 'keywords' => ['report', 'reports', 'analytics']],
            ['title' => 'Transaction Log', 'route' => 'admin.report.transaction', 'icon' => 'las la-list', 'category' => 'Reports', 'keywords' => ['transaction', 'transaction log', 'transactions']],
            ['title' => 'Login History', 'route' => 'admin.report.login.history', 'icon' => 'las la-list', 'category' => 'Reports', 'keywords' => ['login history', 'login log', 'user login']],
            ['title' => 'Notification History', 'route' => 'admin.report.notification.history', 'icon' => 'las la-list', 'category' => 'Reports', 'keywords' => ['notification history', 'notification log']],
            ['title' => 'User Search Analytics', 'route' => 'admin.report.search.analytics', 'icon' => 'las la-search', 'category' => 'Reports', 'keywords' => ['search analytics', 'user search']],
            ['title' => 'Analytics Dashboard', 'route' => 'admin.report.activity.dashboard', 'icon' => 'las la-chart-line', 'category' => 'Reports', 'keywords' => ['analytics dashboard', 'activity dashboard']],
            ['title' => 'Activity Search', 'route' => 'admin.report.activity.search', 'icon' => 'las la-search', 'category' => 'Reports', 'keywords' => ['activity search', 'user activity']],
            ['title' => 'Product Views', 'route' => 'admin.report.activity.product_views', 'icon' => 'las la-eye', 'category' => 'Reports', 'keywords' => ['product views', 'views']],
            ['title' => 'Cart Activity', 'route' => 'admin.report.activity.cart', 'icon' => 'las la-shopping-cart', 'category' => 'Reports', 'keywords' => ['cart', 'cart activity']],
            ['title' => 'Wishlist Activity', 'route' => 'admin.report.activity.wishlist', 'icon' => 'las la-heart', 'category' => 'Reports', 'keywords' => ['wishlist', 'wishlist activity']],
            ['title' => 'Compare Activity', 'route' => 'admin.report.activity.compare', 'icon' => 'las la-balance-scale', 'category' => 'Reports', 'keywords' => ['compare', 'compare activity']],
            ['title' => 'Orders Activity', 'route' => 'admin.report.activity.orders', 'icon' => 'las la-list-alt', 'category' => 'Reports', 'keywords' => ['orders activity']],
            ['title' => 'Track Order Searches', 'route' => 'admin.report.activity.track_order', 'icon' => 'las la-search', 'category' => 'Reports', 'keywords' => ['track order', 'track order searches']],
            ['title' => 'Payments Activity', 'route' => 'admin.report.activity.payments', 'icon' => 'las la-file-invoice-dollar', 'category' => 'Reports', 'keywords' => ['payments activity']],
            ['title' => 'Login Activity', 'route' => 'admin.report.activity.login', 'icon' => 'las la-sign-in-alt', 'category' => 'Reports', 'keywords' => ['login activity']],
            ['title' => 'Registration Activity', 'route' => 'admin.report.activity.registration', 'icon' => 'las la-user-plus', 'category' => 'Reports', 'keywords' => ['registration activity']],
            ['title' => 'Messages Activity', 'route' => 'admin.report.activity.messages', 'icon' => 'las la-envelope', 'category' => 'Reports', 'keywords' => ['messages activity']],
            ['title' => 'Location Activity', 'route' => 'admin.report.activity.location', 'icon' => 'las la-map-marker-alt', 'category' => 'Reports', 'keywords' => ['location activity']],
            ['title' => 'All Activity', 'route' => 'admin.report.activity.all', 'icon' => 'las la-list', 'category' => 'Reports', 'keywords' => ['all activity']],
            ['title' => 'Live Monitor', 'route' => 'admin.report.activity.live', 'icon' => 'las la-broadcast-tower', 'category' => 'Reports', 'keywords' => ['live monitor', 'live']],
            ['title' => 'Suspicious Activity', 'route' => 'admin.report.activity.suspicious', 'icon' => 'las la-exclamation-triangle', 'category' => 'Reports', 'keywords' => ['suspicious', 'suspicious activity']],
            ['title' => 'General Setting', 'route' => 'admin.setting.index', 'icon' => 'las la-life-ring', 'category' => 'Settings', 'keywords' => ['general setting', 'general settings', 'site setting', 'basic setting', 'seting', 'setings']],
            ['title' => 'Admin Management', 'route' => 'admin.setting.admin.index', 'icon' => 'las la-user-shield', 'category' => 'Settings', 'keywords' => ['admin management', 'admin', 'admins', 'appoint admin', 'admin recruitment', 'admin control']],
            ['title' => 'System Configuration', 'route' => 'admin.setting.system.configuration', 'icon' => 'las la-cog', 'category' => 'Settings', 'keywords' => ['system configuration', 'system config', 'configuration']],
            ['title' => 'Logo & Favicon', 'route' => 'admin.frontend.sections.icon', 'icon' => 'las la-images', 'category' => 'Settings', 'keywords' => ['logo', 'favicon', 'icon', 'site logo', 'brand logo']],
            ['title' => 'Extensions', 'route' => 'admin.extensions.index', 'icon' => 'las la-cogs', 'category' => 'Settings', 'keywords' => ['extension', 'extensions', 'plugin', 'plugins', 'addon']],
            ['title' => 'Language', 'route' => 'admin.language.manage', 'icon' => 'las la-language', 'category' => 'Settings', 'keywords' => ['language', 'languages', 'translation', 'locale']],
            ['title' => 'SEO Manager', 'route' => 'admin.seo', 'icon' => 'las la-globe', 'category' => 'Settings', 'keywords' => ['seo', 'search engine', 'meta', 'seo manager']],
            ['title' => 'Notification Setting', 'route' => 'admin.setting.notification.global', 'icon' => 'las la-bell', 'category' => 'Settings', 'keywords' => ['notification setting', 'notification', 'notifications']],
            ['title' => 'Global Template', 'route' => 'admin.setting.notification.global', 'icon' => 'las la-bell', 'category' => 'Settings', 'keywords' => ['global template', 'notification template']],
            ['title' => 'Email Setting', 'route' => 'admin.setting.notification.email', 'icon' => 'las la-bell', 'category' => 'Settings', 'keywords' => ['email setting', 'email configuration', 'smtp']],
            ['title' => 'SMS Setting', 'route' => 'admin.setting.notification.sms', 'icon' => 'las la-bell', 'category' => 'Settings', 'keywords' => ['sms setting', 'sms configuration', 'sms gateway']],
            ['title' => 'Notification Templates', 'route' => 'admin.setting.notification.templates', 'icon' => 'las la-bell', 'category' => 'Settings', 'keywords' => ['notification templates', 'email templates', 'sms templates']],
            ['title' => 'Manage Templates', 'route' => 'admin.frontend.templates', 'icon' => 'las la-html5', 'category' => 'Frontend', 'keywords' => ['template', 'templates', 'theme', 'frontend template']],
            ['title' => 'Manage Section', 'route' => 'admin.frontend.sections.general', 'icon' => 'las la-puzzle-piece', 'category' => 'Frontend', 'keywords' => ['section', 'sections', 'frontend section', 'page section']],
            ['title' => 'Templates', 'route' => 'admin.frontend.templates', 'icon' => 'las la-dot-circle', 'category' => 'Frontend', 'keywords' => ['templates']],
            ['title' => 'Locations', 'route' => 'admin.locations.index', 'icon' => 'las la-map-marked-alt', 'category' => 'Frontend', 'keywords' => ['locations', 'division', 'district', 'thana']],
            ['title' => 'District (Checkout)', 'route' => 'admin.frontend.sections.district', 'icon' => 'las la-map-marker-alt', 'category' => 'Frontend', 'keywords' => ['district', 'checkout district']],
            ['title' => 'General', 'route' => 'admin.frontend.sections.general', 'icon' => 'las la-dot-circle', 'category' => 'Frontend', 'keywords' => ['general section']],
            ['title' => 'Homepage Sections', 'route' => 'admin.frontend.sections.homepage', 'icon' => 'las la-home', 'category' => 'Frontend', 'keywords' => ['homepage', 'homepage sections']],
            ['title' => 'Contact Channels', 'route' => 'admin.contact.channels.index', 'icon' => 'las la-headset', 'category' => 'Frontend', 'keywords' => ['contact channels', 'contact']],
            ['title' => 'Maintenance Mode', 'route' => 'admin.maintenance.mode', 'icon' => 'las la-robot', 'category' => 'Settings', 'keywords' => ['maintenance', 'maintenance mode', 'down', 'offline']],
            ['title' => 'Maintenance Dashboard', 'route' => 'admin.maintenance.dashboard', 'icon' => 'las la-tools', 'category' => 'System', 'keywords' => ['maintenance dashboard']],
            ['title' => 'GDPR Cookie', 'route' => 'admin.setting.cookie', 'icon' => 'las la-cookie-bite', 'category' => 'Settings', 'keywords' => ['cookie', 'gdpr', 'privacy', 'cookie policy']],
            ['title' => 'Custom CSS', 'route' => 'admin.setting.custom.css', 'icon' => 'lab la-css3-alt', 'category' => 'Settings', 'keywords' => ['custom css', 'css', 'styling', 'custom style']],
            ['title' => 'Social Logins', 'route' => 'admin.setting.social.login', 'icon' => 'lab la-google', 'category' => 'Settings', 'keywords' => ['social login', 'social auth', 'google login', 'facebook login']],
            ['title' => 'Report & Request', 'route' => 'admin.request.report', 'icon' => 'las la-bug', 'category' => 'System', 'keywords' => ['report request', 'bug report', 'request', 'feedback']],
            ['title' => 'System', 'route' => 'admin.system.info', 'icon' => 'las la-server', 'category' => 'System', 'keywords' => ['system', 'system info']],
            ['title' => 'Application', 'route' => 'admin.system.info', 'icon' => 'las la-server', 'category' => 'System', 'keywords' => ['application', 'app info', 'system application']],
            ['title' => 'Server', 'route' => 'admin.system.server.info', 'icon' => 'las la-server', 'category' => 'System', 'keywords' => ['server', 'server info', 'server information']],
            ['title' => 'Cache', 'route' => 'admin.system.optimize', 'icon' => 'las la-hdd', 'category' => 'System', 'keywords' => ['cache', 'clear cache', 'optimize']],
            ['title' => 'Clear Cache', 'route' => 'admin.system.optimize', 'icon' => 'las la-hdd', 'category' => 'System', 'keywords' => ['clear cache', 'cache clear', 'optimize']],
            ['title' => 'Profile', 'route' => 'admin.profile', 'icon' => 'las la-user-circle', 'category' => 'Account', 'keywords' => ['profile', 'admin profile', 'my profile']],
            ['title' => 'Password', 'route' => 'admin.password', 'icon' => 'las la-key', 'category' => 'Account', 'keywords' => ['password', 'change password', 'password change']],
            ['title' => 'Notifications', 'route' => 'admin.notifications', 'icon' => 'las la-bell', 'category' => 'Account', 'keywords' => ['notifications', 'admin notifications']],
        ];
    }

    /**
     * Score for fuzzy/tolerant matching: 0 = no match, 1 = fuzzy, 2 = keyword/title contains, 3 = exact/substring.
     */
    private function menuMatchScore(string $searchTerm, string $title, string $category, array $keywords): int
    {
        $q = $searchTerm;
        $t = $title;
        $c = $category;
        if (strpos($t, $q) !== false || strpos($c, $q) !== false) {
            return 3;
        }
        foreach ($keywords as $kw) {
            if (strpos($kw, $q) !== false || strpos($q, $kw) !== false) {
                return 2;
            }
        }
        // Fuzzy: single char
        if (strlen($q) === 1) {
            if (strpos($t, $q) !== false || strpos($c, $q) !== false) {
                return 3;
            }
            foreach ($keywords as $kw) {
                if (strpos($kw, $q) !== false) {
                    return 2;
                }
            }
        }
        // Fuzzy: word tokens - any query word contained in title/keywords
        $words = preg_split('/\s+/', trim($q), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($words as $w) {
            if (strlen($w) < 2) {
                continue;
            }
            if (strpos($t, $w) !== false) {
                return 2;
            }
            foreach ($keywords as $kw) {
                if (strpos($kw, $w) !== false) {
                    return 2;
                }
            }
        }
        // Fuzzy: typo tolerance (one char diff for short words)
        if (strlen($q) >= 3 && strlen($q) <= 10) {
            foreach (array_merge([$t], $keywords) as $target) {
                $tw = preg_split('/\s+/', $target, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($tw as $tword) {
                    if (strlen($tword) >= 2 && strlen($q) <= strlen($tword) + 1 && @levenshtein($q, $tword) <= 2) {
                        return 1;
                    }
                }
            }
        }
        return 0;
    }

    private function searchMenuItems($searchTerm)
    {
        $menuItems = [];
        $menus = self::getAdminMenuIndex();

        foreach ($menus as $menu) {
            $title = strtolower($menu['title']);
            $category = strtolower($menu['category']);
            $keywords = isset($menu['keywords']) ? array_map('strtolower', $menu['keywords']) : [];

            $score = $this->menuMatchScore($searchTerm, $title, $category, $keywords);
            if ($score < 1) {
                continue;
            }

            try {
                $url = isset($menu['route_param'])
                    ? route($menu['route'], $menu['route_param'])
                    : route($menu['route']);
                if (!empty($menu['route_query']) && is_array($menu['route_query'])) {
                    $url .= '?' . http_build_query($menu['route_query']);
                }
            } catch (\Throwable $e) {
                continue;
            }

            $menuItems[] = [
                'type' => 'menu',
                'title' => $menu['title'],
                'description' => $menu['category'],
                'url' => $url,
                'icon' => $menu['icon'],
                'category' => $menu['category'],
                '_score' => $score,
            ];
        }

        // Sort by score desc, then limit to keep response lightweight
        usort($menuItems, function ($a, $b) {
            return ($b['_score'] ?? 0) <=> ($a['_score'] ?? 0);
        });
        $menuItems = array_slice($menuItems, 0, 50);
        foreach ($menuItems as &$item) {
            unset($item['_score']);
        }

        return $menuItems;
    }

    private function searchFrontendSections($searchTerm)
    {
        $sections = [];
        
        try {
            $pageSections = getPageSections(true);
            if ($pageSections) {
                foreach ($pageSections as $key => $sec) {
                    if (isset($sec['builder']) && $sec['builder']) {
                        $name = strtolower($sec['name'] ?? '');
                        $keyLower = strtolower($key);
                        
                        $match = false;
                        
                        // For single character search, check if character exists anywhere
                        if (strlen($searchTerm) === 1) {
                            if (strpos($name, $searchTerm) !== false || strpos($keyLower, $searchTerm) !== false) {
                                $match = true;
                            }
                        } else {
                            // For multi-character search, use substring matching
                            if (strpos($name, $searchTerm) !== false || strpos($keyLower, $searchTerm) !== false) {
                                $match = true;
                            }
                        }
                        
                        if ($match) {
                            $routeMapping = [
                                'banner' => 'admin.frontend.sections.banner',
                                'contact_us' => 'admin.frontend.sections.contact',
                                'footer' => 'admin.frontend.sections.footer',
                                'header_icons' => 'admin.frontend.sections.headericons',
                                'login' => 'admin.frontend.sections.login',
                                'policy_pages' => 'admin.frontend.sections.policy',
                                'register' => 'admin.frontend.sections.register',
                                'service' => 'admin.frontend.sections.service',
                                'social_icon' => 'admin.frontend.sections.social_icon',
                                'scrollbar' => 'admin.frontend.sections.scrollbar',
                                'ticker' => 'admin.frontend.sections.ticker',
                            ];
                            $homeLayoutKeys = [];
                            try {
                                if (isset($routeMapping[$key])) {
                                    $url = route($routeMapping[$key]);
                                } elseif (Route::has('admin.frontend.sections')) {
                                    $url = route('admin.frontend.sections', ['key' => $key]);
                                } else {
                                    $url = route('admin.frontend.sections.general');
                                }
                                $sections[] = [
                                    'type' => 'frontend',
                                    'title' => $sec['name'] ?? ucfirst($key),
                                    'description' => 'Frontend Section - ' . ucfirst($key),
                                    'url' => $url,
                                    'icon' => 'las la-puzzle-piece',
                                    'category' => in_array($key, $homeLayoutKeys, true) ? 'Home Layout' : 'Sections',
                                ];
                            } catch (\Exception $e) {
                                // Skip section if route fails
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fail if getPageSections doesn't work
        }
        
        return $sections;
    }

    private function searchSettings($searchTerm)
    {
        $settings = [];
        
        $settingKeywords = [
            'banner' => ['title' => 'Banner Upload & Management', 'description' => 'Manage hero banners (Sections)', 'url' => route('admin.frontend.sections.banner'), 'icon' => 'las la-image'],
            'logo' => ['title' => 'Logo Upload', 'description' => 'Upload and manage site logo', 'url' => route('admin.frontend.sections.icon'), 'icon' => 'las la-images'],
            'favicon' => ['title' => 'Favicon Upload', 'description' => 'Upload and manage favicon', 'url' => route('admin.frontend.sections.icon'), 'icon' => 'las la-images'],
            'login section' => ['title' => 'Login Section', 'description' => 'Manage login page section', 'url' => route('admin.frontend.sections.login'), 'icon' => 'las la-sign-in-alt'],
            'register section' => ['title' => 'Register Section', 'description' => 'Manage registration page section', 'url' => route('admin.frontend.sections.register'), 'icon' => 'las la-user-plus'],
            'service section' => ['title' => 'Service Section', 'description' => 'Manage service section', 'url' => route('admin.frontend.sections.service'), 'icon' => 'las la-concierge-bell'],
            'policy pages' => ['title' => 'Policy Pages', 'description' => 'Manage policy pages', 'url' => route('admin.frontend.sections.policy'), 'icon' => 'las la-file-alt'],
            'social icon' => ['title' => 'Social Icons', 'description' => 'Manage social media icons', 'url' => route('admin.frontend.sections.social_icon'), 'icon' => 'las la-share-alt'],
            'footer' => ['title' => 'Footer Section', 'description' => 'Manage footer section', 'url' => route('admin.frontend.sections.footer'), 'icon' => 'las la-window-minimize'],
            'scrollbar' => ['title' => 'Scroll Bar', 'description' => 'Scroll bars below header, banner or above footer (Sections)', 'url' => route('admin.frontend.sections.scrollbar'), 'icon' => 'las la-arrows-alt-h'],
            'ticker' => ['title' => 'News Ticker', 'description' => 'Homepage news ticker (Sections)', 'url' => route('admin.frontend.sections.ticker'), 'icon' => 'las la-bullhorn'],
            'quick order' => ['title' => 'Quick Order Page', 'description' => 'Quick order landing page (Orders)', 'url' => route('admin.frontend.quickorder'), 'icon' => 'las la-shipping-fast'],
        ];

        foreach ($settingKeywords as $keyword => $data) {
            $keywordLower = strtolower($keyword);
            $match = false;
            
            // For single character search, check if character exists in keyword
            if (strlen($searchTerm) === 1) {
                if (strpos($keywordLower, $searchTerm) !== false) {
                    $match = true;
                }
                // Also check in title and description
                if (!$match) {
                    $titleLower = strtolower($data['title']);
                    $descLower = strtolower($data['description']);
                    if (strpos($titleLower, $searchTerm) !== false || strpos($descLower, $searchTerm) !== false) {
                        $match = true;
                    }
                }
            } else {
                // For multi-character search
                if (strpos($keywordLower, $searchTerm) !== false || strpos($searchTerm, $keywordLower) !== false) {
                    $match = true;
                }
                // Also check in title and description
                if (!$match) {
                    $titleLower = strtolower($data['title']);
                    $descLower = strtolower($data['description']);
                    if (strpos($titleLower, $searchTerm) !== false || strpos($descLower, $searchTerm) !== false) {
                        $match = true;
                    }
                }
            }
            
            if ($match) {
                $settings[] = [
                    'type' => 'setting',
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'url' => $data['url'],
                    'icon' => $data['icon'],
                    'category' => 'Frontend'
                ];
            }
        }

        return $settings;
    }
}
