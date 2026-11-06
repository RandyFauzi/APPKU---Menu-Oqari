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
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'photo_base64' => ['nullable', 'string'],
            'user_id' => ['prohibited'], 'shop_id' => ['prohibited'],
            'checked_in_at' => ['prohibited'], 'checked_out_at' => ['prohibited'],
            'work_date' => ['prohibited'], 'crew_shift_id' => ['prohibited'],
            'distance_meters' => ['prohibited'], 'in_radius' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'type.required' => 'Jenis absensi (masuk/pulang) wajib dipilih.',
            'type.in' => 'Jenis absensi tidak valid.',
            'latitude.between' => 'Koordinat latitude tidak valid (-90 s/d 90).',
            'longitude.between' => 'Koordinat longitude tidak valid (-180 s/d 180).',
            'photo.image' => 'File foto harus berupa file gambar valid.',
            'photo.mimes' => 'Format foto harus berupa JPG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto maksimal 5MB.',
        ];
    }
}
