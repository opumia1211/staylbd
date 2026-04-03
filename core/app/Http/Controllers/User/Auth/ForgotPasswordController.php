<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /** Max failed attempts before lockout */
    const LOCKOUT_AFTER_ATTEMPTS = 5;

    /** Lockout duration in minutes */
    const LOCKOUT_MINUTES = 5;

    /** Cache key prefix for attempts (per IP) */
    const CACHE_ATTEMPTS = 'pass_reset_attempts_';

    /** Cache key for lockout until timestamp (per IP) */
    const CACHE_LOCKOUT = 'pass_reset_lockout_';

    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        $pageTitle = __('Account Recovery');
        $ip = request()->ip();
        $lockoutUntil = Cache::get(self::CACHE_LOCKOUT . $ip);
        $throttleUntil = (int) session('password_reset_throttle_until', 0);
        $unlockAt = max((int) $lockoutUntil, $throttleUntil);

        if ($unlockAt > time()) {
            return view($this->activeTemplate . 'user.auth.passwords.timeout_modal', [
                'pageTitle' => $pageTitle,
                'unlockAt' => $unlockAt,
            ]);
        }

        if ($lockoutUntil) {
            Cache::forget(self::CACHE_LOCKOUT . $ip);
            Cache::forget(self::CACHE_ATTEMPTS . $ip);
        }
        session()->forget('password_reset_throttle_until');

        return view($this->activeTemplate . 'user.auth.passwords.email_modal', compact('pageTitle'));
    }

    public function sendResetCodeEmail(Request $request)
    {
        $ip = $request->ip();
        $lockoutUntil = Cache::get(self::CACHE_LOCKOUT . $ip);
        if ($lockoutUntil && (int) $lockoutUntil > time()) {
            $notify[] = ['error', __('Too many attempts. Please wait before trying again.')];
            return redirect()->route('user.password.request')->withNotify($notify);
        }

        $request->validate([
            'email_or_username' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', __('Invalid captcha provided.')];
            return back()->withNotify($notify);
        }

        $user = $this->findUserByEmailOrUsername($request->email_or_username);
        if (!$user) {
            $this->recordFailedAttempt($ip);
            if (Cache::get(self::CACHE_LOCKOUT . $ip)) {
                $notify[] = ['error', __('Too many attempts. Try again after the countdown.')];
                return redirect()->route('user.password.request')->withNotify($notify);
            }
            $notify[] = ['error', __('No account found. Email/username and phone must match the same registered account.')];
            return back()->withInput($request->only('email_or_username', 'phone'))->withNotify($notify);
        }

        $phoneNormalized = preg_replace('/\D/', '', $request->phone);
        if (strlen($phoneNormalized) >= 10 && $phoneNormalized[0] === '0') {
            $phoneNormalized = '880' . substr($phoneNormalized, 1);
        }
        if ($user->mobile !== $phoneNormalized) {
            $this->recordFailedAttempt($ip);
            if (Cache::get(self::CACHE_LOCKOUT . $ip)) {
                $notify[] = ['error', __('Too many attempts. Try again after the countdown.')];
                return redirect()->route('user.password.request')->withNotify($notify);
            }
            $notify[] = ['error', __('No account found. Email/username and phone must match the same registered account.')];
            return back()->withInput($request->only('email_or_username', 'phone'))->withNotify($notify);
        }

        Cache::forget(self::CACHE_ATTEMPTS . $ip);

        PasswordReset::where('email', $user->email)->delete();
        $code = verificationCode(6);
        $password = new PasswordReset();
        $password->email = $user->email;
        $password->token = $code;
        $password->created_at = \Carbon\Carbon::now();
        $password->save();

        $userIpInfo = getIpInfo();
        $userBrowserInfo = osBrowser();
        notify($user, 'PASS_RESET_CODE', [
            'code' => $code,
            'operating_system' => @$userBrowserInfo['os_platform'],
            'browser' => @$userBrowserInfo['browser'],
            'ip' => @$userIpInfo['ip'],
            'time' => @$userIpInfo['time']
        ],['email']);

        $email = $user->email;
        session()->put('pass_res_mail', $email);
        $notify[] = ['success', 'Password reset email sent successfully'];
        return redirect()->route('user.password.code.verify')->withNotify($notify);
    }

    /**
     * Record a failed attempt; lock out if over limit.
     */
    protected function recordFailedAttempt(string $ip): void
    {
        $key = self::CACHE_ATTEMPTS . $ip;
        $attempts = (int) Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addMinutes(15));

        if ($attempts >= self::LOCKOUT_AFTER_ATTEMPTS) {
            $unlockAt = time() + (self::LOCKOUT_MINUTES * 60);
            Cache::put(self::CACHE_LOCKOUT . $ip, $unlockAt, now()->addMinutes(self::LOCKOUT_MINUTES + 1));
        }
    }

    /**
     * Find user by email or username. Returns null if not found.
     */
    protected function findUserByEmailOrUsername(string $input): ?User
    {
        $input = trim($input);
        if ($input === '') {
            return null;
        }
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return User::where('email', strtolower($input))->first();
        }
        return User::where('username', strtolower($input))->first();
    }

    public function codeVerify()
    {
        $email = session()->get('pass_res_mail');
        if (!$email) {
            $notify[] = ['error', 'Oops! session expired'];
            return redirect()->route('user.password.request')->withNotify($notify);
        }
        $pageTitle = __('Verify Email Address');
        return view($this->activeTemplate . 'user.auth.passwords.code_modal', compact('email', 'pageTitle'));
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'email' => 'required'
        ]);
        $code =  str_replace(' ', '', $request->code);

        if (PasswordReset::where('token', $code)->where('email', $request->email)->count() != 1) {
            $notify[] = ['error', 'Verification code doesn\'t match'];
            return redirect()->route('user.password.request')->withNotify($notify);
        }
        $notify[] = ['success', 'You can change your password.'];
        session()->flash('fpass_email', $request->email);
        return redirect()->route('user.password.reset', $code)->withNotify($notify);
    }

}
