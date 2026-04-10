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
    private const ALLOWED_JS = ['fly-to-header', 'product-carousel', 'glass-header', 'storefront-lucide', 'auth'];

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
     * jQuery UI (bundled in tailwind-storefront.css) uses url("images/ui-icons_*.png").
     * Resolved against /serve-css/tailwind-storefront → /serve-css/images/…
     */
    public function cssBundleImages(Request $request, string $name): Response|BinaryFileResponse
    {
        if (str_contains($name, '..') || str_contains($name, '/') || str_contains($name, '\\')) {
            abort(404);
        }
        // Allow underscores or spaces/dashes in the icon filenames to be robust
        if (!preg_match('/^ui-icons[-_ ]?[A-Za-z0-9._\s-]+(?:\.[a-z0-9]+)?\.(png|gif|svg)$/i', urldecode($name))) {
            abort(404);
        }
        
        // Ensure spaces in URL resolve to underscores on disk
        $diskName = str_replace([' ', '%20'], '_', $name);
        
        // Try core/public first, then standard public_path
        $path = base_path('public/css/images/' . $diskName);
        if (!is_file($path)) {
            $path = public_path('css/images/' . $diskName);
        }

        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }
        $mime = match (strtolower(pathinfo($name, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'gif' => 'image/gif',
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
     * Homepage-only storefront CSS (smaller — no PDP/listing-only legacy imports).
     */
    public function tailwindHomepage(Request $request): Response|BinaryFileResponse
    {
        $path = public_path('css/tailwind-homepage.css');
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Full storefront CSS (PDP, listing, cart legacy + Tailwind).
     */
    public function tailwindProduct(Request $request): Response|BinaryFileResponse
    {
        $path = public_path('css/tailwind-product.css');
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Legacy URL: same file as tailwind-product.css (build copies for compatibility).
     */
    public function tailwindStorefront(Request $request): Response|BinaryFileResponse
    {
        return $this->tailwindProduct($request);
    }

    /**
     * Async-loaded storefront legacy CSS (see resources/css/tailwind-storefront-deferred.css).
     */
    public function tailwindStorefrontDeferred(Request $request): Response|BinaryFileResponse
    {
        return $this->serveCompiledStorefrontCss('tailwind-storefront-deferred.css');
    }

    public function tailwindStorefrontDeferredCart(Request $request): Response|BinaryFileResponse
    {
        return $this->serveCompiledStorefrontCss('tailwind-storefront-deferred-cart.css');
    }

    public function tailwindStorefrontDeferredAccount(Request $request): Response|BinaryFileResponse
    {
        return $this->serveCompiledStorefrontCss('tailwind-storefront-deferred-account.css');
    }

    public function tailwindStorefrontDeferredCompare(Request $request): Response|BinaryFileResponse
    {
        return $this->serveCompiledStorefrontCss('tailwind-storefront-deferred-compare.css');
    }

    public function tailwindStorefrontDeferredHome(Request $request): Response|BinaryFileResponse
    {
        return $this->serveCompiledStorefrontCss('tailwind-storefront-deferred-home.css');
    }

    private function serveCompiledStorefrontCss(string $filename): Response|BinaryFileResponse
    {
        $path = public_path('css/'.$filename);
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Consolidated storefront Blade <style> rules (built from resources/css/critical-storefront.css).
     * Loaded after @stack('style') so product-card and page rules keep correct cascade.
     */
    public function criticalStorefront(Request $request): Response|BinaryFileResponse
    {
        $path = public_path('css/critical-storefront.css');
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    /**
     * Serve admin-dashboard-panel.css from resources/css with correct MIME type.
     * This file is in resources/ not public/, so it needs to be served via controller.
     */
    public function adminPanel(Request $request): Response
    {
        $path = resource_path('css/admin-dashboard-panel.css');
        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }

        $css = file_get_contents($path);
        $css = $this->minifyCss($css);

        return response($css)
            ->header('Content-Type', 'text/css; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=2592000');
    }

    /**
     * Admin panel CSS bundle (Tailwind + imported admin assets).
     */
    public function tailwindAdmin(Request $request): Response
    {
        // Prefer resources source file so @import rewriting always runs consistently.
        // Fallback to public copy only if source file is unavailable.
        $path = resource_path('css/tailwind-admin.css');
        if (!is_file($path) || !is_readable($path)) {
            $path = public_path('css/tailwind-admin.css');
        }

        if (!is_file($path) || !is_readable($path)) {
            abort(404);
        }

        $contents = file_get_contents($path);
        
        // Resolve @imports recursively to combine all dashboard styles into one bundle
        // This fixes relative-path and subfolder delivery issues.
        $contents = $this->resolveImports($contents, dirname($path));

        // Minify CSS for faster delivery
        $minified = $this->minifyCss($contents);

        return response($minified)
            ->header('Content-Type', 'text/css; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=2592000');
    }

    /**
     * Recursively resolve and embed @import statements.
     */
    private function resolveImports(string $css, string $currentDir): string
    {
        return preg_replace_callback('/@import\s+[\'"](.+)[\'"];/', function ($matches) use ($currentDir) {
            $importPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $matches[1]);
            
            // Handle relative paths (e.g., ../../public/...)
            $fullPath = realpath($currentDir . DIRECTORY_SEPARATOR . $importPath);
            
            if ($fullPath && is_file($fullPath) && is_readable($fullPath)) {
                $content = file_get_contents($fullPath);
                // Rewrite URLs in the imported file to be absolute before embedding
                $content = $this->rewriteUrls($content, dirname($fullPath));
                // Recursively resolve imports in the imported file
                return $this->resolveImports($content, dirname($fullPath));
            }
            
            // If file not found, leave as is (browser will try to resolve)
            return $matches[0];
        }, $css);
    }

    /**
     * Rewrite relative URLs in CSS to absolute paths based on the file location.
     */
    private function rewriteUrls(string $css, string $dir): string
    {
        // Normalize directory separator for replacement
        $publicPath = str_replace('\\', '/', public_path());
        $dir = str_replace('\\', '/', $dir);

        // Match url(...) or url('...') or url("...")
        return preg_replace_callback('/url\(\s*[\'"]?([^\)\'"]+)[\'"]?\s*\)/i', function ($matches) use ($dir, $publicPath) {
            $path = $matches[1];
            
            // Skip absolute, data-uri, or protocol-relative URLs
            if (str_starts_with($path, '/') || str_starts_with($path, 'data:') || str_starts_with($path, 'http') || str_starts_with($path, '#')) {
                return $matches[0];
            }
            
            // Resolve path relative to the CSS file's directory
            $fullPathOnDisk = realpath(str_replace('/', DIRECTORY_SEPARATOR, $dir . '/' . $path));
            
            if ($fullPathOnDisk) {
                // Normalize for URL conversion
                $normalizedFullPath = str_replace('\\', '/', $fullPathOnDisk);
                
                // Convert disk path to public URL path
                // Using case-insensitive replacement to be robust on Windows
                $relativeToPublic = str_ireplace($publicPath, '', $normalizedFullPath);
                
                // Generate absolute URL. In subdirectory setup (e.g. /staylbd),
                // ensure assets always resolve to /core/public even when ASSET_URL is missing.
                $relativeAssetPath = ltrim($relativeToPublic, '/');
                $assetRoot = rtrim((string) config('app.asset_url'), '/');
                if ($assetRoot === '') {
                    $assetRoot = rtrim((string) config('app.url'), '/') . '/core/public';
                }
                $url = $assetRoot . '/' . $relativeAssetPath;
                return 'url("' . $url . '")';
            }
            
            return $matches[0];
        }, $css);
    }

    /**
     * Simple CSS minifier - safer version that avoids breaking layouts.
     */
    private function minifyCss(string $css): string
    {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        // Remove tabs and excessive newlines, but keep single spaces for safety
        $css = str_replace(["\t", "\r"], '', $css);
        $css = preg_replace('/\n+/', "\n", $css);
        // Trim each line
        $lines = explode("\n", $css);
        $cleanLines = array_map('trim', $lines);
        return implode('', $cleanLines);
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
