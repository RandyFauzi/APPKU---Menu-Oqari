<?php

namespace App\Models;

use App\Services\MediaService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Shop extends Model
{
    use HasFactory;

    protected $hidden = ['gobiz_access_token', 'gobiz_refresh_token'];

    protected static function booted()
    {
        static::saved(function ($shop) {
            if ($shop->wasChanged('slug')) {
                $oldSlug = $shop->getOriginal('slug');
                if ($oldSlug) {
                    ShopSlugHistory::firstOrCreate([
                        'shop_id' => $shop->id,
                        'old_slug' => $oldSlug,
                    ]);
                }
            }
        });
    }

    protected $fillable = [
        'name',
        'address',
        'slug',
        'logo_path',
        'primary_color',
        'theme_style',
        'business_type',
        'sales_modes',
        'onboarding_status',
        'onboarding_step',
        'is_open',
        'slogan',
        'font_family',
        'title_font_family',
        'receipt_header',
        'receipt_footer',
        'receipt_settings',
        'printer_settings',
        'discount_settings',
        'instagram_link',
        'tiktok_link',
        'social_settings',
        'email',
        'whatsapp_number',
        'maps_link',
        'banner_paths',
        'is_banner_active',
        'background_type',
        'background_pattern',
        'background_image_path',
        'background_color',
        'background_overlay_opacity',
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
        'latitude',
        'longitude',
        'attendance_radius_meters',
        'attendance_require_photo',
        'attendance_require_gps',
        'attendance_strict_location',
    ];

    protected $appends = [
        'logo_url',
        'banners',
        'background_image_url',
    ];

    protected $casts = [
        'banner_paths' => 'array',
        'social_settings' => 'array',
        'operating_hours' => 'array',
        'sales_modes' => 'array',
        'receipt_settings' => 'array',
        'printer_settings' => 'array',
        'discount_settings' => 'array',
        'is_open' => 'boolean',
        'is_banner_active' => 'boolean',
        'background_overlay_opacity' => 'integer',
        'gobiz_token_expires_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'last_active_at' => 'datetime',
        'suspended_at' => 'datetime',
        'mrr' => 'decimal:2',
        'latitude' => 'float',
        'longitude' => 'float',
        'attendance_radius_meters' => 'integer',
        'attendance_require_photo' => 'boolean',
        'attendance_require_gps' => 'boolean',
        'attendance_strict_location' => 'boolean',
    ];

    public function hasLocation(): bool
    {
        return ! is_null($this->latitude) && ! is_null($this->longitude);
    }

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

    public function printers()
    {
        return $this->hasMany(Printer::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function getLogoUrlAttribute()
    {
        return app(MediaService::class)->url($this->logo_path);
    }

    public function getBannersAttribute()
    {
        $value = $this->banner_paths;
        $banners = is_string($value) ? json_decode($value, true) : $value;
        if (! is_array($banners)) {
            return [];
        }

        return array_map(function ($banner) {
            return app(MediaService::class)->url($banner);
        }, $banners);
    }

    public function getBackgroundImageUrlAttribute()
    {
        return app(MediaService::class)->url($this->background_image_path);
    }

    public function activeSocialLinks(): array
    {
        $settings = $this->social_settings ?? [];
        $links = [];

        if (($settings['show_whatsapp'] ?? true) && ! empty($this->whatsapp_number)) {
            $num = preg_replace('/[^0-9]/', '', $this->whatsapp_number);
            if (str_starts_with($num, '0')) {
                $num = '62'.substr($num, 1);
            }
            $links['whatsapp'] = [
                'name' => 'WhatsApp',
                'url' => 'https://wa.me/'.$num,
                'icon' => 'fab fa-whatsapp',
                'color' => '#25D366',
                'handle' => $this->whatsapp_number,
            ];
        }

        if (($settings['show_instagram'] ?? true) && ! empty($this->instagram_link)) {
            $handle = trim($this->instagram_link, '@ ');
            $url = str_starts_with($handle, 'http') ? $handle : 'https://instagram.com/'.$handle;
            $links['instagram'] = [
                'name' => 'Instagram',
                'url' => $url,
                'icon' => 'fab fa-instagram',
                'color' => '#E1306C',
                'handle' => '@'.str_replace('https://instagram.com/', '', $handle),
            ];
        }

        if (($settings['show_tiktok'] ?? true) && ! empty($this->tiktok_link)) {
            $handle = trim($this->tiktok_link, '@ ');
            $url = str_starts_with($handle, 'http') ? $handle : 'https://www.tiktok.com/@'.$handle;
            $links['tiktok'] = [
                'name' => 'TikTok',
                'url' => $url,
                'icon' => 'fab fa-tiktok',
                'color' => '#000000',
                'handle' => '@'.str_replace(['https://www.tiktok.com/@', 'https://tiktok.com/@'], '', $handle),
            ];
        }

        if (($settings['show_maps'] ?? true) && ! empty($this->maps_link)) {
            $links['maps'] = [
                'name' => 'Google Maps',
                'url' => $this->maps_link,
                'icon' => 'fas fa-map-marker-alt',
                'color' => '#EA4335',
                'handle' => 'Lokasi Toko',
            ];
        }

        return $links;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function rawMaterials()
    {
        return $this->hasMany(RawMaterial::class);
    }

    public function stockMutations()
    {
        return $this->hasMany(StockMutation::class);
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

    public function getInstagramUrlAttribute()
    {
        $link = $this->instagram_link;
        if (empty($link)) {
            return null;
        }

        if (Str::startsWith($link, ['http://', 'https://'])) {
            return $link;
        }

        return 'https://www.instagram.com/'.ltrim($link, '/@');
    }

    public function getWhatsappUrlAttribute()
    {
        $phone = $this->whatsapp_number;
        if (empty($phone)) {
            return null;
        }

        // Clean up the phone number (remove spaces, dashes, plus, parentheses)
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Replace leading 0 with 62
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        return 'https://wa.me/'.$phone;
    }

    public function getNormalizedOperatingHoursAttribute(): array
    {
        $hours = $this->operating_hours;
        if (empty($hours) || ! is_array($hours)) {
            $hours = [];
        }

        $defaultWeekly = [
            'monday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            'tuesday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            'wednesday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => false],
            'thursday' => ['open' => '08:00', 'close' => '21:00', 'is_closed' => false],
            'friday' => ['open' => '08:00', 'close' => '23:00', 'is_closed' => false],
            'saturday' => ['open' => '08:00', 'close' => '23:00', 'is_closed' => false],
            'sunday' => ['open' => '08:00', 'close' => '22:00', 'is_closed' => true],
        ];

        $weekly = $hours['weekly'] ?? null;
        if (! $weekly || ! is_array($weekly)) {
            if (isset($hours['monday']) || isset($hours['sunday'])) {
                $weekly = array_merge($defaultWeekly, array_intersect_key($hours, $defaultWeekly));
            } else {
                $weekly = $defaultWeekly;
            }
        } else {
            $weekly = array_merge($defaultWeekly, $weekly);
        }

        return [
            'weekly' => $weekly,
            'holidays' => $hours['holidays'] ?? [
                ['date' => '2026-08-17', 'name' => 'Hari Kemerdekaan', 'is_closed' => true],
                ['date' => '2026-12-25', 'name' => 'Hari Natal', 'is_closed' => true],
            ],
            'timezone' => $hours['timezone'] ?? 'Asia/Jakarta',
            'auto_close' => (bool) ($hours['auto_close'] ?? true),
            'allow_preorder' => (bool) ($hours['allow_preorder'] ?? false),
        ];
    }

    public function isOpenNow(): bool
    {
        if (! $this->is_open) {
            return false;
        }

        $config = $this->normalized_operating_hours;
        if (! ($config['auto_close'] ?? true)) {
            return (bool) $this->is_open;
        }

        $tz = $config['timezone'] ?? 'Asia/Jakarta';
        try {
            $now = now()->setTimezone($tz);
        } catch (\Throwable $e) {
            $now = now();
        }

        // Check holiday for today
        $todayDate = $now->format('Y-m-d');
        foreach ($config['holidays'] ?? [] as $holiday) {
            if (($holiday['date'] ?? '') === $todayDate) {
                return ! ($holiday['is_closed'] ?? true);
            }
        }

        // Check day of week
        $dayKey = strtolower($now->format('l'));
        $daySchedule = $config['weekly'][$dayKey] ?? null;
        if (! $daySchedule || ! empty($daySchedule['is_closed'])) {
            return false;
        }

        $currentTime = $now->format('H:i');
        $open = $daySchedule['open'] ?? '00:00';
        $close = $daySchedule['close'] ?? '23:59';

        if ($close < $open) {
            // Overnight hours, e.g. 18:00 - 02:00
            return $currentTime >= $open || $currentTime <= $close;
        }

        return $currentTime >= $open && $currentTime <= $close;
    }
}
