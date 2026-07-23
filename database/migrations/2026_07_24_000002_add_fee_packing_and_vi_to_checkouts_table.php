<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 5A: Tambah VI (Volume INA) dan Fee Packing ke checkouts table.
 *
 * Client doc: "Fee Packing di Checkout: max(berat, VI) × fee_packing tiered"
 * Client doc: "VI (Volume INA) ini untuk kirim ke ekspedisi di indonesia karna beda satuan"
 *
 * VI = (P×L×T)/4000 — dipakai untuk ekspedisi INA.
 * Fee Packing dihitung saat checkout, berdasarkan max(berat, VI) × tiered rates.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->decimal('volume_ina', 10, 2)->nullable()->after('ongkir');
            $table->decimal('fee_packing', 12, 2)->nullable()->after('volume_ina');
        });
    }

    public function down(): void
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropColumn(['volume_ina', 'fee_packing']);
        });
    }
};
