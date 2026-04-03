<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SecurityAuditLog extends Model
{
    protected $table = 'security_audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'setting_key', 'old_value', 'new_value',
        'admin_id', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public static function log(string $settingKey, $oldValue, $newValue): void
    {
        self::create([
            'setting_key' => $settingKey,
            'old_value'   => $oldValue === null || $oldValue === '' ? null : (string) $oldValue,
            'new_value'   => $newValue === null || $newValue === '' ? null : (string) $newValue,
            'admin_id'    => Auth::guard('admin')->id(),
            'ip_address'  => request()->ip(),
            'user_agent'  => substr(request()->userAgent() ?? '', 0, 500),
        ]);
    }
}
