<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureSuperAdmin
{
    /**
     * Ensure the authenticated admin has Owner role (project owner only).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'admin')
    {
        $user = Auth::guard($guard)->user();
        if (!$user || !$user->isOwner()) {
            \Illuminate\Support\Facades\Log::channel('security')->warning('Owner-only route access denied', [
                'admin_id'   => $user->id ?? null,
                'ip'         => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 200),
                'route'      => $request->route()?->getName(),
            ]);
            abort(403, __('Unauthorized. Only the project Owner can access this.'));
        }
        return $next($request);
    }
}
