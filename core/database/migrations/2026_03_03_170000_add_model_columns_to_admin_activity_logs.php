<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_activity_logs')) {
            return;
        }
        Schema::table('admin_activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_activity_logs', 'model')) {
                $table->string('model', 100)->nullable()->after('action')->index();
            }
            if (!Schema::hasColumn('admin_activity_logs', 'model_id')) {
                $table->unsignedBigInteger('model_id')->nullable()->after('model');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('admin_activity_logs')) {
            return;
        }
        Schema::table('admin_activity_logs', function (Blueprint $table) {
            if (Schema::hasColumn('admin_activity_logs', 'model')) {
                $table->dropColumn('model');
            }
            if (Schema::hasColumn('admin_activity_logs', 'model_id')) {
                $table->dropColumn('model_id');
            }
        });
    }
};
