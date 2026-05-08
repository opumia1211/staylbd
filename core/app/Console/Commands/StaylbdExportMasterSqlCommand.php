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
                            {--output= : Full path to output SQL file (default: staylbd_master_final.sql)}
                            {--mysqldump= : Path to mysqldump executable (e.g. C:\\xampp\\mysql\\bin\\mysqldump.exe)}';
    protected $description = 'Export current database to master staylbd_master_final.sql for cPanel (no migration dependency)';

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

        $outputPath = $this->option('output');
        if (empty($outputPath)) {
            $projectRoot = dirname(base_path());
            $outputPath = $projectRoot . DIRECTORY_SEPARATOR . 'staylbd_master_final.sql';
        }
        $outputPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $outputPath);

        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0755, true)) {
                $this->error("Cannot create directory: {$dir}");
                return self::FAILURE;
            }
        }

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

        if (!is_file($outputPath) || filesize($outputPath) < 100) {
            $this->error('Output file missing or too small.');
            return self::FAILURE;
        }

        $content = file_get_contents($outputPath);
        $header = "-- StayLBD Master DB - Import only this file in cPanel. No migrations/patches required.\n";
        $header .= "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\nUSE `{$dbName}`;\n\n";
        if (strpos($content, 'CREATE DATABASE') === false && strpos($content, 'USE `') === false) {
            $content = $header . $content;
            file_put_contents($outputPath, $content);
        } elseif (strpos($content, $header) !== 0) {
            $content = $header . $content;
            file_put_contents($outputPath, $content);
        }

        $this->info('Master SQL exported. Import staylbd_master_final.sql in cPanel – no migrations or patches required.');
        return self::SUCCESS;
    }
}
