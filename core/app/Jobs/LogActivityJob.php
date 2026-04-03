<?php

namespace App\Jobs;

use App\Models\SuspiciousActivity;
use App\Models\UserActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class LogActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 60, 300];
    public $timeout = 30;

    /** @var array<string, mixed> */
    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
        $this->onQueue(config('activity.log_queue', 'default'));
    }

    public function handle(): void
    {
        try {
            $log = UserActivityLog::create([
                'user_id' => $this->payload['user_id'] ?? null,
                'session_id' => $this->payload['session_id'] ?? null,
                'action_type' => $this->payload['action_type'] ?? 'unknown',
                'description' => isset($this->payload['description']) ? mb_substr((string) $this->payload['description'], 0, 1000) : null,
                'model_type' => isset($this->payload['model_type']) ? mb_substr((string) $this->payload['model_type'], 0, 100) : null,
                'model_id' => $this->payload['model_id'] ?? null,
                'ip_address' => $this->payload['ip_address'] ?? null,
                'device' => $this->payload['device'] ?? null,
                'browser' => $this->payload['browser'] ?? null,
                'os' => $this->payload['os'] ?? null,
                'country' => isset($this->payload['country']) ? mb_substr((string) $this->payload['country'], 0, 100) : null,
                'city' => isset($this->payload['city']) ? mb_substr((string) $this->payload['city'], 0, 100) : null,
                'latitude' => $this->payload['latitude'] ?? null,
                'longitude' => $this->payload['longitude'] ?? null,
                'url' => isset($this->payload['url']) ? mb_substr((string) $this->payload['url'], 0, 500) : null,
            ]);

            $this->checkFraudConditions($log);
        } catch (\Throwable $e) {
            \Log::channel('daily')->warning('LogActivityJob failed: ' . $e->getMessage(), [
                'payload' => $this->payload,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    protected function checkFraudConditions(UserActivityLog $log): void
    {
        if (!class_exists(SuspiciousActivity::class) || !\Illuminate\Support\Facades\Schema::hasTable('suspicious_activities')) {
            return;
        }

        $reason = null;

        if ($log->action_type === 'login_failed') {
            $count = UserActivityLog::where('ip_address', $log->ip_address)
                ->where('action_type', 'login_failed')
                ->where('created_at', '>=', now()->subMinutes(2))
                ->count();
            if ($count >= 5) {
                $reason = '5_failed_logins_2min';
            }
        }

        if ($log->action_type === 'payment_failure' && $log->ip_address) {
            $count = UserActivityLog::where('ip_address', $log->ip_address)
                ->where('action_type', 'payment_failure')
                ->where('created_at', '>=', now()->subMinutes(5))
                ->count();
            if ($count >= 3) {
                $reason = '3_payment_failures_5min';
            }
        }

        if ($log->action_type === 'cart_add' && $log->ip_address) {
            $count = UserActivityLog::where('ip_address', $log->ip_address)
                ->where('action_type', 'cart_add')
                ->where('created_at', '>=', now()->subMinutes(1))
                ->count();
            if ($count >= 15) {
                $reason = 'rapid_cart_spam';
            }
        }

        if ($reason) {
            try {
                SuspiciousActivity::create([
                    'activity_log_id' => $log->id,
                    'user_id' => $log->user_id,
                    'ip_address' => $log->ip_address,
                    'reason' => $reason,
                    'resolved' => 0,
                ]);
            } catch (\Throwable $e) {
                \Log::channel('daily')->warning('SuspiciousActivity create failed: ' . $e->getMessage());
            }
        }
    }
}
