<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'home_section_override')) {
                // One of: new_arrivals, best_selling, recommended (and future-safe strings)
                $table->string('home_section_override', 32)->nullable()->after('trending_now');
            }
            if (!Schema::hasColumn('products', 'home_section_rank')) {
                // Higher rank shows first inside overridden section
                $table->unsignedInteger('home_section_rank')->default(0)->after('home_section_override');
            }
            if (!Schema::hasColumn('products', 'home_exclude_from_auto')) {
                // If enabled, product will not appear in other automatic homepage rows
                $table->boolean('home_exclude_from_auto')->default(false)->after('home_section_rank');
            }

            // Helpful indexes for homepage queries
            if (!Schema::hasColumn('products', 'home_section_override') || !Schema::hasColumn('products', 'home_exclude_from_auto')) {
                return;
            }
            $table->index(['home_section_override', 'home_section_rank'], 'products_home_section_override_rank_index');
            $table->index(['home_exclude_from_auto', 'created_at'], 'products_home_exclude_auto_created_index');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'home_section_override')) {
                // Drop indexes first (if exist)
                try { $table->dropIndex('products_home_section_override_rank_index'); } catch (\Throwable $e) {}
                try { $table->dropIndex('products_home_exclude_auto_created_index'); } catch (\Throwable $e) {}
                $table->dropColumn(['home_section_override', 'home_section_rank', 'home_exclude_from_auto']);
            }
        });
    }
};

