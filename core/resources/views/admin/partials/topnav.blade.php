<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="container-xxl">

    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
      <a href="{{ route('admin.dashboard') }}" class="app-brand-link gap-2">
        <span class="app-brand-logo demo">
          <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="logo" class="w-px-40">
        </span>
        <span class="app-brand-text demo menu-text fw-bold text-heading">{{ $general->site_name }}</span>
      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
        <i class="icon-base bx bx-chevron-left d-flex align-items-center justify-content-center"></i>
      </a>
    </div>

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
      <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
        <i class="icon-base bx bx-menu icon-md"></i>
      </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
      <ul class="navbar-nav flex-row align-items-center ms-md-auto">

        <!-- Search -->
        <li class="nav-item navbar-search-wrapper me-2 me-xl-0">
          <div class="admin-header-search-wrapper position-relative">
            <div class="input-group input-group-merge">
              <span class="input-group-text border-0 bg-transparent ps-0" id="admin-search-addon">
                <i class="icon-base bx bx-search icon-md admin-header-search-icon"></i>
                <i class="icon-base las la-spinner la-spin icon-md admin-header-search-loader d-none"></i>
              </span>
              <input type="text" class="form-control border-0 bg-transparent shadow-none" 
                     id="adminHeaderSearchInput" 
                     placeholder="@lang('Search menu, products, users...')" 
                     aria-label="Search admin panel" 
                     aria-describedby="admin-search-addon">
            </div>
            <div id="adminHeaderSearchResults" class="search-results-pane border-0 shadow-lg"></div>
          </div>
        </li>
        <!-- /Search -->

        <!-- Cache Clear -->
        <li class="nav-item dropdown me-2 me-xl-0">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
            data-bs-auto-close="outside" title="@lang('Clear cache safely')">
            <i class="icon-base las la-broom icon-md" style="font-size: 1.5rem;"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-end p-2 border-0 shadow-sm">
            <div class="dropdown-header d-flex align-items-center py-2">
              <h6 class="mb-0 me-auto">@lang('Cache Manager')</h6>
            </div>
            <div class="dropdown-divider"></div>
            <a href="{{ route('admin.system.optimize') }}" class="dropdown-item d-flex align-items-center py-2">
              <i class="icon-base las la-info-circle me-2 text-primary" style="font-size: 1.2rem;"></i>
              <span>@lang('Cache options')</span>
            </a>
            <a href="{{ route('admin.system.optimize.clear') }}"
              class="dropdown-item d-flex align-items-center py-2 text-success">
              <i class="icon-base las la-check-double me-2" style="font-size: 1.2rem;"></i>
              <span>@lang('Safe Clear')</span>
              <small class="text-muted ms-1">(@lang('Recommended'))</small>
            </a>
            <a href="{{ route('admin.system.optimize.clear.full') }}"
              class="dropdown-item d-flex align-items-center py-2 text-danger">
              <i class="icon-base las la-trash-alt me-2" style="font-size: 1.2rem;"></i>
              <span>@lang('Full Clear')</span>
            </a>
          </div>
        </li>
        <!-- /Cache Clear -->

        <!-- Language -->
        <li class="nav-item me-2 me-xl-0">
          <a class="nav-item nav-link px-0" href="{{ route('admin.language.manage') }}" title="@lang('Language')">
            <i class="icon-base bx bx-globe icon-md"></i>
          </a>
        </li>
        <!-- /Language -->

        <!-- Quick links (System Optimizations) -->
        <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
            data-bs-auto-close="outside">
            <i class="icon-base bx bx-grid-alt icon-md"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-end p-0">
            <div class="dropdown-menu-header border-bottom">
              <div class="dropdown-header d-flex align-items-center py-3">
                <h6 class="mb-0 me-auto">@lang('System Shortcuts')</h6>
                <a href="javascript:void(0)" class="dropdown-shortcuts-add py-2" data-bs-toggle="tooltip"
                  data-bs-placement="top" title="@lang('Add shortcuts')"><i
                    class="icon-base bx bx-plus-circle text-heading"></i></a>
              </div>
            </div>
            <div class="dropdown-shortcuts-list scrollable-container">
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col text-success">
                  <span class="dropdown-shortcuts-icon rounded-circle mb-3 bg-label-success">
                    <i class="icon-base bx bx-check-double icon-26px"></i>
                  </span>
                  <a href="{{ route('admin.system.optimize.clear') }}" class="stretched-link">@lang('Safe Clear')</a>
                  <small>@lang('Recommended')</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                    <i class="icon-base bx bx-info-circle icon-26px text-heading"></i>
                  </span>
                  <a href="{{ route('admin.system.optimize') }}" class="stretched-link">@lang('Cache options')</a>
                  <small>@lang('Optimizations')</small>
                </div>
              </div>
              <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col text-danger">
                  <span class="dropdown-shortcuts-icon rounded-circle mb-3 bg-label-danger">
                    <i class="icon-base bx bx-trash icon-26px"></i>
                  </span>
                  <a href="{{ route('admin.system.optimize.clear.full') }}"
                    class="stretched-link">@lang('Full Clear')</a>
                  <small>@lang('Deep Cleaning')</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                  <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                    <i class="icon-base bx bx-bar-chart icon-26px text-heading"></i>
                  </span>
                  <a href="{{ route('admin.report.activity.dashboard') }}" class="stretched-link">@lang('Analytics')</a>
                  <small>@lang('Search Reports')</small>
                </div>
              </div>
            </div>
          </div>
        </li>
        <!-- / Quick links -->

        <!-- Style Switcher -->
        <li class="nav-item dropdown me-2 me-xl-0">
          <a class="nav-link dropdown-toggle hide-arrow" id="nav-theme" href="javascript:void(0);"
            data-bs-toggle="dropdown">
            <i class="icon-base bx bx-sun icon-md theme-icon-active"></i>
            <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
            <li>
              <button type="button" class="dropdown-item align-items-center active" data-bs-theme-value="light">
                <span><i class="icon-base bx bx-sun icon-md me-3" data-icon="sun"></i>Light</span>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark">
                <span><i class="icon-base bx bx-moon icon-md me-3" data-icon="moon"></i>Dark</span>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system">
                <span><i class="icon-base bx bx-desktop icon-md me-3" data-icon="desktop"></i>System</span>
              </button>
            </li>
          </ul>
        </li>
        <!-- / Style Switcher-->

        <!-- Notification -->
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
            data-bs-auto-close="outside">
            <span class="position-relative">
              <i class="icon-base bx bx-bell icon-md"></i>
              @if($adminNotificationCount > 0)
                <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
              @endif
            </span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end p-0">
            <li class="dropdown-menu-header border-bottom">
              <div class="dropdown-header d-flex align-items-center py-3">
                <h6 class="mb-0 me-auto">@lang('Notification')</h6>
                <div class="d-flex align-items-center h6 mb-0">
                  @if($adminNotificationCount > 0)
                    <span class="badge bg-label-primary me-2">{{ $adminNotificationCount }} @lang('New')</span>
                  @endif
                  <a href="javascript:void(0)" class="dropdown-notifications-all p-2" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="@lang('Mark all as read')"><i
                      class="icon-base bx bx-envelope-open text-heading"></i></a>
                </div>
              </div>
            </li>
            <li class="dropdown-notifications-list scrollable-container">
              <ul class="list-group list-group-flush">
                @forelse($adminNotifications as $notification)
                  <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                          <span class="avatar-initial rounded-circle bg-label-primary"><i
                              class="icon-base bx bx-bell"></i></span>
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="small mb-0">{{ __($notification->title) }}</h6>
                        <small class="text-body-secondary">{{ $notification->created_at->diffForHumans() }}</small>
                      </div>
                      <div class="flex-shrink-0 dropdown-notifications-actions">
                        <a href="{{ route('admin.notification.read', $notification->id) }}"
                          class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                      </div>
                    </div>
                  </li>
                @empty
                  <li class="list-group-item text-center p-4">
                    <small class="text-muted">@lang('No unread notification found')</small>
                  </li>
                @endforelse
              </ul>
            </li>
            <li class="border-top">
              <div class="d-grid p-4">
                <a class="btn btn-primary btn-sm d-flex" href="{{ route('admin.notifications') }}">
                  <small class="align-middle">@lang('View all notification')</small>
                </a>
              </div>
            </li>
          </ul>
        </li>
        <!--/ Notification -->

        <!-- User -->
        <li class="nav-item">
          <a class="nav-link p-0" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd">
            <div class="avatar avatar-online">
              <img src="{{ getImage(getFilePath('adminProfile') . '/' . auth()->guard('admin')->user()->image) }}" alt
                class="rounded-circle" />
            </div>
          </a>
        </li>
        <!--/ User -->

      </ul>
    </div>
  </div>
