<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SecuritySetting extends Model
{
    protected $table = 'security_settings';

    protected $fillable = ['key', 'value'];

    public static function cacheKey(string $key): string
    {
        return 'security_setting_' . $key;
    }

    public static function get(string $key, $default = null)
    {
        $cacheKey = self::cacheKey($key);
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $row = self::where('key', $key)->first();
            return $row !== null ? $row->value : $default;
        });
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $v = self::get($key, $default ? '1' : '0');
        return filter_var($v, FILTER_VALIDATE_BOOLEAN);
    }

    public static function set(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'updated_at' => now()]
        );
        Cache::forget(self::cacheKey($key));
    }

    public static function forgetCache(string $key = null): void
    {
        if ($key) {
            Cache::forget(self::cacheKey($key));
        } else {
            foreach (self::pluck('key') as $k) {
                Cache::forget(self::cacheKey($k));
            }
        }
    }
}
