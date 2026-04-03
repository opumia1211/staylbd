<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use Searchable;

    protected $fillable = ['user_id', 'notification_type', 'sender', 'sent_from', 'sent_to', 'subject', 'message', 'click_url', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
