<?php

namespace App\Console\Commands;

use App\Models\UiSetting;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UiSettingsHealthCommand extends Command
{
    protected $signature = 'ui-settings:health {--repair : Auto repair ui_settings table if broken}';
    protected $description = 'Check and optionally repair ui_settings table health';

    public function handle(): int
    {
        if (UiSetting::isTableQueryable()) {
            $this->info('ui_settings table is healthy.');
            return self::SUCCESS;
        }

        $this->warn('ui_settings table is missing or broken.');
        if (!$this->option('repair')) {
            $this->line('Run with --repair to auto-recover.');
            return self::FAILURE;
        }

        try {
            Schema::dropIfExists('ui_settings');
            Schema::create('ui_settings', function ($table) {
                $table->id();
                $table->string('product_card_bg', 30)->default('#ffffff');
                $table->string('product_button_color', 30)->default('#1f2937');
                $table->string('product_buy_now_color', 30)->default('#0e9f90');
                $table->string('product_buy_now_hover', 30)->default('#0c8a7d');
                $table->string('product_price_color', 30)->nullable();
                $table->string('header_bg', 30)->nullable();
                $table->string('footer_bg', 30)->nullable();
                $table->string('rating_color', 30)->default('#f59e0b');
                $table->string('discount_badge_color', 30)->default('#dc2626');
                $table->string('stock_color', 30)->nullable();
                $table->string('shipping_badge_color', 30)->nullable();
                $table->string('theme_template', 50)->default('default');
                $table->timestamps();
            });

            $values = UiSetting::snapshotOrDefaults();
            DB::table('ui_settings')->updateOrInsert(
                ['id' => 1],
                array_merge($values, ['updated_at' => now(), 'created_at' => now()])
            );
        } catch (QueryException $e) {
            $this->error('Auto repair failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $ok = UiSetting::isTableQueryable();
        $this->info($ok ? 'ui_settings repaired successfully.' : 'Repair attempted, but table is still unhealthy.');
        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
