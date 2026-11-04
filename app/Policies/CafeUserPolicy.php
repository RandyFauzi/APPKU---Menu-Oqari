<?php

namespace App\Policies;

use App\Models\User;

class CafeUserPolicy
{
    public function update(User $actor, User $target): bool
    {
        if ($target->archived_at !== null || ! $actor->shop_id || (int) $actor->shop_id !== (int) $target->shop_id || $actor->id === $target->id || in_array($target->role, ['owner', 'superadmin'], true)) {
            return false;
        }
        if (! $actor->can('manage-users')) {
            return false;
        }

        return $actor->role === 'owner' || in_array($target->role, ['cashier', 'barista', 'kitchen', 'crew'], true);
    }

    public function delete(User $actor, User $target): bool
    {
        return $actor->role === 'owner' && $this->update($actor, $target);
    }
}
