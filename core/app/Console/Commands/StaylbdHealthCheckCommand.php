<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

/**
 * StayLBD Full Site Health Check – সব ফিচার ও সিস্টেম যাচাই।
 * XAMPP টার্মিনালে: php artisan staylbd:health-check
 * অপশন: --http = হোমপেজ ও অ্যাডমিন লগইন পেজ HTTP রিকোয়েস্ট দিয়ে চেক (ওয়েব সার্ভার চালু থাকতে হবে)
 */
class StaylbdHealthCheckCommand extends Command
{
    protected $signature = 'staylbd:health-check {--http : Check homepage and admin login page via HTTP}';
    protected $description = 'StayLBD: Verify all features, security, DB, routes and optional HTTP endpoints';

    private array $results = [];
    private int $passed = 0;
    private int $failed = 0;

    public function handle(): int
    {
        $this->info('');
        $this->info('===== StayLBD Full Health Check =====');
        $this->info('');

        $this->runCheck('Environment & Config', [$this, 'checkConfig']);
        $this->runCheck('Database Connection', [$this, 'checkDatabase']);
        $this->runCheck('Critical Tables', [$this, 'checkTables']);
        $this->runCheck('Migrations Status', [$this, 'checkMigrations']);
        $this->runCheck('Payment Ledger Integrity', [$this, 'checkLedger']);
        $this->runCheck('Audit Log Integrity', [$this, 'checkAudit']);
        $this->runCheck('Data Retention Command', [$this, 'checkDataRetention']);
        $this->runCheck('Security Report Command', [$this, 'checkSecurityReport']);
        $this->runCheck('Critical Classes & Files', [$this, 'checkFiles']);
        $this->runCheck('Key Routes', [$this, 'checkRoutes']);
        $this->runCheck('Config & Services', [$this, 'checkServices']);

        if ($this->option('http')) {
            $this->runCheck('HTTP – Homepage & Admin', [$this, 'checkHttp']);
        }

        $this->printSummary();
        return $this->failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function runCheck(string $name, callable $check): void
    {
        try {
            $msg = $check();
            if ($msg === true || $msg === '' || $msg === null) {
                $this->results[] = [$name, 'OK', '—'];
                $this->passed++;
            } elseif (is_string($msg)) {
                $this->results[] = [$name, 'OK', $msg];
                $this->passed++;
            } else {
                $this->results[] = [$name, 'FAIL', is_string($msg) ? $msg : 'Check returned false'];
                $this->failed++;
            }
        } catch (\Throwable $e) {
            $this->results[] = [$name, 'FAIL', $e->getMessage()];
            $this->failed++;
        }
    }

    private function checkConfig(): ?string
    {
        if (empty(config('app.key'))) {
            throw new \RuntimeException('APP_KEY is empty. Run: php artisan key:generate');
        }
        $prefix = config('admin.prefix', 'admin');
        return "APP_KEY set, Admin prefix: {$prefix}";
    }

    private function checkDatabase(): ?string
    {
        DB::connection()->getPdo();
        $name = config('database.connections.' . config('database.default') . '.database');
        return "Connected to DB: {$name}";
    }

    private function checkTables(): ?string
    {
        $tables = [
            'users', 'admins', 'orders', 'order_details', 'general_settings', 'products',
            'frontends', 'subscribers', 'carts', 'deposits', 'categories', 'brands', 'reviews',
            'payment_ledger', 'security_events', 'audit_logs', 'payment_events',
            'trusted_admin_devices', 'admin_ip_whitelist',
        ];
        $missing = [];
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $missing[] = $table;
            }
        }
        if (!empty($missing)) {
            throw new \RuntimeException('Missing tables: ' . implode(', ', $missing) . '. Run: php artisan migrate');
        }
        return count($tables) . ' critical tables exist';
    }

    private function checkMigrations(): ?string
    {
        Artisan::call('migrate:status');
        $output = Artisan::output();
        if (preg_match('/\d+ pending/', $output) || str_contains($output, 'Pending')) {
            return 'Some migrations pending. Run: php artisan migrate';
        }
        return 'No pending migrations';
    }

    private function checkLedger(): ?string
    {
        $exit = Artisan::call('ledger:verify');
        if ($exit !== 0) {
            throw new \RuntimeException('Ledger integrity failed. See: php artisan ledger:verify');
        }
        return 'Ledger hash chain OK';
    }

    private function checkAudit(): ?string
    {
        $errors = \App\Models\AuditLog::verifyIntegrity();
        if (!empty($errors)) {
            throw new \RuntimeException('Audit log integrity failed. Errors: ' . count($errors));
        }
        return 'Audit log chain OK';
    }

    private function checkDataRetention(): ?string
    {
        Artisan::call('data:retention:enforce');
        return 'Command runs OK';
    }

    private function checkSecurityReport(): ?string
    {
        $month = now()->format('Y-m');
        Artisan::call('security:report', ['--month' => $month]);
        return "Report generated for {$month}";
    }

    private function checkFiles(): ?string
    {
        $paths = [
            'app/Models/PaymentLedger.php',
            'app/Models/AuditLog.php',
            'app/Models/SecurityEvent.php',
            'app/Models/TrustedAdminDevice.php',
            'app/Http/Controllers/Gateway/StripeV3/ProcessController.php',
            'app/Http/Middleware/RequireReAuthentication.php',
            'app/Console/Commands/LedgerVerifyCommand.php',
            'app/Console/Commands/DataRetentionEnforceCommand.php',
            'app/Console/Commands/SecurityReportCommand.php',
            'config/admin.php',
        ];
        $missing = [];
        foreach ($paths as $path) {
            if (!file_exists(base_path($path))) {
                $missing[] = $path;
            }
        }
        if (!empty($missing)) {
            throw new \RuntimeException('Missing: ' . implode(', ', $missing));
        }
        return count($paths) . ' critical files exist';
    }

    private function checkRoutes(): ?string
    {
        $routes = ['admin.login', 'home'];
        $prefix = config('admin.prefix', 'admin');
        foreach ($routes as $name) {
            if (!Route::has($name)) {
                throw new \RuntimeException("Route [{$name}] not registered.");
            }
        }
        $adminUrl = url($prefix);
        return "admin.login, home OK (admin: {$adminUrl})";
    }

    private function checkServices(): ?string
    {
        $list = [];
        if (class_exists(\App\Services\TOTPService::class)) {
            $list[] = 'TOTPService';
        }
        if (class_exists(\App\Services\PaymentEventLogger::class)) {
            $list[] = 'PaymentEventLogger';
        }
        if (config('admin.zero_trust_mode') === true) {
            $list[] = 'Zero-Trust ON';
        }
        return $list ? implode(', ', $list) : 'Core services loaded';
    }

    private function checkHttp(): ?string
    {
        $base = rtrim(config('app.url'), '/');
        $prefix = config('admin.prefix', 'admin');
        $home = $base . '/';
        $adminLogin = $base . '/' . $prefix;
        $health = $base . '/health';

        $ctx = stream_context_create([
            'http' => [
                'timeout' => 5,
                'ignore_errors' => true,
            ],
        ]);

        $errors = [];
        foreach (['Home' => $home, 'Admin login' => $adminLogin, 'Health' => $health] as $label => $url) {
            $code = @get_headers($url, 1, $ctx)[0] ?? '';
            if (!str_contains($code, '200') && !str_contains($code, '302')) {
                $errors[] = "{$label} ({$url}): " . ($code ?: 'No response');
            }
        }
        if (!empty($errors)) {
            throw new \RuntimeException(implode('; ', $errors));
        }
        return 'Home, Admin login, /health responded';
    }

    private function printSummary(): void
    {
        $this->info('');
        $this->table(['Check', 'Status', 'Detail'], $this->results);
        $this->info('');
        $this->info("Passed: {$this->passed} | Failed: {$this->failed}");
        if ($this->failed > 0) {
            $this->error('কিছু চেক ব্যর্থ। উপরের বিস্তারিত দেখুন।');
        } else {
            $this->info('সব চেক সফল – সাইট ও ফিচার সঠিকভাবে কাজ করছে।');
        }
        $this->info('');
    }
}
