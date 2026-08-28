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
        'theme_style'
    ];
}
