@extends($activeTemplate . 'layouts.master')
@section('dashboard_page_title')
    @include($activeTemplate . 'partials.dashboard_page_header', ['title' => __('Dashboard'), 'subtitle' => __('Overview of your account and orders')])
@endsection
@section('content')
@php $userDashboardTimers = get_offer_timers_for_display('user_dashboard', 'user_dashboard_top'); @endphp
@if($userDashboardTimers->isNotEmpty())
    <div class="mb-4">
        @foreach($userDashboardTimers as $udt)
            @include('partials.offer_timer_bar', ['timer' => $udt])
        @endforeach
    </div>
@endif

<div class="user-dashboard-home">
    {{-- All 10 cards: Mobile 3 lines (4+4+2), Tablet/Laptop 2 lines (5+5) – professional square cards --}}
    {{-- row-cols-2: very small screens (square cards); row-cols-sm-4: mobile; row-cols-md-5: tablet/laptop --}}
    <div class="row g-3 dashboard-stats-row row-cols-2 row-cols-sm-4 row-cols-md-5">
        <div class="col">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'list'])</div>
                <div class="dashboard-card__content">
                    <p>@lang('All Orders')</p>
                    <h4 class="title">{{ $order['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'spinner'])</div>
                <div class="dashboard-card__content">
                    <p>@lang('Pending')</p>
                    <h4 class="title">{{ $order['pending'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'check-square'])</div>
                <div class="dashboard-card__content">
                    <p>@lang('Confirmed')</p>
                    <h4 class="title">{{ $order['confirmed'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'truck'])</div>
                <div class="dashboard-card__content">
                    <p>@lang('Shipping')</p>
                    <h4 class="title">{{ $order['shipped'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'check-circle'])</div>
                <div class="dashboard-card__content">
                    <p>@lang('Delivered')</p>
                    <h4 class="title">{{ $order['delivered'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'times-circle'])</div>
                <div class="dashboard-card__content">
                    <p>@lang('Cancelled')</p>
                    <h4 class="title">{{ $order['cancelled'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <a href="{{ route('user.cart') }}" class="dashboard-card text-decoration-none d-block" title="@lang('Cart')" data-dashboard-link="1">
                <div class="dashboard-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'shopping-cart'])</div>
                <div class="dashboard-card__content">
                    <p>@lang('Cart')</p>
                    <h4 class="title">{{ $cartCount ?? 0 }}</h4>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('user.wishlist') }}" class="dashboard-card text-decoration-none d-block" title="@lang('Wishlist')" data-dashboard-link="1">
                <div class="dashboard-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'heart'])</div>
                <div class="dashboard-card__content">
                    <p>@lang('Wishlist')</p>
                    <h4 class="title">{{ $wishlistCount ?? 0 }}</h4>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('user.notifications') }}" class="dashboard-card text-decoration-none d-block" title="@lang('Notifications')" data-dashboard-link="1">
                <div class="dashboard-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'bell'])</div>
                <div class="dashboard-card__content">
                    <p>@lang('Notifications')</p>
                    <h4 class="title">{{ $unreadNotifications ?? 0 }}</h4>
                    <small class="text-muted d-block">@lang('Unread')</small>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('message.index') }}" class="dashboard-card text-decoration-none d-block" title="@lang('My Messages')" data-dashboard-link="1">
                <div class="dashboard-card__icon">@include($activeTemplate . 'partials.icon', ['name' => 'comments'])</div>
                <div class="dashboard-card__content">
                    <p>@lang('My Messages')</p>
                    <h4 class="title">{{ $supportCount ?? 0 }}</h4>
                    <small class="text-muted d-block">@lang('Open tickets')</small>
                </div>
            </a>
        </div>
    </div>

    <h6 class="dashboard-section-title mt-2 mb-1">@lang('Latest Orders')</h6>
    <div class="table-responsive">
        <table class="table cmn--table table-sm dashboard-orders-table">
            <thead>
                <tr>
                    <th>@lang('Order No')</th>
                    <th>@lang('Payment Type')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Time')</th>
                    <th>@lang('More')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                <tr>
                    <td>{{ $order->order_no }}</td>
                    <td>
                        @if ($order->payment_type == Status::PAYMENT_ONLINE)
                            @lang('Online Payment')
                        @else
                            @lang('Cash On Delivery')
                        @endif
                    </td>
                    <td class="text--base"><strong>{{ showAmount($order->total) }} {{ __($general->cur_text) }}</strong></td>
                    <td>@php echo $order->ordersBadge; @endphp</td>
                    <td>{{ showDateTime($order->created_at) }}</td>
                    <td>
                        <a href="{{ route('user.order.detail', $order->id) }}" class="btn btn-sm btn--base" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'desktop'])</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-3">{{ __($emptyMessage ?? 'No orders yet.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
