<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashRegisterController extends Controller
{
    public function openShift(Request $request)
    {
        $request->validate([
            'opening_cash' => 'required|numeric|min:0'
        ]);

        $user = Auth::user();
        
        return DB::transaction(function () use ($user, $request) {
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
                'status' => 'OPEN'
            ]);

            return response()->json([
                'success' => true, 
                'session' => $session
            ]);
        });
    }

    public function closeShift(Request $request)
    {
        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $user = Auth::user();

        return DB::transaction(function () use ($user, $request) {
            $session = CashRegisterSession::where('user_id', $user->id)
                ->where('status', 'OPEN')
                ->first();

            if (!$session) {
                return response()->json(['success' => false, 'message' => 'Tidak ada shift aktif!'], 400);
            }

            $difference = $request->actual_cash - $session->expected_cash;

            $session->update([
                'closed_at' => now(),
                'actual_cash' => $request->actual_cash,
                'difference' => $difference,
                'status' => 'CLOSED',
                'notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'session' => $session,
                'difference' => $difference
            ]);
        });
    }
}
