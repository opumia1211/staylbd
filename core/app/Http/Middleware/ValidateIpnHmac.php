<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Optional defense-in-depth for /ipn/* when IPN_HMAC_SECRET is set.
 * POST/PUT/PATCH bodies must include X-Ipn-Signature (hex HMAC-SHA256 of raw body).
 */
class ValidateIpnHmac
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('ipn.hmac_secret', '');
        if ($secret === '') {
            return $next($request);
        }

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)) {
            return $next($request);
        }

        $payload = $request->getContent();
        $expected = hash_hmac('sha256', $payload, $secret);
        $provided = strtolower(trim((string) $request->header('X-Ipn-Signature', '')));

        if ($provided === '' || ! hash_equals($expected, $provided)) {
            Log::channel('security')->warning('IPN HMAC validation failed', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'method' => $request->method(),
            ]);

            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        }

        return $next($request);
    }
}
