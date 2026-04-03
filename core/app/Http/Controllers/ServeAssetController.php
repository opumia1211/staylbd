<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves static JS/CSS with correct MIME type when the server returns 404 (e.g. subdirectory setup).
 * Fixes "Refused to execute script because its MIME type ('text/html') is not executable."
 * Fixes "Refused to apply style because MIME type 'text/html' is not supported."
 */
class ServeAssetController extends Controller
{
    private const ALLOWED_JS = ['fly-to-header', 'product-carousel', 'glass-header', 'auth'];

    /** Template basic CSS files allowed to be served with text/css (fixes MIME type 'text/html' when asset() returns 404) */
    private const ALLOWED_CSS = [
        'main', 'custom', 'glass-header', 'jquery-ui.min', 'animate', 'lightbox.min', 'owl.min', 'slick',
        'professional-filter', 'logo-effects', 'scrollbar', 'contact-chat', 'floating-actions',
        'products-listing', 'gdpr-cookie', 'footer-glass', 'products-section-pro', 'product-carousel', 'cart-page',
        'dashboard', 'track-order', 'product-list-screenshot', 'user-pages', 'dashboard-sidebar',
        'compare', 'wishlist-responsive', 'wishlist-buttons', 'user-list-pages-layout', 'user-list-pages-common', 'auth-modal',
    ];

    /** Global CSS (assets/global/css/) – served with text/css to fix "Verify stylesheet URLs" */
    private const ALLOWED_GLOBAL_CSS = ['bootstrap.min', 'all.min', 'line-awesome.min', 'product-card-buttons', 'glass-product-card'];

    /**
     * Serve a template JS file with Content-Type: application/javascript.
     */
    public function js(Request $request, string $name): Response|BinaryFileResponse
    {
        if (!in_array($name, self::ALLOWED_JS, true)) {
            abort(404);
        }

        $path = public_path('assets/templates/basic/js/' . $name . '.js');
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/javascript; charset=UTF-8',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Serve a template CSS file with Content-Type: text/css.
     */
    public function css(Request $request, string $name): Response|BinaryFileResponse
    {
        if (!in_array($name, self::ALLOWED_CSS, true)) {
            abort(404);
        }

        $path = public_path('assets/templates/basic/css/' . $name . '.css');
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Serve global CSS (assets/global/css/) with Content-Type: text/css.
     */
    public function cssGlobal(Request $request, string $name): Response|BinaryFileResponse
    {
        if (!in_array($name, self::ALLOWED_GLOBAL_CSS, true)) {
            abort(404);
        }

        $path = public_path('assets/global/css/' . $name . '.css');
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Serve template image (e.g. footer-bg.png). CSS url(./img/…) when loaded from serve-css resolves to /serve-css/img/…
     * Place files in: public/assets/templates/basic/images/
     */
    public function imageTemplate(Request $request, string $name): Response|BinaryFileResponse
    {
        if (str_contains($name, '..') || str_contains($name, '/') || str_contains($name, '\\')) {
            abort(404);
        }
        $path = public_path('assets/templates/basic/images/' . $name);
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }
        $mime = match (strtolower(pathinfo($name, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };
        return response()->file($path, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Serve Font Awesome webfonts. CSS from serve-css/global/all.min references ../webfonts/ → /serve-css/webfonts/
     * Place files in: public/assets/global/webfonts/ (fa-brands-400.woff2, fa-regular-400.woff2, fa-solid-900.woff2, etc.)
     */
    public function webfonts(Request $request, string $name): Response|BinaryFileResponse
    {
        if (str_contains($name, '..') || str_contains($name, '/') || str_contains($name, '\\')) {
            abort(404);
        }
        $path = public_path('assets/global/webfonts/' . $name);
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }
        $mime = $this->fontMime($name);
        return response()->file($path, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Serve Line Awesome fonts. CSS from serve-css/global/line-awesome.min references ../fonts/ → /serve-css/fonts/
     * Place files in: public/assets/global/fonts/ (la-solid-900.woff2, la-brands-400.woff2, la-regular-400.woff2, etc.)
     */
    public function fonts(Request $request, string $name): Response|BinaryFileResponse
    {
        if (str_contains($name, '..') || str_contains($name, '/') || str_contains($name, '\\')) {
            abort(404);
        }
        $path = public_path('assets/global/fonts/' . $name);
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }
        $mime = $this->fontMime($name);
        return response()->file($path, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Tailwind utilities-only bundle (public/css/tailwind-utilities.css) — subdirectory-safe MIME.
     */
    public function tailwindUtilities(Request $request): Response|BinaryFileResponse
    {
        $path = public_path('css/tailwind-utilities.css');
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Full storefront CSS: PostCSS/Tailwind build (legacy template CSS + @tailwind) — one request, correct MIME.
     */
    public function tailwindStorefront(Request $request): Response|BinaryFileResponse
    {
        $path = public_path('css/tailwind-storefront.css');
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Admin panel Tailwind + Inter base (separate bundle from storefront).
     */
    public function tailwindAdmin(Request $request): Response|BinaryFileResponse
    {
        $path = public_path('css/tailwind-admin.css');
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    private function fontMime(string $name): string
    {
        return match (strtolower(pathinfo($name, PATHINFO_EXTENSION))) {
            'woff2' => 'font/woff2',
            'woff' => 'font/woff',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
            'eot' => 'application/vnd.ms-fontobject',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };
    }
}
