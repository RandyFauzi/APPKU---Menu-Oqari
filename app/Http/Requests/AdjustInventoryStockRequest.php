<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustInventoryStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->shop_id && $this->user()->can('manage-inventory');
    }

    protected function prepareForValidation(): void
    {
        foreach (['amount', 'new_cost_per_unit'] as $field) {
            $value = $this->input($field);
            if (is_float($value) && is_finite($value) && str_contains(strtolower((string) $value), 'e')) {
                $decimal = sprintf('%.12F', $value);
                if ((float) $decimal === $value) {
                    $this->merge([$field => rtrim(rtrim($decimal, '0'), '.')]);
                }
            }
        }
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'raw_material_id' => ['required', 'integer'], 'type' => ['required', 'in:in,adjustment,waste'],
            'amount' => ['required', 'numeric', 'decimal:0,6', $this->input('type') === 'adjustment' ? 'min:0' : 'min:0.000001', 'max:999999999.999999'],
            'notes' => ['nullable', 'string', 'max:255'],
            'new_cost_per_unit' => ['nullable', 'numeric', 'decimal:0,6', 'min:0', 'max:999999999.999999'],
            'unit' => ['prohibited'], 'shop_id' => ['prohibited'],
        ];
    }
}
