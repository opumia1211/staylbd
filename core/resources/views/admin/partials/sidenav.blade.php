@php
  $adminUser = auth()->guard('admin')->user();
  $canAccess = function ($section) use ($adminUser) {
    return !$adminUser || $adminUser->canAccessSection($section);
  };

  // Dynamic section routes (items listed in Home Layout are excluded below)
  $routeMapping = [
    'banner' => 'admin.frontend.sections.banner',
    'contact_us' => 'admin.frontend.sections.contact',
    'footer' => 'admin.frontend.sections.footer',
    'login' => 'admin.frontend.sections.login',
    'policy_pages' => 'admin.frontend.sections.policy',
    'register' => 'admin.frontend.sections.register',
    'service' => 'admin.frontend.sections.service',
    'social_icon' => 'admin.frontend.sections.social_icon',
    'scrollbar' => 'admin.frontend.sections.scrollbar',
    'middle_banner' => 'admin.frontend.sections.middle_banner',
    'bottom_banner' => 'admin.frontend.sections.bottom_banner',
  ];

  // Explicit links above the loop — exclude these keys from dynamic list
  $sectionsMenuExcludeKeys = [
    'banner',
    'scrollbar',
    'header_icons',
    'social_icon',
  ];

  $manageSectionIconMap = [
    'general' => 'las la-cog',
    'icon' => 'las la-image',
    'homepage' => 'las la-th-large',
    'banner' => 'las la-image',
    'contact_us' => 'las la-envelope',
    'footer' => 'las la-sitemap',
    'login' => 'las la-sign-in-alt',
    'policy_pages' => 'las la-file-contract',
    'register' => 'las la-user-plus',
    'service' => 'las la-concierge-bell',
    'ticker' => 'las la-bullhorn',
    'scrollbar' => 'las la-ellipsis-h',
    'middle_banner' => 'las la-image',
    'bottom_banner' => 'las la-image',
  ];

  // Notification counts for badges
  $failedCourierOrders = 0;
  try {
    $failedCourierOrders = \DB::table('courier_logs')->where('status', 'failed')->count();
  } catch (\Exception $e) {
  }
@endphp

