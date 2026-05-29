<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('account_transfers', function (Blueprint $table) {
            $table->foreignId('saving_target_id')
                ->nullable()
                ->after('to_financial_account_id')
                ->constrained('saving_targets')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('account_transfers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('saving_target_id');
        });
    }
};