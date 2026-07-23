<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 5B: Admin China DONE button — lock batch dari input, masih bisa edit.
 *
 * Field is_locked di boxes:
 * - true = box sudah di-DONE oleh Admin China, tidak bisa input barang baru
 * - masih bisa edit data yang sudah ada
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });
    }
};
