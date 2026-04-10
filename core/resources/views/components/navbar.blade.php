@php
    $categories = \App\Models\Category::active()
        ->with(['subcategories' => fn($q) => $q->active()])
        ->orderByDesc('id')
        ->limit(12)
        ->get();

    $customButtonsAll = \App\Models\Frontend::where('data_keys', 'custom_buttons.element')->orderBy('id', 'asc')->get();
    $navButtons = $customButtonsAll->filter(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (($dv['target'] ?? '') === 'header') && 
               (($dv['position'] ?? '') === 'nav') &&
               ((int) ($dv['is_active'] ?? 1) === 1);
    })->sortBy(function ($row) {
        $dv = (array) ($row->data_values ?? []);
        return (int) ($dv['display_order'] ?? 0);
    });
@endphp

<div class="hidden lg:block bg-white border-b border-gray-100 shadow-sm sticky top-0 z-[1000]">
    <div class="container max-w-[1400px] mx-auto px-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-10">
                <!-- Browser Categories Toggle & Sidebar -->
                <div class="relative group/cat-main">
                    <button class="bg-zenis-secondary text-white px-8 py-4 font-black rounded-tr-3xl flex items-center gap-4 transition-all hover:bg-opacity-95 group/btn">
                        <i class="hgi hgi-stroke hgi-menu-01 text-xl"></i>
                        <span class="text-[15px] uppercase tracking-wide">Browse Categories</span>
                        <i class="hgi hgi-stroke hgi-arrow-down-01 text-[12px] ml-4 transition-transform group-hover/btn:rotate-180"></i>
                    </button>
                    
                    <!-- Sidebar Dropdown -->
                    <div class="absolute top-full left-0 w-[280px] bg-white shadow-2xl border border-gray-100 rounded-b-xl opacity-0 invisible group-hover/cat-main:opacity-100 group-hover/cat-main:visible transition-all duration-300 z-[1100] transform translate-y-2 group-hover/cat-main:translate-y-0">
                        <ul class="py-3">
                            @foreach($categories as $cat)
                                <li class="relative group/sub">
                                    <a href="{{ route('category.products', [slug($cat->name), $cat->id]) }}" class="flex items-center justify-between px-6 py-3.5 text-[14px] font-bold text-gray-700 hover:text-zenis-primary hover:bg-gray-50 transition-all">
                                        <div class="flex items-center gap-3">
                                            @if($cat->image)
                                                <img src="{{ getImage(getFilePath('category') . '/' . $cat->image, getFileSize('category')) }}" class="size-5 object-contain">
                                            @endif
                                            <span>{{ __($cat->name) }}</span>
                                        </div>
                                        @if($cat->subcategories->count())
                                            <i class="hgi hgi-stroke hgi-arrow-right-01 text-xs text-gray-400"></i>
                                        @endif
                                    </a>
                                    
                                    @if($cat->subcategories->count())
                                        <!-- Mega Submenu -->
                                        <div class="absolute top-0 left-full w-[240px] ml-0.5 bg-white shadow-2xl border border-gray-50 rounded-xl opacity-0 invisible group-hover/sub:opacity-100 group-hover/sub:visible transition-all duration-300 min-h-full p-4">
                                            <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 pl-2">Sub Categories</h4>
                                            <div class="space-y-1">
                                                @foreach($cat->subcategories->take(12) as $sub)
                                                    <a href="{{ route('category.products', [slug($sub->name), $sub->id]) }}" class="block px-4 py-2 text-[13px] font-bold text-gray-600 hover:text-zenis-primary hover:bg-gray-50 rounded-lg transition-colors">{{ __($sub->name) }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                            <li class="mt-2 pt-2 border-t border-gray-50">
                                <a href="{{ route('category.all') }}" class="flex items-center justify-center px-6 py-3 text-[13px] font-black text-zenis-primary hover:bg-zenis-primary hover:text-white transition-all rounded-lg mx-3">
                                    View All Categories
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Main Navigation -->
                <nav>
                    <ul class="flex items-center gap-x-10">
                        <li>
                            <a href="{{ route('home') }}" class="text-[15px] font-black tracking-wide transition-all hover:text-zenis-primary {{ request()->routeIs('home') ? 'text-zenis-primary' : 'text-gray-800' }}">Home</a>
                        </li>
                        <li>
                            <a href="{{ route('products') }}" class="text-[15px] font-black tracking-wide transition-all hover:text-zenis-primary {{ request()->routeIs('products') ? 'text-zenis-primary' : 'text-gray-800' }}">Shop</a>
                        </li>
                        @foreach($navButtons as $btn)
                             @php $dv = $btn->data_values; @endphp
                             <li>
                                <a href="{{ $dv->url }}" @if(($dv->is_new_tab ?? 1) == 1) target="_blank" @endif class="text-[15px] font-black tracking-wide text-gray-800 hover:text-zenis-primary transition-all">
                                    {{ __($dv->button_text) }}
                                </a>
                             </li>
                        @endforeach
                        <li>
                            <a href="{{ route('contact') }}" class="text-[15px] font-black tracking-wide transition-all hover:text-zenis-primary {{ request()->routeIs('contact') ? 'text-zenis-primary' : 'text-gray-800' }}">Contact</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Optional Right Side Badges/Links -->
            <div class="hidden xl:flex items-center gap-6">
                 <a href="{{ route('product.hot.deal') }}" class="flex items-center gap-2 text-sm font-black text-rose-500 hover:opacity-80 transition-all uppercase tracking-widest">
                    <i class="hgi hgi-stroke hgi-fire text-lg"></i>
                    <span>Flash Deals</span>
                 </a>
            </div>
        </div>
    </div>
</div>
