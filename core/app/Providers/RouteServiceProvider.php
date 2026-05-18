<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidateIpnHmac;
use App\Http\Middleware\VerifyCsrfToken;
use Laramin\Utility\VugiChugi;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */

    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // Health check (minimal middleware for monitoring)
            Route::middleware('web')->group(base_path('routes/health.php'));

            // API routes (REST) – set API_PREFIX=api/v1 for versioned API
            Route::prefix(env('API_PREFIX', 'api'))->middleware('api')->group(base_path('routes/api.php'));

            Route::namespace($this->namespace)->middleware(VugiChugi::mdNm())->group(function(){
                // Payment IPN: no CSRF (external POST). Optional HMAC when IPN_HMAC_SECRET is set.
                Route::middleware(['web', 'maintenance', 'throttle:ipn', ValidateIpnHmac::class])
                    ->withoutMiddleware([VerifyCsrfToken::class])
                    ->namespace('Gateway')
                    ->prefix('ipn')
                    ->name('ipn.')
                    ->group(base_path('routes/ipn.php'));
                // Payment webhook (e.g. PoysaPay): https://yoursite.com/payment/webhook/poysapay
                Route::middleware(['web', 'maintenance', 'throttle:ipn'])
                    ->withoutMiddleware([VerifyCsrfToken::class])
                    ->namespace('Webhook')
                    ->prefix('payment/webhook')
                    ->name('payment.webhook.')
                    ->group(function () {
                        Route::post('poysapay', 'PoysaPayWebhookController')->name('poysapay');
                    });
                Route::middleware(['web', 'no-cache'])
                    ->namespace('Admin')
                    ->prefix(config('admin.prefix'))
                    ->name('admin.')
                    ->group(base_path('routes/admin.php'));



                // Simplified registration: individual route files (web.php, user.php) 
                // now handle their own {locale?} prefixing for maximum control.
                Route::middleware(['web', 'maintenance', 'localization'])
                    ->group(function() {
                        Route::prefix('user')->group(base_path('routes/user.php'));
                        Route::group([], base_path('routes/web.php'));
                    });
            });

        });

        Route::middleware('web')->get('maintenance-mode','App\Http\Controllers\SiteController@maintenance')->name('maintenance');
        Route::middleware('web')->get('banner-image/{filename}', 'App\Http\Controllers\SiteController@serveBannerImage')->name('banner.image')->where('filename', '[a-zA-Z0-9_.-]+');
        Route::middleware('web')->get('row-split-banner/{filename}', 'App\Http\Controllers\SiteController@serveRowSplitBanner')->name('row.split.image')->where('filename', '[a-zA-Z0-9_.-]+');
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            $perMin = max(30, min(500, (int) config('app.api_rate_limit_per_minute', 120)));

            return Limit::perMinute($perMin)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('realtime_poll', function (Request $request) {
            return Limit::perMinute(45)->by($request->ip());
        });

        // User login: progressive lockout in LoginController (5→1m, 10→3m, 15→5m). Throttle as safety: 25/min per IP.
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(25)->by('user_' . $request->ip())->response(function () use ($request) {
                $seconds = 60;
                return redirect()->to(route('user.login'))
                    ->withErrors(['username' => __('Too many requests. Please wait before trying again.')])
                    ->with('login_lockout_until', time() + $seconds);
            });
        });

        // User register: 5 attempts per 10 minutes per IP (brute force / spam protection)
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinutes(10, 5)->by('register_' . $request->ip())->response(function () {
                return redirect()->back()
                    ->withErrors(['error' => __('Too many registration attempts. Please try again later.')]);
            });
        });

        // Review submission: 5 per minute per user/IP
        RateLimiter::for('review', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        // Admin login: separate throttle so user block does not affect admin and vice versa
        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinutes(5, 5)->by('admin_' . $request->ip())->response(function () {
                return redirect()->back()
                    ->withErrors(['username' => __('Too many attempts. Please try again in 5 minutes.')])
                    ->with('admin_lockout_until', now()->addMinutes(5)->timestamp);
            });
        });
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())->response(function () use ($request) {
                $seconds = 60;
                return redirect()->route('user.password.request')
                    ->with('password_reset_throttle_until', time() + $seconds)
                    ->with('error', __('Too many requests. Please wait before trying again.'));
            });
        });

        // Admin password reset: own bucket + redirect back to admin (not user.password.request)
        RateLimiter::for('admin-password-reset', function (Request $request) {
            return Limit::perMinutes(15, 12)->by('admin_pwd_reset_mw_' . $request->ip())->response(function () {
                return redirect()->route('admin.password.reset')
                    ->with('password_reset_throttle_until', time() + (15 * 60))
                    ->withNotify([['error', __('Too many password reset requests. Please wait 15 minutes and try again.')]]);
            });
        });

        // Product filter / listing: prevent abuse and server overload (120/min per IP)
        RateLimiter::for('product_filter', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        // Admin cache clear – 5 per minute per admin (SuperAdmin only)
        RateLimiter::for('admin_clear', function (Request $request) {
            $user = $request->user('admin');
            $key = $user ? 'admin_clear:' . $user->id : $request->ip();
            return Limit::perMinute(5)->by($key)->response(function () {
                return response()->json(['message' => 'Too many requests. Try again in a minute.'], 429);
            });
        });

        // Checkout/Order – limit abuse (30/min per IP)
        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // IPN webhooks – limit spam (100/min per IP)
        RateLimiter::for('ipn', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip());
        });

        // Admin 2FA POST only — generous HTTP cap; wrong-code lockout is enforced in TwoFactorController (per admin).
        RateLimiter::for('admin_2fa', function (Request $request) {
            return Limit::perMinutes(5, 45)->by('admin_2fa_post_' . $request->ip())->response(function (Request $request, array $headers) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => __('Too many requests. Please wait before trying again.'),
                    ], 429, $headers);
                }
                $name = $request->route()?->getName();
                $to = $name === 'admin.2fa.setup.confirm'
                    ? route('admin.2fa.setup')
                    : route('admin.2fa.verify');

                return redirect()->to($to)
                    ->withNotify([['error', __('Too many attempts in a short time. Please wait about a minute and try again.')]])
                    ->withHeaders($headers);
            });
        });

        // Cart API – 60/min per user or IP
        RateLimiter::for('cart', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Payment / deposit – 20/min per IP
        RateLimiter::for('payment', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });
    }
}
