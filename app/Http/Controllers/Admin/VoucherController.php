<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    /**
     * Validate a voucher code against cart subtotal for POS or customer checkout.
     */
    public function validateCode(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'subtotal' => ['required', 'numeric', 'min:0'],
        ]);

        $shop = Auth::user()->shop;
        $code = trim(strtoupper($request->string('code')->toString()));
        $subtotal = (float) $request->input('subtotal');

        $voucher = Voucher::where('shop_id', $shop->id)
            ->where('code', $code)
            ->first();

        if (! $voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher "'.$code.'" tidak ditemukan.',
            ], 422);
        }

        $check = $voucher->validateForSubtotal($subtotal);
        if (! $check['valid']) {
            return response()->json([
                'success' => false,
                'message' => $check['message'],
            ], 422);
        }

        $discount = $voucher->calculateDiscount($subtotal);

        return response()->json([
            'success' => true,
            'message' => 'Voucher "'.$voucher->name.'" berhasil diterapkan!',
            'voucher' => [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'name' => $voucher->name,
                'discount_type' => $voucher->discount_type,
                'discount_value' => $voucher->discount_value,
                'min_spend' => $voucher->min_spend,
                'max_discount' => $voucher->max_discount,
            ],
            'discount_amount' => $discount,
            'net_total' => max(0, $subtotal - $discount),
        ]);
    }

    /**
     * List vouchers for the authenticated shop.
     */
    public function index(): JsonResponse
    {
        $shopId = Auth::user()->shop_id;

        $vouchers = Voucher::where('shop_id', $shopId)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'vouchers' => $vouchers,
        ]);
    }

    /**
     * Store a new voucher.
     */
    public function store(Request $request): JsonResponse
    {
        $shopId = Auth::user()->shop_id;

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vouchers')->where(fn ($query) => $query->where('shop_id', $shopId)),
            ],
            'name' => ['required', 'string', 'max:100'],
            'discount_type' => ['required', 'string', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0.01'],
            'min_spend' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['shop_id'] = $shopId;
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['min_spend'] = $validated['min_spend'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        $voucher = Voucher::create($validated);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'shop_id' => $shopId,
                'user_id' => Auth::id(),
                'action' => 'voucher.created',
                'entity_type' => Voucher::class,
                'entity_id' => $voucher->id,
                'description' => 'Membuat voucher promo baru: '.$voucher->code.' ('.$voucher->name.') - Diskon: '.($voucher->discount_type === 'percentage' ? $voucher->discount_value.'%' : 'Rp '.number_format((float) $voucher->discount_value, 0, ',', '.')),
                'new_values' => $voucher->only(['code', 'name', 'discount_type', 'discount_value', 'min_spend', 'max_discount', 'usage_limit', 'expires_at', 'is_active']),
                'ip_address' => request()->ip(),
                'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dibuat.',
            'voucher' => $voucher,
        ]);
    }

    /**
     * Update an existing voucher.
     */
    public function update(Request $request, Voucher $voucher): JsonResponse
    {
        $shopId = Auth::user()->shop_id;
        abort_unless($voucher->shop_id === $shopId, 403);

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vouchers')->where(fn ($query) => $query->where('shop_id', $shopId))->ignore($voucher->id),
            ],
            'name' => ['required', 'string', 'max:100'],
            'discount_type' => ['required', 'string', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0.01'],
            'min_spend' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['min_spend'] = $validated['min_spend'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        $oldValues = $voucher->only(['code', 'name', 'discount_type', 'discount_value', 'min_spend', 'max_discount', 'usage_limit', 'expires_at', 'is_active']);
        $voucher->update($validated);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'shop_id' => $shopId,
                'user_id' => Auth::id(),
                'action' => 'voucher.updated',
                'entity_type' => Voucher::class,
                'entity_id' => $voucher->id,
                'description' => 'Memperbarui voucher promo: '.$voucher->code.' ('.$voucher->name.')',
                'old_values' => $oldValues,
                'new_values' => $voucher->only(['code', 'name', 'discount_type', 'discount_value', 'min_spend', 'max_discount', 'usage_limit', 'expires_at', 'is_active']),
                'ip_address' => request()->ip(),
                'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diperbarui.',
            'voucher' => $voucher->fresh(),
        ]);
    }

    /**
     * Toggle voucher active status.
     */
    public function toggle(Voucher $voucher): JsonResponse
    {
        $shopId = Auth::user()->shop_id;
        abort_unless($voucher->shop_id === $shopId, 403);

        $voucher->update(['is_active' => ! $voucher->is_active]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'shop_id' => $shopId,
                'user_id' => Auth::id(),
                'action' => 'voucher.status_toggled',
                'entity_type' => Voucher::class,
                'entity_id' => $voucher->id,
                'description' => ($voucher->is_active ? 'Mengaktifkan' : 'Menonaktifkan').' voucher promo: '.$voucher->code,
                'new_values' => ['is_active' => $voucher->is_active],
                'ip_address' => request()->ip(),
                'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status voucher berhasil diubah.',
            'voucher' => $voucher,
        ]);
    }

    /**
     * Delete a voucher.
     */
    public function destroy(Voucher $voucher): JsonResponse
    {
        $shopId = Auth::user()->shop_id;
        abort_unless($voucher->shop_id === $shopId, 403);

        $code = $voucher->code;
        $name = $voucher->name;
        $oldValues = $voucher->only(['code', 'name', 'discount_type', 'discount_value', 'min_spend', 'max_discount', 'usage_limit', 'expires_at', 'is_active']);
        $voucher->delete();

        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'shop_id' => $shopId,
                'user_id' => Auth::id(),
                'action' => 'voucher.deleted',
                'entity_type' => Voucher::class,
                'entity_id' => $voucher->id,
                'description' => 'Menghapus voucher promo: '.$code.' ('.$name.')',
                'old_values' => $oldValues,
                'ip_address' => request()->ip(),
                'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dihapus.',
        ]);
    }
}
