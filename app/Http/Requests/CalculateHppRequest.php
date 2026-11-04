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

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'recipes' => 'Resep bahan baku',
            'recipes.*.raw_material_id' => 'Bahan baku',
            'recipes.*.amount' => 'Jumlah takaran',
            'production_quantity' => 'Jumlah hasil produksi',
            'production_unit' => 'Satuan output',
            'labor_cost' => 'Biaya tenaga kerja',
            'overhead_cost' => 'Biaya overhead',
            'selling_price' => 'Simulasi harga jual',
            'target_margin' => 'Target margin',
            'target_markup' => 'Target markup',
            'tax_rate' => 'Tarif pajak',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'recipes.required' => 'Minimal tambahkan 1 bahan baku ke dalam resep.',
            'recipes.min' => 'Minimal tambahkan 1 bahan baku ke dalam resep.',
            'recipes.*.raw_material_id.required' => 'Bahan baku pada resep wajib dipilih.',
            'recipes.*.raw_material_id.integer' => 'Bahan baku yang dipilih tidak valid.',
            'recipes.*.raw_material_id.distinct' => 'Bahan baku tidak boleh dipilih lebih dari sekali.',
            'recipes.*.raw_material_id.exists' => 'Bahan baku yang dipilih tidak ditemukan dalam inventori toko.',
            'recipes.*.amount.required' => 'Jumlah takaran bahan wajib diisi.',
            'recipes.*.amount.min' => 'Jumlah takaran bahan minimal lebih dari 0.',
            'selling_price.required' => 'Simulasi harga jual per unit wajib diisi.',
            'production_quantity.required' => 'Jumlah hasil produksi wajib diisi.',
        ];
    }
}
