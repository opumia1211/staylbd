@php
    $categories = App\Models\Category::active()->with('subcategories', function($subcategories) {
                    $subcategories->active();
                })->latest()->get();
@endphp

<ul class="category-link sidebar-category-list">
    @foreach ($categories as $category)
        @php $hasSub = $category->subcategories->isNotEmpty(); @endphp
        <li class="{{ $hasSub ? 'has-submenu' : '' }}">
            <div class="sidebar-cat-row">
                <a href="{{ route('category.products', [slug($category->name), $category->id]) }}" class="sidebar-cat-link">
                    {{ __($category->name) }}
                </a>
                @if($hasSub)
                    <button type="button" class="sidebar-cat-expand" aria-label="@lang('Toggle subcategories')">
                        @include($activeTemplate . 'partials.icon', ['name' => 'chevron-down'])
                    </button>
                @endif
            </div>
            @if($hasSub)
            <ul class="sidebar-sublink">
                @foreach ($category->subcategories as $subcategory)
                    <li>
                        <a href="{{ route('subcategory.products', [slug($subcategory->name), $subcategory->id]) }}">
                            {{ __($subcategory->name) }}
                        </a>
                    </li>
                @endforeach
            </ul>
            @endif
        </li>
    @endforeach
    <li>
        <a href="{{ route('category.all') }}" class="sidebar-cat-link sidebar-cat-all">
            @lang('View All Categories')
        </a>
    </li>
</ul>
