<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admins')) {
            return;
        }
        if (Schema::hasColumn('admins', 'role')) {
            return;
        }
        Schema::table('admins', function (Blueprint $table) {
            $table->string('role', 32)->default('admin')->after('password')->index();
        });
        DB::table('admins')->where('id', 1)->update(['role' => 'owner']);
    }

    public function down(): void
    {
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'role')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
