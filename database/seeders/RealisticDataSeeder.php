<?php

namespace Database\Seeders;

use App\Models\Box;
use App\Models\Checkout;
use App\Models\Complain;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RealisticDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding realistic data for performance testing...');

        // ─── Settings (17 rate params) ───────────────────────────
        $this->seedSettings();

        // ─── Owner + Admins ──────────────────────────────────────
        $owner = User::create([
            'name' => 'Owner Utama',
            'email' => 'owner@tingwarehouse.com',
            'phone' => '081000000001',
            'ktp_number' => '1111111111111111',
            'address' => 'Jakarta Pusat',
            'role' => 'owner',
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        $admins = collect();
        foreach (range(1, 3) as $i) {
            $admins->push(User::create([
                'name' => "Admin {$i}",
                'email' => "admin{$i}@tingwarehouse.com",
                'phone' => '08100000000' . ($i + 1),
                'ktp_number' => str_pad(2000 + $i, 16, '0', STR_PAD_LEFT),
                'address' => "Kantor Admin {$i}, Jakarta",
                'role' => 'admin',
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]));
        }


        $this->command->info('');
        $this->command->info('=== Seed Complete ===');
        $this->command->info("Users:      " . User::count());
        $this->command->info("Settings:   " . Setting::count());
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'kurs_yuan_idr', 'value' => '2460', 'group' => 'currency'],
            ['key' => 'rate_sharing_air_berat', 'value' => '255', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_air_volume', 'value' => '230', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sea_berat', 'value' => '70', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sea_volume', 'value' => '83', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_air_berat', 'value' => '315', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_air_volume', 'value' => '315', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_sea_berat', 'value' => '95', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_sea_volume', 'value' => '95', 'group' => 'rate_sharing'],
            ['key' => 'rate_direct_air_berat', 'value' => '230', 'group' => 'rate_direct'],
            ['key' => 'rate_direct_air_volume', 'value' => '160', 'group' => 'rate_direct'],
            ['key' => 'rate_direct_sea_berat', 'value' => '70', 'group' => 'rate_direct'],
            ['key' => 'rate_direct_sea_volume', 'value' => '90', 'group' => 'rate_direct'],
            ['key' => 'fee_packing_150', 'value' => '5000', 'group' => 'fee_packing'],
            ['key' => 'fee_packing_1000', 'value' => '6500', 'group' => 'fee_packing'],
            ['key' => 'fee_packing_2000', 'value' => '8000', 'group' => 'fee_packing'],
            ['key' => 'fee_packing_extra_per_kg', 'value' => '1500', 'group' => 'fee_packing'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
