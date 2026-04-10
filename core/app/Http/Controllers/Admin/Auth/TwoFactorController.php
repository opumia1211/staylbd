<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\SecurityEvent;
use App\Models\TrustedAdminDevice;
use App\Services\TOTPService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    public function __construct()
    {
        // verify/setup: no auth (admin_2fa_pending_id in session)
        // disable: requires admin auth
    }

    /**
     * When 2FA is globally disabled, complete login from pending session (bookmark / mid-flow toggle).
     */
    protected function completeLoginBypassingTwoFactor(Request $request, Admin $admin): RedirectResponse
    {
        $remember = $request->session()->get('admin_2fa_remember', false);
        $request->session()->forget(['admin_2fa_pending_id', 'admin_2fa_remember', 'admin_2fa_setup_secret', 'admin_2fa_setup_admin_id']);
        Cache::forget('admin_2fa_attempts_' . $admin->id);
        Cache::forget('admin_2fa_setup_attempts_' . $admin->id);
        TrustedAdminDevice::markTrusted(
            $admin->id,
            $request->userAgent() ?? '',
            $request->ip(),
            $request->input('device_fingerprint')
        );
        Auth::guard('admin')->login($admin, $remember);
        $request->session()->put('admin_just_logged_in', true);
        $request->session()->put('admin_login_at', time());

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * 2FA verify form (after password login, before full auth).
     */
    public function verify(Request $request)
    {
        if (!config('admin.admin_two_factor_enabled', true)) {
            $adminId = $request->session()->get('admin_2fa_pending_id');
            $admin = $adminId ? Admin::find($adminId) : null;
            if ($admin) {
                return $this->completeLoginBypassingTwoFactor($request, $admin);
            }
            return redirect()->route('admin.login');
        }

        $adminId = $request->session()->get('admin_2fa_pending_id');
        if (!$adminId) {
            return redirect()->route('admin.login');
        }
        $admin = Admin::find($adminId);
        if (!$admin || !$admin->hasTwoFactorEnabled()) {
            $request->session()->forget('admin_2fa_pending_id');
            return redirect()->route('admin.login');
        }

        $pageTitle = __('Two-Factor Authentication');
        return view('admin.auth.two_factor_verify', compact('pageTitle'));
    }

    /**
     * 2FA verify submission.
     */
    public function confirmVerify(Request $request)
    {
        if (!config('admin.admin_two_factor_enabled', true)) {
            $adminId = $request->session()->get('admin_2fa_pending_id');
            $admin = $adminId ? Admin::find($adminId) : null;
            if ($admin) {
                return $this->completeLoginBypassingTwoFactor($request, $admin);
            }
            return redirect()->route('admin.login');
        }

        $adminId = $request->session()->get('admin_2fa_pending_id');
        if (!$adminId) {
            return redirect()->route('admin.login');
        }
        $admin = Admin::find($adminId);
        if (!$admin || !$admin->hasTwoFactorEnabled()) {
            $request->session()->forget('admin_2fa_pending_id');
            return redirect()->route('admin.login');
        }

        $key = 'admin_2fa_attempts_' . $adminId;
        $attempts = (int) Cache::get($key, 0);
        if ($attempts >= config('admin.admin_2fa_attempt_limit', 5)) {
            SecurityEvent::log('admin_2fa_rate_limit', 'high', ['admin_id' => $admin->id]);
            $notify[] = ['error', __('Too many attempts. Try again later.')];
            return back()->withNotify($notify);
        }

        $code = trim((string) $request->input('code'));
        $recovery = trim((string) $request->input('recovery_code'));

        $valid = false;
        try {
            $secret = decrypt($admin->two_factor_secret);
        } catch (\Throwable $e) {
            $secret = null;
        }

        if ($code !== '' && $secret) {
            $valid = TOTPService::adminVerify($secret, $code);
        } elseif ($recovery !== '') {
            $result = TOTPService::verifyRecoveryCode($admin->two_factor_recovery_codes ?? '[]', $recovery);
            if ($result[0]) {
                $valid = true;
                $admin->two_factor_recovery_codes = $result[1];
                $admin->save();
            }
        }

        if (!$valid) {
            Cache::put($key, $attempts + 1, now()->addSeconds(config('admin.admin_2fa_attempt_decay', 60)));
            SecurityEvent::log('admin_2fa_failed', 'medium', ['admin_id' => $admin->id]);
            $notify[] = ['error', __('Invalid code. Try again.')];
            return back()->withNotify($notify);
        }

        Cache::forget($key);
        $remember = $request->session()->get('admin_2fa_remember', false);
        $request->session()->forget(['admin_2fa_pending_id', 'admin_2fa_remember']);
        TrustedAdminDevice::markTrusted(
            $admin->id,
            $request->userAgent() ?? '',
            $request->ip(),
            $request->input('device_fingerprint')
        );
        Auth::guard('admin')->login($admin, $remember);
        $request->session()->put('admin_just_logged_in', true);
        $request->session()->put('admin_login_at', time());

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * 2FA setup (Owner/SuperAdmin who must enable).
     */
    public function setup(Request $request)
    {
        if (!config('admin.admin_two_factor_enabled', true)) {
            $adminId = $request->session()->get('admin_2fa_pending_id');
            $admin = $adminId ? Admin::find($adminId) : null;
            if ($admin) {
                return $this->completeLoginBypassingTwoFactor($request, $admin);
            }
            return redirect()->route('admin.login');
        }

        $adminId = $request->session()->get('admin_2fa_pending_id');
        if (!$adminId) {
            return redirect()->route('admin.login');
        }
        $admin = Admin::find($adminId);
        if (!$admin) {
            $request->session()->forget('admin_2fa_pending_id');
            return redirect()->route('admin.login');
        }
        if ($admin->hasTwoFactorEnabled()) {
            $request->session()->forget('admin_2fa_pending_id');
            return redirect()->route('admin.login');
        }

        $adminId = (int) $adminId;

        // New secret on every page load (reload / open tab) so QR always matches current session.
        // Reset attempt budget for this QR (POST remains throttle-limited).
        Cache::forget('admin_2fa_setup_attempts_' . $adminId);
        $secret = TOTPService::generateSecret();
        $request->session()->put('admin_2fa_setup_secret', encrypt($secret));
        $request->session()->put('admin_2fa_setup_admin_id', $adminId);

        $issuer = (string) config('app.name', 'Admin');
        $accountLabel = $issuer . ':' . (string) ($admin->username ?? 'admin');
        $otpauthUrl = 'otpauth://totp/' . rawurlencode($accountLabel)
            . '?secret=' . rawurlencode($secret)
            . '&issuer=' . rawurlencode($issuer)
            . '&algorithm=SHA1&digits=6&period=30';

        $pageTitle = __('Enable Two-Factor Authentication');
        return view('admin.auth.two_factor_setup', compact('pageTitle', 'secret', 'otpauthUrl'));
    }

    /**
     * 2FA setup confirmation.
     */
    public function confirmSetup(Request $request)
    {
        if (!config('admin.admin_two_factor_enabled', true)) {
            $adminId = $request->session()->get('admin_2fa_pending_id')
                ?? $request->session()->get('admin_2fa_setup_admin_id');
            $admin = $adminId ? Admin::find($adminId) : null;
            if ($admin) {
                return $this->completeLoginBypassingTwoFactor($request, $admin);
            }
            return redirect()->route('admin.login');
        }

        $adminId = $request->session()->get('admin_2fa_setup_admin_id');
        $encSecret = $request->session()->get('admin_2fa_setup_secret');
        if (!$adminId || !$encSecret) {
            return redirect()->route('admin.login');
        }

        $admin = Admin::find($adminId);
        if (!$admin) {
            $request->session()->forget(['admin_2fa_setup_admin_id', 'admin_2fa_setup_secret', 'admin_2fa_pending_id']);
            return redirect()->route('admin.login');
        }

        $attemptKey = 'admin_2fa_setup_attempts_' . $adminId;
        $attempts = (int) Cache::get($attemptKey, 0);
        if ($attempts >= config('admin.admin_2fa_attempt_limit', 5)) {
            SecurityEvent::log('admin_2fa_setup_rate_limit', 'high', ['admin_id' => $adminId]);
            $request->session()->forget(['admin_2fa_setup_secret', 'admin_2fa_setup_admin_id']);
            $notify[] = ['error', __('Too many incorrect attempts. Scan the new QR code on the next page.')];
            return redirect()->route('admin.2fa.setup')->withNotify($notify);
        }

        $code = trim((string) $request->input('code'));
        if ($code === '') {
            $notify[] = ['error', __('Please enter the verification code.')];
            return back()->withNotify($notify);
        }

        try {
            $secret = decrypt($encSecret);
        } catch (\Throwable $e) {
            $request->session()->forget(['admin_2fa_setup_admin_id', 'admin_2fa_setup_secret']);
            return redirect()->route('admin.login');
        }

        if (!TOTPService::adminVerify($secret, $code)) {
            Cache::put($attemptKey, $attempts + 1, now()->addSeconds(config('admin.admin_2fa_attempt_decay', 60)));
            SecurityEvent::log('admin_2fa_setup_failed', 'medium', ['admin_id' => $adminId]);
            $notify[] = ['error', __('Invalid code. Please try again.')];
            return back()
                ->withNotify($notify)
                ->with('admin_2fa_setup_clear_code', true);
        }

        Cache::forget($attemptKey);

        $recoveryCodes = TOTPService::generateRecoveryCodes(8);
        $admin->two_factor_secret = encrypt($secret);
        $admin->two_factor_recovery_codes = json_encode($recoveryCodes);
        $admin->two_factor_confirmed_at = now();
        $admin->save();

        SecurityEvent::log('admin_2fa_enabled', 'low', ['admin_id' => $admin->id]);

        $remember = $request->session()->get('admin_2fa_remember', false);
        $request->session()->forget(['admin_2fa_setup_admin_id', 'admin_2fa_setup_secret', 'admin_2fa_pending_id', 'admin_2fa_remember']);
        TrustedAdminDevice::markTrusted(
            $admin->id,
            $request->userAgent() ?? '',
            $request->ip(),
            $request->input('device_fingerprint')
        );
        Auth::guard('admin')->login($admin, $remember);
        $request->session()->put('admin_just_logged_in', true);
        $request->session()->put('admin_login_at', time());

        return redirect()->route('admin.2fa.recovery-codes')->with('2fa_recovery_codes', $recoveryCodes);
    }

    /**
     * Show recovery codes once after 2FA enable (save/print advice).
     */
    public function showRecoveryCodes(Request $request)
    {
        if (!$request->session()->has('2fa_recovery_codes')) {
            return redirect()->route('admin.dashboard');
        }
        $codes = $request->session()->get('2fa_recovery_codes');
        $request->session()->forget('2fa_recovery_codes');
        $pageTitle = __('Save Your Recovery Codes');
        return view('admin.auth.two_factor_recovery_codes', compact('codes', 'pageTitle'));
    }

    /**
     * 2FA disable (requires password confirmation).
     */
    public function disable(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        if ($admin->mustHaveTwoFactor()) {
            $notify[] = ['error', __('2FA is mandatory for your role.')];
            return back()->withNotify($notify);
        }
        $pageTitle = __('Disable Two-Factor Authentication');
        return view('admin.auth.two_factor_disable', compact('pageTitle'));
    }

    /**
     * 2FA disable confirmation.
     */
    public function confirmDisable(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        if ($admin->mustHaveTwoFactor()) {
            $notify[] = ['error', __('2FA is mandatory for your role.')];
            return back()->withNotify($notify);
        }

        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, $admin->password)) {
            SecurityEvent::log('admin_2fa_disable_failed', 'medium', ['admin_id' => $admin->id]);
            $notify[] = ['error', __('Invalid password.')];
            return back()->withNotify($notify);
        }

        $admin->two_factor_secret = null;
        $admin->two_factor_recovery_codes = null;
        $admin->two_factor_confirmed_at = null;
        $admin->save();

        SecurityEvent::log('admin_2fa_disabled', 'low', ['admin_id' => $admin->id]);
        $notify[] = ['success', __('Two-factor authentication has been disabled.')];
        return redirect()->route('admin.profile')->withNotify($notify);
    }
}
