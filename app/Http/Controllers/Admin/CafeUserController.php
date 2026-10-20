<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Crew\ManageCafeUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveCafeUserRequest;
use App\Models\User;
use App\Policies\CafeUserPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CafeUserController extends Controller
{
    public function index(Request $request, CafeUserPolicy $policy): View
    {
        $this->authorize('manage-users');
        abort_unless($request->user()->shop_id, 403);
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:active,inactive']]);
        $users = User::where('shop_id', $request->user()->shop_id)->where('role', '!=', 'superadmin')
            ->when($filters['q'] ?? null, fn ($query, string $term) => $query->where(fn ($search) => $search->where('name', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%')))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('is_active', $status === 'active'))
            ->orderBy('name')->paginate(20)->withQueryString();

        return view('Admin.Users.index', compact('users', 'filters', 'policy'));
    }

    public function create(Request $request): View
    {
        return $this->form($request);
    }

    public function edit(Request $request, int $cafeUser, CafeUserPolicy $policy): View
    {
        $target = User::where('shop_id', $request->user()->shop_id)->where('role', '!=', 'superadmin')->findOrFail($cafeUser);
        abort_unless($policy->update($request->user(), $target), 403);

        return $this->form($request, $target);
    }

    private function form(Request $request, ?User $target = null): View
    {
        $this->authorize('manage-users');
        abort_unless($request->user()->shop_id, 403);
        $roles = $request->user()->role === 'owner' ? config('access.roles') : array_intersect_key(config('access.roles'), array_flip(['cashier', 'barista', 'kitchen', 'crew']));
        $assignable = $request->user()->role === 'owner' ? array_keys(config('access.permissions')) : array_values(array_diff($request->user()->effectivePermissions(), ['manage-users']));
        $groups = collect(config('access.permissions'))->filter(fn (array $value, string $key): bool => in_array($key, $assignable, true))->groupBy('group', preserveKeys: true);

        return view('Admin.Users.form', compact('target', 'roles', 'groups', 'assignable'));
    }

    public function store(SaveCafeUserRequest $request, ManageCafeUser $manage): JsonResponse|RedirectResponse
    {
        return $this->saved($request, $manage->execute($request->user(), $request->validated()));
    }

    public function update(SaveCafeUserRequest $request, int $cafeUser, ManageCafeUser $manage): JsonResponse|RedirectResponse
    {
        return $this->saved($request, $manage->execute($request->user(), $request->validated(), $cafeUser));
    }

    public function deactivate(Request $request, int $cafeUser, ManageCafeUser $manage): JsonResponse|RedirectResponse
    {
        $this->authorize('manage-users');
        abort_unless($request->user()->shop_id, 403);

        return $this->saved($request, $manage->execute($request->user(), ['is_active' => false], $cafeUser));
    }

    private function saved(Request $request, User $user): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'user' => $user->only(['id', 'name', 'email', 'role', 'is_active']), 'message' => 'Akun dan hak akses tersimpan. Sesi lama akan diminta login kembali jika akses berubah.']);
        }

        return redirect()->route('admin.users.index')->with('success', 'Akun dan hak akses tersimpan. Perubahan akses berlaku pada permintaan berikutnya; user perlu login kembali.');
    }
}
