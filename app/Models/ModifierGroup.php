<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModifierGroup extends Model
{
    use BelongsToShop, HasFactory;

    protected $fillable = [
        'shop_id',
        'product_id',
        'name',
        'selection_type',
        'category_target',
        'is_required',
        'min_selections',
        'max_selections',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'min_selections' => 'integer',
        'max_selections' => 'integer',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_modifier_group')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function modifiers()
    {
        return $this->hasMany(Modifier::class);
    }
}
