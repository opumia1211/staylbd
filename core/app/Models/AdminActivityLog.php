<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    protected $fillable = [
        'admin_id', 'action', 'model', 'model_id',
        'old_values', 'new_values', 'ip', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Log admin action (call from admin controllers).
     * Example: logAdminActivity('update', 'Product', $product->id, $old, $product->toArray());
     */
    public static function logAction(string $action, ?string $model = null, ?int $modelId = null, $oldValues = null, $newValues = null): void
    {
        try {
            self::create([
                'admin_id'   => auth()->guard('admin')->id(),
                'action'     => $action,
                'model'      => $model,
                'model_id'   => $modelId,
                'old_values' => is_array($oldValues) ? $oldValues : (is_object($oldValues) ? (array) $oldValues : null),
                'new_values' => is_array($newValues) ? $newValues : (is_object($newValues) ? (array) $newValues : null),
                'ip'         => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Admin activity log failed: ' . $e->getMessage());
        }
    }
}
