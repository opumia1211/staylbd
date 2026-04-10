@php
    $skeletonCount = isset($skeletonCount) ? max(1, min(12, (int) $skeletonCount)) : 6;
@endphp
@for($i = 0; $i < $skeletonCount; $i++)
    <div class="product-card-col product-card-col--home product-carousel__item" aria-hidden="true">
        <div class="flex h-full min-h-[400px] flex-col overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
            <div class="aspect-[4/5] w-full animate-pulse bg-gradient-to-br from-slate-200 to-slate-100"></div>
            <div class="flex flex-1 flex-col space-y-2.5 p-3 sm:p-3.5">
                <div class="h-4 animate-pulse rounded-md bg-slate-200"></div>
                <div class="h-4 w-3/4 animate-pulse rounded-md bg-slate-100"></div>
                <div class="h-3 w-1/3 animate-pulse rounded-md bg-slate-100"></div>
                <div class="mt-2 h-6 w-2/3 animate-pulse rounded-md bg-slate-200"></div>
                <div class="mt-auto grid grid-cols-2 gap-2 pt-3">
                    <div class="h-10 animate-pulse rounded-xl bg-slate-200"></div>
                    <div class="h-10 animate-pulse rounded-xl bg-slate-200"></div>
                </div>
            </div>
        </div>
    </div>
@endfor
