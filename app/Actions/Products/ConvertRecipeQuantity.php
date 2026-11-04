<?php

namespace App\Actions\Products;

use Illuminate\Validation\ValidationException;

class ConvertRecipeQuantity
{
    public function execute(float $amount, ?string $from, string $to): float
    {
        $from = strtolower(trim($from ?: $to));
        $to = strtolower(trim($to));
        $units = [
            'g' => ['mass', 1], 'gram' => ['mass', 1], 'kg' => ['mass', 1000],
            'ml' => ['volume', 1], 'l' => ['volume', 1000], 'liter' => ['volume', 1000],
            'pcs' => ['count', 1], 'cup' => ['count', 1],
        ];
        if ($from === $to) {
            return $amount;
        }
        if (! isset($units[$from], $units[$to]) || $units[$from][0] !== $units[$to][0]) {
            throw ValidationException::withMessages(['recipes' => 'Satuan resep harus sesuai bahan. Konversi hanya tersedia untuk gram/kg, ml/liter, dan satuan per item (pcs/cup); kemasan karton dihitung dalam satuan stoknya.']);
        }

        $normalized = round($amount * $units[$from][1] / $units[$to][1], 6);
        if ($normalized <= 0) {
            throw ValidationException::withMessages(['recipes' => 'Takaran terlalu kecil setelah konversi. Gunakan satuan stok yang lebih kecil.']);
        }

        return $normalized;
    }
}
