<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Crew\CreateShift;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveShiftRequest;
use App\Models\CrewShift;
use App\Models\ShiftTemplate;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $shopId = (int) $request->user()->shop_id;
        $this->ensureDefaultTemplates($shopId);
        $templates = $this->fetchFormattedTemplates($shopId);

        $shifts = CrewShift::where('shop_id', $shopId)->with('user:id,name,role')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')->orderBy('start_time')->orderBy('id')->get()->map(function (CrewShift $shift) use ($templates): array {
                $startTime = substr($shift->start_time, 0, 5);
                $endTime = substr($shift->end_time, 0, 5);

                $code = null;
                foreach ($templates as $t) {
                    if ($t['start_time'] === $startTime && $t['end_time'] === $endTime) {
                        $code = $t['code'];
                        break;
                    }
                }

                if (! $code) {
                    $notes = strtolower($shift->notes ?? '');
                    foreach ($templates as $t) {
                        if (str_contains($notes, 'shift '.$t['code']) || str_contains($notes, strtolower($t['name']))) {
                            $code = $t['code'];
                            break;
                        }
                    }
                }

                if (! $code) {
                    if ($startTime === '07:00' && $endTime === '15:00') {
                        $code = '1';
                    } elseif ($startTime === '11:00' && $endTime === '19:00') {
                        $code = '2';
                    } elseif ($startTime === '15:00' && $endTime === '23:00') {
                        $code = '3';
                    } elseif ($startTime === '08:00' && $endTime === '17:00') {
                        $code = '4';
                    } elseif ($startTime < '10:00' && $endTime <= '16:00') {
                        $code = '1';
                    } elseif ($startTime >= '10:00' && $startTime < '14:00') {
                        $code = '2';
                    } elseif ($startTime >= '14:00') {
                        $code = '3';
                    } else {
                        $code = '1';
                    }
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
            'templates' => $templates,
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

    public function getTemplates(Request $request): JsonResponse
    {
        $this->authorize('manage-crew');
        abort_unless($request->user()->shop_id, 403);
        $shopId = (int) $request->user()->shop_id;

        $this->ensureDefaultTemplates($shopId);
        $templates = $this->fetchFormattedTemplates($shopId);

        return response()->json(['success' => true, 'data' => $templates]);
    }

    public function saveTemplates(Request $request): JsonResponse
    {
        $this->authorize('manage-crew');
        abort_unless($request->user()->shop_id, 403);
        $shopId = (int) $request->user()->shop_id;

        $validated = $request->validate([
            'templates' => ['required', 'array', 'min:1'],
            'templates.*.id' => ['nullable', 'integer'],
            'templates.*.name' => ['required', 'string', 'max:50'],
            'templates.*.start_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'templates.*.end_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'templates.*.color' => ['nullable', 'string', 'max:30'],
        ]);

        $palette = ['blue', 'emerald', 'purple', 'amber', 'rose', 'cyan', 'indigo'];

        DB::transaction(function () use ($shopId, $validated, $palette) {
            $existingIds = ShiftTemplate::where('shop_id', $shopId)->pluck('id')->all();
            $keptIds = [];

            foreach ($validated['templates'] as $index => $item) {
                $startTime = substr($item['start_time'], 0, 5).':00';
                $endTime = substr($item['end_time'], 0, 5).':00';
                $color = ! empty($item['color']) ? $item['color'] : $palette[$index % count($palette)];

                if (! empty($item['id']) && in_array((int) $item['id'], $existingIds, true)) {
                    $template = ShiftTemplate::find($item['id']);
                    $template->update([
                        'name' => trim($item['name']),
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'color' => $color,
                        'is_active' => true,
                    ]);
                    $keptIds[] = (int) $item['id'];
                } else {
                    $template = ShiftTemplate::create([
                        'shop_id' => $shopId,
                        'name' => trim($item['name']),
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'color' => $color,
                        'is_active' => true,
                    ]);
                    $keptIds[] = $template->id;
                }
            }

            $toDelete = array_diff($existingIds, $keptIds);
            if (! empty($toDelete)) {
                ShiftTemplate::where('shop_id', $shopId)->whereIn('id', $toDelete)->delete();
            }
        });

        $templates = $this->fetchFormattedTemplates($shopId);

        return response()->json(['success' => true, 'data' => $templates]);
    }

    private function ensureDefaultTemplates(int $shopId): void
    {
        if (ShiftTemplate::where('shop_id', $shopId)->count() === 0) {
            $defaults = [
                ['name' => 'Shift 1', 'start_time' => '07:00:00', 'end_time' => '15:00:00', 'color' => 'blue'],
                ['name' => 'Shift 2', 'start_time' => '11:00:00', 'end_time' => '19:00:00', 'color' => 'emerald'],
                ['name' => 'Shift 3', 'start_time' => '15:00:00', 'end_time' => '23:00:00', 'color' => 'purple'],
                ['name' => 'Shift 4', 'start_time' => '08:00:00', 'end_time' => '17:00:00', 'color' => 'amber'],
            ];
            foreach ($defaults as $d) {
                ShiftTemplate::create(array_merge($d, ['shop_id' => $shopId, 'is_active' => true]));
            }
        }
    }

    private function fetchFormattedTemplates(int $shopId): array
    {
        $palette = ['blue', 'emerald', 'purple', 'amber', 'rose', 'cyan', 'indigo'];

        return ShiftTemplate::where('shop_id', $shopId)
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->values()
            ->map(function (ShiftTemplate $template, int $index) use ($palette): array {
                $start = substr($template->start_time, 0, 5);
                $end = substr($template->end_time, 0, 5);

                return [
                    'id' => $template->id,
                    'code' => (string) ($index + 1),
                    'name' => $template->name,
                    'start_time' => $start,
                    'end_time' => $end,
                    'color' => $template->color ?: $palette[$index % count($palette)],
                ];
            })
            ->all();
    }

    public function store(SaveShiftRequest $request, CreateShift $createShift): JsonResponse
    {
        $data = $request->validated();
        $shift = $createShift->execute($request->user()->shop, $data, isset($data['id']) ? (int) $data['id'] : null);

        return response()->json(['success' => true, 'shift' => $shift], isset($data['id']) ? 200 : 201);
    }
}
