<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::before(function (User $user): ?bool {
            if (! $user->is_active) {
                return false;
            }

            return $user->role === 'superadmin' ? true : null;
        });
        foreach (array_keys(config('access.permissions')) as $permission) {
            Gate::define($permission, fn (User $user): bool => in_array($permission, $user->effectivePermissions(), true));
        }
        Gate::define('view-dashboard', fn (User $user): bool => count(array_intersect(array_merge(config('access.dashboard'), ['view-orders']), $user->effectivePermissions())) > 0);
    }
}
