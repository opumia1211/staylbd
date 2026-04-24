@php
    $services = getContent('service.element', false, null, true);
    $themes = [
        ['bg' => '#FFF5E5', 'icon_bg' => '#FF9F00', 'shadow' => 'rgba(255, 159, 0, 0.2)'], // Orange
        ['bg' => '#E0F7FA', 'icon_bg' => '#00C1D4', 'shadow' => 'rgba(0, 193, 212, 0.2)'], // Cyan
        ['bg' => '#F0F9EB', 'icon_bg' => '#7ED321', 'shadow' => 'rgba(126, 211, 33, 0.2)'], // Green
        ['bg' => '#E0F2F1', 'icon_bg' => '#00BFA5', 'shadow' => 'rgba(0, 191, 165, 0.2)'], // Teal
    ];
@endphp

@if($services->count() > 0)
<section class="mt-[-20px] relative z-20 mb-[80px]">
    <div class="max-w-[1400px] mx-auto px-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-y-10 gap-x-8 md:gap-x-12 mt-10">
            @foreach($services as $service)
                @php
                    $current = $themes[$loop->index % 4];
                    $img = @$service->data_values->image;
                @endphp
                
                <div class="group relative flex items-center p-5 pl-12 rounded-2xl transition-all duration-400 hover:translate-y-[-5px]" 
                     style="background-color: {{ $current['bg'] }};">
                    
                    <!-- Icon Box (Offset to Left) -->
                    <div class="absolute -left-6 top-1/2 -translate-y-1/2 size-16 md:size-20 rounded-2xl flex items-center justify-center transition-all duration-400 group-hover:shadow-2xl shadow-lg ring-4 ring-white"
                         style="background-color: {{ $current['icon_bg'] }}; box-shadow: 0 10px 20px {{ $current['shadow'] }};">
                        
                        @if($img)
                            <img src="{{ getImageWebP(getFilePath('service') . '/' . $img, '100x100') }}" 
                                 alt="{{ __(@$service->data_values->title) }}" 
                                 class="w-10 h-10 md:w-12 md:h-12 object-contain filter brightness-0 invert opacity-100">
                        @else
                            <i class="fas fa-gift text-2xl md:text-3xl text-white"></i>
                        @endif
                    </div>

                    <!-- Text Content -->
                    <div class="flex flex-col">
                        <h5 class="font-bold text-[#1a202c] text-base md:text-lg leading-tight mb-0.5">
                            {{ __(@$service->data_values->title) }}
                        </h5>
                        <p class="text-[#718096] text-xs md:text-sm font-medium leading-relaxed">
                            {{ __(@$service->data_values->short_detail) }}
                        </p>
                    </div>

                    <!-- Hover Glow Effect -->
                    <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none ring-1 ring-white/50"></div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<style>
    .service-feature-card {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .service-feature-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>
@endif


