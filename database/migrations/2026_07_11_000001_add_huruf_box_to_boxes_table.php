<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Revisi: Tambah kolom huruf_box ke tabel boxes.
     * Format box: batch_name + huruf_box (contoh: 126-H).
     * Huruf box ditentukan oleh orang China saat menandai box.
     */
    public function up(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->string('huruf_box', 10)->nullable()->after('batch_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn('huruf_box');
        });
    }
};
