<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'channel', 'external_id',
        'status', 'assigned_to', 'priority', 'subject', 'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(Admin::class, 'assigned_to', 'id');
    }

    public function messages()
    {
        return $this->hasMany(OmnichannelMessage::class, 'conversation_id');
    }
}
