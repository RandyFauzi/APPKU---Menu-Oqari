<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Traits\BelongsToShop;
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use BelongsToShop;
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
    use BelongsToShop;
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

