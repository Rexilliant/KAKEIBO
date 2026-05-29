<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monthly_reflections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->unsignedSmallInteger('month');
            $table->unsignedSmallInteger('year');

            $table->decimal('planned_saving', 15, 2)->default(0);
            $table->decimal('actual_saving', 15, 2)->default(0);

            $table->text('question_1_money_owned')->nullable();
            $table->text('question_2_saving_goal')->nullable();
            $table->text('question_3_actual_spending')->nullable();
            $table->text('question_4_unnecessary_expense')->nullable();
            $table->text('question_5_improvement_next_month')->nullable();
            $table->text('question_6_best_financial_decision')->nullable();

            $table->enum('mood', ['calm', 'good', 'wasteful', 'chaotic'])->nullable();
            $table->text('mood_note')->nullable();
            $table->text('commitment_next_month')->nullable();

            $table->timestamps();

            $table->unique(
                ['user_id', 'month', 'year'],
                'monthly_reflections_unique_user_month_year'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_reflections');
    }
};