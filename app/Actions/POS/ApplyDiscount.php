<?php

namespace App\Actions\POS;

use App\Models\Shop;
use App\Models\Voucher;
use Illuminate\Validation\ValidationException;

/**
 * Applies voucher or manual/shortcut discount to an already-calculated cart.
 *
 * Formula:
 *   gross_sales (subtotal)
 *   - discount_amount
 *   = net_sales
 */
class ApplyDiscount
{
    /**
     * @param  array  $cart  Output of CalculateCart::execute()
     * @param  array<string, mixed>  $discountData  Data containing voucher_code, discount_type, discount_value, discount_amount, discount_reason
     * @return array Same cart shape with discount_amount, net_sales, voucher_code, discount_reason, and voucher
     *
     * @throws ValidationException
     */
    public function execute(Shop $shop, array $cart, array $discountData = []): array
    {
        $subtotal = (float) ($cart['subtotal'] ?? 0);
        $discountAmount = 0.0;
        $voucherCode = ! empty($discountData['voucher_code']) ? trim((string) $discountData['voucher_code']) : null;
        $discountReason = ! empty($discountData['discount_reason']) ? trim((string) $discountData['discount_reason']) : null;
        $voucherModel = null;

        // 1. Voucher validation & calculation
        if ($voucherCode) {
            $voucherModel = Voucher::where('shop_id', $shop->id)
                ->where('code', $voucherCode)
                ->first();

            if (! $voucherModel) {
                throw ValidationException::withMessages(['voucher_code' => 'Kode voucher "'.$voucherCode.'" tidak ditemukan atau tidak valid.']);
            }

            $validation = $voucherModel->validateForSubtotal($subtotal);
            if (! $validation['valid']) {
                throw ValidationException::withMessages(['voucher_code' => $validation['message']]);
            }

            $discountAmount = $voucherModel->calculateDiscount($subtotal);
            if (! $discountReason) {
                $discountReason = $voucherModel->name;
            }
        } elseif (! empty($discountData['discount_type']) && isset($discountData['discount_value'])) {
            // 2. Shortcut preset or manual discount
            $type = $discountData['discount_type']; // 'percentage' or 'fixed'
            $val = (float) $discountData['discount_value'];

            if ($val > 0) {
                if ($type === 'percentage') {
                    $discountAmount = ($subtotal * $val) / 100;
                    if (! empty($discountData['max_discount']) && (float) $discountData['max_discount'] > 0) {
                        $discountAmount = min($discountAmount, (float) $discountData['max_discount']);
                    }
                } else {
                    $discountAmount = $val;
                }

                $discountAmount = min($discountAmount, $subtotal);
            }
        } elseif (! empty($discountData['discount_amount'])) {
            // 3. Direct nominal discount amount provided
            $directAmount = (float) $discountData['discount_amount'];
            $discountAmount = min(max(0, $directAmount), $subtotal);
        }

        $discountAmount = round(max(0, $discountAmount), 2);
        $netSales = max(0, $subtotal - $discountAmount);

        return array_merge($cart, [
            'discount_amount' => $discountAmount,
            'net_sales' => $netSales,
            'voucher_code' => $voucherCode,
            'discount_reason' => $discountReason,
            'voucher' => $voucherModel,
        ]);
    }
}
