<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Crew\RecordAttendance;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceReportRequest;
use App\Http\Requests\RecordAttendanceRequest;
use App\Http\Requests\UpdateAttendanceLocationRequest;
use App\Models\Attendance;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(AttendanceReportRequest $request): View
    {
        return $this->render($request, $request->validated(), $request->isTeamReport());
    }

    public function report(AttendanceReportRequest $request): RedirectResponse
    {
        return redirect()->route('admin.attendance.index', ['tab' => 'team'] + $request->validated());
    }

    public function store(RecordAttendanceRequest $request, RecordAttendance $recordAttendance): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $photo = $request->file('photo') ?? $data['photo_base64'] ?? null;
        $latitude = isset($data['latitude']) && $data['latitude'] !== '' ? (float) $data['latitude'] : null;
        $longitude = isset($data['longitude']) && $data['longitude'] !== '' ? (float) $data['longitude'] : null;
        $accuracy = isset($data['accuracy']) && $data['accuracy'] !== '' ? (float) $data['accuracy'] : null;

        $attendance = $recordAttendance->execute(
            employee: $request->user(),
            type: $data['type'],
            note: $data['note'] ?? null,
            latitude: $latitude,
            longitude: $longitude,
            accuracy: $accuracy,
            photo: $photo,
        );

        $message = $data['type'] === 'check_in' ? 'Absen masuk berhasil dicatat.' : 'Absen pulang berhasil dicatat.';
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'attendance' => $attendance]);
        }

        return redirect()->route('admin.attendance.index')->with('status', $message);
    }

    public function updateLocationSettings(UpdateAttendanceLocationRequest $request): JsonResponse|RedirectResponse
    {
        $shop = $request->user()->shop;
        $data = $request->validated();

        $shop->update([
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'attendance_radius_meters' => $data['attendance_radius_meters'],
            'attendance_require_photo' => $data['attendance_require_photo'],
            'attendance_require_gps' => $data['attendance_require_gps'],
            'attendance_strict_location' => $data['attendance_strict_location'],
        ]);

        $message = 'Pengaturan lokasi & verifikasi absensi berhasil diperbarui.';
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'shop' => $shop]);
        }

        return redirect()->route('admin.attendance.index')->with('status', $message);
    }

    /** @param array<string, mixed> $filters */
    private function render(Request $request, array $filters, bool $isReport): View
    {
        $user = $request->user();
        $shop = $user->shop;
        $timezone = config('attendance.timezone');
        $today = CarbonImmutable::now($timezone);
        $filters['month'] = $filters['month'] ?? $today->format('Y-m');
        $month = CarbonImmutable::createFromFormat('!Y-m', $filters['month'], $timezone);
        $query = Attendance::where('shop_id', $user->shop_id)
            ->whereBetween('work_date', [$month->startOfMonth(), $month->endOfMonth()]);
        if (! $isReport) {
            $query->where('user_id', $user->id);
        } elseif (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        match ($filters['status'] ?? '') {
            'late' => $query->where('late_minutes', '>', 0),
            'on_time' => $query->whereNotNull('scheduled_start_at')->where('late_minutes', 0),
            'unscheduled' => $query->whereNull('scheduled_start_at'),
            'active' => $query->whereNull('checked_out_at'),
            'completed' => $query->whereNotNull('checked_out_at'),
            default => null,
        };
        $summary = [
            'count' => (clone $query)->count(),
            'late' => (clone $query)->where('late_minutes', '>', 0)->count(),
            'on_time' => (clone $query)->whereNotNull('scheduled_start_at')->where('late_minutes', 0)->count(),
            'today' => Attendance::where('shop_id', $user->shop_id)->whereDate('work_date', $today->toDateString())->count(),
            'active' => (clone $query)->whereNull('checked_out_at')->count(),
            'minutes' => (int) (clone $query)->sum('worked_minutes'),
        ];
        $attendances = $query->with('user:id,name')->orderByDesc('work_date')->orderByDesc('id')
            ->paginate(20)->withQueryString();
        $active = null;
        $todayRecord = Attendance::where('shop_id', $user->shop_id)->where('user_id', $user->id)
            ->whereDate('work_date', $today->toDateString())->first();
        $employees = $isReport ? User::where('shop_id', $user->shop_id)->orderBy('name')->get(['id', 'name']) : collect();

        return view('Admin.Attendance.index', compact(
            'attendances', 'active', 'todayRecord', 'employees', 'filters', 'summary', 'isReport', 'timezone', 'today', 'shop',
        ));
    }
}
