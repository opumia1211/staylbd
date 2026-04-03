<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'group', 'description'];

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_permissions', 'permission_id', 'admin_id');
    }

    public static function has(Admin $admin, string $permission): bool
    {
        if ($admin->isOwner()) {
            return true;
        }
        if ($admin->isSuperAdmin()) {
            return true;
        }
        $rolePerms = \DB::table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role', $admin->role)
            ->where('permissions.name', $permission)
            ->exists();
        if ($rolePerms) {
            return true;
        }
        return $admin->permissions()->where('permissions.name', $permission)->exists();
    }
}
