<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'username_editable')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('username_editable')->default(1)->after('username')->comment('1=auto-generated username, can edit once; 0=user set at registration, cannot edit');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'username_editable')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('username_editable');
            });
        }
    }
};
