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
        if (Schema::hasColumn('admins', 'mobile')) {
            return;
        }
        Schema::table('admins', function (Blueprint $table) {
            $table->string('mobile', 20)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'mobile')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('mobile');
            });
        }
    }
};
