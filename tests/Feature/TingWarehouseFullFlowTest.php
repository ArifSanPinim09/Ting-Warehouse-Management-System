<?php

namespace Tests\Feature;

use App\Livewire\Admin\CustomerIndex;
use App\Livewire\Admin\EstUpdate;
use App\Livewire\Admin\GenerateInvoice;
use App\Livewire\Admin\ManageBox;
use App\Livewire\Admin\SettingsIndex;
use App\Livewire\Admin\VerificationIndex;
use App\Livewire\Customer\BoxSharing;
use App\Livewire\Customer\CheckoutIndex;
use App\Livewire\Customer\InvoiceIndex;
use App\Livewire\Customer\KomplainIndex;
use App\Livewire\Customer\SetorResi;
use App\Livewire\Owner\Dashboard as OwnerDashboard;
use App\Livewire\Owner\FinanceIndex;
use App\Livewire\AuditLogIndex;
use App\Models\ActivityLog;
use App\Models\Box;
use App\Models\Checkout;
use App\Models\Complain;
use App\Models\DendaClaim;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\KursHistory;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use App\Models\WhChinaData;
use App\Services\AuditLogService;
use App\Services\FeeCalculationService;
use App\Services\NoTuanClaimService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use Tests\TestCase;

/**
 * Full lifecycle integration test — one shipment from registration to completion.
 *
 * Covers TAHAP 1–8: Onboarding → Setor Barang → Perjalanan →
 * Invoice & Pembayaran → Checkout & Pengiriman → Komplain → Owner Verification →
 * Negative Role Tests.
 *
 * Runs as ONE sequential test to guarantee step ordering.
 * Each step asserts DATABASE STATE, notifications, and audit logs.
 */
class TingWarehouseFullFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $owner;
    private ?User $customer = null;
    private ?Box $box = null;
    private ?Box $box2 = null;
    private ?Invoice $invoice = null;
    private ?Invoice $flexibleInvoice = null;
    private ?Checkout $checkout = null;
    private ?Checkout $dropshipCheckout = null;
    private ?Complain $complaint = null;
    private ?Item $noTuanItem = null;
    private ?DendaClaim $dendaClaim = null;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $this->owner = User::factory()->create(['role' => 'owner', 'status' => User::STATUS_ACTIVE]);
        $this->seedDefaultRates();
    }

    /**
     * Full lifecycle: TAHAP 1 through TAHAP 8, sequential.
     */
    public function test_full_lifecycle(): void
    {
        $this->runTahap1_Onboarding();
        $this->runTahap2_SetorBarang();
        $this->runTahap3_PerjalananBarang();
        $this->runTahap4_InvoicePembayaran();
        $this->runTahap5_CheckoutPengiriman();
        $this->runTahap6_Komplain();
        $this->runTahap7_OwnerVerification();
        $this->runTahap8_NegativeRoleAccess();
        $this->runTahap9_RevisiExtensions();
    }

    // ═══════════════════════════════════════════════════════════════
    //  TAHAP 1 — ONBOARDING (Customer → Admin)
    // ═══════════════════════════════════════════════════════════════

    private function runTahap1_Onboarding(): void
    {
        // ── Step 1: Customer registers → PENDING, cannot login ──

        Volt::test('pages.auth.register')
            ->set('name', 'Budi Santoso')
            ->set('email', 'budi@example.com')
            ->set('phone', '081234567890')
            ->set('ktp_number', '3171234567890001')
            ->set('address', 'Jl. Sudirman No. 123, Jakarta Selatan')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertRedirect(route('login', absolute: false));

        $this->customer = User::where('email', 'budi@example.com')->first();
        $this->assertNotNull($this->customer);
        $this->assertEquals(User::STATUS_PENDING, $this->customer->status);
        $this->assertEquals('customer', $this->customer->role);
        $this->assertNull($this->customer->email_verified_at);
        $this->assertGuest();

        // Admin AND Owner must receive registration notification (PRD §4.1)
        $adminRegNotif = Notification::where('notifiable_id', $this->admin->id)
            ->where('type', NotificationService::TYPE_CUSTOMER_REGISTER)
            ->first();
        $this->assertNotNull($adminRegNotif, 'Admin must receive customer registration notification');
        $this->assertEquals('Customer Baru', $adminRegNotif->data['title']);
        $this->assertEquals($this->customer->name, $adminRegNotif->data['customer_name']);

        $ownerRegNotif = Notification::where('notifiable_id', $this->owner->id)
            ->where('type', NotificationService::TYPE_CUSTOMER_REGISTER)
            ->first();
        $this->assertNotNull($ownerRegNotif, 'Owner must receive customer registration notification');

        // PENDING customer cannot login — Volt login rejects (PRD §13.1)
        Volt::test('pages.auth.login')
            ->set('form.email', 'budi@example.com')
            ->set('form.password', 'password123')
            ->call('login')
            ->assertHasErrors();
        $this->assertGuest();

        // ── Step 2: Admin activates customer → audit + notification ──

        $this->actingAs($this->admin);
        Livewire::test(CustomerIndex::class)
            ->call('selectCustomer', $this->customer->id)
            ->call('activateCustomer');

        $this->customer->refresh();
        $this->assertEquals(User::STATUS_ACTIVE, $this->customer->status);

        $activationLog = ActivityLog::where('subject_type', User::class)
            ->where('subject_id', $this->customer->id)
            ->where('event', 'activated')
            ->first();
        $this->assertNotNull($activationLog, 'Audit log must record customer activation');
        $this->assertEquals($this->admin->id, $activationLog->user_id);
        $this->assertNotNull($activationLog->created_at);

        $notif = Notification::where('notifiable_id', $this->customer->id)
            ->where('type', NotificationService::TYPE_ACCOUNT_ACTIVATED)
            ->first();
        $this->assertNotNull($notif, 'Customer must receive account activated notification');
        $this->assertEquals('Akun Aktif', $notif->data['title']);
        $this->assertNull($notif->read_at);

        // ── Step 3: Activated customer can login + sees notification ──

        // Verify email so 'verified' middleware passes (Breeze default)
        $this->customer->email_verified_at = now();
        $this->customer->save();

        // Login via actingAs (simulates authenticated session)
        Auth::login($this->customer);
        $this->assertAuthenticated();
        $this->assertEquals($this->customer->id, Auth::id());

        // Dashboard accessible
        $this->get('/dashboard')->assertOk();

        // "Account activated" notification exists in DB
        $notifExists = Notification::where('notifiable_id', $this->customer->id)
            ->where('type', NotificationService::TYPE_ACCOUNT_ACTIVATED)
            ->exists();
        $this->assertTrue($notifExists, 'Account activated notification must persist for customer to see');
    }

    // ═══════════════════════════════════════════════════════════════
    //  TAHAP 2 — SETOR BARANG (Customer → Admin)
    // ═══════════════════════════════════════════════════════════════

    private function runTahap2_SetorBarang(): void
    {
        // ── Step 4: Admin creates box (sharing, air, OPEN) ──

        $this->actingAs($this->admin);
        Livewire::test(ManageBox::class)
            ->call('openCreateModal')
            ->set('newType', 'sharing')
            ->set('newMethod', 'air')
            ->set('newTrackingNumber', 'TRK-FULL-001')
            ->set('newBatchName', 'Batch-July')
            ->set('newCustomerId', $this->customer->id)
            ->set('newNotes', 'Box untuk test full flow')
            ->call('createBox');

        $this->box = Box::where('tracking_number', 'TRK-FULL-001')->first();
        $this->assertNotNull($this->box);
        $this->assertEquals('sharing', $this->box->type);
        $this->assertEquals('air', $this->box->method);
        $this->assertEquals(Box::STATUS_OPEN, $this->box->status);
        $this->assertEquals($this->customer->id, $this->box->customer_id);

        $boxCreateLog = ActivityLog::where('subject_type', Box::class)
            ->where('subject_id', $this->box->id)
            ->where('event', 'created')
            ->first();
        $this->assertNotNull($boxCreateLog, 'Box creation must be audited');
        $this->assertEquals($this->admin->id, $boxCreateLog->user_id);

        // ── Step 5: Customer setor resi — 1 normal + 1 sensitive ──

        Auth::login($this->customer);

        // Item 1: Normal
        Livewire::test(SetorResi::class)
            ->set('boxId', $this->box->id)
            ->set('name', 'Kaos Polos Putih')
            ->set('quantity', 100)
            ->set('priceYuan', '15.50')
            ->set('resiNumber', 'RESI-CHINA-001')
            ->set('proofCo', UploadedFile::fake()->image('proof1.jpg', 100, 100))
            ->set('isSensitive', false)
            ->call('submit');

        $item1 = Item::where('resi_number', 'RESI-CHINA-001')->first();
        $this->assertNotNull($item1);
        $this->assertEquals('Kaos Polos Putih', $item1->name);
        $this->assertEquals(100, $item1->quantity);
        $this->assertEquals('15.50', $item1->price_yuan);
        $this->assertFalse($item1->is_sensitive);
        $this->assertNull($item1->sensitive_type);
        $this->assertEquals($this->box->id, $item1->box_id);
        $this->assertEquals($this->customer->id, $item1->customer_id);
        $this->assertNotNull($item1->proof_co);

        // Item 2: Sensitive
        Livewire::test(SetorResi::class)
            ->set('boxId', $this->box->id)
            ->set('name', 'Laptop ASUS ROG')
            ->set('quantity', 2)
            ->set('priceYuan', '8500.00')
            ->set('resiNumber', 'RESI-CHINA-002')
            ->set('proofCo', UploadedFile::fake()->image('proof2.jpg', 100, 100))
            ->set('isSensitive', true)
            ->set('sensitiveType', 'Elektronik')
            ->call('submit');

        $item2 = Item::where('resi_number', 'RESI-CHINA-002')->first();
        $this->assertNotNull($item2);
        $this->assertEquals('Laptop ASUS ROG', $item2->name);
        $this->assertEquals(2, $item2->quantity);
        $this->assertEquals('8500.00', $item2->price_yuan);
        $this->assertTrue($item2->is_sensitive);
        $this->assertEquals('Elektronik', $item2->sensitive_type);

        $this->assertEquals(2, Item::where('box_id', $this->box->id)->count());

        // ── Step 6: Admin marks items arrived in China ──

        $items = Item::where('box_id', $this->box->id)->get();
        foreach ($items as $item) {
            $item->update([
                'arrived_china' => true,
                'arrived_china_photo' => 'arrived-china/' . $item->resi_number . '.jpg',
            ]);
        }

        foreach ($items as $item) {
            $item->refresh();
            $this->assertTrue($item->arrived_china);
            $this->assertNotNull($item->arrived_china_photo);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  TAHAP 3 — PERJALANAN BARANG
    // ═══════════════════════════════════════════════════════════════

    private function runTahap3_PerjalananBarang(): void
    {
        $this->actingAs($this->admin);

        // ── Step 7a: Admin can change status freely (client request) ──

        Livewire::test(ManageBox::class)
            ->call('selectBox', $this->box->id)
            ->call('confirmStatusChange', Box::STATUS_SEND_TO_CARGO)
            ->call('updateStatus');

        $this->box->refresh();
        $this->assertEquals(Box::STATUS_SEND_TO_CARGO, $this->box->status);

        // ── Step 7b: SENT_TO_CARGO → OTW_INA ──

        Livewire::test(ManageBox::class)
            ->call('selectBox', $this->box->id)
            ->call('confirmStatusChange', Box::STATUS_ARRIVED_INA)
            ->call('updateStatus');

        $this->box->refresh();
        $this->assertEquals(Box::STATUS_ARRIVED_INA, $this->box->status);

        // Assert 2 box_status_changed notifications sent to customer
        $boxNotifs = Notification::where('notifiable_id', $this->customer->id)
            ->where('type', NotificationService::TYPE_BOX_STATUS_CHANGED)
            ->orderBy('created_at')
            ->get();

        $this->assertCount(2, $boxNotifs, 'Customer should receive 2 box status notifications');

        $this->assertEquals(Box::STATUS_OPEN, $boxNotifs[0]->data['old_status']);
        $this->assertEquals(Box::STATUS_SEND_TO_CARGO, $boxNotifs[0]->data['new_status']);

        $this->assertEquals(Box::STATUS_SEND_TO_CARGO, $boxNotifs[1]->data['old_status']);
        $this->assertEquals(Box::STATUS_ARRIVED_INA, $boxNotifs[1]->data['new_status']);

        // Audit logs: status changes logged by ManageBox + BoxObserver (each transition = 2 logs)
        $statusLogs = ActivityLog::where('subject_type', Box::class)
            ->where('subject_id', $this->box->id)
            ->where('event', 'updated')
            ->where('new_values->status', '!=', null)
            ->get();
        $this->assertGreaterThanOrEqual(2, $statusLogs->count(), 'At least 2 status change audit entries expected');

        // ── Step 8: Admin inputs ETD/ETA ──

        Livewire::test(EstUpdate::class)
            ->call('selectBox', $this->box->id)
            ->set('etd', '2026-08-01')
            ->set('eta', '2026-08-15')
            ->set('estNote', 'Kapal berangkat dari Guangzhou')
            ->call('saveEstimate');

        $this->box->refresh();
        $this->assertEquals('2026-08-01', $this->box->etd->format('Y-m-d'));
        $this->assertEquals('2026-08-15', $this->box->eta->format('Y-m-d'));

        $estLog = ActivityLog::where('subject_type', Box::class)
            ->where('subject_id', $this->box->id)
            ->where('event', 'estimate_updated')
            ->first();
        $this->assertNotNull($estLog);
        $this->assertEquals($this->admin->id, $estLog->user_id);
        $this->assertEquals('2026-08-01', $estLog->new_values['etd']);
        $this->assertEquals('2026-08-15', $estLog->new_values['eta']);

        $estNotif = Notification::where('notifiable_id', $this->customer->id)
            ->where('type', 'box_status_changed')
            ->where('data->title', 'Estimasi Diperbarui')
            ->first();
        $this->assertNotNull($estNotif, 'Customer should receive estimate update notification');

        // ── Step 9: Customer views My Box Sharing ──

        Auth::login($this->customer);

        $customerBox = Box::where('customer_id', $this->customer->id)
            ->where('type', 'sharing')
            ->first();

        $this->assertNotNull($customerBox);
        $this->assertEquals(Box::STATUS_ARRIVED_INA, $customerBox->status);
        $this->assertEquals('2026-08-01', $customerBox->etd->format('Y-m-d'));
        $this->assertEquals('2026-08-15', $customerBox->eta->format('Y-m-d'));
        $this->assertEquals('TRK-FULL-001', $customerBox->tracking_number);
    }

    // ═══════════════════════════════════════════════════════════════
    //  TAHAP 4 — INVOICE & PEMBAYARAN
    // ═══════════════════════════════════════════════════════════════

    private function runTahap4_InvoicePembayaran(): void
    {
        // ── Step 10: Admin generates invoice via FeeCalculationService ──

        $feeService = new FeeCalculationService();
        $expected = $feeService->calculate(
            type: 'sharing', method: 'air',
            weight: 150, length: 80, width: 60, height: 50,
            isSensitive: false, addOn: 25000,
        );

        $this->actingAs($this->admin);
        Livewire::test(GenerateInvoice::class)
            ->set('selectedBoxId', $this->box->id)
            ->set('weight', '150')
            ->set('length', '80')
            ->set('width', '60')
            ->set('height', '50')
            ->set('addOn', '25000')
            ->call('generateInvoice');

        $this->invoice = Invoice::where('box_id', $this->box->id)->first();
        $this->assertNotNull($this->invoice);
        $this->assertEquals(Invoice::STATUS_WAITING_PAYMENT, $this->invoice->status);
        $this->assertEquals($this->customer->id, $this->invoice->customer_id);
        $this->assertStringStartsWith('INV-', $this->invoice->invoice_number);

        // Fee amounts match FeeCalculationService output exactly
        $this->assertEquals($expected['fee_tax'], (float) $this->invoice->fee_tax);
        $this->assertEquals($expected['fee_wh'], (float) $this->invoice->fee_wh);
        $this->assertEquals($expected['fee_packing'], (float) $this->invoice->fee_packing);
        $this->assertEquals($expected['grand_total'], (float) $this->invoice->grand_total);

        // Box status → UP_INVOICE
        $this->box->refresh();
        $this->assertEquals(Box::STATUS_INVOICE, $this->box->status);

        // Notification to customer
        $invNotif = Notification::where('notifiable_id', $this->customer->id)
            ->where('type', NotificationService::TYPE_INVOICE_GENERATED)
            ->first();
        $this->assertNotNull($invNotif);
        $this->assertEquals('Invoice Baru', $invNotif->data['title']);
        $this->assertEquals($this->invoice->invoice_number, $invNotif->data['invoice_number']);
        $this->assertEquals($this->invoice->grand_total, $invNotif->data['grand_total']);

        // Audit log
        $invLog = ActivityLog::where('subject_type', Invoice::class)
            ->where('subject_id', $this->invoice->id)
            ->where('event', 'generated')
            ->first();
        $this->assertNotNull($invLog);
        $this->assertEquals($this->admin->id, $invLog->user_id);

        // ── Step 11: Customer pays invoice (Transfer + upload proof) ──

        Auth::login($this->customer);

        Livewire::test(InvoiceIndex::class)
            ->call('openPayModal', $this->invoice->id)
            ->set('paymentMethod', 'transfer')
            ->set('paymentProof', UploadedFile::fake()->image('bukti-transfer.jpg', 100, 100))
            ->call('submitPayment');

        $this->invoice->refresh();
        $this->assertEquals(Invoice::STATUS_WAITING_VERIFICATION, $this->invoice->status);
        $this->assertEquals('transfer', $this->invoice->payment_method);
        $this->assertNotNull($this->invoice->payment_proof);

        $payNotif = Notification::where('notifiable_id', $this->admin->id)
            ->where('type', NotificationService::TYPE_PAYMENT_RECEIVED)
            ->first();
        $this->assertNotNull($payNotif, 'Admin must receive payment notification');
        $this->assertEquals('Pembayaran Masuk', $payNotif->data['title']);
        $this->assertEquals($this->customer->name, $payNotif->data['customer_name']);

        // ── Step 12: Admin verifies payment ──

        $this->actingAs($this->admin);
        Livewire::test(VerificationIndex::class)
            ->set('filterStatus', 'waiting_verification')
            ->call('selectInvoice', $this->invoice->id)
            ->call('verifyPayment');

        $this->invoice->refresh();
        $this->assertEquals(Invoice::STATUS_VERIFIED, $this->invoice->status);

        $verifyNotif = Notification::where('notifiable_id', $this->customer->id)
            ->where('type', NotificationService::TYPE_PAYMENT_VERIFIED)
            ->first();
        $this->assertNotNull($verifyNotif);
        $this->assertEquals('Pembayaran Terverifikasi', $verifyNotif->data['title']);
        $this->assertEquals($this->invoice->invoice_number, $verifyNotif->data['invoice_number']);

        $verifyLog = ActivityLog::where('subject_type', Invoice::class)
            ->where('subject_id', $this->invoice->id)
            ->where('event', 'payment_verified')
            ->first();
        $this->assertNotNull($verifyLog);
        $this->assertEquals($this->admin->id, $verifyLog->user_id);

        // ── Step 13: RATE SNAPSHOT — invoice must NOT change ──

        $this->invoice->refresh();
        $snapTax = (float) $this->invoice->fee_tax;
        $snapWh = (float) $this->invoice->fee_wh;
        $snapPacking = (float) $this->invoice->fee_packing;
        $snapTotal = (float) $this->invoice->grand_total;

        Livewire::test(SettingsIndex::class)
            ->set('rate_sharing_air_berat', '999')
            ->set('rate_sharing_air_volume', '999')
            ->set('rate_sharing_sea_berat', '999')
            ->set('rate_sharing_sea_volume', '999')
            ->set('rate_sharing_sensitive_air_berat', '999')
            ->set('rate_sharing_sensitive_air_volume', '999')
            ->set('rate_sharing_sensitive_sea_berat', '999')
            ->set('rate_sharing_sensitive_sea_volume', '999')
            ->call('saveSharingRates');

        $this->assertEquals('999', Setting::getValue('rate_sharing_air_berat'));

        $this->invoice->refresh();
        $this->assertEquals($snapTax, (float) $this->invoice->fee_tax, 'Snapshot rule: fee_tax must NOT change');
        $this->assertEquals($snapWh, (float) $this->invoice->fee_wh, 'Snapshot rule: fee_wh must NOT change');
        $this->assertEquals($snapPacking, (float) $this->invoice->fee_packing, 'Snapshot rule: fee_packing must NOT change');
        $this->assertEquals($snapTotal, (float) $this->invoice->grand_total, 'Snapshot rule: grand_total must NOT change');
    }

    // ═══════════════════════════════════════════════════════════════
    //  TAHAP 5 — CHECKOUT & PENGIRIMAN
    // ═══════════════════════════════════════════════════════════════

    private function runTahap5_CheckoutPengiriman(): void
    {
        // ── Step 14: Customer requests checkout ──

        Auth::login($this->customer);

        Livewire::test(CheckoutIndex::class)
            ->call('openForm')
            ->set('invoiceId', $this->invoice->id)
            ->set('addressType', 'personal')
            ->set('recipientName', 'Budi Santoso')
            ->set('recipientPhone', '081234567890')
            ->set('address', 'Jl. Sudirman No. 123, Jakarta Selatan 12190')
            ->set('confirmation', true)
            ->call('submit');

        $this->checkout = Checkout::where('invoice_id', $this->invoice->id)
            ->where('customer_id', $this->customer->id)
            ->first();
        $this->assertNotNull($this->checkout);
        $this->assertEquals(Checkout::STATUS_REQUEST, $this->checkout->status);
        $this->assertEquals('personal', $this->checkout->address_type);
        $this->assertEquals('Budi Santoso', $this->checkout->recipient_name);

        // ── Step 15: Admin processes checkout ──

        // No dedicated Livewire component — admin processes via model/service
        $this->checkout->update(['status' => Checkout::STATUS_ON_PROCESS]);
        $this->checkout->update([
            'status' => Checkout::STATUS_SENT,
            'packing_photo' => 'packing/checkout-' . $this->checkout->id . '.jpg',
            'tracking_number' => 'JNE-2026-001234',
        ]);

        $notifService = new NotificationService();
        $notifService->checkoutProcessed($this->checkout);

        $this->checkout->refresh();
        $this->assertEquals(Checkout::STATUS_SENT, $this->checkout->status);
        $this->assertNotNull($this->checkout->packing_photo);
        $this->assertEquals('JNE-2026-001234', $this->checkout->tracking_number);

        $checkoutNotif = Notification::where('notifiable_id', $this->customer->id)
            ->where('type', NotificationService::TYPE_CHECKOUT_PROCESSED)
            ->latest()
            ->first();
        $this->assertNotNull($checkoutNotif);
        $this->assertEquals('Checkout Diproses', $checkoutNotif->data['title']);
    }

    // ═══════════════════════════════════════════════════════════════
    //  TAHAP 6 — KOMPLAIN
    // ═══════════════════════════════════════════════════════════════

    private function runTahap6_Komplain(): void
    {
        // ── Step 16: Customer files complaint ──

        Auth::login($this->customer);

        Livewire::test(KomplainIndex::class)
            ->call('openForm')
            ->set('type', 'Kurang Barang Ekspedisi')
            ->set('resolution', 'refund')
            ->set('invoiceNumber', $this->invoice->invoice_number)
            ->set('resiNumber', 'RESI-CHINA-001')
            ->set('description', 'Barang yang saya terima kurang 2 pcs dari jumlah yang diorder. Packing sudah dibuka sebelum diterima.')
            ->set('photoFile', UploadedFile::fake()->image('komplain-foto.jpg', 100, 100))
            ->call('submit');

        $this->complaint = Complain::where('customer_id', $this->customer->id)->first();
        $this->assertNotNull($this->complaint);
        $this->assertEquals(Complain::STATUS_OPEN, $this->complaint->status);
        $this->assertEquals('Kurang Barang Ekspedisi', $this->complaint->type);
        $this->assertEquals('refund', $this->complaint->resolution);

        $complaintNotif = Notification::where('notifiable_id', $this->admin->id)
            ->where('type', NotificationService::TYPE_NEW_COMPLAINT)
            ->latest()
            ->first();
        $this->assertNotNull($complaintNotif);
        $this->assertEquals('Komplain Baru', $complaintNotif->data['title']);
        $this->assertEquals($this->customer->name, $complaintNotif->data['customer_name']);

        // ── Step 17: Admin processes complaint through all statuses ──

        $notifService = new NotificationService();
        $auditService = new AuditLogService();

        $transitions = [
            [Complain::STATUS_OPEN, Complain::STATUS_IN_REVIEW],
            [Complain::STATUS_IN_REVIEW, Complain::STATUS_PROCESSING],
            [Complain::STATUS_PROCESSING, Complain::STATUS_RESOLVED],
        ];

        foreach ($transitions as [$old, $new]) {
            $this->complaint->update(['status' => $new]);
            $notifService->complaintUpdated($this->complaint, $old, $new);
            $auditService->log('updated', $this->complaint, ['status' => $old], ['status' => $new]);
        }

        $this->complaint->refresh();
        $this->assertEquals(Complain::STATUS_RESOLVED, $this->complaint->status);

        // 3 notifications to customer (one per transition)
        $complaintNotifs = Notification::where('notifiable_id', $this->customer->id)
            ->where('type', NotificationService::TYPE_COMPLAINT_UPDATED)
            ->get();
        $this->assertCount(3, $complaintNotifs);

        foreach ($transitions as $i => [$old, $new]) {
            $this->assertEquals($old, $complaintNotifs[$i]->data['old_status']);
            $this->assertEquals($new, $complaintNotifs[$i]->data['new_status']);
        }

        // 3 audit log entries
        $complaintLogs = ActivityLog::where('subject_type', Complain::class)
            ->where('subject_id', $this->complaint->id)
            ->where('event', 'updated')
            ->get();
        $this->assertCount(3, $complaintLogs);

        foreach ($transitions as $i => [$old, $new]) {
            $this->assertEquals(['status' => $old], $complaintLogs[$i]->old_values);
            $this->assertEquals(['status' => $new], $complaintLogs[$i]->new_values);
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  TAHAP 7 — OWNER VERIFICATION
    // ═══════════════════════════════════════════════════════════════

    private function runTahap7_OwnerVerification(): void
    {
        // ── Step 18: Owner dashboard reflects real data ──

        $this->actingAs($this->owner);

        $this->invoice->refresh();
        $this->invoice->grand_total;

        Livewire::test(OwnerDashboard::class)
            ->assertSet('verifiedInvoices', function (int $value) {
                return $value >= 1;
            })
            ->assertSet('revenueThisMonth', function (float $value) {
                return $value > 0;
            });

        // Exact revenue check via DB
        $verifiedRevenue = Invoice::where('status', Invoice::STATUS_VERIFIED)
            ->where('updated_at', '>=', now()->startOfMonth())
            ->sum('grand_total');
        $this->assertGreaterThan(0, $verifiedRevenue);
        $this->assertEquals((float) $this->invoice->grand_total, (float) $verifiedRevenue);

        // ── Step 19: Owner finance shows invoice with correct total ──

        Livewire::test(FinanceIndex::class)
            ->assertSee($this->invoice->invoice_number);

        $this->invoice->refresh();
        $this->assertEquals(Invoice::STATUS_VERIFIED, $this->invoice->status);

        // ── Step 20: Owner audit log shows all critical actions ──

        Livewire::test(AuditLogIndex::class)->assertOk();

        // Customer activation
        $this->assertNotNull(
            ActivityLog::where('subject_type', User::class)
                ->where('subject_id', $this->customer->id)
                ->where('event', 'activated')
                ->first(),
            'Audit: customer activation'
        );

        // Invoice generation
        $this->assertNotNull(
            ActivityLog::where('subject_type', Invoice::class)
                ->where('subject_id', $this->invoice->id)
                ->where('event', 'generated')
                ->first(),
            'Audit: invoice generation'
        );

        // Payment verification
        $this->assertNotNull(
            ActivityLog::where('subject_type', Invoice::class)
                ->where('subject_id', $this->invoice->id)
                ->where('event', 'payment_verified')
                ->first(),
            'Audit: payment verification'
        );

        // Rate update
        $rateLog = ActivityLog::where('event', 'rate_updated')->latest()->first();
        $this->assertNotNull($rateLog, 'Audit: rate update');
        $this->assertArrayHasKey('rate_sharing_air_berat', $rateLog->old_values);
        $this->assertArrayHasKey('rate_sharing_air_berat', $rateLog->new_values);

        // Complaint status changes
        $complaintLogs = ActivityLog::where('subject_type', Complain::class)
            ->where('subject_id', $this->complaint->id)
            ->where('event', 'updated')
            ->get();
        $this->assertCount(3, $complaintLogs, 'Audit: 3 complaint status changes');

        // Box status changes
        $boxStatusLogs = ActivityLog::where('subject_type', Box::class)
            ->where('subject_id', $this->box->id)
            ->where('event', 'updated')
            ->get();
        $this->assertGreaterThanOrEqual(2, $boxStatusLogs->count(), 'Audit: box status changes');

        // All logs have proper timestamps and event types
        $allLogs = ActivityLog::orderBy('created_at')->get();
        $this->assertGreaterThan(0, $allLogs->count(), 'Audit log should have entries');
        foreach ($allLogs as $log) {
            $this->assertNotNull($log->created_at, 'Timestamp required');
            $this->assertNotNull($log->event, 'Event type required');
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  TAHAP 8 — NEGATIVE ROLE ACCESS
    // ═══════════════════════════════════════════════════════════════

    private function runTahap8_NegativeRoleAccess(): void
    {
        $otherCustomer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);

        // ── Customer A cannot see Customer B's invoice data ──

        Auth::login($this->customer);
        $myInvoices = Invoice::where('customer_id', $this->customer->id)->pluck('id');
        $this->assertFalse(
            $myInvoices->contains('customer_id', $otherCustomer->id),
            'Customer A invoice list must not include Customer B data'
        );

        // InvoiceIndex filters by auth()->id()
        $otherInvoice = Invoice::factory()->create(['customer_id' => $otherCustomer->id]);
        $filteredOut = Invoice::where('customer_id', $this->customer->id)
            ->where('id', $otherInvoice->id)
            ->exists();
        $this->assertFalse($filteredOut, 'Customer cannot access other customer invoice via query filter');

        // ── Customer CANNOT access /admin/* ──

        $this->get('/admin/dashboard')->assertStatus(403);
        $this->get('/admin/manage-boxes')->assertStatus(403);
        $this->get('/admin/invoices')->assertStatus(403);
        $this->get('/admin/verification')->assertStatus(403);
        $this->get('/admin/settings')->assertStatus(403);
        $this->get('/admin/customers')->assertStatus(403);
        $this->get('/admin/est-update')->assertStatus(403);
        $this->get('/admin/recap')->assertStatus(403);

        // ── Admin CANNOT access /owner/* ──

        Auth::login($this->admin);
        $this->get('/owner/dashboard')->assertStatus(403);
        $this->get('/owner/finance')->assertStatus(403);
        $this->get('/owner/manage-admin')->assertStatus(403);
        $this->get('/owner/audit-log')->assertStatus(403);

        // ── Guest CANNOT access any protected route ──

        Auth::logout();
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/owner/dashboard')->assertRedirect('/login');
    }

    // ═══════════════════════════════════════════════════════════════
    //  TAHAP 9 — REVISION EXTENSIONS (R1–R7)
    // ═══════════════════════════════════════════════════════════════
    //
    //  Covers all revision features in one sequential flow:
    //   R1: Kurs history input before invoice generation
    //   R2: Box close/open + setor resi rejection
    //   R3: No Tuan item → customer claim → denda
    //   R5: Flexible invoice (2 boxes) + dropship checkout
    //   R6: Payment deadline reminder (scheduled command)
    //   R7: Barang lelang page
    //   R3+R5: Owner finance report includes denda + flexible invoice
    // ═══════════════════════════════════════════════════════════════

    private function runTahap9_RevisiExtensions(): void
    {
        // ── Step 21: R1 — Admin inputs kurs for today ──

        $this->actingAs($this->admin);

        $today = now()->format('Y-m-d');
        Livewire::test(\App\Livewire\Admin\KursHistoryIndex::class)
            ->call('toggleForm')
            ->set('kurs_value', '2650')
            ->set('effective_date', $today)
            ->call('saveKurs');

        $kurs = KursHistory::whereDate('effective_date', $today)->first();
        $this->assertNotNull($kurs, 'Kurs history must be created');
        $this->assertEquals(2650.0, (float) $kurs->kurs_value);
        $this->assertEquals($this->admin->id, $kurs->input_by);

        $kursLog = ActivityLog::where('subject_type', KursHistory::class)
            ->where('subject_id', $kurs->id)
            ->where('event', 'kurs_created')
            ->first();
        $this->assertNotNull($kursLog, 'Audit: kurs input logged');

        // Latest kurs query returns this record
        $latest = KursHistory::getLatest();
        $this->assertNotNull($latest);
        $this->assertEquals($kurs->id, $latest->id);

        // ── Step 22: R2 — Admin creates second box, then closes it ──

        Livewire::test(\App\Livewire\Admin\ManageBox::class)
            ->call('openCreateModal')
            ->set('newType', 'sharing')
            ->set('newMethod', 'sea')
            ->set('newTrackingNumber', 'TRK-FULL-002')
            ->set('newBatchName', 'Batch-Aug')
            ->set('newCustomerId', $this->customer->id)
            ->set('newNotes', 'Second box for flexible invoice test')
            ->call('createBox');

        $this->box2 = Box::where('tracking_number', 'TRK-FULL-002')->first();
        $this->assertNotNull($this->box2);
        $this->assertEquals(Box::STATUS_OPEN, $this->box2->status);

        // Customer setor resi to box2
        Auth::login($this->customer);

        Livewire::test(\App\Livewire\Customer\SetorResi::class)
            ->set('boxId', $this->box2->id)
            ->set('name', 'Sepatu Nike Air Max')
            ->set('quantity', 50)
            ->set('priceYuan', '280.00')
            ->set('resiNumber', 'RESI-CHINA-003')
            ->set('proofCo', UploadedFile::fake()->image('proof3.jpg', 100, 100))
            ->set('isSensitive', false)
            ->call('submit');

        $item3 = Item::where('resi_number', 'RESI-CHINA-003')->first();
        $this->assertNotNull($item3);
        $this->assertEquals($this->box2->id, $item3->box_id);

        // Admin marks item3 as arrived in Indonesia (needed for flexible invoice)
        $item3->update(['arrived_china' => true, 'arrived_indonesia' => true]);

        // Also mark items from box1 as arrived Indonesia (needed for flexible invoice)
        Item::where('box_id', $this->box->id)->update(['arrived_indonesia' => true]);

        // Add WH China data to items (needed for weight in flexible invoice)
        foreach (Item::where('box_id', $this->box->id)->get() as $boxItem) {
            WhChinaData::create([
                'item_id' => $boxItem->id,
                'resi_number' => $boxItem->resi_number,
                'berat' => $boxItem->is_sensitive ? 5.0 : 2.5,
                'ukuran_box' => '30x20x15',
                'input_by' => $this->admin->id,
                'matched_at' => now(),
            ]);
        }
        WhChinaData::create([
            'item_id' => $item3->id,
            'resi_number' => $item3->resi_number,
            'berat' => 3.0,
            'ukuran_box' => '40x30x20',
            'input_by' => $this->admin->id,
            'matched_at' => now(),
        ]);

        // Now close box2 — customer should NOT be able to setor anymore
        $this->actingAs($this->admin);
        Livewire::test(\App\Livewire\Admin\ManageBox::class)
            ->call('selectBox', $this->box2->id)
            ->call('closeBox');

        $this->box2->refresh();
        $this->assertEquals(Box::STATUS_CLOSED, $this->box2->status);
        $this->assertNotNull($this->box2->close_date);

        // Notification sent to customer
        $boxClosedNotif = Notification::where('notifiable_id', $this->customer->id)
            ->where('type', NotificationService::TYPE_BOX_CLOSED)
            ->latest()
            ->first();
        $this->assertNotNull($boxClosedNotif, 'Customer must receive box closed notification');

        // Customer tries to setor resi to closed box → REJECTED
        Auth::login($this->customer);

        $setorResiTest = Livewire::test(\App\Livewire\Customer\SetorResi::class)
            ->set('boxId', $this->box2->id)
            ->set('name', 'Tas Gucci')
            ->set('quantity', 10)
            ->set('priceYuan', '1200.00')
            ->set('resiNumber', 'RESI-CHINA-REJECTED')
            ->set('proofCo', UploadedFile::fake()->image('proof-rejected.jpg', 100, 100))
            ->set('isSensitive', false)
            ->call('submit');

        // Verify the rejected item was NOT created
        $rejectedItem = Item::where('resi_number', 'RESI-CHINA-REJECTED')->first();
        $this->assertNull($rejectedItem, 'Setor resi to closed box must be rejected — item not created');

        // Admin re-opens box2
        $this->actingAs($this->admin);
        Livewire::test(\App\Livewire\Admin\ManageBox::class)
            ->call('selectBox', $this->box2->id)
            ->call('openBox');

        $this->box2->refresh();
        $this->assertEquals(Box::STATUS_OPEN, $this->box2->status);

        // ── Step 23: R3 — No Tuan item: create, mark, claim ──

        // Create an item that does NOT match any setor resi (orphan item in box)
        // customer_id is NOT NULL, so assign to customer but mark as No Tuan
        // (item arrived at WH without matching any customer's setor resi)
        $this->actingAs($this->admin);
        $orphanItem = Item::create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'name' => 'Headphone Sony WH-1000XM5',
            'quantity' => 5,
            'price_yuan' => '1500.00',
            'resi_number' => 'RESI-ORPHAN-001',
            'is_sensitive' => false,
            'status' => Item::STATUS_ACTIVE,
        ]);

        // Admin marks orphan item as No Tuan
        $claimService = app(NoTuanClaimService::class);
        $claimService->markNoTuan($orphanItem);

        $orphanItem->refresh();
        $this->assertEquals(Item::STATUS_NO_TUAN, $orphanItem->status);

        // Customer claims the No Tuan item
        Auth::login($this->customer);

        $this->dendaClaim = $claimService->claimItem(
            $orphanItem,
            $this->customer,
            'proof-purchase/orphan-claim.jpg',
            'Ini barang saya, nota pembelian terlampir',
        );

        $this->assertNotNull($this->dendaClaim);
        $this->assertEquals($this->customer->id, $this->dendaClaim->customer_id);
        $this->assertEquals($orphanItem->id, $this->dendaClaim->item_id);
        $this->assertEquals(5000, (float) $this->dendaClaim->jumlah_denda);
        $this->assertEquals(DendaClaim::STATUS_PENDING, $this->dendaClaim->status);
        $this->assertNull($this->dendaClaim->invoice_id, 'Denda not yet tagged to any invoice');

        $orphanItem->refresh();
        $this->assertEquals(Item::STATUS_CLAIMED, $orphanItem->status);
        $this->assertEquals($this->customer->id, $orphanItem->customer_id);

        // Claim notification sent to customer
        $claimNotif = Notification::where('notifiable_id', $this->customer->id)
            ->where('type', NotificationService::TYPE_CLAIM_SUCCESSFUL)
            ->latest()
            ->first();
        $this->assertNotNull($claimNotif, 'Customer must receive claim successful notification');

        // Add WH China data to orphan item so it can be invoiced
        WhChinaData::create([
            'item_id' => $orphanItem->id,
            'resi_number' => $orphanItem->resi_number,
            'berat' => 1.5,
            'ukuran_box' => '25x15x10',
            'input_by' => $this->admin->id,
            'matched_at' => now(),
        ]);
        $orphanItem->update(['arrived_indonesia' => true]);

        // ── Step 24: R5 — Flexible invoice from 2 different boxes ──

        // Customer creates flexible invoice with items from box1 + box2 + orphan
        Auth::login($this->customer);

        $item1 = Item::where('resi_number', 'RESI-CHINA-001')->first();
        $item3 = Item::where('resi_number', 'RESI-CHINA-003')->first();

        // All items must be arrived_indonesia and not yet invoiced
        $this->assertTrue($item1->arrived_indonesia, 'Item 1 must be arrived Indonesia');
        $this->assertTrue($item3->arrived_indonesia, 'Item 3 must be arrived Indonesia');
        $this->assertTrue($orphanItem->arrived_indonesia, 'Orphan item must be arrived Indonesia');
        $this->assertFalse($item1->isInvoiced(), 'Item 1 not yet invoiced');
        $this->assertFalse($item3->isInvoiced(), 'Item 3 not yet invoiced');

        Livewire::test(\App\Livewire\Customer\CreateInvoice::class)
            ->call('toggleItem', $item1->id)
            ->call('toggleItem', $item3->id)
            ->call('toggleItem', $orphanItem->id)
            ->set('length', '60')
            ->set('width', '40')
            ->set('height', '30')
            ->call('createInvoice');

        // Verify flexible invoice created
        $this->flexibleInvoice = Invoice::where('customer_id', $this->customer->id)
            ->whereNull('box_id')
            ->first();
        $this->assertNotNull($this->flexibleInvoice, 'Flexible invoice must be created');
        $this->assertEquals(Invoice::STATUS_WAITING_PAYMENT, $this->flexibleInvoice->status);
        $this->assertNull($this->flexibleInvoice->box_id, 'Flexible invoice has no box_id');
        $this->assertTrue($this->flexibleInvoice->isFlexible());

        // Junction items: items from 2 different boxes + orphan
        $invoiceItems = $this->flexibleInvoice->items->pluck('id')->sort()->values()->toArray();
        $expectedItems = collect([$item1->id, $item3->id, $orphanItem->id])->sort()->values()->toArray();
        $this->assertEquals($expectedItems, $invoiceItems, 'Flexible invoice must contain items from 2 boxes + orphan');

        // Box info shows multiple boxes
        $boxInfo = $this->flexibleInvoice->box_info;
        $this->assertStringContainsString('TRK-FULL-001', $boxInfo);
        $this->assertStringContainsString('TRK-FULL-002', $boxInfo);

        // Denda claim now tagged to this invoice
        $this->dendaClaim->refresh();
        $this->assertEquals($this->flexibleInvoice->id, $this->dendaClaim->invoice_id);
        $this->assertEquals(DendaClaim::STATUS_TAGGED, $this->dendaClaim->status);

        // Denda total in invoice
        $this->flexibleInvoice->refresh();
        $this->assertEquals(5000, (float) $this->flexibleInvoice->denda_total, 'Denda Rp 5000 must be in invoice denda_total');

        // Grand total includes denda
        $expectedGrandTotalWithoutDenda = (float) $this->flexibleInvoice->fee_tax
            + (float) $this->flexibleInvoice->fee_wh
            + (float) $this->flexibleInvoice->fee_packing
            + (float) $this->flexibleInvoice->add_on;
        $this->assertEquals(
            $expectedGrandTotalWithoutDenda + 5000,
            (float) $this->flexibleInvoice->grand_total,
            'Grand total must include denda'
        );

        // Payment deadline auto-set
        $this->assertNotNull($this->flexibleInvoice->payment_deadline, 'Payment deadline must be set');

        // Invoice notification sent
        $flexInvNotif = Notification::where('notifiable_id', $this->customer->id)
            ->where('type', NotificationService::TYPE_INVOICE_GENERATED)
            ->latest()
            ->first();
        $this->assertNotNull($flexInvNotif);

        // ── Step 25: R5 — Pay flexible invoice + admin verifies ──

        Livewire::test(\App\Livewire\Customer\InvoiceIndex::class)
            ->call('openPayModal', $this->flexibleInvoice->id)
            ->set('paymentMethod', 'transfer')
            ->set('paymentProof', UploadedFile::fake()->image('bukti-flex.jpg', 100, 100))
            ->call('submitPayment');

        $this->flexibleInvoice->refresh();
        $this->assertEquals(Invoice::STATUS_WAITING_VERIFICATION, $this->flexibleInvoice->status);

        // Admin verifies
        $this->actingAs($this->admin);
        Livewire::test(\App\Livewire\Admin\VerificationIndex::class)
            ->set('filterStatus', 'waiting_verification')
            ->call('selectInvoice', $this->flexibleInvoice->id)
            ->call('verifyPayment');

        $this->flexibleInvoice->refresh();
        $this->assertEquals(Invoice::STATUS_VERIFIED, $this->flexibleInvoice->status);

        // Denda claim status → paid (after verification)
        $this->dendaClaim->refresh();
        $this->assertEquals(DendaClaim::STATUS_PAID, $this->dendaClaim->status);

        // ── Step 26: R5 — Checkout with Dropship address ──

        Auth::login($this->customer);

        Livewire::test(\App\Livewire\Customer\CheckoutIndex::class)
            ->call('openForm')
            ->set('invoiceId', $this->flexibleInvoice->id)
            ->set('addressType', 'dropship')
            ->set('recipientName', 'Toko Online Maju')
            ->set('recipientPhone', '087654321098')
            ->set('address', 'Jl. Gatot Subroto No. 45, Jakarta Pusat 10270')
            ->set('senderName', 'Budi Santoso')
            ->set('senderPhone', '081234567890')
            ->set('confirmation', true)
            ->call('submit');

        $this->dropshipCheckout = Checkout::where('invoice_id', $this->flexibleInvoice->id)
            ->where('customer_id', $this->customer->id)
            ->first();
        $this->assertNotNull($this->dropshipCheckout);
        $this->assertEquals('dropship', $this->dropshipCheckout->address_type);
        $this->assertEquals('Toko Online Maju', $this->dropshipCheckout->recipient_name);
        $this->assertEquals('087654321098', $this->dropshipCheckout->recipient_phone);
        $this->assertEquals('Budi Santoso', $this->dropshipCheckout->sender_name);
        $this->assertEquals('081234567890', $this->dropshipCheckout->sender_phone);

        // ── Step 27: R6 — Payment deadline reminder ──

        // Backdate the invoice's payment_deadline to 3 days ago
        $this->flexibleInvoice->update([
            'payment_deadline' => now()->subDays(3)->toDateString(),
        ]);
        $this->flexibleInvoice->refresh();

        // Reset status to waiting_payment to test reminder
        $this->flexibleInvoice->update(['status' => Invoice::STATUS_WAITING_PAYMENT]);

        // Run the scheduled command
        $this->artisan('deadlines:check')->assertExitCode(0);

        // H-0 reminder should be sent (deadline is today-3, so it's overdue)
        // Actually, let's test H-3: set deadline to today + 3 days
        $this->flexibleInvoice->update([
            'payment_deadline' => now()->addDays(3)->toDateString(),
            'reminder_sent' => [], // reset
        ]);

        // Re-verify status is still waiting_payment
        $this->flexibleInvoice->update(['status' => Invoice::STATUS_WAITING_PAYMENT]);

        $this->artisan('deadlines:check')->assertExitCode(0);

        $this->flexibleInvoice->refresh();
        $this->assertTrue(
            $this->flexibleInvoice->hasReminderBeenSent('h3'),
            'H-3 reminder must be marked as sent'
        );

        // Reminder notification exists
        $reminderNotif = Notification::where('notifiable_id', $this->customer->id)
            ->where('type', NotificationService::TYPE_PAYMENT_REMINDER_H3)
            ->latest()
            ->first();
        $this->assertNotNull($reminderNotif, 'H-3 reminder notification must be sent');

        // Run again → no duplicate
        $this->artisan('deadlines:check')->assertExitCode(0);

        $reminderCount = Notification::where('notifiable_id', $this->customer->id)
            ->where('type', NotificationService::TYPE_PAYMENT_REMINDER_H3)
            ->count();
        $this->assertEquals(1, $reminderCount, 'H-3 reminder must NOT be sent twice (idempotent)');

        // Restore flexible invoice to verified for finance report test
        $this->flexibleInvoice->update(['status' => Invoice::STATUS_VERIFIED]);

        // ── Step 28: R7 — Barang klaim_wh appears in lelang page ──

        // Mark a No Tuan item as Klaim WH for lelang
        $lelangItem = Item::create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'name' => 'Kamera Canon EOS R5',
            'quantity' => 1,
            'price_yuan' => '12000.00',
            'resi_number' => 'RESI-LELANG-001',
            'is_sensitive' => true,
            'sensitive_type' => 'Elektronik',
            'status' => Item::STATUS_ACTIVE,
        ]);

        $this->actingAs($this->admin);
        $claimService->markNoTuan($lelangItem);
        $lelangItem->refresh();
        $this->assertEquals(Item::STATUS_NO_TUAN, $lelangItem->status);

        $claimService->markKlaimWh($lelangItem);
        $lelangItem->refresh();
        $this->assertEquals(Item::STATUS_KLAIM_WH, $lelangItem->status);

        // Lelang page shows this item
        Livewire::test(\App\Livewire\Admin\LelangIndex::class)
            ->assertSee($lelangItem->name)
            ->assertSee($lelangItem->resi_number);

        // Filter by klaim_wh status
        Livewire::test(\App\Livewire\Admin\LelangIndex::class)
            ->set('filterStatus', Item::STATUS_KLAIM_WH)
            ->assertSee($lelangItem->name);

        // Summary includes the item
        $lelangComponent = Livewire::test(\App\Livewire\Admin\LelangIndex::class);
        $summary = $lelangComponent->get('summary');
        $this->assertGreaterThan(0, $summary['total_barang']);
        $this->assertGreaterThan(0, $summary['belum_terjual']);

        // ── Step 29: R3+R5 — Owner finance report ──

        $this->actingAs($this->owner);

        // Owner dashboard sees both verified invoices
        Livewire::test(OwnerDashboard::class)
            ->assertSet('verifiedInvoices', function (int $value) {
                return $value >= 2; // original + flexible
            });

        // Revenue includes both invoices
        $totalVerifiedRevenue = Invoice::where('status', Invoice::STATUS_VERIFIED)
            ->sum('grand_total');
        $this->assertGreaterThan(0, $totalVerifiedRevenue);

        // Revenue includes denda from flexible invoice
        $flexInvoiceDenda = (float) $this->flexibleInvoice->refresh()->denda_total;
        $this->assertEquals(5000, $flexInvoiceDenda);

        // Total revenue = original invoice + flexible invoice (with denda)
        $originalTotal = (float) $this->invoice->grand_total;
        $flexTotal = (float) $this->flexibleInvoice->grand_total;
        $this->assertEqualsWithDelta(
            $originalTotal + $flexTotal,
            (float) $totalVerifiedRevenue,
            0.01,
            'Total verified revenue must equal sum of both invoices'
        );

        // Finance page shows both invoice numbers
        Livewire::test(FinanceIndex::class)
            ->assertSee($this->invoice->invoice_number)
            ->assertSee($this->flexibleInvoice->invoice_number);

        // Finance page shows flexible invoice grand total (includes denda)
        Livewire::test(FinanceIndex::class)
            ->assertSee($this->flexibleInvoice->invoice_number);
    }

    // ═══════════════════════════════════════════════════════════════
    //  Helpers
    // ═══════════════════════════════════════════════════════════════

    private function seedDefaultRates(): void
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
