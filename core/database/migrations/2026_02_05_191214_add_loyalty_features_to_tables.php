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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'points')) {
                $table->decimal('points', 28, 8)->default(0)->after('balance');
            }
        });

        Schema::table('general_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('general_settings', 'loyalty_points_per_currency')) {
                $table->decimal('loyalty_points_per_currency', 10, 2)->default(1.00); // 1.00 point per 1 currency unit
            }
            if (!Schema::hasColumn('general_settings', 'loyalty_points_status')) {
                $table->tinyInteger('loyalty_points_status')->default(0)->comment('0: Disable, 1: Enable');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('points');
        });

        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['loyalty_points_per_currency', 'loyalty_points_status']);
        });
    }
};
