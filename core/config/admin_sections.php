<?php

/*
|--------------------------------------------------------------------------
| Admin Sections – Auto-Merged
|--------------------------------------------------------------------------
| Base sections + registered sections (from admin_sections_registered.php).
| New features: add to config('admin_sections_registered.sections') and they
| appear automatically in Admin Management for section assignment.
*/
$baseSections = [
        'dashboard'           => 'Dashboard',
        'users'               => 'Manage Customer',
        'orders'              => 'Manage Orders',
        'category'            => 'Manage Categories',
        'subcategory'         => 'Manage Subcategories',
        'brand'               => 'Manage Brands',
        'products'            => 'Manage Products',
        'coupon'              => 'Coupon',
        'shipping'             => 'Shipping Method',
        'courier_api'         => 'Courier API',
        'gateways'             => 'Payment Gateways',
        'deposit'             => 'Payments',
        'messages'             => 'Support Ticket',
        'reports'              => 'Report',
        'subscribers'          => 'Subscribers',
        'admin_management'     => 'Admin Management',
        'system_config'        => 'System Configuration',
        'extensions'           => 'Extensions',
        'language'             => 'Language',
        'seo'                  => 'SEO Manager',
        'notification_setting' => 'Notification Setting',
        'frontend_templates'   => 'Manage Templates',
        'frontend_sections'    => 'Manage Section',
        'contact_channels'     => 'Contact Channels',
        'maintenance'          => 'Maintenance Mode',
        'cookie'               => 'GDPR Cookie',
        'system'               => 'System',
        'custom_css'           => 'Custom CSS',
        'social_login'         => 'Social Logins',
        'request_report'       => 'Report & Request',
        'security'             => 'Security Dashboard',
        'maintenance_dashboard'=> 'Maintenance Dashboard',
    ];

$registered = config('admin_sections_registered.sections', []);
$sections = array_merge($baseSections, is_array($registered) ? $registered : []);

return [
    'sections' => $sections,

    /*
    |--------------------------------------------------------------------------
    | Route name patterns mapped to section key (for middleware / sidebar)
    |--------------------------------------------------------------------------
    */
    'route_to_section' => [
        'admin.dashboard' => 'dashboard',
        'admin.users' => 'users',
        'admin.order' => 'orders',
        'admin.orders' => 'orders',
        'admin.category' => 'category',
        'admin.subcategory' => 'subcategory',
        'admin.brand' => 'brand',
        'admin.product' => 'products',
        'admin.attributes' => 'products',
        'admin.category.attributes' => 'products',
        'admin.coupon' => 'coupon',
        'admin.shipping' => 'shipping',
        'admin.api.courier' => 'courier_api',
        'admin.orders.bulk.courier' => 'courier_api',
        'admin.gateway' => 'gateways',
        'admin.deposit' => 'deposit',
        'admin.ticket' => 'messages',
        'admin.autoai' => 'messages',
        'admin.report' => 'reports',
        'admin.subscriber' => 'subscribers',
        'admin.setting.admin' => 'admin_management',
        'admin.setting.system.configuration' => 'system_config',
        'admin.extensions' => 'extensions',
        'admin.language' => 'language',
        'admin.seo' => 'seo',
        'admin.setting.notification' => 'notification_setting',
        'admin.frontend.templates' => 'frontend_templates',
        'admin.frontend.sections' => 'frontend_sections',
        'admin.frontend.quickorder' => 'frontend_sections',
        'admin.contact.channels' => 'contact_channels',
        'admin.maintenance' => 'maintenance',
        'admin.setting.cookie' => 'cookie',
        'admin.system' => 'system',
        'admin.setting.custom.css' => 'custom_css',
        'admin.setting.social.login' => 'social_login',
        'admin.request.report' => 'request_report',
        'admin.security.dashboard' => 'security',
        'admin.maintenance.dashboard' => 'maintenance_dashboard',
    ],

    /*
    |--------------------------------------------------------------------------
    | Section key => primary route (for "Go to feature" links in Admin Management)
    |--------------------------------------------------------------------------
    | Add new feature routes here so admins can open that section directly.
    */
    'section_routes' => [
        'dashboard' => 'admin.dashboard',
        'users' => 'admin.users.all',
        'orders' => 'admin.orders.index',
        'category' => 'admin.category.index',
        'subcategory' => 'admin.subcategory.index',
        'brand' => 'admin.brand.index',
        'products' => 'admin.product.index',
        'coupon' => 'admin.coupon.index',
        'shipping' => 'admin.shipping.index',
        'courier_api' => 'admin.api.courier.manage',
        'gateways' => 'admin.gateway.automatic.index',
        'deposit' => 'admin.deposit.list',
        'messages' => 'admin.ticket.index',
        'reports' => 'admin.report.transaction',
        'subscribers' => 'admin.subscriber.index',
        'admin_management' => 'admin.setting.admin.index',
        'system_config' => 'admin.setting.system.configuration',
        'extensions' => 'admin.extensions.index',
        'language' => 'admin.language.manage',
        'seo' => 'admin.seo',
        'notification_setting' => 'admin.setting.notification.global',
        'frontend_templates' => 'admin.frontend.templates',
        'frontend_sections' => 'admin.frontend.sections.general',
        'contact_channels' => 'admin.contact.channels.index',
        'maintenance' => 'admin.maintenance.mode',
        'cookie' => 'admin.setting.cookie',
        'system' => 'admin.system.info',
        'custom_css' => 'admin.setting.custom.css',
        'social_login' => 'admin.setting.social.login',
        'request_report' => 'admin.request.report',
        'security' => 'admin.security.dashboard',
        'maintenance_dashboard' => 'admin.maintenance.dashboard',
    ],
];
