<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Move old activity logs to archive; delete very old archive.
 * Schedule: daily. E.g. app/Console/Kernel.php: schedule->command('activity:archive')->daily();
 */
class ActivityArchiveCommand extends Command
{
    protected $signature = 'activity:archive
                            {--days= : Move logs older than this (default from config activity.archive_after_days)}
                            {--delete-days= : Delete archive rows older than this (default activity.delete_archive_after_days)}
                            {--dry-run : Only show what would be done}';

    protected $description = 'Archive old user_activity_logs to user_activity_logs_archive and optionally delete very old archive rows';

    public function handle(): int
    {
        $archiveAfterDays = (int) ($this->option('days') ?: config('activity.archive_after_days', 30));
        $deleteArchiveAfterDays = (int) ($this->option('delete-days') ?: config('activity.delete_archive_after_days', 365));
        $dryRun = $this->option('dry-run');

        $cutoff = now()->subDays($archiveAfterDays)->format('Y-m-d 00:00:00');
        $deleteCutoff = now()->subDays($deleteArchiveAfterDays)->format('Y-m-d 00:00:00');

        if (!\Illuminate\Support\Facades\Schema::hasTable('user_activity_logs_archive')) {
            $this->warn('Table user_activity_logs_archive does not exist. Run migrations.');
            return self::FAILURE;
        }

        $toArchive = DB::table('user_activity_logs')->where('created_at', '<', $cutoff)->count();
        $toDelete = DB::table('user_activity_logs_archive')->where('created_at', '<', $deleteCutoff)->count();

        $this->info("Logs to archive (older than {$archiveAfterDays} days): {$toArchive}");
        $this->info("Archive rows to delete (older than {$deleteArchiveAfterDays} days): {$toDelete}");

        if ($dryRun) {
            $this->info('Dry run — no changes made.');
            return self::SUCCESS;
        }

        if ($toArchive > 0) {
            $chunk = 5000;
            $archived = 0;
            DB::table('user_activity_logs')->where('created_at', '<', $cutoff)->orderBy('id')->chunk($chunk, function ($rows) use (&$archived) {
                $insert = [];
                foreach ($rows as $row) {
                    $insert[] = [
                        'id' => $row->id,
                        'user_id' => $row->user_id,
                        'session_id' => $row->session_id,
                        'action_type' => $row->action_type,
                        'description' => $row->description,
                        'model_type' => $row->model_type,
                        'model_id' => $row->model_id,
                        'ip_address' => $row->ip_address,
                        'device' => $row->device,
                        'browser' => $row->browser,
                        'os' => $row->os,
                        'country' => $row->country,
                        'city' => $row->city,
                        'latitude' => $row->latitude,
                        'longitude' => $row->longitude,
                        'url' => $row->url,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ];
                }
                DB::table('user_activity_logs_archive')->insert($insert);
                $ids = array_column($insert, 'id');
                DB::table('user_activity_logs')->whereIn('id', $ids)->delete();
                $archived += count($insert);
            });
            $this->info("Archived {$archived} rows.");
        }

        if ($toDelete > 0) {
            $deleted = DB::table('user_activity_logs_archive')->where('created_at', '<', $deleteCutoff)->delete();
            $this->info("Deleted {$deleted} old archive rows.");
        }

        return self::SUCCESS;
    }
}
