<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrewShift extends Model
{
    use BelongsToShop;
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'shift_template_id',
        'user_id',
        'position',
        'date',
        'start_time',
        'end_time',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function startsAt(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->date->format('Y-m-d').' '.$this->start_time, config('attendance.timezone'));
    }

    public function endsAt(): CarbonImmutable
    {
        $end = CarbonImmutable::parse($this->date->format('Y-m-d').' '.$this->end_time, config('attendance.timezone'));

        return $end->lte($this->startsAt()) ? $end->addDay() : $end;
    }

    public function durationMinutes(): int
    {
        return (int) $this->startsAt()->diffInMinutes($this->endsAt());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
