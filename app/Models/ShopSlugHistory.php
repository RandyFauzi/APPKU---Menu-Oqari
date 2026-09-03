<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSlugHistory extends Model
{
    protected $fillable = ['shop_id', 'old_slug'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
