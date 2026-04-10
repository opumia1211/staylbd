@php
    $customButtonsAll = \App\Models\Frontend::where('data_keys', 'custom_buttons.element')->orderBy('id', 'asc')->get();
    $customHeaderButtons = $customButtonsAll->filter(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (($dv['target'] ?? '') === 'header') && ((int) ($dv['is_active'] ?? 1) === 1);
    })->sortBy(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (int) ($dv['display_order'] ?? 0);
    })->values();

    if (!isset($__staylHeaderCategories)) {
        $__staylHeaderCategories = \Illuminate\Support\Facades\Cache::remember(
            'storefront.header_nav_categories_v1',
            300,
            static fn () => \App\Models\Category::active()
                ->with(['subcategories' => static fn ($q) => $q->active()])
                ->orderByDesc('id')
                ->limit(24)
                ->get()
        );
    }
@endphp

<style>
    :root {
        --stayl-h1: 120px;
        --stayl-h2: 100px;
        --stayl-yellow: #ffbb38;
        --stayl-active-blue: #2eb4e7;
        --stayl-bg-light: #f1f3f5;
        --stayl-icon-gray: #f8f9fa;
    }
    .stayl-fixed-master {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 100000;
        width: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 35px rgba(0,0,0,0.06);
        font-family: 'Outfit', sans-serif !important;
        background: #fff;
    }
    .stayl-top-bar {
        height: var(--stayl-h1);
        background: #ffffff;
        border-bottom: 1px solid #f1f1f1;
        display: flex;
        align-items: center;
        width: 100%;
    }
    .stayl-yellow-bar {
        height: var(--stayl-h2);
        background: var(--stayl-yellow);
        display: flex;
        align-items: center;
        width: 100%;
    }
    .stayl-wrap {
        max-width: 1650px;
        margin: 0 auto;
        padding: 0 40px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
    }

    /* Professional Search UI */
    .stayl-search-pill {
        flex: 1;
        max-width: 850px;
        margin: 0 40px;
        display: flex;
        align-items: center;
        background: #f8f9fa;
        border: 2px solid #f1f1f1;
        border-radius: 999px;
        height: 64px;
        padding: 4px;
        padding-right: 6px;
        transition: 0.3s;
    }
    .stayl-search-pill:focus-within {
        background: #ffffff;
        border-color: var(--stayl-active-blue);
        box-shadow: 0 10px 25px rgba(46, 180, 231, 0.12);
    }
    .stayl-search-input {
        flex: 1;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        padding: 0 35px !important;
        font-size: 16px !important;
        font-weight: 500 !important;
        color: #111 !important;
        width: 100%;
    }
    .stayl-search-actions-inner {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-right: 10px;
    }
    .stayl-search-icon-btn {
        width: 52px;
        height: 52px;
        background: var(--stayl-active-blue);
        color: white !important;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: 0 4px 12px rgba(46, 180, 231, 0.35);
    }
    .stayl-search-icon-btn:hover {
        background: #1e97c9;
        transform: scale(1.04);
    }

    /* 3D/Professional Icons Alignment */
    .stayl-action-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .stayl-icon-item {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: var(--stayl-icon-gray);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        text-decoration: none !important;
        transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        color: #111;
        filter: drop-shadow(0 1px 2px rgba(0,0,0,0.05));
    }
    .stayl-icon-item svg {
        stroke-width: 1.8;
        width: 24px;
        height: 24px;
    }
    .stayl-icon-item:hover {
        background: #ffffff;
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.12));
    }
    .stayl-icon-item:hover svg {
        color: var(--stayl-active-blue);
    }
    .stayl-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: #ff4d4d;
        color: #fff !important;
        font-size: 11px;
        font-weight: 800;
        min-width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(255, 77, 77, 0.3);
    }

    /* Yellow Bar Navigation */
    .stayl-cat-btn {
        background: transparent !important;
        height: var(--stayl-h2);
        padding: 0 45px;
        display: flex;
        align-items: center;
        gap: 15px;
        color: #000;
        font-weight: 800;
        font-size: 17px;
        cursor: pointer;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        transition: 0.3s;
    }
    .stayl-cat-btn:hover {
        background: transparent !important;
        color: #fff !important;
    }
    .stayl-nav-ul {
        display: flex;
        align-items: center;
        gap: 45px;
        margin-left: 50px;
        list-style: none;
        padding: 0;
        margin-bottom: 0;
    }
    .stayl-nav-ul li a {
        color: #000 !important;
        font-weight: 800;
        font-size: 16px;
        text-decoration: none !important;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .stayl-nav-ul li a:hover {
        color: #fff !important;
        transform: translateY(-2px);
    }
    .stayl-seller-btn {
        background: #111;
        color: #fff !important;
        padding: 16px 36px;
        border-radius: 14px;
        font-weight: 900;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none !important;
        transition: 0.3s;
        box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }
    .stayl-seller-btn:hover {
        background: #000;
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.25);
    }

    /* Categories Dropdown CSS */
    .stayl-cat-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        width: 300px;
        background: #fff;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        border-radius: 0 0 20px 20px;
        border-top: 4px solid #111;
        opacity: 0;
        visibility: hidden;
        transform: translateY(15px);
        transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 10000;
        padding: 15px 0;
    }
    .stayl-cat-dropdown.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .stayl-cat-dropdown ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .stayl-cat-dropdown li a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 30px;
        font-size: 15px;
        font-weight: 800;
        color: #111 !important;
        text-decoration: none !important;
        transition: 0.2s;
    }
    .stayl-cat-dropdown li a:hover {
        background: #f8f9fa;
        color: var(--stayl-active-blue) !important;
        padding-left: 35px;
    }


    /* Premium Glass Sidebar CSS (Framework Independent) */
    .stayl-sb-header {
        padding: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f1f1;
        background: #f8f9fa;
    }
    .stayl-sb-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .stayl-sb-icon-box {
        background: #111;
        color: #fff;
        padding: 10px;
        border-radius: 12px;
        display: flex;
    }
    .stayl-sb-title {
        font-size: 22px;
        font-weight: 900;
        color: #111;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin: 0;
    }
    .stayl-sb-close {
        background: #fff;
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        cursor: pointer;
        transition: 0.3s;
    }
    .stayl-sb-close:hover {
        background: #f1f1f1;
        transform: rotate(90deg);
    }
    .stayl-sb-body {
        padding: 25px;
        overflow-y: auto;
        max-height: calc(100vh - 100px);
    }
    .stayl-sb-item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 15px 25px;
        border-radius: 18px;
        text-decoration: none !important;
        color: #111;
        margin-bottom: 15px;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #f8f9fa;
        border: 1px solid transparent;
    }
    .stayl-sb-item:hover {
        background: #ffffff;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        border-color: #f1f1f1;
        transform: translateY(-3px);
        color: var(--stayl-active-blue);
    }
    .stayl-sb-item-icon {
        width: 50px;
        height: 50px;
        background: #ffffff;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #555;
        transition: 0.3s;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }
    .stayl-sb-item:hover .stayl-sb-item-icon {
        background: var(--stayl-active-blue);
        color: #ffffff;
    }
    .stayl-sb-item-text {
        font-size: 18px;
        font-weight: 800;
        margin: 0;
    }
    .stayl-sb-divider-text {
        font-size: 12px;
        font-weight: 900;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin: 30px 10px 15px;
        border-bottom: 1px solid #f1f1f1;
        padding-bottom: 10px;
    }
    .stayl-sb-action-box {
        margin-top: 35px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 24px;
        border: 1px solid #f1f1f1;
    }
    .stayl-btn-dark {
        background: #111;
        color: #fff !important;
        width: 100%;
        padding: 18px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        text-decoration: none !important;
        font-weight: 800;
        font-size: 16px;
        transition: 0.3s;
    }
    .stayl-btn-dark:hover {
        background: var(--stayl-active-blue);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(46, 180, 231, 0.3);
    }
    .stayl-btn-outline {
        background: #fff;
        color: #111 !important;
        width: 100%;
        padding: 18px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        text-decoration: none !important;
        font-weight: 800;
        font-size: 16px;
        border: 2px solid #f1f1f1;
        margin-top: 15px;
        transition: 0.3s;
    }
    .stayl-btn-outline:hover {
        background: #f8f9fa;
        border-color: #ddd;
    }
