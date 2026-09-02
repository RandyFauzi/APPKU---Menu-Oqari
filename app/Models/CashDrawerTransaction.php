<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashDrawerTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_register_session_id', 'type', 'amount', 'description', 'payment_id'
    ];

    public function session()
    {
        return $this->belongsTo(CashRegisterSession::class, 'cash_register_session_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
