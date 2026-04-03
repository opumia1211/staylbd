<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'last_chat_seen_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_chat_seen_at')->nullable()->after('remember_token')->comment('When user last viewed live chat; used for unread admin reply count');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'last_chat_seen_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('last_chat_seen_at');
            });
        }
    }
};
