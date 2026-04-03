<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('courier_logs')) {
            return;
        }
        if (!Schema::hasColumn('courier_logs', 'return_status')) {
            Schema::table('courier_logs', function (Blueprint $table) {
                $table->string('return_status', 20)->nullable()->default('none')->after('status'); // none, pending, returned
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('courier_logs') && Schema::hasColumn('courier_logs', 'return_status')) {
            Schema::table('courier_logs', function (Blueprint $table) {
                $table->dropColumn('return_status');
            });
        }
    }
};
