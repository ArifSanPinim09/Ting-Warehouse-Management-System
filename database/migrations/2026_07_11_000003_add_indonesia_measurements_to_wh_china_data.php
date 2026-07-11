<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Revisi: Admin Indonesia perlu input berat & P×L×T saat barang arrived di Indonesia.
     * Volume dihitung otomatis: (P×L×T) / 6
     * Field ini dipisah dari data China (berat, ukuran_box).
     */
    public function up(): void
    {
        Schema::table('wh_china_data', function (Blueprint $table) {
            $table->decimal('berat_ina', 10, 2)->nullable()->after('berat');
            $table->decimal('panjang', 10, 2)->nullable()->after('berat_ina');
            $table->decimal('lebar', 10, 2)->nullable()->after('panjang');
            $table->decimal('tinggi', 10, 2)->nullable()->after('lebar');
            $table->decimal('volume', 10, 4)->nullable()->after('tinggi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wh_china_data', function (Blueprint $table) {
            $table->dropColumn(['berat_ina', 'panjang', 'lebar', 'tinggi', 'volume']);
        });
    }
};
