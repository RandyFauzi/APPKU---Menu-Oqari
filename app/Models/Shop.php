<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo_url',
        'primary_color',
        'theme_style',
        'is_open',
        'slogan',
        'font_family',
        'instagram_link',
        'whatsapp_number',
        'maps_link',
        'banners', 'is_banner_active',
        'gobiz_outlet_id',
        'gobiz_access_token',
        'gobiz_refresh_token',
        'gobiz_token_expires_at',
        'operating_hours',
        'banner_url',
    ];

    protected $casts = [
        'banners' => 'array',
        'operating_hours' => 'array',
        'is_open' => 'boolean',
        'gobiz_token_expires_at' => 'datetime',
    ];
}

