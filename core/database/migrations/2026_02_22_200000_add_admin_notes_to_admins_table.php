<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admins') && !Schema::hasColumn('admins', 'admin_notes')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->text('admin_notes')->nullable()->after('mobile');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'admin_notes')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('admin_notes');
            });
        }
    }
};
