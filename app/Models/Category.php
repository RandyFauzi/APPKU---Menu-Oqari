<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use BelongsToShop, HasFactory;

    protected $fillable = [
        'shop_id',
        'name',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug) && ! empty($category->name)) {
                $baseSlug = Str::slug($category->name) ?: 'kategori';
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('shop_id', $category->shop_id)->where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$counter;
                    $counter++;
                }
                $category->slug = $slug;
            }
        });

        static::updated(function (Category $category): void {
            if ($category->wasChanged('name')) {
                Product::where('shop_id', $category->shop_id)
                    ->where('category_id', $category->id)
                    ->update(['category_name' => $category->name]);
            }
        });

        static::deleting(function (Category $category): void {
            $fallback = static::where('shop_id', $category->shop_id)
                ->where('id', '!=', $category->id)
                ->orderBy('sort_order')
                ->first();

            Product::where('shop_id', $category->shop_id)
                ->where(function ($query) use ($category): void {
                    $query->where('category_id', $category->id)
                        ->orWhere('category_name', $category->name);
                })
                ->update([
                    'category_id' => $fallback?->id,
                    'category_name' => $fallback?->name,
                ]);
        });
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