</nav>

<!-- User Profile Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
  <div class="offcanvas-header border-bottom">
    <h5 id="offcanvasEndLabel" class="offcanvas-title text-heading"><i class="bx bx-user me-2"></i> @lang('User Profile')</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body mx-0 flex-grow-0 p-0">
    <div class="p-6 text-center border-bottom bg-lighter">
      <div class="avatar avatar-xl avatar-online mb-4 mx-auto">
        <img src="{{ getImage(getFilePath('adminProfile') . '/' . auth()->guard('admin')->user()->image) }}"
          alt class="rounded-circle" />
      </div>
      <h5 class="mb-0 text-heading">{{ auth()->guard('admin')->user()->username }}</h5>
      <small class="text-body-secondary text-uppercase">@lang('Administrator')</small>
    </div>
    
    <div class="list-group list-group-flush pt-2">
      <a href="{{ route('admin.profile') }}" class="list-group-item list-group-item-action d-flex align-items-center border-0 px-6 py-3">
        <i class="icon-base bx bx-user icon-md me-3"></i>
        <span>@lang('My Profile')</span>
      </a>
      <a href="{{ route('admin.password') }}" class="list-group-item list-group-item-action d-flex align-items-center border-0 px-6 py-3">
        <i class="icon-base bx bx-lock-open-alt icon-md me-3"></i>
        <span>@lang('Security & Password')</span>
      </a>
    </div>

    <div class="p-6 mt-2 border-top">
      <a class="btn btn-label-danger d-grid w-100" href="{{ route('admin.logout') }}">
        <span class="d-flex align-items-center justify-content-center">
          <i class="icon-base bx bx-power-off icon-sm me-2"></i> @lang('Sign Out')
        </span>
      </a>
    </div>
  </div>
</div>