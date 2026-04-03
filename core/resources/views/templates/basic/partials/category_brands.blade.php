@php
    $brands     = App\Models\Brand::active()->featured()->latest()->take(6)->get();
    if ($brands->isEmpty()) { $brands = App\Models\Brand::active()->latest()->take(6)->get(); }
    $categories = App\Models\Category::active()->featured()->latest()->limit(6)->get();
    if ($categories->isEmpty()) { $categories = App\Models\Category::active()->latest()->take(6)->get(); }
@endphp

<section class="pro-section pro-section--tight top-categories-brands-section top-categories-brands-section--below-banner">
    <div class="container-fluid px-2 px-md-3 px-lg-4">
        <div class="row row--gap-5mm">
            <div class="col-lg-6">
                <div class="pro-section__head">
                    <h2 class="pro-section__title">@include($activeTemplate . 'partials.icon', ['name' => 'th-large']) @lang('Top Categories')</h2>
                    <a href="{{ route('category.all') }}" class="pro-section__link">@lang('Show All')</a>
                </div>
                <div class="pro-cat-brand-grid">
                    @forelse($categories as $category)
                        <a class="pro-cat-brand-card" href="{{ route('category.products', [slug($category->name), $category->id]) }}">
                            <span class="pro-cat-brand-card__img">
                                @if($category->image)
                                    <img src="{{ $category->imageShow() }}" alt="{{ __($category->name) }}" loading="lazy" width="80" height="80">
                                @else
                                    @include($activeTemplate . 'partials.icon', ['name' => 'folder'])
                                @endif
                            </span>
                            <span class="pro-cat-brand-card__name">{{ __($category->name) }}</span>
                            @include($activeTemplate . 'partials.icon', ['name' => 'arrow-right', 'class' => 'pro-cat-brand-card__arrow'])
                        </a>
                    @empty
                        <div class="pro-section__empty py-4">
                            <p class="text-muted mb-0 small">@lang('No categories yet.')</p>
                            <a href="{{ route('category.all') }}" class="btn btn-outline-primary btn-sm mt-2">@lang('View All')</a>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="col-lg-6">
                <div class="pro-section__head">
                    <h2 class="pro-section__title">@include($activeTemplate . 'partials.icon', ['name' => 'copyright']) @lang('Top Brands')</h2>
                    <a href="{{ route('brand.all') }}" class="pro-section__link">@lang('Show All')</a>
                </div>
                <div class="pro-cat-brand-grid">
                    @forelse($brands as $brand)
                        <a class="pro-cat-brand-card" href="{{ route('brand.products', [slug($brand->name), $brand->id]) }}">
                            <span class="pro-cat-brand-card__img">
                                @if($brand->image)
                                    <img src="{{ $brand->imageShow() }}" alt="{{ __($brand->name) }}" loading="lazy" width="80" height="80">
                                @else
                                    @include($activeTemplate . 'partials.icon', ['name' => 'tag'])
                                @endif
                            </span>
                            <span class="pro-cat-brand-card__name">{{ __($brand->name) }}</span>
                            @include($activeTemplate . 'partials.icon', ['name' => 'arrow-right', 'class' => 'pro-cat-brand-card__arrow'])
                        </a>
                    @empty
                        <div class="pro-section__empty py-4">
                            <p class="text-muted mb-0 small">@lang('No brands yet.')</p>
                            <a href="{{ route('brand.all') }}" class="btn btn-outline-primary btn-sm mt-2">@lang('View All')</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
