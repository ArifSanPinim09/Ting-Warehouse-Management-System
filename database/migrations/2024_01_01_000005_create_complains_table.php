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
        Schema::create('complains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('box_id')->nullable()->constrained('boxes')->nullOnDelete();
            $table->index(['customer_id', 'box_id']);
            $table->string('type', 100);
            $table->enum('resolution', ['refund', 'replacement']);
            $table->string('invoice_number', 50)->nullable();
            $table->string('resi_number', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('video_url', 255)->nullable();
            $table->string('photo_url', 255)->nullable();
            $table->enum('status', ['open', 'in_review', 'processing', 'resolved'])->default('open')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complains');
    }
};
