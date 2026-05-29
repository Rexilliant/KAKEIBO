<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavingContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'saving_target_id',
        'account_transfer_id',
        'financial_account_id',
        'contribution_date',
        'amount',
        'type',
        'note',
    ];

    protected $casts = [
        'contribution_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function savingTarget()
    {
        return $this->belongsTo(SavingTarget::class);
    }

    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    public function accountTransfer()
    {
        return $this->belongsTo(AccountTransfer::class);
    }
}