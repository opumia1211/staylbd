<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PermissionSeeder extends Seeder
{
    protected array $permissions = [
        ['name' => 'products.view', 'group' => 'products', 'description' => 'View products'],
        ['name' => 'products.create', 'group' => 'products', 'description' => 'Create products'],
        ['name' => 'products.edit', 'group' => 'products', 'description' => 'Edit products'],
        ['name' => 'products.delete', 'group' => 'products', 'description' => 'Delete products'],
        ['name' => 'orders.view', 'group' => 'orders', 'description' => 'View orders'],
        ['name' => 'orders.edit', 'group' => 'orders', 'description' => 'Edit orders'],
        ['name' => 'users.view', 'group' => 'users', 'description' => 'View users'],
        ['name' => 'users.edit', 'group' => 'users', 'description' => 'Edit users'],
        ['name' => 'settings.manage', 'group' => 'settings', 'description' => 'Manage settings'],
        ['name' => 'support.view', 'group' => 'support', 'description' => 'View support tickets'],
        ['name' => 'support.reply', 'group' => 'support', 'description' => 'Reply to tickets'],
    ];

    public function run(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        foreach ($this->permissions as $p) {
            Permission::updateOrCreate(
                ['name' => $p['name']],
                ['group' => $p['group'] ?? null, 'description' => $p['description'] ?? null]
            );
        }

        if (!Schema::hasTable('role_permissions')) {
            return;
        }

        $rolePerms = [
            'admin'   => ['products.view', 'products.create', 'products.edit', 'products.delete', 'orders.view', 'orders.edit', 'users.view', 'users.edit', 'support.view', 'support.reply'],
            'manager' => ['products.view', 'products.create', 'products.edit', 'orders.view', 'orders.edit', 'users.view', 'support.view', 'support.reply'],
            'support' => ['orders.view', 'users.view', 'support.view', 'support.reply'],
        ];
        foreach ($rolePerms as $role => $permNames) {
            $ids = Permission::whereIn('name', $permNames)->pluck('id');
            foreach ($ids as $pid) {
                \DB::table('role_permissions')->insertOrIgnore([
                    'role' => $role,
                    'permission_id' => $pid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
