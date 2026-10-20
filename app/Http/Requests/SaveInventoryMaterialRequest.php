<?php

namespace App\Http\Requests;

use App\Models\RawMaterial;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveInventoryMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->shop_id && $this->user()->can('manage-inventory');
    }

    protected function prepareForValidation(): void
    {
        foreach (['current_stock', 'minimum_stock', 'cost_per_unit'] as $field) {
            $value = $this->input($field);
            if (is_float($value) && is_finite($value) && str_contains(strtolower((string) $value), 'e')) {
                $decimal = sprintf('%.12F', $value);
                if ((float) $decimal === $value) {
                    $this->merge([$field => rtrim(rtrim($decimal, '0'), '.')]);
                }
            }
        }
        if (is_string($this->input('unit'))) {
            $unit = trim($this->input('unit'));
            $canonical = strtolower($unit);
            $this->merge(['unit' => array_key_exists($canonical, config('inventory.units')) ? $canonical : $unit]);
        }
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $units = array_keys(config('inventory.units'));
        if (is_numeric($this->input('id'))) {
            $existing = RawMaterial::where('shop_id', $this->user()->shop_id)->find($this->input('id'));
            if ($existing) {
                $units[] = $existing->unit;
            }
        }

        return [
            'id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'], 'sku' => ['nullable', 'string', 'max:100'],
            'unit' => ['required', 'string', Rule::in($units)],
            'current_stock' => $this->filled('id') ? ['exclude'] : ['required', 'numeric', 'decimal:0,6', 'min:0', 'max:999999999.999999'],
            'minimum_stock' => ['required', 'numeric', 'decimal:0,6', 'min:0', 'max:999999999.999999'],
            'cost_per_unit' => ['required', 'numeric', 'decimal:0,6', 'min:0', 'max:999999999.999999'],
            'shop_id' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['unit.in' => 'Pilih satuan stok yang tersedia.', 'unit.required' => 'Satuan stok wajib dipilih.'];
    }
}
