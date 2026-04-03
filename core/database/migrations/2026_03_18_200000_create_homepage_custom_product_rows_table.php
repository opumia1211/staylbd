<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_custom_product_rows', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('source_type', 20);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->json('product_ids')->nullable();
            $table->unsignedTinyInteger('product_limit')->default(12);
            $table->unsignedTinyInteger('interval_seconds')->nullable();
            $table->string('view_all_url', 512)->nullable();
            $table->string('view_all_label', 120)->nullable();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_custom_product_rows');
    }
};
