<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Seed the 17 rate parameters from PRD §4.12.
     *
     * All values are default — admin can update via /admin/settings.
     * These are the exact keys from the PRD, do not rename.
     *
     * @return void
     */
    public function run(): void
    {
        $settings = [
            // ─── Currency ────────────────────────────────────────────
            ['key' => 'kurs_yuan_idr', 'value' => '2460', 'group' => 'currency'],

            // ─── Rate Sharing ────────────────────────────────────────
            ['key' => 'rate_sharing_air_berat', 'value' => '255', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_air_volume', 'value' => '230', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sea_berat', 'value' => '70', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sea_volume', 'value' => '83', 'group' => 'rate_sharing'],

            // ─── Rate Sharing Sensitive ──────────────────────────────
            ['key' => 'rate_sharing_sensitive_air_berat', 'value' => '315', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_air_volume', 'value' => '315', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_sea_berat', 'value' => '95', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_sea_volume', 'value' => '95', 'group' => 'rate_sharing'],

            // ─── Rate Direct ─────────────────────────────────────────
            ['key' => 'rate_direct_air_berat', 'value' => '230', 'group' => 'rate_direct'],
            ['key' => 'rate_direct_air_volume', 'value' => '160', 'group' => 'rate_direct'],
            ['key' => 'rate_direct_sea_berat', 'value' => '70', 'group' => 'rate_direct'],
            ['key' => 'rate_direct_sea_volume', 'value' => '90', 'group' => 'rate_direct'],

            // ─── Sprint 1: Rate Garment ────────────────────────────
            ['key' => 'rate_sharing_air_garment', 'value' => '240', 'group' => 'rate_garment'],
            ['key' => 'rate_sharing_sea_garment', 'value' => '75', 'group' => 'rate_garment'],
            ['key' => 'rate_direct_air_garment', 'value' => '220', 'group' => 'rate_garment'],
            ['key' => 'rate_direct_sea_garment', 'value' => '80', 'group' => 'rate_garment'],

            // ─── Fee Packing (tiered) ────────────────────────────────
            ['key' => 'fee_packing_150', 'value' => '5000', 'group' => 'fee_packing'],
            ['key' => 'fee_packing_1000', 'value' => '6500', 'group' => 'fee_packing'],
            ['key' => 'fee_packing_2000', 'value' => '8000', 'group' => 'fee_packing'],
            ['key' => 'fee_packing_extra_per_kg', 'value' => '1500', 'group' => 'fee_packing'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
