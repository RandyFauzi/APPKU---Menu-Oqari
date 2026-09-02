<?php

namespace App\Actions\Order;

use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Cancels a confirmed or pending order.
 * Financial snapshot is immutable — we mark as CANCELLED and log reason.
 * Does NOT hard-delete anything.
 */
class CancelOrder
{
    /**
     * @throws Exception
     */
    public function execute(Order $order, User $cancelledBy, string $reason): Order
    {
        if (! in_array($order->order_status, ['CONFIRMED', 'PREPARING'])) {
            throw new Exception("Pesanan tidak dapat dibatalkan karena statusnya: {$order->order_status}");
        }

        if ($order->payment_status === 'PAID') {
            throw new Exception('Pesanan sudah dibayar. Gunakan Refund untuk pembatalan.');
        }

        return DB::transaction(function () use ($order, $cancelledBy, $reason) {
            $order->update([
                'order_status' => 'CANCELLED',
                'payment_status' => 'FAILED',
                'cancelled_at' => now(),
                'cancelled_by' => $cancelledBy->id,
                'cancellation_reason' => $reason,
            ]);

            // TODO: Trigger stock rollback via Inventory\AdjustStock if stock tracking is enabled

            return $order->fresh();
        });
    }
}