</style>

<header class="stayl-fixed-master">
    {{-- Row 1: The Main Action Bar --}}
    <div class="stayl-top-bar">
        <div class="stayl-wrap">
            {{-- Logo --}}
            <div class="d-flex align-items-center">
                <a href="{{ route('home') }}">
                    @php $logo = getLogo('logo'); @endphp
                    @if($logo)
                        <img src="{{ $logo }}" alt="Staylbd" style="max-height: 75px; width: auto;">
                    @else
                        <span style="font-size: 34px; font-weight: 900; color: #111; letter-spacing: -2px;">{{ strtoupper(gs('site_name')) }}</span>
                    @endif
                </a>
            </div>

            {{-- Search Pill & External Lens --}}
            <div class="flex flex-1 items-center justify-center gap-2"> {{-- Tight gap for unified look --}}
                <form action="{{ route('products') }}" method="GET" class="stayl-search-pill" style="margin: 0 !important; flex: 1; max-width: 800px;">
                    <input type="text" name="search" class="stayl-search-input" placeholder="@lang('Search products, brands, and more')..." value="{{ request()->search ?? null }}" autocomplete="off">
                    <div class="stayl-search-actions-inner" style="gap: 12px; margin-right: 8px;">
                        {{-- Voice Search --}}
                        <div style="cursor:pointer; color:#555;" id="voiceSearchBtn" title="Voice Search">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="22"></line></svg>
                        </div>
                        {{-- Search Submit --}}
                        <button type="submit" class="stayl-search-icon-btn" style="width: 48px; height: 48px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </button>
                    </div>
                </form>
                {{-- Professional Lens Icon (Outside but Right Next to Search) --}}
                <div id="cameraSearchBtn" title="Camera Search" style="width: 58px; height: 58px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 1px solid #f1f1f1; box-shadow: 0 4px 12px rgba(0,0,0,0.06); transition: 0.3s; flex-shrink: 0;" onmouseover="this.style.boxShadow='0 8px 25px rgba(0,0,0,0.12)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)'; this.style.transform='translateY(0)';" onclick="alert('Camera Search feature');">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 7V5a2 2 0 0 1 2-2h2"></path>
                        <path d="M17 3h2a2 2 0 0 1 2 2v2"></path>
                        <path d="M21 17v2a2 2 0 0 1-2 2h-2"></path>
                        <path d="M7 21H5a2 2 0 0 1-2-2v-2"></path>
                        <circle cx="12" cy="12" r="3" stroke-width="2"></circle>
                    </svg>
                </div>
            </div>

            {{-- Action Icons (Premium SVG Set) --}}
            <div class="stayl-action-row">
                <a href="#" class="stayl-icon-item" title="Orders">
                    {{-- Lucide: shopping-bag --}}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                </a>
                <a href="{{ route('contact') }}" class="stayl-icon-item" title="Contact">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </a>
                <a href="{{ route('track.order') }}" class="stayl-icon-item" title="Track Order">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                </a>
                <a href="#" class="stayl-icon-item" title="Language">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                </a>
                <a href="{{ route('user.wishlist') }}" class="stayl-icon-item" title="Wishlist">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    <span class="stayl-badge show-wishlist-count">0</span>
                </a>
                <a href="#" class="stayl-icon-item" title="Compare">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m16 4 4 4-4 4"></path><path d="M20 8H4"></path><path d="m8 20-4-4 4-4"></path><path d="M4 16h16"></path></svg>
                    <span class="stayl-badge">0</span>
                </a>
                <a href="{{ route('user.cart') }}" class="stayl-icon-item" title="Cart">
                    {{-- Lucide: shopping-cart --}}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                    <span class="stayl-badge show-cart-count">0</span>
                </a>
                <a href="{{ route('user.home') }}" class="stayl-icon-item" style="background:#111; color:#fff !important;" title="Account">
                    {{-- Lucide: user-round --}}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 0 0-16 0"></path></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Row 2: Secondary Nav Bar --}}
    <div class="stayl-yellow-bar">
        <div class="stayl-wrap">
            <nav class="d-flex align-items-center h-100" style="gap: 50px;"> {{-- Fixed huge gap --}}
                {{-- Separate Hamburger Button --}}
                <div class="stayl-sidebar-trigger cursor-pointer flex items-center justify-center" style="width: 42px; height: 42px; transition: 0.3s; color: #000;" onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1)';" title="Open Menu">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </div>

                {{-- All Categories Button (Separate) --}}
                <div class="h-100 position-relative" id="staylCatContainer">
                    <button class="stayl-cat-btn" id="staylCatBtn" style="padding: 0; gap: 10px;">
                        @lang('ALL CATEGORIES')
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="m6 9 6 6 6-6"></path></svg>
                    </button>
                    @if($__staylHeaderCategories->isNotEmpty())
                    <div class="stayl-cat-dropdown" id="staylCatDropdown">
                        <ul>
                            @foreach($__staylHeaderCategories->take(12) as $hc)
                                <li>
                                    <a href="{{ route('category.products', [slug($hc->name), $hc->id]) }}">
                                        {{ __($hc->name) }}
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="m9 18 6-6-6-6"></path></svg>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <ul class="stayl-nav-ul">
                    <li><a href="{{ route('home') }}">@lang('Homepage')</a></li>
                    <li><a href="{{ route('products') }}">@lang('Shop Products')</a></li>
                    <li class="relative group">
                        <a href="#">@lang('Pages') <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="m6 9 6 6 6-6"></path></svg></a>
                        <div class="absolute top-full left-0 w-64 bg-white shadow-2xl rounded-2xl py-6 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-3 group-hover:translate-y-0 transition-all duration-300">
                             <a href="{{ route('category.all') }}" class="block px-8 py-3.5 text-sm font-bold text-slate-700 hover:text-blue-500 no-underline">@lang('All Categories')</a>
                             <a href="{{ route('track.order') }}" class="block px-8 py-3.5 text-sm font-bold text-slate-700 hover:text-blue-500 no-underline">@lang('Track Order')</a>
                             <a href="{{ route('contact') }}" class="block px-8 py-3.5 text-sm font-bold text-slate-700 hover:text-blue-500 no-underline">@lang('Customer Support')</a>
                        </div>
                    </li>
                    <li><a href="#">@lang('About Us')</a></li>
                    <li><a href="#">@lang('Latest Blog')</a></li>
                    <li><a href="{{ route('contact') }}">@lang('Contact Us')</a></li>
                </ul>
            </nav>

            <a href="{{ route('seller.apply') }}" class="stayl-seller-btn">
                @lang('BECOME A SELLER')
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color:var(--stayl-yellow);"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </div>
    </div>
