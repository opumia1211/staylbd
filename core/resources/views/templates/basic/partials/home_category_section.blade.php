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
                            <a href="{{ route('category.products', [slug($category->name), $category->id]) }}" class="home-category-section__card" title="{{ __($category->name) }}">
                                <div class="home-category-section__card-media">
                                    @if(!empty($category->image))
                                        <img src="{{ getImageWebP(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}"
                                             alt="{{ __($category->name) }}"
                                             loading="lazy"
                                             decoding="async"
                                             width="148"
                                             height="148"
                                             class="stayl-category-img"
                                             onerror="this.onerror=null;this.src='{{ stayl_placeholder_icon_data_url() }}';">
                                    @else
                                        <div class="home-category-section__card-icon">
                                            @include($activeTemplate . 'partials.icon', ['name' => 'layout-grid', 'sizePx' => 48])
                                        </div>
                                    @endif
                                </div>
                                <span class="home-category-section__card-label">
                                    {{ __($category->name) }}
                                </span>
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
