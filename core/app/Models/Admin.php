<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    const ROLE_OWNER = 'owner';
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_SUPPORT = 'support';

    protected $fillable = [
        'name', 'username', 'email', 'mobile', 'admin_notes', 'password', 'role', 'allowed_sections', 'force_password_change',
        'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
    ];

    protected $casts = [
        'allowed_sections'        => 'array',
        'force_password_change'   => 'boolean',
        'two_factor_confirmed_at' => 'datetime',
    ];

    public function hasTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret) && $this->two_factor_confirmed_at !== null;
    }

    public function mustHaveTwoFactor(): bool
    {
        if (!config('admin.admin_two_factor_enabled', true)) {
            return false;
        }
        if (config('admin.zero_trust_mode', false)) {
            return true;
        }
        return in_array($this->role ?? '', config('admin.two_factor_mandatory_roles', []), true);
    }

    public function needsPasswordChange(): bool
    {
        return (bool) ($this->force_password_change ?? false);
    }

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function canManageAdmins(): bool
    {
        if ($this->id === 1 && !\Illuminate\Support\Facades\Schema::hasColumn('admins', 'role')) {
            return true;
        }
        return $this->isOwner();
    }

    /** Owner or null allowed_sections = full access. Otherwise check array. */
    public function hasFullAccess(): bool
    {
        if ($this->isOwner()) {
            return true;
        }
        $sections = $this->allowed_sections;
        return $sections === null || (is_array($sections) && in_array('*', $sections, true));
    }

    public function canAccessSection(string $sectionKey): bool
    {
        if ($this->hasFullAccess()) {
            return true;
        }
        $allowed = $this->allowed_sections ?? [];
        return is_array($allowed) && in_array($sectionKey, $allowed, true);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'admin_permissions', 'admin_id', 'permission_id');
    }

    public function can($abilities, $arguments = [])
    {
        if (is_string($abilities) && empty($arguments)) {
            return Permission::has($this, $abilities);
        }
        return parent::can($abilities, $arguments);
    }
}
