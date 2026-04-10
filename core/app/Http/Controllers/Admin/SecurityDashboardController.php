<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminIpWhitelist;
use App\Models\AdminLockout;
use App\Models\SecurityAuditLog;
use App\Models\SecuritySetting;
use Illuminate\Support\Facades\Artisan;
use App\Models\AdminSession;
use App\Models\PaymentEvent;
use App\Models\SecurityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SecurityDashboardController extends Controller
{
    /**
     * Security overview – forensic style with config status & recommendations.
     */
    public function index(Request $request)
    {
        $last24h = now()->subDay();
        $last7d = now()->subDays(7);

        $data = [
            'failed_logins' => SecurityEvent::where('event_type', 'failed_admin_login')
                ->where('created_at', '>', $last24h)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get(),
            'two_fa_failures' => SecurityEvent::whereIn('event_type', ['admin_2fa_failed', 'admin_2fa_rate_limit', 'admin_2fa_setup_failed', 'admin_2fa_setup_rate_limit'])
                ->where('created_at', '>', $last24h)
                ->orderBy('created_at', 'desc')
                ->limit(30)
                ->get(),
            'rate_limit_triggers' => SecurityEvent::where('event_type', 'rate_limit_exceeded')
                ->where('created_at', '>', $last24h)
                ->count(),
            'payment_signature_failures' => PaymentEvent::where('event_type', 'signature_failed')
                ->where('created_at', '>', $last24h)
                ->orderBy('created_at', 'desc')
                ->limit(30)
                ->get(),
            'active_admin_sessions' => AdminSession::with('admin:id,name,username')
                ->where('last_activity_at', '>', now()->subHours(24))
                ->orderBy('last_activity_at', 'desc')
                ->get(),
            'active_lockouts' => AdminLockout::whereNotNull('locked_at')
                ->where('locked_at', '>', now())
                ->orderBy('locked_at', 'desc')
                ->get(),
            'suspicious_ips' => SecurityEvent::where('created_at', '>', $last24h)
                ->whereIn('event_type', ['failed_admin_login', 'payment_replay_attempt', 'admin_2fa_failed', 'admin_2fa_setup_failed'])
                ->selectRaw('ip_address, count(*) as cnt')
                ->groupBy('ip_address')
                ->havingRaw('count(*) >= 3')
                ->orderByDesc('cnt')
                ->limit(20)
                ->get(),
            'critical_events_24h' => SecurityEvent::whereIn('severity', ['critical', 'high'])
                ->where('created_at', '>', $last24h)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get(),
            'stats' => $this->getStats($last24h, $last7d),
            'security_config' => $this->getSecurityConfig(),
            'recommendations' => $this->getRecommendations(),
            'audit_logs' => $this->getAuditLogs(),
        ];

        $this->ensureSecuritySettingsExist();

        return view('admin.security.dashboard', $data);
    }

    protected function getStats($last24h, $last7d): array
    {
        return [
            'failed_logins_24h' => SecurityEvent::where('event_type', 'failed_admin_login')->where('created_at', '>', $last24h)->count(),
            'failed_logins_7d'  => SecurityEvent::where('event_type', 'failed_admin_login')->where('created_at', '>', $last7d)->count(),
            'payment_failures_24h' => PaymentEvent::where('event_type', 'signature_failed')->where('created_at', '>', $last24h)->count(),
            'critical_24h' => SecurityEvent::where('severity', 'critical')->where('created_at', '>', $last24h)->count(),
            'admin_count' => Admin::count(),
            'admin_with_2fa' => Admin::whereNotNull('two_factor_confirmed_at')->whereNotNull('two_factor_secret')->count(),
            'lockout_count' => AdminLockout::whereNotNull('locked_at')->where('locked_at', '>', now())->count(),
        ];
    }

    protected function getSecurityConfig(): array
    {
        $env = config('app.env');
        $debug = config('app.debug');
        $sessionEncrypt = config('session.encrypt', false);
        $sessionSecure = config('session.secure', false);
        $https = request()->secure();
        $adminPrefix = config('admin.prefix', 'admin');
        $ipWhitelist = AdminIpWhitelist::isEnabled();
        $whitelistCount = AdminIpWhitelist::where('enabled', true)->count();
        $adminCaptcha = config('admin.admin_login_captcha', false);
        $adminTwoFactor = config('admin.admin_two_factor_enabled', true);

        return [
            'app_env'         => $env,
            'app_debug'       => $debug,
            'session_encrypt' => $sessionEncrypt,
            'session_secure'  => $sessionSecure,
            'https'           => $https,
            'admin_prefix'    => $adminPrefix,
            'admin_prefix_ok' => $adminPrefix !== 'admin', // custom prefix = safer
            'ip_whitelist'    => $ipWhitelist,
            'whitelist_count' => $whitelistCount,
            'admin_captcha'   => $adminCaptcha,
            'admin_two_factor' => $adminTwoFactor,
        ];
    }

    protected function ensureSecuritySettingsExist(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('security_settings')) {
            return;
        }
        foreach ([
            'ip_whitelist_enabled' => env('ADMIN_IP_WHITELIST_ENABLED', false),
            'admin_login_captcha' => env('ADMIN_LOGIN_CAPTCHA', false),
            'admin_two_factor_enabled' => env('ADMIN_TWO_FACTOR_ENABLED', true),
        ] as $key => $envDefault) {
            SecuritySetting::firstOrCreate(
                ['key' => $key],
                ['value' => filter_var($envDefault, FILTER_VALIDATE_BOOLEAN) ? '1' : '0', 'updated_at' => now()]
            );
        }
    }

    protected function getAuditLogs()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('security_audit_logs')) {
            return collect([]);
        }
        return SecurityAuditLog::with('admin:id,name,username')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * AJAX: Toggle a security setting (DB-backed: ip_whitelist_enabled, admin_login_captcha, admin_two_factor_enabled).
     * SuperAdmin only.
     */
    public function toggleSetting(Request $request)
    {
        $request->validate(['key' => 'required|string|in:ip_whitelist_enabled,admin_login_captcha,admin_two_factor_enabled', 'value' => 'required|boolean']);
        $key = $request->key;
        $newValue = $request->boolean('value') ? '1' : '0';

        $old = SecuritySetting::get($key, '');
        SecuritySetting::set($key, $newValue);
        SecurityAuditLog::log($key, $old, $newValue);

        if ($key === 'admin_login_captcha' || $key === 'admin_two_factor_enabled') {
            SecuritySetting::forgetCache($key);
            try {
                Artisan::call('config:clear');
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return response()->json(['success' => true, 'value' => $request->boolean('value')]);
    }

    /**
     * Update admin prefix in .env and clear caches. SuperAdmin only.
     * Returns new admin URL; frontend should redirect (session may be invalidated).
     */
    public function updateAdminPrefix(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prefix' => 'required|string|max:60|regex:/^[a-zA-Z0-9_-]+$/',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => __('Invalid prefix. Use only letters, numbers, underscore, hyphen.')], 422);
        }

        $prefix = trim($request->prefix);
        if (strtolower($prefix) === 'admin') {
            return response()->json(['success' => false, 'message' => __('Use a custom prefix, not "admin".')], 422);
        }

        $oldPrefix = config('admin.prefix', 'admin');
        if ($prefix === $oldPrefix) {
            return response()->json(['success' => true, 'redirect_url' => route('admin.security.dashboard')]);
        }

        $updated = updateEnv('ADMIN_PREFIX', $prefix);
        if (!$updated) {
            return response()->json(['success' => false, 'message' => __('Could not update .env file.')], 500);
        }

        SecurityAuditLog::log('ADMIN_PREFIX', $oldPrefix, $prefix);

        try {
            Artisan::call('route:clear');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
        } catch (\Throwable $e) {
            // continue
        }

        $newUrl = url($prefix);
        return response()->json([
            'success' => true,
            'redirect_url' => $newUrl,
            'message' => __('Admin prefix updated. You will be redirected. Session may require re-login.'),
        ]);
    }

    protected function getRecommendations(): array
    {
        $recs = [];

        if (config('app.env') !== 'production') {
            $recs[] = ['level' => 'info', 'msg' => __('Ensure APP_ENV=production in production.')];
        }
        if (config('app.debug')) {
            $recs[] = ['level' => 'danger', 'msg' => __('Disable APP_DEBUG in production.')];
        }
        if (!config('session.encrypt', false)) {
            $recs[] = ['level' => 'warning', 'msg' => __('Enable SESSION_ENCRYPT in production.')];
        }
        if (!request()->secure() && config('app.env') === 'production') {
            $recs[] = ['level' => 'warning', 'msg' => __('Use HTTPS and SESSION_SECURE_COOKIE.')];
        }
        if (config('admin.prefix') === 'admin') {
            $recs[] = ['level' => 'warning', 'msg' => __('Use custom ADMIN_PREFIX in .env for security.')];
        }
        if (!config('admin.admin_two_factor_enabled', true)) {
            $recs[] = ['level' => 'warning', 'msg' => __('Admin login two-factor is off. Enable it before production.')];
        }
        if (config('admin.admin_two_factor_enabled', true)) {
            $mandatory = config('admin.two_factor_mandatory_roles', []);
            $without2fa = Admin::whereIn('role', $mandatory)
                ->where(function ($q) {
                    $q->whereNull('two_factor_confirmed_at')->orWhereNull('two_factor_secret');
                })
                ->count();
            if ($without2fa > 0) {
                $recs[] = ['level' => 'danger', 'msg' => __(':n Owner/SuperAdmin without 2FA.', ['n' => $without2fa])];
            }
        }

        if (empty($recs)) {
            $recs[] = ['level' => 'success', 'msg' => __('Key security settings look good.')];
        }

        return $recs;
    }

    /**
     * Clear all admin lockouts (SuperAdmin action).
     */
    public function clearLockouts(Request $request)
    {
        $count = AdminLockout::whereNotNull('locked_at')->update([
            'locked_at' => null,
            'unlocked_at' => now(),
            'failed_attempts' => 0,
        ]);

        $notify[] = ['success', __(':n lockout(s) cleared.', ['n' => $count])];
        return back()->withNotify($notify);
    }

    /**
     * Run security scan and show results.
     */
    public function runScan(Request $request)
    {
        Artisan::call('security:scan');
        $output = trim(Artisan::output());

        $notify[] = ['success', __('Security scan completed. Check logs for details.')];
        return back()->withNotify($notify)->with('scan_output', $output);
    }
}
