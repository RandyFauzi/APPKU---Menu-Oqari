<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToShop;
class Product extends Model
{
    use BelongsToShop;
    protected $fillable = [
        'shop_id',
        'category_name',
        'name',
        'description',
        'price',
        'image_url',
        'is_sold_out',
    ];

    public function shop()
    {
    use BelongsToShop;
        return $this->belongsTo(Shop::class);
    }
}