</header>

<div class="glass-mobile-menu" id="glassSidebar" style="font-family: 'Outfit', sans-serif;">
    <div class="glass-mobile-menu-overlay glass-sidebar-overlay" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(5px);"></div>
    <div class="glass-mobile-menu-content" style="background: #ffffff; width: 420px; box-shadow: 20px 0 60px rgba(0,0,0,0.15);">
        
        {{-- Custom CSS Header --}}
        <div class="stayl-sb-header">
            <div class="stayl-sb-title-wrap">
                <div class="stayl-sb-icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </div>
                <h3 class="stayl-sb-title">@lang('NAVIGATION')</h3>
            </div>
            <button id="glassSidebarClose" class="stayl-sb-close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <div class="stayl-sb-body">
            
            <a href="{{ route('home') }}" class="stayl-sb-item">
                <div class="stayl-sb-item-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                </div>
                <h5 class="stayl-sb-item-text">@lang('Home Page')</h5>
            </a>
            
            <a href="{{ route('products') }}" class="stayl-sb-item">
                <div class="stayl-sb-item-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h1.49l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                </div>
                <h5 class="stayl-sb-item-text">@lang('Shop Products')</h5>
            </a>

            <div class="stayl-sb-divider-text">@lang('Support & Orders')</div>
            
            <a href="{{ route('track.order') }}" class="stayl-sb-item">
                <div class="stayl-sb-item-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                </div>
                <h5 class="stayl-sb-item-text">@lang('Track My Order')</h5>
            </a>

            <a href="{{ route('contact') }}" class="stayl-sb-item">
                <div class="stayl-sb-item-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </div>
                <h5 class="stayl-sb-item-text">@lang('Customer Support')</h5>
            </a>

            {{-- Action Area --}}
            <div class="stayl-sb-action-box">
                @guest
                <a href="{{ route('user.login') }}" class="stayl-btn-dark">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 0 0-16 0"></path></svg>
                    @lang('LOGIN TO ACCOUNT')
                </a>
                <a href="{{ route('user.register') }}" class="stayl-btn-outline">
                    @lang('CREATE NEW ACCOUNT')
                </a>
                @else
                <a href="{{ route('user.home') }}" class="stayl-btn-dark" style="background: var(--stayl-active-blue);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    @lang('VIEW DASHBOARD')
                </a>
                @endguest
            </div>
            
        </div>
    </div>
