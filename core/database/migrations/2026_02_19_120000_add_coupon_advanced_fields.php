<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Advanced coupon features: usage limit, max discount cap, per-user limit, description, type.
     */
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'usage_limit')) {
                $table->unsignedInteger('usage_limit')->nullable();
            }
            if (!Schema::hasColumn('coupons', 'max_discount')) {
                $table->decimal('max_discount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('coupons', 'per_user_limit')) {
                $table->unsignedInteger('per_user_limit')->nullable();
            }
            if (!Schema::hasColumn('coupons', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('coupons', 'type')) {
                $table->string('type', 50)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            foreach (['usage_limit', 'max_discount', 'per_user_limit', 'description', 'type'] as $col) {
                if (Schema::hasColumn('coupons', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
