<?php

namespace App\Actions\Payment;

use App\Models\CashRegisterSession;
use App\Models\Order;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Processes payment for a confirmed, unpaid order.
 *
 * Responsibilities:
 * - Creates a Payment ledger record
 * - Marks order as PAID
 * - If payment_method = CASH: increments total_cash_sales on active CashRegisterSession
 * - If payment_method = QRIS: increments total_qris_sales (settlement handled separately)
 *
 * Does NOT process Xendit gateway calls — that is the gateway integration layer.
 */
class ProcessPayment
{
    /**
     * @param  string  $method  CASH | QRIS | TRANSFER | OTHER
     * @param  float  $amountPaid  Physical amount tendered by customer
     * @param  string|null  $reference  External reference (Xendit ID, transfer ref, etc.)
     *
     * @throws Exception
     */
    public function execute(Order $order, string $method, float $amountPaid, ?string $reference = null): Payment
    {
        if ($order->payment_status === 'PAID') {
            throw new Exception("Pesanan #{$order->id} sudah dibayar sebelumnya.");
        }

        if ($amountPaid < $order->grand_total) {
            throw new Exception("Jumlah pembayaran kurang. Grand total: {$order->grand_total}, dibayar: {$amountPaid}");
        }

        return DB::transaction(function () use ($order, $method, $amountPaid, $reference) {
            $change = $amountPaid - $order->grand_total;

            // 1. Create Payment record
            $payment = Payment::create([
                'shop_id' => $order->shop_id,
                'order_id' => $order->id,
                'payment_method' => $method,
                'amount' => $order->grand_total,
                'status' => 'SUCCESS',
                'reference_id' => $reference,
                'paid_at' => now(),
            ]);

            // 2. Mark order as PAID
            $order->update([
                'payment_status' => 'PAID',
                'payment_method' => $method,
                'payment_reference' => $reference,
                'amount_paid' => $amountPaid,
                'change_amount' => $change,
                'paid_at' => now(),
            ]);

            // 3. Update active CashRegisterSession financial summary
            $session = CashRegisterSession::whereHas('register', fn ($q) => $q->where('shop_id', $order->shop_id))
                ->where('status', 'OPEN')
                ->latest()
                ->first();

            if ($session) {
                match (strtoupper($method)) {
                    'CASH' => $session->increment('total_cash_sales', $order->grand_total),
                    'QRIS' => $session->increment('total_qris_sales', $order->grand_total),
                    default => $session->increment('total_other_sales', $order->grand_total),
                };
            }

            return $payment;
        });
    }
}
