<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use BelongsToShop, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'category_id',
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
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

    public function modifierGroups()
    {
        return $this->hasMany(ModifierGroup::class);
    }
}
}
