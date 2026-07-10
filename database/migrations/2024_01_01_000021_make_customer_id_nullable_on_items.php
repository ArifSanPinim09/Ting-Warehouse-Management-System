<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make customer_id nullable on items table.
     *
     * Required for No Tuan items: admin inputs barang langsung tanpa customer,
     * jadi customer_id harus null sampai barang diklaim.
     * Revisi §2.1: "Barang tidak terdaftar di setor resi customer mana pun"
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete()->change();
        });
    }
};
