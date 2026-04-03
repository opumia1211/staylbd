<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminIpWhitelist;
use App\Models\AdminLockout;
use App\Models\SecurityEvent;
use App\Models\TrustedAdminDevice;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laramin\Utility\Onumoti;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     */
    protected function redirectTo(): string
    {
        return route('admin.dashboard');
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->put('admin_just_logged_in', true);
        $request->session()->put('admin_login_at', time());
        return parent::sendLoginResponse($request);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('admin.guest')->except('logout');
    }

    /**
     * Throttle key separate from user so admin block does not affect user and vice versa.
     */
    protected function throttleKey(Request $request)
    {
        return 'admin_login_' . $request->ip();
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Captcha code: 6 chars, letters + digits only (no 0/O, 1/I/l to avoid confusion).
     * Validation is case-insensitive for letters.
     */
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
     * Generate image-based captcha (stronger, bot-resistant).
     * Uses code from session (set when login page was loaded) so session matches on submit.
     * Falls back to text if GD not available.
     */
    public function captchaImage()
    {
        if (!config('admin.admin_login_captcha', false)) {
            abort(404);
        }
        $code = (string) request()->session()->get('admin_login_captcha', '');
        if ($code === '') {
            $code = $this->generateCaptchaCode();
            request()->session()->put('admin_login_captcha', $code);
        }

        if (extension_loaded('gd')) {
            return $this->generateCaptchaImage($code);
        }
        return response($code, 200, ['Content-Type' => 'text/plain']);
    }

    private function getCaptchaFontPath(): ?string
    {
        $paths = [
            resource_path('fonts/captcha.ttf'),
            resource_path('fonts/arial.ttf'),
            base_path('core/resources/fonts/captcha.ttf'),
            base_path('core/resources/fonts/arial.ttf'),
        ];
        if (PHP_OS_FAMILY === 'Windows') {
            $paths[] = 'C:\\Windows\\Fonts\\arial.ttf';
        }
        foreach ($paths as $path) {
            if ($path && is_readable($path)) {
                return $path;
            }
        }
        return null;
    }

    private function generateCaptchaImage(string $code)
    {
        // Matches admin login CSS (--cw x --ch)
        $width = 132;
        $height = 34;
        $img = imagecreatetruecolor($width, $height);
        if (!$img) {
            return response($code, 200, ['Content-Type' => 'text/plain']);
        }
        $bg = imagecolorallocate($img, 15, 23, 42);
        $textColor = imagecolorallocate($img, 255, 255, 255);
        $noiseColor = imagecolorallocatealpha($img, 100, 116, 139, 115);
        imagefill($img, 0, 0, $bg);

        for ($i = 0; $i < 1; $i++) {
            imageline($img, random_int(0, $width), random_int(0, $height),
                random_int(0, $width), random_int(0, $height), $noiseColor);
        }
        for ($i = 0; $i < 14; $i++) {
            imagesetpixel($img, random_int(0, $width - 1), random_int(0, $height - 1), $noiseColor);
        }

        $charCount = strlen($code);
        $useTtf = function_exists('imagettftext') && $this->getCaptchaFontPath();

        if ($useTtf) {
            $fontPath = $this->getCaptchaFontPath();
            $fontSize = 12;
            $slotWidth = max(8, (int) (($width - 14) / max(1, $charCount)));
            $x = 5;
            $y = 24;
            for ($i = 0; $i < $charCount; $i++) {
                $char = $code[$i];
                imagettftext($img, $fontSize, 0, $x, $y, $textColor, $fontPath, $char);
                $x += $slotWidth;
            }
        } else {
            $totalPadding = 16;
            $step = max(6, (int) (($width - $totalPadding) / max(1, $charCount)));
            $x = 5;
            $y = 10;
            for ($i = 0; $i < $charCount; $i++) {
                $char = $code[$i];
                imagestring($img, 5, $x, $y, $char, $textColor);
                $x += $step;
            }
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return response($png)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function showLoginForm(Request $request)
    {
        $blockInfo = $this->getBlockInfo($request->ip());
        $pageTitle = "Admin Login";
        $useCaptcha = config('admin.admin_login_captcha', false);
        $useImageCaptcha = $useCaptcha && extension_loaded('gd');
        $captchaCode = null;
        if ($useCaptcha) {
            $captchaCode = $this->generateCaptchaCode();
            request()->session()->put('admin_login_captcha', $captchaCode);
            if ($useImageCaptcha) {
                $captchaCode = null; // image will show it; keep code only in session
            }
        }
        return view('admin.auth.login', compact('pageTitle', 'captchaCode', 'useImageCaptcha', 'blockInfo'));
    }

    /**
     * Check if IP is blocked (AdminLockout: 5 fails = 15 min, 3 consecutive locks = 24h).
     */
    private function getBlockInfo(string $ip): array
    {
        [$locked, $retryAt, $retryMinutes] = AdminLockout::isLocked($ip);
        return [
            'blocked' => $locked,
            'retry_at' => $retryAt,
            'retry_minutes' => $retryMinutes,
        ];
    }

    /**
     * Record failed attempt. 5 fails = 15 min lock, 3 consecutive locks = 24h.
     */
    private function recordFailedAttempt(string $ip, ?string $identifier = null): ?int
    {
        $retryAt = AdminLockout::recordFailure($ip, $identifier);
        if ($retryAt) {
            SecurityEvent::log('admin_login_lockout', 'high', [
                'admin_id' => null,
                'payload' => ['ip' => $ip, 'identifier' => $identifier],
            ]);
        }
        return $retryAt;
    }

    /**
     * Refresh captcha via AJAX (no page reload; keeps username/password).
     */
    public function refreshCaptcha()
    {
        if (!config('admin.admin_login_captcha', false)) {
            return response()->json(['code' => '', 'image' => false]);
        }
        $captchaCode = $this->generateCaptchaCode();
        request()->session()->put('admin_login_captcha', $captchaCode);
        $useImage = extension_loaded('gd');
        return response()->json([
            'code' => $useImage ? '' : $captchaCode,
            'image' => $useImage,
            'imageUrl' => $useImage ? route('admin.login.captcha.image') . '?t=' . time() : null,
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth()->guard('admin');
    }

    public function username()
    {
        return 'username';
    }

    /**
     * Get the login credentials (email or username).
     */
    protected function credentials(Request $request)
    {
        $login = $request->input('username');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        return [
            $field => $login,
            'password' => $request->input('password'),
        ];
    }

    /**
     * Redirect back to admin login without flashing "remember", so the user must tick Remember Me again after any mistake.
     */
    protected function adminLoginBackWithErrors(Request $request, $errors, int $httpStatus = Response::HTTP_FOUND): void
    {
        $redirect = redirect()
            ->back()
            ->withErrors($errors)
            ->withInput($request->except(['remember', 'password', 'password_confirmation']));

        if ($httpStatus !== Response::HTTP_FOUND) {
            $redirect->setStatusCode($httpStatus);
        }

        throw new HttpResponseException($redirect);
    }

    /**
     * Require explicit "Remember Me" before admin login (UI + server).
     */
    protected function validateLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            $this->username() => 'required|string',
            'password' => 'required|string',
            'remember' => 'accepted',
        ]);

        if ($validator->fails()) {
            $this->adminLoginBackWithErrors($request, $validator);
        }
    }

    /**
     * Wrong credentials — do not re-flash "remember" (must confirm again).
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $this->adminLoginBackWithErrors($request, [
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Rate limit lockout — same as failed login for Remember Me state.
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn($this->throttleKey($request));

        $this->adminLoginBackWithErrors($request, [
            $this->username() => [trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])],
        ], Response::HTTP_TOO_MANY_REQUESTS);
    }

    public function login(Request $request)
    {
        $ip = $request->ip();
        $blockInfo = $this->getBlockInfo($ip);
        if ($blockInfo['blocked']) {
            return redirect()->route('admin.login');
        }

        $this->validateLogin($request);

        if (config('admin.admin_login_captcha', false)) {
            $sessionCode = (string) $request->session()->get('admin_login_captcha', '');
            $userCode = trim((string) $request->input('admin_login_captcha', ''));
            // Normalize: case-insensitive, remove spaces
            $sessionNormalized = mb_strtolower(preg_replace('/\s+/', '', $sessionCode), 'UTF-8');
            $userNormalized = mb_strtolower(preg_replace('/\s+/', '', $userCode), 'UTF-8');

            if ($sessionCode === '' || $userNormalized === '' || $userNormalized !== $sessionNormalized) {
                $request->session()->forget('admin_login_captcha');
                $this->recordFailedAttempt($ip, $request->input('username'));
                $msg = $sessionCode === ''
                    ? __('Session expired. Refresh the page and try again.')
                    : __('Invalid captcha. Enter the code as shown above (you can use small or capital letters).');
                $notify[] = ['error', $msg];
                return back()->withNotify($notify)->withInput($request->only('username'));
            }
            $request->session()->forget('admin_login_captcha');
        }

        Onumoti::getData();

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->recordFailedAttempt($ip);
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            AdminLockout::resetAttempts($ip);
            $admin = $this->guard()->user();
            if ($admin instanceof Admin) {
                if (AdminIpWhitelist::isEnabled() && $admin->isOwner() && !AdminIpWhitelist::isAllowed($ip)) {
                    $this->guard()->logout();
                    SecurityEvent::log('admin_ip_whitelist_denied', 'critical', [
                        'admin_id' => $admin->id,
                        'payload' => ['ip' => $ip, 'admin' => $admin->username],
                    ]);
                    $notify[] = ['error', __('Your IP is not in the whitelist. Contact administrator.')];
                    return back()->withNotify($notify)->withInput($request->only('username'));
                }
                $userAgent = $request->userAgent() ?? '';
                $fingerprint = $request->input('device_fingerprint'); // optional client-side fingerprint
                $deviceTrusted = TrustedAdminDevice::isTrusted($admin->id, $userAgent, $ip, $fingerprint);
                if (!$deviceTrusted && !$admin->hasTwoFactorEnabled()) {
                    $this->guard()->logout();
                    $request->session()->put('admin_2fa_pending_id', $admin->id);
                    $request->session()->put('admin_2fa_remember', $request->boolean('remember'));
                    return redirect()->route('admin.2fa.setup');
                }
                if ($admin->hasTwoFactorEnabled()) {
                    $this->guard()->logout();
                    $request->session()->put('admin_2fa_pending_id', $admin->id);
                    $request->session()->put('admin_2fa_remember', $request->boolean('remember'));
                    return redirect()->route('admin.2fa.verify');
                }
                if ($admin->mustHaveTwoFactor() && !$admin->hasTwoFactorEnabled()) {
                    $this->guard()->logout();
                    $request->session()->put('admin_2fa_pending_id', $admin->id);
                    $request->session()->put('admin_2fa_remember', $request->boolean('remember'));
                    return redirect()->route('admin.2fa.setup');
                }
            }
            return $this->sendLoginResponse($request);
        }

        $this->recordFailedAttempt($ip, $request->input('username'));
        $this->incrementLoginAttempts($request);
        SecurityEvent::log('failed_admin_login', 'medium', [
            'admin_id' => null,
            'route' => 'admin.login',
            'payload' => ['username' => $request->input('username')],
        ]);

        return $this->sendFailedLoginResponse($request);
    }


    public function logout(Request $request)
    {
        $sessionId = $request->session()->getId();
        $this->guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        \App\Models\AdminSession::where('session_id', $sessionId)->delete();
        return $this->loggedOut($request) ?: redirect()->route('admin.login');
    }
}
