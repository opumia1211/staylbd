<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically create a backup of the main database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-d_H-i-s') . ".sql";
        $directory = storage_path('app/backups');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filePath = $directory . DIRECTORY_SEPARATOR . $filename;

        // Retrieve DB configuration
        $host = env('DB_HOST', '127.0.0.1');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');
        $database = env('DB_DATABASE', 'staylbd_wintersm');
        $port = env('DB_PORT', '3306');

        // Detect mysqldump path
        $mysqldump = env('MYSQLDUMP_PATH') ?: 'mysqldump';
        if (PHP_OS_FAMILY === 'Windows' && $mysqldump === 'mysqldump') {
            $candidates = ['C:\\xampp\\mysql\\bin\\mysqldump.exe', 'C:\\wamp64\\bin\\mysql\\mysql8\\bin\\mysqldump.exe'];
            foreach ($candidates as $c) {
                if (file_exists($c)) {
                    $mysqldump = $c;
                    break;
                }
            }
        }

        if (empty($password)) {
            $command = "{$mysqldump} --user={$username} --host={$host} --port={$port} {$database} > \"{$filePath}\"";
        } else {
            $command = "{$mysqldump} --user={$username} --password=\"{$password}\" --host={$host} --port={$port} {$database} > \"{$filePath}\"";
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $command = 'cmd /c ' . $command;
        }

        $returnVar = null;
        $output  = null;
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("Database successfully backed up to: {$filePath}");
            
            // Delete backups older than 7 days
            $oldFiles = File::files($directory);
            foreach ($oldFiles as $file) {
                if (Carbon::createFromTimestamp(filemtime($file))->diffInDays(Carbon::now()) > 7) {
                    File::delete($file);
                }
            }
            return 0;
        } else {
            $this->error("Failed to backup the database.");
            return 1;
        }
    }
}
