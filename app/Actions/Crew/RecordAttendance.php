<?php

namespace App\Actions\Crew;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\CrewShift;
use App\Models\Shop;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RecordAttendance
{
    public function execute(
        User $employee,
        string $type,
        ?string $note = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?float $accuracy = null,
        mixed $photo = null,
    ): Attendance {
        return DB::transaction(function () use ($employee, $type, $note, $latitude, $longitude, $accuracy, $photo): Attendance {
            $employee = User::whereKey($employee->id)->lockForUpdate()->firstOrFail();
            abort_unless($employee->isAccountActive() && $employee->can('record-attendance'), 403);
            $shop = Shop::whereKey($employee->shop_id)->firstOrFail();
            abort_if($shop->isSuspended(), 403, 'Toko dinonaktifkan.');

            // 1. Process Photo Upload (if provided or required)
            $photoPath = $this->handlePhotoUpload($shop, $photo);
            if ($shop->attendance_require_photo && empty($photoPath)) {
                throw ValidationException::withMessages([
                    'photo' => 'Foto selfie bukti kehadiran wajib diambil sebelum mencatat absensi.',
                ]);
            }

            // 2. Validate GPS coordinates & Radius
            [$distanceMeters, $inRadius] = $this->validateLocation($shop, $latitude, $longitude);

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
                    'check_out_photo_path' => $photoPath,
                    'check_out_latitude' => $latitude,
                    'check_out_longitude' => $longitude,
                    'check_out_accuracy' => $accuracy,
                    'check_out_distance_meters' => $distanceMeters,
                    'check_out_in_radius' => $inRadius,
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
                    'shop_id' => $shop->id,
                    'user_id' => $employee->id,
                    'crew_shift_id' => $shift?->id,
                    'employee_name' => $employee->name,
                    'work_date' => $workDate,
                    'timezone' => $timezone,
                    'scheduled_start_at' => $start?->utc(),
                    'scheduled_end_at' => $end?->utc(),
                    'checked_in_at' => $now,
                    'late_minutes' => $start ? (int) ceil(max(0, $start->diffInMinutes($localNow, false) - max(0, config('attendance.grace_minutes')))) : 0,
                    'check_in_note' => $note,
                    'check_in_photo_path' => $photoPath,
                    'check_in_latitude' => $latitude,
                    'check_in_longitude' => $longitude,
                    'check_in_accuracy' => $accuracy,
                    'check_in_distance_meters' => $distanceMeters,
                    'check_in_in_radius' => $inRadius,
                ]);
            }

            ActivityLog::create([
                'shop_id' => $shop->id,
                'user_id' => $employee->id,
                'action' => 'attendance.'.$type,
                'entity_type' => Attendance::class,
                'entity_id' => $attendance->id,
                'description' => $type === 'check_in' ? 'Absensi masuk' : 'Absensi pulang',
                'new_values' => [
                    'attendance_id' => $attendance->id,
                    'recorded_at' => $now->toIso8601String(),
                    'photo_path' => $photoPath,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'distance_meters' => $distanceMeters,
                    'in_radius' => $inRadius,
                ],
            ]);

            return $attendance;
        });
    }

    /**
     * @return array{0: int|null, 1: bool|null}
     */
    private function validateLocation(Shop $shop, ?float $latitude, ?float $longitude): array
    {
        if ($shop->attendance_require_gps && ($latitude === null || $longitude === null)) {
            throw ValidationException::withMessages([
                'gps' => 'Koordinat lokasi GPS wajib diaktifkan. Harap aktifkan izin lokasi di browser Anda.',
            ]);
        }

        if ($latitude === null || $longitude === null) {
            return [null, null];
        }

        if (! $shop->hasLocation()) {
            return [null, true];
        }

        $distance = self::calculateDistanceInMeters(
            $latitude,
            $longitude,
            (float) $shop->latitude,
            (float) $shop->longitude
        );

        $radius = (int) ($shop->attendance_radius_meters ?: 100);
        $inRadius = ($distance <= $radius);

        if (! $inRadius && $shop->attendance_strict_location) {
            throw ValidationException::withMessages([
                'gps' => "Lokasi Anda terdeteksi berjarak {$distance}m dari kafe (radius maksimal {$radius}m). Absensi hanya dapat dicatat di dalam area kafe.",
            ]);
        }

        return [$distance, $inRadius];
    }

    private function handlePhotoUpload(Shop $shop, mixed $photo): ?string
    {
        if (! $photo) {
            return null;
        }

        if ($photo instanceof UploadedFile) {
            return $photo->store("attendances/{$shop->id}/".date('Y/m'), 'public');
        }

        if (is_string($photo) && str_starts_with($photo, 'data:image/')) {
            preg_match('/^data:image\/(\w+);base64,/', $photo, $matches);
            $ext = strtolower($matches[1] ?? 'jpg');
            if ($ext === 'jpeg') {
                $ext = 'jpg';
            }
            if (! in_array($ext, ['jpg', 'png', 'webp'])) {
                $ext = 'jpg';
            }

            $rawBase64 = substr($photo, strpos($photo, ',') + 1);
            $decoded = base64_decode($rawBase64, true);
            if ($decoded !== false) {
                $path = "attendances/{$shop->id}/".date('Y/m').'/'.Str::random(40).'.'.$ext;
                Storage::disk('public')->put($path, $decoded);

                return $path;
            }
        }

        return null;
    }

    public static function calculateDistanceInMeters(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $earthRadius = 6371000; // in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
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
