<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name', 'price_adjustment', 'is_active'
        'cogs_adjustment',];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
