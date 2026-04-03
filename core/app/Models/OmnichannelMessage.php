<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmnichannelMessage extends Model
{
    protected $table = 'omnichannel_messages';

    protected $fillable = [
        'conversation_id', 'sender_type', 'sender_id', 'message', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function attachments()
    {
        return $this->hasMany(OmnichannelMessageAttachment::class, 'message_id');
    }
}
