<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('saving_contributions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('saving_target_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('financial_account_id')
                ->nullable()
                ->constrained('financial_accounts')
                ->nullOnDelete();

            $table->date('contribution_date');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['in', 'out'])->default('in');
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saving_contributions');
    }
};