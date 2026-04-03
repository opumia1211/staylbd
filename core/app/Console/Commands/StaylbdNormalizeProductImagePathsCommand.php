<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

/**
 * Normalize product image column: store filename only (e.g. "abc123.jpg").
 * Removes absolute Windows paths (C:\...) or full paths so images render correctly.
 */
class StaylbdNormalizeProductImagePathsCommand extends Command
{
    protected $signature = 'staylbd:normalize-product-image-paths {--dry-run : Show what would be updated without changing DB}';
    protected $description = 'Set product image column to filename only (fix 404 / path issues)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $products = Product::whereNotNull('image')->where('image', '!=', '')->get();
        $updated = 0;

        foreach ($products as $product) {
            $image = $product->image;
            $normalized = $this->normalize($image);
            if ($normalized === $image) {
                continue;
            }
            if ($dryRun) {
                $this->line("Would update product id {$product->id}: \"{$image}\" → \"{$normalized}\"");
            } else {
                $product->update(['image' => $normalized]);
                $this->line("Updated product id {$product->id}: image → \"{$normalized}\"");
            }
            $updated++;
        }

        if ($dryRun && $updated > 0) {
            $this->info("[Dry run] Would normalize {$updated} product(s). Run without --dry-run to apply.");
        } elseif ($updated > 0) {
            $this->info("Normalized {$updated} product image path(s).");
        } else {
            $this->info('No product image paths needed normalization.');
        }

        return self::SUCCESS;
    }

    private function normalize(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return $value;
        }
        $value = str_replace('\\', '/', $value);
        if (preg_match('#^[A-Za-z]:/#', $value) || str_contains($value, ':/') || str_contains($value, '\\')) {
            return basename($value);
        }
        if (preg_match('#^assets/images/product/#', $value)) {
            return basename($value);
        }
        if (str_contains($value, '/')) {
            return basename($value);
        }
        return $value;
    }
}
