<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\SecurityEvent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * For high-risk actions: require re-auth if last login > 15 min.
 * High-risk: payment override, refund, role change, 2FA disable, IP whitelist modify.
 */
class RequireReAuthentication
{
    private function maxMinutes(): int
    {
        return (int) config('admin.reauth_required_minutes', 15);
    }

    public function handle(Request $request, Closure $next, string $actionName = 'high_risk_action')
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        if (!$this->isReAuthRequired($request, $admin)) {
            return $next($request);
        }

        if ($request->routeIs('admin.reauth.*')) {
            return $next($request);
        }

        return redirect()->route('admin.reauth.form', ['next' => $request->fullUrl(), 'action' => $actionName]);
    }

    private function isReAuthRequired(Request $request, Admin $admin): bool
    {
        $lastReauth = $request->session()->get('admin_last_reauth_at');
        if ($lastReauth && is_numeric($lastReauth)) {
            $mins = (time() - (int) $lastReauth) / 60;
            if ($mins < $this->maxMinutes()) {
                return false;
            }
        }

        $lastLogin = $request->session()->get('admin_just_logged_in') ? time() : $request->session()->get('admin_login_at');
        if ($lastLogin && is_numeric($lastLogin)) {
            $mins = (time() - (int) $lastLogin) / 60;
            if ($mins < $this->maxMinutes()) {
                return false;
            }
        }

        return true;
    }
}
