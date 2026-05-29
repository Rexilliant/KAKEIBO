<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('saving_contributions', function (Blueprint $table) {
            $table->foreignId('account_transfer_id')
                ->nullable()
                ->after('saving_target_id')
                ->constrained('account_transfers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('saving_contributions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_transfer_id');
        });
    }
};