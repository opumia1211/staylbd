@section('banners_triple')
<section class="mb-[80px]">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Banner 1 -->
        <a href="{{ route('products') }}" class="relative h-[250px] overflow-hidden rounded-lg group">
            <img src="{{ asset('assets/images/zenis/slider_2.jpg') }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="@lang('Promo')" loading="lazy" decoding="async">
            <div class="absolute inset-x-8 top-1/2 -translate-y-1/2 z-10">
                <h4 class="text-[#ffa500] font-bold text-sm mb-2">@lang('New Arrivals')</h4>
                <h3 class="text-xl font-bold text-gray-800 leading-tight mb-4">{!! __('Summer Collection') !!}<br>2026</h3>
                <span class="text-gray-600 font-bold border-b-2 border-[#ffa500] group-hover:pr-3 transition-all">@lang('SHOP NOW')</span>
            </div>
        </a>
        <!-- Banner 2 -->
        <a href="{{ route('products') }}" class="relative h-[250px] overflow-hidden rounded-lg group">
            <img src="{{ asset('assets/images/zenis/slider_3.jpg') }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="@lang('Promo')" loading="lazy" decoding="async">
            <div class="absolute inset-x-8 top-1/2 -translate-y-1/2 z-10">
                <h4 class="text-[#ffa500] font-bold text-sm mb-2">@lang('Up To 50% Off')</h4>
                <h3 class="text-xl font-bold text-white leading-tight mb-4">{!! __('Modern Furniture') !!}<br>@lang('Sale')</h3>
                <span class="text-white font-bold border-b-2 border-[#ffa500] group-hover:pr-3 transition-all">@lang('SHOP NOW')</span>
            </div>
        </a>
        <!-- Banner 3 -->
        <a href="{{ route('products') }}" class="relative h-[250px] overflow-hidden rounded-lg group">
            <img src="{{ asset('assets/images/zenis/slider_1.jpg') }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="@lang('Promo')" loading="lazy" decoding="async">
            <div class="absolute inset-x-8 top-1/2 -translate-y-1/2 z-10">
                <h4 class="text-[#ffa500] font-bold text-sm mb-2">@lang('Limited Edition')</h4>
                <h3 class="text-xl font-bold text-gray-800 leading-tight mb-4">{!! __('Acoustic Guitar') !!}<br>@lang('Series')</h3>
                <span class="text-gray-600 font-bold border-b-2 border-[#ffa500] group-hover:pr-3 transition-all">@lang('SHOP NOW')</span>
            </div>
        </a>
    </div>
</section>
@endsection

@section('banners_double')
<section class="mb-[80px]">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
         <!-- Banner 1 -->
         <a href="{{ route('products') }}" class="relative h-[300px] overflow-hidden rounded-lg group">
            <img src="{{ asset('assets/images/zenis/slider_1.jpg') }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="@lang('Promo')" loading="lazy" decoding="async">
            <div class="absolute left-10 top-1/2 -translate-y-1/2 z-10">
                <h4 class="text-[#ffa500] font-bold text-lg mb-2">@lang('Winter Special')</h4>
                <h3 class="text-3xl font-black text-gray-800 leading-tight mb-6">{!! __('High Quality') !!}<br>@lang('Fashion Collection')</h3>
                <span class="bg-[#ffa500] text-white px-6 py-3 rounded font-bold shadow-lg group-hover:bg-gray-800 transition-colors">@lang('EXPLORE NOW')</span>
            </div>
        </a>
        <!-- Banner 2 -->
        <a href="{{ route('products') }}" class="relative h-[300px] overflow-hidden rounded-lg group">
            <img src="{{ asset('assets/images/zenis/slider_2.jpg') }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="@lang('Promo')" loading="lazy" decoding="async">
            <div class="absolute left-10 top-1/2 -translate-y-1/2 z-10">
                <h4 class="text-[#ffa500] font-bold text-lg mb-2">@lang('Smart Gadgets')</h4>
                <h3 class="text-3xl font-black text-gray-800 leading-tight mb-6">{!! __('Latest Tech') !!}<br>@lang('Accessories')</h3>
                <span class="bg-gray-800 text-white px-6 py-3 rounded font-bold shadow-lg group-hover:bg-[#ffa500] transition-colors">@lang('SHOP NOW')</span>
            </div>
        </a>
    </div>
</section>
@endsection
