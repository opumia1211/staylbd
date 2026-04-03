<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Enforce production security settings (APP_DEBUG=false, SESSION_SECURE_COOKIE, HTTPS).
 * Only active when APP_ENV=production.
 */
class EnvironmentIntegrityCheck
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.env') !== 'production') {
            return $next($request);
        }

        $strict = filter_var(env('ENV_STRICT_MODE', false), FILTER_VALIDATE_BOOLEAN)
            || config('admin.zero_trust_mode', false);
        if (!$strict) {
            return $next($request);
        }

        $issues = [];
        if (config('app.debug')) {
            $issues[] = 'APP_DEBUG must be false in production';
        }
        if (!config('session.secure')) {
            $issues[] = 'SESSION_SECURE_COOKIE must be true in production';
        }
        if (!$request->secure()) {
            $issues[] = 'HTTPS required in production';
        }

        if (!empty($issues)) {
            \Illuminate\Support\Facades\Log::channel('security')->critical('Environment integrity check failed', [
                'issues' => $issues,
                'ip' => $request->ip(),
            ]);
            abort(500, 'Server configuration error. Contact administrator.');
        }

        return $next($request);
    }
}
