<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'force_password_change')) {
                $table->boolean('force_password_change')->default(false)->after('remember_token');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('admins') || !Schema::hasColumn('admins', 'force_password_change')) {
            return;
        }
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('force_password_change');
        });
    }
};
