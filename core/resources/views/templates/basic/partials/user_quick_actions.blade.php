<div class="user-quick-actions mb-3">
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route('user.track.order') }}" class="btn btn-sm btn-outline--primary" title="@lang('Track Order')" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast', 'class' => 'me-1'])<span class="d-none d-md-inline">@lang('Track Order')</span></a>
        <a href="{{ route('user.notifications') }}" class="btn btn-sm btn-outline--primary position-relative" title="@lang('Notifications')" data-dashboard-link="1">
            @include($activeTemplate . 'partials.icon', ['name' => 'bell', 'class' => 'me-1'])<span class="d-none d-md-inline">@lang('Notifications')</span>
            @if(($userNotificationCount ?? 0) > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $userNotificationCount > 99 ? '99+' : $userNotificationCount }}</span>
            @endif
        </a>
        <a href="{{ route('user.wishlist') }}" class="btn btn-sm btn-outline--primary" title="@lang('Wishlist')" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'heart', 'class' => 'me-1'])<span class="d-none d-md-inline">@lang('Wishlist')</span></a>
        <a href="{{ route('user.compare') }}" class="btn btn-sm btn-outline--primary" title="@lang('Compare')" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'exchange-alt', 'class' => 'me-1'])<span class="d-none d-md-inline">@lang('Compare')</span></a>
        <a href="{{ route('user.cart') }}" class="btn btn-sm btn-outline--primary" title="@lang('Cart')" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'shopping-cart', 'class' => 'me-1'])<span class="d-none d-md-inline">@lang('Cart')</span> <span class="show-cart-count">0</span></a>
        <a href="{{ route('user.order.index') }}" class="btn btn-sm btn-outline--primary" title="@lang('My Orders')" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'shopping-bag', 'class' => 'me-1'])<span class="d-none d-md-inline">@lang('My Orders')</span></a>
    </div>
</div>
