<?php

namespace App\Models;

use App\Services\MediaService;
use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use BelongsToShop, HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'category_id',
        'category_name',
        'name',
        'description',
        'price',
        'image_path',
        'is_sold_out',
        'cogs',
    ];

    protected $appends = [
        'image_url',
    ];

    protected static function boot()
    {
        parent::boot();

        static::forceDeleted(function ($product) {
            if ($product->image_path && ! Str::startsWith($product->image_path, ['http://', 'https://', '/'])) {
                Storage::disk('public')->delete($product->image_path);
            }
        });
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function modifierGroups()
    {
        return $this->hasMany(ModifierGroup::class);
    }

    public function recipes()
    {
        return $this->hasMany(ProductRecipe::class);
    }

    public function calculateHppFromRecipes(): float
    {
        $this->loadMissing('recipes.rawMaterial');
        $total = 0;
        foreach ($this->recipes as $recipe) {
            $unitCost = (float) ($recipe->rawMaterial->cost_per_unit ?? 0);
            $total += round($unitCost * (float) $recipe->amount, 2);
        }

        return round($total, 2);
    }

    public function getImageUrlAttribute()
    {
        return app(MediaService::class)->url($this->image_path);
    }
}
