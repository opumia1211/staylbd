@php
    $adminUser = auth()->guard('admin')->user();
    $canAccess = function($section) use ($adminUser) {
        return !$adminUser || $adminUser->canAccessSection($section);
    };
@endphp
<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('admin.dashboard') }}" class="sidebar__main-logo" title="@lang('Dashboard')">
                @php $adminLogo = getLogo('logo_dark') ?: getLogo('logo'); @endphp
                @if($adminLogo)
                    <img src="{{ $adminLogo }}" alt="{{ gs('site_name') }}" class="admin-sidebar-logo site-logo-img" style="{{ getLogoStyle() }}">
                @else
                    <div class="admin-sidebar-logo-text">{{ gs('site_name') ?: 'STYLE BD' }}</div>
                @endif
            </a>
        </div>

        <div class="px-3 pb-3">
            <form class="navbar-search w-100" id="sidebarSearchForm">
                <div class="position-relative">
                    <input type="search" 
                           class="navbar-search-field w-100" 
                           id="searchInput" 
                           autocomplete="off" 
                           placeholder="@lang('Search menu, products, users, settings...')"
                           aria-label="Search admin panel">
                    <i class="las la-search position-absolute admin-search-icon"></i>
                </div>
                <ul class="search-list d-none"></ul>
            </form>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                @if($canAccess('dashboard'))
                <li class="sidebar-menu-item {{ menuActive('admin.dashboard') }}">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link ">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang('Dashboard')</span>
                    </a>
                </li>
                @endif
                @if($adminUser && $adminUser->isOwner())
                <li class="sidebar-menu-item {{ menuActive('admin.security.dashboard') }}">
                    <a href="{{ route('admin.security.dashboard') }}" class="nav-link">
                        <i class="menu-icon las la-shield-alt"></i>
                        <span class="menu-title">@lang('Security')</span>
                    </a>
                </li>
                @endif
                @php
                    $homeLayoutMenuRoutes = [
                        'admin.frontend.sections.scrollbar*',
                        'admin.category.index',
                        'admin.product.todayDeal',
                        'admin.product.hot',
                        'admin.product.feature.index',
                        'admin.product.trending',
                        'admin.product.bestSelling',
                        'admin.frontend.sections.homepage',
                        'admin.frontend.sections.homepageCustomRows*',
                        'admin.frontend.sections.homepageAds*',
                    ];
                    $showHomeLayoutMenu = ($canAccess('products') || $canAccess('category') || $canAccess('frontend_sections'));
                @endphp
                @if($showHomeLayoutMenu)
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive($homeLayoutMenuRoutes, 3) }}">
                        <i class="menu-icon las la-th-large"></i>
                        <span class="menu-title">@lang('Home layout')</span>
                    </a>
                    <div class="sidebar-submenu sidebar-submenu--home-layout {{ menuActive($homeLayoutMenuRoutes, 2) }}">
                        <ul class="mb-0">
                            @if($canAccess('frontend_sections'))
                            <li class="sidebar-menu-item {{ menuActive('admin.frontend.sections.scrollbar*') }}">
                                <a href="{{ route('admin.frontend.sections.scrollbar') }}" class="nav-link py-2">
                                    <i class="menu-icon las la-arrows-alt-h"></i>
                                    <span class="menu-title">@lang('Scroll Bar')</span>
                                </a>
                            </li>
                            @endif
                            @if($canAccess('category'))
                            <li class="sidebar-menu-item {{ menuActive('admin.category.index') }}">
                                <a href="{{ route('admin.category.index') }}" class="nav-link py-2">
                                    <i class="menu-icon las la-border-all"></i>
                                    <span class="menu-title">@lang('Categories')</span>
                                </a>
                            </li>
                            @endif
                            @if($canAccess('products'))
                            <li class="sidebar-menu-item {{ menuActive('admin.product.todayDeal') }}">
                                <a href="{{ route('admin.product.todayDeal') }}" class="nav-link py-2">
                                    <i class="menu-icon las la-bolt"></i>
                                    <span class="menu-title">@lang('Quick deals')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.product.hot') }}">
                                <a href="{{ route('admin.product.hot') }}" class="nav-link py-2">
                                    <i class="menu-icon las la-fire"></i>
                                    <span class="menu-title">@lang('Hot deals')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.product.feature.index') }}">
                                <a href="{{ route('admin.product.feature.index') }}" class="nav-link py-2">
                                    <i class="menu-icon las la-star"></i>
                                    <span class="menu-title">@lang('Featured')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.product.trending') }}">
                                <a href="{{ route('admin.product.trending') }}" class="nav-link py-2">
                                    <i class="menu-icon las la-chart-line"></i>
                                    <span class="menu-title">@lang('Trending')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.product.bestSelling') }}">
                                <a href="{{ route('admin.product.bestSelling') }}" class="nav-link py-2">
                                    <i class="menu-icon las la-trophy"></i>
                                    <span class="menu-title">@lang('Best sellers')</span>
                                </a>
                            </li>
                            @endif
                            @if($canAccess('frontend_sections'))
                            <li class="sidebar-menu-item {{ menuActive('admin.frontend.sections.homepage') }}">
                                <a href="{{ route('admin.frontend.sections.homepage') }}" class="nav-link py-2">
                                    <i class="menu-icon las la-sliders-h"></i>
                                    <span class="menu-title">@lang('Homepage hub')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.frontend.sections.homepageCustomRows*') }}">
                                <a href="{{ route('admin.frontend.sections.homepageCustomRows') }}" class="nav-link py-2">
                                    <i class="menu-icon las la-stream"></i>
                                    <span class="menu-title">@lang('Layout & rows')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.frontend.sections.homepageAds*') }}">
                                <a href="{{ route('admin.frontend.sections.homepageAds') }}" class="nav-link py-2">
                                    <i class="menu-icon las la-ad"></i>
                                    <span class="menu-title">@lang('Homepage ads')</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif
                @if($canAccess('users'))
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('admin.users*', 3) }}">
                        <i class="menu-icon las la-users"></i>
                        <span class="menu-title">@lang('Manage Customer')</span>
                         @if($bannedUsersCount > 0 || $emailUnverifiedUsersCount > 0 || $mobileUnverifiedUsersCount > 0)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.users*', 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.users.active') }} ">
                                <a href="{{ route('admin.users.active') }}" class="nav-link">
                                    <i class="menu-icon las la-user-check"></i>
                                    <span class="menu-title">@lang('Active')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.users.banned') }} ">
                                <a href="{{ route('admin.users.banned') }}" class="nav-link">
                                    <i class="menu-icon las la-user-slash"></i>
                                    <span class="menu-title">@lang('Banned')</span>
                                    @if ($bannedUsersCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $bannedUsersCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item  {{ menuActive('admin.users.email.unverified') }}">
                                <a href="{{ route('admin.users.email.unverified') }}" class="nav-link">
                                    <i class="menu-icon las la-envelope"></i>
                                    <span class="menu-title">@lang('Email Unverified')</span>

                                    @if ($emailUnverifiedUsersCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $emailUnverifiedUsersCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.mobile.unverified') }}">
                                <a href="{{ route('admin.users.mobile.unverified') }}" class="nav-link">
                                    <i class="menu-icon las la-mobile-alt"></i>
                                    <span class="menu-title">@lang('Mobile Unverified')</span>
                                    @if ($mobileUnverifiedUsersCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $mobileUnverifiedUsersCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.all') }} ">
                                <a href="{{ route('admin.users.all') }}" class="nav-link">
                                    <i class="menu-icon las la-users"></i>
                                    <span class="menu-title">@lang('All')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.users.notification.all') }}">
                                <a href="{{ route('admin.users.notification.all') }}" class="nav-link">
                                    <i class="menu-icon las la-bell"></i>
                                    <span class="menu-title">@lang('Notification to All')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if($canAccess('orders'))
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive(['admin.order*', 'admin.abandoned-orders*', 'admin.notifications', 'admin.notifications.delivery.scan', 'admin.setting.stock.order.messages'], 3) }}">
                        <i class="menu-icon las  la-list-alt"></i>
                        <span class="menu-title">@lang('Manage Orders')</span>

                        @if ($pendingOrderCount > 0)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive(['admin.orders*', 'admin.abandoned-orders*', 'admin.notifications', 'admin.notifications.delivery.scan', 'admin.setting.stock.order.messages'], 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.orders.index') }} ">
                                <a href="{{ route('admin.orders.index') }}" class="nav-link">
                                    <i class="menu-icon las la-list"></i>
                                    <span class="menu-title">@lang('All Orders')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.orders.pending') }} ">
                                <a href="{{ route('admin.orders.pending') }}" class="nav-link">
                                    <i class="menu-icon las la-clock"></i>
                                    <span class="menu-title">@lang('Pending Orders')</span>
                                    @if ($pendingOrderCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $pendingOrderCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.orders.confirmed') }} ">
                                <a href="{{ route('admin.orders.confirmed') }}" class="nav-link">
                                    <i class="menu-icon las la-check-circle"></i>
                                    <span class="menu-title">@lang('Confirmed Orders')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.orders.shipped') }} ">
                                <a href="{{ route('admin.orders.shipped') }}" class="nav-link">
                                    <i class="menu-icon las la-truck"></i>
                                    <span class="menu-title">@lang('Shipped Orders')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.orders.delivered') }} ">
                                <a href="{{ route('admin.orders.delivered') }}" class="nav-link">
                                    <i class="menu-icon las la-box"></i>
                                    <span class="menu-title">@lang('Delivered Orders')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.orders.cancel') }} ">
                                <a href="{{ route('admin.orders.cancel') }}" class="nav-link">
                                    <i class="menu-icon las la-times-circle"></i>
                                    <span class="menu-title">@lang('Canceled Orders')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.abandoned-orders.index') }} ">
                                <a href="{{ route('admin.abandoned-orders.index') }}" class="nav-link">
                                    <i class="menu-icon las la-shopping-cart"></i>
                                    <span class="menu-title">@lang('Abandoned Carts')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.abandoned-orders.settings') }} ">
                                <a href="{{ route('admin.abandoned-orders.settings') }}" class="nav-link">
                                    <i class="menu-icon las la-cog"></i>
                                    <span class="menu-title">@lang('Abandoned Cart Settings')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.notifications.delivery.scan') }} ">
                                <a href="{{ route('admin.notifications.delivery.scan') }}" class="nav-link">
                                    <i class="menu-icon las la-qrcode"></i>
                                    <span class="menu-title">@lang('Delivery Scan Notifications')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.notifications') }} ">
                                <a href="{{ route('admin.notifications') }}" class="nav-link">
                                    <i class="menu-icon las la-bell"></i>
                                    <span class="menu-title">@lang('Notifications')</span>
                                    @if(isset($adminNotificationCount) && $adminNotificationCount > 0)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $adminNotificationCount > 99 ? '99+' : $adminNotificationCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.stock.order.messages') }} ">
                                <a href="{{ route('admin.setting.stock.order.messages') }}" class="nav-link">
                                    <i class="menu-icon las la-comment-dots"></i>
                                    <span class="menu-title">@lang('Stock & Order Messages')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if($canAccess('category'))
                <li class="sidebar-menu-item {{ menuActive('admin.category*') }}">
                    <a href="{{ route('admin.category.index') }}" class="nav-link ">
                        <i class="menu-icon las la-align-left"></i>
                        <span class="menu-title">@lang('Manage Categories')</span>
                    </a>
                </li>
                @endif
                @if($canAccess('subcategory'))
                <li class="sidebar-menu-item {{ menuActive('admin.subcategory*') }}">
                    <a href="{{ route('admin.subcategory.index') }}" class="nav-link ">
                        <i class="menu-icon las la-align-center"></i>
                        <span class="menu-title">@lang('Manage Subcategories')</span>
                    </a>
                </li>
                @endif
                @if($canAccess('brand'))
                <li class="sidebar-menu-item {{ menuActive('admin.brand*') }}">
                    <a href="{{ route('admin.brand.index') }}" class="nav-link ">
                        <i class="menu-icon las  la-tags "></i>
                        <span class="menu-title">@lang('Manage Brands')</span>
                    </a>
                </li>
                @endif
                @if($canAccess('products'))
                @php
                    $manageProductsRoutes = ['admin.product*', 'admin.attributes*', 'admin.category.attributes*', 'admin.offer-timers.*', 'admin.popup-ads.*'];
                @endphp
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive($manageProductsRoutes, 3) }}">
                        <i class="menu-icon las la-tshirt"></i>
                        <span class="menu-title">@lang('Manage Products')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive($manageProductsRoutes, 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.product.index') }}">
                                <a href="{{ route('admin.product.index') }}" class="nav-link">
                                    <i class="menu-icon las la-boxes"></i>
                                    <span class="menu-title">@lang('All Products')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.product.create') }}">
                                <a href="{{ route('admin.product.create') }}" class="nav-link">
                                    <i class="menu-icon las la-plus-circle"></i>
                                    <span class="menu-title">@lang('Add Product')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.product.reviews.index') }} {{ menuActive('admin.product.reviews') }}">
                                <a href="{{ route('admin.product.reviews.index') }}" class="nav-link">
                                    <i class="menu-icon las la-comment-dots"></i>
                                    <span class="menu-title">@lang('Product Reviews')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ (request()->routeIs('admin.product.index') && request('low_stock')) ? 'active' : '' }}">
                                <a href="{{ route('admin.product.index', ['low_stock' => 1]) }}" class="nav-link">
                                    <i class="menu-icon las la-exclamation-triangle"></i>
                                    <span class="menu-title">@lang('Low Stock')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.product.stock.alerts') }}">
                                <a href="{{ route('admin.product.stock.alerts') }}" class="nav-link">
                                    <i class="menu-icon las la-bell"></i>
                                    <span class="menu-title">@lang('Stock Alerts')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item small text-muted px-3 py-1">
                                <span class="menu-title" style="font-size: 0.7rem;">@lang('Spotlights:') <strong>@lang('Layout & rows')</strong></span>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.product.topbar.*') }}">
                                <a href="{{ route('admin.product.topbar.index') }}" class="nav-link">
                                    <i class="menu-icon las la-cubes"></i>
                                    <span class="menu-title">@lang('Top Feature Boxes')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.offer-timers.*') }}">
                                <a href="{{ route('admin.offer-timers.index') }}" class="nav-link">
                                    <i class="menu-icon las la-stopwatch"></i>
                                    <span class="menu-title">@lang('Offer Timers')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.popup-ads.*') }}">
                                <a href="{{ route('admin.popup-ads.index') }}" class="nav-link">
                                    <i class="menu-icon las la-window-maximize"></i>
                                    <span class="menu-title">@lang('Popup Ads')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.attributes.*') }}">
                                <a href="{{ route('admin.attributes.index') }}" class="nav-link">
                                    <i class="menu-icon las la-sliders-h"></i>
                                    <span class="menu-title">@lang('Product Attributes')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.category.attributes.*') }}">
                                <a href="{{ route('admin.category.attributes.index') }}" class="nav-link">
                                    <i class="menu-icon las la-tags"></i>
                                    <span class="menu-title">@lang('Category Attributes')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if($canAccess('coupon'))
                <li class="sidebar-menu-item {{ menuActive('admin.coupon*') }}">
                    <a href="{{ route('admin.coupon.index') }}" class="nav-link ">
                        <i class="menu-icon las la-bullhorn"></i>
                        <span class="menu-title">@lang('Coupon')</span>
                    </a>
                </li>
                @endif
                @if($canAccess('shipping'))
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('admin.shipping*', 3) }}">
                        <i class="menu-icon las la-truck-moving"></i>
                        <span class="menu-title">@lang('Shipping')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.shipping*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.shipping.index') }}">
                                <a href="{{ route('admin.shipping.index') }}" class="nav-link">
                                    <i class="menu-icon las la-th-large"></i>
                                    <span class="menu-title">@lang('Hub')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.shipping.zones*') }}">
                                <a href="{{ route('admin.shipping.zones.index') }}" class="nav-link">
                                    <i class="menu-icon las la-map-marked-alt"></i>
                                    <span class="menu-title">@lang('Zones')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.shipping.methods*') }}">
                                <a href="{{ route('admin.shipping.methods.index') }}" class="nav-link">
                                    <i class="menu-icon las la-shipping-fast"></i>
                                    <span class="menu-title">@lang('Methods')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.shipping.rules*') }}">
                                <a href="{{ route('admin.shipping.rules.index') }}" class="nav-link">
                                    <i class="menu-icon las la-cog"></i>
                                    <span class="menu-title">@lang('Rules')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if($canAccess('courier_api'))
                @php
                    $courierApiRoutes = ['admin.api*', 'admin.orders.bulk.courier'];
                @endphp
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive($courierApiRoutes, 3) }}">
                        <i class="menu-icon las la-truck"></i>
                        <span class="menu-title">@lang('Courier API')</span>
                        @php
                            try {
                                $failedCourierOrders = \DB::table('courier_logs')->where('status', 'failed')->count();
                            } catch (\Exception $e) {
                                $failedCourierOrders = 0;
                            }
                        @endphp
                        @if($failedCourierOrders > 0)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive($courierApiRoutes, 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.api.courier.manage') }}">
                                <a href="{{ route('admin.api.courier.manage') }}" class="nav-link">
                                    <i class="menu-icon las la-cog"></i>
                                    <span class="menu-title">@lang('Courier Settings')</span>
                                </a>
                            </li>
                            @foreach($activeCourierProviders ?? [] as $courierProvider)
                            <li class="sidebar-menu-item {{ menuActive('admin.orders.bulk.courier', null, $courierProvider->type) }}">
                                <a href="{{ route('admin.orders.bulk.courier', $courierProvider->type) }}" class="nav-link">
                                    <i class="menu-icon las la-shipping-fast"></i>
                                    <span class="menu-title">@lang('Bulk') ({{ $courierProvider->display_name }})</span>
                                </a>
                            </li>
                            @endforeach
                            @if(empty($activeCourierProviders) || $activeCourierProviders->isEmpty())
                            <li class="sidebar-menu-item">
                                <a href="{{ route('admin.orders.bulk.courier', 'pathao') }}" class="nav-link">
                                    <i class="menu-icon las la-truck"></i>
                                    <span class="menu-title">@lang('Bulk Courier') (Pathao)</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item">
                                <a href="{{ route('admin.orders.bulk.courier', 'steadfast') }}" class="nav-link">
                                    <i class="menu-icon las la-shipping-fast"></i>
                                    <span class="menu-title">@lang('Bulk Courier') (Steadfast)</span>
                                </a>
                            </li>
                            @endif
                            <li class="sidebar-menu-item {{ menuActive('admin.api.courier.logs') }}">
                                <a href="{{ route('admin.api.courier.logs') }}" class="nav-link">
                                    <i class="menu-icon las la-list-alt"></i>
                                    <span class="menu-title">@lang('Courier Logs')</span>
                                    @if($failedCourierOrders > 0)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $failedCourierOrders }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.api.courier.reports') }}">
                                <a href="{{ route('admin.api.courier.reports') }}" class="nav-link">
                                    <i class="menu-icon las la-chart-bar"></i>
                                    <span class="menu-title">@lang('Courier Reports')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if($canAccess('gateways'))
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('admin.gateway*', 3) }}">
                        <i class="menu-icon las la-credit-card"></i>
                        <span class="menu-title">@lang('Payment Gateways')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.gateway*', 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.payment.gateways.hub') }} ">
                                <a href="{{ route('admin.payment.gateways.hub') }}" class="nav-link">
                                    <i class="menu-icon las la-th-large"></i>
                                    <span class="menu-title">@lang('Payment Gateways Hub')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.payment.analytics') }} ">
                                <a href="{{ route('admin.payment.analytics') }}" class="nav-link">
                                    <i class="menu-icon las la-chart-line"></i>
                                    <span class="menu-title">@lang('Payment Analytics')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.gateway.automatic.*') }} ">
                                <a href="{{ route('admin.gateway.automatic.index') }}" class="nav-link">
                                    <i class="menu-icon las la-robot"></i>
                                    <span class="menu-title">@lang('Automatic Gateways')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.gateway.manual.*') }} ">
                                <a href="{{ route('admin.gateway.manual.index') }}" class="nav-link">
                                    <i class="menu-icon las la-hand-holding-usd"></i>
                                    <span class="menu-title">@lang('Manual Gateways')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.gateway.autopay.*') }} ">
                                <a href="{{ route('admin.gateway.autopay.index') }}" class="nav-link">
                                    <i class="menu-icon las la-credit-card"></i>
                                    <span class="menu-title">@lang('Autopay')</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                @endif
                @if($canAccess('deposit'))
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('admin.deposit*', 3) }}">
                        <i class="menu-icon las la-file-invoice-dollar"></i>
                        <span class="menu-title">@lang('Payments')</span>
                        @if (0 < $pendingDepositsCount)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.deposit*', 2) }} ">
                        <ul>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.pending') }} ">
                                <a href="{{ route('admin.deposit.pending') }}" class="nav-link">
                                    <i class="menu-icon las la-clock"></i>
                                    <span class="menu-title">@lang('Pending')</span>
                                    @if ($pendingDepositsCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $pendingDepositsCount }}</span>
                                    @endif
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.approved') }} ">
                                <a href="{{ route('admin.deposit.approved') }}" class="nav-link">
                                    <i class="menu-icon las la-check"></i>
                                    <span class="menu-title">@lang('Approved')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.successful') }} ">
                                <a href="{{ route('admin.deposit.successful') }}" class="nav-link">
                                    <i class="menu-icon las la-check-double"></i>
                                    <span class="menu-title">@lang('Successful')</span>
                                </a>
                            </li>


                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.rejected') }} ">
                                <a href="{{ route('admin.deposit.rejected') }}" class="nav-link">
                                    <i class="menu-icon las la-times"></i>
                                    <span class="menu-title">@lang('Rejected')</span>
                                </a>
                            </li>


                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.initiated') }} ">

                                <a href="{{ route('admin.deposit.initiated') }}" class="nav-link">
                                    <i class="menu-icon las la-play"></i>
                                    <span class="menu-title">@lang('Initiated')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.deposit.list') }} ">
                                <a href="{{ route('admin.deposit.list') }}" class="nav-link">
                                    <i class="menu-icon las la-list"></i>
                                    <span class="menu-title">@lang('All')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if($canAccess('messages'))
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('admin.ticket*', 3) }}">
                        <i class="menu-icon las la-ticket-alt"></i>
                        <span class="menu-title">@lang('Support Ticket')</span>
                        @if (0 < $pendingTicketCount)
                            <span class="menu-badge pill bg--danger ms-auto">
                                <i class="fa fa-exclamation"></i>
                            </span>
                        @endif
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.ticket*', 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.pending') }} ">
                                <a href="{{ route('admin.ticket.pending') }}" class="nav-link">
                                    <i class="menu-icon las la-clock"></i>
                                    <span class="menu-title">@lang('Pending Ticket')</span>
                                    @if ($pendingTicketCount)
                                        <span class="menu-badge pill bg--danger ms-auto">{{ $pendingTicketCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.closed') }} ">
                                <a href="{{ route('admin.ticket.closed') }}" class="nav-link">
                                    <i class="menu-icon las la-lock"></i>
                                    <span class="menu-title">@lang('Closed Ticket')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.answered') }} ">
                                <a href="{{ route('admin.ticket.answered') }}" class="nav-link">
                                    <i class="menu-icon las la-reply"></i>
                                    <span class="menu-title">@lang('Answered Ticket')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.ticket.index') }} ">
                                <a href="{{ route('admin.ticket.index') }}" class="nav-link">
                                    <i class="menu-icon las la-list"></i>
                                    <span class="menu-title">@lang('All Ticket')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.autoai*') }} ">
                                <a href="{{ route('admin.autoai.index') }}" class="nav-link">
                                    <i class="menu-icon las la-robot"></i>
                                    <span class="menu-title">@lang('Auto AI')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if($canAccess('reports'))
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('admin.report*', 3) }}">
                        <i class="menu-icon las la-chart-bar"></i>
                        <span class="menu-title">@lang('Report') </span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.report*', 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive(['admin.report.transaction', 'admin.report.transaction.search']) }}">
                                <a href="{{ route('admin.report.transaction') }}" class="nav-link">
                                    <i class="menu-icon las la-file-invoice-dollar"></i>
                                    <span class="menu-title">@lang('Transaction Log')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive(['admin.report.login.history', 'admin.report.login.ipHistory']) }}">
                                <a href="{{ route('admin.report.login.history') }}" class="nav-link">
                                    <i class="menu-icon las la-sign-in-alt"></i>
                                    <span class="menu-title">@lang('Login History')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.report.notification.history') }}">
                                <a href="{{ route('admin.report.notification.history') }}" class="nav-link">
                                    <i class="menu-icon las la-bell"></i>
                                    <span class="menu-title">@lang('Notification History')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.report.search.analytics') }}">
                                <a href="{{ route('admin.report.search.analytics') }}" class="nav-link">
                                    <i class="menu-icon las la-search"></i>
                                    <span class="menu-title">@lang('User Search Analytics')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('admin.report.ad_source') }}">
                                <a href="{{ route('admin.report.ad_source') }}" class="nav-link">
                                    <i class="menu-icon las la-chart-pie"></i>
                                    <span class="menu-title">@lang('Ad Source Report')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.product') }}">
                                <a href="{{ route('admin.report.product') }}" class="nav-link">
                                    <i class="menu-icon las la-box"></i>
                                    <span class="menu-title">@lang('Product Report')</span>
                                </a>
                            </li>
                            @if(Route::has('admin.report.revenue_profit'))
                            <li class="sidebar-menu-item {{ menuActive('admin.report.revenue_profit') }}">
                                <a href="{{ route('admin.report.revenue_profit') }}" class="nav-link">
                                    <i class="menu-icon las la-coins"></i>
                                    <span class="menu-title">@lang('Revenue & Profit')</span>
                                </a>
                            </li>
                            @endif
                            @if(Route::has('admin.report.employee_performance'))
                            <li class="sidebar-menu-item {{ menuActive('admin.report.employee_performance') }}">
                                <a href="{{ route('admin.report.employee_performance') }}" class="nav-link">
                                    <i class="menu-icon las la-user-tie"></i>
                                    <span class="menu-title">@lang('Employee Performance')</span>
                                </a>
                            </li>
                            @endif

                            <li class="sidebar__menu-header mt-2">@lang('User Activity')</li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.dashboard') }}">
                                <a href="{{ route('admin.report.activity.dashboard') }}" class="nav-link">
                                    <i class="menu-icon las la-chart-line"></i>
                                    <span class="menu-title">@lang('Analytics Dashboard')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.search') }}">
                                <a href="{{ route('admin.report.activity.search') }}" class="nav-link">
                                    <i class="menu-icon las la-search"></i>
                                    <span class="menu-title">@lang('Search')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.product_views') }}">
                                <a href="{{ route('admin.report.activity.product_views') }}" class="nav-link">
                                    <i class="menu-icon las la-eye"></i>
                                    <span class="menu-title">@lang('Product Views')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.cart') }}">
                                <a href="{{ route('admin.report.activity.cart') }}" class="nav-link">
                                    <i class="menu-icon las la-shopping-cart"></i>
                                    <span class="menu-title">@lang('Cart')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.wishlist') }}">
                                <a href="{{ route('admin.report.activity.wishlist') }}" class="nav-link">
                                    <i class="menu-icon las la-heart"></i>
                                    <span class="menu-title">@lang('Wishlist')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.compare') }}">
                                <a href="{{ route('admin.report.activity.compare') }}" class="nav-link">
                                    <i class="menu-icon las la-balance-scale"></i>
                                    <span class="menu-title">@lang('Compare')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.orders') }}">
                                <a href="{{ route('admin.report.activity.orders') }}" class="nav-link">
                                    <i class="menu-icon las la-list-alt"></i>
                                    <span class="menu-title">@lang('Orders')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.track_order') }}">
                                <a href="{{ route('admin.report.activity.track_order') }}" class="nav-link">
                                    <i class="menu-icon las la-truck"></i>
                                    <span class="menu-title">@lang('Track Order Searches')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.payments') }}">
                                <a href="{{ route('admin.report.activity.payments') }}" class="nav-link">
                                    <i class="menu-icon las la-credit-card"></i>
                                    <span class="menu-title">@lang('Payments')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.login') }}">
                                <a href="{{ route('admin.report.activity.login') }}" class="nav-link">
                                    <i class="menu-icon las la-sign-in-alt"></i>
                                    <span class="menu-title">@lang('Login')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.registration') }}">
                                <a href="{{ route('admin.report.activity.registration') }}" class="nav-link">
                                    <i class="menu-icon las la-user-plus"></i>
                                    <span class="menu-title">@lang('Registration')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.messages') }}">
                                <a href="{{ route('admin.report.activity.messages') }}" class="nav-link">
                                    <i class="menu-icon las la-envelope"></i>
                                    <span class="menu-title">@lang('Messages')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.location') }}">
                                <a href="{{ route('admin.report.activity.location') }}" class="nav-link">
                                    <i class="menu-icon las la-map-marker-alt"></i>
                                    <span class="menu-title">@lang('Location')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.all') }}">
                                <a href="{{ route('admin.report.activity.all') }}" class="nav-link">
                                    <i class="menu-icon las la-stream"></i>
                                    <span class="menu-title">@lang('All Activity')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.live') }}">
                                <a href="{{ route('admin.report.activity.live') }}" class="nav-link">
                                    <i class="menu-icon las la-broadcast-tower"></i>
                                    <span class="menu-title">@lang('Live Monitor')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.report.activity.suspicious') }}">
                                <a href="{{ route('admin.report.activity.suspicious') }}" class="nav-link">
                                    <i class="menu-icon las la-exclamation-triangle"></i>
                                    <span class="menu-title">@lang('Suspicious')</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                @endif
                @if($canAccess('subscribers'))
                <li class="sidebar-menu-item  {{ menuActive('admin.subscriber.*') }}">
                    <a href="{{ route('admin.subscriber.index') }}" class="nav-link" data-default-url="{{ route('admin.subscriber.index') }}">
                        <i class="menu-icon las la-thumbs-up"></i>
                        <span class="menu-title">@lang('Subscribers') </span>
                    </a>
                </li>
                @endif
                <li class="sidebar__menu-header">@lang('Settings')</li>
                @if($canAccess('admin_management') && auth()->guard('admin')->user()->isOwner())
                <li class="sidebar-menu-item {{ menuActive('admin.setting.admin.index') }}">
                    <a href="{{ route('admin.setting.admin.index') }}" class="nav-link">
                        <i class="menu-icon las la-user-shield"></i>
                        <span class="menu-title">@lang('Admin Management')</span>
                    </a>
                </li>
                @endif
                @if($canAccess('system_config'))
                <li class="sidebar-menu-item {{ menuActive('admin.setting.system.configuration') }}">
                    <a href="{{ route('admin.setting.system.configuration') }}" class="nav-link">
                        <i class="menu-icon las la-cog"></i>
                        <span class="menu-title">@lang('System Configuration')</span>
                    </a>
                </li>
                @endif
                @if($canAccess('extensions'))
                <li class="sidebar-menu-item {{ menuActive('admin.extensions.index') }}">
                    <a href="{{ route('admin.extensions.index') }}" class="nav-link">
                        <i class="menu-icon las la-cogs"></i>
                        <span class="menu-title">@lang('Extensions')</span>
                    </a>
                </li>
                @endif
                @if($canAccess('language'))
                <li class="sidebar-menu-item  {{ menuActive(['admin.language.manage', 'admin.language.key']) }}">
                    <a href="{{ route('admin.language.manage') }}" class="nav-link" data-default-url="{{ route('admin.language.manage') }}">
                        <i class="menu-icon las la-language"></i>
                        <span class="menu-title">@lang('Language') </span>
                    </a>
                </li>
                @endif
                @if($canAccess('seo'))
                <li class="sidebar-menu-item {{ menuActive('admin.seo') }}">
                    <a href="{{ route('admin.seo') }}" class="nav-link">
                        <i class="menu-icon las la-globe"></i>
                        <span class="menu-title">@lang('SEO Manager')</span>
                    </a>
                </li>
                @endif
                @if($canAccess('notification_setting'))
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('admin.setting.notification*', 3) }}">
                        <i class="menu-icon las la-bell"></i>
                        <span class="menu-title">@lang('Notification Setting')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.setting.notification*', 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.global') }} ">
                                <a href="{{ route('admin.setting.notification.global') }}" class="nav-link">
                                    <i class="menu-icon las la-globe"></i>
                                    <span class="menu-title">@lang('Global Template')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.email') }} ">
                                <a href="{{ route('admin.setting.notification.email') }}" class="nav-link">
                                    <i class="menu-icon las la-envelope"></i>
                                    <span class="menu-title">@lang('Email Setting')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.sms') }} ">
                                <a href="{{ route('admin.setting.notification.sms') }}" class="nav-link">
                                    <i class="menu-icon las la-sms"></i>
                                    <span class="menu-title">@lang('SMS Setting')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.setting.notification.templates') }} ">
                                <a href="{{ route('admin.setting.notification.templates') }}" class="nav-link">
                                    <i class="menu-icon las la-file-alt"></i>
                                    <span class="menu-title">@lang('Notification Templates')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                <li class="sidebar__menu-header">@lang('Frontend Manager')</li>
                @if($canAccess('frontend_templates'))
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive('admin.frontend.templates', 3) }} {{ menuActive('admin.frontend.sections.district', 3) }} {{ menuActive('admin.locations.*', 3) }}">
                        <i class="menu-icon la la-html5"></i>
                        <span class="menu-title">@lang('Manage Templates')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('admin.frontend.templates', 2) }} {{ menuActive('admin.frontend.sections.district', 2) }} {{ menuActive('admin.locations.*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.frontend.templates') }}">
                                <a href="{{ route('admin.frontend.templates') }}" class="nav-link">
                                    <i class="menu-icon las la-file-code"></i>
                                    <span class="menu-title">@lang('Templates')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.locations.index') }}">
                                <a href="{{ route('admin.locations.index') }}" class="nav-link">
                                    <i class="menu-icon las la-map-marked-alt"></i>
                                    <span class="menu-title">@lang('Locations')</span>
                                    <span class="badge bg--primary ms-1">@lang('Div/Dist/Thana')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.frontend.sections.district') }}">
                                <a href="{{ route('admin.frontend.sections.district') }}" class="nav-link">
                                    <i class="menu-icon las la-map-marker-alt"></i>
                                    <span class="menu-title">@lang('District (Checkout)')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if($canAccess('frontend_sections'))
                @php
                    $manageSectionIconMap = [
                        'general'       => 'las la-cog',
                        'icon'         => 'las la-image',
                        'homepage'      => 'las la-th-large',
                        'banner'        => 'las la-image',
                        'contact_us'    => 'las la-envelope',
                        'footer'        => 'las la-sitemap',
                        'login'         => 'las la-sign-in-alt',
                        'policy_pages'  => 'las la-file-contract',
                        'register'      => 'las la-user-plus',
                        'service'       => 'las la-concierge-bell',
                        'ticker'        => 'las la-bullhorn',
                        'scrollbar'     => 'las la-ellipsis-h',
                    ];
                @endphp
                <li class="sidebar-menu-item sidebar-dropdown sidebar-menu-item--manage-section" data-menu="manage-section">
                    <a href="javascript:void(0)" class="{{ menuActive('admin.frontend.sections*', 3) }}">
                        <i class="menu-icon las la-puzzle-piece"></i>
                        <span class="menu-title">@lang('Manage Section')</span>
                    </a>
                    <div class="sidebar-submenu sidebar-submenu--manage-section {{ menuActive('admin.frontend.sections*', 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.frontend.sections.general') }}">
                                <a href="{{ route('admin.frontend.sections.general') }}" class="nav-link">
                                    <i class="menu-icon las la-cog"></i>
                                    <span class="menu-title">@lang('General')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.ui.settings') }} {{ menuActive('admin.theme.settings') }}">
                                <a href="{{ route('admin.ui.settings') }}" class="nav-link">
                                    <i class="menu-icon las la-palette"></i>
                                    <span class="menu-title">@lang('UI & Theme')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.frontend.sections.icon') }}">
                                <a href="{{ route('admin.frontend.sections.icon') }}" class="nav-link">
                                    <i class="menu-icon las la-image"></i>
                                    <span class="menu-title">@lang('Logo & Favicon')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.frontend.sections.homepage') }}">
                                <a href="{{ route('admin.frontend.sections.homepage') }}" class="nav-link">
                                    <i class="menu-icon las la-th-large"></i>
                                    <span class="menu-title">@lang('Homepage Sections')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.frontend.sections.userprofile') }}">
                                <a href="{{ route('admin.frontend.sections.userprofile') }}" class="nav-link">
                                    <i class="menu-icon las la-user-edit"></i>
                                    <span class="menu-title">@lang('User profile control')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.frontend.quickorder') }}">
                                <a href="{{ route('admin.frontend.quickorder') }}" class="nav-link">
                                    <i class="menu-icon las la-bolt"></i>
                                    <span class="menu-title">@lang('Quick Order')</span>
                                </a>
                            </li>
                            @php
                                $lastSegment = collect(request()->segments())->last();
                            @endphp
                            @php
                                $routeMapping = [
                                    'banner' => 'admin.frontend.sections.banner',
                                    'contact_us' => 'admin.frontend.sections.contact',
                                    'footer' => 'admin.frontend.sections.footer',
                                    'login' => 'admin.frontend.sections.login',
                                    'policy_pages' => 'admin.frontend.sections.policy',
                                    'register' => 'admin.frontend.sections.register',
                                    'service' => 'admin.frontend.sections.service',
                                    'ticker' => 'admin.frontend.sections.ticker',
                                    'scrollbar' => 'admin.frontend.sections.scrollbar',
                                ];
                            @endphp
                            @foreach (getPageSections(true) as $k => $secs)
                                @if ($secs['builder'] && $k !== 'social_icon')
                                    @php
                                        $routeName = $routeMapping[$k] ?? 'admin.frontend.sections';
                                        $routeParams = isset($routeMapping[$k]) ? [] : ['key' => $k];
                                        $cleanSegment = isset($routeMapping[$k]) ? str_replace('admin.frontend.sections.', '', $routeMapping[$k]) : $k;
                                        $secIcon = $manageSectionIconMap[$k] ?? 'las la-puzzle-piece';
                                    @endphp
                                    <li class="sidebar-menu-item  @if ($lastSegment == $k || $lastSegment == $cleanSegment) active @endif ">
                                        <a href="{{ route($routeName, $routeParams) }}" class="nav-link">
                                            <i class="menu-icon {{ $secIcon }}"></i>
                                            <span class="menu-title">{{ __($secs['name']) }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </li>
                @endif
                @if($canAccess('contact_channels') && Route::has('admin.contact.channels.index'))
                <li class="sidebar-menu-item {{ menuActive('admin.contact.channels.*') }}">
                    <a href="{{ route('admin.contact.channels.index') }}" class="nav-link">
                        <i class="menu-icon las la-headset"></i>
                        <span class="menu-title">@lang('Contact Channels')</span>
                    </a>
                </li>
                @endif
                <li class="sidebar__menu-header">@lang('Compliance & Privacy')</li>
                @if($canAccess('cookie'))
                <li class="sidebar-menu-item {{ menuActive('admin.setting.cookie') }}">
                    <a href="{{ route('admin.setting.cookie') }}" class="nav-link">
                        <i class="menu-icon las la-cookie-bite"></i>
                        <span class="menu-title">@lang('GDPR Cookie')</span>
                    </a>
                </li>
                @endif
                <li class="sidebar__menu-header">@lang('Extra')</li>
                @if($canAccess('maintenance'))
                <li class="sidebar-menu-item {{ menuActive('admin.maintenance.mode') }}">
                    <a href="{{ route('admin.maintenance.mode') }}" class="nav-link">
                        <i class="menu-icon las la-robot"></i>
                        <span class="menu-title">@lang('Maintenance Mode')</span>
                    </a>
                </li>
                @endif
                @if($canAccess('system'))
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{ menuActive(['admin.system*', 'admin.maintenance*'], 3) }}">
                        <i class="menu-icon la la-server"></i>
                        <span class="menu-title">@lang('System')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive(['admin.system*', 'admin.maintenance*'], 2) }} ">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('admin.maintenance.dashboard') }} ">
                                <a href="{{ route('admin.maintenance.dashboard') }}" class="nav-link">
                                    <i class="menu-icon las la-tools"></i>
                                    <span class="menu-title">@lang('Maintenance')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.info') }} ">
                                <a href="{{ route('admin.system.info') }}" class="nav-link">
                                    <i class="menu-icon las la-info-circle"></i>
                                    <span class="menu-title">@lang('Application')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.server.info') }} ">
                                <a href="{{ route('admin.system.server.info') }}" class="nav-link">
                                    <i class="menu-icon las la-server"></i>
                                    <span class="menu-title">@lang('Server')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('admin.system.optimize') }} ">
                                <a href="{{ route('admin.system.optimize') }}" class="nav-link">
                                    <i class="menu-icon las la-bolt"></i>
                                    <span class="menu-title">@lang('Cache')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if($canAccess('custom_css'))
                <li class="sidebar-menu-item {{ menuActive('admin.setting.custom.css') }}">
                    <a href="{{ route('admin.setting.custom.css') }}" class="nav-link">
                        <i class="menu-icon lab la-css3-alt"></i>
                        <span class="menu-title">@lang('Custom CSS')</span>
                    </a>
                </li>
                @endif
                @if($canAccess('social_login'))
                <li class="sidebar-menu-item {{ menuActive('admin.setting.social.login') }}">
                    <a href="{{ route('admin.setting.social.login') }}" class="nav-link">
                        <i class="menu-icon lab la-google"></i>
                        <span class="menu-title">@lang('Social Logins')</span>
                    </a>
                </li>
                @endif
                @if($canAccess('request_report'))
                <li class="sidebar-menu-item  {{ menuActive('admin.request.report') }}">
                    <a href="{{ route('admin.request.report') }}" class="nav-link" data-default-url="{{ route('admin.request.report') }}">
                        <i class="menu-icon las la-bug"></i>
                        <span class="menu-title">@lang('Report & Request') </span>
                    </a>
                </li>
                @endif
            </ul>
            <div class="text-center mb-3 text-uppercase">
                <span class="text--primary">{{ __(systemDetails()['name']) }}</span>
                <span class="text--success">@lang('V'){{ systemDetails()['version'] }} </span>
            </div>
        </div>
    </div>
</div>
<!-- sidebar end -->

@once
@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush
@endonce

{{-- Sidebar scroll animation removed: menu no longer auto-scrolls on click --}}
