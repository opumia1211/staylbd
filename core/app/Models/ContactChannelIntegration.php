<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ContactChannelIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'channel',
        'is_active',
        'is_primary',
        'settings',
        'auth_meta',
        'last_synced_at',
        'last_error_at',
        'last_error_message',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'is_primary' => 'bool',
        'settings' => 'array',
        'auth_meta' => 'array',
        'last_synced_at' => 'datetime',
        'last_error_at' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(ContactChannelMessage::class);
    }

    public function getSetting(string $key, $default = null)
    {
        return Arr::get($this->settings ?? [], $key, $default);
    }

    public function getSecret(string $key, $default = null)
    {
        $value = Arr::get($this->auth_meta ?? [], $key, null);
        if (!$value) {
            return $default;
        }
        try {
            return decrypt($value);
        } catch (\Throwable $e) {
            return $default;
        }
    }
}
