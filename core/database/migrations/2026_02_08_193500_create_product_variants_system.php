<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations - Product Variants System
     * For multi-category e-commerce with different attributes per category
     */
    public function up()
    {
        // Product Attributes (Size, Color, Material, etc.)
        if (!Schema::hasTable('product_attributes')) {
            Schema::create('product_attributes', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100); // Size, Color, Material, Capacity, etc.
                $table->string('slug', 100)->unique();
                $table->string('type', 50)->default('select'); // select, color, text, number
                $table->text('values')->nullable(); // JSON array of possible values
                $table->integer('sort_order')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();

                $table->index('status');
                $table->index('type');
            });
        }

        // Category-specific attributes (which attributes apply to which category)
        if (!Schema::hasTable('category_attributes')) {
            Schema::create('category_attributes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('category_id');
                $table->unsignedBigInteger('attribute_id');
                $table->tinyInteger('is_required')->default(0);
                $table->tinyInteger('is_variant')->default(1); // If 1, creates product variants
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index('is_variant');
            });

            // Add foreign keys separately to avoid errors
            try {
                Schema::table('category_attributes', function (Blueprint $table) {
                    $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
                    $table->foreign('attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');
                    $table->unique(['category_id', 'attribute_id'], 'cat_attr_unique');
                });
            } catch (\Exception $e) {
                // Foreign keys might already exist
            }
        }

        // Product Variants (specific combinations like "Red, Large", "Blue, Medium")
        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->string('sku', 100);
                $table->text('attributes'); // JSON: {"size": "L", "color": "Red"}
                $table->decimal('price', 28, 8)->default(0);
                $table->decimal('discount', 28, 8)->default(0);
                $table->tinyInteger('discount_type')->default(1); // 1=fixed, 2=percent
                $table->integer('quantity')->default(0);
                $table->string('image')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();

                $table->index('product_id');
                $table->index('status');
                $table->index('quantity');
                $table->index('sku');
            });

            // Add foreign key separately
            try {
                Schema::table('product_variants', function (Blueprint $table) {
                    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }

        // Product Attribute Values (actual values assigned to products)
        if (!Schema::hasTable('product_attribute_values')) {
            Schema::create('product_attribute_values', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('attribute_id');
                $table->string('value', 255);
                $table->timestamps();

                $table->index(['product_id', 'attribute_id'], 'prod_attr_idx');
            });

            // Add foreign keys separately
            try {
                Schema::table('product_attribute_values', function (Blueprint $table) {
                    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                    $table->foreign('attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign keys might already exist
            }
        }

        // Update products table to support variants
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'has_variants')) {
                $table->tinyInteger('has_variants')->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('products', 'variant_attributes')) {
                $table->text('variant_attributes')->nullable()->after('has_variants');
            }
        });

        // Update carts table to support variants (Laravel table name is 'carts')
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'variant_id')) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('carts', 'variant_details')) {
                $table->text('variant_details')->nullable()->after('variant_id');
            }
        });

        // Add foreign key for carts separately
        try {
            Schema::table('carts', function (Blueprint $table) {
                $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // Foreign key might already exist
        }

        // Update order_details table to support variants
        Schema::table('order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('order_details', 'variant_id')) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('order_details', 'variant_details')) {
                $table->text('variant_details')->nullable()->after('variant_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Drop foreign keys first
        try {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropForeign(['variant_id']);
            });
        } catch (\Exception $e) {
        }

        Schema::table('order_details', function (Blueprint $table) {
            if (Schema::hasColumn('order_details', 'variant_id')) {
                $table->dropColumn(['variant_id', 'variant_details']);
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'variant_id')) {
                $table->dropColumn(['variant_id', 'variant_details']);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'has_variants')) {
                $table->dropColumn(['has_variants', 'variant_attributes']);
            }
        });

        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('category_attributes');
        Schema::dropIfExists('product_attributes');
    }
};
