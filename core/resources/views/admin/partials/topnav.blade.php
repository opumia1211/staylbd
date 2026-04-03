<!-- navbar-wrapper start -->
<nav class="navbar-wrapper bg--dark">
    <div class="navbar__left">
        <ul class="navbar__action-list d-flex align-items-center">
            <li>
                <button type="button" class="res-sidebar-open-btn primary--layer me-3 d-block" aria-label="Toggle sidebar">
                    <i class="las la-bars" style="color: #1a1a1a !important; opacity: 1 !important; visibility: visible !important;"></i>
                </button>
            </li>
            <li>
                <a href="{{ route('admin.language.manage') }}" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center" aria-label="Languages">
                    <i class="las la-globe"></i> @lang('Language')
                </a>
            </li>
            <li class="dropdown">
                <button type="button" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('Clear cache safely - logo, favicon and settings will work correctly')">
                    <i class="las la-broom me-2"></i> @lang('Clear Cache')
                    <i class="las la-chevron-down ms-1 small"></i>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-2 border-0 box--shadow1">
                    <a href="{{ route('admin.system.optimize') }}" class="dropdown-item d-flex align-items-center px-3 py-2">
                        <i class="las la-info-circle text--primary me-2"></i>
                        <span>@lang('Cache options')</span>
                    </a>
                    <div class="dropdown-divider my-2"></div>
                    <a href="{{ route('admin.system.optimize.clear') }}" class="dropdown-item d-flex align-items-center px-3 py-2 text-success">
                        <i class="las la-check-double me-2"></i>
                        <span>@lang('Safe Clear')</span>
                        <small class="text-muted ms-1">(@lang('Recommended'))</small>
                    </a>
                    <a href="{{ route('admin.system.optimize.clear.full') }}" class="dropdown-item d-flex align-items-center px-3 py-2 text-muted">
                        <i class="las la-cog me-2"></i>
                        <span>@lang('Full Clear')</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
    <div class="navbar__center">
        <div class="admin-header-search-wrapper">
            <form class="admin-header-search-form" id="adminHeaderSearchForm">
                <div class="position-relative">
                    <input type="search" 
                           class="admin-header-search-field" 
                           id="adminHeaderSearchInput" 
                           autocomplete="off" 
                           placeholder="@lang('Search everything: products, users, orders, settings, features, posts...')"
                           aria-label="Search">
                    <i class="las la-search admin-header-search-icon"></i>
                    <div class="admin-header-search-loader d-none">
                        <i class="las la-spinner la-spin"></i>
                    </div>
                </div>
                <div class="admin-header-search-results" id="adminHeaderSearchResults"></div>
            </form>
        </div>
    </div>
    <div class="navbar__right">
        <ul class="navbar__action-list">

            <li class="dropdown">
                <button type="button" class="primary--layer position-relative admin-notification-bell" data-bs-toggle="dropdown" data-display="static"
                    aria-haspopup="true" aria-expanded="false" aria-label="@lang('Notifications')">
                    <i class="las la-bell text--primary @if($adminNotificationCount > 0) icon-left-right @endif" style="color: #4634ff !important; opacity: 1 !important; visibility: visible !important;"></i>
                    @if($adminNotificationCount > 0)
                        <span class="admin-notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-2 border-light">{{ $adminNotificationCount > 99 ? '99+' : $adminNotificationCount }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu--md p-0 border-0 box--shadow1 dropdown-menu-right">
                    <div class="dropdown-menu__header">
                        <span class="caption">@lang('Notification')</span>
                        @if($adminNotificationCount > 0)
                            <p>@lang('You have') {{ $adminNotificationCount }} @lang('unread notification')</p>
                        @else
                            <p>@lang('No unread notification found')</p>
                        @endif
                    </div>
                    <div class="dropdown-menu__body">
                        @forelse($adminNotifications as $notification)
                            <a href="{{ route('admin.notification.read', $notification->id) }}"
                                class="dropdown-menu__item text-decoration-none admin-notification-item">
                                <div class="navbar-notifi">
                                    <div class="navbar-notifi__right">
                                        <h6 class="notifi__title mb-0">{{ __($notification->title) }}</h6>
                                        <span class="time"><i class="far fa-clock"></i>
                                            {{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="dropdown-menu__item text-muted small">@lang('No unread notification found')</div>
                        @endforelse
                    </div>
                    <div class="dropdown-menu__footer">
                        <a href="{{ route('admin.notifications') }}"
                            class="view-all-message">@lang('View all notification')</a>
                    </div>
                </div>
            </li>


            <li class="dropdown">
                <button type="button" class="" data-bs-toggle="dropdown" data-display="static" aria-haspopup="true"
                    aria-expanded="false">
                    <span class="navbar-user">
                        <span class="navbar-user__thumb"><img
                                src="{{ auth()->guard('admin')->user()->image ? getImageWebP(getFilePath('adminProfile') . '/' . auth()->guard('admin')->user()->image, getFileSize('adminProfile')) : getImage('', '200x200') }}"
                                alt="@lang('Profile')"></span>
                        <span class="navbar-user__info">
                            <span
                                class="navbar-user__name" style="color: #1a1a1a !important; opacity: 1 !important; visibility: visible !important;">{{ auth()->guard('admin')->user()->username }}</span>
                        </span>
                        <span class="icon"><i class="las la-chevron-circle-down" style="color: #1a1a1a !important; opacity: 1 !important; visibility: visible !important;"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    <a href="{{ route('admin.profile') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-user-circle"></i>
                        <span class="dropdown-menu__caption">@lang('Profile')</span>
                    </a>

                    <a href="{{ route('admin.password') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-key"></i>
                        <span class="dropdown-menu__caption">@lang('Password')</span>
                    </a>

                    <a href="{{ route('admin.logout') }}"
                        class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                        <span class="dropdown-menu__caption">@lang('Logout')</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- navbar-wrapper end -->
