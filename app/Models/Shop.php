<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'primary_color',
        'theme_style',
        'is_open',
        'slogan',
        'font_family',
        'instagram_link',
        'whatsapp_number',
        'maps_link',
        'banner_paths',
        'is_banner_active',
        'gobiz_outlet_id',
        'gobiz_access_token',
        'gobiz_refresh_token',
        'gobiz_token_expires_at',
        'operating_hours',
        'status',
        'trial_ends_at',
        'last_active_at',
        'mrr',
        'suspended_at',
        'suspended_reason',
        'suspended_by',
    ];

    protected $appends = [
        'logo_url',
        'banners',
    ];

    protected $casts = [
        'banner_paths' => 'array',
        'operating_hours' => 'array',
        'is_open' => 'boolean',
        'is_banner_active' => 'boolean',
        'gobiz_token_expires_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'last_active_at' => 'datetime',
        'suspended_at' => 'datetime',
        'mrr' => 'decimal:2',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getLogoUrlAttribute()
    {
        $value = $this->logo_path;
        if (!$value) return null;
        if (\Illuminate\Support\Str::startsWith($value, ['http://', 'https://', '/'])) return $value;
        return \Illuminate\Support\Facades\Storage::disk('public')->url($value);
    }

    public function getBannersAttribute()
    {
        $value = $this->banner_paths;
        $banners = is_string($value) ? json_decode($value, true) : $value;
        if (!is_array($banners)) return [];
        
        return array_map(function ($banner) {
            if (!$banner) return null;
            if (\Illuminate\Support\Str::startsWith($banner, ['http://', 'https://', '/'])) return $banner;
            return \Illuminate\Support\Facades\Storage::disk('public')->url($banner);
        }, $banners);
    }


    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function suspendedBy()
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%");
        });
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    // "Dormant" = tidak ada aktivitas order 14 hari terakhir — sinyal churn risk
    // untuk owner platform, bukan status resmi di database.
    public function isDormant(): bool
    {
        return $this->isActive()
            && (! $this->last_active_at || $this->last_active_at->lt(now()->subDays(14)));
    }
}
