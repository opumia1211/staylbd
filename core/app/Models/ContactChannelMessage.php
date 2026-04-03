<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactChannelMessage extends Model
{
    protected $fillable = [
        'contact_channel_integration_id',
        'support_ticket_id',
        'user_id',
        'channel',
        'direction',
        'remote_chat_id',
        'remote_message_id',
        'sender_name',
        'sender_handle',
        'subject',
        'message',
        'attachments',
        'metadata',
        'status',
        'delivered_at',
        'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'metadata' => 'array',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function integration()
    {
        return $this->belongsTo(ContactChannelIntegration::class, 'contact_channel_integration_id');
    }

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
