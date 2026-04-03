<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrustedAdminDevice extends Model
{
    protected $fillable = ['admin_id', 'device_hash', 'ip_address', 'user_agent', 'verified_at'];

    protected $casts = ['verified_at' => 'datetime'];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public static function deviceHash(string $userAgent, string $ip, ?string $fingerprint = null): string
    {
        return hash('sha256', $userAgent . '|' . $ip . '|' . ($fingerprint ?? ''));
    }

    public static function isTrusted(int $adminId, string $userAgent, string $ip, ?string $fingerprint = null): bool
    {
        $hash = self::deviceHash($userAgent, $ip, $fingerprint);
        return self::where('admin_id', $adminId)->where('device_hash', $hash)->whereNotNull('verified_at')->exists();
    }

    public static function markTrusted(int $adminId, string $userAgent, string $ip, ?string $fingerprint = null): self
    {
        $hash = self::deviceHash($userAgent, $ip, $fingerprint);
        return self::updateOrCreate(
            ['admin_id' => $adminId, 'device_hash' => $hash],
            ['ip_address' => $ip, 'user_agent' => substr($userAgent ?? '', 0, 500), 'verified_at' => now()]
        );
    }
}
