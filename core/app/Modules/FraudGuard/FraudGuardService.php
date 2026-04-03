<?php

namespace App\Modules\FraudGuard;

use Illuminate\Support\Facades\DB;

class FraudGuardService
{
    public function isBlockedIp(?string $ip): bool
    {
        if (!$ip || !\Schema::hasTable('fraud_blocks')) {
            return false;
        }
        return DB::table('fraud_blocks')->where('type', 'ip')->where('value', $ip)->exists();
    }

    public function isBlockedPhone(?string $phone): bool
    {
        if (!$phone || !\Schema::hasTable('fraud_blocks')) {
            return false;
        }
        $normalized = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($normalized) < 10) {
            return false;
        }
        $patterns = [$normalized];
        if (substr($normalized, 0, 2) === '88') {
            $patterns[] = substr($normalized, 2);
            $patterns[] = '0' . substr($normalized, 2);
        } elseif (substr($normalized, 0, 1) === '0') {
            $patterns[] = '88' . substr($normalized, 1);
        }
        return DB::table('fraud_blocks')->where('type', 'phone')
            ->whereIn('value', $patterns)
            ->exists();
    }

    public function blockIp(string $ip, ?string $reason = null, ?int $adminId = null): void
    {
        if (!\Schema::hasTable('fraud_blocks')) {
            return;
        }
        DB::table('fraud_blocks')->updateOrInsert(
            ['type' => 'ip', 'value' => $ip],
            ['reason' => $reason, 'blocked_by_admin_id' => $adminId, 'updated_at' => now()]
        );
    }

    public function blockPhone(string $phone, ?string $reason = null, ?int $adminId = null): void
    {
        if (!\Schema::hasTable('fraud_blocks')) {
            return;
        }
        $value = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($value) >= 10) {
            DB::table('fraud_blocks')->updateOrInsert(
                ['type' => 'phone', 'value' => $value],
                ['reason' => $reason, 'blocked_by_admin_id' => $adminId, 'updated_at' => now()]
            );
        }
    }

    /** Returns order count for user (repeat customer check). */
    public function orderCountForUser(?int $userId): int
    {
        if (!$userId || !\Schema::hasTable('orders')) {
            return 0;
        }
        return DB::table('orders')->where('user_id', $userId)->whereNotIn('order_status', [\App\Constants\Status::ORDER_CANCEL])->count();
    }
}
