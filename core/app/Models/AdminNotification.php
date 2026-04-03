<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AdminNotification extends Model
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function () {
            self::clearNotificationCache();
        });
        static::updated(function () {
            self::clearNotificationCache();
        });
        static::deleted(function () {
            self::clearNotificationCache();
        });
    }

    public static function clearNotificationCache(): void
    {
        Cache::forget('admin.notifications.list');
        Cache::forget('admin.notifications.count');
    }
}
