<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('support_tickets')) {
            return;
        }
        Schema::table('support_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('support_tickets', 'channel')) {
                $table->string('channel', 32)->default('web')->after('subject');
            }
            if (!Schema::hasColumn('support_tickets', 'assigned_to')) {
                $table->unsignedBigInteger('assigned_to')->nullable()->after('channel')->comment('Admin/agent assigned');
            }
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn(['channel', 'assigned_to']);
        });
    }
};
