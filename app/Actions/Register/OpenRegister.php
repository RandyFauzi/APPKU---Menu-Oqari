<?php

namespace App\Actions\Register;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Opens a cashier session (shift).
 * Guards against double-opening the same register.
 */
class OpenRegister
{
    /**
     * @param  float  $openingCash  Physical amount counted in the drawer before shift starts
     *
     * @throws Exception
     */
    public function execute(User $cashier, float $openingCash): CashRegisterSession
    {
        $shopId = $cashier->shop_id;

        if (! $shopId) {
            throw new Exception('User tidak terhubung ke toko manapun.');
        }

        return DB::transaction(function () use ($cashier, $shopId, $openingCash) {
            // Guard: prevent double-open
            $existing = CashRegisterSession::where('shop_id', $shopId)
                ->where('status', 'OPEN')
                ->first();

            if ($existing) {
                throw new Exception('Sesi kasir sudah aktif. Tutup sesi sebelumnya terlebih dahulu.');
            }

            $register = CashRegister::where('shop_id', $shopId)->firstOrCreate(
                ['shop_id' => $shopId],
                ['name' => 'Main Register']
            );

            return CashRegisterSession::create([
                'shop_id' => $shopId,
                'cash_register_id' => $register->id,
                'opened_by' => $cashier->id,
                'opening_cash' => $openingCash,
                'total_cash_sales' => 0,
                'total_qris_sales' => 0,
                'total_other_sales' => 0,
                'status' => 'OPEN',
                'opened_at' => now(),
            ]);
        });
    }
}
