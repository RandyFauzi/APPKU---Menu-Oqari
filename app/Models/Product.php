<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
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
        return $this->belongsTo(Shop::class);
    }
}
