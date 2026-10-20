<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use BelongsToShop, HasFactory;

    protected $fillable = [
        'shop_id', 'user_id', 'employee_name', 'crew_shift_id', 'work_date', 'timezone',
        'scheduled_start_at', 'scheduled_end_at', 'checked_in_at', 'checked_out_at',
        'late_minutes', 'early_leave_minutes', 'worked_minutes', 'check_in_note', 'check_out_note',
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
        ];
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
