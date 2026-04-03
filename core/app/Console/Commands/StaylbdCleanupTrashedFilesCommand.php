<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Clean trashed uploads (delayed-delete) older than FILE_RETENTION_DAYS
 * and clear temp cache/compiled views. Run daily via cron: php artisan schedule:run
 */
class StaylbdCleanupTrashedFilesCommand extends Command
{
    protected $signature = 'staylbd:cleanup-trashed-files
                            {--days= : Override retention days (default: config FILE_RETENTION_DAYS)}
                            {--temp : Also clear storage/framework cache and compiled views}
                            {--dry-run : List what would be deleted without deleting}';
    protected $description = 'Permanently delete trashed files older than retention and optionally clear temp files';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('upload.trashed_retention_days', 15));
        $dryRun = $this->option('dry-run');
        $trashPath = storage_path('app/' . (config('upload.trashed_path', 'trashed_uploads')));

        if ($days <= 0) {
            $this->info('Auto delete is disabled (retention days <= 0). Nothing to delete.');
            if ($this->option('temp')) {
                $this->cleanTemp($dryRun);
            }
            return self::SUCCESS;
        }

        $cutoff = now()->subDays($days)->format('Y-m-d');
        $deleted = 0;

        if (is_dir($trashPath)) {
            $dirs = @scandir($trashPath) ?: [];
            foreach ($dirs as $dir) {
                if ($dir === '.' || $dir === '..' || !is_dir($trashPath . DIRECTORY_SEPARATOR . $dir)) {
                    continue;
                }
                if ($dir < $cutoff) {
                    $full = $trashPath . DIRECTORY_SEPARATOR . $dir;
                    $files = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($full, \RecursiveDirectoryIterator::SKIP_DOTS),
                        \RecursiveIteratorIterator::CHILD_FIRST
                    );
                    foreach ($files as $item) {
                        if ($dryRun) {
                            $deleted++;
                            continue;
                        }
                        if ($item->isDir()) {
                            @rmdir($item->getPathname());
                        } else {
                            @unlink($item->getPathname());
                            $deleted++;
                        }
                    }
                    if (!$dryRun) {
                        @rmdir($full);
                    }
                }
            }
        }

        if ($dryRun) {
            $this->info("[Dry run] Would permanently delete {$deleted} trashed files (folders older than {$days} days).");
        } else {
            $this->info("Permanently deleted {$deleted} trashed files older than {$days} days.");
        }

        if ($this->option('temp')) {
            $this->cleanTemp($dryRun);
        }

        return self::SUCCESS;
    }

    protected function cleanTemp(bool $dryRun): void
    {
        $cache = storage_path('framework/cache/data');
        $views = storage_path('framework/views');
        $count = 0;
        if (is_dir($cache)) {
            $items = @scandir($cache) ?: [];
            foreach ($items as $f) {
                if ($f !== '.' && $f !== '..') {
                    $path = $cache . DIRECTORY_SEPARATOR . $f;
                    if (!$dryRun) {
                        File::isFile($path) ? @unlink($path) : File::deleteDirectory($path);
                    }
                    $count++;
                }
            }
        }
        if (is_dir($views)) {
            $items = @scandir($views) ?: [];
            foreach ($items as $f) {
                if ($f !== '.' && $f !== '..' && substr($f, -4) === '.php') {
                    if (!$dryRun) {
                        @unlink($views . DIRECTORY_SEPARATOR . $f);
                    }
                    $count++;
                }
            }
        }
        $this->info(($dryRun ? '[Dry run] Would clear ' : 'Cleared ') . $count . ' temp/cache items.');
    }
}
