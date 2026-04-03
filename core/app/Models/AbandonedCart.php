<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbandonedCart extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_ABANDONED = 'abandoned';
    const STATUS_RECOVERED = 'recovered';

    protected $fillable = [
        'user_id',
        'session_id',
        'cookie_id',
        'local_storage_id',
        'cart_snapshot',
        'cart_value',
        'checkout_started_at',
        'last_activity_at',
        'ip_address',
        'device_type',
        'email',
        'mobile',
        'status',
        'reminder_sent_at',
        'recovery_token',
    ];

    protected $casts = [
        'cart_snapshot' => 'array',
        'cart_value' => 'decimal:2',
        'checkout_started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAbandoned($query)
    {
        return $query->where('status', self::STATUS_ABANDONED);
    }

    public function scopeRecovered($query)
    {
        return $query->where('status', self::STATUS_RECOVERED);
    }

    public function getRecoveryUrlAttribute(): ?string
    {
        if (!$this->recovery_token) {
            return null;
        }
        return route('recover.cart', ['token' => $this->recovery_token]);
    }

    public function getContactInfoAttribute(): string
    {
        if ($this->user_id && $this->user) {
            $u = $this->user;
            $parts = array_filter([$u->email ?? null, $u->mobile ?? null]);
            return implode(' / ', $parts) ?: '—';
        }
        $parts = array_filter([$this->email, $this->mobile]);
        return implode(' / ', $parts) ?: '—';
    }
}
