@php
    $services = getContent('service.element', false, null, true);
@endphp

@if($services->count() > 0)
<section class="stayl-section home-features-section">
    <div class="main-container">
        <div class="stayl-grid-responsive" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
            @foreach($services->take(4) as $service)
                @php
                    $img = @$service->data_values->image;
                    $url = @$service->data_values->url;
                    $title = __(@$service->data_values->title);
                    $detail = __(@$service->data_values->short_detail);
                @endphp
                
                <a href="{{ $url ? $url : 'javascript:void(0)' }}" 
                   class="stayl-feature-card group" 
                   data-theme-index="{{ $loop->index % 4 }}">
                    
                    <div class="stayl-feature-icon-box">
                        @if($img)
                            <img src="{{ getImageWebP(getFilePath('service') . '/' . $img, '100x100') }}" 
                                 alt="{{ $title }}" 
                                 class="stayl-feature-img">
                        @else
                            <i class="las la-shipping-fast stayl-feature-icon-fallback"></i>
                        @endif
                    </div>

                    <div class="stayl-feature-content">
                        <h5 class="stayl-feature-title">{{ $title }}</h5>
                        <p class="stayl-feature-desc">{{ $detail }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
