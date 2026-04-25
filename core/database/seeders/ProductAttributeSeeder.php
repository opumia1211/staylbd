<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use Illuminate\Database\Seeder;

/**
 * Advanced Product Attribute Seeder
 * Seeds essential attributes like Size, Color, and Material with optimized logic.
 */
class ProductAttributeSeeder extends Seeder
{
    /**
     * Standard clothing sizes
     */
    public const SIZE_VALUES = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', '5XL'];

    /**
     * Standard footwear sizes (EU)
     */
    public const SHOE_SIZES = ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];

    /**
     * Common fabric materials
     */
    public const MATERIALS = ['Cotton', 'Polyester', 'Silk', 'Wool', 'Linen', 'Denim', 'Leather'];

    /**
     * Common colors
     */
    public const COLORS = ['Black', 'White', 'Red', 'Blue', 'Green', 'Yellow', 'Grey', 'Navy', 'Maroon', 'Beige'];

    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Size',
                'slug' => 'size',
                'type' => ProductAttribute::TYPE_SELECT,
                'values' => self::SIZE_VALUES,
                'sort_order' => 1,
            ],
            [
                'name' => 'Color',
                'slug' => 'color',
                'type' => ProductAttribute::TYPE_COLOR,
                'values' => self::COLORS,
                'sort_order' => 2,
            ],
            [
                'name' => 'Shoe Size',
                'slug' => 'shoe-size',
                'type' => ProductAttribute::TYPE_SELECT,
                'values' => self::SHOE_SIZES,
                'sort_order' => 3,
            ],
            [
                'name' => 'Material',
                'slug' => 'material',
                'type' => ProductAttribute::TYPE_SELECT,
                'values' => self::MATERIALS,
                'sort_order' => 4,
            ],
        ];

        foreach ($attributes as $attr) {
            ProductAttribute::updateOrCreate(
                ['slug' => $attr['slug']],
                array_merge($attr, ['status' => 1])
            );
        }
    }
}
