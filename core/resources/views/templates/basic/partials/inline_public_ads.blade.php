@php
    use Illuminate\Support\Facades\Schema;

    $inlineAds = collect();
    $currentPath = '/' . ltrim(request()->path(), '/');
    $isHome = request()->routeIs('home') || $currentPath === '/';

    // Homepage inline ads are already controlled by HomepageLayoutService slots; avoid duplicate render here.
    if (!$isHome && Schema::hasTable('homepage_ad_slots')) {
        $inlineAds = \App\Models\HomepageAdSlot::query()
            ->where('is_active', true)
            ->where('position', 'inline')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->filter(function ($ad) use ($currentPath) {
                $mode = (string) ($ad->display_pages ?? 'all');
                if ($mode === 'homepage') {
                    return false;
                }
                if ($mode === 'custom_path') {
                    $p = trim((string) ($ad->custom_path ?? ''));
                    if ($p === '') {
                        return false;
                    }
                    $regex = '#^' . str_replace('\*', '.*', preg_quote($p, '#')) . '$#i';
                    return (bool) preg_match($regex, $currentPath);
                }
                return true;
            })
            ->values();
    }
@endphp

@if($inlineAds->isNotEmpty())
    @foreach($inlineAds as $ad)
        @include($activeTemplate . 'partials.homepage_ad_slot', ['ad' => $ad])
    @endforeach
@endif
