<?php

namespace App\Console\Commands;

use App\Models\SearchLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SearchCleanupCommand extends Command
{
    protected $signature = 'search:cleanup {--temp-only : Only clean temp/search folder} {--days=30 : Delete search_log images older than N days }';

    protected $description = 'Clean temp search files and optionally old search log images (auto-optimize)';

    public function handle(): int
    {
        $this->info('Search cleanup...');

        $tempPath = Storage::disk('public')->path('temp/search');
        if (File::isDirectory($tempPath)) {
            $count = 0;
            foreach (File::files($tempPath) as $file) {
                if (filemtime($file->getPathname()) < time() - 86400) {
                    @unlink($file->getPathname());
                    $count++;
                }
            }
            $this->line('  - temp/search: removed ' . $count . ' old file(s)');
        }

        if (!$this->option('temp-only')) {
            $days = (int) $this->option('days');
            $cutoff = now()->subDays($days);
            $logs = SearchLog::whereNotNull('image_path')->where('created_at', '<', $cutoff)->get();
            $disk = Storage::disk('public');
            $removed = 0;
            foreach ($logs as $log) {
                if ($log->image_path && $disk->exists($log->image_path)) {
                    $disk->delete($log->image_path);
                    $removed++;
                }
                $log->update(['image_path' => null]);
            }
            if ($removed > 0) {
                $this->line('  - search_log images older than ' . $days . ' days: ' . $removed . ' removed');
            }
        }

        $this->info('Search cleanup done.');
        return 0;
    }
}
