<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('box_id')->constrained('boxes')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->index(['box_id', 'customer_id']);
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('volume', 10, 2)->nullable();
            $table->decimal('fee_tax', 12, 2)->default(0);
            $table->decimal('fee_wh', 12, 2)->default(0);
            $table->decimal('fee_packing', 12, 2)->default(0);
            $table->decimal('add_on', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->enum('payment_method', ['transfer', 'qris'])->nullable();
            $table->string('payment_proof', 255)->nullable();
            $table->enum('status', ['waiting_payment', 'waiting_verification', 'verified'])->default('waiting_payment')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
