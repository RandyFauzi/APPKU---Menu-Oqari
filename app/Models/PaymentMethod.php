<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory, BelongsToShop;

    protected $fillable = [
        'shop_id',
        'name',
        'code',
        'type',
        'is_active'
    ];
}
