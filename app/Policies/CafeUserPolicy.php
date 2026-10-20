<?php

namespace App\Policies;

use App\Models\User;

class CafeUserPolicy
{
    public function update(User $actor, User $target): bool
    {
        if (! $actor->shop_id || (int) $actor->shop_id !== (int) $target->shop_id || $actor->id === $target->id || in_array($target->role, ['owner', 'superadmin'], true)) {
            return false;
        }
        if (! $actor->can('manage-users')) {
            return false;
        }

        return $actor->role === 'owner' || in_array($target->role, ['cashier', 'barista', 'kitchen', 'crew'], true);
    }
}
