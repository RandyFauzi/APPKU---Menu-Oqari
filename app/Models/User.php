<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token', 'permissions', 'account_version'])]
class User extends Authenticatable
{
    protected $attributes = ['is_active' => true, 'account_version' => 0];

    protected $fillable = ['name', 'email', 'password', 'shop_id', 'role'];

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array', 'is_active' => 'boolean', 'account_version' => 'integer',
        ];
    }

    public function isAccountActive(): bool
    {
        return ! array_key_exists('is_active', $this->getAttributes()) || $this->is_active === true;
    }

    /** @return list<string> */
    public function effectivePermissions(): array
    {
        if (! $this->isAccountActive() || ! $this->shop_id || $this->role === 'superadmin') {
            return [];
        }
        $catalog = array_keys(config('access.permissions'));
        if ($this->role === 'owner') {
            return $catalog;
        }
        if ($this->permissions !== null) {
            return array_values(array_intersect($catalog, $this->permissions));
        }
        $defaults = config('access.defaults.'.$this->role, []);

        return in_array('*', $defaults, true) ? array_values(array_diff($catalog, ['manage-users'])) : $defaults;
    }

    public function homeRoute(): string
    {
        if ($this->role === 'superadmin') {
            return 'superadmin.dashboard';
        }
        if ($this->role === 'owner' && ! $this->shop_id) {
            return 'admin.dashboard';
        }
        if (array_intersect(config('access.dashboard'), $this->effectivePermissions()) !== []) {
            return 'admin.dashboard';
        }
        foreach (['manage-users' => 'admin.users.index', 'access-pos' => 'admin.pos.index', 'view-kitchen' => 'admin.kitchen.index', 'manage-hpp' => 'admin.hpp.index', 'manage-register' => 'admin.register.index', 'view-attendance' => 'admin.attendance.report', 'record-attendance' => 'admin.attendance.index', 'view-own-schedule' => 'admin.my-schedule', 'view-orders' => 'admin.dashboard'] as $ability => $route) {
            if ($this->can($ability)) {
                return $route;
            }
        }

        return 'profile.edit';
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function crewShifts(): HasMany
    {
        return $this->hasMany(CrewShift::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
