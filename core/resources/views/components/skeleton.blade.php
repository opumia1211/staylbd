@props(['type' => 'text', 'count' => 1, 'class' => ''])

<div class="stayl-skeleton-wrapper {{ $class }}">
    @for ($i = 0; $i < $count; $i++)
        @if($type === 'product')
            <div class="stayl-skeleton-product p-3 border border-slate-100 rounded-xl">
                <div class="stayl-skeleton-rect h-48 w-full bg-slate-200 rounded-lg animate-pulse mb-4"></div>
                <div class="stayl-skeleton-line h-4 w-3/4 bg-slate-200 rounded animate-pulse mb-2"></div>
                <div class="stayl-skeleton-line h-4 w-1/2 bg-slate-200 rounded animate-pulse mb-4"></div>
                <div class="flex justify-between items-center">
                    <div class="stayl-skeleton-line h-6 w-1/4 bg-slate-200 rounded animate-pulse"></div>
                    <div class="stayl-skeleton-rect h-8 w-8 bg-slate-200 rounded-full animate-pulse"></div>
                </div>
            </div>
        @elseif($type === 'category')
             <div class="stayl-skeleton-category flex flex-col items-center p-2">
                <div class="stayl-skeleton-rect h-16 w-16 bg-slate-200 rounded-full animate-pulse mb-2"></div>
                <div class="stayl-skeleton-line h-3 w-12 bg-slate-200 rounded animate-pulse"></div>
            </div>
        @else
            <div class="stayl-skeleton-line h-4 w-full bg-slate-200 rounded animate-pulse mb-2 {{ $class }}"></div>
        @endif
    @endfor
</div>

<style>
    .stayl-skeleton-rect, .stayl-skeleton-line {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: stayl-skeleton-loading 1.5s infinite;
    }
    @keyframes stayl-skeleton-loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
