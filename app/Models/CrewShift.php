<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToShop;
class CrewShift extends Model
{
    use BelongsToShop;
    use HasFactory;

    protected $fillable = [
        "user_id",
        "date",
        "start_time",
        "end_time",
        "notes"
    ];

    protected $casts = [
        "date" => "date",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


