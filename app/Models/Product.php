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
        'hpp_overhead_cost',
        'hpp_target_margin', 'hpp_settings',
    ];

    protected $casts = ['hpp_settings' => 'array'];

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
        $settings = $this->hpp_settings ?? [];
        $quantity = (float) ($settings['production_quantity'] ?? 1);
        $total = 0;
        foreach ($this->recipes as $recipe) {
            $unitCost = (float) ($recipe->rawMaterial->cost_per_unit ?? 0);
            $batchAmount = (float) ($recipe->batch_amount ?? ((float) $recipe->amount * $quantity));
            $total += round($unitCost * $batchAmount, 2);
        }
        $labor = (float) ($settings['labor_cost'] ?? 0);
        $overhead = ($settings['overhead_mode'] ?? 'fixed') === 'percentage'
            ? round(($total + $labor) * (float) ($settings['overhead_percent'] ?? 0) / 100, 2)
            : (float) ($settings['overhead_cost'] ?? $this->hpp_overhead_cost ?? 0);

        return round(($total + $labor + $overhead) / $quantity, 2);
    }

    public function getImageUrlAttribute()
    {
        return app(MediaService::class)->url($this->image_path);
    }
}
