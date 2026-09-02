<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Crew\CreateShift;
use App\Http\Controllers\Controller;
use App\Models\CrewShift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  MY SCHEDULE — Crew/Barista view: their own 7-day schedule
    // ─────────────────────────────────────────────────────────────

    public function mySchedule(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        // Show a rolling 7-day window starting from today
        $shifts = CrewShift::where('shop_id', $user->shop_id)
            ->where('user_id', $user->id)
            ->whereBetween('date', [$today, $today->copy()->addDays(6)])
            ->orderBy('date')
            ->get()
            ->keyBy(fn ($shift) => $shift->date->format('Y-m-d'));

        // Build a 7-day grid (today → +6) so UI always renders all 7 slots
        $schedule = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $today->copy()->addDays($i);
            $key = $date->format('Y-m-d');
            $shift = $shifts->get($key);

            $schedule[] = [
                'date' => $date,
                'label' => $i === 0 ? 'TODAY'
                             : ($i === 1 ? 'TOMORROW'
                             : strtoupper($date->format('l'))),
                'shift' => $shift,
                'is_off' => is_null($shift),
            ];
        }

        return view('Admin.shift.my-schedule', compact('schedule', 'user'));
    }

    // ─────────────────────────────────────────────────────────────
    //  SHIFT MANAGEMENT — Owner/Manager view: full crew scheduling
    // ─────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->authorize('manage-crew');

        $shop = $request->user()->shop;
        $week = Carbon::now()->startOfWeek();

        $crew = User::where('shop_id', $shop->id)
            ->whereNotIn('role', ['owner', 'superadmin'])
            ->with(['crewShifts' => fn ($q) => $q->whereBetween('date', [$week, $week->copy()->endOfWeek()])])
            ->get();

        return view('Admin.shift.index', compact('crew', 'week'));
    }

    public function store(Request $request, CreateShift $createShift)
    {
        $this->authorize('manage-crew');

        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'position' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $shift = $createShift->execute($request->user()->shop, $validated);

        return response()->json(['success' => true, 'shift' => $shift], 201);
    }
}
