<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add a flag so some coupons are limited to a customer's first order only.
     */
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'is_first_order_only')) {
                $table->boolean('is_first_order_only')
                    ->default(false)
                    ->after('type');
            }
        });
    }

    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (Schema::hasColumn('coupons', 'is_first_order_only')) {
                $table->dropColumn('is_first_order_only');
            }
        });
    }
};

