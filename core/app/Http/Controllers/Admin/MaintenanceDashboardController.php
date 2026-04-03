<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CacheClearLog;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MaintenanceDashboardController extends Controller
{
    public function index()
    {
        $pageTitle = __('Maintenance Dashboard');

        $diskUsage = $this->getDiskUsage();
        $dbHealth = $this->getDatabaseHealth();
        $cacheStatus = $this->getCacheStatus();
        $mediaUploads = $this->getMediaUploadsStatus();

        return view('admin.maintenance.dashboard', compact(
            'pageTitle',
            'diskUsage',
            'dbHealth',
            'cacheStatus',
            'mediaUploads'
        ));
    }

    protected function formatBytes($bytes)
    {
        if ($bytes === null || $bytes < 0) {
            return 'N/A';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    protected function dirSize($path)
    {
        $size = 0;
        try {
            if (!File::isDirectory($path)) {
                return 0;
            }
            foreach (File::allFiles($path) as $file) {
                $size += $file->getSize();
            }
        } catch (\Throwable $e) {
            return 0;
        }
        return $size;
    }

    protected function getDiskUsage(): array
    {
        $storagePath = storage_path();
        $publicPath = public_path();
        $logPath = storage_path('logs');
        $tempPath = storage_path('framework/cache');

        $storageSize = $this->dirSize($storagePath);
        $publicSize = $this->dirSize($publicPath);
        $logSize = File::isDirectory($logPath) ? $this->dirSize($logPath) : 0;
        $tempSize = File::isDirectory($tempPath) ? $this->dirSize($tempPath) : 0;

        $diskFree = function_exists('disk_free_space') ? @disk_free_space(base_path()) : null;
        $diskTotal = function_exists('disk_total_space') ? @disk_total_space(base_path()) : null;

        return [
            'storage_size'   => $this->formatBytes($storageSize),
            'public_size'    => $this->formatBytes($publicSize),
            'log_size'       => $this->formatBytes($logSize),
            'temp_size'      => $this->formatBytes($tempSize),
            'disk_free'      => $diskFree !== null ? $this->formatBytes($diskFree) : 'N/A',
            'disk_total'     => $diskTotal !== null ? $this->formatBytes($diskTotal) : 'N/A',
            'usage_percent'  => ($diskTotal && $diskTotal > 0) ? round((($diskTotal - $diskFree) / $diskTotal) * 100, 1) : null,
        ];
    }

    protected function getDatabaseHealth(): array
    {
        $dbDriver = config('database.default');
        $dbName = config('database.connections.' . $dbDriver . '.database') ?? null;

        $result = [
            'driver'        => $dbDriver,
            'name'          => $dbName ?? 'N/A',
            'connected'     => false,
            'tables'        => [],
            'total_size'    => 'N/A',
            'row_counts'    => [],
        ];

        try {
            DB::connection()->getPdo();
            $result['connected'] = true;
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }

        if ($dbDriver === 'mysql' && $dbName) {
            try {
                $tables = DB::select("
                    SELECT TABLE_NAME, TABLE_ROWS, ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS size_mb
                    FROM information_schema.TABLES
                    WHERE TABLE_SCHEMA = ?
                    AND TABLE_NAME IN ('products', 'orders', 'order_details', 'users', 'deposits', 'admins', 'support_tickets')
                    ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
                ", [$dbName]);
                foreach ($tables as $t) {
                    $result['tables'][] = [
                        'name' => $t->TABLE_NAME,
                        'rows' => $t->TABLE_ROWS ?? 0,
                        'size' => ($t->size_mb ?? 0) . ' MB',
                    ];
                }

                $total = DB::selectOne("
                    SELECT ROUND(SUM(DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024 / 1024, 2) as total_gb
                    FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?
                ", [$dbName]);
                $result['total_size'] = ($total->total_gb ?? 0) . ' GB';
            } catch (\Throwable $e) {
                $result['error'] = $e->getMessage();
            }
        }

        return $result;
    }

    protected function getCacheStatus(): array
    {
        $cachePath = storage_path('framework/cache');
        $viewPath = storage_path('framework/views');

        $cacheSize = File::isDirectory($cachePath) ? $this->dirSize($cachePath) : 0;
        $viewSize = File::isDirectory($viewPath) ? $this->dirSize($viewPath) : 0;

        $lastCleared = null;
        try {
            $log = CacheClearLog::where('success', true)->latest('created_at')->first();
            if ($log) {
                $lastCleared = $log->created_at->diffForHumans();
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return [
            'cache_size'     => $this->formatBytes($cacheSize),
            'view_size'      => $this->formatBytes($viewSize),
            'last_cleared'   => $lastCleared ?? __('Never'),
        ];
    }

    protected function getMediaUploadsStatus(): array
    {
        $fileInfo = new \App\Constants\FileInfo();
        $info = $fileInfo->fileInfo();

        $paths = [
            'product'        => 'assets/images/product',
            'productGallery' => 'assets/images/product/gallery',
            'productVideo'   => 'assets/images/product/video',
            'productFile'    => 'assets/images/product/file',
            'category'       => 'assets/images/category',
            'brand'          => 'assets/images/brand',
            'userProfile'    => 'assets/images/user/profile',
            'ticket'         => 'assets/support',
        ];

        $total = 0;
        $breakdown = [];

        foreach ($paths as $key => $relPath) {
            $fullPath = public_path($relPath);
            $size = File::isDirectory($fullPath) ? $this->dirSize($fullPath) : 0;
            $total += $size;
            $breakdown[$key] = $this->formatBytes($size);
        }

        return [
            'total_size' => $this->formatBytes($total),
            'breakdown'  => $breakdown,
        ];
    }

    /**
     * Clean Temp & Cache (Phase 2) - One-click action
     */
    public function cleanTempCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');

            $frameworkPath = storage_path('framework');
            $cachePath = $frameworkPath . '/cache';
            $viewPath = $frameworkPath . '/views';

            if (File::isDirectory($cachePath)) {
                $this->deleteDirectoryContents($cachePath);
            }
            if (File::isDirectory($viewPath)) {
                $this->deleteDirectoryContents($viewPath);
            }

            $this->warmGeneralSettingCache();

            $admin = auth()->guard('admin')->user();
            if ($admin) {
                CacheClearLog::create([
                    'admin_id'   => $admin->id,
                    'admin_name' => $admin->name ?? null,
                    'action'     => 'maintenance_clean_temp_cache',
                    'ip'         => request()->ip(),
                    'user_agent' => substr(request()->userAgent() ?? '', 0, 500),
                    'success'    => true,
                ]);
            }

            $notify[] = ['success', __('Temp & cache cleared successfully.')];
        } catch (\Throwable $e) {
            $notify[] = ['error', __('Cleanup failed: ') . $e->getMessage()];
        }

        return back()->withNotify($notify);
    }

    protected function deleteDirectoryContents(string $path): void
    {
        if (!File::isDirectory($path)) {
            return;
        }
        foreach (File::files($path) as $file) {
            @unlink($file->getPathname());
        }
        foreach (File::directories($path) as $dir) {
            File::deleteDirectory($dir);
        }
    }

    protected function warmGeneralSettingCache(): void
    {
        $general = GeneralSetting::first();
        if ($general) {
            Cache::put('GeneralSetting', $general);
        }
    }
}
