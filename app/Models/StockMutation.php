<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMutation extends Model
{
    use BelongsToShop, HasFactory;

    protected $fillable = [
        'shop_id',
        'raw_material_id',
        'type',
        'amount', 'unit',
        'balance_after',
        'reference_type',
        'reference_id',
        'notes',
        'user_id',
    ];

    protected $appends = ['unit_label'];

    public function getUnitLabelAttribute(): string
    {
        $unit = $this->unit ?? $this->rawMaterial?->unit;

        return $unit ? config('inventory.units.'.strtolower($unit), $unit) : '—';
    }

    protected $casts = [
        'amount' => 'decimal:6',
        'balance_after' => 'decimal:6',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
