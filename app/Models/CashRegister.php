<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use BelongsToShop, HasFactory;

    protected $fillable = ['shop_id', 'name', 'is_active'];

    public function sessions()
    {
        return $this->hasMany(CashRegisterSession::class);
    }
}
