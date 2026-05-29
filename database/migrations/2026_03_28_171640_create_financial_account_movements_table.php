<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financial_account_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('financial_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('type', ['in', 'out']); // uang masuk / keluar
            $table->decimal('amount', 15, 2);

            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);

            $table->string('ref_type')->nullable(); // contoh: transaction
            $table->unsignedBigInteger('ref_id')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_account_movements');
    }
};
