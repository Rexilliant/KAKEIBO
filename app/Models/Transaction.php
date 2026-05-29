<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'financial_account_id',
        'category_id',
        'transaction_date',
        'type',
        'amount',
        'title',
        'note',
        'is_unexpected',
        'affects_budget',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'is_unexpected' => 'boolean',
        'affects_budget' => 'boolean',
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

    public function attachments()
    {
        return $this->hasMany(TransactionAttachment::class);
    }
}