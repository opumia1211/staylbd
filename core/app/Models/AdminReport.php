<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminReport extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public const TYPE_BUG = 'bug';
    public const TYPE_FEATURE = 'feature';

    public const STATUS_PENDING = 'pending';
    public const STATUS_READ = 'read';
    public const STATUS_RESOLVED = 'resolved';

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function scopeBug($query)
    {
        return $query->where('type', self::TYPE_BUG);
    }

    public function scopeFeature($query)
    {
        return $query->where('type', self::TYPE_FEATURE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeRead($query)
    {
        return $query->where('status', self::STATUS_READ);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function getTypeBadgeAttribute(): string
    {
        return $this->type === self::TYPE_BUG ? 'danger' : 'primary';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_READ => 'info',
            self::STATUS_RESOLVED => 'success',
            default => 'secondary',
        };
    }
}
