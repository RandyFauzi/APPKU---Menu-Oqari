<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->shop_id && $this->user()->role === 'owner';
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'attendance_radius_meters' => ['required', 'integer', 'min:10', 'max:5000'],
            'attendance_require_photo' => ['required', 'boolean'],
            'attendance_require_gps' => ['required', 'boolean'],
            'attendance_strict_location' => ['required', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'latitude.between' => 'Koordinat latitude tidak valid (-90 s/d 90).',
            'longitude.between' => 'Koordinat longitude tidak valid (-180 s/d 180).',
            'attendance_radius_meters.required' => 'Radius absensi wajib ditentukan.',
            'attendance_radius_meters.min' => 'Radius absensi minimal 10 meter.',
            'attendance_radius_meters.max' => 'Radius absensi maksimal 5000 meter.',
        ];
    }
}
