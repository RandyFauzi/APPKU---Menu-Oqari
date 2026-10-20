<?php

namespace App\Providers;

use App\Models\User;
use App\Services\SchemaCompatibility;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(SchemaCompatibility::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! is_array(config('database_schema.groups'))) {
            config(['database_schema' => require config_path('database_schema.php')]);
        }
        if (! is_array(config('access.permissions'))) {
            config(['access' => require config_path('access.php')]);
        }

        Gate::before(function (User $user): ?bool {
            if (! $user->isAccountActive()) {
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
