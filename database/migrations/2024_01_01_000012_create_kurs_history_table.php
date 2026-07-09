<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Revisi §3.1 — kurs_history: history-based kurs Yuan→IDR.
     * Each row records a kurs value and the date it became effective.
     * Unique constraint on (kurs_value, effective_date) prevents duplicates.
     */
    public function up(): void
    {
        Schema::create('kurs_history', function (Blueprint $table) {
            $table->id();
            $table->decimal('kurs_value', 10, 2);
            $table->date('effective_date');
            $table->foreignId('input_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index('effective_date', 'idx_effective_date');
            $table->unique(['kurs_value', 'effective_date'], 'uk_kurs_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurs_history');
    }
};
