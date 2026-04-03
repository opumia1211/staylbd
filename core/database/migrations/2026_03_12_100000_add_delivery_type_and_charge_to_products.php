<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'delivery_type')) {
                $table->string('delivery_type', 20)->default('free')->after('delivery_time')->comment('free|paid');
            }
            if (!Schema::hasColumn('products', 'delivery_charge')) {
                $table->decimal('delivery_charge', 12, 2)->default(0)->after('delivery_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'delivery_type')) {
                $table->dropColumn('delivery_type');
            }
            if (Schema::hasColumn('products', 'delivery_charge')) {
                $table->dropColumn('delivery_charge');
            }
        });
    }
};
