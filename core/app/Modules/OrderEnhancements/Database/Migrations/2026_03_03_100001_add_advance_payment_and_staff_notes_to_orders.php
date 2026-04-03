<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'advance_payment')) {
                $table->decimal('advance_payment', 14, 2)->default(0)->after('total')->comment('Advance/partial payment received');
            }
            if (!Schema::hasColumn('orders', 'staff_notes')) {
                $table->text('staff_notes')->nullable()->after('advance_payment')->comment('Internal staff notes');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'advance_payment')) {
                $table->dropColumn('advance_payment');
            }
            if (Schema::hasColumn('orders', 'staff_notes')) {
                $table->dropColumn('staff_notes');
            }
        });
    }
};
