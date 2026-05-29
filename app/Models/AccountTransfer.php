<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from_financial_account_id',
        'to_financial_account_id',
        'saving_target_id',
        'transfer_date',
        'amount',
        'note',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'from_financial_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'to_financial_account_id');
    }

    public function savingTarget()
    {
        return $this->belongsTo(SavingTarget::class);
    }

    public function savingContributions()
    {
        return $this->hasMany(SavingContribution::class);
    }
}