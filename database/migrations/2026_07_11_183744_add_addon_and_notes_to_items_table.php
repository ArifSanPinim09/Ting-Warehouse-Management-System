<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Revisi Client: Tambah kolom add_on dan notes ke items.
     * - add_on: pilihan add on (opsional, decimal)
     * - notes: catatan dari klien (opsional, max 100 kata)
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('add_on', 12, 2)->nullable()->default(0)->after('price_yuan');
            $table->text('notes')->nullable()->after('sensitive_type');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['add_on', 'notes']);
        });
    }
};
