<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact " dir="ltr" data-skin="default"
  data-assets-path="{{ url('assets/admin-ui') }}/" data-template="horizontal-menu-template" data-bs-theme="light">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>{{ $general->siteName($pageTitle ?? '') }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @php 
    $adminFavicon = getLogo('favicon'); 
    // Fix for subdirectory asset pathing
    $assetBase = str_replace('/core/public', '', url('/'));
    if (str_ends_with($assetBase, '/')) {
        $assetBase = rtrim($assetBase, '/');
    }
  @endphp
  <link rel="icon" type="image/x-icon" href="{{ $adminFavicon }}" />

  @php
    $adminAssetVer = config('app.asset_version', '1');
    try {
        $adminTwUtilities = mix('css/admin-tailwind-utilities.css');
    } catch (\Throwable $e) {
        $adminTwUtilities = asset('css/admin-tailwind-utilities.css') . '?v=' . $adminAssetVer;
    }
  @endphp
  @include('partials.inter-font-preload', ['assetVersion' => $adminAssetVer, 'interPreloadMode' => 'admin-heavy'])

  <script>
    (function () {
      try {
        var key = 'admin-bs-theme';
        var stored = localStorage.getItem(key) || 'light';
        var dark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        var resolved = stored === 'system' ? (dark ? 'dark' : 'light') : stored;
        document.documentElement.setAttribute('data-bs-theme', resolved);
      } catch (e) { /* ignore */ }
    })();
  </script>

  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/fonts/iconify-icons.css" />
  <link rel="stylesheet" href="{{ $assetBase }}/assets/global/css/line-awesome.min.css">
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin/css/vendor/datepicker.min.css">

  <!-- Core CSS -->
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/libs/pickr/pickr-themes.css" />
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/css/core.css" />
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/css/demo.css" />
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin/css/admin-light-contrast.css?v={{ $adminAssetVer }}" />
  {{-- Local Tailwind utilities (compiled, preflight off) — after Sneat so layout stays intact --}}
  <link rel="stylesheet" href="{{ $adminTwUtilities }}">

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/fonts/flag-icons.css" />
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/libs/apex-charts/apex-charts.css" />
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/libs/select2/select2.css">
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/libs/sweetalert2/sweetalert2.css">
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/libs/animate-css/animate.css">

  <!-- Helpers -->
  <script src="{{ $assetBase }}/assets/admin-ui/vendor/js/helpers.js"></script>
  <script src="{{ $assetBase }}/assets/admin-ui/vendor/js/template-customizer.js"></script>
  <script src="{{ $assetBase }}/assets/admin-ui/js/config.js"></script>

  @include('partials.storefront_ui_variables')
  <style>
    :root {
      --bs-font-sans-serif: 'Inter', system-ui, -apple-system, sans-serif !important;
      --bs-body-font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
    }

    body {
      font-family: 'Inter', sans-serif !important;
    }

    .layout-navbar {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.8) !important;
    }

    .btn-primary {
      background-color: var(--product-buy-now-color, #0e9f90) !important;
      border-color: var(--product-buy-now-color, #0e9f90) !important;
    }

    .btn-primary:hover {
      background-color: var(--product-buy-now-hover, #0c8a7d) !important;
      border-color: var(--product-buy-now-hover, #0c8a7d) !important;
    }

    .bg-menu-theme {
      background-color: #ffffff !important;
      color: #566a7f !important;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05) !important;
      border-bottom: 1px solid rgba(67, 89, 113, 0.1) !important;
    }

    .bg-menu-theme .menu-link,
    .bg-menu-theme .menu-header {
      color: #566a7f !important;
    }

    .bg-menu-theme .menu-item.active>.menu-link {
      background: rgba(14, 159, 144, 0.08) !important;
      color: #0e9f90 !important;
      border-radius: 6px !important;
    }

    .bg-menu-theme .menu-item.active>.menu-link i {
      color: #0e9f90 !important;
    }

    /* Remove the dot pseudo-elements that appear in some theme versions */
    .layout-menu-horizontal .menu-link::before,
    .layout-menu-horizontal .menu-sub .menu-link::before {
      display: none !important;
      content: none !important;
    }

    /* Search Results Styling */
    .admin-header-search-wrapper {
      min-width: 300px;
    }

    .search-results-pane {
      position: absolute;
      top: 100%;
      left: 0;
      width: 450px;
      max-height: 500px;
      overflow-y: auto;
      background: #fff;
      z-index: 1090;
      border-radius: 0.5rem;
      margin-top: 0.5rem;
      display: none;
      scrollbar-width: thin;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .search-results-pane.show {
      display: block;
    }

    .search-result-item {
      padding: 12px 15px;
      cursor: pointer;
      display: flex;
      align-items: center;
      transition: background 0.2s;
      border-bottom: 1px solid #f1f5f9;
    }

    .search-result-item:hover {
      background: #f1f5f9;
    }

    /* Submenu Card Styling - Clean & Sparkling */
    .layout-menu-horizontal .menu-sub {
      background-color: #ffffff !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12) !important;
      border: 1px solid rgba(67, 89, 113, 0.1) !important;
      border-radius: 8px !important;
      padding: 0.5rem 0 !important;
      z-index: 1100 !important;
      min-width: 230px !important;
      list-style: none !important;
      margin: 0 !important;
    }

    .layout-menu-horizontal .menu-inner > .menu-item > .menu-sub,
    .layout-menu-horizontal .menu-sub.menu-sub--scroll,
    .layout-menu-horizontal .menu-item.menu-payment-panel > .menu-sub,
    .layout-menu-horizontal .menu-item.menu-finance-panel > .menu-sub {
      max-height: min(72vh, 520px) !important;
      overflow-x: hidden !important;
      overflow-y: auto !important;
      overscroll-behavior: contain;
    }

    /* Orders / Payment long menus: wider + readable sections */
    .layout-menu-horizontal .menu-sub.menu-orders-panel,
    .layout-menu-horizontal .menu-item.menu-payment-panel > .menu-sub {
      min-width: 16.5rem !important;
    }

    .layout-menu-horizontal .menu-sub.menu-orders-panel {
      max-height: min(78vh, 600px) !important;
    }

    /* Inside scroll dropdowns: nested submenus stack vertically (no right flyout) */
    .layout-menu-horizontal .menu-sub.menu-sub--scroll .menu-item > .menu-sub {
      display: none !important;
      position: static !important;
      left: auto !important;
      top: auto !important;
      margin: 0 0 0.35rem 0 !important;
      padding: 0.25rem 0 0.25rem 0.75rem !important;
      box-shadow: none !important;
      border: none !important;
      border-left: 2px solid rgba(14, 159, 144, 0.4) !important;
      border-radius: 0 !important;
      background: rgba(14, 159, 144, 0.04) !important;
      min-width: 100% !important;
    }

    .layout-menu-horizontal .menu-sub.menu-sub--scroll .menu-item.open > .menu-sub,
    .layout-menu-horizontal .menu-sub.menu-sub--scroll .menu-item.active.open > .menu-sub {
      display: block !important;
    }

    .layout-menu-horizontal .menu-sub.menu-sub--scroll .menu-sub .menu-link {
      padding: 0.5rem 1rem 0.5rem 1.15rem !important;
      font-size: 0.875rem !important;
    }

    .layout-menu-horizontal .menu-sub .menu-item-header:first-child,
    .layout-menu-horizontal .menu-sub .menu-section-label:first-child {
      border-top: none !important;
      margin-top: 0 !important;
    }

    .layout-menu-horizontal .menu-sub .menu-item:not(.active) > .menu-link {
      color: #384551 !important;
    }

    .layout-menu-horizontal .menu-sub .menu-item.active > .menu-link {
      color: #0a6b62 !important;
      font-weight: 600 !important;
      background-color: rgba(14, 159, 144, 0.12) !important;
    }

    .layout-menu-horizontal .menu-sub .menu-item-header,
    .layout-menu-horizontal .menu-sub .menu-section-label {
      color: #384551 !important;
      opacity: 1 !important;
      font-weight: 700 !important;
      font-size: 0.7rem !important;
      letter-spacing: 0.06em !important;
      border-top: 1px solid rgba(67, 89, 113, 0.12);
      background: linear-gradient(180deg, #f8f9fb 0%, transparent 100%);
    }

    .layout-menu-horizontal .menu-sub .menu-link {
      color: #384551 !important;
      padding: 0.65rem 1.25rem !important;
      font-weight: 500 !important;
      font-size: 0.9375rem !important;
      display: flex !important;
      align-items: center !important;
      transition: background 0.2s, color 0.2s !important;
    }

    .layout-menu-horizontal .menu-sub .menu-link:hover {
      background-color: #f5f5f9 !important;
      color: #0e9f90 !important;
    }

    .layout-menu-horizontal .menu-sub .menu-icon {
      color: #697a8d !important;
      margin-right: 0.85rem !important;
      font-size: 1.25rem !important;
      width: 1.25rem !important;
      text-align: center !important;
    }
    
    .layout-menu-horizontal .menu-sub .menu-item-header {
      padding: 0.75rem 1.25rem 0.35rem !important;
      text-transform: uppercase !important;
    }

    .layout-menu-horizontal .menu-sub .menu-toggle::after {
      color: #a1acb8 !important;
    }

    /* Perfect Alignment & Orientation - No Overlapping, No Upside-Down */
    .layout-menu-horizontal .menu-link.menu-toggle {
      display: flex !important;
      flex-direction: row !important;
      align-items: center !important;
      justify-content: flex-start !important;
      position: relative !important;
      padding-right: 1.25rem !important;
      white-space: nowrap !important;
    }

    .layout-menu-horizontal .menu-toggle::after {
      content: "" !important;
      display: inline-block !important;
      position: static !important;
      margin-left: 0.65rem !important;
      width: 12px !important;
      height: 12px !important;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23566a7f' stroke-width='4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") !important;
      background-repeat: no-repeat !important;
      background-size: contain !important;
      transform: translateY(1px) !important; /* Fine-tune vertical alignment with text */
      transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
      opacity: 0.8 !important;
      flex-shrink: 0 !important;
      border: none !important;
    }

    /* Only rotate when the dropdown is actually OPEN */
    .layout-menu-horizontal .menu-item.open > .menu-link.menu-toggle::after {
      transform: translateY(1px) rotate(-180deg) !important;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230e9f90' stroke-width='4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") !important;
      opacity: 1 !important;
    }

    /* Keep color teal on active items but keep arrow DOWN if not open */
    .layout-menu-horizontal .menu-item.active > .menu-link.menu-toggle {
      color: #0e9f90 !important;
    }
    
    .layout-menu-horizontal .menu-item.active > .menu-link.menu-toggle::after {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230e9f90' stroke-width='4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") !important;
      opacity: 1 !important;
    }

    /* Badge dot alignment */
    .layout-menu-horizontal .menu-link .badge-dot {
      margin-left: 0.5rem !important;
      margin-right: -0.2rem !important;
      position: static !important;
      display: inline-block !important;
      vertical-align: middle !important;
    }

    .layout-menu-horizontal .menu-inner > .menu-item.active > .menu-link {
      background: transparent !important;
      color: #0e9f90 !important;
      font-weight: 700 !important;
    }

    .layout-menu-horizontal .menu-inner > .menu-item.active > .menu-link i {
      color: #0e9f90 !important;
    }
    
    /* Hover effect for top-level items on white background */
    .layout-menu-horizontal .menu-inner > .menu-item:hover > .menu-link {
      background: rgba(14, 159, 144, 0.05) !important;
      color: #0e9f90 !important;
      border-radius: 6px !important;
    }

    .layout-menu-horizontal .menu-inner > .menu-item:hover > .menu-link i {
      color: #0e9f90 !important;
    }

    .layout-menu-horizontal .menu-inner > .menu-item > .menu-link i {
      color: #566a7f !important;
    }

    /* Fix Clipping of last menu item (Frontend) */
    .layout-menu-horizontal .menu-inner {
      overflow: visible !important;
    }

    .layout-menu-horizontal .menu-inner > .menu-item > .menu-link {
      white-space: nowrap !important;
      min-width: max-content !important;
    }

    /* Fix Nested Sub-menus (Sections, Sys Tools) in Horizontal Layout */
    .layout-menu-horizontal .menu-sub .menu-item {
      position: relative !important;
    }

    .layout-menu-horizontal .menu-sub .menu-sub {
      display: none !important;
      position: absolute !important;
      left: 100% !important;
      top: 0 !important;
      z-index: 1200 !important;
      margin-left: 0.1rem !important;
    }

    .layout-menu-horizontal .menu-sub .menu-item:hover > .menu-sub {
      display: block !important;
    }

    /* Correct Centering - Max Width 1920px for content containers only */
    .container-xxl {
      max-width: 1920px !important;
      margin-left: auto !important;
      margin-right: auto !important;
    }

    /* Handle Menu wrapping/overflow for standard desktops */
    @media (min-width: 1200px) {
      .layout-menu-horizontal .menu-inner {
        width: 100% !important;
        display: flex !important;
        justify-content: flex-start !important;
        flex-wrap: nowrap !important;
      }
      
      .layout-menu-horizontal .menu-inner > .menu-item > .menu-link {
        padding-left: 0.9rem !important;
        padding-right: 0.9rem !important;
      }
    }

    /* Adjustments for smaller laptops to prevent wrapping */
    @media (min-width: 1200px) and (max-width: 1600px) {
      .layout-menu-horizontal .menu-inner > .menu-item > .menu-link {
        padding-left: 0.6rem !important;
        padding-right: 0.6rem !important;
        font-size: 0.85rem !important;
      }
      .layout-menu-horizontal .menu-inner > .menu-item > .menu-link .menu-icon {
        margin-right: 0.4rem !important;
        font-size: 1.15rem !important;
      }
    }

    /* Smooth transition for responsive adjustments */
    .menu-link, .container-xxl {
      transition: all 0.2s ease !important;
    }

    [data-bs-theme="dark"] .layout-navbar {
      background: rgba(43, 44, 64, 0.95) !important;
    }

    [data-bs-theme="dark"] .bg-menu-theme {
      background-color: #2b2c40 !important;
      color: #e7e9f1 !important;
    }

    [data-bs-theme="dark"] .layout-menu-horizontal .menu-sub {
      background-color: #32334a !important;
      border-color: rgba(255, 255, 255, 0.08) !important;
    }

    [data-bs-theme="dark"] .layout-menu-horizontal .menu-sub .menu-item:not(.active) > .menu-link {
      color: #e7e9f1 !important;
    }

    [data-bs-theme="dark"] .layout-menu-horizontal .menu-sub .menu-item-header,
    [data-bs-theme="dark"] .layout-menu-horizontal .menu-sub .menu-section-label {
      color: #d5d7de !important;
    }

    [data-bs-theme="dark"] .search-results-pane {
      background: #32334a !important;
      color: #e7e9f1 !important;
    }
  </style>

  @stack('style-lib')
  @stack('style')
</head>

<body>

  @yield('content')

  <div class="layout-overlay layout-menu-toggle"></div>
  <div class="drag-target"></div>

  <!-- Core JS -->
  <script src="{{ url('assets/admin-ui/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ url('assets/admin-ui/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ url('assets/admin-ui/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ url('assets/admin-ui/vendor/libs/@algolia/autocomplete-js.js') }}"></script>
  <script src="{{ url('assets/admin-ui/vendor/libs/pickr/pickr.js') }}"></script>
  <script src="{{ url('assets/admin-ui/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ url('assets/admin-ui/vendor/libs/hammer/hammer.js') }}"></script>
  <script src="{{ url('assets/admin-ui/vendor/libs/i18n/i18n.js') }}"></script>
  <script src="{{ url('assets/admin-ui/vendor/libs/typeahead-js/typeahead.js') }}"></script>
  <script src="{{ url('assets/admin-ui/vendor/js/menu.js') }}"></script>

  <!-- Vendors JS -->
  <script src="{{ url('assets/admin-ui/vendor/libs/apex-charts/apexcharts.js') }}"></script>
  <script src="{{ url('assets/admin-ui/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{ url('assets/admin-ui/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>

  <!-- Main JS -->
  <script src="{{ url('assets/admin-ui/js/main.js') }}?v={{ time() }}"></script>

  <script>window.adminSearchUrl = "{{ route('admin.search.index') }}";</script>

  @include('partials.notify')
  @stack('script-lib')
  @stack('script')

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var themeKey = 'admin-bs-theme';
      var htmlEl = document.documentElement;

      function resolveTheme(mode) {
        if (mode === 'system' && window.matchMedia) {
          return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        return mode === 'dark' ? 'dark' : 'light';
      }

      function applyTheme(mode) {
        var resolved = resolveTheme(mode);
        htmlEl.setAttribute('data-bs-theme', resolved);
        document.querySelectorAll('[data-bs-theme-value]').forEach(function (btn) {
          btn.classList.toggle('active', btn.getAttribute('data-bs-theme-value') === mode);
        });
        var icon = document.querySelector('#nav-theme .theme-icon-active');
        if (icon) {
          icon.classList.remove('bx-sun', 'bx-moon', 'bx-desktop');
          if (mode === 'system') {
            icon.classList.add('bx-desktop');
          } else if (resolved === 'dark') {
            icon.classList.add('bx-moon');
          } else {
            icon.classList.add('bx-sun');
          }
        }
      }

      document.querySelectorAll('[data-bs-theme-value]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var mode = btn.getAttribute('data-bs-theme-value') || 'light';
          try { localStorage.setItem(themeKey, mode); } catch (e) { /* ignore */ }
          applyTheme(mode);
        });
      });

      try {
        var saved = localStorage.getItem(themeKey) || htmlEl.getAttribute('data-bs-theme') || 'light';
        applyTheme(saved);
      } catch (e) {
        applyTheme('light');
      }

      if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
          try {
            if (localStorage.getItem(themeKey) === 'system') {
              applyTheme('system');
            }
          } catch (err) { /* ignore */ }
        });
      }

      setInterval(function () {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (!meta) return;
        fetch('{{ route('admin.session.keepalive') }}', { credentials: 'same-origin' })
          .then(function (r) { return r.json(); })
          .then(function (d) { if (d.csrf) meta.content = d.csrf; })
          .catch(function () { });
      }, 90000);
    });
  </script>
</body>

</html>
