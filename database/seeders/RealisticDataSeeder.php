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

/**
 * Realistic data seeder for performance testing.
 *
 * §1.6 estimates: 50-200 customers, 20-50 resellers, 10-30 dropshippers
 * We seed 250 customers, 1200 boxes, 6000 items, 3000 invoices.
 *
 * Usage: php artisan db:seed --class=RealisticDataSeeder
 */
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

        // ─── Customers (250) ─────────────────────────────────────
        $this->command->info('Creating 250 customers...');
        $customers = collect();
        $statuses = [User::STATUS_ACTIVE, User::STATUS_ACTIVE, User::STATUS_ACTIVE, User::STATUS_PENDING, User::STATUS_INACTIVE];

        foreach (range(1, 250) as $i) {
            $customers->push(User::create([
                'name' => "Customer {$i} " . fake()->lastName(),
                'email' => "customer{$i}@example.com",
                'phone' => '08' . str_pad($i, 9, '0', STR_PAD_LEFT),
                'ktp_number' => str_pad(300000 + $i, 16, '0', STR_PAD_LEFT),
                'address' => fake()->address(),
                'role' => 'customer',
                'status' => $statuses[$i % 5],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]));

            if ($i % 50 === 0) {
                $this->command->info("  {$i}/250 customers created");
            }
        }

        // ─── Boxes (1200) ────────────────────────────────────────
        $this->command->info('Creating 1200 boxes...');
        $boxStatuses = [
            Box::STATUS_OPEN,
            Box::STATUS_OPEN,
            Box::STATUS_SENT_TO_CARGO,
            Box::STATUS_OTW_INA,
            Box::STATUS_UP_INVOICE,
            Box::STATUS_DONE,
            Box::STATUS_DONE,
            Box::STATUS_DONE,
        ];
        $types = ['sharing', 'sharing', 'sharing', 'direct'];
        $methods = ['air', 'air', 'sea'];

        $activeCustomers = $customers->where('status', User::STATUS_ACTIVE)->values();
        $boxIds = collect();

        foreach (range(1, 1200) as $i) {
            $customer = $activeCustomers->random();
            $box = Box::create([
                'type' => $types[$i % 4],
                'tracking_number' => 'TRK-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'batch_name' => 'Batch-' . str_pad(intdiv($i, 10) + 1, 3, '0', STR_PAD_LEFT),
                'status' => $boxStatuses[$i % 8],
                'method' => $methods[$i % 3],
                'customer_id' => $customer->id,
                'notes' => $i % 10 === 0 ? "Catatan box {$i}" : null,
                'etd' => $i % 3 === 0 ? now()->addDays(rand(5, 30)) : null,
                'eta' => $i % 3 === 0 ? now()->addDays(rand(35, 60)) : null,
            ]);
            $boxIds->push($box->id);

            if ($i % 200 === 0) {
                $this->command->info("  {$i}/1200 boxes created");
            }
        }

        // ─── Items (6000) ────────────────────────────────────────
        $this->command->info('Creating 6000 items...');
        $itemNames = [
            'Kaos Polos', 'Sepatu Sneakers', 'Tas Ransel', 'Jaket Hoodie',
            'Celana Jeans', 'Kemeja Flanel', 'Topi Baseball', 'Kacamata Hitam',
            'Jam Tangan', 'Dompet Kulit', 'Sandal Slide', 'Baju Koko',
            'Sarung Tangan', 'Scarf Syal', 'Ikat Pinggang', 'HP Case',
            'Charger USB', 'Power Bank', 'Earphone Bluetooth', 'Speaker Mini',
        ];

        foreach (range(1, 6000) as $i) {
            $boxId = $boxIds->random();
            $box = Box::find($boxId);
            $customerId = $box->customer_id;

            Item::create([
                'box_id' => $boxId,
                'customer_id' => $customerId,
                'name' => $itemNames[$i % 20],
                'quantity' => rand(1, 50),
                'price_yuan' => rand(10, 5000) + (rand(0, 99) / 100),
                'resi_number' => 'RESI-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'proof_co' => 'proof-co/' . Str::uuid() . '.jpg',
                'is_sensitive' => $i % 10 === 0,
                'sensitive_type' => $i % 10 === 0 ? 'Elektronik' : null,
                'arrived_china' => $i % 4 === 0,
                'arrived_indonesia' => $i % 6 === 0,
            ]);

            if ($i % 1000 === 0) {
                $this->command->info("  {$i}/6000 items created");
            }
        }

        // ─── Invoices (3000) ─────────────────────────────────────
        $this->command->info('Creating 3000 invoices...');
        $invoiceStatuses = [
            Invoice::STATUS_WAITING_PAYMENT,
            Invoice::STATUS_WAITING_PAYMENT,
            Invoice::STATUS_WAITING_VERIFICATION,
            Invoice::STATUS_VERIFIED,
            Invoice::STATUS_VERIFIED,
            Invoice::STATUS_VERIFIED,
        ];

        $otwBoxes = Box::where('status', Box::STATUS_OTW_INA)->pluck('id')->toArray();
        $upInvoiceBoxes = Box::where('status', Box::STATUS_UP_INVOICE)->pluck('id')->toArray();
        $doneBoxes = Box::where('status', Box::STATUS_DONE)->pluck('id')->toArray();
        $allBoxIds = array_merge($otwBoxes, $upInvoiceBoxes, $doneBoxes);

        foreach (range(1, 3000) as $i) {
            if (empty($allBoxIds)) break;

            $boxId = $allBoxIds[array_rand($allBoxIds)];
            $box = Box::find($boxId);
            if (!$box || !$box->customer_id) continue;

            $weight = rand(10, 500) + (rand(0, 99) / 100);
            $volume = rand(1, 100) + (rand(0, 99) / 100);
            $feeTax = $weight * rand(70, 315);
            $feeWh = $weight <= 150 ? 5000 : ($weight <= 1000 ? 6500 : 8000);
            $feePacking = $feeWh;
            $addOn = $i % 20 === 0 ? rand(10000, 100000) : 0;

            Invoice::create([
                'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'box_id' => $boxId,
                'customer_id' => $box->customer_id,
                'weight' => $weight,
                'volume' => $volume,
                'fee_tax' => $feeTax,
                'fee_wh' => $feeWh,
                'fee_packing' => $feePacking,
                'add_on' => $addOn,
                'grand_total' => $feeTax + $feeWh + $feePacking + $addOn,
                'status' => $invoiceStatuses[$i % 6],
            ]);

            if ($i % 500 === 0) {
                $this->command->info("  {$i}/3000 invoices created");
            }
        }

        // ─── Checkouts (500) ─────────────────────────────────────
        $this->command->info('Creating 500 checkouts...');
        $verifiedInvoices = Invoice::where('status', Invoice::STATUS_VERIFIED)->pluck('id')->toArray();

        foreach (range(1, 500) as $i) {
            if (empty($verifiedInvoices)) break;

            $invoiceId = $verifiedInvoices[array_rand($verifiedInvoices)];
            $invoice = Invoice::find($invoiceId);
            if (!$invoice) continue;

            Checkout::create([
                'invoice_id' => $invoiceId,
                'customer_id' => $invoice->customer_id,
                'address_type' => $i % 3 === 0 ? 'dropship' : 'personal',
                'recipient_name' => fake()->name(),
                'recipient_phone' => '08' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'address' => fake()->address(),
                'status' => ['request', 'on_process', 'sent'][$i % 3],
            ]);

            if ($i % 100 === 0) {
                $this->command->info("  {$i}/500 checkouts created");
            }
        }

        // ─── Complaints (100) ────────────────────────────────────
        $this->command->info('Creating 100 complaints...');
        $complaintTypes = [
            'Kurang Barang Ekspedisi', 'Tidak Arrived China', 'Barang Rusak',
            'Barang Salah', 'Kurang Barang Indonesia', 'Lainnya',
        ];
        $complaintStatuses = [Complain::STATUS_OPEN, Complain::STATUS_IN_REVIEW, Complain::STATUS_PROCESSING, Complain::STATUS_RESOLVED];

        foreach (range(1, 100) as $i) {
            $customer = $activeCustomers->random();
            $customerBoxes = Box::where('customer_id', $customer->id)->pluck('id')->toArray();
            if (empty($customerBoxes)) continue;

            Complain::create([
                'customer_id' => $customer->id,
                'box_id' => $customerBoxes[array_rand($customerBoxes)],
                'type' => $complaintTypes[$i % 6],
                'resolution' => $i % 2 === 0 ? 'refund' : 'replacement',
                'invoice_number' => 'INV-' . str_pad(rand(1, 3000), 4, '0', STR_PAD_LEFT),
                'resi_number' => 'RESI-' . str_pad(rand(1, 6000), 6, '0', STR_PAD_LEFT),
                'description' => fake()->paragraph(),
                'status' => $complaintStatuses[$i % 4],
            ]);
        }

        // ─── Summary ─────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('=== Seed Complete ===');
        $this->command->info("Users:      " . User::count());
        $this->command->info("Boxes:      " . Box::count());
        $this->command->info("Items:      " . Item::count());
        $this->command->info("Invoices:   " . Invoice::count());
        $this->command->info("Checkouts:  " . Checkout::count());
        $this->command->info("Complaints: " . Complain::count());
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
            Setting::create($setting);
        }
    }
}
