<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Per-customer rate overrides (nullable = use global rate from settings)
            $table->decimal('custom_rate_air', 10, 2)->nullable()->after('status');
            $table->decimal('custom_rate_sea', 10, 2)->nullable()->after('custom_rate_air');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['custom_rate_air', 'custom_rate_sea']);
        });
    }
};
