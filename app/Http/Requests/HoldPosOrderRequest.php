<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HoldPosOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('access-pos') ?? false;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.id' => ['required', 'integer'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:100'],
            'items.*.variant_id' => ['nullable', 'integer'],
            'items.*.modifiers' => ['nullable', 'array', 'max:20'],
            'items.*.modifiers.*' => ['integer', 'distinct'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
            'table' => ['nullable', 'string', 'max:50'],
            'customer_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'items.*.notes.max' => 'Catatan produk maksimal 255 karakter.',
            'items.*.notes.string' => 'Catatan produk harus berupa teks.',
        ];
    }
}
