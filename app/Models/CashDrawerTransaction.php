<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashDrawerTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'user_id', 'cash_register_session_id', 'type', 'amount', 'description', 'payment_id',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    public function session()
    {
        return $this->belongsTo(CashRegisterSession::class, 'cash_register_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
