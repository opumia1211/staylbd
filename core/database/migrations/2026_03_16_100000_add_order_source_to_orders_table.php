<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds order_source for admin tracking (e.g. quick_order, checkout).
     */
    public function up()
    {
        if (!Schema::hasTable('orders')) {
            return;
        }
        if (Schema::hasColumn('orders', 'order_source')) {
            return;
        }
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_source', 50)->nullable()->after('ip_address')->comment('quick_order|checkout|etc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (!Schema::hasTable('orders') || !Schema::hasColumn('orders', 'order_source')) {
            return;
        }
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_source');
        });
    }
};
