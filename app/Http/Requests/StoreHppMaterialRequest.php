<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHppMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->shop_id && $this->user()->can('manage-hpp') && $this->user()->can('manage-inventory');
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('unit'))) {
            $this->merge(['unit' => strtolower(trim($this->input('unit')))]);
        }
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'purchase_total' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:99999999.99'],
            'purchase_quantity' => ['required', 'numeric', 'decimal:0,4', 'min:0.0001', 'max:99999999'],
            'unit' => ['required', Rule::in(array_keys(config('inventory.units')))],
            'shop_id' => ['prohibited'], 'cost_per_unit' => ['prohibited'], 'current_stock' => ['prohibited'],
        ];
    }
}
