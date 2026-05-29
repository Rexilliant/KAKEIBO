<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyClosing extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'total_income',
        'total_expense',
        'net_balance',
        'is_closed',
    ];

    protected $casts = [
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'net_balance' => 'decimal:2',
        'is_closed' => 'boolean',
    ];
}