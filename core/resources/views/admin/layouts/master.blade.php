<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact " dir="ltr" data-skin="default"
  data-assets-path="{{ url('assets/admin-ui') }}/" data-template="horizontal-menu-template" data-bs-theme="light">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>{{ $general->siteName($pageTitle ?? '') }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @php $adminFavicon = getLogo('favicon'); @endphp
  <link rel="icon" type="image/x-icon" href="{{ $adminFavicon }}" />

  <!-- Fonts: Standardizing on Outfit (Brand) and Inter (UI) -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="{{ url('assets/admin-ui/vendor/fonts/iconify-icons.css') }}" />
  <link rel="stylesheet" href="{{ url('assets/global/css/line-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ url('assets/admin/css/vendor/datepicker.min.css') }}">

  <!-- Core CSS -->
  <link rel="stylesheet" href="{{ url('assets/admin-ui/vendor/libs/pickr/pickr-themes.css') }}" />
  <link rel="stylesheet" href="{{ url('assets/admin-ui/vendor/css/core.css') }}" />
  <link rel="stylesheet" href="{{ url('assets/admin-ui/css/demo.css') }}" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="{{ url('assets/admin-ui/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
  <link rel="stylesheet" href="{{ url('assets/admin-ui/vendor/fonts/flag-icons.css') }}" />
  <link rel="stylesheet" href="{{ url('assets/admin-ui/vendor/libs/apex-charts/apex-charts.css') }}" />
  <link rel="stylesheet" href="{{ url('assets/admin-ui/vendor/libs/select2/select2.css') }}">
  <link rel="stylesheet" href="{{ url('assets/admin-ui/vendor/libs/sweetalert2/sweetalert2.css') }}">
  <link rel="stylesheet" href="{{ url('assets/admin-ui/vendor/libs/animate-css/animate.css') }}">

  <!-- Helpers -->
  <script src="{{ url('assets/admin-ui/vendor/js/helpers.js') }}"></script>
  <script src="{{ url('assets/admin-ui/vendor/js/template-customizer.js') }}"></script>
  <script src="{{ url('assets/admin-ui/js/config.js') }}"></script>

  @include('partials.storefront_ui_variables')
  <style>
    :root {
      --bs-font-sans-serif: 'Outfit', 'Inter', system-ui, -apple-system, sans-serif !important;
      --bs-body-font-family: 'Outfit', 'Inter', system-ui, -apple-system, sans-serif !important;
    }
    body {
      font-family: 'Outfit', 'Inter', sans-serif !important;
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
      background-color: #0f172a !important; /* Matching footer_bg from storefront */
      color: #fff !important;
    }
    .bg-menu-theme .menu-link, .bg-menu-theme .menu-header {
      color: rgba(255, 255, 255, 0.7) !important;
    }
    .bg-menu-theme .menu-item.active > .menu-link {
      background: var(--product-buy-now-color, #0e9f90) !important;
      color: #fff !important;
    }

    /* Search Results Styling */
    .admin-header-search-wrapper { min-width: 300px; }
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
    }
    .search-results-pane.show { display: block; }
    .search-results-category {
      padding: 10px 15px;
      background: #f8f9fa;
      font-weight: 700;
      font-size: 11px;
      text-uppercase: uppercase;
      color: #6c757d;
      border-bottom: 1px solid #edf2f7;
    }
    .search-result-item {
      padding: 12px 15px;
      cursor: pointer;
      display: flex;
      align-items: center;
      transition: all 0.2s;
      border-bottom: 1px solid #f1f5f9;
    }
    .search-result-item:hover, .search-result-item.active {
      background: #f1f5f9;
    }
    .search-result-icon {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(14, 159, 144, 0.1);
      color: #0e9f90;
      border-radius: 6px;
      margin-right: 12px;
    }
    .search-result-title { font-weight: 600; font-size: 14px; color: #334155; }
    .search-result-description { font-size: 12px; color: #64748b; margin-bottom: 0; }
    .search-result-url { font-size: 10px; color: #94a3b8; display: block; }
    .search-no-results { padding: 30px; text-align: center; color: #64748b; }
    .highlight { background: #fef08a; padding: 0 2px; border-radius: 2px; }
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
  document.addEventListener('DOMContentLoaded', function() {
      setInterval(function() {
          var meta = document.querySelector('meta[name="csrf-token"]');
          if (!meta) return;
          fetch('{{ route('admin.session.keepalive') }}', { credentials: 'same-origin' })
              .then(function(r) { return r.json(); })
              .then(function(d) { if (d.csrf) meta.content = d.csrf; })
              .catch(function() {});
      }, 90000);
  });
  </script>
</body>

</html>
