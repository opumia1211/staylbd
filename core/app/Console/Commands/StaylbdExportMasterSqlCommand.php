<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

/**
 * Master SQL export for production: one file (wintersm_tt.sql) only.
 * Run after: php artisan migrate --force && php artisan db:seed --force
 * Then in cPanel import only staylbd_master_final.sql – no patches/migrations needed.
 */
class StaylbdExportMasterSqlCommand extends Command
{
    protected $signature = 'staylbd:export-master-sql
                            {--output= : Full path to output SQL file (default: staylbd_wintersm.sql in database folder)}
                            {--mysqldump= : Path to mysqldump executable}';
    protected $description = 'Export current database to staylbd_wintersm.sql and backup';

    public function handle(): int
    {
        $connection = Config::get('database.default');
        $config = Config::get("database.connections.{$connection}");
        if (empty($config) || ($config['driver'] ?? '') !== 'mysql') {
            $this->error('Only MySQL connection is supported.');
            return self::FAILURE;
        }

        $dbHost = $config['host'] ?? '127.0.0.1';
        $dbPort = $config['port'] ?? '3306';
        $dbName = $config['database'] ?? '';
        $dbUser = $config['username'] ?? '';
        $dbPass = $config['password'] ?? '';

        if (empty($dbName)) {
            $this->error('DB_DATABASE is empty in .env');
            return self::FAILURE;
        }

        $dbDir = base_path('database');
        $outputPath = $this->option('output');
        if (empty($outputPath)) {
            $outputPath = $dbDir . DIRECTORY_SEPARATOR . 'staylbd_wintersm.sql';
        }
        $backupPath = $dbDir . DIRECTORY_SEPARATOR . 'staylbd_wintersm_backup.sql';

        $outputPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $outputPath);

        $mysqldump = $this->option('mysqldump');
        if (empty($mysqldump)) {
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
        }

        $passArg = ($dbPass !== '' && $dbPass !== null) ? '-p' . escapeshellarg($dbPass) : '';
        $cmd = $mysqldump . ' -h' . escapeshellarg($dbHost) . ' -P' . $dbPort . ' -u' . escapeshellarg($dbUser)
            . ' ' . $passArg . ' --single-transaction --routines --triggers --set-charset --default-character-set=utf8mb4 '
            . escapeshellarg($dbName) . ' > ' . escapeshellarg($outputPath);
        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'cmd /c ' . $cmd;
        }

        $this->info('Exporting database to: ' . $outputPath);

        $code = 0;
        $output = [];
        exec($cmd . ' 2>&1', $output, $code);

        if ($code !== 0) {
            $this->error('mysqldump failed: ' . implode("\n", $output));
            return self::FAILURE;
        }

        if (is_file($outputPath)) {
            $this->info('Creating backup: ' . $backupPath);
            copy($outputPath, $backupPath);
        }

        $this->info('Master SQL and Backup exported successfully.');
        return self::SUCCESS;
    }

}
