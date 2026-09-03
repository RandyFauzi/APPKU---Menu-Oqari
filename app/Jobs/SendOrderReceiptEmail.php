<?php

namespace App\Jobs;

use App\Mail\OrderReceiptMail;
use App\Models\Order;
use App\Models\ReceiptDelivery;
use App\Services\Receipt\ReceiptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendOrderReceiptEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $orderId)
    {
    }

    public function handle(ReceiptService $receiptService): void
    {
        $order = Order::with('shop')->find($this->orderId);
        
        if (!$order || !$order->customer_email) {
            return;
        }

        // Prevent duplicate sending if already sent
        $existingDelivery = ReceiptDelivery::where('order_id', $order->id)
            ->where('channel', 'email')
            ->where('status', 'sent')
            ->first();

        if ($existingDelivery) {
            return; // Already sent, abort retry
        }

        $delivery = ReceiptDelivery::create([
            'order_id' => $order->id,
            'shop_id' => $order->shop_id,
            'channel' => 'email',
            'destination' => $order->customer_email,
            'status' => 'processing',
            'attempts' => 1,
        ]);

        try {
            Mail::to($order->customer_email)->send(new OrderReceiptMail($receiptData = $receiptService->build($order)));
            
            $delivery->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (Throwable $e) {
            $delivery->update([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_reason' => $e->getMessage(),
            ]);
        }
    }
}
