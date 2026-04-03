<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures dynamic pages (admin, user, cart, checkout, payment) are never
 * stored by the browser. Use with CacheHeaders for path-based no-cache.
 */
class NoCacheMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // For normal responses and streamed responses, set headers via the
        // Symfony headers bag instead of Laravel's Response::header(),
        // which is not available on StreamedResponse.
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');

        return $response;
    }
}
