<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use BelongsToShop, HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'slug',
        'sort_order',
        'is_active',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
