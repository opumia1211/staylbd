@php
    $brands = $homeData['brands'] ?? collect();
@endphp

@if($brands->isNotEmpty())
<section class="mb-[80px]">
    <div class="max-w-[1400px] mx-auto px-4">
        <div class="border-t border-b border-gray-100 py-10">
            <div class="flex items-center justify-between gap-10 overflow-x-auto no-scrollbar scroll-smooth">
                @foreach($brands as $brand)
                    <div class="shrink-0 grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300">
                        <img src="{{ getImageWebP(getFilePath('brand') . '/' . $brand->image, getFileSize('brand')) }}" alt="{{ $brand->name }}" class="h-12 w-auto object-contain">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
