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
        Schema::table('frontends', function (Blueprint $table) {
            if (!Schema::hasIndex('frontends', 'frontends_data_keys_index')) {
                $table->index('data_keys');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasIndex('orders', 'orders_order_status_index')) {
                $table->index('order_status');
            }
            if (!Schema::hasIndex('orders', 'orders_payment_status_index')) {
                $table->index('payment_status');
            }
        });
    }

    public function down()
    {
        Schema::table('frontends', function (Blueprint $table) {
            $table->dropIndex(['data_keys']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['order_status']);
            $table->dropIndex(['payment_status']);
        });
    }

};
