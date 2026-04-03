<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ForceAdminPasswordChange
{
    /**
     * Redirect admin to change password if force_password_change is set.
     */
    public function handle($request, Closure $next, $guard = 'admin')
    {
        $user = Auth::guard($guard)->user();
        if (!$user) {
            return $next($request);
        }

        $allowed = ['admin.password', 'admin.password.update', 'admin.profile.update', 'admin.logout', 'admin.2fa.disable', 'admin.2fa.disable.confirm', 'admin.reauth.form', 'admin.reauth.verify'];
        if ($user->needsPasswordChange() && !$request->routeIs($allowed)) {
            return redirect()->route('admin.password')
                ->with('warning', __('You must change your password before continuing.'));
        }

        return $next($request);
    }
}
