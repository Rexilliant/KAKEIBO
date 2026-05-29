<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonthlyReflection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'planned_saving',
        'actual_saving',
        'question_1_money_owned',
        'question_2_saving_goal',
        'question_3_actual_spending',
        'question_4_unnecessary_expense',
        'question_5_improvement_next_month',
        'question_6_best_financial_decision',
        'mood',
        'mood_note',
        'commitment_next_month',
    ];

    protected $casts = [
        'planned_saving' => 'decimal:2',
        'actual_saving' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}