<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SystemController extends Controller
{
    public function systemInfo()
    {
        $pageTitle = 'Application Information';
        $laravelVersion = app()->version();
        $timeZone = config('app.timezone');
        $env = config('app.env');
        $debug = config('app.debug');
        $url = config('app.url');

        // PHP Info
        $phpVersion = phpversion();
        $phpExtensions = get_loaded_extensions();
        sort($phpExtensions);
        $requiredExtensions = ['bcmath', 'ctype', 'json', 'mbstring', 'openssl', 'pdo', 'tokenizer', 'xml', 'fileinfo'];
        $missingExtensions = array_diff($requiredExtensions, $phpExtensions);

        // Database
        $dbDriver = config('database.default');
        $dbName = config('database.connections.' . $dbDriver . '.database') ?? 'N/A';
        $dbConnected = false;
        $dbVersion = null;
        try {
            $dbConnected = true;
            if ($dbDriver === 'mysql') {
                $v = DB::selectOne('SELECT VERSION() as v');
                $dbVersion = $v ? $v->v : null;
            } elseif ($dbDriver === 'pgsql') {
                $v = DB::selectOne('SELECT version() as v');
                $dbVersion = $v ? $v->v : null;
            } else {
                $dbVersion = ucfirst($dbDriver);
            }
        } catch (\Throwable $e) {
            $dbVersion = 'Error: ' . $e->getMessage();
        }

        // Storage & Permissions
        $storagePath = storage_path();
        $storageWritable = is_writable($storagePath);
        $bootstrapCachePath = base_path('bootstrap/cache');
        $bootstrapCacheWritable = is_writable($bootstrapCachePath);

        // Disk & Memory (approximate)
        $diskFree = function_exists('disk_free_space') ? @disk_free_space(base_path()) : null;
        $diskTotal = function_exists('disk_total_space') ? @disk_total_space(base_path()) : null;
        $memoryLimit = ini_get('memory_limit');
        $uploadMax = ini_get('upload_max_filesize');
        $postMax = ini_get('post_max_size');
        $maxExecutionTime = ini_get('max_execution_time');

        // Format disk space for display
        $diskFreeFormatted = $diskFree !== null ? $this->formatBytes($diskFree) : 'N/A';
        $diskTotalFormatted = $diskTotal !== null ? $this->formatBytes($diskTotal) : 'N/A';

        // App paths
        $basePath = base_path();
        $storagePathReal = realpath($storagePath);

        // Drivers & Config
        $cacheDriver = config('cache.default');
        $sessionDriver = config('session.driver');
        $queueDriver = config('queue.default');
        $mailDriver = config('mail.default');

        // Server OS
        $serverOs = PHP_OS_FAMILY ?? PHP_OS;

        // HTTPS
        $isHttps = request()->secure();

        // Active template
        try {
            $activeTemplate = gs('active_template') ?? 'basic';
        } catch (\Throwable $e) {
            $activeTemplate = 'basic';
        }

        // Database tables count (MySQL only)
        $dbTablesCount = null;
        try {
            if ($dbDriver === 'mysql' && $dbName && $dbName !== 'N/A') {
                $t = DB::selectOne("SELECT COUNT(*) as c FROM information_schema.tables WHERE table_schema = ?", [$dbName]);
                $dbTablesCount = $t ? (int) $t->c : null;
            }
        } catch (\Throwable $e) {
            $dbTablesCount = null;
        }

        // Log file size
        $logSize = 'N/A';
        try {
            $logPath = storage_path('logs/laravel.log');
            if (file_exists($logPath) && is_readable($logPath)) {
                $logSize = $this->formatBytes(filesize($logPath));
            }
        } catch (\Throwable $e) {
            $logSize = 'N/A';
        }

        return view('admin.system.info', compact(
            'pageTitle', 'laravelVersion', 'timeZone', 'env', 'debug', 'url',
            'phpVersion', 'phpExtensions', 'requiredExtensions', 'missingExtensions',
            'dbDriver', 'dbName', 'dbConnected', 'dbVersion', 'dbTablesCount',
            'storageWritable', 'bootstrapCacheWritable',
            'diskFree', 'diskTotal', 'diskFreeFormatted', 'diskTotalFormatted',
            'memoryLimit', 'uploadMax', 'postMax', 'maxExecutionTime',
            'basePath', 'storagePathReal',
            'cacheDriver', 'sessionDriver', 'queueDriver', 'mailDriver',
            'serverOs', 'isHttps', 'activeTemplate', 'logSize'
        ));
    }

    public function systemInfoExport()
    {
        $data = [
            'app' => [
                'name' => systemDetails()['name'],
                'version' => systemDetails()['build_version'],
                'laravel' => app()->version(),
                'env' => config('app.env'),
                'debug' => config('app.debug'),
                'url' => config('app.url'),
                'timezone' => config('app.timezone'),
            ],
            'php' => [
                'version' => phpversion(),
                'memory_limit' => ini_get('memory_limit'),
                'upload_max' => ini_get('upload_max_filesize'),
                'post_max' => ini_get('post_max_size'),
            ],
            'database' => [
                'driver' => config('database.default'),
                'connected' => false,
            ],
            'directories' => [
                'storage_writable' => is_writable(storage_path()),
                'bootstrap_cache_writable' => is_writable(base_path('bootstrap/cache')),
            ],
            'exported_at' => now()->toIso8601String(),
        ];
        try {
            $data['database']['connected'] = (bool) DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $data['database']['error'] = $e->getMessage();
        }
        return response()->json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="system-info-' . date('Y-m-d-His') . '.json"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    protected function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function optimize()
    {
        $pageTitle = 'Clear System Cache';

        // Cache directory size (approximate)
        $cacheSize = 'N/A';
        $viewSize = 'N/A';
        try {
            $cachePath = storage_path('framework/cache');
            $viewPath = storage_path('framework/views');
            if (File::isDirectory($cachePath)) {
                $cacheSize = $this->formatBytes($this->dirSize($cachePath));
            }
            if (File::isDirectory($viewPath)) {
                $viewSize = $this->formatBytes($this->dirSize($viewPath));
            }
        } catch (\Throwable $e) {
            // Ignore
        }

        $retentionDays = (int) config('upload.trashed_retention_days', 15);

        return view('admin.system.optimize', compact('pageTitle', 'cacheSize', 'viewSize', 'retentionDays'));
    }

    protected function dirSize($path)
    {
        $size = 0;
        try {
            if (!File::isDirectory($path)) return 0;
            $files = File::allFiles($path);
            foreach ($files as $file) {
                $size += $file->getSize();
            }
        } catch (\Throwable $e) {
            return 0;
        }
        return $size;
    }

    /**
     * Safe cache clear: only application + view cache.
     * Re-warms GeneralSetting so logo, favicon and all features keep working.
     */
    public function optimizeClear(){
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Cache::put('asset_version', (string) time()); // Invalidate browser cache
            $this->warmGeneralSettingCache();
            $notify[] = ['success', 'Cache cleared successfully. Logo, favicon and all settings will work correctly.'];
        } catch (\Throwable $e) {
            $notify[] = ['error', 'Cache clear failed: ' . $e->getMessage()];
        }
        return back()->withNotify($notify);
    }

    /**
     * Full system clear (config, route, compiled etc). Use only when needed.
     * Re-warms GeneralSetting after clear to avoid broken panel.
     */
    public function optimizeClearFull(){
        try {
            Artisan::call('optimize:clear');
            Cache::put('asset_version', (string) time()); // Invalidate browser cache
            $this->warmGeneralSettingCache();
            $notify[] = ['success', 'Full cache cleared. Settings re-loaded. If any issue, refresh the page.'];
        } catch (\Throwable $e) {
            $notify[] = ['error', 'Full clear failed: ' . $e->getMessage()];
        }
        return back()->withNotify($notify);
    }

    public function optimizeClearConfig()
    {
        try {
            Artisan::call('config:clear');
            $this->warmGeneralSettingCache();
            $notify[] = ['success', __('Config cache cleared.')];
        } catch (\Throwable $e) {
            $notify[] = ['error', __('Config clear failed: ') . $e->getMessage()];
        }
        return back()->withNotify($notify);
    }

    public function optimizeClearRoute()
    {
        try {
            Artisan::call('route:clear');
            $notify[] = ['success', __('Route cache cleared.')];
        } catch (\Throwable $e) {
            $notify[] = ['error', __('Route clear failed: ') . $e->getMessage()];
        }
        return back()->withNotify($notify);
    }

    public function optimizeClearView()
    {
        try {
            Artisan::call('view:clear');
            $notify[] = ['success', __('Compiled views cleared.')];
        } catch (\Throwable $e) {
            $notify[] = ['error', __('View clear failed: ') . $e->getMessage()];
        }
        return back()->withNotify($notify);
    }

    /**
     * Optimize for production (config + route + view cache).
     */
    public function optimizeRun()
    {
        try {
            Artisan::call('optimize');
            $this->warmGeneralSettingCache();
            $notify[] = ['success', __('Application optimized for production. Config, routes and views are cached.')];
        } catch (\Throwable $e) {
            $notify[] = ['error', __('Optimize failed: ') . $e->getMessage()];
        }
        return back()->withNotify($notify);
    }

    /**
     * Advanced cleanup: permanently delete old trashed uploads (delayed delete)
     * and clear temp/cache files. Safe to run when disk usage grows.
     */
    public function optimizeCleanup()
    {
        try {
            Artisan::call('staylbd:cleanup-trashed-files', ['--temp' => true]);
            $output = trim(Artisan::output());
            $message = $output !== '' ? $output : __('Advanced cleanup completed.');
            $notify[] = ['success', $message];
        } catch (\Throwable $e) {
            $notify[] = ['error', __('Cleanup failed: ') . $e->getMessage()];
        }
        return back()->withNotify($notify);
    }

    /**
     * Update delayed-delete retention days (0 = manual only).
     */
    public function optimizeUpdateRetention(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'file_retention_days' => 'required|in:0,7,15,30,60',
        ]);

        $days = (int) $request->input('file_retention_days');

        try {
            $this->setEnvValue('FILE_RETENTION_DAYS', (string) $days);
            Artisan::call('config:clear');
            $notify[] = ['success', __('File retention updated to :days days (0 = manual only).', ['days' => $days])];
        } catch (\Throwable $e) {
            $notify[] = ['error', __('Could not update retention setting: ') . $e->getMessage()];
        }

        return back()->withNotify($notify);
    }

    /**
     * Simple .env editor for single key.
     */
    protected function setEnvValue(string $key, string $value): void
    {
        $envPath = base_path('.env');
        if (!is_file($envPath) || !is_readable($envPath)) {
            return;
        }
        $content = file_get_contents($envPath);
        $pattern = '/^' . preg_quote($key, '/') . '=.*/m';
        $line = $key . '=' . $value;
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $line, $content);
        } else {
            $content .= PHP_EOL . $line . PHP_EOL;
        }
        @file_put_contents($envPath, $content);
    }

    protected function warmGeneralSettingCache(){
        $general = GeneralSetting::first();
        if ($general) {
            Cache::put('GeneralSetting', $general);
        }
    }

    /**
     * Server Information - All data auto-detected from environment.
     * No manual editing needed when deploying - update .env only.
     */
    public function systemServerInfo()
    {
        $pageTitle = 'Server Information';
        $srv = $_SERVER;

        // Core Server Info
        $core = [
            'php_version' => phpversion(),
            'server_software' => $srv['SERVER_SOFTWARE'] ?? 'N/A',
            'server_addr' => $srv['SERVER_ADDR'] ?? 'N/A',
            'server_port' => $srv['SERVER_PORT'] ?? 'N/A',
            'server_protocol' => $srv['SERVER_PROTOCOL'] ?? 'N/A',
            'http_host' => $srv['HTTP_HOST'] ?? 'N/A',
            'server_name' => $srv['SERVER_NAME'] ?? 'N/A',
            'document_root' => $srv['DOCUMENT_ROOT'] ?? 'N/A',
            'server_admin' => $srv['SERVER_ADMIN'] ?? 'N/A',
            'script_filename' => $srv['SCRIPT_FILENAME'] ?? 'N/A',
            'gateway_interface' => $srv['GATEWAY_INTERFACE'] ?? 'N/A',
        ];

        // Request Info
        $request = [
            'request_method' => $srv['REQUEST_METHOD'] ?? 'N/A',
            'request_uri' => $srv['REQUEST_URI'] ?? 'N/A',
            'query_string' => $srv['QUERY_STRING'] ?? '-',
            'remote_addr' => $srv['REMOTE_ADDR'] ?? 'N/A',
            'remote_port' => $srv['REMOTE_PORT'] ?? 'N/A',
            'http_user_agent' => $srv['HTTP_USER_AGENT'] ?? 'N/A',
            'http_accept' => $srv['HTTP_ACCEPT'] ?? 'N/A',
            'http_accept_language' => $srv['HTTP_ACCEPT_LANGUAGE'] ?? 'N/A',
            'http_accept_encoding' => $srv['HTTP_ACCEPT_ENCODING'] ?? 'N/A',
            'http_referer' => $srv['HTTP_REFERER'] ?? '-',
            'https' => (!empty($srv['HTTPS']) && $srv['HTTPS'] !== 'off') ? __('Yes') : __('No'),
        ];

        // PHP Configuration (Runtime)
        $phpConfig = [
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_execution_time' => ini_get('max_execution_time') . ' ' . __('seconds'),
            'max_input_time' => ini_get('max_input_time') . ' ' . __('seconds'),
            'max_input_vars' => ini_get('max_input_vars'),
            'default_socket_timeout' => ini_get('default_socket_timeout') . ' ' . __('seconds'),
            'date_timezone' => date_default_timezone_get(),
            'safe_mode' => @ini_get('safe_mode') ? __('On') : __('Off'),
            'display_errors' => ini_get('display_errors') ? __('On') : __('Off'),
            'short_open_tag' => ini_get('short_open_tag') ? __('On') : __('Off'),
            'opcache_enabled' => (function_exists('opcache_get_status') && @opcache_get_status(false)) ? __('Yes') : __('No'),
        ];

        // Disk & Memory
        $diskFree = function_exists('disk_free_space') ? @disk_free_space(base_path()) : null;
        $diskTotal = function_exists('disk_total_space') ? @disk_total_space(base_path()) : null;
        $resources = [
            'disk_free' => $diskFree !== null ? $this->formatBytes($diskFree) : 'N/A',
            'disk_total' => $diskTotal !== null ? $this->formatBytes($diskTotal) : 'N/A',
            'disk_used' => ($diskFree !== null && $diskTotal !== null) ? $this->formatBytes($diskTotal - $diskFree) : 'N/A',
            'server_os' => PHP_OS_FAMILY ?? PHP_OS,
            'server_time' => date('Y-m-d H:i:s T'),
        ];

        // Loaded modules (if Apache)
        $loadedModules = [];
        if (function_exists('apache_get_modules')) {
            $loadedModules = apache_get_modules();
        }

        // Security Headers (popular ones)
        $securityHeaders = [];
        $headerKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED_PROTO', 'HTTP_X_REAL_IP', 'HTTP_X_CSRF_TOKEN', 'HTTP_AUTHORIZATION'];
        foreach ($headerKeys as $k) {
            if (!empty($srv[$k])) {
                $securityHeaders[$k] = strlen($srv[$k]) > 80 ? substr($srv[$k], 0, 80) . '...' : $srv[$k];
            }
        }

        // Laravel/App Context
        $appContext = [
            'app_url' => config('app.url'),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug') ? __('On') : __('Off'),
            'laravel_version' => app()->version(),
        ];

        return view('admin.system.server', compact(
            'pageTitle', 'core', 'request', 'phpConfig', 'resources', 'loadedModules', 'securityHeaders', 'appContext'
        ));
    }
}
