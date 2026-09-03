<?php

namespace App\Models;

use App\Traits\BelongsToShop;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use BelongsToShop;

    protected $fillable = [
        'shop_id',
        'name',
        'public_token',
    ];
    
    protected $appends = ['qr_code_url'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    
    public function getQrCodeUrlAttribute()
    {
        if (!$this->shop) {
            $this->load('shop');
        }
        
        $slug = $this->shop->slug ?? 'menu';
        $baseUrl = url('/' . $slug);
        // User said: /menu/coffee-shop?t=8f3a9c...
        $url = $baseUrl . '?t=' . $this->public_token;
        return 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($url);
    }
}
