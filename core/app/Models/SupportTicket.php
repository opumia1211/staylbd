<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    public const CHANNEL_WEB = 'web';
    public const CHANNEL_TELEGRAM = 'telegram';
    public const CHANNEL_WHATSAPP = 'whatsapp';
    public const CHANNEL_FACEBOOK = 'facebook';
    public const CHANNEL_INSTAGRAM = 'instagram';
    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_OTHER = 'other';

    public function assignedTo()
    {
        return $this->belongsTo(Admin::class, 'assigned_to', 'id');
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get:fn () => $this->name,
        );
    }

    public function username(): Attribute
    {
        return new Attribute(
            get:fn () => $this->email,
        );
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function(){
            $html = '';
            if($this->status == Status::TICKET_OPEN){
                $html = '<span class="badge badge--success">'.trans("Open").'</span>';
            }
            elseif($this->status == Status::TICKET_ANSWER){
                $html = '<span class="badge badge--primary">'.trans("Answered").'</span>';
            }

            elseif($this->status == Status::TICKET_REPLY){
                $html = '<span class="badge badge--warning">'.trans("Customer Reply").'</span>';
            }
            elseif($this->status == Status::TICKET_CLOSE){
                $html = '<span class="badge badge--dark">'.trans("Closed").'</span>';
            }
            return $html;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supportMessage(){
        return $this->hasMany(SupportMessage::class);
    }

    public function channelLabel(): Attribute
    {
        return new Attribute(
            get: fn () => match ($this->channel ?? 'web') {
                'telegram' => 'Telegram',
                'whatsapp' => 'WhatsApp',
                'facebook' => 'Facebook',
                'instagram' => 'Instagram',
                'email' => 'Email',
                'other' => 'Other',
                default => 'Web Chat',
            }
        );
    }

}