<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu menu-no-animation bg-menu-theme flex-grow-0">
  <div class="container-xxl d-flex align-items-center h-100">
    <div class="menu-horizontal-wrapper">
    <ul class="menu-inner" role="menubar" aria-label="@lang('Admin navigation')">

      <!-- 1. Dashboard & Security -->
      <li
        class="menu-item {{ menuActive(['admin.dashboard', 'admin.security.dashboard', 'admin.business.insights']) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base bx bx-home-smile"></i>
          <div data-i18n="Dashboard">@lang('Dashboard')</div>
        </a>
        <ul class="menu-sub">
          @if($canAccess('dashboard'))
            <li class="menu-item {{ menuActive('admin.dashboard') }}">
              <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon icon-base las la-chart-bar"></i>
                <div data-i18n="Analytics">@lang('Analytics')</div>
              </a>
            </li>
            <li class="menu-item {{ menuActive('admin.business.insights') }}">
              <a href="{{ route('admin.business.insights') }}" class="menu-link">
                <i class="menu-icon icon-base las la-lightbulb"></i>
                <div data-i18n="Insights">@lang('Insights')</div>
              </a>
            </li>
          @endif
          @if($adminUser && $adminUser->isOwner())
            <li class="menu-item {{ menuActive('admin.security.dashboard') }}">
              <a href="{{ route('admin.security.dashboard') }}" class="menu-link">
                <i class="menu-icon icon-base las la-shield-alt"></i>
                <div data-i18n="Security">@lang('Security Hub')</div>
              </a>
            </li>
          @endif
        </ul>
      </li>

      <!-- 2. Home Layout (homepage, ads, header & banner uploads) -->
      @php
        $homeRoutes = [
          'admin.frontend.sections.homepage',
          'admin.frontend.sections.homepageCustomRows*',
          'admin.frontend.sections.homepageAds*',
          'admin.product.topbar.*',
          'admin.offer-timers*',
          'admin.popup-ads*',
          'admin.frontend.sections.header.index',
          'admin.frontend.sections.headericons',
          'admin.frontend.sections.banner*',
          'admin.frontend.sections.middle_banner*',
          'admin.frontend.sections.bottom_banner*',
        ];
      @endphp
      <li class="menu-item {{ menuActive($homeRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-desktop"></i>
          <div data-i18n="Home">@lang('Home Layout')</div>
        </a>
        <ul class="menu-sub menu-sub-scrollable">
          <li class="menu-item {{ menuActive('admin.frontend.sections.homepage') }}">
            <a href="{{ route('admin.frontend.sections.homepage') }}" class="menu-link">
              <i class="menu-icon icon-base las la-th-large"></i>
              <div data-i18n="Homepage Sections">@lang('Homepage Sections')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.homepageCustomRows*') }}">
            <a href="{{ route('admin.frontend.sections.homepageCustomRows') }}" class="menu-link">
              <i class="menu-icon icon-base las la-stream"></i>
              <div data-i18n="Layout & Rows">@lang('Custom Rows')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.homepageAds*') }}">
            <a href="{{ route('admin.frontend.sections.homepageAds') }}" class="menu-link">
              <i class="menu-icon icon-base las la-ad"></i>
              <div data-i18n="Homepage Ads">@lang('Homepage Ads')</div>
            </a>
          </li>
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Promotions & Ads')</li>
          <li class="menu-item {{ menuActive('admin.product.topbar.index') }}">
            <a href="{{ route('admin.product.topbar.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-cubes"></i>
              <div data-i18n="Top Features">@lang('Top Feature Boxes')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.offer-timers.*') }}">
            <a href="{{ route('admin.offer-timers.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-stopwatch"></i>
              <div data-i18n="Offer Timers">@lang('Offer Timers')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.popup-ads.*') }}">
            <a href="{{ route('admin.popup-ads.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-window-maximize"></i>
              <div data-i18n="Popup Ads">@lang('Popup Ads')</div>
            </a>
          </li>
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Header & Banners')</li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.header.index') }}">
            <a href="{{ route('admin.frontend.sections.header.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-window-maximize"></i>
              <div data-i18n="Header">@lang('Header Control')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.headericons') }}">
            <a href="{{ route('admin.frontend.sections.headericons') }}" class="menu-link">
              <i class="menu-icon icon-base las la-icons"></i>
              <div data-i18n="Header Icons">@lang('Header Icons Upload')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.banner*') }}">
            <a href="{{ route('admin.frontend.sections.banner') }}" class="menu-link">
              <i class="menu-icon icon-base las la-image"></i>
              <div data-i18n="Banners">@lang('Banner Management')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.middle_banner*') }}">
            <a href="{{ route('admin.frontend.sections.middle_banner') }}" class="menu-link">
              <i class="menu-icon icon-base las la-image"></i>
              <div data-i18n="Middle Banner">@lang('Middle Banner')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.bottom_banner*') }}">
            <a href="{{ route('admin.frontend.sections.bottom_banner') }}" class="menu-link">
              <i class="menu-icon icon-base las la-image"></i>
              <div data-i18n="Bottom Banner">@lang('Bottom Banner')</div>
            </a>
          </li>
        </ul>
      </li>

      <!-- 3a. Catalog (inventory & deals) -->
      @php
        $catalogRoutes = [
          'admin.product.index',
          'admin.product.stock.alerts',
          'admin.product.todayDeal',
          'admin.product.hot',
          'admin.product.trending',
          'admin.product.bestSelling',
          'admin.product.feature.index',
        ];
        $catalogActive = menuActive($catalogRoutes)
          || (request()->routeIs('admin.product.index') && request('low_stock'));
      @endphp
      <li class="menu-item {{ $catalogActive ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-tshirt"></i>
          <div data-i18n="Catalog">@lang('Catalog')</div>
        </a>
        <ul class="menu-sub menu-sub-scrollable">
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Inventory & Alerts')</li>
          <li class="menu-item {{ menuActive('admin.product.index') }}">
            <a href="{{ route('admin.product.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-boxes"></i>
              <div data-i18n="All">@lang('All Products')</div>
            </a>
          </li>
          <li
            class="menu-item {{ (request()->routeIs('admin.product.index') && request('low_stock')) ? 'active' : '' }}">
            <a href="{{ route('admin.product.index', ['low_stock' => 1]) }}" class="menu-link">
              <i class="menu-icon icon-base las la-exclamation-triangle text-warning"></i>
              <div data-i18n="Low Stock">@lang('Low Stock Items')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.product.stock.alerts') }}">
            <a href="{{ route('admin.product.stock.alerts') }}" class="menu-link">
              <i class="menu-icon icon-base las la-bell text-danger"></i>
              <div data-i18n="Stock Alerts">@lang('Stock Alerts')</div>
            </a>
          </li>
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Deals & Trending')</li>
          <li class="menu-item {{ menuActive('admin.product.todayDeal') }}">
            <a href="{{ route('admin.product.todayDeal') }}" class="menu-link">
              <i class="menu-icon icon-base las la-bolt"></i>
              <div data-i18n="Quick Deals">@lang('Quick Deals')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.product.hot') }}">
            <a href="{{ route('admin.product.hot') }}" class="menu-link">
              <i class="menu-icon icon-base las la-fire"></i>
              <div data-i18n="Hot Deals">@lang('Hot Deals')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.product.trending') }}">
            <a href="{{ route('admin.product.trending') }}" class="menu-link">
              <i class="menu-icon icon-base las la-chart-line"></i>
              <div data-i18n="Trending">@lang('Trending')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.product.bestSelling') }}">
            <a href="{{ route('admin.product.bestSelling') }}" class="menu-link">
              <i class="menu-icon icon-base las la-star"></i>
              <div data-i18n="Best Selling">@lang('Best Selling')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.product.feature.index') }}">
            <a href="{{ route('admin.product.feature.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-certificate"></i>
              <div data-i18n="Featured">@lang('Featured Products')</div>
            </a>
          </li>
        </ul>
      </li>

      <!-- 3b. Products (upload & management) -->
      @php
        $productUploadRoutes = [
          'admin.product.hub',
          'admin.product.create',
          'admin.product.general.create',
          'admin.product.reviews*',
          'admin.report.product',
          'admin.report.activity.product_views',
          'admin.setting.stock.order.messages',
        ];
      @endphp
      <li class="menu-item {{ menuActive($productUploadRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-cloud-upload-alt"></i>
          <div data-i18n="Products">@lang('Products')</div>
        </a>
        <ul class="menu-sub menu-sub-scrollable">
          <li class="menu-item {{ menuActive('admin.product.hub') }}">
            <a href="{{ route('admin.product.hub') }}" class="menu-link">
              <i class="menu-icon icon-base las la-th-large"></i>
              <div data-i18n="Product Center">@lang('Product Center')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.product.index') }}">
            <a href="{{ route('admin.product.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-boxes"></i>
              <div data-i18n="All">@lang('All Products')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.product.create') }}">
            <a href="{{ route('admin.product.create') }}" class="menu-link">
              <i class="menu-icon icon-base las la-plus-circle"></i>
              <div data-i18n="Add">@lang('Add New Product')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.product.general.create') }}">
            <a href="{{ route('admin.product.general.create') }}" class="menu-link">
              <i class="menu-icon icon-base las la-magic"></i>
              <div data-i18n="Quick Upload">@lang('Quick Upload')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.product.reviews.index') }}">
            <a href="{{ route('admin.product.reviews.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-comment-dots"></i>
              <div data-i18n="Reviews">@lang('Product Reviews')</div>
            </a>
          </li>
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Upload Tools')</li>
          <li class="menu-item {{ menuActive('admin.attributes.index') }}">
            <a href="{{ route('admin.attributes.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-sliders-h"></i>
              <div data-i18n="Attributes">@lang('Product Attributes')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.category.attributes.index') }}">
            <a href="{{ route('admin.category.attributes.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-tag"></i>
              <div data-i18n="Cat Attr">@lang('Category Attributes')</div>
            </a>
          </li>
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Sell & Visibility')</li>
          <li class="menu-item {{ menuActive('admin.report.product') }}">
            <a href="{{ route('admin.report.product') }}" class="menu-link">
              <i class="menu-icon icon-base las la-chart-bar"></i>
              <div data-i18n="Performance">@lang('Product Performance')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.report.activity.product_views') }}">
            <a href="{{ route('admin.report.activity.product_views') }}" class="menu-link">
              <i class="menu-icon icon-base las la-eye"></i>
              <div data-i18n="Views">@lang('Product Views')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.seo') }}">
            <a href="{{ route('admin.seo') }}" class="menu-link">
              <i class="menu-icon icon-base las la-globe"></i>
              <div data-i18n="SEO">@lang('SEO Manager')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.setting.stock.order.messages') }}">
            <a href="{{ route('admin.setting.stock.order.messages') }}" class="menu-link">
              <i class="menu-icon icon-base las la-cog"></i>
              <div data-i18n="Stock Msgs">@lang('Stock & Order Messages')</div>
            </a>
          </li>
        </ul>
      </li>

      <!-- 3c. Categories (taxonomy & coupons) -->
      @php
        $categoryRoutes = [
          'admin.category.hub',
          'admin.category*',
          'admin.subcategory*',
          'admin.brand*',
          'admin.attributes*',
          'admin.coupon*',
          'admin.category.attributes*',
        ];
      @endphp
      <li class="menu-item {{ menuActive($categoryRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-sitemap"></i>
          <div data-i18n="Categories">@lang('Categories')</div>
        </a>
        <ul class="menu-sub menu-sub-scrollable">
          <li class="menu-item {{ menuActive('admin.category.hub') }}">
            <a href="{{ route('admin.category.hub') }}" class="menu-link">
              <i class="menu-icon icon-base las la-th-large"></i>
              <div data-i18n="Category Center">@lang('Category Center')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.category.index') }}">
            <a href="{{ route('admin.category.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-align-left"></i>
              <div data-i18n="Categories">@lang('Categories')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.subcategory.index') }}">
            <a href="{{ route('admin.subcategory.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-align-center"></i>
              <div data-i18n="Subcategories">@lang('Subcategories')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.brand.index') }}">
            <a href="{{ route('admin.brand.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-tags"></i>
              <div data-i18n="Brands">@lang('Brands')</div>
            </a>
          </li>
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Attributes & Promos')</li>
          <li class="menu-item {{ menuActive('admin.attributes.create') }}">
            <a href="{{ route('admin.attributes.create') }}" class="menu-link">
              <i class="menu-icon icon-base las la-plus"></i>
              <div data-i18n="Add Attr">@lang('Add Attribute')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.attributes.index') }}">
            <a href="{{ route('admin.attributes.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-sliders-h"></i>
              <div data-i18n="Attributes">@lang('Product Attributes')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.category.attributes.index') }}">
            <a href="{{ route('admin.category.attributes.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-tag"></i>
              <div data-i18n="Cat Attr">@lang('Category Attributes')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.coupon.index') }}">
            <a href="{{ route('admin.coupon.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-bullhorn"></i>
              <div data-i18n="Coupon">@lang('Coupons')</div>
            </a>
          </li>
        </ul>
      </li>

      <!-- 4a. Orders (split from Operations) -->
      @php
        $orderMenuRoutes = [
          'admin.orders*',
          'admin.abandoned-orders*',
          'admin.notifications*',
          'admin.frontend.quickorder',
        ];
      @endphp
      <li class="menu-item {{ menuActive($orderMenuRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-list-alt"></i>
          <div data-i18n="Orders">@lang('Orders')</div>
          @if($pendingOrderCount > 0) <span class="badge badge-dot bg-danger ms-2"></span> @endif
        </a>
        <ul class="menu-sub menu-sub--scroll menu-sub-scrollable menu-orders-panel">
          <li class="menu-item-header small menu-section-label text-uppercase py-2 px-3">@lang('Order Hub')</li>
          <li class="menu-item {{ menuActive('admin.orders.hub') }}">
            <a href="{{ route('admin.orders.hub') }}" class="menu-link">
              <i class="menu-icon icon-base las la-th-large"></i>
              <div data-i18n="Order Center">@lang('Order Center')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.orders.automation.*') }}">
            <a href="{{ route('admin.orders.automation.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-robot"></i>
              <div data-i18n="Automation">@lang('Order Automation')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.orders.fulfillment') }}">
            <a href="{{ route('admin.orders.fulfillment') }}" class="menu-link">
              <i class="menu-icon icon-base las la-tasks"></i>
              <div data-i18n="Fulfillment">@lang('Fulfillment Queue')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.orders.index') }}">
            <a href="{{ route('admin.orders.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-list"></i>
              <div data-i18n="All Orders">@lang('All Orders')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.orders.channels.*') }}">
            <a href="{{ route('admin.orders.channels.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-project-diagram"></i>
              <div data-i18n="Order Channels">@lang('Order Channels')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive(['admin.orders.import-export', 'admin.orders.import']) }}">
            <a href="{{ route('admin.orders.import-export') }}" class="menu-link">
              <i class="menu-icon icon-base las la-exchange-alt"></i>
              <div data-i18n="Import Export">@lang('Import / Export')</div>
            </a>
          </li>
          <li class="menu-item-header small menu-section-label text-uppercase py-2 px-3">@lang('Order Status')</li>
          <li class="menu-item {{ menuActive('admin.orders.pending') }}">
            <a href="{{ route('admin.orders.pending') }}" class="menu-link">
              <i class="menu-icon icon-base las la-clock"></i>
              <div>@lang('Pending')</div>
              @if($pendingOrderCount > 0)<span class="badge bg-danger ms-auto">{{ $pendingOrderCount }}</span>@endif
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.orders.confirmed') }}">
            <a href="{{ route('admin.orders.confirmed') }}" class="menu-link">
              <i class="menu-icon icon-base las la-check"></i>
              <div>@lang('Confirmed')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.orders.processing') }}">
            <a href="{{ route('admin.orders.processing') }}" class="menu-link">
              <i class="menu-icon icon-base las la-cog"></i>
              <div>@lang('Processing')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.orders.packaging') }}">
            <a href="{{ route('admin.orders.packaging') }}" class="menu-link">
              <i class="menu-icon icon-base las la-box"></i>
              <div>@lang('Packaging')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.orders.shipped') }}">
            <a href="{{ route('admin.orders.shipped') }}" class="menu-link">
              <i class="menu-icon icon-base las la-truck"></i>
              <div>@lang('Shipped')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.orders.delivered') }}">
            <a href="{{ route('admin.orders.delivered') }}" class="menu-link">
              <i class="menu-icon icon-base las la-check-double"></i>
              <div>@lang('Delivered')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.orders.cancel') }}">
            <a href="{{ route('admin.orders.cancel') }}" class="menu-link">
              <i class="menu-icon icon-base las la-times-circle"></i>
              <div>@lang('Canceled')</div>
            </a>
          </li>
          <li class="menu-item-header small menu-section-label text-uppercase py-2 px-3">@lang('More')</li>
          <li class="menu-item {{ menuActive('admin.abandoned-orders.*') }}">
            <a href="{{ route('admin.abandoned-orders.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-shopping-cart"></i>
              <div data-i18n="Abandoned">@lang('Abandoned Carts')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.frontend.quickorder') }}">
            <a href="{{ route('admin.frontend.quickorder') }}" class="menu-link">
              <i class="menu-icon icon-base las la-shipping-fast"></i>
              <div data-i18n="Quick Order">@lang('Quick Order Page')</div>
            </a>
          </li>
        </ul>
      </li>

      <!-- 4b. Shipping & Logistics (split from Operations) -->
      @php
        $shippingMenuRoutes = [
          'admin.shipping*',
          'admin.api.courier*',
          'admin.locations.*',
          'admin.setting.stock.order.messages',
        ];
      @endphp
      <li class="menu-item menu-shipping-panel {{ menuActive($shippingMenuRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-truck-moving"></i>
          <div data-i18n="Shipping">@lang('Shipping')</div>
          @if($failedCourierOrders > 0) <span class="badge badge-dot bg-danger ms-2"></span> @endif
        </a>
        <ul class="menu-sub menu-sub--scroll menu-sub-scrollable">
          <li class="menu-item-header small menu-section-label text-uppercase py-2 px-3">@lang('Shipping Hub')</li>
          <li class="menu-item {{ menuActive('admin.shipping.index') }}">
            <a href="{{ route('admin.shipping.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-th-large"></i>
              <div data-i18n="Shipping Hub">@lang('Shipping Center')</div>
            </a>
          </li>
          <li class="menu-item-header small menu-section-label text-uppercase py-2 px-3">@lang('Shipping Settings')</li>
          <li class="menu-item {{ menuActive('admin.shipping.zones*') }}">
            <a href="{{ route('admin.shipping.zones.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-map"></i>
              <div data-i18n="Zones">@lang('Zones')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.shipping.methods*') }}">
            <a href="{{ route('admin.shipping.methods.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-shipping-fast"></i>
              <div data-i18n="Methods">@lang('Methods')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.shipping.rules*') }}">
            <a href="{{ route('admin.shipping.rules.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-ruler-combined"></i>
              <div data-i18n="Rules">@lang('Rules')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.shipping.cod.index') }}">
            <a href="{{ route('admin.shipping.cod.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-money-bill-wave"></i>
              <div data-i18n="COD">@lang('COD Settings')</div>
            </a>
          </li>
          <li class="menu-item-header small menu-section-label text-uppercase py-2 px-3">
            @lang('Courier & Delivery')
            @if($failedCourierOrders > 0) <span class="badge bg-danger ms-1">{{ $failedCourierOrders }}</span> @endif
          </li>
          <li class="menu-item {{ menuActive('admin.api.courier.manage') }}">
            <a href="{{ route('admin.api.courier.manage') }}" class="menu-link">
              <i class="menu-icon icon-base las la-cog"></i>
              <div data-i18n="Courier Settings">@lang('Courier Settings')</div>
            </a>
          </li>
          @foreach($activeCourierProviders ?? [] as $courierProvider)
            @if(is_object($courierProvider))
              <li class="menu-item {{ menuActive('admin.orders.bulk.courier', null, $courierProvider->type) }}">
                <a href="{{ route('admin.orders.bulk.courier', $courierProvider->type) }}" class="menu-link">
                  <i class="menu-icon icon-base las la-shipping-fast"></i>
                  <div data-i18n="Bulk">@lang('Bulk Ship') ({{ $courierProvider->display_name }})</div>
                </a>
              </li>
            @endif
          @endforeach
          @if(empty($activeCourierProviders) || $activeCourierProviders->isEmpty())
            <li class="menu-item {{ menuActive('admin.orders.bulk.courier', null, 'pathao') }}">
              <a href="{{ route('admin.orders.bulk.courier', 'pathao') }}" class="menu-link">
                <i class="menu-icon icon-base las la-shipping-fast"></i>
                <div>@lang('Bulk Ship') (Pathao)</div>
              </a>
            </li>
            <li class="menu-item {{ menuActive('admin.orders.bulk.courier', null, 'steadfast') }}">
              <a href="{{ route('admin.orders.bulk.courier', 'steadfast') }}" class="menu-link">
                <i class="menu-icon icon-base las la-shipping-fast"></i>
                <div>@lang('Bulk Ship') (Steadfast)</div>
              </a>
            </li>
          @endif
          <li class="menu-item {{ menuActive('admin.api.courier.logs') }}">
            <a href="{{ route('admin.api.courier.logs') }}" class="menu-link">
              <i class="menu-icon icon-base las la-list-alt"></i>
              <div data-i18n="Courier Logs">@lang('Courier Logs')</div>
              @if($failedCourierOrders > 0) <span class="badge bg-danger ms-auto">{{ $failedCourierOrders }}</span> @endif
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.api.courier.reports') }}">
            <a href="{{ route('admin.api.courier.reports') }}" class="menu-link">
              <i class="menu-icon icon-base las la-chart-bar"></i>
              <div data-i18n="Courier Reports">@lang('Courier Reports')</div>
            </a>
          </li>
          <li class="menu-item-header small menu-section-label text-uppercase py-2 px-3">@lang('Locations & Messages')</li>
          <li class="menu-item {{ menuActive('admin.locations.index') }}">
            <a href="{{ route('admin.locations.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-map-pin"></i>
              <div data-i18n="BD Locations">@lang('Div/Dist/Thana')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.setting.stock.order.messages') }}">
            <a href="{{ route('admin.setting.stock.order.messages') }}" class="menu-link">
              <i class="menu-icon icon-base las la-comment-dots"></i>
              <div data-i18n="Order Messages">@lang('Stock & Order Msgs')</div>
            </a>
          </li>
        </ul>
      </li>

      <!-- 5. Payment -->
      @php
        $paymentRoutes = [
          'admin.finance*',
          'admin.gateway*',
          'admin.deposit*',
          'admin.payment.*',
          'admin.shipping.cod*',
        ];
      @endphp
      <li class="menu-item menu-payment-panel {{ menuActive($paymentRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-credit-card"></i>
          <div data-i18n="Payment">@lang('Payment')</div>
          @if($pendingDepositsCount > 0) <span class="badge badge-dot bg-danger ms-2"></span> @endif
        </a>
        <ul class="menu-sub menu-sub--scroll menu-sub-scrollable">
          <li class="menu-item {{ menuActive(['admin.payment.gateways.hub', 'admin.finance.hub']) }}">
            <a href="{{ route('admin.payment.gateways.hub') }}" class="menu-link">
              <i class="menu-icon icon-base las la-th-large"></i>
              <div data-i18n="Payment Center">@lang('Payment Center')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.payment.analytics') }}">
            <a href="{{ route('admin.payment.analytics') }}" class="menu-link">
              <i class="menu-icon icon-base las la-chart-line"></i>
              <div data-i18n="Pay Analytics">@lang('Payment Analytics')</div>
            </a>
          </li>
          <li class="menu-item-header small menu-section-label text-uppercase py-2 px-3">@lang('Gateway Methods')</li>
          <li class="menu-item {{ menuActive('admin.gateway.automatic.*') }}">
            <a href="{{ route('admin.gateway.automatic.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-robot"></i>
              <div data-i18n="Automatic API">@lang('Automatic (API)')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.gateway.manual.*') }}">
            <a href="{{ route('admin.gateway.manual.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-university"></i>
              <div data-i18n="Manual Bank">@lang('Manual / Bank')</div>
            </a>
          </li>
          @if(Route::has('admin.gateway.manual.create'))
          <li class="menu-item {{ menuActive('admin.gateway.manual.create') }}">
            <a href="{{ route('admin.gateway.manual.create') }}" class="menu-link">
              <i class="menu-icon icon-base las la-plus-circle"></i>
              <div data-i18n="Add Manual">@lang('Add Manual Gateway')</div>
            </a>
          </li>
          @endif
          <li class="menu-item {{ menuActive('admin.gateway.autopay.*') }}">
            <a href="{{ route('admin.gateway.autopay.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-external-link-alt"></i>
              <div data-i18n="Autopay">@lang('Autopay / SMS')</div>
            </a>
          </li>
          <li class="menu-item-header small menu-section-label text-uppercase py-2 px-3">@lang('More Options')</li>
          <li class="menu-item {{ menuActive('admin.shipping.cod.*') }}">
            <a href="{{ route('admin.shipping.cod.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-money-bill-wave"></i>
              <div data-i18n="COD">@lang('COD Settings')</div>
            </a>
          </li>
          @if(Route::has('admin.report.activity.payments'))
          <li class="menu-item {{ menuActive('admin.report.activity.payments') }}">
            <a href="{{ route('admin.report.activity.payments') }}" class="menu-link">
              <i class="menu-icon icon-base las la-history"></i>
              <div data-i18n="Pay Activity">@lang('Payment Activity')</div>
            </a>
          </li>
          @endif
          <li class="menu-item-header small menu-section-label text-uppercase py-2 px-3">@lang('Payment History')</li>
          <li class="menu-item {{ menuActive('admin.deposit.list') }}">
            <a href="{{ route('admin.deposit.list') }}" class="menu-link">
              <i class="menu-icon icon-base las la-list"></i>
              <div data-i18n="All Payments">@lang('All Payments')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.deposit.pending') }}">
            <a href="{{ route('admin.deposit.pending') }}" class="menu-link">
              <i class="menu-icon icon-base las la-clock"></i>
              <div data-i18n="Pending">@lang('Pending')</div>
              @if($pendingDepositsCount) <span class="badge bg-danger ms-auto">{{ $pendingDepositsCount }}</span> @endif
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.deposit.approved') }}">
            <a href="{{ route('admin.deposit.approved') }}" class="menu-link">
              <i class="menu-icon icon-base las la-check"></i>
              <div data-i18n="Approved">@lang('Approved')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.deposit.successful') }}">
            <a href="{{ route('admin.deposit.successful') }}" class="menu-link">
              <i class="menu-icon icon-base las la-check-double"></i>
              <div data-i18n="Successful">@lang('Successful')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.deposit.rejected') }}">
            <a href="{{ route('admin.deposit.rejected') }}" class="menu-link">
              <i class="menu-icon icon-base las la-times-circle"></i>
              <div data-i18n="Rejected">@lang('Rejected')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.deposit.initiated') }}">
            <a href="{{ route('admin.deposit.initiated') }}" class="menu-link">
              <i class="menu-icon icon-base las la-hourglass-start"></i>
              <div data-i18n="Initiated">@lang('Initiated')</div>
            </a>
          </li>
        </ul>
      </li>

      <!-- 6. Customers & Support -->
      @php
        $supportRoutes = [
          'admin.users*',
          'admin.subscriber*',
          'admin.ticket*',
          'admin.autoai*',
          'admin.notifications',
          'admin.contact.channels.*'
        ];
      @endphp
      <li class="menu-item {{ menuActive($supportRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-users"></i>
          <div data-i18n="Support">@lang('Support')</div>
          @if($pendingTicketCount > 0 || $bannedUsersCount > 0) <span class="badge badge-dot bg-danger ms-2"></span>
          @endif
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ menuActive('admin.users.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
              <i class="menu-icon icon-base las la-user-cog"></i>
              <div data-i18n="Customers">@lang('Manage Customers')</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ menuActive('admin.users.all') }}"><a href="{{ route('admin.users.all') }}"
                  class="menu-link">@lang('All Customers')</a></li>
              <li class="menu-item {{ menuActive('admin.users.active') }}"><a href="{{ route('admin.users.active') }}"
                  class="menu-link">@lang('Active Customers')</a></li>
              <li class="menu-item {{ menuActive('admin.users.banned') }}"><a href="{{ route('admin.users.banned') }}"
                  class="menu-link">@lang('Banned Customers')</a></li>
              <li class="menu-item {{ menuActive('admin.users.email.unverified') }}"><a
                  href="{{ route('admin.users.email.unverified') }}" class="menu-link">@lang('Email Unverified')</a>
              </li>
              <li class="menu-item {{ menuActive('admin.users.mobile.unverified') }}"><a
                  href="{{ route('admin.users.mobile.unverified') }}" class="menu-link">@lang('SMS Unverified')</a></li>
              <li class="menu-item {{ menuActive('admin.users.with.balance') }}"><a
                  href="{{ route('admin.users.with.balance') }}" class="menu-link">@lang('With Balance')</a></li>
            </ul>
          </li>
          <li class="menu-item {{ menuActive('admin.users.notification.all') }}">
            <a href="{{ route('admin.users.notification.all') }}" class="menu-link">
              <i class="menu-icon icon-base las la-bell"></i>
              <div data-i18n="Notif All">@lang('Notification to All')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.subscriber.index') }}">
            <a href="{{ route('admin.subscriber.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-thumbs-up"></i>
              <div data-i18n="Subscribers">@lang('Subscribers')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.ticket.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
              <i class="menu-icon icon-base las la-ticket-alt"></i>
              <div data-i18n="Tickets">@lang('Support Tickets')</div>
              @if($pendingTicketCount > 0) <span class="badge bg-danger ms-2">{{ $pendingTicketCount }}</span> @endif
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ menuActive('admin.ticket.index') }}"><a href="{{ route('admin.ticket.index') }}"
                  class="menu-link">@lang('All Tickets')</a></li>
              <li class="menu-item {{ menuActive('admin.ticket.pending') }}"><a
                  href="{{ route('admin.ticket.pending') }}" class="menu-link">@lang('Pending') @if($pendingTicketCount)
                  <span class="badge bg-danger ms-auto">{{ $pendingTicketCount }}</span> @endif</a></li>
              <li class="menu-item {{ menuActive('admin.ticket.answered') }}"><a
                  href="{{ route('admin.ticket.answered') }}" class="menu-link">@lang('Answered')</a></li>
              <li class="menu-item {{ menuActive('admin.ticket.closed') }}"><a href="{{ route('admin.ticket.closed') }}"
                  class="menu-link">@lang('Closed')</a></li>
            </ul>
          </li>
          <li class="menu-item {{ menuActive('admin.autoai.index') }}">
            <a href="{{ route('admin.autoai.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-robot"></i>
              <div data-i18n="AI">@lang('Auto AI System')</div>
            </a>
          </li>
          @if(Route::has('admin.contact.channels.index'))
            <li class="menu-item {{ menuActive('admin.contact.channels.*') }}">
              <a href="{{ route('admin.contact.channels.index') }}" class="menu-link">
                <i class="menu-icon icon-base las la-headset"></i>
                <div data-i18n="Contact Ch">@lang('Contact Channels')</div>
              </a>
            </li>
          @endif
        </ul>
      </li>

      <!-- 7. Analytics & Reports -->
      @php
        $reportRoutes = [
          'admin.report*',
          'admin.report.activity.*'
        ];
      @endphp
      <li class="menu-item {{ menuActive($reportRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-chart-bar"></i>
          <div data-i18n="Reports">@lang('Reports')</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ menuActive('admin.report.transaction') }}">
            <a href="{{ route('admin.report.transaction') }}" class="menu-link">
              <i class="menu-icon icon-base las la-file-invoice-dollar"></i>
              <div data-i18n="Trans">@lang('Transaction Logs')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.report.login.history') }}">
            <a href="{{ route('admin.report.login.history') }}" class="menu-link">
              <i class="menu-icon icon-base las la-sign-in-alt"></i>
              <div data-i18n="Login Hist">@lang('Login History')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.report.notification.history') }}">
            <a href="{{ route('admin.report.notification.history') }}" class="menu-link">
              <i class="menu-icon icon-base las la-bell"></i>
              <div data-i18n="Notif Hist">@lang('Notification History')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.report.search.analytics') }}">
            <a href="{{ route('admin.report.search.analytics') }}" class="menu-link">
              <i class="menu-icon icon-base las la-search"></i>
              <div data-i18n="Search">@lang('Search Analytics')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.report.ad_source') }}">
            <a href="{{ route('admin.report.ad_source') }}" class="menu-link">
              <i class="menu-icon icon-base las la-chart-pie"></i>
              <div data-i18n="Ads">@lang('Ad Source Report')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.report.product') }}">
            <a href="{{ route('admin.report.product') }}" class="menu-link">
              <i class="menu-icon icon-base las la-box"></i>
              <div data-i18n="Product">@lang('Product Performance')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.report.activity.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
              <i class="menu-icon icon-base las la-chart-line"></i>
              <div data-i18n="Activity">@lang('User Activity')</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ menuActive('admin.report.activity.dashboard') }}"><a
                  href="{{ route('admin.report.activity.dashboard') }}" class="menu-link">@lang('Dashboard')</a></li>
              <li class="menu-item {{ menuActive('admin.report.activity.search') }}"><a
                  href="{{ route('admin.report.activity.search') }}" class="menu-link">@lang('Search Activity')</a></li>
              <li class="menu-item {{ menuActive('admin.report.activity.product_views') }}"><a
                  href="{{ route('admin.report.activity.product_views') }}" class="menu-link">@lang('Product Views')</a>
              </li>
              <li class="menu-item {{ menuActive('admin.report.activity.cart') }}"><a
                  href="{{ route('admin.report.activity.cart') }}" class="menu-link">@lang('Cart Activity')</a></li>
              <li class="menu-item {{ menuActive('admin.report.activity.wishlist') }}"><a
                  href="{{ route('admin.report.activity.wishlist') }}" class="menu-link">@lang('Wishlist Activity')</a>
              </li>
              <li class="menu-item {{ menuActive('admin.report.activity.compare') }}"><a
                  href="{{ route('admin.report.activity.compare') }}" class="menu-link">@lang('Compare Activity')</a>
              </li>
              <li class="menu-item {{ menuActive('admin.report.activity.orders') }}"><a
                  href="{{ route('admin.report.activity.orders') }}" class="menu-link">@lang('Order History')</a></li>
              <li class="menu-item {{ menuActive('admin.report.activity.track_order') }}"><a
                  href="{{ route('admin.report.activity.track_order') }}" class="menu-link">@lang('Track Search')</a>
              </li>
              <li class="menu-item {{ menuActive('admin.report.activity.payments') }}"><a
                  href="{{ route('admin.report.activity.payments') }}" class="menu-link">@lang('Payment Activity')</a>
              </li>
              <li class="menu-item {{ menuActive('admin.report.activity.login') }}"><a
                  href="{{ route('admin.report.activity.login') }}" class="menu-link">@lang('Login Tracking')</a></li>
              <li class="menu-item {{ menuActive('admin.report.activity.live') }}"><a
                  href="{{ route('admin.report.activity.live') }}" class="menu-link">@lang('Live Monitor')</a></li>
              <li class="menu-item {{ menuActive('admin.report.activity.suspicious') }}"><a
                  href="{{ route('admin.report.activity.suspicious') }}"
                  class="menu-link">@lang('Suspicious Activity')</a></li>
            </ul>
          </li>
          @if(Route::has('admin.report.revenue_profit'))
            <li class="menu-item {{ menuActive('admin.report.revenue_profit') }}">
              <a href="{{ route('admin.report.revenue_profit') }}" class="menu-link">
                <i class="menu-icon icon-base las la-coins"></i>
                <div data-i18n="Revenue">@lang('Revenue & Profit')</div>
              </a>
            </li>
          @endif
          @if(Route::has('admin.report.employee_performance'))
            <li class="menu-item {{ menuActive('admin.report.employee_performance') }}">
              <a href="{{ route('admin.report.employee_performance') }}" class="menu-link">
                <i class="menu-icon icon-base las la-user-tie"></i>
                <div data-i18n="Employee">@lang('Employee Performance')</div>
              </a>
            </li>
          @endif
        </ul>
      </li>

      <!-- 8. Global Settings -->
      @php
        $settingsRoutes = [
          'admin.setting*',
          'admin.extensions*',
          'admin.language*',
          'admin.seo*',
          'admin.ui.settings',
          'admin.theme.settings'
        ];
      @endphp
      <li class="menu-item {{ menuActive($settingsRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-cog"></i>
          <div data-i18n="Settings">@lang('Global Settings')</div>
        </a>
        <ul class="menu-sub">
          @if($canAccess('system_config'))
            <li class="menu-item {{ menuActive('admin.setting.system.configuration') }}">
              <a href="{{ route('admin.setting.system.configuration') }}" class="menu-link">
                <i class="menu-icon icon-base las la-cogs"></i>
                <div data-i18n="Config">@lang('System Configuration')</div>
              </a>
            </li>
          @endif
          @if($canAccess('admin_management') && auth()->guard('admin')->user()->isOwner())
            <li class="menu-item {{ menuActive('admin.setting.admin.index') }}">
              <a href="{{ route('admin.setting.admin.index') }}" class="menu-link">
                <i class="menu-icon icon-base las la-user-shield"></i>
                <div data-i18n="Admin Mgmt">@lang('Admin Management')</div>
              </a>
            </li>
          @endif
          <li class="menu-item {{ menuActive(['admin.ui.settings', 'admin.theme.settings']) }}">
            <a href="{{ route('admin.ui.settings') }}" class="menu-link">
              <i class="menu-icon icon-base las la-palette"></i>
              <div data-i18n="UI">@lang('UI & Theme Settings')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.language.manage') }}">
            <a href="{{ route('admin.language.manage') }}" class="menu-link">
              <i class="menu-icon icon-base las la-language"></i>
              <div data-i18n="Lang">@lang('Language Manager')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.seo') }}">
            <a href="{{ route('admin.seo') }}" class="menu-link">
              <i class="menu-icon icon-base las la-globe"></i>
              <div data-i18n="SEO">@lang('SEO Manager')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.setting.notification*') }}">
            <a href="{{ route('admin.setting.notification.global') }}" class="menu-link">
              <i class="menu-icon icon-base las la-bell"></i>
              <div data-i18n="Notif Set">@lang('Notification Settings')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.extensions.index') }}">
            <a href="{{ route('admin.extensions.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-puzzle-piece"></i>
              <div data-i18n="Extensions">@lang('Extensions')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.setting.social.login') }}">
            <a href="{{ route('admin.setting.social.login') }}" class="menu-link">
              <i class="menu-icon icon-base lab la-google"></i>
              <div data-i18n="Social Login">@lang('Social Logins')</div>
            </a>
          </li>
        </ul>
      </li>

      <!-- 9. Sections (Direct Access) -->
      @php
        $sectionsRoutes = [
          'admin.frontend.sections.header.index',
          'admin.frontend.sections.headericons',
          'admin.frontend.sections.icon',
          'admin.frontend.sections.general',
          'admin.frontend.sections.userprofile',
          'admin.frontend.sections.social_icon',
          'admin.frontend.sections.banner',
          'admin.frontend.sections.scrollbar',
          'admin.frontend.sections.ticker',
          'admin.frontend.sections.contact',
          'admin.frontend.sections.footer',
          'admin.frontend.sections.login',
          'admin.frontend.sections.policy',
          'admin.frontend.sections.register',
          'admin.frontend.sections.service',
        ];
      @endphp
      <li class="menu-item {{ menuActive($sectionsRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-puzzle-piece"></i>
          <div data-i18n="Sections">@lang('Sections')</div>
        </a>
        <ul class="menu-sub menu-sub-scrollable">
          <li class="menu-item {{ menuActive('admin.frontend.sections.icon') }}">
            <a href="{{ route('admin.frontend.sections.icon') }}" class="menu-link">
              <i class="menu-icon icon-base las la-image"></i>
              <div data-i18n="Logo">@lang('Logo & Favicon')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.general') }}"><a
              href="{{ route('admin.frontend.sections.general') }}" class="menu-link">@lang('General')</a></li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.userprofile') }}"><a
              href="{{ route('admin.frontend.sections.userprofile') }}"
              class="menu-link">@lang('User Profile Control')</a></li>
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Homepage Content')</li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.scrollbar*') }}">
            <a href="{{ route('admin.frontend.sections.scrollbar') }}" class="menu-link">
              <i class="menu-icon icon-base las la-arrows-alt-h"></i>
              <div data-i18n="Scroll Bar">@lang('Scroll Bar')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.ticker') }}">
            <a href="{{ route('admin.frontend.sections.ticker') }}" class="menu-link">
              <i class="menu-icon icon-base las la-bullhorn"></i>
              <div data-i18n="Ticker">@lang('News Ticker')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.social_icon') }}">
            <a href="{{ route('admin.frontend.sections.social_icon') }}" class="menu-link">
              <i class="menu-icon icon-base las la-share-alt"></i>
              <div data-i18n="Social Icons">@lang('Social Icons')</div>
            </a>
          </li>
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Page Sections')</li>
          @foreach (getPageSections(true) as $k => $secs)
            @if ($secs['builder'] && !in_array($k, $sectionsMenuExcludeKeys, true))
              @php
                $routeName = $routeMapping[$k] ?? 'admin.frontend.sections';
                $routeParams = isset($routeMapping[$k]) ? [] : ['key' => $k];
                $secIcon = $manageSectionIconMap[$k] ?? 'las la-puzzle-piece';
              @endphp
              <li class="menu-item {{ menuActive($routeName) }}">
                <a href="{{ route($routeName, $routeParams) }}" class="menu-link">
                  <i class="menu-icon icon-base {{ $secIcon }}"></i>
                  <div data-i18n="{{ $secs['name'] }}">{{ __($secs['name']) }}</div>
                </a>
              </li>
            @endif
          @endforeach
        </ul>
      </li>

      <!-- 13. Frontend & System -->
      @php
        $finalRoutes = [
          'admin.maintenance*',
          'admin.system*',
          'admin.setting.custom.css',
          'admin.request.report',
          'admin.setting.cookie',
          'admin.frontend.templates',
        ];
      @endphp
      <li class="menu-item menu-item--align-end {{ menuActive($finalRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-desktop"></i>
          <div data-i18n="Frontend">@lang('Frontend')</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ menuActive('admin.frontend.templates') }}">
            <a href="{{ route('admin.frontend.templates') }}" class="menu-link">
              <i class="menu-icon icon-base las la-file-code"></i>
              <div data-i18n="Templates">@lang('Manage Templates')</div>
            </a>
          </li>
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Compliance & Extra')</li>
          <li class="menu-item {{ menuActive('admin.setting.cookie') }}">
            <a href="{{ route('admin.setting.cookie') }}" class="menu-link">
              <i class="menu-icon icon-base las la-cookie-bite"></i>
              <div data-i18n="Cookie">@lang('GDPR Cookie')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.maintenance.mode') }}">
            <a href="{{ route('admin.maintenance.mode') }}" class="menu-link">
              <i class="menu-icon icon-base las la-robot"></i>
              <div data-i18n="Maint">@lang('Maintenance Mode')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive(['admin.maintenance.*', 'admin.system.*']) ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
              <i class="menu-icon icon-base la la-server"></i>
              <div data-i18n="Sys Tools">@lang('System Tools')</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ menuActive('admin.maintenance.dashboard') }}"><a
                  href="{{ route('admin.maintenance.dashboard') }}" class="menu-link">@lang('Maintenance Hub')</a></li>
              <li class="menu-item {{ menuActive('admin.system.info') }}"><a href="{{ route('admin.system.info') }}"
                  class="menu-link">@lang('Application Info')</a></li>
              <li class="menu-item {{ menuActive('admin.system.server.info') }}"><a
                  href="{{ route('admin.system.server.info') }}" class="menu-link">@lang('Server Information')</a></li>
              <li class="menu-item {{ menuActive('admin.system.optimize') }}"><a
                  href="{{ route('admin.system.optimize') }}" class="menu-link">@lang('Cache Manager')</a></li>
            </ul>
          </li>
          <li class="menu-item {{ menuActive('admin.setting.custom.css') }}">
            <a href="{{ route('admin.setting.custom.css') }}" class="menu-link">
              <i class="menu-icon icon-base lab la-css3-alt"></i>
              <div data-i18n="CSS">@lang('Custom CSS Editor')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.request.report') }}">
            <a href="{{ route('admin.request.report') }}" class="menu-link">
              <i class="menu-icon icon-base las la-bug"></i>
              <div data-i18n="Bug">@lang('Report Bug/Request')</div>
            </a>
          </li>
        </ul>
      </li>

    </ul>
    </div>
  </div>
</aside>