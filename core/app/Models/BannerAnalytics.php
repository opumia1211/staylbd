<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BannerAnalytics extends Model
{
    public $timestamps = false;

    protected $fillable = ['frontend_id', 'event', 'device', 'campaign_source'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function frontend(): BelongsTo
    {
        return $this->belongsTo(Frontend::class, 'frontend_id');
    }

    public static function record(int $frontendId, string $event, ?string $device = null, ?string $campaignSource = null): void
    {
        static::create([
            'frontend_id' => $frontendId,
            'event' => $event === 'click' ? 'click' : 'impression',
            'device' => $device ?: self::detectDevice(),
            'campaign_source' => $campaignSource,
        ]);
    }

    protected static function detectDevice(): string
    {
        $ua = request()->userAgent() ?? '';
        if (preg_match('/mobile|android|iphone|ipad|ipod|webos|blackberry|iemobile/i', $ua)) {
            return strpos($ua, 'iPad') !== false ? 'tablet' : 'mobile';
        }
        return 'desktop';
    }
}
