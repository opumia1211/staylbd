<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MaintenanceCleanLogsCommand extends Command
{
    protected $signature = 'maintenance:clean-logs {--keep-days=7 : Keep last N days of log files}';

    protected $description = 'Rotate and clean old Laravel log files';

    public function handle(): int
    {
        $keepDays = (int) $this->option('keep-days');
        $logPath = storage_path('logs');
        if (!File::isDirectory($logPath)) {
            $this->info('Logs directory not found.');
            return 0;
        }

        $this->info('Cleaning logs (keeping last ' . $keepDays . ' days)...');
        $cutoff = now()->subDays($keepDays)->timestamp;
        $removed = 0;

        foreach (File::files($logPath) as $file) {
            if ($file->getExtension() !== 'log') {
                continue;
            }
            if ($file->getMTime() < $cutoff) {
                @unlink($file->getPathname());
                $removed++;
                $this->line('  - Removed: ' . $file->getFilename());
            }
        }

        $this->info('Log cleanup completed. Removed ' . $removed . ' file(s).');
        return 0;
    }
}
