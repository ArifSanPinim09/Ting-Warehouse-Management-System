<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->date('stevedoring_date')->nullable()->after('eta');
            $table->date('tagihan_update_date')->nullable()->after('stevedoring_date');
        });
    }

    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn(['stevedoring_date', 'tagihan_update_date']);
        });
    }
};