</div>
<script>
    (function() {
        // Vanilla JS Toggle to avoid dependency issues
        function setupStaylSidebar() {
            const triggers = document.querySelectorAll('.stayl-sidebar-trigger');
            const sidebar = document.getElementById('glassSidebar');
            const closeBtn = document.getElementById('glassSidebarClose');
            const overlay = document.querySelector('.glass-sidebar-overlay');

            if (!sidebar) return;

            triggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    sidebar.classList.add('active');
                    document.body.style.overflow = 'hidden'; // Prevent scroll
                });
            });

            const closeAction = function() {
                sidebar.classList.remove('active');
                document.body.style.overflow = '';
            };

            if (closeBtn) closeBtn.addEventListener('click', closeAction);
            if (overlay) overlay.addEventListener('click', closeAction);

            // Categories Click Toggle
            const catBtn = document.getElementById('staylCatBtn');
            const catDropdown = document.getElementById('staylCatDropdown');
            const catContainer = document.getElementById('staylCatContainer');

            if (catBtn && catDropdown) {
                catBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    catDropdown.classList.toggle('active');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (catContainer && !catContainer.contains(e.target)) {
                        catDropdown.classList.remove('active');
                    }
                });
            }
        }

        // Initialize
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupStaylSidebar);
        } else {
            setupStaylSidebar();
        }
    })();
</script>
