@php
    $categories = \App\Models\Category::active()
        ->with(['subcategories' => fn($q) => $q->active()])
        ->orderByDesc('id')
        ->limit(10)
        ->get();
@endphp

<div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden hidden lg:block h-full">
    <div class="bg-zenis-primary text-white p-4 font-black flex items-center gap-3">
        <i class="hgi hgi-stroke hgi-menu-01"></i>
        <span class="uppercase tracking-widest text-sm">Browse Categories</span>
    </div>
    <ul class="py-2">
        @foreach($categories as $cat)
            <li class="group/cat relative">
                <a href="{{ route('category.products', [slug($cat->name), $cat->id]) }}" class="flex items-center justify-between px-5 py-3.5 text-[14px] font-bold text-gray-700 hover:text-zenis-primary hover:bg-gray-50 transition-all border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-3">
                        @if($cat->image)
                            <img src="{{ getImage(getFilePath('category') . '/' . $cat->image, getFileSize('category')) }}" class="size-5 object-contain opacity-70 group-hover/cat:opacity-100 group-hover/cat:scale-110 transition-all">
                        @endif
                        <span>{{ __($cat->name) }}</span>
                    </div>
                    @if($cat->subcategories->count())
                        <i class="hgi hgi-stroke hgi-arrow-right-01 text-[10px] text-gray-300 group-hover/cat:text-zenis-primary"></i>
                    @endif
                </a>

                @if($cat->subcategories->count())
                    <!-- Mega Dropdown -->
                    <div class="absolute top-0 left-full w-[240px] ml-1 bg-white shadow-2xl border border-gray-100 rounded-2xl opacity-0 invisible group-hover/cat:opacity-100 group-hover/cat:visible transition-all duration-300 z-50 p-4">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Sub Categories</h4>
                        <div class="space-y-1">
                            @foreach($cat->subcategories->take(10) as $sub)
                                <a href="{{ route('category.products', [slug($sub->name), $sub->id]) }}" class="block px-4 py-2 text-[13px] font-bold text-gray-600 hover:text-zenis-primary hover:bg-gray-50 rounded-xl transition-all">
                                    {{ __($sub->name) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
    <div class="p-3 bg-gray-50/50">
        <a href="{{ route('category.all') }}" class="flex items-center justify-center py-2.5 text-[12px] font-black text-gray-500 hover:text-zenis-primary transition-colors">
            View All Categories <i class="hgi hgi-stroke hgi-arrow-right-01 ml-2"></i>
        </a>
    </div>
</div>
