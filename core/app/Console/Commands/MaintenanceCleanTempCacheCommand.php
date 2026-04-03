<?php

namespace App\Console\Commands;

use App\Models\GeneralSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class MaintenanceCleanTempCacheCommand extends Command
{
    protected $signature = 'maintenance:clean-temp-cache';

    protected $description = 'Clear temp and cache (framework/cache, framework/views, artisan cache/config/view)';

    public function handle(): int
    {
        $this->info('Cleaning temp and cache...');

        try {
            Artisan::call('cache:clear');
            $this->line('  - Application cache cleared');

            Artisan::call('config:clear');
            $this->line('  - Config cache cleared');

            Artisan::call('view:clear');
            $this->line('  - View cache cleared');

            $frameworkPath = storage_path('framework');
            $cachePath = $frameworkPath . '/cache';
            $viewPath = $frameworkPath . '/views';

            if (File::isDirectory($cachePath)) {
                $this->deleteDirectoryContents($cachePath);
                $this->line('  - framework/cache directory cleared');
            }
            if (File::isDirectory($viewPath)) {
                $this->deleteDirectoryContents($viewPath);
                $this->line('  - framework/views directory cleared');
            }

            $general = GeneralSetting::first();
            if ($general) {
                Cache::put('GeneralSetting', $general);
                $this->line('  - GeneralSetting cache warmed');
            }

            $this->info('Temp & cache cleanup completed.');
            return 0;
        } catch (\Throwable $e) {
            $this->error('Cleanup failed: ' . $e->getMessage());
            return 1;
        }
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
}
