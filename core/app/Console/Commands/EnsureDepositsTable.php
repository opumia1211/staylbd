<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsureDepositsTable extends Command
{
    protected $signature = 'deposit:ensure-table';
    protected $description = 'Create or repair the deposits table (fixes "Payment Tables Not Set Up" and corrupt table errors).';

    public function handle(): int
    {
        $this->info('Checking deposits table...');

        try {
            if (Schema::hasTable('deposits')) {
                $this->info('Deposits table already exists. Verifying with a query...');
                DB::table('deposits')->limit(1)->count();
                $this->info('OK. Table is accessible.');
                return 0;
            }
        } catch (\Throwable $e) {
            $this->warn('Table missing or corrupt: ' . $e->getMessage());
            $this->info('Dropping and recreating deposits table...');
        }

        try {
            Schema::dropIfExists('deposits');
            Schema::create('deposits', function ($table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->unsignedBigInteger('order_id')->default(0)->index();
                $table->unsignedInteger('method_code')->index();
                $table->string('method_currency', 20);
                $table->decimal('amount', 18, 8)->default(0);
                $table->decimal('charge', 18, 8)->default(0);
                $table->decimal('rate', 18, 8)->default(0);
                $table->decimal('final_amo', 18, 8)->default(0);
                $table->decimal('btc_amo', 18, 8)->default(0);
                $table->string('btc_wallet', 255)->nullable();
                $table->string('trx', 100)->unique();
                $table->unsignedTinyInteger('status')->default(0)->index();
                $table->json('detail')->nullable();
                $table->text('admin_feedback')->nullable();
                $table->timestamps();
            });
            $this->info('Deposits table created successfully.');
        } catch (\Throwable $e) {
            $this->error('Failed: ' . $e->getMessage());
            $this->line('Run in phpMyAdmin: core/database/fix_deposits_table.sql');
            return 1;
        }

        return 0;
    }
}
