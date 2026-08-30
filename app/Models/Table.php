<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'shop_id',
        'name',
        'qr_code_url'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
