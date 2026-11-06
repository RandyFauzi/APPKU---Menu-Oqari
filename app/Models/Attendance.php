<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Attendance extends Model
{
    use BelongsToShop, HasFactory;

    protected $fillable = [
        'shop_id', 'user_id', 'employee_name', 'crew_shift_id', 'work_date', 'timezone',
        'scheduled_start_at', 'scheduled_end_at', 'checked_in_at', 'checked_out_at',
        'late_minutes', 'early_leave_minutes', 'worked_minutes', 'check_in_note', 'check_out_note',
        'check_in_photo_path', 'check_in_latitude', 'check_in_longitude', 'check_in_accuracy', 'check_in_distance_meters', 'check_in_in_radius',
        'check_out_photo_path', 'check_out_latitude', 'check_out_longitude', 'check_out_accuracy', 'check_out_distance_meters', 'check_out_in_radius',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'immutable_date:Y-m-d',
            'scheduled_start_at' => 'immutable_datetime',
            'scheduled_end_at' => 'immutable_datetime',
            'checked_in_at' => 'immutable_datetime',
            'checked_out_at' => 'immutable_datetime',
            'late_minutes' => 'integer', 'early_leave_minutes' => 'integer', 'worked_minutes' => 'integer',
            'check_in_latitude' => 'float', 'check_in_longitude' => 'float', 'check_in_accuracy' => 'float',
            'check_in_distance_meters' => 'integer', 'check_in_in_radius' => 'boolean',
            'check_out_latitude' => 'float', 'check_out_longitude' => 'float', 'check_out_accuracy' => 'float',
            'check_out_distance_meters' => 'integer', 'check_out_in_radius' => 'boolean',
        ];
    }

    public function getCheckInPhotoUrlAttribute(): ?string
    {
        return $this->check_in_photo_path ? Storage::disk('public')->url($this->check_in_photo_path) : null;
    }

    public function getCheckOutPhotoUrlAttribute(): ?string
    {
        return $this->check_out_photo_path ? Storage::disk('public')->url($this->check_out_photo_path) : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CrewShift::class, 'crew_shift_id');
    }
}
