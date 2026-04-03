<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminLockout extends Model
{
    protected $fillable = [
        'ip_address', 'identifier', 'failed_attempts', 'lock_count',
        'locked_at', 'unlocked_at', 'unlocked_by',
    ];

    protected $casts = [
        'locked_at'   => 'datetime',
        'unlocked_at' => 'datetime',
        'failed_attempts' => 'integer',
        'lock_count' => 'integer',
    ];

    private const FAILED_THRESHOLD = 5;
    private const LOCK_MINUTES = 15;
    private const CONSECUTIVE_LOCKS_24H = 3;
    private const EXTENDED_LOCK_HOURS = 24;

    public static function recordFailure(string $ip, ?string $identifier = null): ?int
    {
        $row = self::firstOrCreate(
            ['ip_address' => $ip],
            ['identifier' => $identifier, 'failed_attempts' => 0, 'lock_count' => 0]
        );
        $row->identifier = $identifier ?? $row->identifier;
        $row->failed_attempts = ($row->failed_attempts ?? 0) + 1;
        $row->save();

        if ($row->failed_attempts >= self::FAILED_THRESHOLD) {
            $row->locked_at = now();
            $row->lock_count = ($row->lock_count ?? 0) + 1;
            if ($row->lock_count >= self::CONSECUTIVE_LOCKS_24H) {
                $row->locked_at = now()->addHours(self::EXTENDED_LOCK_HOURS);
            } else {
                $row->locked_at = now()->addMinutes(self::LOCK_MINUTES);
            }
            $row->failed_attempts = 0;
            $row->save();
        }
        return $row->locked_at ? $row->locked_at->timestamp : null;
    }

    public static function resetAttempts(string $ip): void
    {
        self::where('ip_address', $ip)->update(['failed_attempts' => 0]);
    }

    public static function isLocked(string $ip): array
    {
        $row = self::where('ip_address', $ip)->first();
        if (!$row || !$row->locked_at) {
            return [false, null, 0];
        }
        if ($row->locked_at <= now()) {
            $row->update(['locked_at' => null, 'unlocked_at' => now()]);
            return [false, null, 0];
        }
        $remaining = max(0, $row->locked_at->timestamp - time());
        return [true, $row->locked_at->timestamp, (int) ceil($remaining / 60)];
    }

    public static function unlock(string $ip, int $adminId): bool
    {
        return self::where('ip_address', $ip)->update([
            'locked_at'   => null,
            'unlocked_at' => now(),
            'unlocked_by' => $adminId,
        ]) > 0;
    }
}
