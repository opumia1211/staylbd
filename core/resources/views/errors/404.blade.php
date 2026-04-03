<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $general->siteName($pageTitle ?? '404 | page not found') }}</title>
  @php $errFavicon = getLogo('favicon'); @endphp
  @if($errFavicon)
  <link rel="icon" type="image/x-icon" href="{{ $errFavicon }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ $errFavicon }}">
  @else
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/default.png') }}">
  @endif
  <!-- bootstrap 4  -->
  <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}">
  <!-- dashdoard main css -->
  <link rel="stylesheet" href="{{ asset('assets/errors/css/main.css') }}">
</head>
  <body>


  <!-- error-404 start -->
  <div class="error">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-7 text-center">
          <img src="{{ asset('assets/errors/images/error-404.png') }}" alt="@lang('image')">
          <h2><b>@lang('404')</b> @lang('Page not found')</h2>
          <p>@lang('page you are looking for doesn\'t exit or an other error ocurred') <br> @lang('or temporarily unavailable.')</p>
          <div class="mt-4">
            <a href="{{ route('home') }}" class="cmn-btn">@lang('GO TO HOME')</a>
            @auth
            <a href="{{ route('user.home') }}" class="btn btn-outline-secondary ml-2 mt-2">@lang('Dashboard')</a>
            <a href="{{ route('user.order.index') }}" class="btn btn-outline-secondary ml-2 mt-2">@lang('My Orders')</a>
            <a href="{{ route('user.cart') }}" class="btn btn-outline-secondary ml-2 mt-2">@lang('Cart')</a>
            @else
            <a href="{{ route('user.login') }}" class="btn btn-outline-secondary ml-2 mt-2">@lang('Login')</a>
            <a href="{{ route('products') }}" class="btn btn-outline-secondary ml-2 mt-2">@lang('Shop')</a>
            @endauth
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- error-404 end -->


  </body>
</html>
