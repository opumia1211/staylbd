@props([
    /** @var array<int, array{code: string, symbol?: string, country?: string, flag: string}> */
    'currencies' => [],
    'currentCurrency' => 'BDT',
])

@php
    $current = strtoupper(trim((string) $currentCurrency));
    $rows = collect($currencies)->values()->all();
    $currentRow = collect($rows)->first(fn ($r) => strtoupper((string) ($r['code'] ?? '')) === $current)
        ?? ($rows[0] ?? ['code' => $current, 'flag' => '']);
@endphp

<div {{ $attributes->merge(['class' => 'relative isolate overflow-visible z-[10001] group']) }}>
    <button
        type="button"
        class="flex cursor-pointer items-center gap-2 rounded-lg border-0 bg-transparent px-2 py-1.5 text-sm font-semibold tracking-tight text-white/95 ring-0 transition-colors hover:bg-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-300"
        aria-haspopup="listbox"
        aria-expanded="false"
    >
        @if(!empty($currentRow['flag']))
            <img
                src="{{ $currentRow['flag'] }}"
                alt=""
                class="w-5 h-4 shrink-0 rounded-none object-cover"
                width="20"
                height="20"
                loading="eager"
            />
        @endif
        <span id="staylCurrentCurrencyLabel" class="notranslate font-inter uppercase">{{ $currentRow['code'] ?? $current }}</span>
        <svg class="size-3.5 shrink-0 opacity-70 transition-transform duration-300 group-hover:rotate-180 group-focus-within:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div
        class="absolute right-0 top-full z-[10002] mt-1 hidden w-[228px] max-h-[min(320px,calc(100vh-120px))] overflow-hidden overflow-y-auto rounded-xl border border-slate-100 bg-white shadow-xl ring-1 ring-black/5 [scrollbar-width:thin] group-hover:block group-focus-within:block dark:border-slate-800 dark:bg-slate-900"
        role="listbox"
    >
        <div class="border-b border-slate-100 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-800/60">
            <span class="font-inter text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">{{ __('Currency') }}</span>
        </div>
        <div class="space-y-0.5 p-1 font-inter">
            @foreach($rows as $curr)
                @php
                    $code = strtoupper(trim((string) ($curr['code'] ?? '')));
                    $active = $code === $current;
                @endphp
                <a
                    href="#"
                    role="option"
                    aria-selected="{{ $active ? 'true' : 'false' }}"
                    data-stayl-currency-option="{{ $code }}"
                    class="flex cursor-pointer items-center justify-between rounded-lg px-3 py-2 text-left text-sm outline-none transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/80 {{ $active ? 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-400' : 'text-slate-800 dark:text-slate-200' }}"
                >
                    <span class="flex items-center gap-2.5 min-w-0">
                        @if(!empty($curr['flag']))
                            <img
                                src="{{ $curr['flag'] }}"
                                alt=""
                                class="w-5 h-4 shrink-0 rounded-none object-cover"
                                width="20"
                                height="20"
                                loading="lazy"
                            />
                        @endif
                        <span class="notranslate truncate font-semibold">{{ $code }}</span>
                    </span>
                    <svg
                        data-stayl-currency-check
                        class="size-4 shrink-0 text-emerald-500 dark:text-emerald-400 {{ $active ? '' : 'hidden' }}"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="2.5"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </a>
            @endforeach
        </div>
    </div>
</div>
