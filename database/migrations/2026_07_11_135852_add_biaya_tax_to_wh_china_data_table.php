<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wh_china_data', function (Blueprint $table) {
            $table->decimal('biaya_tax', 15, 2)->nullable()->after('tinggi');
        });
    }

    public function down(): void
    {
        Schema::table('wh_china_data', function (Blueprint $table) {
            $table->dropColumn('biaya_tax');
        });
    }
};
