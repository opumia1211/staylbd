<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminIpWhitelist extends Model
{
    protected $table = 'admin_ip_whitelist';

    protected $fillable = ['ip_address', 'label', 'enabled'];

    protected $casts = ['enabled' => 'boolean'];

    public static function isAllowed(string $ip): bool
    {
        if (!self::isEnabled()) {
            return true;
        }
        return self::where('ip_address', $ip)->where('enabled', true)->exists();
    }

    public static function isEnabled(): bool
    {
        if (config('admin.zero_trust_mode', false)) {
            return true;
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('security_settings')) {
            $v = \App\Models\SecuritySetting::getBool(
                'ip_whitelist_enabled',
                filter_var(env('ADMIN_IP_WHITELIST_ENABLED', false), FILTER_VALIDATE_BOOLEAN)
            );
            return $v;
        }
        return filter_var(env('ADMIN_IP_WHITELIST_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
    }
}
