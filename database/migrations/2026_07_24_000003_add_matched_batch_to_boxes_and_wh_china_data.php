<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 5A: Matched Data — hubungkan box Admin INA dengan batch Admin China.
 *
 * Flow Website P280: "MATCHED DATA (ini nanti pilih mau matching data yang di buat admin CINA)"
 * Flow Website P285: "MATCHED DATA: 20072607-26"
 * Flow Website P290: Customer muncul: "20072607-26_AIR_140"
 *
 * - matched_batch: batch Admin China (misal 20072607-26)
 * - boxes.matched_batch menyimpan batch_name dari Admin China
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->string('matched_batch', 50)->nullable()->after('batch_name');
        });

        // Admin China input batch_name di WhChinaData
        Schema::table('wh_china_data', function (Blueprint $table) {
            $table->string('china_batch_name', 50)->nullable()->after('huruf_box');
        });
    }

    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn('matched_batch');
        });
        Schema::table('wh_china_data', function (Blueprint $table) {
            $table->dropColumn('china_batch_name');
        });
    }
};
