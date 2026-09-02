<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Model;

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
    
        'cogs',];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function modifierGroups()
    {
        return $this->hasMany(ModifierGroup::class);
    }
}
}
