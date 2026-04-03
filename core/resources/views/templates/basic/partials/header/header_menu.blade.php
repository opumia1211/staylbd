<div class="header-menu bg--base">
    <div class="container">
        <div class="menu-area">
            <div class="menu-close">
                @include($activeTemplate . 'partials.icon', ['name' => 'times'])
            </div>

            <form action="" method="GET" class="search-form d-lg-none mb-4">
                <div class="input-group search--group">
                    <input type="text" class="form-control" name="search" placeholder="@lang('Search here')" value="{{ request()->search ?? null }}">
                    <button class="cmn--btn" type="submit">@lang('Search') </button>
                </div>
            </form>

            <div class="category-link-area  @if (request()->routeIs('home') ||request()->routeIs('shope')) d-lg-none @endif">
                <button type="submit" class="cmn--btn categoryButton">@lang('All Categories') @include($activeTemplate . 'partials.icon', ['name' => 'angle-down', 'class' => 'ms-2 fs--14px'])</button>
                <button type="submit" class="cmn--btn menuButton active">@lang('Menu')</button>

                @include($activeTemplate . 'partials.navbar')

            </div>

            <ul class="menu  @if (request()->routeIs('home')) me-lg-auto @endif">
                <li>
                    <a href="{{ route('home') }}" class="{{ menuActive('home') }}">
                        @include($activeTemplate . 'partials.icon', ['name' => 'home', 'class' => 'me-1']) @lang('Home')
                    </a>
                </li>
                <li>
                    <a href="{{ route('products') }}" class="{{ menuActive('products') }}">
                        @include($activeTemplate . 'partials.icon', ['name' => 'box', 'class' => 'me-1']) @lang('Products')
                    </a>
                </li>
                <li>
                    <a href="{{ route('contact') }}" class="{{ menuActive('contact') }}">
                        @include($activeTemplate . 'partials.icon', ['name' => 'phone', 'class' => 'me-1']) @lang('Contact')
                    </a>
                </li>
                <li>
                    <a href="{{ route('track.order') }}" class="{{ menuActive('track-order') }}">
                        @include($activeTemplate . 'partials.icon', ['name' => 'shipping-fast', 'class' => 'me-1']) @lang('Track Order')
                    </a>
                </li>
            </ul>

            <div class="sign-in-up d-none d-md-block font-heading change-language2">
                <span class="text-white">@include($activeTemplate . 'partials.icon', ['name' => 'user'])</span>
                @auth
                    <a class="text-white me-3" href="{{ route('user.home') }}">{{ auth()->user()->username }}</a>
                    @php $contactContent = getContent('contact_us.content', true); @endphp
                    @if(!empty(@$contactContent->data_values->contact_number))
                        <a class="text-white d-inline-flex align-items-center" href="https://wa.me/{{ preg_replace('/\D+/', '', __(@$contactContent->data_values->contact_number)) }}" target="_blank" rel="noopener">
                            @include($activeTemplate . 'partials.icon', ['name' => 'whatsapp', 'class' => 'me-1']) @lang('Whatsapp support')
                        </a>
                    @endif
                @else
                    <a class="text-white" href="{{ route('user.login') }}">@lang('Login')</a>
                    <a class="text-white" href="{{ route('user.register') }}">@lang('Register')</a>
                @endauth
            </div>

            <div class="change-language d-md-none mt-4 fs--16px">
                <div class="sign-in-up">
                    <span>@include($activeTemplate . 'partials.icon', ['name' => 'user'])</span>
                    @auth
                        <a href="{{ route('user.home') }}">{{ auth()->user()->username }}</a>
                    @else
                        <a href="{{ route('user.login') }}">@lang('Login')</a>
                        <a href="{{ route('user.register') }}">@lang('Register')</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
