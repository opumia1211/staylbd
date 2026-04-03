<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsMiddleware
{
    /**
     * When Force SSL is enabled (general_settings.force_ssl), redirect HTTP to HTTPS.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->secure()) {
            return $next($request);
        }

        try {
            $general = gs();
            if (isset($general->force_ssl) && $general->force_ssl) {
                return redirect()->secure($request->getRequestUri(), 302);
            }
        } catch (\Throwable $e) {
            // If gs() fails (e.g. DB not ready), continue without redirect
        }

        return $next($request);
    }
}
