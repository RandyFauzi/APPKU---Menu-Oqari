<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! $this->user()?->shop_id) {
            return false;
        }

        return $this->isTeamReport()
            ? $this->user()->role === 'owner' && $this->user()->can('view-attendance')
            : $this->user()->can('record-attendance');
    }

    public function isTeamReport(): bool
    {
        return $this->routeIs('admin.attendance.report') || $this->input('tab') === 'team';
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $rules = [
            'month' => ['nullable', 'date_format:Y-m'],
            'tab' => ['nullable', 'in:mine,team'],
        ];

        if (! $this->isTeamReport()) {
            return $rules;
        }

        return $rules + [
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('shop_id', $this->user()->shop_id)],
            'status' => ['nullable', 'in:late,on_time,unscheduled,active,completed'],
        ];
    }
}
