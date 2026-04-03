<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('gateways') && !Schema::hasColumn('gateways', 'sort_order')) {
            Schema::table('gateways', function (Blueprint $table) {
                $table->unsignedSmallInteger('sort_order')->default(0)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('gateways') && Schema::hasColumn('gateways', 'sort_order')) {
            Schema::table('gateways', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};
