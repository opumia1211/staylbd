@php
    $categories = $homeData['categories'] ?? collect();
    $sectionTitle = $sectionTitle ?? __('Shop by Category');
    $hasCategories = $categories->isNotEmpty();
    $sectionId = 'category-slider-' . uniqid();
    $intervalSec = $categoryScrollIntervalSec ?? 4;
@endphp

<section class="home-category-section relative mb-8" id="{{ $sectionId }}" aria-label="@lang('Category')">
    <div class="stayl-section-header">
        <h2 class="stayl-section-title">
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'category_icon', 'fallback' => 'layers', 'width' => 24, 'height' => 24, 'alt' => '', 'class' => 'text-[#6366f1] dark:text-indigo-400'])
            <span>{{ $sectionTitle }}</span>
        </h2>
        <a href="{{ route('category.all') }}" class="stayl-section-link">
            @lang('View All')
            @include($activeTemplate . 'partials.icon', ['name' => 'chevron-right', 'sizePx' => 16])
        </a>
    </div>

    @if($hasCategories)
        <div class="home-category-section__viewport relative">
            <div class="home-category-section__grid flex gap-[5px] overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory pb-4" data-auto-scroll="1" data-interval-sec="{{ $intervalSec }}">
                @foreach($categories as $category)
                    <a href="{{ route('category.products', [slug($category->name), $category->id]) }}" 
                       class="home-category-section__card group relative block flex-shrink-0 w-[200px] aspect-square rounded-[24px] overflow-hidden bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 transition-all duration-300 transform hover:-translate-y-1 snap-start" 
                       title="{{ __($category->name) }}">
                        @if(!empty($category->image))
                            <img src="{{ getImageWebP(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}"
                                 alt="{{ __($category->name) }}"
                                 loading="lazy"
                                 decoding="async"
                                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                 onerror="this.onerror=null;this.src='{{ stayl_placeholder_icon_data_url() }}';">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center bg-slate-100 dark:bg-slate-800">
                                @include($activeTemplate . 'partials.icon', ['name' => 'layout-grid', 'sizePx' => 54, 'class' => 'opacity-20'])
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-slate-900/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <span class="block text-base font-bold text-center text-white uppercase tracking-wider px-2 drop-shadow-md transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                {{ __($category->name) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="home-category-section__grid animate-pulse flex gap-[5px] overflow-x-auto no-scrollbar pb-4" aria-hidden="true">
            @for($s = 0; $s < 7; $s++)
                <div class="flex-shrink-0 w-[200px] aspect-square bg-slate-100 dark:bg-slate-800 rounded-[24px]"></div>
            @endfor
        </div>
    @endif
</section>
