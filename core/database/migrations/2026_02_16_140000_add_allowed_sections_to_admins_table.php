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
        if (Schema::hasColumn('admins', 'allowed_sections')) {
            return;
        }
        Schema::table('admins', function (Blueprint $table) {
            $table->json('allowed_sections')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'allowed_sections')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('allowed_sections');
            });
        }
    }
};
