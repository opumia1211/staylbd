@php
    $languages = \App\Models\Language::all();
    $currentLocale = app()->getLocale();
    $currentLang = $languages->firstWhere('code', $currentLocale) ?? $languages->first();
    $segments = request()->segments();
    $localeCodes = $languages->pluck('code')->map(fn ($code) => strtolower(trim((string) $code)))->all();
    if (!empty($segments) && in_array(strtolower((string) $segments[0]), $localeCodes, true)) {
        array_shift($segments);
    }
    $basePath = implode('/', $segments);
    $queryString = request()->getQueryString();

    $customButtonsAll = \App\Models\Frontend::where('data_keys', 'custom_buttons.element')->orderBy('id', 'asc')->get();
    $customHeaderButtons = $customButtonsAll->filter(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (($dv['target'] ?? '') === 'header') && ((int) ($dv['is_active'] ?? 1) === 1);
    })->sortBy(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (int) ($dv['display_order'] ?? 0);
    })->values();

    if (!isset($categories)) {
        $categories = \App\Models\Category::active()
            ->with(['subcategories' => fn($q) => $q->active()])
            ->orderByDesc('id')
            ->limit(12)
            ->get();
    }
@endphp

<header class="w-full bg-white z-[1001]">
    <!-- Top Bar -->
    <div class="hidden lg:block bg-[#F8FAFC] border-b border-gray-100 py-2.5">
        <div class="container max-w-[1400px] mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="tel:{{ $general->whatsapp_number }}" class="text-[13px] text-gray-600 hover:text-zenis-primary flex items-center gap-2 transition-colors">
                    <i class="hgi hgi-stroke hgi-customer-support"></i>
                    <span>{{ __('Support') }}: {{ $general->whatsapp_number }}</span>
                </a>
            </div>
            <div class="flex items-center gap-6 divide-x divide-gray-200">
                <div class="flex items-center gap-4 pl-4">
                    <!-- Language Selection -->
                    <div class="relative group">
                        <button class="text-[13px] text-gray-600 hover:text-zenis-primary flex items-center gap-1.5 transition-colors">
                            <span>{{ __($currentLang->name) }}</span>
                            <i class="hgi hgi-stroke hgi-arrow-down-01 text-[10px]"></i>
                        </button>
                        <div class="absolute right-0 top-full mt-2 w-32 bg-white border border-gray-100 shadow-xl rounded-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-[1100]">
                            @foreach($languages as $lang)
                                @php
                                    $langCode = strtolower(trim((string) $lang->code));
                                    $targetUrl = url($langCode . ($basePath ? '/' . $basePath : ''));
                                    if ($queryString) {
                                        $targetUrl .= '?' . $queryString;
                                    }
                                    $isActiveLang = $currentLocale === $langCode;
                                @endphp
                                <a href="{{ $targetUrl }}" class="block px-4 py-2 text-xs hover:bg-gray-50 hover:text-zenis-primary {{ $isActiveLang ? 'bg-gray-100 font-semibold text-zenis-primary' : 'text-gray-700' }}">
                                    {{ __($lang->name) }} @if($isActiveLang) <span class="float-right">✔</span> @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4 pl-4">
                    @auth
                        <a href="{{ route('user.home') }}" class="text-[13px] text-gray-600 hover:text-zenis-primary transition-colors">{{ __('My Account') }}</a>
                        <a href="{{ route('user.logout') }}" class="text-[13px] text-gray-600 hover:text-zenis-primary transition-colors">{{ __('Logout') }}</a>
                    @else
                        <a href="{{ route('user.login') }}" class="text-[13px] text-gray-600 hover:text-zenis-primary transition-colors">{{ __('Sign In / Register') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="py-5 lg:py-8 border-b border-gray-100">
        <div class="container max-w-[1400px] mx-auto px-4 flex flex-wrap lg:flex-nowrap items-center justify-between gap-y-4 lg:gap-x-12">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex-shrink-0">
                <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" class="h-8 lg:h-10 w-auto object-contain" alt="Logo">
            </a>

            <!-- Search Area -->
            <div class="order-3 lg:order-2 w-full lg:flex-1 lg:max-w-2xl group">
                <form action="{{ route('products') }}" method="GET" class="relative flex items-center w-full bg-[#f1f5f9] rounded-xl border border-transparent focus-within:border-zenis-primary focus-within:bg-white transition-all duration-300">
                    <div class="hidden lg:flex items-center px-4 border-r border-gray-200">
                        <select name="category" class="bg-transparent text-sm font-semibold text-gray-700 focus:outline-none cursor-pointer">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ __($cat->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="text" name="search" placeholder="{{ __('Search for items...') }}" value="{{ request()->search }}" class="flex-1 bg-transparent px-6 py-3.5 text-sm text-gray-700 focus:outline-none placeholder:text-gray-400">
                    <button type="submit" class="p-3 mr-1 bg-zenis-primary text-white rounded-lg hover:bg-opacity-90 transition-all">
                        <i class="hgi hgi-stroke hgi-search-01 text-xl"></i>
                    </button>
                </form>
            </div>

            <!-- Actions Area -->
            <div class="order-2 lg:order-3 flex items-center gap-4 lg:gap-8">
                <!-- Hotline (Desktop) -->
                @if($general->whatsapp_number)
                <div class="hidden xl:flex items-center gap-3">
                    <div class="size-11 rounded-full bg-zenis-secondary/10 text-zenis-secondary flex items-center justify-center">
                        <i class="hgi hgi-stroke hgi-call text-xl"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[11px] text-gray-400 font-bold uppercase tracking-wider">{{ __('Hotline') }}</span>
                        <span class="text-sm font-black text-gray-800">{{ $general->whatsapp_number }}</span>
                    </div>
                </div>
                @endif

                <div class="flex items-center gap-4 lg:gap-6">
                    <!-- Quick View (Global/Search) -->
                    <a href="javascript:void(0)" class="relative group hidden lg:block">
                        <div class="size-11 rounded-full hover:bg-gray-50 flex items-center justify-center transition-all group-hover:-translate-y-1">
                            <i class="hgi hgi-stroke hgi-view text-2xl text-gray-700"></i>
                        </div>
                    </a>

                    <!-- Compare -->
                    <a href="javascript:void(0)" class="relative group hidden lg:block">
                        <div class="size-11 rounded-full hover:bg-gray-50 flex items-center justify-center transition-all group-hover:-translate-y-1">
                            <i class="hgi hgi-stroke hgi-arrow-data-transfer-horizontal text-2xl text-gray-700"></i>
                        </div>
                        <span class="absolute -top-1 -right-1 size-5 bg-gray-800 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white show-compare-count">0</span>
                    </a>

                    <!-- Wishlist -->
                    <a href="{{ route('user.wishlist') }}" class="relative group">
                        <div class="size-11 rounded-full hover:bg-gray-50 flex items-center justify-center transition-all group-hover:-translate-y-1">
                            <i class="hgi hgi-stroke hgi-favourite text-2xl text-gray-700"></i>
                        </div>
                        <span class="absolute -top-1 -right-1 size-5 bg-zenis-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white show-wishlist-count">0</span>
                    </a>
                    
                    <!-- Cart -->
                    <a href="{{ route('user.cart') }}" class="relative group">
                        <div class="size-11 rounded-full hover:bg-gray-50 flex items-center justify-center transition-all group-hover:-translate-y-1">
                            <i class="hgi hgi-stroke hgi-shopping-cart-01 text-2xl text-gray-700"></i>
                        </div>
                        <span class="absolute -top-1 -right-1 size-5 bg-zenis-secondary text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white show-cart-count">0</span>
                    </a>

                    <!-- User (Mobile might show differently) -->
                    <div class="hidden lg:block relative group">
                        <a href="{{ route('user.home') }}" class="flex items-center gap-3 group">
                            <div class="size-11 rounded-full border-2 border-gray-100 overflow-hidden">
                                <img src="{{ getImage(getFilePath('userProfile') . '/' . (auth()->user() ? auth()->user()->image : 'default.png')) }}" class="size-full object-cover">
                            </div>
                            <div class="flex flex-col overflow-hidden max-w-[100px]">
                                <span class="text-[11px] text-gray-400 font-bold uppercase truncate">@auth {{ auth()->user()->username }} @else {{ __('Account') }} @endauth</span>
                                <span class="text-sm font-black text-gray-800 truncate">@auth {{ __('Profile') }} @else {{ __('Login') }} @endauth</span>
                            </div>
                        </a>
                    </div>

                    <!-- Mobile Menu Trigger -->
                    <button class="lg:hidden size-11 flex items-center justify-center text-gray-800" onclick="window.toggleMobileMenu()">
                        <i class="hgi hgi-stroke hgi-menu-01 text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>
