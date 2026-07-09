<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Revisi §3.2: denda_claims table for No Tuan claim penalties.
     */
    public function up(): void
    {
        Schema::create('denda_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->decimal('jumlah_denda', 10, 2)->default(5000);
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->enum('status', ['pending', 'tagged', 'paid'])->default('pending');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['customer_id', 'status'], 'idx_customer_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denda_claims');
    }
};
