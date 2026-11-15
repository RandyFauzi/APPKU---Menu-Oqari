<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Register\CalculateExpectedCash;
use App\Actions\Register\CloseRegister;
use App\Http\Controllers\Controller;
use App\Http\Requests\CloseRegisterRequest;
use App\Http\Requests\RegisterHistoryRequest;
use App\Models\ActivityLog;
use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashRegisterController extends Controller
{
    public function openShift(Request $request)
    {
        $request->validate([
            'opening_cash' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:999999999.99'],
        ]);

        $user = Auth::user();

        return DB::transaction(function () use ($user, $request) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            // Check if already open
            $active = CashRegisterSession::where('user_id', $user->id)
                ->where('status', 'OPEN')
                ->first();

            if ($active) {
                return response()->json(['success' => false, 'message' => 'Anda sudah memiliki shift yang aktif!'], 400);
            }

            // Get or create default register for the shop
            $register = CashRegister::firstOrCreate(
                ['shop_id' => $user->shop_id, 'name' => 'Main Register'],
                ['is_active' => true]
            );

            $session = CashRegisterSession::create([
                'cash_register_id' => $register->id,
                'user_id' => $user->id,
                'opened_at' => now(),
                'opening_cash' => $request->opening_cash,
                'expected_cash' => $request->opening_cash,
                'actual_cash' => 0,
                'difference' => 0,
                'total_cash_sales' => 0,
                'total_qris_sales' => 0,
                'total_other_sales' => 0,
                'status' => 'OPEN',
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'session' => $session,
                ]);
            }

            return redirect()->back()->with('success', 'Shift berhasil dibuka!');
        });
    }

    public function index(RegisterHistoryRequest $request, CalculateExpectedCash $calculateExpectedCash): View
    {
        $user = $request->user();
        $filters = $request->validated();
        $scope = CashRegisterSession::whereHas('register', fn ($query) => $query->where('shop_id', $user->shop_id));
        $activeSession = (clone $scope)->where('user_id', $user->id)
            ->where('status', 'OPEN')
            ->with(['register', 'transactions' => fn ($q) => $q->whereNull('cancelled_at')->latest('id')])
            ->latest('id')
            ->first()
            ?? (clone $scope)
                ->where('status', 'OPEN')
                ->with(['register', 'transactions' => fn ($q) => $q->whereNull('cancelled_at')->latest('id')])
                ->latest('id')
                ->first();
        $breakdown = $activeSession ? $calculateExpectedCash->execute($activeSession) : null;
        $history = (clone $scope)->with(['user', 'register']);
        if (! $user->can('view-reports')) {
            $history->where('user_id', $user->id);
        }
        if (! empty($filters['from'])) {
            $history->whereDate('opened_at', '>=', $filters['from']);
        }
        if (! empty($filters['to'])) {
            $history->whereDate('opened_at', '<=', $filters['to']);
        }
        if (! empty($filters['status'])) {
            if ($filters['status'] === 'UNCOUNTED') {
                $history->where(function ($query): void {
                    $query->where('status', '!=', 'CLOSED')->orWhereNull('actual_cash')->orWhereNull('difference');
                });
            } else {
                $history->where('status', 'CLOSED')->whereNotNull('actual_cash')->whereNotNull('difference');
                $operator = match ($filters['status']) {
                    'SHORT' => '<', 'OVER' => '>', default => '='
                };
                $history->where('difference', $operator, 0);
            }
        }
        $shop = $user->shop;
        $sessions = $history->latest('id')->paginate(20)->withQueryString();

        return view('Admin.Register.index', compact('activeSession', 'breakdown', 'sessions', 'filters', 'shop'));
    }

    public function closeShift(CloseRegisterRequest $request, CloseRegister $closeRegister): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $session = $closeRegister->execute($request->user(), (float) $validated['actual_cash'], $validated['notes'] ?? null, isset($validated['session_id']) ? (int) $validated['session_id'] : null, isset($validated['expected_cash_confirmation']) ? (float) $validated['expected_cash_confirmation'] : null);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'session' => $session, 'difference' => $session->difference]);
        }

        return redirect()->route('admin.register.index')->with('success', 'Shift berhasil ditutup. Status: '.match ($session->reconciliation_status) {
            'SHORT' => 'Kas kurang', 'OVER' => 'Kas lebih', default => 'Kas sesuai'
        }.'.');
    }

    public function recordCashOut(Request $request, CalculateExpectedCash $calculateExpectedCash): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'item' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:100', 'max:999999999.99'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'item.required' => 'Nama item atau keperluan pengeluaran wajib diisi.',
            'amount.required' => 'Nominal kas keluar wajib diisi.',
            'amount.min' => 'Nominal minimal Rp 100.',
        ]);

        $user = $request->user();

        // Cari sesi register yang sedang aktif (milik kasir atau milik toko)
        $session = CashRegisterSession::whereHas('register', fn ($query) => $query->where('shop_id', $user->shop_id))
            ->where('status', 'OPEN')
            ->where('user_id', $user->id)
            ->latest('id')
            ->first()
            ?? CashRegisterSession::whereHas('register', fn ($query) => $query->where('shop_id', $user->shop_id))
                ->where('status', 'OPEN')
                ->latest('id')
                ->first();

        if (! $session) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada shift kasir yang sedang aktif. Buka shift kasir terlebih dahulu untuk mencatat pengeluaran kas.',
                ], 422);
            }

            return redirect()->back()->with('error', 'Tidak ada shift kasir yang sedang aktif. Buka shift kasir terlebih dahulu.');
        }

        $item = trim($validated['item']);
        $notes = ! empty($validated['notes']) ? trim($validated['notes']) : null;
        $description = $notes ? "{$item} - {$notes}" : $item;

        $transaction = $session->transactions()->create([
            'shop_id' => $user->shop_id,
            'user_id' => $user->id,
            'type' => 'CASH_OUT',
            'amount' => (float) $validated['amount'],
            'description' => $description,
        ]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'shop_id' => $user->shop_id,
                'user_id' => $user->id,
                'action' => 'cash_drawer.cash_out',
                'entity_type' => CashRegisterSession::class,
                'entity_id' => $session->id,
                'description' => 'Mencatat kas keluar Rp '.number_format((float) $validated['amount'], 0, ',', '.')." ({$description})",
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
            ]);
        }

        $breakdown = $calculateExpectedCash->execute($session);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kas keluar sebesar Rp '.number_format((float) $validated['amount'], 0, ',', '.').' berhasil dicatat!',
                'transaction' => $transaction,
                'breakdown' => $breakdown,
            ]);
        }

        return redirect()->back()->with('success', 'Kas keluar sebesar Rp '.number_format((float) $validated['amount'], 0, ',', '.').' berhasil dicatat.');
    }
}
