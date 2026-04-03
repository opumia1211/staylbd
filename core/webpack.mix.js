const mix = require('laravel-mix');
const fs = require('fs');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

if (fs.existsSync('resources/js/app.js')) {
    mix.js('resources/js/app.js', 'public/js');
}

if (fs.existsSync('resources/sass/app.scss')) {
    mix.sass('resources/sass/app.scss', 'public/css');
}

mix.postCss('resources/css/tailwind-storefront.css', 'public/css', [
    require('tailwindcss'),
    require('autoprefixer'),
]).options({
    processCssUrls: false,
}).sourceMaps();

mix.postCss('resources/css/tailwind-utilities.css', 'public/css/tailwind-utilities.css', [
    require('tailwindcss'),
    require('autoprefixer'),
]).options({
    processCssUrls: false,
});

mix.postCss('resources/css/tailwind-admin.css', 'public/css/tailwind-admin.css', [
    require('tailwindcss')('./tailwind.admin.config.js'),
    require('autoprefixer'),
]).options({
    processCssUrls: false,
});
