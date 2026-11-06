<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    use BelongsToShop, HasFactory;

    protected $fillable = [
        'shop_id',
        'code',
        'name',
        'discount_type',
        'discount_value',
        'min_spend',
        'max_discount',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'float',
        'min_spend' => 'float',
        'max_discount' => 'float',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Scope query to only active and non-expired vouchers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    /**
     * Check if this voucher is valid for a given subtotal.
     *
     * @return array{valid: bool, message: ?string}
     */
    public function validateForSubtotal(float $subtotal): array
    {
        if (! $this->is_active) {
            return ['valid' => false, 'message' => 'Voucher ini sedang tidak aktif.'];
        }

        if ($this->expires_at && now()->isAfter($this->expires_at)) {
            return ['valid' => false, 'message' => 'Voucher ini sudah kadaluarsa.'];
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Batas kuota penggunaan voucher telah habis.'];
        }

        if ($this->min_spend > 0 && $subtotal < $this->min_spend) {
            return [
                'valid' => false,
                'message' => 'Minimal belanja untuk voucher ini adalah Rp '.number_format($this->min_spend, 0, ',', '.').'.',
            ];
        }

        return ['valid' => true, 'message' => null];
    }

    /**
     * Calculate discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0.0;
        }

        if ($this->discount_type === 'percentage') {
            $discount = ($subtotal * $this->discount_value) / 100;
            if ($this->max_discount !== null && $this->max_discount > 0) {
                $discount = min($discount, $this->max_discount);
            }
        } else {
            $discount = $this->discount_value;
        }

        return (float) min($subtotal, max(0, $discount));
    }
}
