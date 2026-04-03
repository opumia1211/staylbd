<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Add security-related HTTP headers for production.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // microphone=()/camera=() blocks APIs entirely — breaks voice search & camera upload. Same-origin allow:
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(self), camera=(self)');

        if (config('app.env') === 'production' && $request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Content-Security-Policy: allow Google Fonts, tawk.to chat, inline scripts
        $cspReportOnly = config('security_headers.csp_report_only', true);
        $strictCsp = config('security_headers.csp_strict', false);
        if ($strictCsp) {
            $csp = "default-src 'self'; script-src 'self' https://embed.tawk.to; style-src 'self' https://fonts.googleapis.com https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: https:; connect-src 'self' https: https://embed.tawk.to; frame-ancestors 'self'; base-uri 'self'; form-action 'self'";
        } else {
            $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://embed.tawk.to; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:; img-src 'self' data: https:; connect-src 'self' https: https://embed.tawk.to; frame-ancestors 'self'; base-uri 'self'; form-action 'self'";
        }
        $response->headers->set($cspReportOnly ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy', $csp);

        return $response;
    }
}
