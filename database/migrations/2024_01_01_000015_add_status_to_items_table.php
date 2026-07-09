<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Revisi §2.5.2: Add status column to items table for No Tuan / Klaim WH tracking.
     * 5 statuses: active, no_tuan, claimed, klaim_wh, shipped
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->after('arrived_indonesia_photo')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
