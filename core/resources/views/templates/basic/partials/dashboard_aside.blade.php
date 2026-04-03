<aside class="dashboard__sidebar">
    <div class="dashboard__logo">
        <span class="close-dashboard-sidebar d-lg-none">
            @include($activeTemplate . 'partials.icon', ['name' => 'times'])
        </span>
    </div>
    <div class="side__menu__area">
        <div class="side__menu__area-inner">
            @php
                $user = auth()->user();
                $userImagePath = $user && $user->image
                    ? getFilePath('userProfile') . '/' . $user->image
                    : getFilePath('default');
                $userImageSize = ($user && $user->image) ? getFileSize('userProfile') : null;
            @endphp
            <div class="dashboard__author">
                <div class="thumb">
                    @if($user)
                    <a href="{{ route('user.profile.setting') }}">
                        <img src="{{ getImage($userImagePath, $userImageSize) }}" alt="{{ $user->fullname ?? '' }}" loading="lazy" width="42" height="42">
                    </a>
                    @else
                    <a href="{{ route('user.login') }}">
                        <img src="{{ getImage($userImagePath, $userImageSize) }}" alt="@lang('Guest')" loading="lazy" width="42" height="42">
                    </a>
                    @endif
                </div>
                <div class="content">
                    @if($user)
                    <h6 class="title">
                        <a href="{{ route('user.profile.setting') }}" class="text--base">{{ $user->fullname ?? $user->username }}</a>
                    </h6>
                    <a href="{{ route('user.profile.setting') }}" class="text--base fz--14">@lang('@'){{ $user->username ?? '' }}</a>
                    @else
                    <h6 class="title">
                        <a href="{{ route('user.login') }}" class="text--base">@lang('Guest')</a>
                    </h6>
                    <a href="{{ route('user.login') }}" class="text--base fz--14">@lang('Login')</a>
                    @endif
                </div>
            </div>

            <ul class="side__menu">
                @auth
                {{-- Overview – লগইন থাকলেই শুধু দেখা যাবে --}}
                <li class="side__menu-title">@lang('Overview')</li>
                <li>
                    <a class="{{ menuActive('user.home') }}" href="{{ route('user.home') }}" data-dashboard-link="1">
                        @include($activeTemplate . 'partials.icon', ['name' => 'home']) <span class="cont">@lang('Dashboard')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.track.order') }}" href="{{ route('user.track.order') }}" data-dashboard-link="1">
                        @include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast']) <span class="cont">@lang('Track Order')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.notifications') }}" href="{{ route('user.notifications') }}" data-dashboard-link="1">
                        @include($activeTemplate . 'partials.icon', ['name' => 'bell']) <span class="cont">@lang('Notifications')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.order.index') }}" href="{{ route('user.order.index') }}" data-dashboard-link="1">
                        @include($activeTemplate . 'partials.icon', ['name' => 'shopping-bag']) <span class="cont">@lang('My Orders')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.transactions') }}" href="{{ route('user.transactions') }}" data-dashboard-link="1">
                        @include($activeTemplate . 'partials.icon', ['name' => 'money-bill-wave']) <span class="cont">@lang('Transactions History')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('message*') }}" href="{{ route('message.index') }}" data-dashboard-link="1">
                        @include($activeTemplate . 'partials.icon', ['name' => 'comments']) <span class="cont">@lang('My Messages')</span>
                    </a>
                </li>
                @endauth

                {{-- Shopping – গেস্ট ও লগইন দুজনেরই দেখা যাবে --}}
                <li class="side__menu-title mt-2">@lang('Shopping')</li>
                <li>
                    <a class="{{ menuActive('user.cart') }}" href="{{ route('user.cart') }}" data-dashboard-link="1">
                        @include($activeTemplate . 'partials.icon', ['name' => 'shopping-cart']) <span class="cont">@lang('Cart')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.wishlist') }}" href="{{ route('user.wishlist') }}" data-dashboard-link="1">
                        @include($activeTemplate . 'partials.icon', ['name' => 'heart']) <span class="cont">@lang('Wishlist')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.compare') }}" href="{{ route('user.compare') }}" data-dashboard-link="1">
                        @include($activeTemplate . 'partials.icon', ['name' => 'exchange-alt']) <span class="cont">@lang('Compare')</span>
                    </a>
                </li>

                @auth
                <li>
                    <a class="{{ menuActive('user.review*') }}" href="{{ route('user.review.index') }}" data-dashboard-link="1">
                        @include($activeTemplate . 'partials.icon', ['name' => 'haykal']) <span class="cont">@lang('Review Products')</span>
                    </a>
                </li>

                {{-- Account – লগইন থাকলেই --}}
                <li class="side__menu-title mt-2">@lang('Account')</li>
                <li>
                    <a class="{{ menuActive('user.profile.setting') }}" href="{{ route('user.profile.setting') }}" data-dashboard-link="1">
                        @include($activeTemplate . 'partials.icon', ['name' => 'user-tie']) <span class="cont">@lang('Profile')</span>
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.change.password') }}" href="{{ route('user.change.password') }}" data-dashboard-link="1">
                        @include($activeTemplate . 'partials.icon', ['name' => 'key']) <span class="cont">@lang('Change Password')</span>
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('user.logout') }}" class="d-inline w-100">
                        @csrf
                        <button type="submit" class="side__menu__logout-btn border-0 bg-transparent w-100 text-start d-flex align-items-center" style="cursor:pointer;">@include($activeTemplate . 'partials.icon', ['name' => 'sign-out-alt'])<span class="cont">@lang('Logout')</span></button>
                    </form>
                </li>
                @else
                {{-- গেস্ট: লগইন লিংক --}}
                <li class="side__menu-title mt-2">@lang('Account')</li>
                <li>
                    <a href="{{ route('user.login') }}?redirect={{ urlencode(request()->url()) }}" class="text--base" role="button">
                        @include($activeTemplate . 'partials.icon', ['name' => 'sign-in-alt']) <span class="cont">@lang('Login')</span>
                    </a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</aside>
