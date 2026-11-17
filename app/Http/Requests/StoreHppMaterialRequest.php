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
            $unit = trim($this->input('unit'));
            $canonical = strtolower($unit);
            $configUnits = config('inventory.units');

            if (array_key_exists($canonical, $configUnits)) {
                $this->merge(['unit' => $canonical]);
            } else {
                $resolved = $canonical;
                foreach ($configUnits as $key => $label) {
                    if (strtolower($label) === $canonical || str_starts_with($canonical, strtolower($key).' ')) {
                        $resolved = $key;
                        break;
                    }
                }
                $this->merge(['unit' => $resolved]);
            }
        }

        foreach (['purchase_total', 'purchase_quantity'] as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $val = trim($this->input($field));
                if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $val)) {
                    $val = str_replace('.', '', $val);
                    $val = str_replace(',', '.', $val);
                    $this->merge([$field => $val]);
                } elseif (preg_match('/^\d+,\d+$/', $val)) {
                    $val = str_replace(',', '.', $val);
                    $this->merge([$field => $val]);
                }
            }
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
