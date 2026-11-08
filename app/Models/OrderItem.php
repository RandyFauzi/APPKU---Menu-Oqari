<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'price',
        'product_name',
        'discount_amount',
        'tax_amount',
        'subtotal',
        'notes',
        'variant_id',
        'variant_name',
        'modifiers',
        'cogs_total',
        'serving_type',
    ];

    protected static function booted(): void
    {
        static::creating(function (OrderItem $item): void {
            if (Schema::hasColumn('order_items', 'price')) {
                if (isset($item->unit_price) && ! isset($item->price)) {
                    $item->price = $item->unit_price;
                }
            } else {
                unset($item->attributes['price']);
            }
        });
    }

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
