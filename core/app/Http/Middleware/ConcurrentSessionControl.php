<?php

namespace App\Http\Middleware;

use App\Models\AdminSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Limit admin concurrent sessions and optionally invalidate old on new login.
 */
class ConcurrentSessionControl
{
    public function handle(Request $request, Closure $next)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return $next($request);
        }

        $sessionId = $request->session()->getId();
        $maxSessions = config('admin.admin_max_sessions', 5);
        $invalidateOnLogin = config('admin.admin_invalidate_old_session_on_login', true);

        AdminSession::updateOrCreate(
            ['admin_id' => $admin->id, 'session_id' => $sessionId],
            [
                'ip_address'       => $request->ip(),
                'user_agent'       => substr($request->userAgent() ?? '', 0, 500),
                'last_activity_at' => now(),
            ]
        );

        $sessions = AdminSession::where('admin_id', $admin->id)
            ->orderBy('last_activity_at', 'desc')
            ->get();

        if ($invalidateOnLogin && $request->session()->pull('admin_just_logged_in')) {
            foreach ($sessions->where('session_id', '!=', $sessionId) as $row) {
                try {
                    Session::getHandler()->destroy($row->session_id);
                } catch (\Throwable $e) {
                }
                $row->delete();
            }
            return $next($request);
        }

        $count = $sessions->count();
        if ($count > $maxSessions) {
            $toRemove = $sessions->skip($maxSessions);
            foreach ($toRemove as $row) {
                if ($row->session_id !== $sessionId) {
                    try {
                        Session::getHandler()->destroy($row->session_id);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                    $row->delete();
                }
            }
        }

        return $next($request);
    }
}
