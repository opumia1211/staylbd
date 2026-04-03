<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Move uploaded images from project root (staylbd/assets) or Laravel root (core/assets)
 * into public (core/public/assets) so they are served correctly. Run once if images
 * were uploaded before the path fix or are in the wrong folder.
 */
class StaylbdMoveUploadsToPublicCommand extends Command
{
    protected $signature = 'staylbd:move-uploads-to-public {--dry-run : List what would be moved without moving}';
    protected $description = 'Move assets/images into core/public/assets so uploaded images load on site';

    public function handle(): int
    {
        $sources = array_filter([
            base_path('assets'),
            base_path('../assets'),
        ], 'is_dir');
        $to = public_path('assets');

        if (empty($sources)) {
            $this->info('No assets folder found in core/ or project root. Uploads go to public by default now.');
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $total = 0;
            foreach ($sources as $from) {
                $total += $this->countFiles($from);
            }
            $this->info('[Dry run] Would move ' . $total . ' files from ' . implode(', ', $sources) . ' to ' . $to);
            return self::SUCCESS;
        }

        if (!is_dir($to)) {
            if (!File::makeDirectory($to, 0755, true)) {
                $this->error('Could not create ' . $to);
                return self::FAILURE;
            }
        }

        $moved = 0;
        foreach ($sources as $from) {
            $moved += $this->moveDir($from, $to);
        }
        $this->info("Moved {$moved} files to public/assets. Images should now load.");
        return self::SUCCESS;
    }

    private function moveDir(string $from, string $to): int
    {
        $moved = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($from, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            $rel = substr($item->getPathname(), strlen($from) + 1);
            $rel = str_replace('\\', '/', $rel);
            $target = $to . '/' . $rel;
            if ($item->isDir()) {
                if (!is_dir($target)) {
                    @mkdir($target, 0755, true);
                }
            } else {
                $targetDir = dirname($target);
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0755, true);
                }
                if (!file_exists($target)) {
                    if (@copy($item->getPathname(), $target)) {
                        $moved++;
                    }
                }
            }
        }
        return $moved;
    }

    private function countFiles(string $dir): int
    {
        $n = 0;
        if (!is_dir($dir)) {
            return 0;
        }
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $item) {
            if ($item->isFile()) {
                $n++;
            }
        }
        return $n;
    }
}
