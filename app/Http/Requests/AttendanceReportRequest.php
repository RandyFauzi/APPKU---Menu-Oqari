<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->shop_id && $this->user()->can('view-attendance');
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'month' => ['nullable', 'date_format:Y-m'],
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('shop_id', $this->user()->shop_id)],
            'status' => ['nullable', 'in:late,on_time,unscheduled,active,completed'],
        ];
    }
}
