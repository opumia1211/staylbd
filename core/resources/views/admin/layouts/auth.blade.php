<!doctype html>
<html lang="en" class="layout-wide customizer-hide" dir="ltr" data-skin="default" data-assets-path="{{ asset('assets/admin-ui') }}/" data-template="horizontal-menu-template" data-bs-theme="light">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ ($general ?? null) ? $general->siteName($pageTitle ?? '') : ($pageTitle ?? 'Admin') }}</title>
    
    @php 
      $adminFavicon = getLogo('favicon'); 
      // Robust Fix for subdirectory asset pathing
      $baseUrl = url('/');
      $assetBase = str_replace('/core/public', '', $baseUrl);
      if (str_ends_with($assetBase, '/')) {
          $assetBase = rtrim($assetBase, '/');
      }
    @endphp
    <link rel="icon" type="image/x-icon" href="{{ str_replace(url('/') . '/', $assetBase . '/', $adminFavicon) }}" />

    <!-- Fonts: Standardizing on Local System Fonts -->

    <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="{{ $assetBase }}/assets/global/css/line-awesome.min.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/css/core.css" />
    <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ $assetBase }}/assets/admin-ui/vendor/css/pages/page-auth.css" />

    <!-- Tailwind CSS for utility bits -->
    <script src="{{ $assetBase }}/assets/global/js/tailwind.min.js"></script>

    <!-- Helpers -->
    <script src="{{ $assetBase }}/assets/admin-ui/vendor/js/helpers.js"></script>
    <script src="{{ $assetBase }}/assets/admin-ui/js/config.js"></script>
    
    @include('partials.storefront_ui_variables')
    <style>
      :root {
        --bs-font-sans-serif: 'Inter', system-ui, -apple-system, sans-serif !important;
        --bs-body-font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
      }
      body {
        font-family: 'Inter', sans-serif !important;
        background: #f8fafc !important;
      }
      /* Compact Card Overrides */
      .authentication-basic .authentication-inner {
        max-width: 360px !important;
      }
      .card {
        border-radius: 12px !important;
        border: 1px solid rgba(0,0,0,0.05) !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important;
      }
      .card-body {
        padding: 1.5rem !important;
      }
      .form-control {
        padding: 0.55rem 0.85rem !important;
        border-radius: 8px !important;
      }
      .btn-primary {
        background-color: #0e9f90 !important;
        border-color: #0e9f90 !important;
        padding: 0.6rem !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
      }
    </style>
    
    @stack('style')
  </head>

  <body>
    <!-- Content -->
    @yield('content')
    <!-- / Content -->

    <!-- Core JS -->
    <script src="{{ $assetBase }}/assets/admin-ui/vendor/libs/jquery/jquery.js"></script>
    <script src="{{ $assetBase }}/assets/admin-ui/vendor/libs/popper/popper.js"></script>
    <script src="{{ $assetBase }}/assets/admin-ui/vendor/js/bootstrap.js"></script>
    <script src="{{ $assetBase }}/assets/admin-ui/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{{ $assetBase }}/assets/admin-ui/vendor/js/menu.js"></script>

    <!-- Main JS -->
    <script src="{{ $assetBase }}/assets/admin-ui/js/main.js"></script>

    @include('partials.notify')
    @stack('script')
  </body>
</html>
