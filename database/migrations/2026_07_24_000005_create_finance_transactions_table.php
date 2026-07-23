<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 5B: Laporan Keuangan kategori.
 *
 * Tabel finance_transactions untuk mencatat:
 * - Biaya Operasional (sewa, listrik, gaji, dll)
 * - Biaya Refund
 * - Pemasukan lain (non-invoice)
 *
 * Kategori lain sudah ada di tabel terpisah:
 * - Pengeluaran China → goods_weight_fees + shipping_material_fees
 * - Biaya Box → shipping_material_fees
 * - Pemasukan → invoices (grand_total yang sudah dibayar)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['operasional', 'refund', 'pemasukan_lain']);
            $table->string('description', 255);
            $table->decimal('amount', 14, 2);
            $table->date('transaction_date');
            $table->foreignId('input_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};
