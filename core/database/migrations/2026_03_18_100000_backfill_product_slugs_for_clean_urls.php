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

        Product::query()
            ->where(function ($q) {
                $q->whereNull('slug')->orWhere('slug', '');
            })
            ->orderBy('id')
            ->chunkById(200, function ($products) {
                foreach ($products as $product) {
                    $product->slug = Product::buildShortSlugForProduct($product);
                    $product->saveQuietly();
                }
            });
    }

    public function down(): void
    {
        // Intentionally empty — slugs are required for clean URLs
    }
};
