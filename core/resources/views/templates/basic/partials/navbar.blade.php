@php
    $categories = App\Models\Category::active()->with('subcategories', function($subcategories) {
                    $subcategories->active();
                })->latest()->get();
@endphp

<ul class="category-link">
    @foreach ($categories as $category)
        <li>
            <a href="{{ route('category.products', [slug($category->name), $category->id]) }}">
                {{ __($category->name) }}
            </a>
            <ul class="category-sublink">
                @forelse ($category->subcategories as $subcategory)
                    <li>
                        <a href="{{ route('subcategory.products', [slug($subcategory->name), $subcategory->id]) }}">
                            {{ __($subcategory->name) }}
                        </a>
                    </li>
                @empty
                    <li class="p-2">
                        @lang('Subcategory not found')
                    </li>
                @endforelse
            </ul>
        </li>
    @endforeach
    <li>
        <a href="{{ route('category.all') }}">
            @lang('View All Categories')
        </a>
    </li>
</ul>
