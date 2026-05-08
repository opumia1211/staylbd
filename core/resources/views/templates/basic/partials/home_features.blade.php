@php
    $services = getContent('service.element', false, null, true);
@endphp

@if($services->count() > 0)
<section class="stayl-section home-features-section">
    <div class="main-container">
        <div class="stayl-grid-responsive grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">
            @foreach($services->take(4) as $service)
                @php
                    $img = @$service->data_values->image;
                    $url = @$service->data_values->url;
                    $title = __(@$service->data_values->title);
                    $detail = __(@$service->data_values->short_detail);
                @endphp
                
                <a href="{{ $url ? $url : 'javascript:void(0)' }}" 
                   class="stayl-feature-card group rounded-xl p-3 sm:p-4 border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50/30 transition-all duration-300" 
                   data-theme-index="{{ $loop->index % 4 }}">
                    
                    <div class="stayl-feature-icon-box mb-2 sm:mb-3">
                        @if($img)
                            <img src="{{ getImageWebP(getFilePath('service') . '/' . $img, '100x100') }}" 
                                 alt="{{ $title }}" 
                                 loading="lazy"
                                 decoding="async"
                                 class="stayl-feature-img w-10 h-10 sm:w-12 sm:h-12 object-contain">
                        @else
                            <i class="las la-shipping-fast text-2xl sm:text-3xl text-emerald-500"></i>
                        @endif
                    </div>

                    <div class="stayl-feature-content">
                        <h5 class="stayl-feature-title text-sm sm:text-base font-bold text-slate-800">{{ $title }}</h5>
                        <p class="stayl-feature-desc text-xs sm:text-sm text-slate-500 line-clamp-1">{{ $detail }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
