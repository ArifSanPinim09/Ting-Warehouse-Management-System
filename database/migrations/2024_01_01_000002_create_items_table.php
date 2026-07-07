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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->constrained('boxes')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->index(['box_id', 'customer_id']);
            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->decimal('price_yuan', 12, 2)->nullable();
            $table->string('resi_number', 100)->nullable()->index();
            $table->string('proof_co', 255)->nullable();
            $table->boolean('is_sensitive')->default(false);
            $table->string('sensitive_type', 50)->nullable();
            $table->boolean('arrived_china')->default(false);
            $table->string('arrived_china_photo', 255)->nullable();
            $table->boolean('arrived_indonesia')->default(false);
            $table->string('arrived_indonesia_photo', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
