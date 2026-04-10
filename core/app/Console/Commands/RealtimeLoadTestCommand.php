<?php

namespace App\Console\Commands;

use App\Events\ProductUpdated;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Stress-test HTTP throughput and optionally enqueue broadcast jobs (Horizon / workers).
 */
class RealtimeLoadTestCommand extends Command
{
    protected $signature = 'realtime:load-test
                            {--url= : Base URL (default: config app.url)}
                            {--path=/ : Request path (e.g. / or /products)}
                            {--requests=200 : Total HTTP GET requests}
                            {--concurrency=20 : Requests per wave}
                            {--broadcasts=0 : Enqueue ProductUpdated events (random product)}';

    protected $description                  = 'Simulate high storefront traffic and optional broadcast load';

    public function handle(): int
    {
        $base         = rtrim((string) ($this->option('url') ?: config('app.url')), '/');
        $path         = (string) $this->option('path');
        if ($path === '') {
            $path = '/';
        }
        if ($path[0] !== '/') {
            $path = '/'.$path;
        }
        $total        = max(1, (int) $this->option('requests'));
        $concurrency  = max(1, (int) $this->option('concurrency'));
        $broadcasts   = max(0, (int) $this->option('broadcasts'));

        $this->info("GET {$base}{$path} — {$total} requests, concurrency {$concurrency}");

        $errors         = 0;
        $completed      = 0;
        $startedAt      = microtime(true);
        $remaining      = $total;

        while ($remaining > 0) {
            $wave = min($concurrency, $remaining);
            $urls = [];
            for ($i = 0; $i < $wave; $i++) {
                $urls[] = $base.$path;
            }

            try {
                $responses = Http::pool(function (Pool $pool) use ($urls) {
                    foreach ($urls as $idx => $url) {
                        $pool->as((string) $idx)->timeout(45)->get($url);
                    }
                });

                foreach ($responses as $response) {
                    $completed++;
                    if ($response instanceof Throwable) {
                        $errors++;
                    } elseif (method_exists($response, 'successful') && ! $response->successful()) {
                        $errors++;
                    }
                }
            } catch (Throwable $e) {
                $this->error($e->getMessage());
                $errors += $wave;
                $completed += $wave;
            }

            $remaining -= $wave;
        }

        $elapsed = microtime(true) - $startedAt;
        $rps     = $elapsed > 0 ? round($total / $elapsed, 1) : $total;

        $this->info(sprintf(
            'HTTP done in %.2fs — %d/%d ok, ~%s req/s',
            $elapsed,
            $total - $errors,
            $total,
            $rps
        ));

        if ($broadcasts > 0) {
            $this->dispatchBroadcasts($broadcasts);
        }

        $this->line('Sustained polling: k6 run tools/k6-realtime-stress.js (set BASE_URL; see script header).');

        return $errors > 0 ? 1 : 0;
    }

    protected function dispatchBroadcasts(int $count): void
    {
        if (! Product::query()->exists()) {
            $this->warn('No products in DB; skipping --broadcasts.');

            return;
        }

        if (config('broadcasting.default') === 'null') {
            $this->warn('BROADCAST_DRIVER is null; events will still queue if ShouldBroadcast is used — ensure pusher/redis and workers.');
        }

        for ($i = 0; $i < $count; $i++) {
            $product = Product::query()->inRandomOrder()->first();
            if ($product) {
                ProductUpdated::dispatch($product, 'updated');
            }
        }

        $this->info("Queued {$count} ProductUpdated broadcast job(s); run Horizon or queue:work to process.");
    }
}
