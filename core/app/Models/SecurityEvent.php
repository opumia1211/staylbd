<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SecurityEvent extends Model
{
    protected $fillable = [
        'event_type', 'severity', 'ip_address', 'admin_id', 'user_id',
        'route', 'user_agent', 'payload', 'message',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public static function log(string $eventType, string $severity = 'medium', array $data = []): self
    {
        $model = self::create(array_merge([
            'event_type' => $eventType,
            'severity'   => $severity,
            'ip_address' => request()->ip(),
            'admin_id'   => auth()->guard('admin')->id(),
            'route'      => request()->path(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 500),
        ], $data));

        if (in_array($severity, ['critical', 'high'], true)) {
            Log::channel('security')->warning("Security event: {$eventType}", $data);
        }
        return $model;
    }
}
