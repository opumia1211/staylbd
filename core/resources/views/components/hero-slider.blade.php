@props(['banners' => collect()])

<div class="relative w-full rounded-2xl lg:rounded-3xl overflow-hidden bg-gray-100 aspect-[16/9] lg:aspect-[21/9]" id="zenis-hero-slider">
    <div class="slider-wrapper h-full relative">
        @forelse($banners as $index => $banner)
            <div class="hero-slide absolute inset-0 opacity-0 transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'active opacity-100 z-10' : 'z-0' }}" data-index="{{ $index }}">
                @php
                    $dv = $banner->data_values;
                    $image = getImage(getFilePath('banner') . '/' . $banner->data_values->image, '1920x400');
                    $mobileImage = !empty($dv->mobile_image) ? getImage(getFilePath('banner') . '/' . $dv->mobile_image) : $image;
                @endphp
                
                <picture>
                    <source media="(max-width: 768px)" srcset="{{ $mobileImage }}">
                    <img src="{{ $image }}" class="w-full h-full object-cover" alt="Banner">
                </picture>

                <!-- Content Overlay -->
                <div class="absolute inset-0 bg-gradient-to-r from-black/40 via-transparent to-transparent flex items-center px-6 lg:px-20">
                    <div class="max-w-xl text-white transform translate-y-8 opacity-0 transition-all duration-700 delay-300 slide-content">
                        @if(!empty($dv->heading))
                            <h2 class="text-3xl lg:text-5xl font-black mb-4 leading-tight">
                                {{ __($dv->heading) }}
                            </h2>
                        @endif
                        @if(!empty($dv->subheading))
                            <p class="text-sm lg:text-lg mb-8 text-gray-100 font-medium">
                                {{ __($dv->subheading) }}
                            </p>
                        @endif
                        @if(!empty($dv->button_text))
                            <a href="{{ $dv->url ?? '#' }}" class="inline-flex items-center gap-3 bg-zenis-primary text-white px-8 py-4 rounded-xl font-black uppercase tracking-wider hover:bg-white hover:text-zenis-primary transition-all group">
                                <span>{{ __($dv->button_text) }}</span>
                                <i class="hgi hgi-stroke hgi-arrow-right-01 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
             <div class="flex items-center justify-center h-full bg-gray-200">
                <p class="text-gray-400 font-bold uppercase tracking-widest">No Banners Found</p>
             </div>
        @endforelse
    </div>

    <!-- Controls -->
    @if($banners->count() > 1)
        <div class="absolute bottom-10 right-10 z-20 flex gap-4">
            <button onclick="window.heroSlider.prev()" class="size-12 rounded-full border border-white/30 bg-black/20 text-white flex items-center justify-center hover:bg-zenis-primary hover:border-zenis-primary transition-all backdrop-blur-sm">
                <i class="hgi hgi-stroke hgi-arrow-left-01"></i>
            </button>
            <button onclick="window.heroSlider.next()" class="size-12 rounded-full border border-white/30 bg-black/20 text-white flex items-center justify-center hover:bg-zenis-primary hover:border-zenis-primary transition-all backdrop-blur-sm">
                <i class="hgi hgi-stroke hgi-arrow-right-01"></i>
            </button>
        </div>
        
        <!-- Indicators -->
        <div class="absolute bottom-10 left-10 z-20 flex gap-2">
            @foreach($banners as $index => $b)
                <button onclick="window.heroSlider.goTo({{ $index }})" class="hero-dot w-8 h-1 rounded-full bg-white/30 transition-all {{ $index === 0 ? 'active bg-white w-12' : '' }}" data-index="{{ $index }}"></button>
            @endforeach
        </div>
    @endif

    <style>
        .hero-slide.active .slide-content {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

    <script>
        window.addEventListener('load', function() {
            const container = document.getElementById('zenis-hero-slider');
            if (!container) return;
            const slides = container.querySelectorAll('.hero-slide');
            const dots = container.querySelectorAll('.hero-dot');
            let current = 0;
            let timer = null;

            window.heroSlider = {
                goTo: function(index) {
                    slides[current].classList.remove('active', 'opacity-100', 'z-10');
                    slides[current].classList.add('opacity-0', 'z-0');
                    dots[current].classList.remove('active', 'bg-white', 'w-12');
                    dots[current].classList.add('bg-white/30', 'w-8');
                    
                    current = (index + slides.length) % slides.length;
                    
                    slides[current].classList.add('active', 'opacity-100', 'z-10');
                    slides[current].classList.remove('opacity-0', 'z-0');
                    dots[current].classList.add('active', 'bg-white', 'w-12');
                    dots[current].classList.remove('bg-white/30', 'w-8');
                    this.resetTimer();
                },
                next: function() { this.goTo(current + 1); },
                prev: function() { this.goTo(current - 1); },
                startTimer: function() {
                    timer = setInterval(() => this.next(), 6000);
                },
                resetTimer: function() {
                    clearInterval(timer);
                    this.startTimer();
                }
            };
            window.heroSlider.startTimer();
        });
    </script>
</div>
