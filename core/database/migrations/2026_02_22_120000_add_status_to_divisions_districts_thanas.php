<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('divisions') && !Schema::hasColumn('divisions', 'status')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->unsignedTinyInteger('status')->default(1)->after('sort_order');
            });
        }
        if (Schema::hasTable('districts') && !Schema::hasColumn('districts', 'status')) {
            Schema::table('districts', function (Blueprint $table) {
                $table->unsignedTinyInteger('status')->default(1)->after('sort_order');
            });
        }
        if (Schema::hasTable('thanas')) {
            if (!Schema::hasColumn('thanas', 'status')) {
                Schema::table('thanas', function (Blueprint $table) {
                    $table->unsignedTinyInteger('status')->default(1)->after('sort_order');
                });
            }
            if (!Schema::hasColumn('thanas', 'postal_code')) {
                Schema::table('thanas', function (Blueprint $table) {
                    $table->string('postal_code', 20)->nullable()->after('name_bn');
                });
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('divisions') && Schema::hasColumn('divisions', 'status')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
        if (Schema::hasTable('districts') && Schema::hasColumn('districts', 'status')) {
            Schema::table('districts', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
        if (Schema::hasTable('thanas')) {
            if (Schema::hasColumn('thanas', 'status')) {
                Schema::table('thanas', function (Blueprint $table) {
                    $table->dropColumn('status');
                });
            }
            if (Schema::hasColumn('thanas', 'postal_code')) {
                Schema::table('thanas', function (Blueprint $table) {
                    $table->dropColumn('postal_code');
                });
            }
        }
    }
};
