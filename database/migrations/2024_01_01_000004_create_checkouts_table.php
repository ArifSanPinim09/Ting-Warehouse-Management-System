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
        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->index(['invoice_id', 'customer_id']);
            $table->enum('address_type', ['personal', 'dropship']);
            $table->string('recipient_name');
            $table->string('recipient_phone', 15);
            $table->text('address');
            $table->string('packing_photo', 255)->nullable();
            $table->string('tracking_number', 100)->nullable()->index();
            $table->enum('status', ['request', 'on_process', 'sent'])->default('request')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkouts');
    }
};
