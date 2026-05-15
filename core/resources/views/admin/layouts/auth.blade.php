<!doctype html>
<html lang="en" class="layout-wide customizer-hide" dir="ltr" data-skin="default" data-assets-path="{{ asset('assets/admin-ui') }}/" data-template="horizontal-menu-template" data-bs-theme="light">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ ($general ?? null) ? $general->siteName($pageTitle ?? '') : ($pageTitle ?? 'Admin') }}</title>
    
    @php $adminFavicon = getLogo('favicon'); @endphp
    <link rel="icon" type="image/x-icon" href="{{ $adminFavicon }}" />

    <!-- Fonts: Standardizing on Outfit (Brand) and Inter (UI) -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/admin-ui/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin-ui/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin-ui/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin-ui/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin-ui/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/admin-ui/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/admin-ui/js/config.js') }}"></script>
    
    @include('partials.storefront_ui_variables')
    <style>
      :root {
        --bs-font-sans-serif: 'Outfit', 'Inter', system-ui, -apple-system, sans-serif !important;
        --bs-body-font-family: 'Outfit', 'Inter', system-ui, -apple-system, sans-serif !important;
      }
      body {
        font-family: 'Outfit', 'Inter', sans-serif !important;
        background: radial-gradient(120% 90% at 0% 0%, #dbeafe 0%, rgba(219,234,254,0) 42%),
                    radial-gradient(140% 100% at 100% 0%, #ccfbf1 0%, rgba(204,251,241,0) 44%),
                    #f3f7fb !important;
      }
      .card {
        border: 1px solid rgba(148, 163, 184, 0.2) !important;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08) !important;
        backdrop-filter: blur(10px);
      }
      .btn-primary {
        background-color: var(--product-buy-now-color, #0e9f90) !important;
        border-color: var(--product-buy-now-color, #0e9f90) !important;
      }
      .btn-primary:hover {
        background-color: var(--product-buy-now-hover, #0c8a7d) !important;
        border-color: var(--product-buy-now-hover, #0c8a7d) !important;
      }
    </style>
    
    @stack('style')
  </head>

  <body>
    <!-- Content -->
    @yield('content')
    <!-- / Content -->

    <!-- Core JS -->
    <script src="{{ asset('assets/admin-ui/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/admin-ui/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/admin-ui/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/admin-ui/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/admin-ui/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/admin-ui/js/main.js') }}"></script>

    @include('partials.notify')
    @stack('script')
  </body>
</html>
