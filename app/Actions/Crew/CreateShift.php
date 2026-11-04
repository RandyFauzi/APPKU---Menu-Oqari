<?php

namespace App\Actions\Crew;

use App\Models\CrewShift;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Creates a shift schedule for a crew member.
 * Validates that the assigned user belongs to the same shop (tenant safety).
 */
class CreateShift
{
    /**
     * @param  array{user_id: int|string, date: string, start_time: string, end_time: string, position?: ?string, notes?: ?string}  $data
     */
    public function execute(Shop $shop, array $data, ?int $shiftId = null): CrewShift
    {
        return DB::transaction(function () use ($shop, $data, $shiftId): CrewShift {
            Shop::whereKey($shop->id)->lockForUpdate()->firstOrFail();
            $shift = $shiftId ? CrewShift::where('shop_id', $shop->id)->whereKey($shiftId)->lockForUpdate()->firstOrFail() : new CrewShift;
            $crewMember = User::where('id', $data['user_id'])
                ->where('shop_id', $shop->id)
                ->where('role', '!=', 'superadmin')->unarchived()->lockForUpdate()->first();

            if (! $crewMember || ! $crewMember->isAccountActive()) {
                throw ValidationException::withMessages(['user_id' => 'Pilih staf aktif dari kafe Anda.']);
            }
            $shift->fill([
                'shop_id' => $shop->id,
                'user_id' => $crewMember->id,
                'date' => $data['date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'position' => $data['position'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
            $candidates = CrewShift::where('shop_id', $shop->id)->where('user_id', $crewMember->id)
                ->whereBetween('date', [$shift->startsAt()->subDay()->toDateString(), $shift->endsAt()->toDateString()])
                ->when($shiftId, fn ($query) => $query->where('id', '!=', $shiftId))->get();
            foreach ($candidates as $existing) {
                if ($shift->startsAt()->lt($existing->endsAt()) && $shift->endsAt()->gt($existing->startsAt())) {
                    throw ValidationException::withMessages(['start_time' => 'Jadwal bertabrakan dengan shift lain milik staf ini.']);
                }
            }
            $shift->save();

            return $shift;
        }, 3);
    }
}
