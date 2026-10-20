<?php

namespace App\Actions\Register;

use App\Models\CashRegisterSession;

class CalculateExpectedCash
{
    /** @return array{opening_cash: float, cash_sales: float, cash_in: float, cash_out: float, expected_cash: float} */
    public function execute(CashRegisterSession $session, bool $lockTransactions = false): array
    {
        $query = $session->transactions()->where('shop_id', $session->register->shop_id)->whereNull('cancelled_at')->orderBy('id');
        if ($lockTransactions) {
            $query->lockForUpdate();
        }
        $cashIn = 0;
        $cashOut = 0;
        foreach ($query->get() as $transaction) {
            if ($transaction->type === 'CASH_IN') {
                $cashIn += (int) round((float) $transaction->amount * 100);
            } elseif ($transaction->type === 'CASH_OUT') {
                $cashOut += (int) round((float) $transaction->amount * 100);
            }
        }
        $opening = (int) round((float) $session->opening_cash * 100);
        $sales = (int) round((float) $session->total_cash_sales * 100);

        return [
            'opening_cash' => $opening / 100, 'cash_sales' => $sales / 100,
            'cash_in' => $cashIn / 100, 'cash_out' => $cashOut / 100,
            'expected_cash' => ($opening + $sales + $cashIn - $cashOut) / 100,
        ];
    }
}
