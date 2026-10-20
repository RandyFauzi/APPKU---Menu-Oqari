<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SaveCafeUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->shop_id && $this->user()->can('manage-users');
    }

    public function targetUser(): ?User
    {
        $id = $this->route('cafeUser') ?? $this->route('id');

        return $id ? User::where('shop_id', $this->user()->shop_id)->where('role', '!=', 'superadmin')->findOrFail($id) : null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('permission_mode') === 'role') {
            $this->merge(['permissions' => null]);
        } elseif ($this->input('permission_mode') === 'custom' && ! $this->has('permissions')) {
            $this->merge(['permissions' => []]);
        }
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $target = $this->targetUser();
        $email = Rule::unique('users', 'email');
        if ($target) {
            $email->ignore($target->id);
        }
        $roles = $this->user()->role === 'owner' ? array_keys(config('access.roles')) : ['cashier', 'barista', 'kitchen', 'crew'];

        return [
            'name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', $email],
            'role' => ['required', Rule::in($roles)], 'is_active' => ['sometimes', 'boolean'],
            'permission_mode' => ['sometimes', 'in:role,custom'],
            'permissions' => ['sometimes', 'nullable', 'array', 'list', 'max:30'],
            'permissions.*' => ['required', 'string', 'distinct', Rule::in(array_keys(config('access.permissions')))],
            'password' => [$target ? 'nullable' : 'required', 'string', Password::min(12)->mixedCase()->numbers(), 'confirmed', 'max:255'],
            'shop_id' => ['prohibited'], 'account_version' => ['prohibited'], 'email_verified_at' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['email.unique' => 'Email tidak tersedia. Gunakan email lain.', 'role.in' => 'Role tersebut tidak dapat Anda berikan.', 'permissions.*.in' => 'Hak akses tidak dikenali.'];
    }
}
