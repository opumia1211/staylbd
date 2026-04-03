<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Seeder;

class ProductSizeAttributeSeeder extends Seeder
{
    public static function SIZE_VALUES = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '2XL', '3XL'];

    public function run(): void
    {
        $slug = 'size';
        if (ProductAttribute::where('slug', $slug)->exists()) {
            return;
        }
        ProductAttribute::create([
            'name' => 'Size',
            'slug' => $slug,
            'type' => 'select',
            'values' => self::SIZE_VALUES,
            'sort_order' => 0,
            'status' => 1,
        ]);
    }
}
