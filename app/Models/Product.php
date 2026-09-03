<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
            if ($product->image_path && !\Illuminate\Support\Str::startsWith($product->image_path, ['http://', 'https://', '/'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image_path);
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

    public function getImageUrlAttribute()
    {
        return app(\App\Services\MediaService::class)->url($this->image_path);
    }
}
