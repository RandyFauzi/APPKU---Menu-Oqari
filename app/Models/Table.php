<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToShop;
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

