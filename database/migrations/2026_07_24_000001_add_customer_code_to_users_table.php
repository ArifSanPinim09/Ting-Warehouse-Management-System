<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 5A: Tambah kolom customer_code ke tabel users.
 *
 * Customer code adalah kode unik per customer (misal: LIX, AHF, MAY).
 * Dipakai untuk format nama box Direct: {customer_code}-{METHOD}-{huruf_box}
 * Contoh: LIX-SEA-B-1
 *
 * Client confirm: "Direct, LIX dari ID customer kak"
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('customer_code', 10)->nullable()->unique()->after('line_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('customer_code');
        });
    }
};
