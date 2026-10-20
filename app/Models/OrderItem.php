<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'product_name',
        'discount_amount',
        'tax_amount',
        'subtotal',
        'notes',
        'variant_id',
        'variant_name',
        'modifiers',

        'cogs_total', ];

    protected $casts = [
        'modifiers' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
