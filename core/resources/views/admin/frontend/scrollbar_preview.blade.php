{{-- WYSIWYG admin preview: same partial, same CSS. Sandboxed HTML fragment. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scroll Bar Preview</title>
    @php $scrollCssVer = is_file(public_path('assets/templates/basic/css/scrollbar.css')) ? (string) filemtime(public_path('assets/templates/basic/css/scrollbar.css')) : '1'; @endphp
    <link rel="stylesheet" href="{{ url('serve-css/scrollbar') }}?v={{ $scrollCssVer }}">

</head>
<body class="st-scrollbar-admin-preview">
    <div class="preview-shell">
        @include(activeTemplate() . 'partials.scrollbar', ['bars' => $bars, 'position' => $position])
    </div>
</body>
</html>
