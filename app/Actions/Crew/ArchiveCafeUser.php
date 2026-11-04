<?php

namespace App\Actions\Crew;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\CashRegisterSession;
use App\Models\Shop;
use App\Models\User;
use App\Policies\CafeUserPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ArchiveCafeUser
{
    public function __construct(private CafeUserPolicy $policy) {}

    public function execute(User $actor, int $targetId): User
    {
        return DB::transaction(function () use ($actor, $targetId): User {
            Shop::whereKey($actor->shop_id)->lockForUpdate()->firstOrFail();
            $actor = User::where('shop_id', $actor->shop_id)->whereKey($actor->id)->firstOrFail();
            $target = User::where('shop_id', $actor->shop_id)->whereNull('archived_at')->whereKey($targetId)->lockForUpdate()->firstOrFail();
            abort_unless($this->policy->delete($actor, $target), 403);

            if (CashRegisterSession::where('user_id', $target->id)->where('status', 'OPEN')->exists()
                || Attendance::where('shop_id', $actor->shop_id)->where('user_id', $target->id)->whereNull('checked_out_at')->exists()) {
                throw ValidationException::withMessages(['user' => 'Tutup shift kasir dan selesaikan absensi staf sebelum menghapus akun.']);
            }

            $target->archived_at = now();
            $target->is_active = false;
            $target->account_version++;
            $target->remember_token = null;
            $target->save();

            ActivityLog::create([
                'shop_id' => $actor->shop_id, 'user_id' => $actor->id, 'action' => 'user.archived',
                'entity_type' => User::class, 'entity_id' => $target->id,
                'description' => 'Mengarsipkan akun staf '.$target->name,
                'new_values' => $target->only(['name', 'email', 'role', 'is_active', 'archived_at']),
                'ip_address' => request()->ip(), 'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
            ]);

            return $target;
        }, 3);
    }
}
