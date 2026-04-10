Line Awesome fonts
==================
CSS (line-awesome.min.css) references: la-brands-400.woff2, la-regular-400.woff2,
la-solid-900.woff2 (and .woff, .ttf, .eot, .svg).

To fix 404s for .woff2/.woff/.ttf:
1. Download Line Awesome from https://icons8.com/line-awesome or the official repo.
2. Copy from the fonts/ folder:
   la-brands-400.woff2, la-brands-400.woff, la-brands-400.ttf
   la-regular-400.woff2, la-regular-400.woff, la-regular-400.ttf
   la-solid-900.woff2, la-solid-900.woff, la-solid-900.ttf
   into this directory (assets/global/fonts/).

Existing .svg files work in some browsers; adding woff2/woff/ttf improves compatibility.
