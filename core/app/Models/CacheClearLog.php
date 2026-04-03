<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CacheClearLog extends Model
{
    protected $fillable = [
        'admin_id', 'admin_name', 'action', 'ip', 'user_agent', 'success', 'error_message',
    ];

    protected $casts = [
        'success' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
