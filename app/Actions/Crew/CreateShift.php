<?php

namespace App\Actions\Crew;

use App\Models\CrewShift;
use App\Models\Shop;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Creates a shift schedule for a crew member.
 * Validates that the assigned user belongs to the same shop (tenant safety).
 */
class CreateShift
{
    /**
     * @param  array  $data  {user_id, date, start_time, end_time, position, notes}
     *
     * @throws Exception
     */
    public function execute(Shop $shop, array $data): CrewShift
    {
        return DB::transaction(function () use ($shop, $data) {
            // Validate user belongs to this shop — prevent cross-tenant IDOR
            $crewMember = User::where('id', $data['user_id'])
                ->where('shop_id', $shop->id)
                ->first();

            if (! $crewMember) {
                throw new Exception('Karyawan tidak ditemukan atau bukan anggota toko ini.');
            }

            return CrewShift::create([
                'shop_id' => $shop->id,
                'user_id' => $crewMember->id,
                'date' => $data['date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'position' => $data['position'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}
