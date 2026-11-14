<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modifier extends Model
{
    use HasFactory;

    protected $fillable = [
        'modifier_group_id',
        'name',
        'price_adjustment',
        'cogs_adjustment',
        'is_active',
    ];

    protected $casts = [
        'price_adjustment' => 'float',
        'cogs_adjustment' => 'float',
        'is_active' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(ModifierGroup::class, 'modifier_group_id');
    }
}
