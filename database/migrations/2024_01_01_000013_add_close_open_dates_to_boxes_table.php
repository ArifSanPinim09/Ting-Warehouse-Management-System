<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Revisi §3.4: Tambah kolom open_date, close_date, last_setor_date ke tabel boxes.
     */
    public function up(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->timestamp('open_date')->nullable()->after('notes');
            $table->timestamp('close_date')->nullable()->after('open_date');
            $table->timestamp('last_setor_date')->nullable()->after('close_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn(['open_date', 'close_date', 'last_setor_date']);
        });
    }
};
