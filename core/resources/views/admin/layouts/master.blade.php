<!doctype html>
<html lang="en" class="admin-panel-root layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-skin="default"
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

  @include('partials.inter-font-preload', ['interPreloadMode' => 'admin-heavy'])

  <!-- Tailwind CSS Library Integration -->
  <script src="{{ $assetBase }}/assets/global/js/tailwind.min.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              teal: '#0e9f90',
              hover: '#0c8a7d',
            }
          },
          fontFamily: {
            inter: ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/fonts/iconify-icons.css" />
  <link rel="stylesheet" href="{{ $assetBase }}/assets/global/css/line-awesome.min.css">
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin/css/vendor/datepicker.min.css">

  <!-- Core CSS -->
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/libs/pickr/pickr-themes.css" />
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/css/core.css" />
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/css/demo.css" />
  <link rel="stylesheet" href="{{ $assetBase }}/core/public/css/admin-menu-horizontal.css?v=stayl101">
  <link rel="stylesheet" href="{{ $assetBase }}/core/public/css/admin-layout-fix.css?v=stayl101">
  <link rel="stylesheet" href="{{ $assetBase }}/assets/admin/css/admin-light-contrast.css?v=4">
  <link rel="stylesheet" href="{{ route('serve.css.admin.panel') }}?v=stayl101">

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
      z-index: 1115 !important;
      min-width: 230px !important;
      list-style: none !important;
      margin: 0 !important;
      pointer-events: auto !important;
      transition: none !important;
      animation: none !important;
    }

    /* Instant open on hover (no Sneat delay) */
    .layout-menu-horizontal.menu-no-animation .menu-inner > .menu-item:hover > .menu-sub,
    .layout-menu-horizontal.menu-no-animation .menu-inner > .menu-item.open > .menu-sub {
      display: flex !important;
      flex-direction: column !important;
    }

    #layout-menu .menu-horizontal-wrapper {
      scrollbar-width: none;
      -ms-overflow-style: none;
    }

    #layout-menu .menu-horizontal-wrapper::-webkit-scrollbar {
      display: none;
      height: 0;
    }

    .layout-wrapper.layout-horizontal .content-wrapper,
    .layout-wrapper.layout-horizontal .container-p-y {
      position: relative;
      z-index: 1;
    }

    /* Long dropdowns (Catalog, Sections): scroll so all items visible at 100% zoom */
    .layout-menu-horizontal .menu-sub.menu-sub-scrollable {
      max-height: min(70vh, 26rem) !important;
      overflow-y: auto !important;
      overflow-x: hidden !important;
      overscroll-behavior: contain;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: thin;
      scrollbar-color: rgba(14, 159, 144, 0.55) #eef1f4;
    }

    .layout-menu-horizontal .menu-sub.menu-sub-scrollable::-webkit-scrollbar {
      width: 8px;
    }

    .layout-menu-horizontal .menu-sub.menu-sub-scrollable::-webkit-scrollbar-track {
      background: #eef1f4;
      border-radius: 4px;
      margin: 4px 0;
    }

    .layout-menu-horizontal .menu-sub.menu-sub-scrollable::-webkit-scrollbar-thumb {
      background: rgba(14, 159, 144, 0.45);
      border-radius: 4px;
    }

    .layout-menu-horizontal .menu-sub.menu-sub-scrollable::-webkit-scrollbar-thumb:hover {
      background: rgba(14, 159, 144, 0.75);
    }

    .layout-menu-horizontal .menu-sub .menu-link {
      color: #435971 !important;
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
      color: #a1acb8 !important;
      font-weight: 700 !important;
      font-size: 0.75rem !important;
      text-transform: uppercase !important;
      letter-spacing: 0.8px !important;
      padding: 0.85rem 1.25rem 0.35rem !important;
      opacity: 0.8 !important;
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

  </style>

  @stack('style-lib')
  @stack('style')

  {{-- Must load last: removes gap below fixed menubar (overrides Sneat core + demo) --}}
  <style id="stayl-admin-header-gap-fix">
    .layout-wrapper.layout-horizontal .layout-container {
      display: block !important;
      padding-top: 0 !important;
    }
    .layout-wrapper.layout-horizontal .layout-page {
      margin-top: calc(4rem + 3.25rem) !important;
      padding-top: 0 !important;
    }
    .layout-wrapper.layout-horizontal #layout-navbar {
      position: fixed !important;
      top: 0 !important;
      height: 4rem !important;
      max-height: 4rem !important;
      margin: 0 !important;
    }
    .layout-wrapper.layout-horizontal #layout-menu {
      position: fixed !important;
      top: 4rem !important;
      height: 3.25rem !important;
      max-height: 3.25rem !important;
      margin: 0 !important;
      padding: 0 !important;
    }
    .layout-wrapper.layout-horizontal .layout-page > .content-wrapper {
      margin-top: 0 !important;
      padding-top: 0 !important;
      justify-content: flex-start !important;
    }
    .layout-wrapper.layout-horizontal .content-wrapper > .container-xxl {
      padding-top: 0.5rem !important;
      margin-top: 0 !important;
    }
    .container-p-y:not([class^="pt-"]):not([class*=" pt-"]) {
      padding-top: 0.5rem !important;
    }
    .layout-horizontal .layout-page .menu-horizontal + .content-wrapper,
    .layout-horizontal .layout-page > .menu-horizontal + .content-wrapper {
      padding-top: 0 !important;
      margin-top: 0 !important;
    }
  </style>
