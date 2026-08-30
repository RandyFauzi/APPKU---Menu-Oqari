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
        if (! $order->relationLoaded('items')) {
            $order->load('items.product');
        } $this->order = $order;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('shop.'.$this->order->shop_id.'.orders')];
    }
}
