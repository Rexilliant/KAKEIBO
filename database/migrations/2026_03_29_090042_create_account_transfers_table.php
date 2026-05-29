<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('account_transfers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('from_financial_account_id')
                ->constrained('financial_accounts')
                ->cascadeOnDelete();

            $table->foreignId('to_financial_account_id')
                ->constrained('financial_accounts')
                ->cascadeOnDelete();

            $table->date('transfer_date');
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_transfers');
    }
};