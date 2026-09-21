<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Product;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OnboardingController extends Controller
{
    private function ensureOnboardingColumnsExist(): void
    {
        if (! Schema::hasTable('shops')) {
            return;
        }

        if (! Schema::hasColumn('shops', 'business_type') || ! Schema::hasColumn('shops', 'onboarding_status') || ! Schema::hasColumn('shops', 'sales_modes') || ! Schema::hasColumn('shops', 'onboarding_step')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                Log::warning('Artisan migrate in OnboardingController failed: '.$e->getMessage());
            }

            // Direct fallback: ensure columns exist even if artisan migrate was blocked or skipped
            if (! Schema::hasColumn('shops', 'business_type') || ! Schema::hasColumn('shops', 'onboarding_status') || ! Schema::hasColumn('shops', 'sales_modes') || ! Schema::hasColumn('shops', 'onboarding_step')) {
                try {
                    Schema::table('shops', function (Blueprint $table) {
                        if (! Schema::hasColumn('shops', 'business_type')) {
                            $table->string('business_type')->nullable();
                        }
                        if (! Schema::hasColumn('shops', 'sales_modes')) {
                            $table->json('sales_modes')->nullable();
                        }
                        if (! Schema::hasColumn('shops', 'onboarding_status')) {
                            $table->string('onboarding_status')->default('pending');
                        }
                        if (! Schema::hasColumn('shops', 'onboarding_step')) {
                            $table->string('onboarding_step')->default('welcome');
                        }
                    });
                } catch (\Throwable $e) {
                    Log::error('Direct schema update in OnboardingController failed: '.$e->getMessage());
                }
            }
        }
    }

    public function index()
    {
        $this->ensureOnboardingColumnsExist();

        $user = Auth::user();
        if (! in_array($user->role, ['owner', 'manager'])) {
            return redirect()->route('admin.dashboard');
        }

        $shop = $user->shop;
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
        $shop = $user->shop;

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
