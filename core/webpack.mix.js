const mix = require('laravel-mix');
const fs = require('fs');
const path = require('path');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | CSS pipeline: Tailwind runs only through PostCSS here (mix.postCss).
 | Production MUST NOT load Tailwind from any CDN; ships as compiled
 | public/css/tailwind-homepage.css, tailwind-product.css, tailwind-admin.css (+ serve-css routes).
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

if (mix.inProduction()) {
    mix.version();
}

if (fs.existsSync('resources/js/app.js')) {
    mix.js('resources/js/app.js', 'public/js');
}

mix.js('resources/js/storefront-echo.js', 'public/js');
mix.js('resources/js/storefront-listing-realtime.js', 'public/js');
mix.js('resources/js/storefront-lucide.js', 'public/js');

if (fs.existsSync('resources/sass/app.scss')) {
    mix.sass('resources/sass/app.scss', 'public/css');
}

/* Bootstrap subset: compiled by `npm run sass:storefront` before Mix — postcss-import must read an existing public/css/bootstrap-storefront.css (parallel Mix.sass ran too late). */

function storefrontDeferredPostCssPlugins() {
    const plugins = [require('postcss-import')];
    if (mix.inProduction() && process.env.STOREFRONT_DEFERRED_PURGE !== '0') {
        plugins.push(require('./postcss-purgecss-deferred.cjs'));
    }
    plugins.push(require('autoprefixer'));
    return plugins;
}

mix.postCss('resources/css/tailwind-homepage.css', 'public/css/tailwind-homepage.css', [
    require('postcss-import'),
    require('tailwindcss')('./tailwind.homepage.config.js'),
    require('autoprefixer'),
]).options({
    processCssUrls: false,
    cssNano: mix.inProduction() ? { preset: ['default', { discardComments: { removeAll: true } }] } : false
}).sourceMaps();

mix.postCss('resources/css/tailwind-product.css', 'public/css/tailwind-product.css', [
    require('postcss-import'),
    require('tailwindcss')('./tailwind.product.config.js'),
    require('autoprefixer'),
]).options({
    processCssUrls: false,
    cssNano: mix.inProduction() ? { preset: ['default', { discardComments: { removeAll: true } }] } : false
}).sourceMaps();

/* Async-loaded legacy CSS — core deferred + feature splits (cart / account / compare); PurgeCSS in production unless STOREFRONT_DEFERRED_PURGE=0 */
mix.postCss('resources/css/tailwind-storefront-deferred.css', 'public/css/tailwind-storefront-deferred.css', storefrontDeferredPostCssPlugins()).options({
    processCssUrls: false,
    cssNano: mix.inProduction() ? { preset: ['default', { discardComments: { removeAll: true } }] } : false
});

mix.postCss('resources/css/tailwind-storefront-deferred-cart.css', 'public/css/tailwind-storefront-deferred-cart.css', storefrontDeferredPostCssPlugins()).options({
    processCssUrls: false,
    cssNano: mix.inProduction() ? { preset: ['default', { discardComments: { removeAll: true } }] } : false
});

mix.postCss('resources/css/tailwind-storefront-deferred-account.css', 'public/css/tailwind-storefront-deferred-account.css', storefrontDeferredPostCssPlugins()).options({
    processCssUrls: false,
    cssNano: mix.inProduction() ? { preset: ['default', { discardComments: { removeAll: true } }] } : false
});

mix.postCss('resources/css/tailwind-storefront-deferred-compare.css', 'public/css/tailwind-storefront-deferred-compare.css', storefrontDeferredPostCssPlugins()).options({
    processCssUrls: false,
    cssNano: mix.inProduction() ? { preset: ['default', { discardComments: { removeAll: true } }] } : false
});

mix.postCss('resources/css/tailwind-storefront-deferred-home.css', 'public/css/tailwind-storefront-deferred-home.css', storefrontDeferredPostCssPlugins()).options({
    processCssUrls: false,
    cssNano: mix.inProduction() ? { preset: ['default', { discardComments: { removeAll: true } }] } : false
});

mix.postCss('resources/css/tailwind-utilities.css', 'public/css/tailwind-utilities.css', [
    require('tailwindcss'),
    require('autoprefixer'),
]).options({
    processCssUrls: false,
    cssNano: mix.inProduction() ? { preset: ['default', { discardComments: { removeAll: true } }] } : false
});

mix.postCss('resources/css/tailwind-admin.css', 'public/css/tailwind-admin.css', [
    require('tailwindcss')('./tailwind.admin.config.js'),
    require('autoprefixer'),
]).options({
    processCssUrls: false,
    cssNano: mix.inProduction() ? { preset: ['default', { discardComments: { removeAll: true } }] } : false
});

mix.postCss('resources/css/admin-tailwind-utilities.css', 'public/css/admin-tailwind-utilities.css', [
    require('tailwindcss')('./tailwind.admin.config.js'),
    require('autoprefixer'),
]).options({
    processCssUrls: false,
    cssNano: mix.inProduction() ? { preset: ['default', { discardComments: { removeAll: true } }] } : false
});

/* Blade-extracted rules (no @tailwind) */
mix.postCss('resources/css/critical-storefront.css', 'public/css/critical-storefront.css', [
    require('autoprefixer'),
]).options({
    processCssUrls: false,
    cssNano: mix.inProduction() ? { preset: ['default', { discardComments: { removeAll: false } }] } : false
});

