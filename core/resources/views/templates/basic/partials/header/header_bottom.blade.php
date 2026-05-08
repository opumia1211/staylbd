@php
    $contactContent = getContent('contact_us.content', true);
    $currentLocale = app()->getLocale();
    $segments = request()->segments();
    $languageCodes = \App\Models\Language::pluck('code')->map(fn ($code) => strtolower(trim((string) $code)))->all();
    if (!empty($segments) && in_array(strtolower((string) $segments[0]), $languageCodes, true)) {
        array_shift($segments);
    }
    $basePath = implode('/', $segments);
    $queryString = request()->getQueryString();
@endphp
<div class="header-bottom bg--section py-2">
    <div class="container">
        <div class="header-wrapper">
            <div class="logo me-lg-4 me-auto {{ getLogoEffectClasses() }}">
                <a href="{{ route('home') }}" title="@lang('Home')">
                    @php $headerLogo = getLogo('logo'); @endphp
                    @if($headerLogo)
                        <img src="{{ $headerLogo }}" alt="{{ gs('site_name') }}" class="site-logo-img" style="{{ getLogoStyle() }}">
                    @endif
                </a>
            </div>
            <form action="{{ route('products') }}" method="GET" class="search-form d-none d-lg-block" role="search">
                <div class="input-group search--group">
                    <label for="header-search-input" class="visually-hidden">@lang('Search products')</label>
                    <input type="search" id="header-search-input" class="form-control" name="search" placeholder="@lang('Search here')" value="{{ request()->search ?? null }}" autocomplete="off" aria-label="@lang('Search products')">
                    <button class="cmn--btn" type="submit" aria-label="@lang('Search')">@lang('Search')</button>
                </div>
            </form>
            <div class="cart-wrapper d-flex flex-wrap  me-4 me-lg-0">
                <a href="{{ route('user.wishlist') }}" class="cart--btn">
                    @include($activeTemplate . 'partials.icon', ['name' => 'heart'])
                    <span class="qty show-wishlist-count">0</span>
                </a>
                <a href="{{ route('user.cart') }}" class="cart--btn">
                    @include($activeTemplate . 'partials.icon', ['name' => 'cart-arrow-down'])
                    <span class="qty show-cart-count">0</span>
                </a>
                @if($general->multi_language)
                    @php $language = App\Models\Language::all(); @endphp
                    <div class="dropdown ms-2">
                        <button class="btn btn-sm btn--base dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @include($activeTemplate . 'partials.icon', ['name' => 'language', 'class' => 'me-1'])
                            {{ __(optional($language->firstWhere('code', $currentLocale))->name ?? 'English') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach ($language as $item)
                                @php
                                    $langCode = strtolower(trim((string) $item->code));
                                    $targetUrl = url($langCode . ($basePath ? '/' . $basePath : ''));
                                    if ($queryString) {
                                        $targetUrl .= '?' . $queryString;
                                    }
                                @endphp
                                <li>
                                    <a class="dropdown-item {{ $currentLocale === $langCode ? 'active fw-semibold' : '' }}" href="{{ $targetUrl }}">
                                        {{ __($item->name) }} @if($currentLocale === $langCode) ✔ @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="header-bar d-lg-none">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
</div>
