<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Check if admin has the given permission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission  e.g. products.view
     * @return mixed
     */
    public function handle($request, Closure $next, string $permission, $guard = 'admin')
    {
        $user = Auth::guard($guard)->user();
        if (!$user || !\App\Models\Permission::has($user, $permission)) {
            \App\Models\SecurityEvent::log('permission_denied', 'medium', [
                'admin_id' => $user->id ?? null,
                'payload'  => ['permission' => $permission],
            ]);
            abort(403, __('You do not have permission to perform this action.'));
        }
        return $next($request);
    }
}
