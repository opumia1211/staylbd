{{-- WYSIWYG admin preview: same partial, same CSS. Sandboxed HTML fragment. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scroll Bar Preview</title>
    <link rel="stylesheet" href="{{ asset(activeTemplate(true) . 'css/scrollbar.css') }}">
    <style>
        body {
            margin: 0;
            padding: 10px;
            background: linear-gradient(180deg, #f8fbff 0%, #eef4fa 100%);
        }
        .preview-shell {
            border: 1px solid rgba(148, 163, 184, 0.35);
            border-radius: 10px;
            padding: 6px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            overflow: hidden;
        }
        .preview-shell .scrollbar-segment--text {
            text-shadow: none;
            color: inherit;
            font-weight: 500;
        }
        .preview-shell .scrollbar-wrapper {
            min-height: 42px !important;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }
        .preview-shell .scrollbar-track {
            display: flex;
            align-items: center;
            min-height: 100%;
        }
        .preview-shell .scrollbar-content {
            line-height: 1.4;
        }
        /* In preview canvas, custom position should stay inside frame */
        .preview-shell .scrollbar-wrapper[data-position="custom"] {
            position: relative !important;
            left: auto !important;
            top: auto !important;
            width: 100% !important;
            max-width: 100% !important;
        }
    </style>
</head>
<body>
    <div class="preview-shell">
        @include(activeTemplate() . 'partials.scrollbar', ['bars' => $bars, 'position' => $position])
    </div>
</body>
</html>
