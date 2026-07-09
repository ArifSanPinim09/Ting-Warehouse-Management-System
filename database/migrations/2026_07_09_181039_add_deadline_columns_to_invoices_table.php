<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('payment_deadline')->nullable()->after('status');
            $table->date('storage_deadline')->nullable()->after('payment_deadline');
            $table->json('reminder_sent')->nullable()->after('storage_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_deadline', 'storage_deadline', 'reminder_sent']);
        });
    }
};
