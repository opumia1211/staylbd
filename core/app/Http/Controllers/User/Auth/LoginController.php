<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Constants\Status;
use App\Traits\OrderConfirmation;
use App\Models\UserLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use App\Models\ProductComparison;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    /** Cache key: failed login attempts per IP (progressive lockout) */
    const CACHE_ATTEMPTS = 'user_login_attempts_';
    /** Cache key: lockout until Unix timestamp per IP */
    const CACHE_LOCKOUT = 'user_login_lockout_';
    /** After 5 fails → 1 min, 10 → 3 min, 15+ → 5 min */
    const LOCKOUT_1_MIN_AFTER = 5;
    const LOCKOUT_3_MIN_AFTER = 10;
    const LOCKOUT_5_MIN_AFTER = 15;
    const LOCKOUT_1_MIN = 60;
    const LOCKOUT_3_MIN = 180;
    const LOCKOUT_5_MIN = 300;
    /** Show captcha after this many failed attempts */
    const CAPTCHA_AFTER_ATTEMPTS = 2;
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
    use OrderConfirmation;
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();
    }

    /**
     * Show login page at /user/login – same page stays open, no redirect to home.
     */
    public function showLoginForm(Request $request)
    {
        if (request()->has('redirect') && is_string(request()->get('redirect')) && \Illuminate\Support\Str::startsWith(request()->get('redirect'), url('/'))) {
            session()->put('url.intended', request()->get('redirect'));
        }
        $ip = $request->ip();
        $loginLockoutUntil = (int) Cache::get(self::CACHE_LOCKOUT . $ip, 0);
        if ($loginLockoutUntil <= time()) {
            $loginLockoutUntil = (int) session()->get('login_lockout_until', 0);
        }
        if ($loginLockoutUntil <= time()) {
            $loginLockoutUntil = null;
        }
        $attempts = (int) Cache::get(self::CACHE_ATTEMPTS . $ip, 0);
        $showLoginCaptchaDueToAttempts = $attempts >= self::CAPTCHA_AFTER_ATTEMPTS;
        return response()->view($this->activeTemplate . 'user.auth.modal_shell', [
            'openModal' => 'login',
            'loginLockoutUntil' => $loginLockoutUntil,
            'showLoginCaptchaDueToAttempts' => $showLoginCaptchaDueToAttempts,
        ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function login(Request $request)
    {
        $wantsJson = $request->wantsJson() || $request->ajax();

        try {
            $this->validateLogin($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($wantsJson) {
                return response()->json(['message' => __('Validation failed.'), 'errors' => $e->errors()], 422);
            }
            return redirect()->to(route('user.login'))
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors($e->errors());
        }

        $ip = $request->ip();
        $attempts = (int) Cache::get(self::CACHE_ATTEMPTS . $ip, 0);
        $lockoutUntil = (int) Cache::get(self::CACHE_LOCKOUT . $ip, 0);
        if ($lockoutUntil > time()) {
            return $this->sendProgressiveLockoutResponse($request, $lockoutUntil);
        }

        $requireCaptcha = isLoginCaptchaEnabled() || $attempts >= self::CAPTCHA_AFTER_ATTEMPTS;
        if ($requireCaptcha && !verifyCaptcha()) {
            if ($wantsJson) {
                return response()->json(['message' => __('Invalid captcha provided'), 'errors' => ['captcha' => [__('Invalid captcha provided')]]], 422);
            }
            $notify[] = ['error', __('Invalid captcha provided')];
            return redirect()->to(route('user.login'))->withInput($request->only($this->username(), 'remember'))->withNotify($notify);
        }

        // Preserve current session id so we can migrate guest compare list
        // even after Laravel regenerates the session on successful login.
        $request->session()->put('compare_old_session_id', $request->session()->getId());

        if ($this->attemptLogin($request)) {
            Cache::forget(self::CACHE_ATTEMPTS . $ip);
            Cache::forget(self::CACHE_LOCKOUT . $ip);
            return $this->sendLoginResponse($request);
        }

        $this->recordFailedLoginAttempt($ip);
        $lockoutUntil = (int) Cache::get(self::CACHE_LOCKOUT . $ip, 0);
        if ($lockoutUntil > time()) {
            return $this->sendProgressiveLockoutResponse($request, $lockoutUntil);
        }
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Progressive lockout: 5 fails → 1 min, 10 → 3 min, 15+ → 5 min.
     */
    protected function recordFailedLoginAttempt(string $ip): void
    {
        $key = self::CACHE_ATTEMPTS . $ip;
        $attempts = (int) Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addHours(1));

        $lockoutSeconds = null;
        if ($attempts >= self::LOCKOUT_5_MIN_AFTER) {
            $lockoutSeconds = self::LOCKOUT_5_MIN;
        } elseif ($attempts >= self::LOCKOUT_3_MIN_AFTER) {
            $lockoutSeconds = self::LOCKOUT_3_MIN;
        } elseif ($attempts >= self::LOCKOUT_1_MIN_AFTER) {
            $lockoutSeconds = self::LOCKOUT_1_MIN;
        }
        if ($lockoutSeconds !== null) {
            $unlockAt = time() + $lockoutSeconds;
            Cache::put(self::CACHE_LOCKOUT . $ip, $unlockAt, now()->addSeconds($lockoutSeconds + 60));
        }
    }

    protected function sendProgressiveLockoutResponse(Request $request, int $unlockAt)
    {
        $seconds = $unlockAt - time();
        $message = __('Too many login attempts. Please try again in :minutes minute(s).', ['minutes' => (int) ceil($seconds / 60)]);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'errors' => [$this->username() => [$message]],
                'retry_after' => $seconds,
                'login_lockout_until' => $unlockAt,
            ], 429);
        }
        return redirect()->to(route('user.login'))
            ->withInput($request->only($this->username()))
            ->withErrors([$this->username() => $message])
            ->with('login_lockout_until', $unlockAt);
    }

    /**
     * Redirect back with lockout message and exact unlock timestamp for countdown.
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn($this->throttleKey($request));
        $message = trans('auth.throttle', ['seconds' => $seconds, 'minutes' => (int) ceil($seconds / 60)]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'errors' => [$this->username() => [$message]],
                'retry_after' => $seconds,
                'login_lockout_until' => time() + $seconds,
            ], 429);
        }

        return redirect()->to(route('user.login'))
            ->withInput($request->only($this->username()))
            ->withErrors([$this->username() => $message])
            ->with('login_lockout_until', time() + $seconds);
    }

    /**
     * Show which field is wrong: wrong username/email or wrong password.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        activity_log(\App\Models\UserActivityLog::LOGIN_FAILED, 'Failed login attempt: ' . $request->input('username', ''), null, null);

        $credentialLabel = getLoginFieldLabel();
        [$loginTrim, $loginLower] = $this->normalizeLoginInput($request->input('username', ''));
        $user = null;

        if ($loginTrim !== '') {
            $config = loginFieldsConfig();
            $enabled = array_keys(array_filter($config, fn ($v) => (int) $v === 1));
            if (empty($enabled)) {
                $enabled = ['username', 'email'];
            }
            if (in_array('username', $enabled) && Schema::hasColumn('users', 'username')) {
                $user = User::whereRaw('LOWER(username) = ?', [$loginLower])->first();
            }
            if (!$user && in_array('email', $enabled)) {
                $user = $this->findUserByEmail($loginTrim, $loginLower);
            }
            if (!$user && in_array('email', $enabled) && strpos($loginLower, '@') === false) {
                $user = User::whereRaw('LOWER(SUBSTRING_INDEX(email, \'@\', 1)) = ?', [$loginLower])->first();
            }
            if (!$user && in_array('mobile', $enabled)) {
                $user = User::where('mobile', $loginTrim)->first();
            }
            if (!$user && strpos($loginLower, '@') !== false) {
                $user = $this->findUserByEmail($loginTrim, $loginLower);
            }
        }

        $errors = [];
        if (!$user) {
            $errors[$this->username()] = [__('auth.wrong_credential_or_not_found', ['label' => $credentialLabel])];
        } else {
            $errors['password'] = [__('auth.wrong_password')];
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => __('These credentials do not match our records.'),
                'errors' => $errors,
            ], 422);
        }

        return redirect()->to(route('user.login'))
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    /**
     * Throttle key separate from admin so user block does not affect admin and vice versa.
     */
    protected function throttleKey(Request $request)
    {
        return 'user_login_' . $request->ip();
    }

    /**
     * Determine which credential field to use (username, email, or mobile) from admin login settings.
     */
    public function findUsername()
    {
        $login = request()->input('username');
        $enabled = array_filter(loginFieldsConfig(), function ($v) { return (int) $v === 1; });
        $enabledKeys = array_keys($enabled);

        if (count($enabledKeys) === 0) {
            $enabledKeys = ['username', 'email'];
        }

        $fieldType = $enabledKeys[0];
        if (count($enabledKeys) > 1) {
            if (in_array('email', $enabledKeys) && filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $fieldType = 'email';
            } elseif (in_array('mobile', $enabledKeys) && preg_match('/^[0-9+\-\s]{8,20}$/', trim((string) $login))) {
                $fieldType = 'mobile';
            } elseif (in_array('username', $enabledKeys)) {
                $fieldType = 'username';
            }
        }

        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    protected function validateLogin(Request $request)
    {

        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);

    }

    /**
     * Get the needed authorization credentials from the request.
     * Resolves user by trying username (case-insensitive), email, email prefix,
     * then mobile so that login works even when username was never saved.
     */
    /**
     * Normalize login input (trim, lowercase for comparison).
     */
    private function normalizeLoginInput(?string $login): array
    {
        $trim = trim((string) ($login ?? ''));
        return [$trim, strtolower($trim)];
    }

    protected function credentials(Request $request)
    {
        $login = $request->input('username');
        $password = trim((string) $request->input('password', ''));
        if ($login === null || $login === '') {
            return [$this->username() => $login, 'password' => $password];
        }

        [$loginTrim, $loginLower] = $this->normalizeLoginInput($login);

        $config = loginFieldsConfig();
        $enabled = array_keys(array_filter($config, fn ($v) => (int) $v === 1));
        if (empty($enabled)) {
            $enabled = ['username', 'email'];
        }

        // 1) Try username (case-insensitive) – only if column exists and enabled
        if (in_array('username', $enabled) && Schema::hasColumn('users', 'username')) {
            $user = User::whereRaw('LOWER(username) = ?', [$loginLower])->first();
            if ($user) {
                return ['username' => $user->username, 'password' => $password];
            }
        }

        // 2) Try email (case-insensitive) – exact and prefix
        if (in_array('email', $enabled)) {
            $user = $this->findUserByEmail($loginTrim, $loginLower);
            if ($user) {
                return ['email' => $user->email, 'password' => $password];
            }
        }

        // 3) Try email prefix (e.g. "opumia" matches "opumia@...") when no @ in input
        if (in_array('email', $enabled) && strpos($loginLower, '@') === false) {
            $user = User::whereRaw('LOWER(SUBSTRING_INDEX(email, \'@\', 1)) = ?', [$loginLower])->first();
            if ($user) {
                return ['email' => $user->email, 'password' => $password];
            }
        }

        // 4) Try mobile
        if (in_array('mobile', $enabled)) {
            $user = User::where('mobile', $loginTrim)->first();
            if ($user) {
                return ['mobile' => $user->mobile, 'password' => $password];
            }
        }

        // 5) Backward compatibility: if input looks like email, always try by email so existing
        //    accounts (e.g. Opumia@gmail.com) can log in even when admin disabled email in login config.
        if (strpos($loginLower, '@') !== false) {
            $user = $this->findUserByEmail($loginTrim, $loginLower);
            if ($user) {
                return ['email' => $user->email, 'password' => $password];
            }
        }

        $fieldType = $this->username();
        return [$fieldType => $loginTrim, 'password' => $password];
    }

    /**
     * Find user by email (case-insensitive) for login credential resolution.
     */
    private function findUserByEmail(string $loginTrim, string $loginLower): ?User
    {
        return User::whereRaw('LOWER(email) = ?', [$loginLower])->first();
    }

    public function logout(Request $request)
    {
        activity_log(\App\Models\UserActivityLog::LOGOUT, 'User logged out', null, null);
        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $cookieName = config('session.cookie');
        $path = config('session.path', '/');
        $domain = config('session.domain');
        Cookie::queue(Cookie::forget($cookieName, $path, $domain));

        $referer = $request->headers->get('referer');
        $appUrl = url('/');
        $targetUrl = null;
        if ($referer && \Illuminate\Support\Str::startsWith($referer, $appUrl)
            && !\Illuminate\Support\Str::contains($referer, '/user/logout')) {
            $refererPath = parse_url($referer, PHP_URL_PATH);
            $refererPath = $refererPath ? trim($refererPath, '/') : '';
            $isProtectedUserArea = (bool) preg_match('#user/(?!login$|register$|password/|social-login)#', '/' . $refererPath);
            if (!$isProtectedUserArea) {
                $sep = strpos($referer, '?') !== false ? '&' : '?';
                $targetUrl = $referer . $sep . 'logged_out=1';
            }
        }
        if (!$targetUrl) {
            $targetUrl = route('home') . (strpos(route('home'), '?') !== false ? '&' : '?') . 'logged_out=1';
        }
        $redirect = redirect()->to($targetUrl);
        $redirect->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $redirect->header('Pragma', 'no-cache');
        $redirect->header('Expires', '0');
        return $redirect;
    }





    /**
     * Send login response; for AJAX return JSON with redirect URL so client can stay on same page.
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);
        $redirectResponse = $this->authenticated($request, $this->guard()->user());
        if ($request->wantsJson() || $request->ajax()) {
            $url = $redirectResponse && method_exists($redirectResponse, 'getTargetUrl')
                ? $redirectResponse->getTargetUrl()
                : route('user.home');
            return response()->json(['redirect' => $url]);
        }
        return $redirectResponse ?: redirect()->intended($this->redirectPath());
    }

    public function authenticated(Request $request, $user)
    {
        $user->tv = $user->ts == Status::VERIFIED ? Status::UNVERIFIED : Status::VERIFIED;
        $user->save();
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip',$ip)->first();
        $userLogin = new UserLogin();
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->city =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        }else{
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',',$info['long']);
            $userLogin->latitude =  @implode(',',$info['lat']);
            $userLogin->city =  @implode(',',$info['city']);
            $userLogin->country_code = @implode(',',$info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();

        activity_log(\App\Models\UserActivityLog::LOGIN, 'User logged in', null, null);

        if (session()->has('cart')) {
            static::createCart($user);
        }
        if (session()->has('wishlist')) {
            static::migrateWishlist($user);
        }

        // Migrate guest compare list (by cookie or session) to this user so items
        // added before login show up under the profile compare page.
        $guestCompareId = $request->cookie(\App\Models\ProductComparison::GUEST_COOKIE_NAME);
        ProductComparison::migrateSessionToUser((int) $user->id, $guestCompareId);

        // 1) একই পেজে থাকুন – রিডাইরেক্ট URL অনুযায়ী যেখান থেকে লগইন করেছিলেন সেখানে ফেরান
        $redirect = $request->input('redirect');
        if ($redirect && is_string($redirect)) {
            $url = trim($redirect);
            if ($url !== '' && \Illuminate\Support\Str::startsWith($url, url('/'))) {
                $url = $this->stripOpenParamFromUrl($url);
                return redirect()->to($url);
            }
        }
        // 2) Laravel intended URL
        $intended = session()->pull('url.intended');
        if ($intended && is_string($intended) && $intended !== '' && \Illuminate\Support\Str::startsWith($intended, url('/'))) {
            $intended = $this->stripOpenParamFromUrl($intended);
            return redirect()->to($intended);
        }
        return to_route('user.home');
    }

    /**
     * Strip open=register and open=login from URL so redirect does not re-open the auth panel.
     */
    private function stripOpenParamFromUrl(string $url): string
    {
        $url = preg_replace('/([?&])open=(?:register|login)(?=&|$)/', '$1', $url);
        $url = preg_replace('/\?&/', '?', $url);
        return rtrim(rtrim($url, '&'), '?');
    }

    public function redirectToProvider($provider)
    {
        $driver = config("services.{$provider}.client_id") && config("services.{$provider}.client_secret")
            ? Socialite::driver($provider)
            : null;
        if (!$driver) {
            $notify[] = ['error', __('Social login is not configured. Please use email/password or contact support.')];
            return redirect()->route('home', ['open' => 'login'])->withNotify($notify);
        }
        if (request()->get('popup') == '1') {
            request()->session()->put('social_popup', 1);
            $redirect = request()->get('redirect');
            if ($redirect && is_string($redirect) && $redirect !== '' && \Illuminate\Support\Str::startsWith($redirect, url('/'))) {
                request()->session()->put('social_login_redirect', $redirect);
            }
        }
        $callbackUrl = rtrim(url('/'), '/') . '/user/social-login/' . $provider . '/callback';
        config(["services.{$provider}.redirect" => $callbackUrl]);
        try {
            return $driver->redirect();
        } catch (\Throwable $e) {
            $notify[] = ['error', __('Unable to start social login. Please try again or use email/password.')];
            return redirect()->route('home', ['open' => 'login'])->withNotify($notify);
        }
    }

    public function handleProviderCallback(Request $request, $provider)
    {
        if (!config("services.{$provider}.client_id") || !config("services.{$provider}.client_secret")) {
            $notify[] = ['error', __('Social login is not configured. Please use email/password.')];
            return redirect()->route('home', ['open' => 'login'])->withNotify($notify);
        }
        try {
            $socialUser = $provider === 'twitter'
                ? Socialite::driver('twitter')->user()
                : Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            $notify[] = ['error', __('Login was cancelled or failed. Please try again or use email/password.')];
            return redirect()->route('home', ['open' => 'login'])->withNotify($notify);
        }

        $existingByProvider = User::where('provider_id', $socialUser->getId())->first();
        if ($existingByProvider) {
            auth()->login($existingByProvider, true);
            if (session('social_popup') == 1) {
                $redirectUrl = session()->pull('social_login_redirect');
                session()->forget('social_popup');
                return view($this->activeTemplate . 'user.auth.social_callback_close', ['redirectUrl' => $redirectUrl]);
            }
            return $this->redirectAfterSocialLogin();
        }

        $existingByEmail = null;
        if ($socialUser->getEmail()) {
            $existingByEmail = User::where('email', $socialUser->getEmail())->first();
        }

        if ($existingByEmail) {
            $existingByEmail->provider_id = $socialUser->getId();
            $existingByEmail->provider = $provider;
            $existingByEmail->save();
            auth()->login($existingByEmail, true);
            if (session('social_popup') == 1) {
                $redirectUrl = session()->pull('social_login_redirect');
                session()->forget('social_popup');
                return view($this->activeTemplate . 'user.auth.social_callback_close', ['redirectUrl' => $redirectUrl]);
            }
            return $this->redirectAfterSocialLogin();
        }

        $user = new User();
        $user->username = $socialUser->getNickname() ?: ($socialUser->getEmail() ? strstr($socialUser->getEmail(), '@', true) : 'user_'.uniqid());
        $user->username = preg_replace('/[^a-z0-9_]/', '', strtolower($user->username)) ?: ('user_'.uniqid());
        if (User::where('username', $user->username)->exists()) {
            $user->username = 'user_'.uniqid();
        }
        $user->email = $socialUser->getEmail() ?: ('social_'.uniqid().'@placeholder.local');
        $name = $socialUser->getName() ?: 'User';
        $parts = explode(' ', $name, 2);
        $user->firstname = $parts[0] ?? 'User';
        $user->lastname = $parts[1] ?? '';
        $user->ev = 1;
        $user->sv = 0;
        $user->provider_id = $socialUser->getId();
        $user->provider = $provider;
        $user->password = bcrypt(str()->random(16));
        $user->profile_complete = Status::NO;
        $user->save();

        auth()->login($user, true);
        if (session('social_popup') == 1) {
            $redirectUrl = session()->pull('social_login_redirect');
            session()->forget('social_popup');
            return view($this->activeTemplate . 'user.auth.social_callback_close', ['redirectUrl' => $redirectUrl]);
        }
        return $this->redirectAfterSocialLogin();
    }

    private function redirectAfterSocialLogin()
    {
        $url = session()->pull('social_login_redirect');
        if ($url && is_string($url) && $url !== '' && \Illuminate\Support\Str::startsWith($url, url('/'))) {
            return redirect()->to($this->stripOpenParamFromUrl($url));
        }
        return redirect()->intended(route('home'));
    }

}
