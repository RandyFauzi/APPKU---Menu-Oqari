<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegisterSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_register_id', 'user_id', 'opened_at', 'closed_at',
        'opening_cash', 'expected_cash', 'actual_cash', 'difference',
        'total_cash_sales', 'total_qris_sales', 'total_other_sales',
        'status', 'notes',
    ];

    protected $appends = ['reconciliation_status'];

    protected $casts = [
        'opening_cash' => 'decimal:2', 'expected_cash' => 'decimal:2',
        'actual_cash' => 'decimal:2', 'difference' => 'decimal:2',
        'total_cash_sales' => 'decimal:2', 'total_qris_sales' => 'decimal:2', 'total_other_sales' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function getReconciliationStatusAttribute(): string
    {
        if ($this->status !== 'CLOSED' || $this->actual_cash === null || $this->difference === null) {
            return 'UNCOUNTED';
        }

        return match (true) {
            (float) $this->difference < 0 => 'SHORT',
            (float) $this->difference > 0 => 'OVER',
            default => 'BALANCED',
        };
    }

    public function register(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class, 'cash_register_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashDrawerTransaction::class);
    }
}
