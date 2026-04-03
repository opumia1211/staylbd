<?php

namespace App\Console\Commands;

use App\Models\Frontend;
use Illuminate\Console\Command;

/**
 * Debug login content and helpers – verify admin settings are stored and read correctly.
 * Run: php artisan login:debug
 */
class LoginConfigDebugCommand extends Command
{
    protected $signature = 'login:debug';
    protected $description = 'Show login.content row and helper output (getLoginFieldLabel, isLoginCaptchaEnabled)';

    public function handle(): int
    {
        $this->line('');
        $this->info('--- Login config (from DB and helpers) ---');

        $row = Frontend::where('data_keys', 'login.content')->orderBy('id', 'desc')->first();
        if (!$row) {
            $this->warn('No row found with data_keys = login.content');
            $this->line('Create one by saving the Login section in Admin → Frontend → Login.');
        } else {
            $this->line('Row id: ' . $row->id);
            if (isset($row->data_values)) {
                $dv = (array) $row->data_values;
                $this->line('data_values keys: ' . implode(', ', array_keys($dv)));
                if (isset($row->data_values->login_fields)) {
                    $lf = is_array($row->data_values->login_fields)
                        ? $row->data_values->login_fields
                        : (array) $row->data_values->login_fields;
                    $this->line('login_fields: ' . json_encode($lf));
                }
                if (isset($row->data_values->captcha_enabled)) {
                    $this->line('captcha_enabled: ' . $row->data_values->captcha_enabled);
                }
                if (isset($row->data_values->social_login_buttons)) {
                    $sb = is_array($row->data_values->social_login_buttons)
                        ? $row->data_values->social_login_buttons
                        : (array) $row->data_values->social_login_buttons;
                    $this->line('social_login_buttons: ' . json_encode($sb));
                }
            }
        }

        $this->line('');
        $this->line('Helper output (what user login page uses):');
        $this->line('  getLoginFieldLabel(): ' . getLoginFieldLabel());
        $this->line('  isLoginCaptchaEnabled(): ' . (isLoginCaptchaEnabled() ? 'true' : 'false'));
        $this->line('  loginFieldsConfig(): ' . json_encode(loginFieldsConfig()));
        $this->line('  getSocialLoginButtonsConfig(): ' . json_encode(getSocialLoginButtonsConfig()));
        foreach (['google', 'facebook', 'twitter', 'apple'] as $p) {
            $envOn = env(strtoupper($p) . '_LOGIN_ENABLED') == '1';
            $show = isSocialLoginButtonEnabled($p);
            $this->line('  isSocialLoginButtonEnabled(' . $p . '): ' . ($show ? 'true' : 'false') . ' (env ' . $p . '_LOGIN_ENABLED=' . ($envOn ? '1' : '0') . ')');
        }
        $this->line('');

        return 0;
    }
}
