<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Allow admin to show/hide "X people viewed this in the last 24 hours" on product page.
     */
    public function up(): void
    {
        if (!Schema::hasTable('general_settings') || Schema::hasColumn('general_settings', 'display_view_count')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            $table->tinyInteger('display_view_count')->default(1)->after('display_stock')->comment('1=show view count on product page, 0=hide');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }
        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'display_view_count')) {
                $table->dropColumn('display_view_count');
            }
        });
    }
};
