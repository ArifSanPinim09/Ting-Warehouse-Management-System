<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wh_china_data', function (Blueprint $table) {
            $table->id();
            $table->string('resi_number', 100)->index();
            $table->decimal('berat', 10, 2);
            $table->string('ukuran_box', 100);
            $table->decimal('biaya_jasa', 12, 2)->nullable();
            $table->string('foto_barang', 255)->nullable();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->timestamp('matched_at')->nullable();
            $table->foreignId('input_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wh_china_data');
    }
};
