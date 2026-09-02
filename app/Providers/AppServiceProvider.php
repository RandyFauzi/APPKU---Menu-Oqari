<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        // Role-Permission Matrix
        $rolePermissions = [
            'owner' => ['*'], // Has all access
            'manager' => [
                'access-pos', 'view-orders', 'manage-orders', 'manage-menu',
                'manage-crew', 'manage-settings', 'view-reports', 'manage-reports',
                'manage-shifts', 'manage-register', 'manage-payments', 'manage-inventory'
            ],
            'cashier' => [
                'access-pos', 'view-orders', 'manage-orders', 'manage-register', 'manage-payments'
            ],
            'barista' => [
                'view-kitchen', 'update-kitchen-status', 'view-orders'
            ],
            'crew' => [
                'view-own-schedule'
            ]
        ];

        // Superadmin bypass (Platform Level)
        Gate::before(function (User $user, $ability) {
            if ($user->role === 'superadmin') {
                return true;
            }
        });

        // Register Gates dynamically based on matrix
        $allPermissions = [
            'access-pos', 'view-orders', 'manage-orders', 'manage-menu', 'manage-crew',
            'manage-settings', 'view-reports', 'manage-reports', 'manage-shifts',
            'manage-register', 'manage-payments', 'manage-inventory', 'manage-integrations',
            'view-kitchen', 'update-kitchen-status', 'view-own-schedule'
        ];

        foreach ($allPermissions as $permission) {
            Gate::define($permission, function (User $user) use ($permission, $rolePermissions) {
                $role = strtolower($user->role);
                if (!isset($rolePermissions[$role])) return false;
                if (in_array('*', $rolePermissions[$role])) return true;
                return in_array($permission, $rolePermissions[$role]);
            });
        }
        //
    }
}

