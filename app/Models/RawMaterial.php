<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterial extends Model
{
    use BelongsToShop, HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'name',
        'sku',
        'unit',
        'current_stock',
        'minimum_stock',
        'cost_per_unit',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class);
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}
