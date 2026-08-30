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
        'banners',
    ];

    protected $casts = [
        'banners' => 'array',
        'is_open' => 'boolean',
    ];
}
