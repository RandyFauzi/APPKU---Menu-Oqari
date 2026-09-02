import re
with open("app/Providers/AppServiceProvider.php", "r", encoding="utf-8") as f:
    content = f.read()

if "Gate::define" not in content:
    content = content.replace("use Illuminate\Support\ServiceProvider;", "use Illuminate\Support\ServiceProvider;\nuse Illuminate\Support\Facades\Gate;\nuse App\Models\User;")
    
    gates = """
        Gate::define('manage-platform', fn(User $user) => $user->role === 'superadmin');
        Gate::define('manage-crew', fn(User $user) => in_array($user->role, ['owner']));
        Gate::define('manage-settings', fn(User $user) => in_array($user->role, ['owner', 'manager']));
        Gate::define('manage-menu', fn(User $user) => in_array($user->role, ['owner', 'manager']));
        Gate::define('access-pos', fn(User $user) => in_array($user->role, ['owner', 'manager', 'cashier']));
        Gate::define('access-kitchen', fn(User $user) => in_array($user->role, ['owner', 'manager', 'kitchen']));
        Gate::define('view-reports', fn(User $user) => in_array($user->role, ['owner', 'manager']));
    """
    
    content = re.sub(r"public function boot\(\): void\s*\{", "public function boot(): void\n    {" + gates, content)
    
    with open("app/Providers/AppServiceProvider.php", "w", encoding="utf-8") as f:
        f.write(content)
