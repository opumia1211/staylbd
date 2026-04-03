<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('auto_responses')) {
            return;
        }
        Schema::table('auto_responses', function (Blueprint $table) {
            if (!Schema::hasColumn('auto_responses', 'keywords')) {
                $table->json('keywords')->nullable()->after('keyword')->comment('Multiple keywords; any match triggers reply');
            }
            if (!Schema::hasColumn('auto_responses', 'name')) {
                $table->string('name', 191)->nullable()->after('id')->comment('Rule title for admin reference');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('auto_responses')) {
            Schema::table('auto_responses', function (Blueprint $table) {
                if (Schema::hasColumn('auto_responses', 'keywords')) {
                    $table->dropColumn('keywords');
                }
                if (Schema::hasColumn('auto_responses', 'name')) {
                    $table->dropColumn('name');
                }
            });
        }
    }
};
