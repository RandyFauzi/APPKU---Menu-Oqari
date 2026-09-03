<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptDelivery extends Model
{
    protected $fillable = [
        'order_id',
        'shop_id',
        'channel',
        'destination',
        'status',
        'attempts',
        'provider_message_id',
        'failure_reason',
        'sent_at',
        'failed_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
