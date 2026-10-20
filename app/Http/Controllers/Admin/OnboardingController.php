<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OnboardingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (! $user->can('manage-settings')) {
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
        $user = Auth::user();
        $shop = $user->shop?->fresh() ?: $user->shop;

        if (! $shop || $shop->onboarding_status === 'completed') {
            return response()->json(['success' => false, 'message' => 'Onboarding already completed']);
        }

        $validated = $request->validate([
            'step' => ['required', 'in:business_profile,sales_mode,menu,payment,receipt'],
            'data' => ['sometimes', 'array'],
            'data.address' => ['nullable', 'string', 'max:1000'],
            'data.sales_modes' => ['sometimes', 'array', 'min:1'],
            'data.sales_modes.*' => ['in:DINE_IN,TAKEAWAY,DELIVERY'],
            'data.slogan' => ['nullable', 'string', 'max:255'],
        ]);
        $step = $validated['step'];
        $data = $validated['data'] ?? [];

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
        DB::transaction(function (): void {
            $shop = Shop::whereKey(Auth::user()->shop_id)->lockForUpdate()->firstOrFail();
            abort_if($shop->isSuspended(), 403, 'Toko dinonaktifkan.');
            $shop->onboarding_status = 'completed';
            $shop->onboarding_step = 'completed';
            if ($shop->status === 'onboarding') {
                $shop->status = 'active';
            }
            $shop->save();
        });

        return response()->json(['success' => true]);
    }
}
