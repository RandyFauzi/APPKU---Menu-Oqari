<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->shop_id && $this->user()->can('record-attendance');
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:check_in,check_out'],
            'note' => ['nullable', 'string', 'max:500'],
            'user_id' => ['prohibited'], 'shop_id' => ['prohibited'],
            'checked_in_at' => ['prohibited'], 'checked_out_at' => ['prohibited'],
            'work_date' => ['prohibited'], 'crew_shift_id' => ['prohibited'],
        ];
    }
}
