<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OmnichannelMessageAttachment extends Model
{
    protected $table = 'omnichannel_message_attachments';

    protected $fillable = ['message_id', 'attachment', 'original_name', 'mime_type'];

    public function message()
    {
        return $this->belongsTo(OmnichannelMessage::class, 'message_id');
    }
}
