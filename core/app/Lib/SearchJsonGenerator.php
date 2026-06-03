<?php

namespace App\Lib;

class SearchJsonGenerator
{
    public static function generate($prefix = null)
    {
        if (!$prefix) {
            $prefix = config('admin.prefix', 'admin');
        }
        
        $baseUrl = url($prefix);

        $schema = [
            "navigation" => [
                "Dashboards" => [
                    [ "name" => "Analytics", "url" => $baseUrl . "/dashboard", "icon" => "bx bx-pie-chart-alt" ],
                    [ "name" => "Security", "url" => $baseUrl . "/security", "icon" => "bx bx-shield-alt" ]
                ],
                "Home Layout" => [
                    [ "name" => "Homepage Sections", "url" => $baseUrl . "/frontend/homepage-sections", "icon" => "las la-th-large" ],
                    [ "name" => "Custom Rows", "url" => $baseUrl . "/frontend/homepage-custom-rows", "icon" => "las la-stream" ],
                    [ "name" => "Homepage Ads", "url" => $baseUrl . "/frontend/homepage-ads", "icon" => "las la-ad" ],
                ],
                "Sections" => [
                    [ "name" => "Header Control", "url" => $baseUrl . "/frontend/header", "icon" => "las la-window-maximize" ],
                    [ "name" => "Header Icons", "url" => $baseUrl . "/frontend/headericons", "icon" => "las la-icons" ],
                    [ "name" => "Logo & Favicon", "url" => $baseUrl . "/frontend/icon", "icon" => "las la-image" ],
                    [ "name" => "Banner Management", "url" => $baseUrl . "/frontend/banner", "icon" => "las la-image" ],
                    [ "name" => "Scroll Bar", "url" => $baseUrl . "/frontend/scrollbar", "icon" => "las la-arrows-alt-h" ],
                    [ "name" => "News Ticker", "url" => $baseUrl . "/frontend/ticker", "icon" => "las la-bullhorn" ],
                    [ "name" => "Social Icons", "url" => $baseUrl . "/frontend/social_icon", "icon" => "las la-share-alt" ],
                    [ "name" => "Contact Us", "url" => $baseUrl . "/frontend/contact", "icon" => "las la-envelope" ],
                    [ "name" => "Footer Builder", "url" => $baseUrl . "/frontend/footer", "icon" => "las la-sitemap" ],
                ],
                "Customers" => [
                    [ "name" => "Active", "url" => $baseUrl . "/users/active", "icon" => "las la-user-check" ],
                    [ "name" => "Banned", "url" => $baseUrl . "/users/banned", "icon" => "las la-user-slash" ],
                    [ "name" => "Email Unverified", "url" => $baseUrl . "/users/email-unverified", "icon" => "las la-envelope" ],
                    [ "name" => "Mobile Unverified", "url" => $baseUrl . "/users/mobile-unverified", "icon" => "las la-mobile-alt" ],
                    [ "name" => "All Customers", "url" => $baseUrl . "/users", "icon" => "las la-users" ],
                    [ "name" => "Notification to All", "url" => $baseUrl . "/users/send-notification", "icon" => "las la-bell" ]
                ],
                "Catalog" => [
                    [ "name" => "All Products", "url" => $baseUrl . "/product", "icon" => "las la-boxes" ],
                    [ "name" => "Low Stock", "url" => $baseUrl . "/product?low_stock=1", "icon" => "las la-exclamation-triangle" ],
                    [ "name" => "Stock Alerts", "url" => $baseUrl . "/product/stock-alerts", "icon" => "las la-bell" ],
                    [ "name" => "Quick Deals", "url" => $baseUrl . "/product/today-deal", "icon" => "las la-bolt" ],
                    [ "name" => "Hot Deals", "url" => $baseUrl . "/product/hot", "icon" => "las la-fire" ],
                    [ "name" => "Trending", "url" => $baseUrl . "/product/trending", "icon" => "las la-chart-line" ],
                    [ "name" => "Best Selling", "url" => $baseUrl . "/product/best-selling", "icon" => "las la-star" ],
                    [ "name" => "Featured Products", "url" => $baseUrl . "/product/featured", "icon" => "las la-certificate" ],
                ],
                "Products" => [
                    [ "name" => "Product Center", "url" => $baseUrl . "/product-center", "icon" => "las la-th-large" ],
                    [ "name" => "Add Product", "url" => $baseUrl . "/product/create", "icon" => "las la-plus-circle" ],
                    [ "name" => "Quick Upload", "url" => $baseUrl . "/product/general-create", "icon" => "las la-magic" ],
                    [ "name" => "Product Reviews", "url" => $baseUrl . "/product/reviews", "icon" => "las la-comment-dots" ],
                    [ "name" => "Product Performance", "url" => $baseUrl . "/report/product", "icon" => "las la-chart-bar" ],
                ],
                "Categories" => [
                    [ "name" => "Category Center", "url" => $baseUrl . "/category-center", "icon" => "las la-th-large" ],
                    [ "name" => "Categories", "url" => $baseUrl . "/category", "icon" => "las la-align-left" ],
                    [ "name" => "Subcategories", "url" => $baseUrl . "/sub-category", "icon" => "las la-align-center" ],
                    [ "name" => "Brands", "url" => $baseUrl . "/brand", "icon" => "las la-tags" ],
                    [ "name" => "Coupons", "url" => $baseUrl . "/coupon", "icon" => "las la-bullhorn" ],
                ],
                "Home Ads" => [
                    [ "name" => "Homepage Ads", "url" => $baseUrl . "/frontend/homepage-ads", "icon" => "las la-ad" ],
                    [ "name" => "Popup Ads", "url" => $baseUrl . "/popup-ads", "icon" => "las la-window-maximize" ],
                    [ "name" => "Offer Timers", "url" => $baseUrl . "/offer-timers", "icon" => "las la-stopwatch" ],
                    [ "name" => "Header Icons", "url" => $baseUrl . "/frontend/headericons", "icon" => "las la-icons" ],
                    [ "name" => "Banners", "url" => $baseUrl . "/frontend/banner", "icon" => "las la-image" ],
                ],
                "Orders" => [
                    [ "name" => "All Orders", "url" => $baseUrl . "/order", "icon" => "las la-list" ],
                    [ "name" => "Pending Orders", "url" => $baseUrl . "/order/pending", "icon" => "las la-clock" ],
                    [ "name" => "Canceled Orders", "url" => $baseUrl . "/order/cancel", "icon" => "las la-times-circle" ],
                    [ "name" => "Abandoned Carts", "url" => $baseUrl . "/abandoned-orders", "icon" => "las la-shopping-cart" ],
                    [ "name" => "Quick Order Page", "url" => $baseUrl . "/frontend/quickorder", "icon" => "las la-shipping-fast" ],
                ],
                "Shipping" => [
                    [ "name" => "Shipping Hub", "url" => $baseUrl . "/shipping-method", "icon" => "las la-truck-moving" ],
                    [ "name" => "Courier Settings", "url" => $baseUrl . "/api-integration/courier", "icon" => "las la-cog" ],
                    [ "name" => "Courier Logs", "url" => $baseUrl . "/api-integration/courier/logs", "icon" => "las la-list-alt" ]
                ],
                "Finance" => [
                    [ "name" => "Gateways Hub", "url" => $baseUrl . "/payment-gateways", "icon" => "las la-th-large" ],
                    [ "name" => "Automatic", "url" => $baseUrl . "/gateway/automatic", "icon" => "las la-robot" ],
                    [ "name" => "Manual", "url" => $baseUrl . "/gateway/manual", "icon" => "las la-hand-holding-usd" ]
                ],
                "files" => [],
                "members" => []
            ],
            "suggestions" => [
                "Popular Searches" => [
                    [ "name" => "Analytics", "url" => $baseUrl . "/dashboard", "icon" => "bx bx-pie-chart-alt-2" ],
                    [ "name" => "Users", "url" => $baseUrl . "/users", "icon" => "bx bx-group" ],
                    [ "name" => "Products", "url" => $baseUrl . "/product", "icon" => "bx bx-cart-alt" ]
                ]
            ]
        ];

        $json = json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        $assetPath = base_path('../assets/json/');
        file_put_contents($assetPath . 'search-horizontal.json', $json);
        file_put_contents($assetPath . 'search-vertical.json', $json);
    }
}
