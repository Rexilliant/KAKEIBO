<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'financial_account_id',
        'category_id',
        'title',
        'type',
        'amount',
        'note',
        'frequency',
        'start_date',
        'next_run_date',
        'last_run_date',
        'is_active',
        'affects_budget',
        'is_unexpected',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'next_run_date' => 'date',
        'last_run_date' => 'date',
        'is_active' => 'boolean',
        'affects_budget' => 'boolean',
        'is_unexpected' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}