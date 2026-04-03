<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use App\Models\PopupAd;

/**
 * Popup Ads feature verification for XAMPP / production.
 * Run: php artisan popup-ads:verify
 */
class PopupAdsVerifyCommand extends Command
{
    protected $signature = 'popup-ads:verify';
    protected $description = 'Verify Popup Ads feature: table, active ads, and display logic';

    public function handle(): int
    {
        $this->info('');
        $this->info('===== Popup Ads Feature Verification =====');
        $this->info('');

        $ok = true;

        if (!Schema::hasTable('popup_ads')) {
            $this->error('FAIL: Table popup_ads does not exist. Run: php artisan migrate');
            return self::FAILURE;
        }
        $this->info('OK: Table popup_ads exists.');

        $total = PopupAd::count();
        $this->info('OK: Total popup ads: ' . $total);

        $active = PopupAd::active()->count();
        $this->info('OK: Active popup ads (within schedule): ' . $active);

        $forHome = get_popup_ads_for_display('home');
        $this->info('OK: Ads that would show on "home" page: ' . $forHome->count());

        if ($forHome->isNotEmpty()) {
            $first = $forHome->first();
            $this->info('    First ad: id=' . $first->id . ', name="' . $first->name . '", size=' . $first->getWidth() . ' x ' . $first->getHeight());
        }

        $this->info('');
        $this->info('Popup ads will show on frontend when:');
        $this->info('  - Page uses layout that includes popup (e.g. layouts.frontend)');
        $this->info('  - Ad is Active and within Start/End date');
        $this->info('  - "Show on pages" includes current page or "all"');
        $this->info('  - User has not closed this ad in this session (sessionStorage)');
        $this->info('');
        $this->info('Verification done. Feature is ' . ($active > 0 ? 'ready' : 'ready (add an active ad to see it on site)') . '.');
        $this->info('');

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
