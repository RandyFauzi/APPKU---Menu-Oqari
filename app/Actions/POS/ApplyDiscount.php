<?php

namespace App\Actions\POS;

use Exception;

/**
 * Applies a discount rule to an already-calculated cart.
 *
 * Formula:
 *   gross_sales (subtotal)
 *   - discount_amount
 *   = net_sales_before_tax
 *   + tax_amount
 *   + service_charge_amount
 *   = grand_total
 *
 * This action only handles the discount injection step.
 * Tax and service charge are applied downstream (CreatePosOrder).
 */
class ApplyDiscount
{
    /**
     * @param  array  $cart  Output of CalculateCart::execute()
     * @return array Same cart shape with discount_amount and net_sales injected
     */
    public function execute(array $cart, ?string $promoCode, int $shopId): array
    {
        $discountAmount = 0;

        if ($promoCode) {
            // TODO: resolve Promotion model once implemented
            // $promo = Promotion::where('shop_id', $shopId)
            //     ->where('code', $promoCode)
            //     ->where('is_active', true)
            //     ->where('valid_until', '>=', now())
            //     ->first();
            //
            // if (!$promo) throw new Exception("Kode promo tidak valid atau sudah kadaluarsa.");
            //
            // $discountAmount = $promo->type === 'percent'
            //     ? $cart['subtotal'] * ($promo->value / 100)
            //     : $promo->value;

            throw new Exception('Promo code system belum diaktifkan.');
        }

        $netSales = $cart['subtotal'] - $discountAmount;

        return array_merge($cart, [
            'discount_amount' => $discountAmount,
            'net_sales' => $netSales,
            'promo_code' => $promoCode,
        ]);
    }
}
