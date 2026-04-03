Font Awesome webfonts
====================
CSS (all.min.css) references: fa-brands-400.woff2, fa-brands-400.woff, fa-brands-400.ttf,
fa-regular-400.*, fa-solid-900.* (and .eot, .svg).

To fix 404s for .woff2/.woff/.ttf:
1. Download Font Awesome Free from https://fontawesome.com/download (e.g. 5.15.4).
2. Extract and copy from webfonts/ folder:
   fa-brands-400.woff2, fa-brands-400.woff, fa-brands-400.ttf
   fa-regular-400.woff2, fa-regular-400.woff, fa-regular-400.ttf
   fa-solid-900.woff2, fa-solid-900.woff, fa-solid-900.ttf
   into this directory (assets/global/webfonts/).

Existing .svg files work in some browsers; adding woff2/woff/ttf improves compatibility.
