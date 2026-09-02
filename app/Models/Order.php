<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'shop_id',
        'table_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'payment_method',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'service_charge_amount',
        'rounding',
        'grand_total',
        'amount_paid',
        'change_amount',
        'order_status',
        'payment_status',
        'fulfillment_type',
        'payment_reference',
        'paid_at',
    
        'total_cogs',];

    protected $casts = [
        'paid_at' => 'datetime',
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

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
