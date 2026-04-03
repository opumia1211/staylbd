<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'slug')) {
            return;
        }

        Product::query()->orderBy('id')->chunkById(200, function ($products) {
            foreach ($products as $product) {
                $new = Product::buildShortSlugForProduct($product);
                if (trim((string) ($product->slug ?? '')) !== $new) {
                    $product->slug = $new;
                    $product->saveQuietly();
                }
            }
        });
    }

    public function down(): void
    {
        // Irreversible — short slugs are canonical
    }
};
