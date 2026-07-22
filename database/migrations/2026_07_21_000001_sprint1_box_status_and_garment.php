<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sprint 1 Revisi: Box Status Flow — 13 status sesuai dokumen client
     * 
     * Status lama → baru:
     * - LAST_SETOR → LAST_CLAIM
     * - SENT_TO_CARGO → SEND_TO_CARGO
     * - OTW_INA → ARRIVED_INA
     * - UP_INVOICE → INVOICE
     * 
     * Status baru:
     * - REQUEST_TO_SEND
     * - ARRIVED_AT_CARGO
     * - WAITING_FOR_DEPARTURE
     * - DEPARTURE
     * - REDLINE (jadi status, bukan kolom)
     * - STEVEDORING (jadi status, bukan kolom)
     * - CHECKED_BY_WH
     * 
     * Kolom baru:
     * - is_garment (items)
     * - volume_ina (items) — Volume INA = (P×L×T)/4
     * - garment_rate (settings)
     */
    public function up(): void
    {
        // 1. Rename box statuses in DB
        DB::table('boxes')->where('status', 'LAST_SETOR')->update(['status' => 'LAST_CLAIM']);
        DB::table('boxes')->where('status', 'SENT_TO_CARGO')->update(['status' => 'SEND_TO_CARGO']);
        DB::table('boxes')->where('status', 'OTW_INA')->update(['status' => 'ARRIVED_INA']);
        DB::table('boxes')->where('status', 'UP_INVOICE')->update(['status' => 'INVOICE']);

        // 2. Add is_garment to items table
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('is_garment')->default(false)->after('is_sensitive');
        });

        // 3. Add volume_ina to items table (Volume INA = (P×L×T)/4)
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('volume_ina', 10, 4)->nullable()->after('is_garment');
        });

        // 4. Add garment rates to settings
        $garmentRates = [
            ['key' => 'rate_sharing_air_garment', 'value' => '240', 'group' => 'rate_garment'],
            ['key' => 'rate_sharing_sea_garment', 'value' => '75', 'group' => 'rate_garment'],
            ['key' => 'rate_direct_air_garment', 'value' => '220', 'group' => 'rate_garment'],
            ['key' => 'rate_direct_sea_garment', 'value' => '80', 'group' => 'rate_garment'],
        ];

        foreach ($garmentRates as $rate) {
            DB::table('settings')->updateOrInsert(
                ['key' => $rate['key']],
                array_merge($rate, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        // Reverse status renames
        DB::table('boxes')->where('status', 'LAST_CLAIM')->update(['status' => 'LAST_SETOR']);
        DB::table('boxes')->where('status', 'SEND_TO_CARGO')->update(['status' => 'SENT_TO_CARGO']);
        DB::table('boxes')->where('status', 'ARRIVED_INA')->update(['status' => 'OTW_INA']);
        DB::table('boxes')->where('status', 'INVOICE')->update(['status' => 'UP_INVOICE']);

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['is_garment', 'volume_ina']);
        });

        DB::table('settings')->whereIn('key', [
            'rate_sharing_air_garment',
            'rate_sharing_sea_garment',
            'rate_direct_air_garment',
            'rate_direct_sea_garment',
        ])->delete();
    }
};