</head>

<body class="admin-panel-body font-inter antialiased">

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
  <script>
    (function staylFixAdminHeaderGap() {
      function applyGapFix() {
        var wrap = document.querySelector('.layout-wrapper.layout-horizontal');
        var page = wrap && wrap.querySelector('.layout-page');
        var nav = document.getElementById('layout-navbar');
        var menu = document.getElementById('layout-menu');
        if (!wrap || !page || !nav || !menu) return;
        var navH = 64;
        var menuH = 52;
        nav.style.setProperty('position', 'fixed', 'important');
        nav.style.setProperty('top', '0', 'important');
        nav.style.setProperty('height', navH + 'px', 'important');
        nav.style.setProperty('max-height', navH + 'px', 'important');
        nav.style.setProperty('margin', '0', 'important');
        menu.style.setProperty('position', 'fixed', 'important');
        menu.style.setProperty('top', navH + 'px', 'important');
        menu.style.setProperty('height', menuH + 'px', 'important');
        menu.style.setProperty('max-height', menuH + 'px', 'important');
        menu.style.setProperty('margin', '0', 'important');
        menu.style.setProperty('padding', '0', 'important');
        
        var targetTop = navH + menuH;
        page.style.setProperty('margin-top', targetTop + 'px', 'important');
        page.style.setProperty('padding-top', '0', 'important');
        
        // Measure and compensate for any offset/shift (e.g. margin collapsing or theme offset)
        var currentTop = page.getBoundingClientRect().top;
        if (currentTop !== targetTop) {
          var neededMargin = targetTop + (targetTop - currentTop);
          page.style.setProperty('margin-top', neededMargin + 'px', 'important');
        }
        
        var cw = page.querySelector(':scope > .content-wrapper');
        if (cw) {
          cw.style.setProperty('margin-top', '0', 'important');
          cw.style.setProperty('padding-top', '0', 'important');
        }
        var container = page.querySelector('.content-wrapper > .container-xxl');
        if (container) {
          container.style.setProperty('margin-top', '0', 'important');
          container.style.setProperty('padding-top', '8px', 'important');
        }
        
        // Temporary Diagnostics
        (function showDiagnostics() {
          var nav = document.getElementById('layout-navbar');
          var menu = document.getElementById('layout-menu');
          var pageEl = document.querySelector('.layout-page');
          var cw = document.querySelector('.content-wrapper');
          var containerEl = document.querySelector('.content-wrapper > .container-xxl');
          var header = document.querySelector('.admin-page-header');
          
          var getStyle = function(el, prop) {
            return el ? window.getComputedStyle(el)[prop] : 'N/A';
          };
          
          var getRect = function(el) {
            if (!el) return 'N/A';
            var r = el.getBoundingClientRect();
            return 'h:' + Math.round(r.height) + ' t:' + Math.round(r.top) + ' b:' + Math.round(r.bottom);
          };
          
          var data = {
            url: window.location.href,
            html_class: document.documentElement.className,
            body_class: document.body.className,
            nav: getRect(nav) + ' (h:' + getStyle(nav, 'height') + ' pos:' + getStyle(nav, 'position') + ')',
            menu: getRect(menu) + ' (h:' + getStyle(menu, 'height') + ' pos:' + getStyle(menu, 'position') + ')',
            page: getRect(pageEl) + ' (mt:' + getStyle(pageEl, 'marginTop') + ' pt:' + getStyle(pageEl, 'paddingTop') + ')',
            cw: getRect(cw) + ' (mt:' + getStyle(cw, 'marginTop') + ' pt:' + getStyle(cw, 'paddingTop') + ')',
            container: getRect(containerEl) + ' (mt:' + getStyle(containerEl, 'marginTop') + ' pt:' + getStyle(containerEl, 'paddingTop') + ')',
            header: getRect(header) + ' (mt:' + getStyle(header, 'marginTop') + ' pt:' + getStyle(header, 'paddingTop') + ')'
          };
          
          fetch('/staylbd/core/public/debug_layout.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
          }).catch(function(err) {});
        })();
      }
      applyGapFix();
      document.addEventListener('DOMContentLoaded', applyGapFix);
      window.addEventListener('load', applyGapFix);
      window.addEventListener('resize', applyGapFix);
    })();

    (function staylHorizontalMenu() {
      var closeTimer = null;

      function setWrapperOpen(layoutMenu, on) {
        var wrap = layoutMenu.querySelector('.menu-horizontal-wrapper');
        if (wrap) wrap.classList.toggle('is-dropdown-open', on);
      }

      function openItem(item, allItems) {
        if (!item) return;
        clearTimeout(closeTimer);
        for (var i = 0; i < allItems.length; i++) {
          if (allItems[i] !== item) allItems[i].classList.remove('open');
        }
        item.classList.add('open');
        setWrapperOpen(item.closest('#layout-menu'), true);
      }

      function closeAllItems(allItems, layoutMenu) {
        for (var i = 0; i < allItems.length; i++) allItems[i].classList.remove('open');
        setWrapperOpen(layoutMenu, false);
      }

      function keepItemOpen(item, topItems, layoutMenu) {
        clearTimeout(closeTimer);
        openItem(item, topItems);
        setWrapperOpen(layoutMenu, true);
      }

      function scheduleCloseItem(item, topItems, layoutMenu) {
        closeTimer = setTimeout(function () {
          if (item.matches(':hover') || item.querySelector(':scope > .menu-sub:hover')) return;
          item.classList.remove('open');
          var stillHover = layoutMenu.querySelector('.menu-inner > .menu-item:hover');
          setWrapperOpen(layoutMenu, !!stillHover || !!layoutMenu.querySelector('.menu-inner > .menu-item.open'));
        }, 100);
      }

      function setupInstantHover(layoutMenu, inner) {
        if (inner._staylInstantHover) return;
        inner._staylInstantHover = true;
        var topItems = inner.querySelectorAll(':scope > .menu-item');
        var alignEndFrom = Math.max(0, topItems.length - 4);
        topItems.forEach(function (item, idx) {
          if (idx >= alignEndFrom) item.classList.add('menu-item--align-end');
          var toggle = item.querySelector(':scope > .menu-link.menu-toggle');
          if (!toggle) return;
          var sub = item.querySelector(':scope > .menu-sub');
          item.addEventListener('mouseenter', function () { keepItemOpen(item, topItems, layoutMenu); });
          item.addEventListener('mouseleave', function () { scheduleCloseItem(item, topItems, layoutMenu); });
          if (sub) {
            sub.addEventListener('mouseenter', function () { keepItemOpen(item, topItems, layoutMenu); });
            sub.addEventListener('mouseleave', function () { scheduleCloseItem(item, topItems, layoutMenu); });
          }
        });
        layoutMenu.addEventListener('mouseleave', function () {
          closeTimer = setTimeout(function () { closeAllItems(topItems, layoutMenu); }, 150);
        });
      }

      function setupDropdownLinkClicks(layoutMenu) {
        if (layoutMenu._staylDropdownLinks) return;
        layoutMenu._staylDropdownLinks = true;
        layoutMenu.addEventListener('click', function (e) {
          var link = e.target.closest('.menu-inner > .menu-item > .menu-sub .menu-link[href]');
          if (!link || link.getAttribute('href') === 'javascript:void(0)') return;
          var topItem = link.closest('.menu-inner > .menu-item');
          if (topItem) topItem.classList.remove('open');
          setWrapperOpen(layoutMenu, false);
        });
      }

      function apply() {
        var layoutMenu = document.getElementById('layout-menu');
        if (!layoutMenu || !layoutMenu.classList.contains('menu-horizontal')) return;

        layoutMenu.classList.add('menu-no-animation');
        layoutMenu.querySelectorAll('.menu-horizontal-prev, .menu-horizontal-next').forEach(function (el) {
          el.style.display = 'none';
        });

        var wrap = layoutMenu.querySelector('.menu-horizontal-wrapper');
        var inner = layoutMenu.querySelector('.menu-inner');
        if (wrap) {
          wrap.style.scrollbarWidth = 'none';
          wrap.style.msOverflowStyle = 'none';
        }
        if (inner) {
          inner.style.marginLeft = '0';
          inner.style.marginRight = '0';
          inner.style.transform = 'none';
          if (inner._ps && typeof inner._ps.destroy === 'function') {
            try { inner._ps.destroy(); } catch (e) { /* ignore */ }
          }
          inner.classList.remove('ps', 'ps--active-y', 'ps--active-x');
          setupInstantHover(layoutMenu, inner);
          setupDropdownLinkClicks(layoutMenu);
        }

        var menuApi = layoutMenu.menuInstance || (window.Helpers && window.Helpers.mainMenu);
        if (menuApi) {
          menuApi._animate = false;
          menuApi._showDropdownOnHover = false;
          if (menuApi._inner) {
            menuApi._inner.style.marginLeft = '0';
            menuApi._inner.style.marginRight = '0';
          }
          if (!menuApi._staylPatchedUpdate) {
            menuApi._staylPatchedUpdate = true;
            var origUpdate = menuApi.update.bind(menuApi);
            menuApi.update = function () {
              if (menuApi._horizontal) {
                layoutMenu.querySelectorAll('.menu-horizontal-prev, .menu-horizontal-next').forEach(function (el) {
                  el.style.display = 'none';
                });
                if (menuApi._inner) {
                  menuApi._inner.style.marginLeft = '0';
                  menuApi._inner.style.marginRight = '0';
                }
                return;
              }
              return origUpdate();
            };
          }
        }

        if (window.Helpers && !window.Helpers._staylHorizontalMenuScrollPatch) {
          window.Helpers._staylHorizontalMenuScrollPatch = true;
          var origScroll = window.Helpers.scrollToActive;
          window.Helpers.scrollToActive = function () {
            var menu = document.getElementById('layout-menu');
            if (menu && menu.classList.contains('menu-horizontal')) {
              var innerEl = menu.querySelector('.menu-inner');
              if (innerEl) {
                innerEl.style.marginLeft = '0';
                innerEl.style.marginRight = '0';
              }
              return;
            }
            return origScroll.apply(this, arguments);
          };
        }
      }

      apply();
      document.addEventListener('DOMContentLoaded', function () {
        apply();
        setTimeout(apply, 400);
      });
      window.addEventListener('load', apply);
    })();
  </script>
</body>

</html>
