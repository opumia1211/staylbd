<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'gender')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('gender', 20)->nullable()->after('age')->comment('male, female, other');
            });
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'video')) {
                $table->string('video')->nullable()->after('gallery')->comment('Short product video, max 20 sec');
            }
            if (!Schema::hasColumn('products', 'target_gender')) {
                $table->string('target_gender', 20)->nullable()->after('status')->comment('male, female, unisex');
            }
            if (!Schema::hasColumn('products', 'target_age_min')) {
                $table->unsignedTinyInteger('target_age_min')->nullable()->after('target_gender');
            }
            if (!Schema::hasColumn('products', 'target_age_max')) {
                $table->unsignedTinyInteger('target_age_max')->nullable()->after('target_age_min');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'gender')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('gender');
            });
        }
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'video')) $table->dropColumn('video');
            if (Schema::hasColumn('products', 'target_gender')) $table->dropColumn('target_gender');
            if (Schema::hasColumn('products', 'target_age_min')) $table->dropColumn('target_age_min');
            if (Schema::hasColumn('products', 'target_age_max')) $table->dropColumn('target_age_max');
        });
    }
};
