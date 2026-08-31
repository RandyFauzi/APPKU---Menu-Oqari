<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToShop;
class ActivityLog extends Model
{
    use BelongsToShop;
    use HasFactory;

    protected $fillable = [
        "shop_id",
        "user_id",
        "action",
        "description",
        "ip_address"
    ];

    public function user()
    {
    use BelongsToShop;
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
    use BelongsToShop;
        return $this->belongsTo(Shop::class);
    }
}


