<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminSession extends Model
{
    protected $fillable = ['admin_id', 'session_id', 'ip_address', 'user_agent', 'last_activity_at'];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
