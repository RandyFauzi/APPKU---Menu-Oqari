<?php

namespace App\Actions\Crew;

use App\Models\ActivityLog;
use App\Models\CashRegisterSession;
use App\Models\Shop;
use App\Models\User;
use App\Policies\CafeUserPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ManageCafeUser
{
    public function __construct(private CafeUserPolicy $policy) {}

    /** @param array<string, mixed> $data */
    public function execute(User $actor, array $data, ?int $targetId = null): User
    {
        return DB::transaction(function () use ($actor, $data, $targetId): User {
            Shop::whereKey($actor->shop_id)->lockForUpdate()->firstOrFail();
            $actor = User::where('shop_id', $actor->shop_id)->whereKey($actor->id)->firstOrFail();
            abort_unless($actor->isAccountActive() && $actor->can('manage-users'), 403);
            $target = $targetId ? User::where('shop_id', $actor->shop_id)->where('role', '!=', 'superadmin')->whereKey($targetId)->lockForUpdate()->firstOrFail() : new User(['shop_id' => $actor->shop_id]);
            if ($targetId) {
                abort_unless($this->policy->update($actor, $target), 403);
            }
            $role = $data['role'] ?? $target->role;
            $allowedRoles = $actor->role === 'owner' ? array_keys(config('access.roles')) : ['cashier', 'barista', 'kitchen', 'crew'];
            abort_unless(in_array($role, $allowedRoles, true), 403);
            $permissions = array_key_exists('permissions', $data) ? $data['permissions'] : ($role === $target->role ? $target->permissions : null);
            $candidate = new User(['role' => $role, 'shop_id' => $actor->shop_id]);
            $candidate->permissions = $permissions;
            $effective = $candidate->effectivePermissions();
            if ($actor->role !== 'owner' && (array_diff($effective, $actor->effectivePermissions()) !== [] || in_array('manage-users', $effective, true))) {
                throw ValidationException::withMessages(['permissions' => 'Anda tidak dapat membagikan hak akses di luar kewenangan sendiri atau mendelegasikan pengelolaan user.']);
            }
            if (in_array('manage-users', $effective, true) && ! in_array($role, ['admin', 'manager'], true)) {
                throw ValidationException::withMessages(['permissions' => 'Pengelolaan user hanya dapat didelegasikan kepada Admin Cafe atau Manager.']);
            }
            foreach (config('access.dependencies') as $ability => $dependencies) {
                if (in_array($ability, $effective, true) && array_diff($dependencies, $effective) !== []) {
                    throw ValidationException::withMessages(['permissions' => 'Izin '.config('access.permissions.'.$ability.'.label').' membutuhkan: '.implode(', ', array_map(fn (string $key): string => config('access.permissions.'.$key.'.label'), $dependencies)).'.']);
                }
            }
            $before = $targetId ? $target->only(['name', 'email', 'role', 'is_active', 'permissions']) : null;
            $target->fill(['name' => $data['name'] ?? $target->name, 'email' => $data['email'] ?? $target->email, 'role' => $role]);
            $target->permissions = $permissions;
            $target->is_active = $data['is_active'] ?? ($targetId ? $target->is_active : true);
            if (! empty($data['password'])) {
                $target->password = $data['password'];
            }
            $accessChanged = $target->isDirty(['email', 'role', 'permissions', 'is_active', 'password']);
            if ($targetId && $accessChanged && CashRegisterSession::where('user_id', $target->id)->where('status', 'OPEN')->exists()) {
                throw ValidationException::withMessages(['is_active' => 'Tutup shift kasir user ini sebelum mengubah akses, password, atau menonaktifkannya.']);
            }
            if ($targetId && $accessChanged) {
                $target->account_version++;
                $target->remember_token = null;
            }
            if ($target->isDirty('email')) {
                $target->email_verified_at = null;
            }
            $target->save();
            ActivityLog::create(['shop_id' => $actor->shop_id, 'user_id' => $actor->id, 'action' => $targetId ? 'user.access_updated' : 'user.created',
                'ip_address' => request()->ip(), 'user_agent' => mb_substr((string) request()->userAgent(), 0, 255), 'entity_type' => User::class, 'entity_id' => $target->id, 'description' => $targetId ? 'Memperbarui akun dan hak akses user' : 'Membuat akun cafe',
                'old_values' => $before, 'new_values' => $target->only(['name', 'email', 'role', 'is_active', 'permissions'])]);

            return $target;
        }, 3);
    }
}
