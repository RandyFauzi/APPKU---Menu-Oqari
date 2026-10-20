<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRecipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'raw_material_id',
        'amount', 'batch_amount', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:6', 'batch_amount' => 'decimal:6',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function getCostAttribute(): float
    {
        $unitCost = (float) ($this->rawMaterial->cost_per_unit ?? 0);

        return round($unitCost * (float) $this->amount, 2);
    }
}
