<?php

namespace Database\Seeders;

use App\Models\Box;
use App\Models\Checkout;
use App\Models\Complain;
use App\Models\DendaClaim;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\KursHistory;
use App\Models\User;
use App\Models\WhChinaData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Comprehensive Dummy Data Seeder
 *
 * Covers ALL roles, ALL box statuses, ALL item statuses, ALL invoice statuses,
 * ALL checkout statuses, ALL complaint statuses, ALL feature scenarios.
 *
 * 5 Customer accounts with distinct patterns + Owner + Admin + WH China.
 *
 * Password untuk semua akun: password
 */
class RealisticDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Seeding comprehensive dummy data...');

        // ═══════════════════════════════════════════════════════════
        // USERS
        // ═══════════════════════════════════════════════════════════

        // Owner
        $owner = User::create([
            'name' => 'Budi Santoso (Owner)',
            'email' => 'owner@tingwarehouse.com',
            'phone' => '081000000001',
            'ktp_number' => '3175012505900001',
            'address' => 'Jl. Sudirman No. 1, Jakarta Pusat',
            'role' => 'owner',
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Admin 1
        $admin1 = User::create([
            'name' => 'Siti Rahayu (Admin)',
            'email' => 'admin@tingwarehouse.com',
            'phone' => '081000000002',
            'ktp_number' => '3175012505900002',
            'address' => 'Jl. Gatot Subroto No. 5, Jakarta Selatan',
            'role' => 'admin',
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Admin 2
        $admin2 = User::create([
            'name' => 'Dewi Lestari (Admin)',
            'email' => 'admin2@tingwarehouse.com',
            'phone' => '081000000003',
            'ktp_number' => '3175012505900003',
            'address' => 'Jl. Thamrin No. 10, Jakarta Pusat',
            'role' => 'admin',
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // WH China Admin
        $chinaAdmin = User::create([
            'name' => 'Zhang Wei (WH China)',
            'email' => 'china@tingwarehouse.com',
            'phone' => '+8613800138000',
            'ktp_number' => '3175012505900004',
            'address' => 'Guangzhou, China',
            'role' => 'china_admin',
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // ─── 5 CUSTOMERS (beragam skenario) ─────────────────────

        // Customer 1: Toko Aksesoris HP — Heavy user, sharing+direct, banyak barang
        $cust1 = User::create([
            'name' => 'Rina Wijaya (Toko Aksesoris HP)',
            'email' => 'rina@tokorina.com',
            'phone' => '081234567001',
            'ktp_number' => '3175012505900011',
            'address' => 'Jl. Mangga Dua No. 15, Jakarta Utara',
            'role' => 'customer',
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Customer 2: Supplier Fashion — Direct shipping specialist, sensitive items
        $cust2 = User::create([
            'name' => 'Ahmad Faisal (Supplier Fashion)',
            'email' => 'faisal@fashionku.com',
            'phone' => '081234567002',
            'ktp_number' => '3175012505900012',
            'address' => 'Jl. Tanah Abang No. 22, Jakarta Pusat',
            'role' => 'customer',
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Customer 3: Reseller Elektronik — Sharing only, has unpaid invoices + complaints
        $cust3 = User::create([
            'name' => 'Diana Putri (Reseller Elektronik)',
            'email' => 'diana@elektronikmurah.com',
            'phone' => '081234567003',
            'ktp_number' => '3175012505900013',
            'address' => 'Jl. Glodok No. 8, Jakarta Barat',
            'role' => 'customer',
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Customer 4: Importir Mainan — Has no_tuan items, request_to_close direct box
        $cust4 = User::create([
            'name' => 'Hendra Kusuma (Importir Mainan)',
            'email' => 'hendra@mainananak.com',
            'phone' => '081234567004',
            'ktp_number' => '3175012505900014',
            'address' => 'Jl. Surabaya No. 30, Jakarta Pusat',
            'role' => 'customer',
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Customer 5: Pedagang Kosmetik — New customer, few items, unmatched resi
        $cust5 = User::create([
            'name' => 'Maya Sari (Pedagang Kosmetik)',
            'email' => 'maya@kosmetikcantik.com',
            'phone' => '081234567005',
            'ktp_number' => '3175012505900015',
            'address' => 'Jl. Kelapa Gading No. 45, Jakarta Utara',
            'role' => 'customer',
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Customer 6: Inactive (untuk test status inactive)
        $custInactive = User::create([
            'name' => 'Budi Nonaktif',
            'email' => 'budi@nonaktif.com',
            'phone' => '081234567006',
            'ktp_number' => '3175012505900016',
            'address' => 'Jl. Matraman No. 1, Jakarta Timur',
            'role' => 'customer',
            'status' => User::STATUS_INACTIVE,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Customer 7: Pending (untuk test status pending)
        $custPending = User::create([
            'name' => 'Lina Pending',
            'email' => 'lina@pending.com',
            'phone' => '081234567007',
            'ktp_number' => '3175012505900017',
            'address' => 'Jl. Salemba No. 9, Jakarta Pusat',
            'role' => 'customer',
            'status' => User::STATUS_PENDING,
            'email_verified_at' => null,
            'password' => Hash::make('password'),
        ]);

        $this->command->info('✅ Users created: ' . User::count());

        // ═══════════════════════════════════════════════════════════
        // KURS HISTORY
        // ═══════════════════════════════════════════════════════════

        KursHistory::create(['kurs_value' => 2350.00, 'effective_date' => '2026-01-01', 'input_by' => $admin1->id]);
        KursHistory::create(['kurs_value' => 2400.00, 'effective_date' => '2026-03-01', 'input_by' => $admin1->id]);
        KursHistory::create(['kurs_value' => 2460.00, 'effective_date' => '2026-06-01', 'input_by' => $admin1->id]);
        KursHistory::create(['kurs_value' => 2480.00, 'effective_date' => now()->format('Y-m-d'), 'input_by' => $admin2->id]);

        $this->command->info('✅ Kurs history created');

        // ═══════════════════════════════════════════════════════════
        // BOXES — Semua tipe, semua status
        // ═══════════════════════════════════════════════════════════

        // ─── BOX 1: Sharing Air — OPEN (Customer 1 & 3 punya barang) ───
        $boxSharing1 = Box::create([
            'type' => 'sharing',
            'method' => 'air',
            'batch_name' => 'TW-2026-SHARING-AIR-001',
            'huruf_box' => 'A',
            'tracking_number' => 'SF1234567890',
            'status' => Box::STATUS_OPEN,
            'customer_id' => null, // sharing = no owner
            'open_date' => now()->subDays(10),
            'etd' => now()->addDays(5),
            'eta' => now()->addDays(12),
            'notes' => 'Box sharing air reguler, masih terima barang',
        ]);

        // ─── BOX 2: Sharing Sea — OTW_INA (dalam perjalanan) ───
        $boxSharing2 = Box::create([
            'type' => 'sharing',
            'method' => 'sea',
            'batch_name' => 'TW-2026-SHARING-SEA-002',
            'huruf_box' => 'B',
            'tracking_number' => 'SF9876543210',
            'status' => Box::STATUS_OTW_INA,
            'customer_id' => null,
            'open_date' => now()->subDays(45),
            'close_date' => now()->subDays(30),
            'etd' => now()->subDays(25),
            'eta' => now()->subDays(5),
            'stevedoring_date' => now()->subDays(2),
            'notes' => 'Dalam perjalanan laut, ETA segera',
        ]);

        // ─── BOX 3: Sharing Air — UP_INVOICE (siap generate invoice) ───
        $boxSharing3 = Box::create([
            'type' => 'sharing',
            'method' => 'air',
            'batch_name' => 'TW-2026-SHARING-AIR-003',
            'huruf_box' => 'C',
            'tracking_number' => 'SF1111111111',
            'status' => Box::STATUS_UP_INVOICE,
            'customer_id' => null,
            'open_date' => now()->subDays(60),
            'close_date' => now()->subDays(45),
            'etd' => now()->subDays(40),
            'eta' => now()->subDays(20),
            'stevedoring_date' => now()->subDays(18),
            'tagihan_update_date' => now()->subDays(3),
        ]);

        // ─── BOX 4: Direct Sea — OPEN (Customer 2, ada redline!) ───
        $boxDirect1 = Box::create([
            'type' => 'direct',
            'method' => 'sea',
            'batch_name' => 'TW-2026-DIRECT-SEA-004',
            'huruf_box' => 'D',
            'tracking_number' => 'SF2222222222',
            'status' => Box::STATUS_OPEN,
            'customer_id' => $cust2->id,
            'open_date' => now()->subDays(15),
            'etd' => now()->addDays(10),
            'is_redline' => true,
            'redline_note' => 'Tertahan di pelabuhan Guangzhou — inspeksi customs',
            'notes' => 'Direct untuk customer fashion, red line karena customs',
        ]);

        // ─── BOX 5: Direct Air — REQUEST_TO_CLOSE (Customer 4 request close) ───
        $boxDirect2 = Box::create([
            'type' => 'direct',
            'method' => 'air',
            'batch_name' => 'TW-2026-DIRECT-AIR-005',
            'huruf_box' => 'E',
            'tracking_number' => 'SF3333333333',
            'status' => Box::STATUS_REQUEST_TO_CLOSE,
            'customer_id' => $cust4->id,
            'open_date' => now()->subDays(28),
            'notes' => 'Customer request close, menunggu admin konfirmasi',
        ]);

        // ─── BOX 6: Direct Sea — DONE (selesai, history) ───
        $boxDirect3 = Box::create([
            'type' => 'direct',
            'method' => 'sea',
            'batch_name' => 'TW-2026-DIRECT-SEA-006',
            'huruf_box' => 'F',
            'tracking_number' => 'SF4444444444',
            'status' => Box::STATUS_DONE,
            'customer_id' => $cust1->id,
            'open_date' => now()->subDays(90),
            'close_date' => now()->subDays(75),
            'etd' => now()->subDays(70),
            'eta' => now()->subDays(50),
            'stevedoring_date' => now()->subDays(48),
            'tagihan_update_date' => now()->subDays(40),
            'notes' => 'Batch selesai, semua barang sudah dikirim',
        ]);

        // ─── BOX 7: Handcarry — SENT_TO_CARGO ───
        $boxHandcarry = Box::create([
            'type' => 'handcarry',
            'method' => 'air',
            'batch_name' => 'TW-2026-HC-007',
            'huruf_box' => 'G',
            'tracking_number' => null,
            'status' => Box::STATUS_SENT_TO_CARGO,
            'customer_id' => $cust2->id,
            'open_date' => now()->subDays(5),
            'close_date' => now()->subDays(1),
            'notes' => 'Handcarry oleh Zhang Wei',
        ]);

        // ─── BOX 8: Sharing Sea — LAST_SETOR ───
        $boxSharing4 = Box::create([
            'type' => 'sharing',
            'method' => 'sea',
            'batch_name' => 'TW-2026-SHARING-SEA-008',
            'huruf_box' => 'H',
            'tracking_number' => null,
            'status' => Box::STATUS_LAST_SETOR,
            'customer_id' => null,
            'open_date' => now()->subDays(20),
            'last_setor_date' => now()->subDays(1),
            'notes' => 'Last setor, segera ditutup',
        ]);

        $this->command->info('✅ Boxes created: ' . Box::count());

        // ═══════════════════════════════════════════════════════════
        // ITEMS — Beragam skenario per customer
        // ═══════════════════════════════════════════════════════════

        // ─── Customer 1 (Rina - Toko Aksesoris HP) ─────────────

        // Box Sharing 1 (OPEN): 3 items
        $item1_1 = Item::create([
            'box_id' => $boxSharing1->id, 'customer_id' => $cust1->id,
            'name' => 'Casing HP Samsung A16', 'quantity' => 100, 'price_yuan' => 3.50,
            'resi_number' => 'SF1000001', 'is_sensitive' => false,
            'request_type' => json_encode(['extra_bubble_wrap']),
            'notes' => 'Warna campur, handle with care',
        ]);
        $item1_2 = Item::create([
            'box_id' => $boxSharing1->id, 'customer_id' => $cust1->id,
            'name' => 'Tempered Glass iPhone 15', 'quantity' => 200, 'price_yuan' => 1.20,
            'resi_number' => 'SF1000002', 'is_sensitive' => true, 'sensitive_type' => 'kaca',
        ]);
        $item1_3 = Item::create([
            'box_id' => $boxSharing1->id, 'customer_id' => $cust1->id,
            'name' => 'Charger USB-C 65W', 'quantity' => 50, 'price_yuan' => 15.00,
            'resi_number' => 'SF1000003', 'is_sensitive' => false,
            'request_type' => json_encode(['stripping', 'pisahin_album']),
        ]);

        // Box Sharing 2 (OTW_INA): 2 items
        Item::create([
            'box_id' => $boxSharing2->id, 'customer_id' => $cust1->id,
            'name' => 'Earphone Bluetooth TWS', 'quantity' => 150, 'price_yuan' => 8.50,
            'resi_number' => 'SF2000001', 'is_sensitive' => false,
        ]);
        Item::create([
            'box_id' => $boxSharing2->id, 'customer_id' => $cust1->id,
            'name' => 'Powerbank 10000mAh', 'quantity' => 30, 'price_yuan' => 25.00,
            'resi_number' => 'SF2000002', 'is_sensitive' => true, 'sensitive_type' => 'baterai',
        ]);

        // Box Direct 3 (DONE): 2 items — sudah shipped
        Item::create([
            'box_id' => $boxDirect3->id, 'customer_id' => $cust1->id,
            'name' => 'Smart Watch Xiaomi', 'quantity' => 40, 'price_yuan' => 45.00,
            'resi_number' => 'SF6000001', 'is_sensitive' => false, 'status' => Item::STATUS_SHIPPED,
        ]);
        Item::create([
            'box_id' => $boxDirect3->id, 'customer_id' => $cust1->id,
            'name' => 'Speaker Bluetooth JBL', 'quantity' => 20, 'price_yuan' => 35.00,
            'resi_number' => 'SF6000002', 'is_sensitive' => false, 'status' => Item::STATUS_SHIPPED,
        ]);

        // Box Sharing 4 (LAST_SETOR): 1 item
        Item::create([
            'box_id' => $boxSharing4->id, 'customer_id' => $cust1->id,
            'name' => 'Ring Light 26cm', 'quantity' => 25, 'price_yuan' => 12.00,
            'resi_number' => 'SF8000001', 'is_sensitive' => false,
        ]);

        // ─── Customer 2 (Ahmad - Supplier Fashion) ─────────────

        // Box Direct 1 (OPEN + REDLINE): 4 items
        $item2_1 = Item::create([
            'box_id' => $boxDirect1->id, 'customer_id' => $cust2->id,
            'name' => 'Kaos Polos Cotton 240gsm', 'quantity' => 500, 'price_yuan' => 12.00,
            'resi_number' => 'SF3000001', 'is_sensitive' => false,
            'request_type' => json_encode(['stripping']),
        ]);
        $item2_2 = Item::create([
            'box_id' => $boxDirect1->id, 'customer_id' => $cust2->id,
            'name' => 'Hoodie Zipper Oversize', 'quantity' => 200, 'price_yuan' => 35.00,
            'resi_number' => 'SF3000002', 'is_sensitive' => false,
        ]);
        Item::create([
            'box_id' => $boxDirect1->id, 'customer_id' => $cust2->id,
            'name' => 'Celana Cargo Premium', 'quantity' => 150, 'price_yuan' => 28.00,
            'resi_number' => 'SF3000003', 'is_sensitive' => false,
            'request_type' => json_encode(['take_out_freebies']),
        ]);
        Item::create([
            'box_id' => $boxDirect1->id, 'customer_id' => $cust2->id,
            'name' => 'Parfum Import 100ml', 'quantity' => 50, 'price_yuan' => 65.00,
            'resi_number' => 'SF3000004', 'is_sensitive' => true, 'sensitive_type' => 'cairan',
        ]);

        // Box Handcarry (SENT_TO_CARGO): 1 item
        Item::create([
            'box_id' => $boxHandcarry->id, 'customer_id' => $cust2->id,
            'name' => 'Sample Kain Premium', 'quantity' => 5, 'price_yuan' => 200.00,
            'resi_number' => 'SF7000001', 'is_sensitive' => false,
            'notes' => 'Sample untuk klien besar',
        ]);

        // ─── Customer 3 (Diana - Reseller Elektronik) ──────────

        // Box Sharing 1 (OPEN): 2 items
        Item::create([
            'box_id' => $boxSharing1->id, 'customer_id' => $cust3->id,
            'name' => 'Kabel Data USB-C 1m', 'quantity' => 300, 'price_yuan' => 2.50,
            'resi_number' => 'SF4000001', 'is_sensitive' => false,
        ]);
        Item::create([
            'box_id' => $boxSharing1->id, 'customer_id' => $cust3->id,
            'name' => 'Mouse Wireless Logitech', 'quantity' => 80, 'price_yuan' => 18.00,
            'resi_number' => 'SF4000002', 'is_sensitive' => false,
            'request_type' => json_encode(['extra_bubble_wrap', 'pisahin_album']),
        ]);

        // Box Sharing 2 (OTW_INA): 1 item
        Item::create([
            'box_id' => $boxSharing2->id, 'customer_id' => $cust3->id,
            'name' => 'Keyboard Mechanical', 'quantity' => 40, 'price_yuan' => 55.00,
            'resi_number' => 'SF5000001', 'is_sensitive' => false,
        ]);

        // Box Sharing 3 (UP_INVOICE): 2 items — sudah ada berat INA
        Item::create([
            'box_id' => $boxSharing3->id, 'customer_id' => $cust3->id,
            'name' => 'Monitor LED 24 inch', 'quantity' => 10, 'price_yuan' => 350.00,
            'resi_number' => 'SF5500001', 'is_sensitive' => false,
        ]);
        Item::create([
            'box_id' => $boxSharing3->id, 'customer_id' => $cust3->id,
            'name' => 'Webcam HD 1080p', 'quantity' => 60, 'price_yuan' => 22.00,
            'resi_number' => 'SF5500002', 'is_sensitive' => false,
        ]);

        // ─── Customer 4 (Hendra - Importir Mainan) ─────────────

        // Box Direct 2 (REQUEST_TO_CLOSE): 3 items
        Item::create([
            'box_id' => $boxDirect2->id, 'customer_id' => $cust4->id,
            'name' => 'Lego Creator Set', 'quantity' => 30, 'price_yuan' => 85.00,
            'resi_number' => 'SF6000011', 'is_sensitive' => false,
        ]);
        Item::create([
            'box_id' => $boxDirect2->id, 'customer_id' => $cust4->id,
            'name' => 'Boneka Teddy Bear 60cm', 'quantity' => 20, 'price_yuan' => 45.00,
            'resi_number' => 'SF6000012', 'is_sensitive' => false,
            'request_type' => json_encode(['extra_bubble_wrap']),
        ]);
        Item::create([
            'box_id' => $boxDirect2->id, 'customer_id' => $cust4->id,
            'name' => 'RC Car Drift', 'quantity' => 15, 'price_yuan' => 120.00,
            'resi_number' => 'SF6000013', 'is_sensitive' => true, 'sensitive_type' => 'baterai',
        ]);

        // Box Sharing 2 (OTW_INA): 1 item
        Item::create([
            'box_id' => $boxSharing2->id, 'customer_id' => $cust4->id,
            'name' => 'Puzzle 1000 pcs', 'quantity' => 50, 'price_yuan' => 8.00,
            'resi_number' => 'SF6500001', 'is_sensitive' => false,
        ]);

        // ─── Customer 5 (Maya - Pedagang Kosmetik) ─────────────
        // New customer, few items, some unmatched

        // Box Sharing 8 (LAST_SETOR): 2 items
        Item::create([
            'box_id' => $boxSharing4->id, 'customer_id' => $cust5->id,
            'name' => 'Lipstick Matte Set', 'quantity' => 100, 'price_yuan' => 5.50,
            'resi_number' => 'SF7000011', 'is_sensitive' => false,
        ]);
        Item::create([
            'box_id' => $boxSharing4->id, 'customer_id' => $cust5->id,
            'name' => 'Sheet Mask Aloe Vera', 'quantity' => 200, 'price_yuan' => 1.80,
            'resi_number' => 'SF7000012', 'is_sensitive' => false,
            'request_type' => json_encode(['extra_bubble_wrap']),
        ]);

        $this->command->info('✅ Items created: ' . Item::count());

        // ═══════════════════════════════════════════════════════════
        // WH CHINA DATA — Matched & Unmatched
        // ═══════════════════════════════════════════════════════════

        // Matched items (linked to items)
        $whDataSets = [
            // Customer 1 items
            ['resi' => 'SF1000001', 'item_id' => $item1_1->id, 'huruf' => 'A', 'berat' => 15.5, 'berat_ina' => 16.0, 'P' => 50, 'L' => 40, 'T' => 30, 'jasa' => 250000, 'tax' => 1120],
            ['resi' => 'SF1000002', 'item_id' => $item1_2->id, 'huruf' => 'A', 'berat' => 8.2, 'berat_ina' => 8.5, 'P' => 40, 'L' => 30, 'T' => 20, 'jasa' => 150000, 'tax' => 680],
            ['resi' => 'SF1000003', 'item_id' => $item1_3->id, 'huruf' => 'A', 'berat' => 5.0, 'berat_ina' => null, 'P' => 30, 'L' => 25, 'T' => 15, 'jasa' => 80000, 'tax' => 350],
            // Customer 2 items
            ['resi' => 'SF3000001', 'item_id' => $item2_1->id, 'huruf' => 'D', 'berat' => 85.0, 'berat_ina' => 87.0, 'P' => 120, 'L' => 80, 'T' => 60, 'jasa' => 1200000, 'tax' => 6090],
            ['resi' => 'SF3000002', 'item_id' => $item2_2->id, 'huruf' => 'D', 'berat' => 45.0, 'berat_ina' => 46.5, 'P' => 80, 'L' => 60, 'T' => 50, 'jasa' => 650000, 'tax' => 3255],
            // Customer 3 items (OTW)
            ['resi' => 'SF5000001', 'item_id' => Item::where('resi_number', 'SF5000001')->first()?->id, 'huruf' => 'B', 'berat' => 12.0, 'berat_ina' => null, 'P' => 60, 'L' => 40, 'T' => 30, 'jasa' => 180000, 'tax' => null],
            // Customer 3 items (UP_INVOICE — sudah ada berat INA)
            ['resi' => 'SF5500001', 'item_id' => Item::where('resi_number', 'SF5500001')->first()?->id, 'huruf' => 'C', 'berat' => 120.0, 'berat_ina' => 125.0, 'P' => 150, 'L' => 80, 'T' => 60, 'jasa' => 1800000, 'tax' => 18750],
            ['resi' => 'SF5500002', 'item_id' => Item::where('resi_number', 'SF5500002')->first()?->id, 'huruf' => 'C', 'berat' => 8.0, 'berat_ina' => 8.2, 'P' => 40, 'L' => 30, 'T' => 20, 'jasa' => 120000, 'tax' => 574],
        ];

        foreach ($whDataSets as $d) {
            $vol = round(($d['P'] * $d['L'] * $d['T']) / 6000, 4);
            WhChinaData::create([
                'resi_number' => $d['resi'],
                'item_id' => $d['item_id'],
                'huruf_box' => $d['huruf'],
                'ukuran_box' => $d['P'] . 'x' . $d['L'] . 'x' . $d['T'],
                'berat' => $d['berat'],
                'berat_ina' => $d['berat_ina'],
                'panjang' => $d['P'],
                'lebar' => $d['L'],
                'tinggi' => $d['T'],
                'volume' => $vol,
                'biaya_jasa' => $d['jasa'],
                'biaya_tax' => $d['tax'],
                'matched_at' => $d['item_id'] ? now()->subDays(rand(1, 10)) : null,
                'input_by' => $chinaAdmin->id,
            ]);
        }

        // Unmatched WH China data (belum diklaim customer)
        $unmatched = [
            ['resi' => 'SF9990001', 'huruf' => 'B', 'berat' => 5.5, 'P' => 30, 'L' => 25, 'T' => 20, 'jasa' => 80000],
            ['resi' => 'SF9990002', 'huruf' => 'B', 'berat' => 3.2, 'P' => 25, 'L' => 20, 'T' => 15, 'jasa' => 50000],
            ['resi' => 'SF9990003', 'huruf' => 'H', 'berat' => 7.8, 'P' => 40, 'L' => 35, 'T' => 25, 'jasa' => 120000],
        ];

        foreach ($unmatched as $d) {
            $vol = round(($d['P'] * $d['L'] * $d['T']) / 6000, 4);
            WhChinaData::create([
                'resi_number' => $d['resi'],
                'huruf_box' => $d['huruf'],
                'ukuran_box' => $d['P'] . 'x' . $d['L'] . 'x' . $d['T'],
                'berat' => $d['berat'],
                'panjang' => $d['P'],
                'lebar' => $d['L'],
                'tinggi' => $d['T'],
                'volume' => $vol,
                'biaya_jasa' => $d['jasa'],
                'input_by' => $chinaAdmin->id,
            ]);
        }

        $this->command->info('✅ WH China data created: ' . WhChinaData::count());

        // ═══════════════════════════════════════════════════════════
        // NO TUAN ITEMS — Barang tanpa pemilik
        // ═══════════════════════════════════════════════════════════

        Item::create([
            'box_id' => $boxSharing2->id, 'customer_id' => null,
            'name' => 'Mystery Box Accessories', 'quantity' => 10, 'price_yuan' => 0,
            'resi_number' => 'SF_NT_001', 'status' => Item::STATUS_NO_TUAN,
        ]);
        Item::create([
            'box_id' => $boxSharing4->id, 'customer_id' => null,
            'name' => 'Sticker Pack Unbranded', 'quantity' => 50, 'price_yuan' => 0,
            'resi_number' => 'SF_NT_002', 'status' => Item::STATUS_NO_TUAN,
        ]);

        // Lelang items
        Item::create([
            'box_id' => $boxSharing3->id, 'customer_id' => null,
            'name' => 'Headset Gaming bekas', 'quantity' => 5, 'price_yuan' => 0,
            'resi_number' => 'SF_LELANG_001', 'status' => Item::STATUS_LELANG,
        ]);

        $this->command->info('✅ No Tuan & Lelang items created');

        // ═══════════════════════════════════════════════════════════
        // INVOICES — Semua status
        // ═══════════════════════════════════════════════════════════

        // Invoice 1: Customer 1, Box DONE, VERIFIED (sudah lunas)
        $inv1 = Invoice::create([
            'invoice_number' => 'INV-2026-001',
            'box_id' => $boxDirect3->id,
            'customer_id' => $cust1->id,
            'weight' => 25.0,
            'volume' => 8.50,
            'fee_tax' => 5875,
            'fee_wh' => 5000,
            'fee_packing' => 5000,
            'add_on' => 0,
            'denda_total' => 0,
            'grand_total' => 15875,
            'status' => Invoice::STATUS_VERIFIED,
            'payment_deadline' => now()->subDays(30),
        ]);

        // Invoice 2: Customer 3, Box UP_INVOICE, WAITING_PAYMENT (belum bayar!)
        $inv2 = Invoice::create([
            'invoice_number' => 'INV-2026-002',
            'box_id' => $boxSharing3->id,
            'customer_id' => $cust3->id,
            'weight' => 125.0,
            'volume' => 12.00,
            'fee_tax' => 19324,
            'fee_wh' => 8000,
            'fee_packing' => 6500,
            'add_on' => 500,
            'denda_total' => 0,
            'grand_total' => 34324,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
            'payment_deadline' => now()->addDays(3),
        ]);

        // Invoice 3: Customer 3, WAITING_VERIFICATION (sudah upload bukti bayar)
        $inv3 = Invoice::create([
            'invoice_number' => 'INV-2026-003',
            'box_id' => $boxSharing2->id,
            'customer_id' => $cust3->id,
            'weight' => 12.0,
            'volume' => 7.20,
            'fee_tax' => 840,
            'fee_wh' => 5000,
            'fee_packing' => 5000,
            'add_on' => 0,
            'denda_total' => 0,
            'grand_total' => 10840,
            'status' => Invoice::STATUS_WAITING_VERIFICATION,
            'payment_deadline' => now()->subDays(1),
        ]);

        // Invoice 4: Customer 2, Direct, WAITING_PAYMENT (dengan denda)
        $inv4 = Invoice::create([
            'invoice_number' => 'INV-2026-004',
            'box_id' => $boxDirect1->id,
            'customer_id' => $cust2->id,
            'weight' => 87.0,
            'volume' => 28.80,
            'fee_tax' => 6090,
            'fee_wh' => 8000,
            'fee_packing' => 6500,
            'add_on' => 0,
            'denda_total' => 50000,
            'grand_total' => 70590,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
            'payment_deadline' => now()->addDays(7),
        ]);

        $this->command->info('✅ Invoices created: ' . Invoice::count());

        // ═══════════════════════════════════════════════════════════
        // DENDA CLAIMS
        // ═══════════════════════════════════════════════════════════

        DendaClaim::create([
            'customer_id' => $cust2->id,
            'item_id' => $item2_1->id,
            'jumlah_denda' => 50000,
            'invoice_id' => $inv4->id,
            'status' => DendaClaim::STATUS_TAGGED,
        ]);

        DendaClaim::create([
            'customer_id' => $cust3->id,
            'item_id' => Item::where('customer_id', $cust3->id)->first()->id,
            'jumlah_denda' => 25000,
            'status' => DendaClaim::STATUS_PENDING,
        ]);

        $this->command->info('✅ Denda claims created');

        // ═══════════════════════════════════════════════════════════
        // CHECKOUTS — Semua status
        // ═══════════════════════════════════════════════════════════

        // Checkout 1: Customer 1, SENT (sudah dikirim, ada tracking)
        Checkout::create([
            'invoice_id' => $inv1->id,
            'customer_id' => $cust1->id,
            'address_type' => 'personal',
            'recipient_name' => 'Rina Wijaya',
            'recipient_phone' => '081234567001',
            'address' => 'Jl. Mangga Dua No. 15, Jakarta Utara, 14430',
            'sender_name' => 'Ting Warehouse',
            'sender_phone' => '081000000000',
            'tracking_number' => 'JNE123456789',
            'status' => Checkout::STATUS_SENT,
        ]);

        // Checkout 2: Customer 3, ON_PROCESS (sedang dipacking)
        Checkout::create([
            'invoice_id' => $inv3->id,
            'customer_id' => $cust3->id,
            'address_type' => 'dropship',
            'recipient_name' => 'Diana Putri',
            'recipient_phone' => '081234567003',
            'address' => 'Jl. Glodok No. 8, Lt. 2, Jakarta Barat, 11120',
            'sender_name' => 'Ting Warehouse',
            'sender_phone' => '081000000000',
            'status' => Checkout::STATUS_ON_PROCESS,
        ]);

        $this->command->info('✅ Checkouts created');

        // ═══════════════════════════════════════════════════════════
        // COMPLAINTS — Beragam status
        // ═══════════════════════════════════════════════════════════

        // Komplain 1: Customer 3, OPEN (barang rusak)
        Complain::create([
            'customer_id' => $cust3->id,
            'box_id' => $boxSharing2->id,
            'type' => 'barang_rusak',
            'invoice_number' => 'INV-2026-003',
            'resi_number' => 'SF5000001',
            'description' => 'Keyboard mechanical sampai dengan switch merah tidak berfungsi pada 5 unit. Packaging dari China kurang bubble wrap.',
            'status' => Complain::STATUS_OPEN,
        ]);

        // Komplain 2: Customer 1, RESOLVED (sudah selesai)
        Complain::create([
            'customer_id' => $cust1->id,
            'box_id' => $boxDirect3->id,
            'type' => 'barang_hilang',
            'invoice_number' => 'INV-2026-001',
            'resi_number' => 'SF6000001',
            'description' => '2 unit smart watch tidak ada dalam box saat diterima.',
            'resolution' => 'replacement',
            'status' => Complain::STATUS_RESOLVED,
        ]);

        // Komplain 3: Customer 2, IN_REVIEW (sedang ditinjau)
        Complain::create([
            'customer_id' => $cust2->id,
            'box_id' => $boxDirect1->id,
            'type' => 'salah_barang',
            'invoice_number' => 'INV-2026-004',
            'resi_number' => 'SF3000003',
            'description' => 'Celana cargo yang diterima ukurannya salah semua. Seharusnya M dan L, yang datang S semua.',
            'status' => Complain::STATUS_IN_REVIEW,
        ]);

        $this->command->info('✅ Complaints created');

        // ═══════════════════════════════════════════════════════════
        // ACTIVITY LOGS (sample)
        // ═══════════════════════════════════════════════════════════

        \App\Models\ActivityLog::create([
            'user_id' => $admin1->id,
            'subject_type' => 'App\\Models\\Box',
            'subject_id' => $boxSharing1->id,
            'event' => 'created',
            'old_values' => null,
            'new_values' => ['batch_name' => 'TW-2026-SHARING-AIR-001', 'type' => 'sharing'],
        ]);
        \App\Models\ActivityLog::create([
            'user_id' => $cust1->id,
            'subject_type' => 'App\\Models\\Item',
            'subject_id' => $item1_1->id,
            'event' => 'created',
            'old_values' => null,
            'new_values' => ['name' => 'Casing HP Samsung A16', 'quantity' => 100],
        ]);
        \App\Models\ActivityLog::create([
            'user_id' => $admin2->id,
            'subject_type' => 'App\\Models\\Setting',
            'subject_id' => 1,
            'event' => 'updated',
            'old_values' => ['value' => '250'],
            'new_values' => ['value' => '255'],
        ]);
        \App\Models\ActivityLog::create([
            'user_id' => $owner->id,
            'subject_type' => 'App\\Models\\Invoice',
            'subject_id' => $inv1->id,
            'event' => 'updated',
            'old_values' => ['status' => 'waiting_verification'],
            'new_values' => ['status' => 'verified'],
        ]);

        $this->command->info('✅ Activity logs created');

        // ═══════════════════════════════════════════════════════════
        // SUMMARY
        // ═══════════════════════════════════════════════════════════

        $this->command->info('');
        $this->command->info('════════════════════════════════════════════════════');
        $this->command->info('🎉 COMPREHENSIVE SEED COMPLETE');
        $this->command->info('════════════════════════════════════════════════════');
        $this->command->info("Users:          " . User::count());
        $this->command->info("  - Owner:      1 (owner@tingwarehouse.com)");
        $this->command->info("  - Admin:      2 (admin@tingwarehouse.com, admin2@tingwarehouse.com)");
        $this->command->info("  - WH China:   1 (china@tingwarehouse.com)");
        $this->command->info("  - Customer:   5 active + 1 inactive + 1 pending");
        $this->command->info("Boxes:          " . Box::count() . " (sharing/direct/handcarry, semua status)");
        $this->command->info("Items:          " . Item::count() . " (active/no_tuan/lelang/shipped)");
        $this->command->info("WH China Data:  " . WhChinaData::count() . " (matched + unmatched)");
        $this->command->info("Invoices:       " . Invoice::count() . " (waiting_payment/waiting_verification/verified)");
        $this->command->info("Checkouts:      " . Checkout::count() . " (request/on_process/sent)");
        $this->command->info("Complaints:     " . Complain::count() . " (open/in_review/resolved)");
        $this->command->info("Denda Claims:   " . DendaClaim::count());
        $this->command->info("Kurs History:   " . KursHistory::count());
        $this->command->info('');
        $this->command->info('🔑 Login: semua akun pakai password "password"');
        $this->command->info('════════════════════════════════════════════════════');
    }
}
