<?php

namespace Tests\Feature\Notification;

use App\Livewire\Customer\KomplainIndex;
use App\Models\Box;
use App\Models\Checkout;
use App\Models\Complain;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationTriggerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PRD §4.2: Register triggers notification to admin
     */
    public function test_register_triggers_admin_notification(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_PENDING]);

        $service = new NotificationService();
        $service->customerRegister($customer);

        $notif = Notification::where('notifiable_id', $admin->id)
            ->where('type', NotificationService::TYPE_CUSTOMER_REGISTER)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Customer Baru', $notif->data['title']);
        $this->assertNull($notif->read_at);
    }

    /**
     * PRD §4.5: Payment triggers notification to admin
     */
    public function test_payment_triggers_admin_notification(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'status' => Invoice::STATUS_WAITING_VERIFICATION,
        ]);

        $service = new NotificationService();
        $service->paymentReceived($invoice);

        $notif = Notification::where('notifiable_id', $admin->id)
            ->where('type', NotificationService::TYPE_PAYMENT_RECEIVED)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Pembayaran Masuk', $notif->data['title']);
    }

    /**
     * PRD §4.11: Verification triggers notification to customer
     */
    public function test_verification_triggers_customer_notification(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'status' => Invoice::STATUS_VERIFIED,
        ]);

        $service = new NotificationService();
        $service->paymentVerified($invoice);

        $notif = Notification::where('notifiable_id', $customer->id)
            ->where('type', NotificationService::TYPE_PAYMENT_VERIFIED)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Pembayaran Terverifikasi', $notif->data['title']);
    }

    /**
     * PRD §4.11: Rejection triggers notification to customer with reason
     */
    public function test_rejection_triggers_customer_notification_with_reason(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'status' => Invoice::STATUS_WAITING_PAYMENT,
        ]);

        $service = new NotificationService();
        $service->paymentRejected($invoice, 'Bukti transfer blur');

        $notif = Notification::where('notifiable_id', $customer->id)
            ->where('type', NotificationService::TYPE_PAYMENT_REJECTED)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Pembayaran Ditolak', $notif->data['title']);
        $this->assertEquals('Bukti transfer blur', $notif->data['reason']);
    }

    /**
     * PRD §4.7: New complaint triggers notification to admin
     */
    public function test_new_complaint_triggers_admin_notification(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $complaint = Complain::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'type' => 'Barang Rusak',
        ]);

        $service = new NotificationService();
        $service->newComplaint($complaint);

        $notif = Notification::where('notifiable_id', $admin->id)
            ->where('type', NotificationService::TYPE_NEW_COMPLAINT)
            ->first();

        $this->assertNotNull($notif);
        $this->assertEquals('Komplain Baru', $notif->data['title']);
    }
}
