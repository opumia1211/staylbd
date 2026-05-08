<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'today_deals')) {
                $table->boolean('today_deals')->default(false)->after('status');
            }
            if (!Schema::hasColumn('products', 'hot_deals')) {
                $table->boolean('hot_deals')->default(false)->after('today_deals');
            }
            if (!Schema::hasColumn('products', 'featured_product')) {
                $table->boolean('featured_product')->default(false)->after('hot_deals');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['today_deals', 'hot_deals', 'featured_product']);
        });
    }
};
