<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use App\Services\TOTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ReAuthController extends Controller
{
    public function form(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $next = $request->query('next', route('admin.dashboard'));
        $action = $request->query('action', 'high_risk_action');
        return view('admin.auth.reauth', compact('next', 'action'));
    }

    public function verify(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, $admin->password)) {
            SecurityEvent::log('reauth_failed', 'medium', ['admin_id' => $admin->id]);
            $notify[] = ['error', __('Invalid password.')];
            return back()->withNotify($notify);
        }

        $code = trim((string) $request->input('code'));
        if ($admin->hasTwoFactorEnabled() && config('admin.admin_two_factor_enabled', true)) {
            if ($code === '') {
                $notify[] = ['error', __('2FA code is required.')];
                return back()->withNotify($notify);
            }
            try {
                $secret = decrypt($admin->two_factor_secret);
            } catch (\Throwable $e) {
                $secret = null;
            }
            if (!$secret || !TOTPService::adminVerify($secret, $code)) {
                SecurityEvent::log('reauth_2fa_failed', 'medium', ['admin_id' => $admin->id]);
                $notify[] = ['error', __('Invalid 2FA code.')];
                return back()->withNotify($notify);
            }
        }

        $request->session()->put('admin_last_reauth_at', time());
        SecurityEvent::log('high_risk_action_verified', 'low', ['admin_id' => $admin->id]);

        $next = $request->input('next', route('admin.dashboard'));
        return redirect($next);
    }
}
