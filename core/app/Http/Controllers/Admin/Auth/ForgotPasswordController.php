<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\Admin;
use App\Models\AdminPasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /** Failed attempts (wrong email / captcha) before lockout */
    private const LOCKOUT_AFTER_ATTEMPTS = 5;

    /** Lockout length after too many failures */
    private const LOCKOUT_MINUTES = 15;

    /** Rolling window for counting failures */
    private const ATTEMPT_WINDOW_MINUTES = 15;

    private const CACHE_ATTEMPTS = 'admin_pass_reset_attempts_';

    private const CACHE_LOCKOUT = 'admin_pass_reset_lockout_';

    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('admin.guest');
    }

    /**
     * Display the form to request a password reset link.
     * Uses same layout and captcha as admin login for consistency.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        $pageTitle = 'Account Recovery';
        $ip = request()->ip();
        $lockoutUntil = (int) Cache::get(self::CACHE_LOCKOUT . $ip, 0);
        $throttleUntil = (int) session('password_reset_throttle_until', 0);
        $passResetLockoutUntil = max($lockoutUntil, $throttleUntil);

        if ($passResetLockoutUntil <= time()) {
            session()->forget('password_reset_throttle_until');
            if ($lockoutUntil > 0 && $lockoutUntil <= time()) {
                Cache::forget(self::CACHE_LOCKOUT . $ip);
                Cache::forget(self::CACHE_ATTEMPTS . $ip);
            }
            $passResetLockoutUntil = 0;
        }

        $useCaptcha = config('admin.admin_login_captcha', false);
        $useImageCaptcha = $useCaptcha && extension_loaded('gd');
        $captchaCode = null;
        if ($useCaptcha && !($passResetLockoutUntil > time())) {
            $captchaCode = $this->generateCaptchaCode();
            request()->session()->put('admin_login_captcha', $captchaCode);
            if ($useImageCaptcha) {
                $captchaCode = null;
            }
        }
        return view('admin.auth.passwords.email', compact('pageTitle', 'captchaCode', 'useImageCaptcha', 'passResetLockoutUntil'));
    }

    private function generateCaptchaCode(): string
    {
        $pool = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $len = strlen($pool) - 1;
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= $pool[random_int(0, $len)];
        }
        return $code;
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('admins');
    }

    public function sendResetCodeEmail(Request $request)
    {
        $ip = $request->ip();
        $lockoutUntil = (int) Cache::get(self::CACHE_LOCKOUT . $ip, 0);
        if ($lockoutUntil > time()) {
            $notify[] = ['error', __('Too many failed attempts. Please wait before trying again.')];

            return to_route('admin.password.reset')->withNotify($notify);
        }

        $this->validate($request, [
            'email' => 'required|email',
        ]);

        if (config('admin.admin_login_captcha', false)) {
            $sessionCode = (string) $request->session()->get('admin_login_captcha', '');
            $userCode = trim((string) $request->input('admin_login_captcha', ''));
            $sessionNormalized = mb_strtolower(preg_replace('/\s+/', '', $sessionCode), 'UTF-8');
            $userNormalized = mb_strtolower(preg_replace('/\s+/', '', $userCode), 'UTF-8');
            if ($sessionCode === '' || $userNormalized === '' || $userNormalized !== $sessionNormalized) {
                $request->session()->forget('admin_login_captcha');
                $this->recordFailedAttempt($ip);
                if (Cache::get(self::CACHE_LOCKOUT . $ip)) {
                    $notify[] = ['error', __('Too many failed attempts. Please wait :minutes minutes.', ['minutes' => (string) self::LOCKOUT_MINUTES])];

                    return to_route('admin.password.reset')->withNotify($notify);
                }
                $notify[] = ['error', __('Invalid captcha. Enter the code as shown.')];

                return back()->withNotify($notify)->withInput($request->only('email'));
            }
            $request->session()->forget('admin_login_captcha');
        }

        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            $this->recordFailedAttempt($ip);
            if (Cache::get(self::CACHE_LOCKOUT . $ip)) {
                $notify[] = ['error', __('Too many failed attempts. Please wait :minutes minutes.', ['minutes' => (string) self::LOCKOUT_MINUTES])];

                return to_route('admin.password.reset')->withNotify($notify);
            }

            return back()->withErrors(['Email Not Available']);
        }

        Cache::forget(self::CACHE_ATTEMPTS . $ip);
        Cache::forget(self::CACHE_LOCKOUT . $ip);

        $code = verificationCode(6);
        $adminPasswordReset = new AdminPasswordReset();
        $adminPasswordReset->email = $admin->email;
        $adminPasswordReset->token = $code;
        $adminPasswordReset->created_at = date("Y-m-d h:i:s");
        $adminPasswordReset->save();

        $adminIpInfo = getIpInfo();
        $adminBrowser = osBrowser();
        notify($admin, 'PASS_RESET_CODE', [
            'code' => $code,
            'operating_system' => $adminBrowser['os_platform'],
            'browser' => $adminBrowser['browser'],
            'ip' => $adminIpInfo['ip'],
            'time' => $adminIpInfo['time']
        ],['email'],false);

        $email = $admin->email;
        session()->put('pass_res_mail',$email);

        return to_route('admin.password.code.verify');
    }

    public function codeVerify(){
        $pageTitle = 'Verify Code';
        $email = session()->get('pass_res_mail');
        if (!$email) {
            $notify[] = ['error','Oops! session expired'];
            return to_route('admin.password.reset')->withNotify($notify);
        }
        return view('admin.auth.passwords.code_verify', compact('pageTitle','email'));
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required']);
        $notify[] = ['success', 'You can change your password.'];
        $code = str_replace(' ', '', $request->code);
        return to_route('admin.password.reset.form', $code)->withNotify($notify);
    }

    /**
     * Count failed admin password-reset tries (wrong email or captcha); lock IP when over limit.
     */
    protected function recordFailedAttempt(string $ip): void
    {
        $key = self::CACHE_ATTEMPTS . $ip;
        $attempts = (int) Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addMinutes(self::ATTEMPT_WINDOW_MINUTES));

        if ($attempts >= self::LOCKOUT_AFTER_ATTEMPTS) {
            $unlockAt = time() + (self::LOCKOUT_MINUTES * 60);
            Cache::put(self::CACHE_LOCKOUT . $ip, $unlockAt, now()->addMinutes(self::LOCKOUT_MINUTES + 1));
        }
    }
}
