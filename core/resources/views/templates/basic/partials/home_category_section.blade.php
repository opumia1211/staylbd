@php
    $categories = $homeData['categories'] ?? collect();
    $catIntervalSec = isset($categoryScrollIntervalSec) ? (int) $categoryScrollIntervalSec : 4;
    $intervalSec = max(2, min(30, $catIntervalSec));
    $sectionTitle = $sectionTitle ?? __('Category');
    // home_line থাকলে লাইন অনুযায়ী গ্রুপ, না থাকলে এক লাইনে সব
    $lines = $categories->groupBy(function ($c) { return (int) ($c->home_line ?? 1); })->sortKeys();
@endphp
@if($categories->isNotEmpty())
<section class="home-category-section wow fadeInUp" aria-label="@lang('Category')" data-wow-duration="0.4s" data-wow-delay="0s">
    {{-- শুধু টাইটেল: main-container প্যাডিং — কার্ড সারিতে কোনো px-4/extra গাটার নেই (প্রোডাক্ট সারির মতো ফুল-ওয়idth) --}}
    <h2 class="home-category-section__title">{{ $sectionTitle }}</h2>
    @foreach($lines as $lineNum => $lineCategories)
        @if($lineCategories->isNotEmpty())
        <div class="home-category-section__viewport">
        <div class="pass-scroll" data-scroll-step="0.75">
            <div class="home-category-section__grid pass-scroll__track"
                 data-auto-scroll="1"
                 data-interval-sec="{{ $intervalSec }}">
                @foreach($lineCategories as $category)
                    <a href="{{ route('category.products', [slug($category->name), $category->id]) }}" class="home-category-section__card" title="{{ __($category->name) }}">
                        <span class="home-category-section__card-media">
                            @if(!empty($category->image))
                                <img src="{{ getImageWebP(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}"
                                     alt="{{ __($category->name) }}"
                                     loading="lazy"
                                     decoding="async"
                                     width="120"
                                     height="120">
                            @else
                                @include($activeTemplate . 'partials.icon', ['name' => 'th-large', 'class' => 'home-category-section__card-icon'])
                            @endif
                        </span>
                        <span class="home-category-section__card-label">{{ \Illuminate\Support\Str::limit(__($category->name), 22) }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        </div>
        @endif
    @endforeach
</section>
@endif