mix.postCss('resources/css/critical-admin.css', 'public/css/critical-admin.css', [
    require('autoprefixer'),
]).options({
    processCssUrls: false,
    cssNano: mix.inProduction() ? { preset: ['default', { discardComments: { removeAll: false } }] } : false
});

mix.copyDirectory('node_modules/@fontsource/inter/files', 'public/css/files');
/* Icon fonts (Line Awesome sidebar + Font Awesome in admin) — local woff2 so submenu works offline */
if (fs.existsSync(path.join(__dirname, 'node_modules/line-awesome/dist/line-awesome/fonts'))) {
    mix.copyDirectory('node_modules/line-awesome/dist/line-awesome/fonts', 'public/assets/global/fonts');
}
if (fs.existsSync(path.join(__dirname, 'node_modules/line-awesome/dist/font-awesome-line-awesome/webfonts'))) {
    mix.copyDirectory(
        'node_modules/line-awesome/dist/font-awesome-line-awesome/webfonts',
        'public/assets/global/webfonts'
    );
}

/*
 |--------------------------------------------------------------------------
 | Post-build: fix url() in tailwind-admin.css (subdir-safe)
 |--------------------------------------------------------------------------
 | Loaded as {appBase}/serve-css/tailwind-admin → resolving dir is …/serve-css/.
 | Use ../css/files and ../assets/... so Font Awesome / Line Awesome / Inter
 | work on localhost/staylbd/subdir/… (root-absolute /assets/… would 404).
 | Same ../ paths work if the file is served as {appBase}/css/tailwind-admin.css.
 */
function fixServeCssBundleUrls(cssFilePath) {
    if (!fs.existsSync(cssFilePath)) {
        return;
    }
    let css = fs.readFileSync(cssFilePath, 'utf8');
    /* Inter (@fontsource) */
    css = css.replace(/url\(\.\/files\//g, 'url(../css/files/');
    css = css.replace(/url\(files\//g, 'url(../css/files/');
    css = css.replace(/url\(\/css\/files\//g, 'url(../css/files/');
    /* Font Awesome + Line Awesome */
    css = css.replace(/url\(\.\.\/webfonts\//g, 'url(../assets/global/webfonts/');
    css = css.replace(/url\(webfonts\//g, 'url(../assets/global/webfonts/');
    css = css.replace(/url\(\/assets\/global\/webfonts\//g, 'url(../assets/global/webfonts/');
    css = css.replace(/url\(\.\.\/fonts\//g, 'url(../assets/global/fonts/');
    css = css.replace(/url\(fonts\/la-/g, 'url(../assets/global/fonts/la-');
    css = css.replace(/url\(\/assets\/global\/fonts\//g, 'url(../assets/global/fonts/');
    /* Admin-only image uploader */
    css = css.replace(/url\(\/assets\/admin\/font\//g, 'url(../assets/admin/font/');
    fs.writeFileSync(cssFilePath, css);
}

mix.then(() => {
    const headStorefront = path.join(__dirname, 'resources/css/critical-storefront-head.css');
    const appendHead = (cssFile) => {
        if (fs.existsSync(cssFile) && fs.existsSync(headStorefront)) {
            fs.appendFileSync(cssFile, '\n' + fs.readFileSync(headStorefront, 'utf8'));
        }
    };
    const twHome = path.join(__dirname, 'public/css/tailwind-homepage.css');
    const twProduct = path.join(__dirname, 'public/css/tailwind-product.css');
    appendHead(twHome);
    appendHead(twProduct);

    /* Legacy filename: same bytes as product bundle (CDN/bookmarks). */
    if (fs.existsSync(twProduct)) {
        const twLegacy = path.join(__dirname, 'public/css/tailwind-storefront.css');
        fs.copyFileSync(twProduct, twLegacy);
        fixServeCssBundleUrls(twLegacy);
        fs.copyFileSync(twProduct, path.join(__dirname, 'public/css/product.css'));
    }
    if (fs.existsSync(twHome)) {
        fs.copyFileSync(twHome, path.join(__dirname, 'public/css/homepage.css'));
    }
    const twAdminBuilt = path.join(__dirname, 'public/css/tailwind-admin.css');
    if (fs.existsSync(twAdminBuilt)) {
        fs.copyFileSync(twAdminBuilt, path.join(__dirname, 'public/css/admin.css'));
    }

    const twAdmin = path.join(__dirname, 'public/css/tailwind-admin.css');
    const critAdmin = path.join(__dirname, 'public/css/critical-admin.css');
    if (fs.existsSync(twAdmin) && fs.existsSync(critAdmin)) {
        fs.appendFileSync(twAdmin, '\n' + fs.readFileSync(critAdmin, 'utf8'));
    }

    fixServeCssBundleUrls(path.join(__dirname, 'public/css/tailwind-admin.css'));
    fixServeCssBundleUrls(twHome);
    fixServeCssBundleUrls(twProduct);
    const deferredChunks = [
        'tailwind-storefront-deferred.css',
        'tailwind-storefront-deferred-home.css',
        'tailwind-storefront-deferred-cart.css',
        'tailwind-storefront-deferred-account.css',
        'tailwind-storefront-deferred-compare.css',
    ];
    deferredChunks.forEach((f) => {
        const p = path.join(__dirname, 'public/css', f);
        if (fs.existsSync(p)) {
            fixServeCssBundleUrls(p);
        }
    });
});
