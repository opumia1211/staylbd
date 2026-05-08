@props(['title', 'description', 'image' => null, 'icon' => 'las la-shipping-fast', 'url' => '#'])

<a href="{{ $url }}" class="flex items-center gap-4 p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
    <div class="flex-shrink-0 w-14 h-14 flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/30 rounded-xl group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
        @if($image)
            <img src="{{ $image }}" alt="{{ $title }}" class="w-10 h-10 object-contain group-hover:brightness-0 group-hover:invert transition-all">
        @else
            <i class="{{ $icon }} text-2xl text-emerald-600 dark:text-emerald-400 group-hover:text-white transition-colors"></i>
        @endif
    </div>
    <div class="flex-grow">
        <h5 class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-0.5 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $title }}</h5>
        <p class="text-[12px] text-slate-500 dark:text-slate-400 leading-tight">{{ $description }}</p>
    </div>
</a>
