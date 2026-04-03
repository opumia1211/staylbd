<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        // Admin permission Gate: @can('products.edit') in Blade when admin logged in
        Gate::before(function ($user, $ability) {
            if ($user instanceof Admin && Schema::hasTable('permissions')) {
                return Permission::has($user, $ability) ? true : null;
            }
            return null;
        });
    }
}
