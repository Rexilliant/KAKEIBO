<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavingTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'financial_account_id',
        'name',
        'target_amount',
        'current_amount',
        'deadline',
        'note',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'deadline' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    public function contributions()
    {
        return $this->hasMany(SavingContribution::class);
    }
}