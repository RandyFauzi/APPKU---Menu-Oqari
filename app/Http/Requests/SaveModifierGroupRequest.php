<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveModifierGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->shop_id && $this->user()->can('manage-menu'));
    }

    protected function prepareForValidation(): void
    {
        $selectionType = strtoupper((string) ($this->input('selection_type') ?? 'MULTIPLE'));
        $isRequired = filter_var($this->input('is_required'), FILTER_VALIDATE_BOOLEAN);
        $isActive = $this->has('is_active') ? filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN) : true;

        $maxSelections = $this->input('max_selections');
        if ($selectionType === 'SINGLE') {
            $maxSelections = 1;
            $minSelections = $isRequired ? 1 : 0;
        } else {
            $maxSelections = (! empty($maxSelections) && is_numeric($maxSelections)) ? (int) $maxSelections : null;
            $minSelections = $isRequired ? 1 : 0;
        }

        $items = $this->input('items');
        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        $productIds = $this->input('product_ids');
        if (is_string($productIds)) {
            $productIds = json_decode($productIds, true);
        }

        $this->merge([
            'selection_type' => $selectionType,
            'is_required' => $isRequired,
            'is_active' => $isActive,
            'min_selections' => $minSelections,
            'max_selections' => $maxSelections,
            'items' => is_array($items) ? $items : [],
            'product_ids' => is_array($productIds) ? $productIds : [],
        ]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:100'],
            'selection_type' => ['required', 'string', 'in:SINGLE,MULTIPLE'],
            'category_target' => ['nullable', 'string', 'in:all,beverage,food'],
            'is_required' => ['boolean'],
            'min_selections' => ['integer', 'min:0'],
            'max_selections' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.name' => ['required', 'string', 'max:100'],
            'items.*.price_adjustment' => ['required', 'numeric', 'min:0'],
            'items.*.cogs_adjustment' => ['nullable', 'numeric', 'min:0'],
            'items.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama grup addon wajib diisi.',
            'items.required' => 'Daftar pilihan addon minimal 1 item.',
            'items.min' => 'Daftar pilihan addon minimal 1 item.',
            'items.*.name.required' => 'Nama pilihan addon wajib diisi.',
            'items.*.price_adjustment.required' => 'Harga addon wajib diisi (isi 0 jika gratis).',
        ];
    }
}
