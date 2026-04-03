<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuspiciousActivity extends Model
{
    protected $table = 'suspicious_activities';

    protected $fillable = [
        'activity_log_id', 'user_id', 'ip_address', 'reason', 'resolved', 'admin_note',
    ];

    protected $casts = [
        'resolved' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activityLog()
    {
        return $this->belongsTo(UserActivityLog::class, 'activity_log_id');
    }

    public static function reasonLabels(): array
    {
        return [
            '5_failed_logins_2min' => '5 failed logins in 2 min',
            '3_payment_failures_5min' => '3 payment failures in 5 min',
            'rapid_cart_spam' => 'Rapid cart spam',
        ];
    }
}
