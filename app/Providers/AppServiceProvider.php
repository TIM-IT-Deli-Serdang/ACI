<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         // @can('permission.name')
    Blade::if('can', function ($permission) {
        $user = session('user') ?? null;
        if (! $user) return false;

        $perms = $user['permissions'] ?? $user['permission'] ?? [];
        if (is_string($perms)) $perms = [$perms];
        if (! is_array($perms)) return false;

        return in_array($permission, $perms);
    });

    // @canany(['p1','p2'])
    Blade::if('canany', function ($permissions) {
        $user = session('user') ?? null;
        if (! $user) return false;

        $perms = $user['permissions'] ?? $user['permission'] ?? [];
        if (is_string($perms)) $perms = [$perms];
        if (! is_array($perms)) return false;

        foreach ((array) $permissions as $p) {
            if (in_array($p, $perms)) return true;
        }
        return false;
    });

    // optional: @role('admin')
    Blade::if('role', function ($role) {
        $user = session('user') ?? null;
        if (! $user) return false;

        $roles = $user['roles'] ?? [];
        if (is_string($roles)) $roles = [$roles];
        if (! is_array($roles)) return false;

        return in_array($role, $roles);
    });

    // optional: @hasanyrole(['admin','manager'])
    Blade::if('hasanyrole', function ($roles) {
        $user = session('user') ?? null;
        if (! $user) return false;

        $userRoles = $user['roles'] ?? [];
        if (is_string($userRoles)) $userRoles = [$userRoles];
        if (! is_array($userRoles)) return false;

        foreach ((array) $roles as $r) {
            if (in_array($r, $userRoles)) return true;
        }
        return false;
    });
    }
}
