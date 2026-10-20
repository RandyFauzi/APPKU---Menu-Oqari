<?php

namespace App\Actions\Register;

use App\Models\ActivityLog;
use App\Models\CashRegisterSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Closes the active cashier session and records the reconciliation snapshot.
 * Calculates cash discrepancy (difference between expected and actual).
 * Financial snapshot is immutable after closing.
 */
class CloseRegister
{
    public function __construct(private CalculateExpectedCash $calculateExpectedCash) {}

    /**
     * @param  float  $actualCash  Physical amount counted in drawer at end of shift
     */
    public function execute(User $cashier, float $actualCash, ?string $notes = null, ?int $sessionId = null, ?float $expectedConfirmation = null): CashRegisterSession
    {
        return DB::transaction(function () use ($cashier, $actualCash, $notes, $sessionId, $expectedConfirmation) {
            User::whereKey($cashier->id)->lockForUpdate()->firstOrFail();
            $session = CashRegisterSession::where('user_id', $cashier->id)
                ->whereHas('register', fn ($query) => $query->where('shop_id', $cashier->shop_id))
                ->where('status', 'OPEN')->lockForUpdate()->first();

            if (! $session) {
                throw ValidationException::withMessages(['actual_cash' => 'Tidak ada sesi kasir yang aktif untuk ditutup.']);
            }

            if ($sessionId !== null && $sessionId !== $session->id) {
                throw ValidationException::withMessages(['session_id' => 'Sesi kasir sudah berubah. Muat ulang halaman sebelum menutup.']);
            }
            $breakdown = $this->calculateExpectedCash->execute($session, true);
            $expectedCash = $breakdown['expected_cash'];
            if ($expectedConfirmation !== null && (int) round($expectedConfirmation * 100) !== (int) round($expectedCash * 100)) {
                throw ValidationException::withMessages(['actual_cash' => 'Data kas berubah sejak halaman dibuka. Muat ulang halaman dan hitung kembali sebelum menutup shift.']);
            }
            $difference = ((int) round($actualCash * 100) - (int) round($expectedCash * 100)) / 100;
            $notes = $notes !== null ? trim($notes) : null;
            if ($difference != 0 && ($notes === null || $notes === '')) {
                throw ValidationException::withMessages(['notes' => 'Isi alasan atau keterangan ketika kas kurang atau lebih.']);
            }

            $session->update([
                'status' => 'CLOSED',
                'closed_at' => now(),
                'actual_cash' => $actualCash,
                'expected_cash' => $expectedCash,
                'difference' => $difference,
                'notes' => $notes,
            ]);

            ActivityLog::create([
                'shop_id' => $cashier->shop_id, 'user_id' => $cashier->id,
                'action' => 'register.closed', 'entity_type' => CashRegisterSession::class, 'entity_id' => $session->id,
                'description' => 'Menutup sesi kasir #'.$session->id,
                'new_values' => array_merge($breakdown, ['actual_cash' => $actualCash, 'difference' => $difference, 'reconciliation_status' => $session->reconciliation_status, 'notes' => $notes]),
            ]);

            return $session->fresh();
        });
    }
}
