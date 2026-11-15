<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReplicateMonthShiftsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) ($this->user()?->shop_id && $this->user()->can('manage-crew'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $shopId = (int) $this->user()?->shop_id;

        return [
            'source_month' => ['required', 'date_format:Y-m'],
            'target_months' => ['required', 'array', 'min:1', 'max:24'],
            'target_months.*' => ['required', 'date_format:Y-m', 'different:source_month'],
            'strategy' => ['nullable', 'in:date,day_of_week'],
            'overwrite' => ['nullable', 'boolean'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => [
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('shop_id', $shopId)->whereNull('archived_at')),
            ],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'source_month.required' => 'Bulan sumber jadwal wajib dipilih.',
            'source_month.date_format' => 'Format bulan sumber tidak valid (YYYY-MM).',
            'target_months.required' => 'Pilih minimal satu bulan target.',
            'target_months.min' => 'Pilih minimal satu bulan target.',
            'target_months.*.different' => 'Bulan target tidak boleh sama dengan bulan sumber.',
            'target_months.*.date_format' => 'Format bulan target tidak valid (YYYY-MM).',
            'strategy.in' => 'Strategi replikasi tidak valid.',
            'user_ids.*.exists' => 'Staf yang dipilih tidak valid atau bukan staf kafe Anda.',
        ];
    }
}
