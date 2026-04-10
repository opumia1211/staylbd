{{-- Local Inter preload: keep minimal to avoid "preloaded but not used" warnings. --}}
@once
@php
    $av = $assetVersion ?? config('app.asset_version') ?? config('app.version') ?? '1';
    $mode = $interPreloadMode ?? 'minimal';
    $interFontVer = function (string $file) use ($av) {
        $p = public_path('css/files/' . $file);
        return is_file($p) ? (string) filemtime($p) : $av;
    };
@endphp
<link rel="preload" href="{{ asset('css/files/inter-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
@if ($mode === 'admin-heavy')
<link rel="preload" href="{{ asset('css/files/inter-latin-600-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
@endif
@endonce
