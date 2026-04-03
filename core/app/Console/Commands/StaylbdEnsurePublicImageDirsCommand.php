<?php

namespace App\Console\Commands;

use App\Constants\FileInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Ensure all image directories from FileInfo exist under public/assets
 * so uploaded images are stored in the right place and Laravel can serve them.
 */
class StaylbdEnsurePublicImageDirsCommand extends Command
{
    protected $signature = 'staylbd:ensure-public-image-dirs';
    protected $description = 'Create public/assets image directories so images load correctly';

    public function handle(): int
    {
        $fileInfo = new FileInfo();
        $data = $fileInfo->fileInfo();
        $created = 0;

        foreach ($data as $key => $config) {
            if (empty($config['path']) || !is_string($config['path'])) {
                continue;
            }
            $rel = ltrim(str_replace('\\', '/', $config['path']), '/');
            if (str_contains($rel, '..')) {
                continue;
            }
            if (preg_match('/\.(png|jpg|jpeg|gif|webp|svg)$/i', $rel)) {
                $rel = dirname($rel);
            }
            if ($rel === '' || $rel === '.') {
                continue;
            }
            $path = public_path($rel);
            if (!is_dir($path)) {
                if (File::makeDirectory($path, 0755, true)) {
                    $this->line('Created: public/' . $rel);
                    $created++;
                }
            }
        }

        if ($created > 0) {
            $this->info("Created {$created} directory(ies). Images should be stored under public/assets/images.");
        } else {
            $this->info('All public image directories already exist.');
        }

        return self::SUCCESS;
    }
}
