<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcastNow
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
        return [
            'id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'shop_id' => $this->order->shop_id,
            'status' => $this->order->order_status,
            'total' => $this->order->grand_total,
            'table' => $this->order->table_name ?? 'Takeaway',
            'items_count' => $this->order->items()->count(),
            'created_at' => $this->order->created_at?->toISOString(),
        ];
    }
}
