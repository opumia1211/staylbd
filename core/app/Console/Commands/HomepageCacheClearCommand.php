<?php

namespace App\Console\Commands;

use App\Services\HomepageDataService;
use Illuminate\Console\Command;

class HomepageCacheClearCommand extends Command
{
    protected $signature = 'staylbd:homepage-cache-clear';

    protected $description = 'Clear homepage sections cache (categories, products, etc.). Run after editing categories or when homepage does not update.';

    public function handle(): int
    {
        HomepageDataService::clearCache();
        $this->info('Homepage sections cache cleared. Next homepage load will show fresh data.');
        return 0;
    }
}
