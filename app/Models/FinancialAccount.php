<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\FinancialAccountMovement;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'provider',
        'account_number',
        'balance',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function savingTargets()
    {
        return $this->hasMany(SavingTarget::class);
    }

    public function savingContributions()
    {
        return $this->hasMany(SavingContribution::class);
    }

    public function movements()
    {
        return $this->hasMany(FinancialAccountMovement::class);
    }

    public function outgoingTransfers()
    {
        return $this->hasMany(AccountTransfer::class, 'from_financial_account_id');
    }

    public function incomingTransfers()
    {
        return $this->hasMany(AccountTransfer::class, 'to_financial_account_id');
    }

    public function recurringTransactions()
    {
        return $this->hasMany(RecurringTransaction::class);
    }
}