<?php

namespace App\Console\Commands;

use App\Services\ImageOptimizationService;
use Illuminate\Console\Command;

/**
 * Batch WebP + size clamp for existing product / gallery rasters (new uploads already optimized in FileManager).
 */
class StaylbdOptimizeProductImagesCommand extends Command
{
    protected $signature = 'staylbd:optimize-product-images
                            {--gallery=1 : Include gallery folder (0 to skip)}
                            {--dry : List files only}';

    protected $description = 'Regenerate WebP and enforce max size (see config upload.max_product_webp_bytes) for product images.';

    public function handle(): int
    {
        $svc = app(ImageOptimizationService::class);
        $dry = $this->option('dry');
        $withGallery = filter_var($this->option('gallery'), FILTER_VALIDATE_BOOLEAN);

        $publicRel = ['assets/images/product'];
        if ($withGallery) {
            $publicRel[] = 'assets/images/product/gallery';
        }

        // public_path() is .../core/public — URLs use assets/images/product/...
        $scanSets = [[public_path(), $publicRel]];

        // Repo-root assets/ mirrors the same tree as public/assets/ (images live under assets/images/product).
        $repoAssets = dirname(base_path()) . DIRECTORY_SEPARATOR . 'assets';
        if (is_dir($repoAssets)) {
            $legacyRel = ['images/product'];
            if ($withGallery) {
                $legacyRel[] = 'images/product/gallery';
            }
            $scanSets[] = [$repoAssets, $legacyRel];
        }

        $count = 0;
        foreach ($scanSets as [$root, $relativeDirs]) {
            foreach ($relativeDirs as $rel) {
                $dir = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($rel, '/\\'));
                if (!is_dir($dir)) {
                    $this->warn('Skip missing directory: ' . $dir);
                    continue;
                }
                foreach (glob($dir . DIRECTORY_SEPARATOR . '*') ?: [] as $full) {
                    if (!is_file($full)) {
                        continue;
                    }
                    $base = basename($full);
                    if (str_starts_with($base, 'thumb_')) {
                        continue;
                    }
                    $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION) ?: '');
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                        continue;
                    }
                    $count++;
                    if ($dry) {
                        $this->line($full);
                        continue;
                    }
                    $svc->optimizeProductImage($full, ImageOptimizationService::QUALITY_HIGH);
                }
            }
        }

        if ($dry) {
            $this->info("Dry run: {$count} raster file(s) would be processed.");
        } else {
            $this->info("Processed {$count} file(s).");
        }

        return self::SUCCESS;
    }
}
