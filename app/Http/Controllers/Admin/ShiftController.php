<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Crew\CreateShift;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveShiftRequest;
use App\Models\CrewShift;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function destroy(Request $request, CrewShift $shift): JsonResponse
    {
        $this->authorize('manage-crew');
        abort_unless((int) $shift->shop_id === (int) $request->user()->shop_id, 404);
        $shift->delete();

        return response()->json(['success' => true]);
    }

    public function mySchedule(Request $request): View
    {
        $this->authorize('view-own-schedule');
        $user = $request->user();
        abort_unless($user->shop_id, 403);
        $timezone = config('attendance.timezone');
        $today = CarbonImmutable::today($timezone)->locale('id');
        $shifts = CrewShift::where('shop_id', $user->shop_id)->where('user_id', $user->id)
            ->whereBetween('date', [$today->toDateString(), $today->addDays(6)->toDateString()])
            ->orderBy('date')->orderBy('start_time')->get();
        $grouped = $shifts->groupBy(fn (CrewShift $shift): string => $shift->date->format('Y-m-d'));
        $schedule = [];
        for ($index = 0; $index < 7; $index++) {
            $date = $today->addDays($index);
            $schedule[] = ['date' => $date, 'label' => $index === 0 ? 'Hari ini' : ($index === 1 ? 'Besok' : $date->translatedFormat('l')),
                'shifts' => $grouped->get($date->toDateString(), collect())];
        }
        $workDays = $grouped->count();
        $totalMinutes = $shifts->sum(fn (CrewShift $shift): int => $shift->durationMinutes());
        $shiftCount = $shifts->count();

        return view('Admin.shift.my-schedule', compact('schedule', 'user', 'timezone', 'today', 'workDays', 'totalMinutes', 'shiftCount'));
    }

    public function schedule(Request $request): JsonResponse
    {
        $this->authorize('manage-crew');
        abort_unless($request->user()->shop_id, 403);
        $filters = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'month' => ['nullable', 'date_format:Y-m'],
        ]);
        $timezone = config('attendance.timezone');

        if (! empty($filters['month'])) {
            $start = CarbonImmutable::parse($filters['month'].'-01', $timezone)->startOfMonth();
            $end = $start->endOfMonth();
        } else {
            $start = CarbonImmutable::parse($filters['date'] ?? CarbonImmutable::today($timezone)->toDateString(), $timezone);
            $end = $start->addDays(6);
        }

        $shifts = CrewShift::where('shop_id', $request->user()->shop_id)->with('user:id,name,role')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')->orderBy('start_time')->orderBy('id')->get()->map(function (CrewShift $shift): array {
                $startTime = substr($shift->start_time, 0, 5);
                $endTime = substr($shift->end_time, 0, 5);

                $code = '1';
                if ($startTime === '07:00' && $endTime === '15:00') {
                    $code = '1';
                } elseif ($startTime === '11:00' && $endTime === '19:00') {
                    $code = '2';
                } elseif ($startTime === '15:00' && $endTime === '23:00') {
                    $code = '3';
                } elseif ($startTime === '08:00' && $endTime === '17:00') {
                    $code = '4';
                } elseif (str_contains(strtolower($shift->notes ?? ''), 'shift 1')) {
                    $code = '1';
                } elseif (str_contains(strtolower($shift->notes ?? ''), 'shift 2')) {
                    $code = '2';
                } elseif (str_contains(strtolower($shift->notes ?? ''), 'shift 3')) {
                    $code = '3';
                } elseif (str_contains(strtolower($shift->notes ?? ''), 'shift 4')) {
                    $code = '4';
                } elseif ($startTime < '10:00' && $endTime <= '16:00') {
                    $code = '1';
                } elseif ($startTime >= '10:00' && $startTime < '14:00') {
                    $code = '2';
                } elseif ($startTime >= '14:00') {
                    $code = '3';
                }

                return [
                    'id' => $shift->id,
                    'user_id' => $shift->user_id,
                    'user_name' => $shift->user?->name ?? 'Staf tidak tersedia',
                    'user_role' => config('access.roles.'.$shift->user?->role, $shift->user?->role ?? 'Karyawan'),
                    'user_initials' => mb_strtoupper(mb_substr($shift->user?->name ?? '?', 0, 2)),
                    'date' => $shift->date->format('Y-m-d'),
                    'date_label' => $shift->date->locale('id')->translatedFormat('d M Y'),
                    'day_name' => $shift->date->locale('id')->translatedFormat('l'),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'overnight' => $shift->endsAt()->toDateString() !== $shift->startsAt()->toDateString(),
                    'position' => $shift->position,
                    'notes' => $shift->notes,
                    'code' => $code,
                    'duration_minutes' => $shift->durationMinutes(),
                ];
            });

        $employees = User::where('shop_id', $request->user()->shop_id)->unarchived()->where('role', '!=', 'superadmin')
            ->orderBy('name')->get()->filter(fn (User $user): bool => $user->isAccountActive())->values()
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => config('access.roles.'.$user->role, $user->role),
                'initials' => mb_strtoupper(mb_substr($user->name, 0, 2)),
            ]);

        $today = CarbonImmutable::today($timezone);

        return response()->json([
            'data' => $shifts,
            'employees' => $employees,
            'timezone' => $timezone,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'month' => $start->format('Y-m'),
            'month_label' => $start->locale('id')->translatedFormat('F Y'),
            'date_range_label' => $start->locale('id')->translatedFormat('d M').' – '.$end->locale('id')->translatedFormat('d M Y'),
            'today' => $today->toDateString(),
            'current_time' => CarbonImmutable::now($timezone)->format('H:i'),
            'current_user_id' => $request->user()->id,
        ]);
    }

    public function store(SaveShiftRequest $request, CreateShift $createShift): JsonResponse
    {
        $data = $request->validated();
        $shift = $createShift->execute($request->user()->shop, $data, isset($data['id']) ? (int) $data['id'] : null);

        return response()->json(['success' => true, 'shift' => $shift], isset($data['id']) ? 200 : 201);
    }
}
