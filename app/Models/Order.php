<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToShop;
class Order extends Model
{
    use BelongsToShop;
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
    use BelongsToShop;
        return $this->belongsTo(Shop::class);
    }

    public function table()
    {
    use BelongsToShop;
        return $this->belongsTo(Table::class);
    }

    public function items()
    {
    use BelongsToShop;
        return $this->hasMany(OrderItem::class);
    }
}

