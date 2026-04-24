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
        @foreach($lines as $lineNum => $lineCategories)
            @if($lineCategories->isNotEmpty())
                <div class="stayl-grid-responsive mb-4">
                    @foreach($lineCategories as $category)
                        <a href="{{ route('category.products', [slug($category->name), $category->id]) }}" class="stayl-category-card" title="{{ __($category->name) }}">
                            <div class="stayl-category-media">
                                @if(!empty($category->image))
                                    <img src="{{ getImageWebP(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}"
                                         alt="{{ __($category->name) }}"
                                         loading="lazy"
                                         decoding="async"
                                         width="256"
                                         height="256"
                                         class="stayl-category-img"
                                         onerror="this.onerror=null;this.src='{{ stayl_placeholder_icon_data_url() }}';">
                                @else
                                    <div class="stayl-category-fallback">
                                        @include($activeTemplate . 'partials.icon', ['name' => 'layout-grid', 'class' => 'text-slate-300', 'sizePx' => 56])
                                    </div>
                                @endif
                            </div>
                            <span class="stayl-category-name">
                                {{ __($category->name) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        @endforeach
    @else
        <div class="stayl-grid-responsive" aria-hidden="true">
            @for($s = 0; $s < 6; $s++)
                <div class="stayl-category-card">
                    <div class="stayl-category-media animate-pulse bg-slate-200"></div>
                    <div class="p-3">
                        <div class="mx-auto h-3 w-3/4 animate-pulse rounded bg-slate-200"></div>
                    </div>
                </div>
            @endfor
        </div>
    @endif
</section>
