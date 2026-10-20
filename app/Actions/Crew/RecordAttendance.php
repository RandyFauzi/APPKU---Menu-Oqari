<?php

namespace App\Actions\Crew;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\CrewShift;
use App\Models\Shop;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecordAttendance
{
    public function execute(User $employee, string $type, ?string $note = null): Attendance
    {
        return DB::transaction(function () use ($employee, $type, $note): Attendance {
            $employee = User::whereKey($employee->id)->lockForUpdate()->firstOrFail();
            $shop = Shop::whereKey($employee->shop_id)->firstOrFail();
            abort_if($shop->isSuspended(), 403, 'Toko dinonaktifkan.');
            $now = CarbonImmutable::now('UTC');
            $query = Attendance::where('shop_id', $shop->id)->where('user_id', $employee->id);
            $active = (clone $query)->whereNull('checked_out_at')->lockForUpdate()->first();

            if ($type === 'check_out') {
                if (! $active) {
                    throw ValidationException::withMessages(['type' => 'Tidak ada absensi masuk yang belum ditutup.']);
                }
                if ($now->lt($active->checked_in_at)) {
                    throw ValidationException::withMessages(['type' => 'Waktu pulang tidak boleh sebelum waktu masuk.']);
                }
                $active->update([
                    'checked_out_at' => $now,
                    'worked_minutes' => (int) floor($active->checked_in_at->diffInMinutes($now)),
                    'early_leave_minutes' => $active->scheduled_end_at
                        ? (int) ceil(max(0, $now->diffInMinutes($active->scheduled_end_at, false))) : 0,
                    'check_out_note' => $note,
                ]);
                $attendance = $active;
            } else {
                if ($active) {
                    throw ValidationException::withMessages(['type' => 'Anda masih tercatat masuk. Absen pulang terlebih dahulu.']);
                }
                $timezone = config('attendance.timezone');
                $localNow = $now->setTimezone($timezone);
                $shift = $this->resolveShift($employee, $localNow);
                $workDate = $shift?->date->format('Y-m-d') ?? $localNow->toDateString();
                if ((clone $query)->whereDate('work_date', $workDate)->exists()) {
                    throw ValidationException::withMessages(['type' => 'Absensi untuk hari kerja ini sudah tercatat.']);
                }
                $start = $shift ? CarbonImmutable::parse($workDate.' '.$shift->start_time, $timezone) : null;
                $end = $shift ? CarbonImmutable::parse($workDate.' '.$shift->end_time, $timezone) : null;
                if ($end && $end->lte($start)) {
                    $end = $end->addDay();
                }
                $attendance = Attendance::create([
                    'shop_id' => $shop->id, 'user_id' => $employee->id, 'crew_shift_id' => $shift?->id,
                    'employee_name' => $employee->name,
                    'work_date' => $workDate, 'timezone' => $timezone,
                    'scheduled_start_at' => $start?->utc(), 'scheduled_end_at' => $end?->utc(),
                    'checked_in_at' => $now,
                    'late_minutes' => $start ? (int) ceil(max(0, $start->diffInMinutes($localNow, false) - max(0, config('attendance.grace_minutes')))) : 0,
                    'check_in_note' => $note,
                ]);
            }

            ActivityLog::create([
                'shop_id' => $shop->id, 'user_id' => $employee->id,
                'action' => 'attendance.'.$type, 'entity_type' => Attendance::class, 'entity_id' => $attendance->id,
                'description' => $type === 'check_in' ? 'Absensi masuk' : 'Absensi pulang',
                'new_values' => ['attendance_id' => $attendance->id, 'recorded_at' => $now->toIso8601String()],
            ]);

            return $attendance;
        });
    }

    private function resolveShift(User $employee, CarbonImmutable $localNow): ?CrewShift
    {
        $shifts = CrewShift::where('shop_id', $employee->shop_id)->where('user_id', $employee->id)
            ->whereBetween('date', [$localNow->subDay()->startOfDay(), $localNow->endOfDay()])
            ->orderBy('date')->orderBy('start_time')->get();
        foreach ($shifts as $shift) {
            $date = $shift->date->format('Y-m-d');
            $start = CarbonImmutable::parse($date.' '.$shift->start_time, $localNow->timezone);
            $end = CarbonImmutable::parse($date.' '.$shift->end_time, $localNow->timezone);
            if ($end->lte($start)) {
                $end = $end->addDay();
            }
            if ($localNow->betweenIncluded($start, $end)) {
                return $shift;
            }
        }

        return $shifts->first(fn (CrewShift $shift): bool => $shift->date->format('Y-m-d') === $localNow->toDateString());
    }
}
