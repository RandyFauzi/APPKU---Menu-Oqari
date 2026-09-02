<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModifierGroup extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name', 'is_required', 'min_selections', 'max_selections', 'is_active'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function modifiers()
    {
        return $this->hasMany(Modifier::class);
    }
}
