@props([
    /** Optional: limit to configured site languages (@see Language model); empty = middleware-supported set */
])

@php
    $definitions = [
        'en' => ['name' => 'English', 'flag' => 'us', 'native' => 'English'],
        'bn' => ['name' => 'Bengali', 'flag' => 'bd', 'native' => 'বাংলা'],
        'hi' => ['name' => 'Hindi', 'flag' => 'in', 'native' => 'हिन्दी'],
        'ar' => ['name' => 'Arabic', 'flag' => 'sa', 'native' => 'العربية'],
        'ur' => ['name' => 'Urdu', 'flag' => 'pk', 'native' => 'اردو'],
        'ru' => ['name' => 'Russian', 'flag' => 'ru', 'native' => 'Русский'],
        'zh' => ['name' => 'Chinese', 'flag' => 'cn', 'native' => '中文'],
        'es' => ['name' => 'Spanish', 'flag' => 'es', 'native' => 'Español'],
        'fr' => ['name' => 'French', 'flag' => 'fr', 'native' => 'Français'],
        'de' => ['name' => 'German', 'flag' => 'de', 'native' => 'Deutsch'],
        'pt' => ['name' => 'Portuguese', 'flag' => 'pt', 'native' => 'Português'],
        'ja' => ['name' => 'Japanese', 'flag' => 'jp', 'native' => '日本語'],
    ];

    $currentLocale = strtolower(app()->getLocale());
    $rows = [];

    try {
        $multi = (bool) (gs('multi_language') ?? true);
        if ($multi && class_exists(\App\Models\Language::class)) {
            foreach (\App\Models\Language::query()->orderBy('name')->get() as $l) {
                $code = strtolower(trim((string) ($l->code ?? '')));
                if ($code === '' || ! isset($definitions[$code])) {
                    continue;
                }
                $rows[] = ['code' => $code] + $definitions[$code];
            }
        }
    } catch (\Throwable $e) {
        $rows = [];
    }

    if ($rows === []) {
        foreach ($definitions as $code => $meta) {
            $rows[] = ['code' => $code] + $meta;
        }
    }

    usort($rows, fn ($a, $b) => strcmp($a['native'] ?? $a['name'], $b['native'] ?? $b['name']));

    $currentMeta = collect($rows)->firstWhere('code', $currentLocale) ?? (($definitions[$currentLocale] ?? null)
        ? ['code' => $currentLocale] + $definitions[$currentLocale]
        : ['code' => 'en'] + $definitions['en']);

    $segments = request()->segments();
    $codes = collect($definitions)->keys()->map(fn ($c) => strtolower((string) $c))->all();
    if (count($segments) > 0 && in_array(strtolower((string) $segments[0]), $codes, true)) {
        array_shift($segments);
    }
    $basePath = implode('/', $segments);
    $queryString = request()->getQueryString();
@endphp

<div {{ $attributes->merge(['class' => 'relative isolate overflow-visible z-[10001] group']) }}>
    <button
        type="button"
        class="flex cursor-pointer items-center gap-2 rounded-lg border-0 bg-transparent px-2 py-1.5 text-sm font-semibold tracking-tight text-white/95 ring-0 transition-colors hover:bg-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-300"
        aria-haspopup="listbox"
        aria-expanded="false"
    >
        <img
            src="https://flagcdn.com/w40/{{ $currentMeta['flag'] }}.png"
            srcset="https://flagcdn.com/w80/{{ $currentMeta['flag'] }}.png 2x"
            alt="{{ $currentMeta['name'] }}"
            class="w-5 h-4 shrink-0 rounded-none object-cover"
            width="20"
            height="20"
            loading="eager"
        />
        <span id="staylCurrentLanguageLabel" class="font-inter uppercase notranslate">{{ strtoupper($currentLocale === '' ? ($currentMeta['code'] ?? 'en') : $currentLocale) }}</span>
        <svg class="size-3.5 shrink-0 opacity-70 transition-transform duration-300 group-hover:rotate-180 group-focus-within:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div
        class="absolute right-0 top-full z-[10002] mt-1 hidden w-[228px] max-h-[min(320px,calc(100vh-120px))] overflow-hidden overflow-y-auto rounded-xl border border-slate-100 bg-white shadow-xl ring-1 ring-black/5 [scrollbar-width:thin] group-hover:block group-focus-within:block dark:border-slate-800 dark:bg-slate-900"
        role="listbox"
    >
        <div class="border-b border-slate-100 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-800/60">
            <span class="font-inter text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">{{ __('Language') }}</span>
        </div>
        <div class="space-y-0.5 p-1 font-inter">
            @foreach($rows as $lang)
                @php
                    $code = $lang['code'];
                    $targetUrl = url($code . ($basePath ? '/' . $basePath : ''));
                    if ($queryString) {
                        $targetUrl .= '?' . $queryString;
                    }
                    $active = $currentLocale === $code;
                @endphp
                <a
                    href="{{ $targetUrl }}"
                    role="option"
                    aria-selected="{{ $active ? 'true' : 'false' }}"
                    class="flex items-center justify-between rounded-lg px-3 py-2 text-left text-sm outline-none transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/80 {{ $active ? 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-400' : 'text-slate-800 dark:text-slate-200' }}"
                    data-stayl-lang-option="{{ $code }}"
                    data-no-ajax
                >
                    <span class="flex min-w-0 items-center gap-2.5">
                        <img
                            src="https://flagcdn.com/w40/{{ $lang['flag'] }}.png"
                            srcset="https://flagcdn.com/w80/{{ $lang['flag'] }}.png 2x"
                            alt="{{ $lang['name'] }}"
                            class="w-5 h-4 shrink-0 rounded-none object-cover"
                            width="20"
                            height="20"
                            loading="lazy"
                        />
                        <span class="truncate font-semibold">{{ $lang['native'] }}</span>
                    </span>
                    @if($active)
                        <svg class="size-4 shrink-0 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
