<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcastNow, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('shop.'.$this->order->shop_id.'.orders')];
    }

    public function broadcastAs(): string
    {
        return 'order.created';
    }

    public function broadcastWith(): array
    {
        $this->order->loadMissing(['items.product', 'table']);

        $items = $this->order->items->map(function ($item) {
            $name = $item->product_name ?? $item->product?->name ?? 'Menu';
            if (! empty($item->variant_name)) {
                $name .= ' ('.$item->variant_name.')';
            }

            return [
                'name' => $name,
                'product_name' => $name,
                'quantity' => (int) ($item->quantity ?? 1),
                'qty' => (int) ($item->quantity ?? 1),
                'price' => (float) ($item->unit_price ?? $item->subtotal ?? 0),
                'unit_price' => (float) ($item->unit_price ?? $item->subtotal ?? 0),
                'notes' => $item->notes ?? '',
            ];
        })->values()->toArray();

        $tableName = null;
        if ($this->order->table) {
            $tableName = $this->order->table->name;
            if (! str_starts_with(strtolower($tableName), 'meja')) {
                $tableName = 'Meja '.$tableName;
            }
        } elseif (! empty($this->order->table_name)) {
            $tableName = $this->order->table_name;
        } else {
            $tableName = match ($this->order->fulfillment_type) {
                'TAKEAWAY' => 'Takeaway',
                'DELIVERY' => 'Delivery',
                default => 'Dine In',
            };
        }

        $grandTotal = (float) ($this->order->grand_total ?? 0);

        return [
            'id' => $this->order->id,
            'order_number' => '#'.$this->order->id,
            'shop_id' => $this->order->shop_id,
            'customer_name' => $this->order->customer_name ?: 'Walk-in Customer',
            'table' => $tableName,
            'fulfillment_type' => $this->order->fulfillment_type ?? 'DINE_IN',
            'status' => $this->order->order_status ?? 'CONFIRMED',
            'total' => $grandTotal,
            'total_amount' => $grandTotal,
            'items_count' => count($items),
            'items' => $items,
            'created_at' => $this->order->created_at?->toISOString() ?? now()->toISOString(),
        ];
    }
}
