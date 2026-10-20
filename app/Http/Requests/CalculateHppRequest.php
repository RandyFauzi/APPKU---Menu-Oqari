<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CalculateHppRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->shop_id && $this->user()->can('manage-hpp');
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'recipes' => ['required', 'array', 'min:1', 'max:100'],
            'recipes.*' => ['array:raw_material_id,amount,unit,notes'],
            'recipes.*.raw_material_id' => ['required', 'integer', 'distinct', Rule::exists('raw_materials', 'id')
                ->where('shop_id', $this->user()->shop_id)->whereNull('deleted_at')],
            'recipes.*.amount' => ['required', 'numeric', 'decimal:0,6', 'min:0.000001', 'max:999999.99'],
            'recipes.*.unit' => ['nullable', 'string', 'max:30'],
            'recipes.*.notes' => ['nullable', 'string', 'max:500'],
            'production_quantity' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0.01', 'max:999999'],
            'production_unit' => ['sometimes', 'in:porsi,pcs,gram,kg,ml,liter,botol,pack,dus'],
            'labor_cost' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99'],
            'overhead_mode' => ['sometimes', 'in:fixed,percentage'],
            'overhead_percent' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0', 'max:100'],
            'pricing_method' => ['sometimes', 'in:margin,markup'],
            'target_markup' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'max:1000'],
            'tax_rate' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0', 'max:100'],
            'tax_mode' => ['sometimes', 'in:exclusive,inclusive'],
            'overhead_cost' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99'],
            'selling_price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99'],
            'target_margin' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'max:99.99'],
            'cogs' => ['prohibited'], 'shop_id' => ['prohibited'],
        ];
    }
}
