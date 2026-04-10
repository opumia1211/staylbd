/**
 * Purge unused rules from deferred legacy CSS (production only).
 * Wired from webpack.mix.js for tailwind-storefront-deferred*.css entrypoints.
 */
const purgecss = require('@fullhuman/postcss-purgecss');

const contentGlobs = [
    './resources/views/templates/**/*.blade.php',
    './resources/views/partials/**/*.blade.php',
    './resources/views/order_delivery_scanned.blade.php',
    './app/**/*.php',
    './resources/js/**/*.js',
];

module.exports = purgecss({
    content: contentGlobs,
    defaultExtractor: (content) => content.match(/[\w-/:.%[\]]+(?<!:)/g) || [],
    safelist: {
        standard: [
            'active',
            'show',
            'open',
            'is-open',
            'modal-open',
            'd-none',
            'hide',
            'mobile-tab-shell',
            'required',
            'is-pulsing',
            'has-submenu',
            'html',
            'body',
        ],
        deep: [
            /^fa-/,
            /^la-/,
            /^text--/,
            /^bg--/,
            /^b-radius/,
            /^box--/,
            /^widget-/,
            /^cmn-/,
            /^modal/,
            /^btn/,
            /^account-/,
            /^header/,
            /^footer/,
            /^product-/,
            /^cart-/,
            /^section/,
            /^breadcrumb/,
            /^dashboard/,
            /^scrollbar-/,
            /^gdpr-/,
            /^mobile-tab/,
            /^filter/,
            /^pro-section/,
            /^glass-/,
            /^show-cart/,
            /^show-wishlist/,
        ],
        greedy: [],
    },
});
