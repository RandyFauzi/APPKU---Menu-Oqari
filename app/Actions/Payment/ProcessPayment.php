<?php

namespace App\Actions\Payment;

use App\Jobs\SendOrderReceiptEmail;
use App\Models\CashRegisterSession;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
     */
    public function execute(Order $order, string $method, float $amountPaid, ?string $reference = null): Payment
    {
        return DB::transaction(function () use ($order, $method, $amountPaid, $reference) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($order->payment_status === 'PAID') {
                throw ValidationException::withMessages(['amount_paid' => 'Pesanan sudah dibayar.']);
            }
            if ($amountPaid < $order->grand_total) {
                throw ValidationException::withMessages(['amount_paid' => 'Jumlah pembayaran kurang.']);
            }

            $session = CashRegisterSession::where('user_id', auth()->id())
                ->whereHas('register', fn ($query) => $query->where('shop_id', $order->shop_id))
                ->where('status', 'OPEN')->lockForUpdate()->first();
            if (! $session) {
                throw ValidationException::withMessages(['amount_paid' => 'Buka shift kasir Anda sebelum menerima pembayaran.']);
            }
            $change = $amountPaid - $order->grand_total;

            // 1. Create Payment record
            $payment = Payment::create([
                'shop_id' => $order->shop_id,
                'order_id' => $order->id,
                'payment_method' => $method,
                'amount' => $order->grand_total,
                'reference_number' => $reference,
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

            match (strtoupper($method)) {
                'CASH' => $session->increment('total_cash_sales', $order->grand_total),
                'QRIS' => $session->increment('total_qris_sales', $order->grand_total),
                default => $session->increment('total_other_sales', $order->grand_total),
            };

            if ($order->customer_email) {
                DB::afterCommit(function () use ($order) {
                    dispatch(new SendOrderReceiptEmail($order->id));
                });
            }

            return $payment;
        });
    }
}
