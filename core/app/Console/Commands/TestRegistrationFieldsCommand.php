<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class TestRegistrationFieldsCommand extends Command
{
    protected $signature = 'registration:test-fields
                            {--clear : Clear registration_fields_config cache before showing}';
    protected $description = 'Show current registration field toggles (from admin frontend/register). Use --clear to refresh cache after admin save.';

    public function handle()
    {
        if ($this->option('clear')) {
            Cache::forget('registration_fields_config');
            $this->info('Cache "registration_fields_config" cleared.');
        }

        $config = registrationFieldsConfig();
        $enabled = array_filter($config, function ($v) {
            return (int) $v === 1 || $v === '1';
        });
        $disabled = array_diff_key($config, $enabled);

        $this->table(
            ['Field', 'Status'],
            array_merge(
                array_map(fn ($k) => [$k, 'ON'], array_keys($enabled)),
                array_map(fn ($k) => [$k, 'OFF'], array_keys($disabled))
            )
        );
        $this->info('Total: ' . count($enabled) . ' ON, ' . count($disabled) . ' OFF. User register page shows only ON fields.');
        return 0;
    }
}
