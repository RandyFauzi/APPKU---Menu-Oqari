<?php

namespace App\Actions\Register;

use App\Models\CashRegisterSession;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Closes the active cashier session and records the reconciliation snapshot.
 * Calculates cash discrepancy (difference between expected and actual).
 * Financial snapshot is immutable after closing.
 */
class CloseRegister
{
    /**
     * @param  float  $actualCash  Physical amount counted in drawer at end of shift
     *
     * @throws Exception
     */
    public function execute(User $cashier, float $actualCash): CashRegisterSession
    {
        return DB::transaction(function () use ($cashier, $actualCash) {
            $session = CashRegisterSession::where('shop_id', $cashier->shop_id)
                ->where('status', 'OPEN')
                ->latest()
                ->first();

            if (! $session) {
                throw new Exception('Tidak ada sesi kasir yang aktif untuk ditutup.');
            }

            // Expected cash = opening + all cash sales during this session
            $expectedCash = $session->opening_cash + $session->total_cash_sales;
            $difference = $actualCash - $expectedCash;

            $session->update([
                'status' => 'CLOSED',
                'closed_by' => $cashier->id,
                'closed_at' => now(),
                'actual_cash' => $actualCash,
                'expected_cash' => $expectedCash,
                'difference' => $difference,
            ]);

            return $session->fresh();
        });
    }
}
