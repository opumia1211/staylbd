@php
    $categories = $homeData['categories'] ?? collect();
    $sectionTitle = $sectionTitle ?? __('Shop by Category');
    $lines = $categories->groupBy(function ($c) { return (int) ($c->home_line ?? 1); })->sortKeys();
    $hasCategories = $categories->isNotEmpty();
@endphp
<section class="home-category-section mb-6 sm:mb-10" aria-label="@lang('Category')">
    <div class="mb-4 flex flex-col gap-3 sm:mb-5 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="flex min-w-0 items-center gap-2 text-lg font-bold tracking-tight text-slate-900 sm:text-xl md:text-2xl">
            @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'category_icon', 'fallback' => 'layers', 'width' => 24, 'height' => 24, 'alt' => '', 'class' => 'shrink-0 text-emerald-600'])
            <span>{{ $sectionTitle }}</span>
        </h2>
        <a href="{{ route('category.all') }}" class="inline-flex shrink-0 items-center gap-1 rounded-lg px-1 py-0.5 text-sm font-semibold text-emerald-600 transition hover:text-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
            @lang('View All')
            @include($activeTemplate . 'partials.icon', ['name' => 'chevron-right', 'class' => 'text-emerald-600', 'sizePx' => 16])
        </a>
    </div>

    @if($hasCategories)
        @foreach($lines as $lineNum => $lineCategories)
            @if($lineCategories->isNotEmpty())
                {{-- Mobile 2 cols → md 4 cols → xl 6 cols --}}
                <div class="mb-6 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-4 xl:grid-cols-6 last:mb-0">
                    @foreach($lineCategories as $category)
                        <a href="{{ route('category.products', [slug($category->name), $category->id]) }}" class="group/cat block rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2" title="{{ __($category->name) }}">
                            <div class="overflow-hidden rounded-xl border border-slate-100/90 bg-white shadow-sm shadow-slate-200/50 ring-1 ring-slate-900/5 transition-all duration-300 ease-out group-hover/cat:scale-[1.03] group-hover/cat:shadow-lg group-hover/cat:shadow-slate-300/70 group-hover/cat:ring-slate-900/10">
                                <div class="aspect-square w-full bg-slate-50">
                                    @if(!empty($category->image))
                                        <img src="{{ getImageWebP(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}"
                                             alt="{{ __($category->name) }}"
                                             loading="lazy"
                                             decoding="async"
                                             width="256"
                                             height="256"
                                             class="h-full w-full object-cover object-center transition-transform duration-500 ease-out group-hover/cat:scale-110"
                                             onerror="this.onerror=null;this.src='{{ stayl_placeholder_icon_data_url() }}';">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-slate-300 transition-colors group-hover/cat:text-emerald-500">
                                            @include($activeTemplate . 'partials.icon', ['name' => 'layout-grid', 'class' => 'text-slate-300', 'sizePx' => 56])
                                        </div>
                                    @endif
                                </div>
                                <div class="border-t border-slate-100 px-2 py-2.5 text-center md:px-3 md:py-3">
                                    <span class="line-clamp-2 text-xs font-semibold leading-tight text-slate-800 md:text-sm">
                                        {{ __($category->name) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        @endforeach
    @else
        <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-4 xl:grid-cols-6" aria-hidden="true">
            @for($s = 0; $s < 6; $s++)
                <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
                    <div class="aspect-square animate-pulse bg-gradient-to-br from-slate-200 to-slate-100"></div>
                    <div class="space-y-2 border-t border-slate-100 px-2 py-2.5 md:px-3 md:py-3">
                        <div class="mx-auto h-3 w-3/4 animate-pulse rounded bg-slate-200"></div>
                        <div class="mx-auto h-3 w-1/2 animate-pulse rounded bg-slate-100"></div>
                    </div>
                </div>
            @endfor
        </div>
        <div class="mt-5 flex justify-center">
            <a href="{{ route('category.all') }}" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:from-emerald-500 hover:to-teal-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                @lang('Browse Products')
            </a>
        </div>
    @endif
</section>
