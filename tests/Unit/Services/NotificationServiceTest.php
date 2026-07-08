<?php

namespace Tests\Unit\Services;

use App\Models\Box;
use App\Models\Checkout;
use App\Models\Complain;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for NotificationService.
 *
 * Tests each event method creates correct notification with
 * right type, notifiable, and data payload.
 */
class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new NotificationService();
    }

    /**
     * Test customerRegister notifies all admin/owner users.
     */
    public function test_customer_register_notifies_admins(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $owner = User::factory()->create(['role' => 'owner', 'status' => User::STATUS_ACTIVE]);
        $customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_PENDING]);

        $this->service->customerRegister($customer);

        // Admin should receive notification
        $adminNotif = Notification::where('notifiable_id', $admin->id)
            ->where('type', NotificationService::TYPE_CUSTOMER_REGISTER)
            ->first();

        $this->assertNotNull($adminNotif);
        $this->assertEquals('Customer Baru', $adminNotif->data['title']);
        $this->assertEquals($customer->name, $adminNotif->data['customer_name']);
        $this->assertNull($adminNotif->read_at);

        // Owner should also receive notification
        $ownerNotif = Notification::where('notifiable_id', $owner->id)
            ->where('type', NotificationService::TYPE_CUSTOMER_REGISTER)
            ->first();

        $this->assertNotNull($ownerNotif);
    }

    /**
     * Test customerRegister does NOT notify inactive admins.
     */
    public function test_customer_register_skips_inactive_admins(): void
    {
        User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        User::factory()->create(['role' => 'admin', 'status' => User::STATUS_INACTIVE]);

        $customer = User::factory()->create(['role' => 'customer']);

        $this->service->customerRegister($customer);

        $this->assertEquals(1, Notification::where('type', NotificationService::TYPE_CUSTOMER_REGISTER)->count());
    }

    /**
     * Test customerRegister does NOT notify customers.
     */
    public function test_customer_register_does_not_notify_customers(): void
    {
        User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->service->customerRegister($customer);

        $customerNotifs = Notification::where('notifiable_id', $customer->id)
            ->where('type', NotificationService::TYPE_CUSTOMER_REGISTER)
            ->count();

        $this->assertEquals(0, $customerNotifs);
    }

    /**
     * Test accountActivated notifies the customer.
     */
    public function test_account_activated_notifies_customer(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);

        $this->service->accountActivated($customer);

        $notif = Notification::where('notifiable_id', $customer->id)
            ->where('type', NotificationService::TYPE_ACCOUNT_ACTIVATED)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Akun Aktif', $notif->data['title']);
        $this->assertStringContainsString('diaktivasi', $notif->data['message']);
    }

    /**
     * Test boxStatusChanged notifies the box owner.
     */
    public function test_box_status_changed_notifies_owner(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create([
            'customer_id' => $customer->id,
            'tracking_number' => 'TRK-001',
            'status' => Box::STATUS_OPEN,
        ]);

        $this->service->boxStatusChanged($box, Box::STATUS_OPEN, Box::STATUS_SENT_TO_CARGO);

        $notif = Notification::where('notifiable_id', $customer->id)
            ->where('type', NotificationService::TYPE_BOX_STATUS_CHANGED)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Status Box Berubah', $notif->data['title']);
        $this->assertEquals(Box::STATUS_OPEN, $notif->data['old_status']);
        $this->assertEquals(Box::STATUS_SENT_TO_CARGO, $notif->data['new_status']);
        $this->assertEquals('TRK-001', $notif->data['tracking_number']);
    }

    /**
     * Test invoiceGenerated notifies the customer.
     */
    public function test_invoice_generated_notifies_customer(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'invoice_number' => 'INV-2024-001',
            'grand_total' => 5500000,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
        ]);

        $this->service->invoiceGenerated($invoice);

        $notif = Notification::where('notifiable_id', $customer->id)
            ->where('type', NotificationService::TYPE_INVOICE_GENERATED)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Invoice Baru', $notif->data['title']);
        $this->assertEquals('INV-2024-001', $notif->data['invoice_number']);
        $this->assertEquals(5500000, $notif->data['grand_total']);
    }

    /**
     * Test paymentReceived notifies admin/owner.
     */
    public function test_payment_received_notifies_admins(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'invoice_number' => 'INV-2024-001',
            'status' => Invoice::STATUS_WAITING_VERIFICATION,
        ]);

        $this->service->paymentReceived($invoice);

        $notif = Notification::where('notifiable_id', $admin->id)
            ->where('type', NotificationService::TYPE_PAYMENT_RECEIVED)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Pembayaran Masuk', $notif->data['title']);
        $this->assertEquals($customer->name, $notif->data['customer_name']);
    }

    /**
     * Test paymentVerified notifies the customer.
     */
    public function test_payment_verified_notifies_customer(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'invoice_number' => 'INV-2024-001',
            'status' => Invoice::STATUS_VERIFIED,
        ]);

        $this->service->paymentVerified($invoice);

        $notif = Notification::where('notifiable_id', $customer->id)
            ->where('type', NotificationService::TYPE_PAYMENT_VERIFIED)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Pembayaran Terverifikasi', $notif->data['title']);
    }

    /**
     * Test paymentRejected notifies the customer with reason.
     */
    public function test_payment_rejected_notifies_customer_with_reason(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'invoice_number' => 'INV-2024-001',
            'status' => Invoice::STATUS_WAITING_PAYMENT,
        ]);

        $this->service->paymentRejected($invoice, 'Bukti transfer tidak jelas');

        $notif = Notification::where('notifiable_id', $customer->id)
            ->where('type', NotificationService::TYPE_PAYMENT_REJECTED)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Pembayaran Ditolak', $notif->data['title']);
        $this->assertEquals('Bukti transfer tidak jelas', $notif->data['reason']);
    }

    /**
     * Test newComplaint notifies admin/owner.
     */
    public function test_new_complaint_notifies_admins(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $complaint = Complain::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'type' => 'Barang Rusak',
            'status' => Complain::STATUS_OPEN,
        ]);

        $this->service->newComplaint($complaint);

        $notif = Notification::where('notifiable_id', $admin->id)
            ->where('type', NotificationService::TYPE_NEW_COMPLAINT)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Komplain Baru', $notif->data['title']);
        $this->assertEquals($customer->name, $notif->data['customer_name']);
        $this->assertEquals('Barang Rusak', $notif->data['complaint_type']);
    }

    /**
     * Test complaintUpdated notifies the customer.
     */
    public function test_complaint_updated_notifies_customer(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $complaint = Complain::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'status' => Complain::STATUS_PROCESSING,
        ]);

        $this->service->complaintUpdated($complaint, Complain::STATUS_OPEN, Complain::STATUS_PROCESSING);

        $notif = Notification::where('notifiable_id', $customer->id)
            ->where('type', NotificationService::TYPE_COMPLAINT_UPDATED)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Komplain Diperbarui', $notif->data['title']);
        $this->assertEquals(Complain::STATUS_OPEN, $notif->data['old_status']);
        $this->assertEquals(Complain::STATUS_PROCESSING, $notif->data['new_status']);
    }

    /**
     * Test checkoutProcessed notifies the customer.
     */
    public function test_checkout_processed_notifies_customer(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'invoice_number' => 'INV-2024-001',
            'status' => Invoice::STATUS_VERIFIED,
        ]);
        $checkout = Checkout::factory()->create([
            'customer_id' => $customer->id,
            'invoice_id' => $invoice->id,
            'status' => Checkout::STATUS_ON_PROCESS,
        ]);

        $this->service->checkoutProcessed($checkout);

        $notif = Notification::where('notifiable_id', $customer->id)
            ->where('type', NotificationService::TYPE_CHECKOUT_PROCESSED)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Checkout Diproses', $notif->data['title']);
        $this->assertEquals('INV-2024-001', $notif->data['invoice_number']);
    }

    /**
     * Test getValidTypes returns all 10 types.
     */
    public function test_get_valid_types(): void
    {
        $types = NotificationService::getValidTypes();

        $this->assertCount(10, $types);
        $this->assertContains(NotificationService::TYPE_CUSTOMER_REGISTER, $types);
        $this->assertContains(NotificationService::TYPE_ACCOUNT_ACTIVATED, $types);
        $this->assertContains(NotificationService::TYPE_BOX_STATUS_CHANGED, $types);
        $this->assertContains(NotificationService::TYPE_INVOICE_GENERATED, $types);
        $this->assertContains(NotificationService::TYPE_PAYMENT_RECEIVED, $types);
        $this->assertContains(NotificationService::TYPE_PAYMENT_VERIFIED, $types);
        $this->assertContains(NotificationService::TYPE_PAYMENT_REJECTED, $types);
        $this->assertContains(NotificationService::TYPE_CHECKOUT_PROCESSED, $types);
        $this->assertContains(NotificationService::TYPE_NEW_COMPLAINT, $types);
        $this->assertContains(NotificationService::TYPE_COMPLAINT_UPDATED, $types);
    }

    /**
     * Test notification data contains required keys.
     */
    public function test_notification_data_has_required_keys(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->service->accountActivated($customer);

        $notif = Notification::where('notifiable_id', $customer->id)->first();

        $this->assertArrayHasKey('title', $notif->data);
        $this->assertArrayHasKey('message', $notif->data);
        $this->assertArrayHasKey('link', $notif->data);
    }

    /**
     * Test notification is created as unread by default.
     */
    public function test_notification_created_as_unread(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->service->accountActivated($customer);

        $notif = Notification::where('notifiable_id', $customer->id)->first();

        $this->assertNull($notif->read_at);
        $this->assertFalse($notif->isRead());
    }

    /**
     * Test customerRegister creates one notification per active admin/owner.
     */
    public function test_customer_register_creates_one_per_admin(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $admin2 = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $owner = User::factory()->create(['role' => 'owner', 'status' => User::STATUS_ACTIVE]);

        $customer = User::factory()->create(['role' => 'customer']);

        $this->service->customerRegister($customer);

        $this->assertEquals(3, Notification::where('type', NotificationService::TYPE_CUSTOMER_REGISTER)->count());

        $this->assertNotNull(Notification::where('notifiable_id', $admin1->id)->first());
        $this->assertNotNull(Notification::where('notifiable_id', $admin2->id)->first());
        $this->assertNotNull(Notification::where('notifiable_id', $owner->id)->first());
    }

    /**
     * Test customerRegister handles zero active admins gracefully.
     */
    public function test_customer_register_with_no_active_admins(): void
    {
        // Only inactive admins exist
        User::factory()->create(['role' => 'admin', 'status' => User::STATUS_INACTIVE]);

        $customer = User::factory()->create(['role' => 'customer']);

        // Should not throw — notifyAdmins returns null when no admins
        $result = $this->service->customerRegister($customer);

        $this->assertEquals(0, Notification::where('type', NotificationService::TYPE_CUSTOMER_REGISTER)->count());
        $this->assertNull($result);
    }

    /**
     * Test paymentReceived also notifies owner (superset of admin).
     */
    public function test_payment_received_notifies_owner_too(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $owner = User::factory()->create(['role' => 'owner', 'status' => User::STATUS_ACTIVE]);
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'invoice_number' => 'INV-2024-001',
            'status' => Invoice::STATUS_WAITING_VERIFICATION,
        ]);

        $this->service->paymentReceived($invoice);

        $this->assertEquals(2, Notification::where('type', NotificationService::TYPE_PAYMENT_RECEIVED)->count());
        $this->assertNotNull(Notification::where('notifiable_id', $admin->id)->first());
        $this->assertNotNull(Notification::where('notifiable_id', $owner->id)->first());
    }

    /**
     * Test newComplaint notifies both admin and owner.
     */
    public function test_new_complaint_notifies_all_admins_and_owners(): void
    {
        User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        User::factory()->create(['role' => 'owner', 'status' => User::STATUS_ACTIVE]);
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $complaint = Complain::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'type' => 'Barang Hilang',
            'status' => Complain::STATUS_OPEN,
        ]);

        $this->service->newComplaint($complaint);

        $this->assertEquals(3, Notification::where('type', NotificationService::TYPE_NEW_COMPLAINT)->count());
    }
}
