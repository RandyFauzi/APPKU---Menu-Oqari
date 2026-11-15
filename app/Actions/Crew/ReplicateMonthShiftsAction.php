<?php

namespace App\Actions\Crew;

use App\Models\ActivityLog;
use App\Models\CrewShift;
use App\Models\Shop;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Replicates crew shift schedules from a source month to one or more target months.
 * Fully tenant-isolated with transactional integrity, conflict resolution, and audit logging.
 */
class ReplicateMonthShiftsAction
{
    /**
     * @param  array{source_month: string, target_months: array<int, string>, strategy?: string, overwrite?: bool, user_ids?: array<int, int>}  $data
     * @return array{success: bool, message: string, created_count: int, skipped_count: int, target_months: array<int, string>}
     */
    public function execute(Shop $shop, User $actor, array $data): array
    {
        return DB::transaction(function () use ($shop, $actor, $data): array {
            Shop::whereKey($shop->id)->lockForUpdate()->firstOrFail();

            $timezone = config('attendance.timezone', 'Asia/Jakarta');
            $sourceMonth = $data['source_month'];
            $targetMonths = array_values(array_unique($data['target_months']));
            $strategy = $data['strategy'] ?? 'date';
            $overwrite = (bool) ($data['overwrite'] ?? false);
            $userIds = ! empty($data['user_ids']) ? array_map('intval', $data['user_ids']) : null;

            // Fetch active crew members belonging to this shop
            $activeCrewQuery = User::where('shop_id', $shop->id)
                ->where('role', '!=', 'superadmin')
                ->unarchived()
                ->where('is_active', true);

            if ($userIds) {
                $activeCrewQuery->whereIn('id', $userIds);
            }

            $activeCrewIds = $activeCrewQuery->pluck('id')->all();

            if (empty($activeCrewIds)) {
                throw ValidationException::withMessages([
                    'user_ids' => 'Tidak ada staf aktif yang dipilih untuk direplikasi.',
                ]);
            }

            // Fetch source month shifts
            $sourceStart = CarbonImmutable::parse($sourceMonth.'-01', $timezone)->startOfMonth();
            $sourceEnd = $sourceStart->endOfMonth();

            $sourceShifts = CrewShift::where('shop_id', $shop->id)
                ->whereBetween('date', [$sourceStart->toDateString().' 00:00:00', $sourceEnd->toDateString().' 23:59:59'])
                ->whereIn('user_id', $activeCrewIds)
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();

            if ($sourceShifts->isEmpty()) {
                throw ValidationException::withMessages([
                    'source_month' => 'Tidak ada jadwal shift pada bulan sumber untuk staf yang dipilih.',
                ]);
            }

            $totalCreated = 0;
            $totalSkipped = 0;

            foreach ($targetMonths as $targetMonth) {
                $targetStart = CarbonImmutable::parse($targetMonth.'-01', $timezone)->startOfMonth();
                $targetEnd = $targetStart->endOfMonth();
                $targetDaysInMonth = $targetStart->daysInMonth;

                $targetRange = [$targetStart->toDateString().' 00:00:00', $targetEnd->toDateString().' 23:59:59'];

                if ($overwrite) {
                    CrewShift::where('shop_id', $shop->id)
                        ->whereBetween('date', $targetRange)
                        ->whereIn('user_id', $activeCrewIds)
                        ->delete();
                }

                // Load existing shifts in target month for overlap checking when not overwriting
                $existingShifts = $overwrite
                    ? collect()
                    : CrewShift::where('shop_id', $shop->id)
                        ->whereBetween('date', $targetRange)
                        ->whereIn('user_id', $activeCrewIds)
                        ->get();

                if ($strategy === 'day_of_week') {
                    // Extract unique day-of-week templates from source shifts
                    // Map: [user_id][day_of_week_iso] => array of unique shift slots
                    $weeklyPatterns = [];
                    foreach ($sourceShifts as $srcShift) {
                        $dow = (int) $srcShift->date->dayOfWeekIso; // 1 = Monday, 7 = Sunday
                        $uId = (int) $srcShift->user_id;
                        $slotKey = $srcShift->start_time.'-'.$srcShift->end_time.'-'.($srcShift->position ?? '');

                        if (! isset($weeklyPatterns[$uId][$dow][$slotKey])) {
                            $weeklyPatterns[$uId][$dow][$slotKey] = [
                                'shift_template_id' => $srcShift->shift_template_id,
                                'start_time' => $srcShift->start_time,
                                'end_time' => $srcShift->end_time,
                                'position' => $srcShift->position,
                                'notes' => $srcShift->notes,
                            ];
                        }
                    }

                    // Apply patterns across each day of the target month
                    for ($d = 1; $d <= $targetDaysInMonth; $d++) {
                        $targetDateObj = $targetStart->setDay($d);
                        $targetDateStr = $targetDateObj->toDateString();
                        $targetDow = (int) $targetDateObj->dayOfWeekIso;

                        foreach ($activeCrewIds as $uId) {
                            if (empty($weeklyPatterns[$uId][$targetDow])) {
                                continue;
                            }

                            foreach ($weeklyPatterns[$uId][$targetDow] as $slot) {
                                if (! $overwrite && $this->hasConflict($existingShifts, $uId, $targetDateStr, $slot['start_time'], $slot['end_time'], $timezone)) {
                                    $totalSkipped++;

                                    continue;
                                }

                                $newShift = CrewShift::create([
                                    'shop_id' => $shop->id,
                                    'user_id' => $uId,
                                    'shift_template_id' => $slot['shift_template_id'],
                                    'date' => $targetDateStr,
                                    'start_time' => $slot['start_time'],
                                    'end_time' => $slot['end_time'],
                                    'position' => $slot['position'],
                                    'notes' => $slot['notes'],
                                ]);

                                $existingShifts->push($newShift);
                                $totalCreated++;
                            }
                        }
                    }
                } else {
                    // Default strategy: 'date' (replicate day of month: 1st -> 1st, 2nd -> 2nd)
                    foreach ($sourceShifts as $srcShift) {
                        $day = (int) $srcShift->date->format('j');

                        // If target month has fewer days (e.g. Feb 29-31, April 31), skip safely
                        if ($day > $targetDaysInMonth) {
                            $totalSkipped++;

                            continue;
                        }

                        $targetDateStr = $targetStart->setDay($day)->toDateString();

                        if (! $overwrite && $this->hasConflict($existingShifts, (int) $srcShift->user_id, $targetDateStr, $srcShift->start_time, $srcShift->end_time, $timezone)) {
                            $totalSkipped++;

                            continue;
                        }

                        $newShift = CrewShift::create([
                            'shop_id' => $shop->id,
                            'user_id' => $srcShift->user_id,
                            'shift_template_id' => $srcShift->shift_template_id,
                            'date' => $targetDateStr,
                            'start_time' => $srcShift->start_time,
                            'end_time' => $srcShift->end_time,
                            'position' => $srcShift->position,
                            'notes' => $srcShift->notes,
                        ]);

                        $existingShifts->push($newShift);
                        $totalCreated++;
                    }
                }
            }

            // Audit log
            ActivityLog::create([
                'shop_id' => $shop->id,
                'user_id' => $actor->id,
                'action' => 'replicate_shifts',
                'entity_type' => CrewShift::class,
                'entity_id' => 0,
                'description' => "Menerapkan jadwal shift dari {$sourceMonth} ke ".count($targetMonths)." bulan target ({$totalCreated} shift dibuat, {$totalSkipped} dilewati).",
                'new_values' => [
                    'source_month' => $sourceMonth,
                    'target_months' => $targetMonths,
                    'strategy' => $strategy,
                    'overwrite' => $overwrite,
                    'created_count' => $totalCreated,
                    'skipped_count' => $totalSkipped,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return [
                'success' => true,
                'message' => "Berhasil menerapkan {$totalCreated} shift ke ".count($targetMonths).' bulan.'.($totalSkipped > 0 ? " ({$totalSkipped} dilewati karena konflik atau tanggal tidak valid)" : ''),
                'created_count' => $totalCreated,
                'skipped_count' => $totalSkipped,
                'target_months' => $targetMonths,
            ];
        }, 3);
    }

    /**
     * Checks if a user already has an overlapping shift in the provided collection.
     */
    private function hasConflict(
        $shiftsCollection,
        int $userId,
        string $date,
        string $startTime,
        string $endTime,
        string $timezone
    ): bool {
        $newStart = CarbonImmutable::parse($date.' '.$startTime, $timezone);
        $newEnd = CarbonImmutable::parse($date.' '.$endTime, $timezone);
        if ($newEnd->lte($newStart)) {
            $newEnd = $newEnd->addDay();
        }

        foreach ($shiftsCollection as $existing) {
            if ((int) $existing->user_id !== $userId) {
                continue;
            }

            $existingStart = $existing->startsAt();
            $existingEnd = $existing->endsAt();

            // Check time overlap: (startA < endB) and (endA > startB)
            if ($newStart->lt($existingEnd) && $newEnd->gt($existingStart)) {
                return true;
            }
        }

        return false;
    }
}
