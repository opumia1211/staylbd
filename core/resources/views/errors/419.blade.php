<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $general->siteName($pageTitle ?? '419 | Session has expired') }}</title>
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
          <img src="{{ asset('assets/errors/images/error-419.png') }}" alt="@lang('image')">
          <h2><b>@lang('419')</b> @lang('Sorry your session has expired.')</h2>
          <p>@lang('Please go back and refresh your browser and try again')</p>
          @if(str_contains(config('app.url'), '/staylbd') || request()->getBasePath())
          <p class="small text-muted mt-2">@lang('If you use a subdirectory (e.g. /staylbd), add') <code>SESSION_PATH=/staylbd</code> @lang('in .env and run') <code>php artisan config:clear</code>.</p>
          @endif
          <div class="mt-4" style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center;">
            <a href="javascript:void(0)" class="cmn-btn" onclick="if(history.length>1){history.back();setTimeout(function(){location.reload();},300)}else{location.href='{{ route('admin.dashboard') }}'}">@lang('Go Back & Refresh')</a>
            <a href="{{ route('admin.frontend.sections.icon') }}" class="cmn-btn" style="background:#6c757d;color:#fff;">@lang('Logo Settings')</a>
            <a href="{{ route('admin.dashboard') }}" class="cmn-btn" style="background:#6c757d;color:#fff;">@lang('Admin Dashboard')</a>
            <a href="{{ route('home') }}" class="cmn-btn">@lang('Go to Home')</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- error-404 end -->


  </body>
</html>
