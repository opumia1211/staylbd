<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addCompositeIndexes();
        $this->createSuspiciousActivitiesTable();
        $this->createArchiveTable();
    }

    protected function addCompositeIndexes(): void
    {
        if (!Schema::hasTable('user_activity_logs')) {
            return;
        }
        $indexes = ['user_activity_logs_action_created_index', 'user_activity_logs_user_created_index', 'user_activity_logs_ip_created_index'];
        $specs = [
            ['action_type', 'created_at'],
            ['user_id', 'created_at'],
            ['ip_address', 'created_at'],
        ];
        foreach ($indexes as $i => $name) {
            try {
                Schema::table('user_activity_logs', function (Blueprint $table) use ($name, $specs, $i) {
                    $table->index($specs[$i], $name);
                });
            } catch (\Throwable $e) {
                if (strpos($e->getMessage(), 'Duplicate key') === false && strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }
    }

    protected function createSuspiciousActivitiesTable(): void
    {
        if (Schema::hasTable('suspicious_activities')) {
            return;
        }
        Schema::create('suspicious_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_log_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('reason', 80)->index();
            $table->unsignedTinyInteger('resolved')->default(0)->index();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    protected function createArchiveTable(): void
    {
        if (Schema::hasTable('user_activity_logs_archive')) {
            return;
        }
        Schema::create('user_activity_logs_archive', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id', 100)->nullable();
            $table->string('action_type', 60)->index();
            $table->string('description', 1000)->nullable();
            $table->string('model_type', 100)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('device', 50)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('os', 100)->nullable();
            $table->string('country', 100)->nullable()->index();
            $table->string('city', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('url', 500)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('user_activity_logs')) {
            Schema::table('user_activity_logs', function (Blueprint $table) {
                $table->dropIndex('user_activity_logs_action_created_index');
                $table->dropIndex('user_activity_logs_user_created_index');
                $table->dropIndex('user_activity_logs_ip_created_index');
            });
        }
        Schema::dropIfExists('suspicious_activities');
        Schema::dropIfExists('user_activity_logs_archive');
    }
};
