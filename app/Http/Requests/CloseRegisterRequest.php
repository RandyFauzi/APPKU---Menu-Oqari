<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->shop_id && $this->user()->can('manage-register');
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('actual_cash') && is_string($this->actual_cash)) {
            $val = trim($this->actual_cash);
            if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $val)) {
                $val = str_replace('.', '', $val);
                $val = str_replace(',', '.', $val);
                $this->merge(['actual_cash' => $val]);
            } elseif (preg_match('/^\d+,\d+$/', $val)) {
                $val = str_replace(',', '.', $val);
                $this->merge(['actual_cash' => $val]);
            }
        }
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'actual_cash' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:999999999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'session_id' => ['nullable', 'integer'],
            'expected_cash_confirmation' => ['nullable', 'numeric', 'decimal:0,2'],
            'difference' => ['prohibited'], 'expected_cash' => ['prohibited'], 'reconciliation_status' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['actual_cash.required' => 'Masukkan hasil hitung uang tunai fisik.', 'actual_cash.min' => 'Kas fisik tidak boleh negatif.'];
    }
}
