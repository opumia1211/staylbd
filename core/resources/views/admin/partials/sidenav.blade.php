@php
  $adminUser = auth()->guard('admin')->user();
  $canAccess = function ($section) use ($adminUser) {
    return !$adminUser || $adminUser->canAccessSection($section);
  };

  // Mapping for dynamic sections to specific routes if they exist
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
  ];

  // Notification counts for badges
  $failedCourierOrders = 0;
  try {
    $failedCourierOrders = \DB::table('courier_logs')->where('status', 'failed')->count();
  } catch (\Exception $e) {
  }
@endphp

<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu flex-grow-0">
  <div class="container-xxl d-flex h-100">
    <ul class="menu-inner">

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

      <!-- 2. Home Layout (Spotlights) -->
      @php
        $homeRoutes = [
          'admin.frontend.sections.homepage',
          'admin.frontend.sections.homepageCustomRows*',
          'admin.frontend.sections.homepageAds*',
          'admin.frontend.sections.banner*',
          'admin.frontend.sections.scrollbar*',
          'admin.product.todayDeal',
          'admin.product.hot',
          'admin.product.feature.index',
          'admin.product.trending',
          'admin.product.bestSelling',
          'admin.frontend.sections.ticker'
        ];
      @endphp
      <li class="menu-item {{ menuActive($homeRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-desktop"></i>
          <div data-i18n="Home">@lang('Home Layout')</div>
        </a>
        <ul class="menu-sub">
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
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Dynamic Banners')</li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.banner*') }}">
            <a href="{{ route('admin.frontend.sections.banner') }}" class="menu-link">
              <i class="menu-icon icon-base las la-image"></i>
              <div data-i18n="Banners">@lang('Banner Management')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.scrollbar*') }}">
            <a href="{{ route('admin.frontend.sections.scrollbar') }}" class="menu-link">
              <i class="menu-icon icon-base las la-arrows-alt-h"></i>
              <div data-i18n="Scroll Bar">@lang('Scroll Bar')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.ticker') }}">
            <a href="{{ route('admin.frontend.sections.ticker') }}" class="menu-link">
              <i class="menu-icon icon-base las la-ellipsis-h"></i>
              <div data-i18n="Ticker">@lang('News Ticker')</div>
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
        </ul>
      </li>

      <!-- 3. Catalog & Products -->
      @php
        $catalogRoutes = [
          'admin.product*',
          'admin.category*',
          'admin.subcategory*',
          'admin.brand*',
          'admin.attributes*',
          'admin.coupon*',
          'admin.offer-timers*',
          'admin.popup-ads*',
          'admin.category.attributes*',
          'admin.product.topbar.*'
        ];
      @endphp
      <li class="menu-item {{ menuActive($catalogRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-tshirt"></i>
          <div data-i18n="Catalog">@lang('Products')</div>
        </a>
        <ul class="menu-sub">
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
          <li class="menu-item {{ menuActive('admin.product.reviews.index') }}">
            <a href="{{ route('admin.product.reviews.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-comment-dots"></i>
              <div data-i18n="Reviews">@lang('Product Reviews')</div>
            </a>
          </li>
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Inventory & Alerts')</li>
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
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Marketing Tools')</li>
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
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Organization')</li>
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

      <!-- 4. Operations (Orders & Logistics) -->
      @php
        $opsRoutes = [
          'admin.orders*',
          'admin.abandoned-orders*',
          'admin.notifications*',
          'admin.setting.stock.order.messages',
          'admin.shipping*',
          'admin.api.courier*',
          'admin.locations.*'
        ];
      @endphp
      <li class="menu-item {{ menuActive($opsRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-list-alt"></i>
          <div data-i18n="Operations">@lang('Operations')</div>
          @if($pendingOrderCount > 0 || $failedCourierOrders > 0) <span class="badge badge-dot bg-danger ms-2"></span>
          @endif
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ menuActive('admin.orders.index') }}">
            <a href="{{ route('admin.orders.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-list"></i>
              <div data-i18n="All Orders">@lang('All Orders')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.orders.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
              <i class="menu-icon icon-base las la-clock"></i>
              <div data-i18n="Order Status">@lang('Order Statuses')</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ menuActive('admin.orders.pending') }}"><a
                  href="{{ route('admin.orders.pending') }}" class="menu-link">@lang('Pending') @if($pendingOrderCount)
                  <span class="badge bg-danger ms-auto">{{ $pendingOrderCount }}</span> @endif</a></li>
              <li class="menu-item {{ menuActive('admin.orders.confirmed') }}"><a
                  href="{{ route('admin.orders.confirmed') }}" class="menu-link">@lang('Confirmed')</a></li>
              <li class="menu-item {{ menuActive('admin.orders.processing') }}"><a
                  href="{{ route('admin.orders.processing') }}" class="menu-link">@lang('Processing')</a></li>
              <li class="menu-item {{ menuActive('admin.orders.packaging') }}"><a
                  href="{{ route('admin.orders.packaging') }}" class="menu-link">@lang('Packaging')</a></li>
              <li class="menu-item {{ menuActive('admin.orders.shipped') }}"><a
                  href="{{ route('admin.orders.shipped') }}" class="menu-link">@lang('Shipped')</a></li>
              <li class="menu-item {{ menuActive('admin.orders.delivered') }}"><a
                  href="{{ route('admin.orders.delivered') }}" class="menu-link">@lang('Delivered')</a></li>
              <li class="menu-item {{ menuActive('admin.orders.cancel') }}"><a href="{{ route('admin.orders.cancel') }}"
                  class="menu-link">@lang('Canceled')</a></li>
            </ul>
          </li>
          <li class="menu-item {{ menuActive('admin.abandoned-orders.*') }}">
            <a href="{{ route('admin.abandoned-orders.index') }}" class="menu-link">
              <i class="menu-icon icon-base las la-shopping-cart"></i>
              <div data-i18n="Abandoned">@lang('Abandoned Carts')</div>
            </a>
          </li>
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Logistics & API')</li>
          <li class="menu-item {{ menuActive('admin.shipping.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
              <i class="menu-icon icon-base las la-truck-moving"></i>
              <div data-i18n="Shipping">@lang('Shipping')</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ menuActive('admin.shipping.index') }}"><a
                  href="{{ route('admin.shipping.index') }}" class="menu-link">@lang('Shipping Hub')</a></li>
              <li class="menu-item {{ menuActive('admin.shipping.zones*') }}"><a
                  href="{{ route('admin.shipping.zones.index') }}" class="menu-link">@lang('Zones')</a></li>
              <li class="menu-item {{ menuActive('admin.shipping.methods*') }}"><a
                  href="{{ route('admin.shipping.methods.index') }}" class="menu-link">@lang('Methods')</a></li>
              <li class="menu-item {{ menuActive('admin.shipping.rules*') }}"><a
                  href="{{ route('admin.shipping.rules.index') }}" class="menu-link">@lang('Rules')</a></li>
              <li class="menu-item {{ menuActive('admin.shipping.cod.index') }}"><a
                  href="{{ route('admin.shipping.cod.index') }}" class="menu-link">@lang('COD Settings')</a></li>
            </ul>
          </li>
          <li class="menu-item {{ menuActive('admin.api.courier.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
              <i class="menu-icon icon-base las la-truck"></i>
              <div data-i18n="Courier API">@lang('Courier API')</div>
              @if($failedCourierOrders > 0) <span class="badge bg-danger ms-2">{{ $failedCourierOrders }}</span> @endif
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ menuActive('admin.api.courier.manage') }}"><a
                  href="{{ route('admin.api.courier.manage') }}" class="menu-link">@lang('Courier Settings')</a></li>
              @foreach($activeCourierProviders ?? [] as $courierProvider)
                <li class="menu-item {{ menuActive('admin.orders.bulk.courier', null, $courierProvider->type) }}">
                  <a href="{{ route('admin.orders.bulk.courier', $courierProvider->type) }}" class="menu-link">
                    <i class="menu-icon icon-base las la-shipping-fast"></i>
                    <div data-i18n="Bulk">@lang('Bulk') ({{ $courierProvider->display_name }})</div>
                  </a>
                </li>
              @endforeach
              @if(empty($activeCourierProviders) || $activeCourierProviders->isEmpty())
                <li class="menu-item {{ menuActive('admin.api.courier.*') ? 'active' : '' }}"><a
                    href="{{ route('admin.orders.bulk.courier', 'pathao') }}" class="menu-link">@lang('Bulk') (Pathao)</a>
                </li>
                <li class="menu-item {{ menuActive('admin.api.courier.*') ? 'active' : '' }}"><a
                    href="{{ route('admin.orders.bulk.courier', 'steadfast') }}" class="menu-link">@lang('Bulk')
                    (Steadfast)</a></li>
              @endif
              <li class="menu-item {{ menuActive('admin.api.courier.logs') }}"><a
                  href="{{ route('admin.api.courier.logs') }}" class="menu-link">@lang('Courier Logs')
                  @if($failedCourierOrders > 0) <span class="badge bg-danger ms-auto">{{ $failedCourierOrders }}</span>
                  @endif</a></li>
              <li class="menu-item {{ menuActive('admin.api.courier.reports') }}"><a
                  href="{{ route('admin.api.courier.reports') }}" class="menu-link">@lang('Courier Reports')</a></li>
            </ul>
          </li>
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

      <!-- 5. Finance -->
      @php
        $financeRoutes = [
          'admin.gateway*',
          'admin.deposit*',
          'admin.payment.*'
        ];
      @endphp
      <li class="menu-item {{ menuActive($financeRoutes) ? 'active open' : '' }}">
        <a href="javascript:void(0)" class="menu-link menu-toggle">
          <i class="menu-icon icon-base las la-credit-card"></i>
          <div data-i18n="Finance">@lang('Finance')</div>
          @if($pendingDepositsCount > 0) <span class="badge badge-dot bg-danger ms-2"></span> @endif
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ menuActive('admin.payment.gateways.hub') }}">
            <a href="{{ route('admin.payment.gateways.hub') }}" class="menu-link">
              <i class="menu-icon icon-base las la-th-large"></i>
              <div data-i18n="Hub">@lang('Gateways Hub')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.payment.analytics') }}">
            <a href="{{ route('admin.payment.analytics') }}" class="menu-link">
              <i class="menu-icon icon-base las la-chart-line"></i>
              <div data-i18n="Pay Analytics">@lang('Payment Analytics')</div>
            </a>
          </li>
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Gateway Methods')</li>
          <li class="menu-item {{ menuActive('admin.gateway.automatic.*') }}"><a
              href="{{ route('admin.gateway.automatic.index') }}" class="menu-link">@lang('Automatic')</a></li>
          <li class="menu-item {{ menuActive('admin.gateway.manual.*') }}"><a
              href="{{ route('admin.gateway.manual.index') }}" class="menu-link">@lang('Manual')</a></li>
          <li class="menu-item {{ menuActive('admin.gateway.autopay.*') }}"><a
              href="{{ route('admin.gateway.autopay.index') }}" class="menu-link">@lang('Autopay')</a></li>
          <li class="menu-item-header small text-muted text-uppercase py-2 px-3">@lang('Payment History')</li>
          <li class="menu-item {{ menuActive('admin.deposit.list') }}"><a href="{{ route('admin.deposit.list') }}"
              class="menu-link">@lang('All Payments')</a></li>
          <li class="menu-item {{ menuActive('admin.deposit.pending') }}"><a href="{{ route('admin.deposit.pending') }}"
              class="menu-link">@lang('Pending') @if($pendingDepositsCount) <span
              class="badge bg-danger ms-auto">{{ $pendingDepositsCount }}</span> @endif</a></li>
          <li class="menu-item {{ menuActive('admin.deposit.approved') }}"><a
              href="{{ route('admin.deposit.approved') }}" class="menu-link">@lang('Approved')</a></li>
          <li class="menu-item {{ menuActive('admin.deposit.successful') }}"><a
              href="{{ route('admin.deposit.successful') }}" class="menu-link">@lang('Successful')</a></li>
          <li class="menu-item {{ menuActive('admin.deposit.rejected') }}"><a
              href="{{ route('admin.deposit.rejected') }}" class="menu-link">@lang('Rejected')</a></li>
          <li class="menu-item {{ menuActive('admin.deposit.initiated') }}"><a
              href="{{ route('admin.deposit.initiated') }}" class="menu-link">@lang('Initiated')</a></li>
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

      <!-- 9. Frontend & System -->
      @php
        $finalRoutes = [
          'admin.frontend.sections*',
          'admin.maintenance*',
          'admin.system*',
          'admin.setting.custom.css',
          'admin.request.report',
          'admin.setting.cookie',
          'admin.frontend.templates',
          'admin.frontend.quickorder'
        ];
      @endphp
      <li class="menu-item {{ menuActive($finalRoutes) ? 'active open' : '' }}">
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
          <li class="menu-item {{ menuActive('admin.frontend.sections.icon') }}">
            <a href="{{ route('admin.frontend.sections.icon') }}" class="menu-link">
              <i class="menu-icon icon-base las la-image"></i>
              <div data-i18n="Logo">@lang('Logo & Favicon')</div>
            </a>
          </li>
          <li class="menu-item {{ menuActive('admin.frontend.sections.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
              <i class="menu-icon icon-base las la-puzzle-piece"></i>
              <div data-i18n="Sections">@lang('Manage Sections')</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ menuActive('admin.frontend.sections.general') }}"><a
                  href="{{ route('admin.frontend.sections.general') }}" class="menu-link">@lang('General')</a></li>
              <li class="menu-item {{ menuActive('admin.frontend.sections.userprofile') }}"><a
                  href="{{ route('admin.frontend.sections.userprofile') }}"
                  class="menu-link">@lang('User Profile Control')</a></li>
              <li class="menu-item {{ menuActive('admin.frontend.quickorder') }}"><a
                  href="{{ route('admin.frontend.quickorder') }}" class="menu-link">@lang('Quick Order Page')</a></li>
              @foreach (getPageSections(true) as $k => $secs)
                @if ($secs['builder'] && $k !== 'social_icon')
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
</aside>