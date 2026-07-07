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
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['sharing', 'direct', 'handcarry'])->index();
            $table->string('tracking_number', 100)->nullable()->index();
            $table->string('batch_name', 100)->nullable();
            $table->string('status', 50)->default('OPEN')->index();
            $table->enum('method', ['air', 'sea'])->index();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
