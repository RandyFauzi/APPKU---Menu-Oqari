<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    private function ensureOnboardingColumnsExist(): void
    {
        try {
            $columns = DB::select('SHOW COLUMNS FROM `shops`');
            $existing = array_map(fn ($col) => (string) ($col->Field ?? $col['Field'] ?? ''), $columns);

            if (! in_array('business_type', $existing)) {
                DB::statement('ALTER TABLE `shops` ADD COLUMN `business_type` VARCHAR(255) NULL');
            }
            if (! in_array('sales_modes', $existing)) {
                DB::statement('ALTER TABLE `shops` ADD COLUMN `sales_modes` TEXT NULL');
            }
            if (! in_array('onboarding_status', $existing)) {
                DB::statement("ALTER TABLE `shops` ADD COLUMN `onboarding_status` VARCHAR(50) NOT NULL DEFAULT 'pending'");
            }
            if (! in_array('onboarding_step', $existing)) {
                DB::statement("ALTER TABLE `shops` ADD COLUMN `onboarding_step` VARCHAR(50) NOT NULL DEFAULT 'welcome'");
            }
            if (! in_array('address', $existing)) {
                DB::statement("ALTER TABLE `shops` ADD COLUMN `address` TEXT NULL");
            }
            if (! in_array('email', $existing)) {
                DB::statement("ALTER TABLE `shops` ADD COLUMN `email` VARCHAR(255) NULL");
            }
        } catch (\Throwable $e) {
            Log::error('Direct ALTER TABLE in OnboardingController failed: '.$e->getMessage());
        }
    }

    public function fixDb()
    {
        $this->ensureOnboardingColumnsExist();
        $columns = DB::select('SHOW COLUMNS FROM `shops`');
        $fields = array_map(fn ($col) => (string) ($col->Field ?? $col['Field'] ?? ''), $columns);

        return response()->json([
            'success' => true,
            'message' => 'Kolom database toko berhasil diperiksa dan diperbarui.',
            'has_business_type' => in_array('business_type', $fields),
            'has_sales_modes' => in_array('sales_modes', $fields),
            'has_onboarding_status' => in_array('onboarding_status', $fields),
            'has_onboarding_step' => in_array('onboarding_step', $fields),
        ]);
    }

    public function index()
    {
        $this->ensureOnboardingColumnsExist();

        $user = Auth::user();
        if (! in_array($user->role, ['owner', 'manager'])) {
            return redirect()->route('admin.dashboard');
        }

        $shop = $user->shop?->fresh() ?: $user->shop;
        if (! $shop) {
            return redirect()->route('admin.dashboard');
        }

        if ($shop->onboarding_status === 'completed') {
            return redirect()->route('admin.dashboard');
        }

        // Derived states
        $hasMenu = Product::where('shop_id', $shop->id)->exists();
        $hasPaymentMethod = PaymentMethod::where('shop_id', $shop->id)->exists();

        return view('Admin.Onboarding.index', compact('shop', 'hasMenu', 'hasPaymentMethod'));
    }

    public function updateStep(Request $request)
    {
        $this->ensureOnboardingColumnsExist();

        $user = Auth::user();
        $shop = $user->shop?->fresh() ?: $user->shop;

        if (! $shop || $shop->onboarding_status === 'completed') {
            return response()->json(['success' => false, 'message' => 'Onboarding already completed']);
        }

        $step = $request->input('step');
        $data = $request->input('data', []);

        // Logic per step
        switch ($step) {
            case 'business_profile':
                $request->validate([
                    'data.name' => 'required|string|max:255',
                    'data.business_type' => 'required|string|max:255',
                ]);
                $shop->name = $data['name'];
                $shop->business_type = $data['business_type'];

                if (isset($data['address'])) {
                    $shop->address = $data['address'];
                }

                $shop->onboarding_status = 'in_progress';
                $shop->onboarding_step = 'sales_mode';
                break;

            case 'sales_mode':
                $modes = $data['sales_modes'] ?? [];
                $shop->sales_modes = $modes;
                $shop->onboarding_step = 'menu';
                break;

            case 'menu':
                $shop->onboarding_step = 'payment';
                break;

            case 'payment':
                $shop->onboarding_step = 'receipt';
                break;

            case 'receipt':
                if (isset($data['slogan'])) {
                    $shop->slogan = $data['slogan'];
                }
                $shop->onboarding_step = 'ready';
                break;
        }

        $shop->save();

        return response()->json([
            'success' => true,
            'shop' => $shop,
            'next_step' => $shop->onboarding_step,
        ]);
    }

    public function complete(Request $request)
    {
        $this->ensureOnboardingColumnsExist();

        $shop = Auth::user()->shop;
        if ($shop) {
            $shop->onboarding_status = 'completed';
            $shop->status = 'active';
            $shop->onboarding_step = 'completed';
            $shop->save();
        }

        return response()->json(['success' => true]);
    }
}
