<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    use BelongsToShop, HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'location',
        'connection_type',
        'ip_address',
        'port',
        'paper_width',
        'category_ids',
        'is_active',
        'auto_print',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_print' => 'boolean',
        'port' => 'integer',
        'category_ids' => 'array',
    ];
}
