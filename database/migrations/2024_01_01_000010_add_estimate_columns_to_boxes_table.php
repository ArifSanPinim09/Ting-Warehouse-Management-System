<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add ETD/ETA estimate columns to boxes table (PRD §4.15).
     */
    public function up(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->date('etd')->nullable()->after('notes');
            $table->date('eta')->nullable()->after('etd');
        });
    }

    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn(['etd', 'eta']);
        });
    }
};
