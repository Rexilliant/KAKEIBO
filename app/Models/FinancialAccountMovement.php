<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinancialAccountMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'financial_account_id',
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'ref_type',
        'ref_id',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}