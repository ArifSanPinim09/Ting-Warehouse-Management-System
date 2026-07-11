<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Revisi: Tambah kolom baru ke wh_china_data:
     * - huruf_box: kode huruf box dari China
     * - foto_arrived_china: foto bukti barang sampai di warehouse China
     * - foto_arrived_ina: foto bukti barang sampai di Indonesia
     * - tanggal_setor: tanggal customer input setor resi
     */
    public function up(): void
    {
        Schema::table('wh_china_data', function (Blueprint $table) {
            $table->string('huruf_box', 10)->nullable()->after('ukuran_box');
            $table->string('foto_arrived_china', 255)->nullable()->after('foto_barang');
            $table->string('foto_arrived_ina', 255)->nullable()->after('foto_arrived_china');
            $table->timestamp('tanggal_setor')->nullable()->after('foto_arrived_ina');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wh_china_data', function (Blueprint $table) {
            $table->dropColumn(['huruf_box', 'foto_arrived_china', 'foto_arrived_ina', 'tanggal_setor']);
        });
    }
};
