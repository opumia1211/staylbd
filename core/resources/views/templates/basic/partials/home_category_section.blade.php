@php
    $categories = $homeData['categories'] ?? collect();
    $sectionTitle = $sectionTitle ?? __('Shop by Category');
    $lines = $categories->groupBy(function ($c) { return (int) ($c->home_line ?? 1); })->sortKeys();
    $hasCategories = $categories->isNotEmpty();
@endphp
<section class="stayl-section home-category-section" aria-label="@lang('Category')">
    <div class="stayl-section-header">
        <h2 class="stayl-section-title">
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'category_icon', 'fallback' => 'layers', 'width' => 24, 'height' => 24, 'alt' => '', 'class' => 'text-[#10b981]'])
            <span>{{ $sectionTitle }}</span>
        </h2>
        <a href="{{ route('category.all') }}" class="stayl-section-link">
            @lang('View All')
            @include($activeTemplate . 'partials.icon', ['name' => 'chevron-right', 'sizePx' => 16])
        </a>
    </div>

    @if($hasCategories)
        <div class="home-category-section__viewport">
            @foreach($lines as $lineNum => $lineCategories)
                @if($lineCategories->isNotEmpty())
                    <div class="home-category-section__grid mb-4" data-auto-scroll="1" data-interval-sec="{{ $categoryScrollIntervalSec ?? 4 }}" data-scroll-direction="left">
                        @foreach($lineCategories as $category)
                            <a href="{{ route('category.products', [slug($category->name), $category->id]) }}" 
                               class="group relative block w-[148px] rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-sky-500 hover:shadow-xl transition-all duration-300" 
                               style="aspect-ratio: 1 / 1; min-height: 148px;"
                               title="{{ __($category->name) }}">
                                @if(!empty($category->image))
                                    <img src="{{ getImageWebP(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}"
                                         alt="{{ __($category->name) }}"
                                         loading="lazy"
                                         decoding="async"
                                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                         onerror="this.onerror=null;this.src='{{ stayl_placeholder_icon_data_url() }}';">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center bg-slate-50 dark:bg-slate-800">
                                        @include($activeTemplate . 'partials.icon', ['name' => 'layout-grid', 'sizePx' => 48, 'class' => 'opacity-20'])
                                    </div>
                                @endif
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent p-3 pt-8 transform translate-y-1 group-hover:translate-y-0 transition-transform duration-300">
                                    <span class="block text-[11px] font-bold text-center text-white uppercase tracking-wider truncate drop-shadow-md">
                                        {{ __($category->name) }}
                                    </span>
                                </div>
                                <div class="absolute inset-0 bg-sky-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </a>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="home-category-section__grid" aria-hidden="true">
            @for($s = 0; $s < 8; $s++)
                <div class="home-category-section__card">
                    <div class="home-category-section__card-media animate-pulse bg-slate-200"></div>
                </div>
            @endfor
        </div>
    @endif
</section>
