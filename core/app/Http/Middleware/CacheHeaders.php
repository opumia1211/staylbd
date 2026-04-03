<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheHeaders
{
    /** Static assets: long cache when versioned (bust via ?v=). */
    private const STATIC_MAX_AGE = 31536000; // 1 year for versioned assets

    /**
     * Paths that must NEVER be cached (admin, user panel, auth, payment, cart, ticket, etc.).
     */
    private static function noCachePatterns(): array
    {
        $prefix = config('admin.prefix', 'admin');
        return [
            $prefix,
            $prefix . '/*',
            'user',
            'user/*',
            'checkout',
            'checkout/*',
            'payment',
            'payment/*',
            'api/*',
            'cart-list',
            'cart-list/*',
            'ticket',
            'ticket/*',
            'wish-list',
            'wish-list/*',
            'compare',
            'compare/*',
        ];
    }

    /**
     * Static assets: CSS, JS, images, fonts. Only these get short browser cache (max 60s).
     */
    private static function isStaticAsset(Request $request): bool
    {
        $path = $request->path();
        $extensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'map'];
        foreach ($extensions as $ext) {
            if (str_ends_with(strtolower($path), '.' . $ext)) {
                return true;
            }
        }
        return $request->is('assets/*') || $request->is('storage/*') || $request->is('build/*');
    }

    /**
     * Professional e-commerce: minimal cache.
     * - HTML pages: no cache (fresh products, prices, stock). User always sees latest.
     * - Static assets: max 60 seconds so updates (e.g. fly-to-header.js) load quickly.
     * - Everything else: no cache.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && self::isStaticAsset($request)) {
            return $this->addCacheHeaders($response, self::STATIC_MAX_AGE, false, true);
        }

        foreach (self::noCachePatterns() as $pattern) {
            if ($request->is($pattern)) {
                return $this->addNoCacheHeaders($response);
            }
        }

        $sessionCookie = config('session.cookie');
        if ($request->isMethod('GET') && $request->acceptsHtml()) {
            if ($request->hasCookie($sessionCookie) || auth()->check() || auth()->guard('admin')->check()) {
                return $this->addNoCacheHeaders($response);
            }
            // Public HTML (no auth): short cache for instant repeat load (revalidate for fresh content)
            return $this->addCacheHeaders($response, 30, false, true);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->addNoCacheHeaders($response);
        }

        return $this->addNoCacheHeaders($response);
    }

    private function addCacheHeaders($response, int $maxAge, bool $immutable = false, bool $mustRevalidate = false): Response
    {
        $cacheControl = 'public, max-age=' . $maxAge;
        if ($immutable) {
            $cacheControl .= ', immutable';
        }
        if ($mustRevalidate) {
            $cacheControl .= ', must-revalidate';
        }
        $response->headers->set('Cache-Control', $cacheControl);
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT');
        return $response;
    }

    private function addNoCacheHeaders($response): Response
    {
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        return $response;
    }
}
