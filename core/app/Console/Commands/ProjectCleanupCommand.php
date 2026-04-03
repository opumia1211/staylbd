<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Clean unnecessary files - run: php artisan project:clean
 * Removes: cache, compiled views, logs (optional), debugbar cache.
 * Keeps: .gitignore, session files (optional).
 */
class ProjectCleanupCommand extends Command
{
    protected $signature = 'project:clean {--logs : Also truncate log files} {--force : No confirmation}';
    protected $description = 'Clean cache, views, logs - keeps project lean and fast';

    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('Clear cache, views, and optionally logs?')) {
            return self::SUCCESS;
        }

        $this->info('Clearing application cache...');
        $this->call('cache:clear');

        $this->info('Clearing config cache...');
        try {
            $this->call('config:clear');
        } catch (\Throwable $e) {
            // Ignore
        }

        $this->info('Clearing view cache...');
        try {
            $this->call('view:clear');
        } catch (\Throwable $e) {
            // Ignore
        }

        $this->info('Clearing route cache...');
        try {
            $this->call('route:clear');
        } catch (\Throwable $e) {
            // Ignore
        }

        $cachePath = storage_path('framework/cache/data');
        if (is_dir($cachePath)) {
            $count = $this->clearDir($cachePath);
            $this->info("Cleared {$count} cache files.");
        }

        $viewsPath = storage_path('framework/views');
        if (is_dir($viewsPath)) {
            $count = $this->clearCompiledViews($viewsPath);
            $this->info("Cleared {$count} compiled views.");
        }

        if ($this->option('logs')) {
            $logPath = storage_path('logs');
            if (is_dir($logPath)) {
                $count = $this->truncateLogs($logPath);
                $this->info("Truncated {$count} log files.");
            }
        }

        $this->info('Cleanup complete. Project is lean.');
        return self::SUCCESS;
    }

    protected function clearDir(string $path): int
    {
        $count = 0;
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $file) {
            if ($file->isFile() && $file->getFilename() !== '.gitignore') {
                @unlink($file->getPathname());
                $count++;
            }
        }
        return $count;
    }

    protected function clearCompiledViews(string $path): int
    {
        $count = 0;
        foreach (glob($path . '/*.php') ?: [] as $file) {
            if (is_file($file)) {
                @unlink($file);
                $count++;
            }
        }
        return $count;
    }

    protected function truncateLogs(string $path): int
    {
        $count = 0;
        foreach (glob($path . '/*.log') ?: [] as $file) {
            if (is_file($file)) {
                file_put_contents($file, '');
                $count++;
            }
        }
        return $count;
    }
}
