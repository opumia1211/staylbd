@props([
    'src' => '',
    'alt' => '',
    'class' => '',
    'loading' => 'lazy',
    'sizes' => '(max-width: 768px) 100vw, 50vw',
    'width' => null,
    'height' => null,
])

@php
    // Simple naive extension replacer for next-gen formats
    // In a real pipeline, Intervention Image/Spatie would generate these files on upload
    $webpSrc = preg_replace('/\.(jpe?g|png|gif)$/i', '.webp', $src);
    $avifSrc = preg_replace('/\.(jpe?g|png|gif)$/i', '.avif', $src);
    
    // Add base path if needed
    $url = url($src);
    $urlWebp = url($webpSrc);
    $urlAvif = url($avifSrc);
@endphp

<picture>
    {{-- Modern browsers that support AVIF --}}
    <source type="image/avif" srcset="{{ $urlAvif }}" sizes="{{ $sizes }}">
    {{-- Browsers that support WebP --}}
    <source type="image/webp" srcset="{{ $urlWebp }}" sizes="{{ $sizes }}">
    {{-- Fallback for legacy browsers (JPEG/PNG) --}}
    <img 
        src="{{ $url }}" 
        alt="{{ $alt }}" 
        class="{{ $class }}" 
        loading="{{ $loading }}" 
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        fetchpriority="{{ $loading === 'eager' ? 'high' : 'auto' }}"
    >
</picture>
