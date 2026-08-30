<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'shop_id',
        'table_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'status',
        'payment_method',
        'total_price',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
