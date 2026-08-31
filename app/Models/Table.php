<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'shop_id',
        'name',
        'qr_code_url',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
