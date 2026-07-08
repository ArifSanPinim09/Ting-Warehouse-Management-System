<?php

namespace Tests\Feature\Customer;

use App\Livewire\Customer\KomplainIndex;
use App\Models\Complain;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KomplainTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
        // Need an admin for notification target
        User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
    }

    /**
     * PRD §4.7: Customer can submit complaint
     */
    public function test_customer_can_submit_complaint(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(KomplainIndex::class)
            ->call('openForm')
            ->set('type', 'Barang Rusak')
            ->set('resolution', 'refund')
            ->set('invoiceNumber', 'INV-TEST-001')
            ->set('description', 'Barang saya rusak parah saat diterima, kemasan sudah hancur')
            ->call('submit');

        $this->assertDatabaseHas('complains', [
            'customer_id' => $this->customer->id,
            'type' => 'Barang Rusak',
            'resolution' => 'refund',
            'status' => Complain::STATUS_OPEN,
        ]);
    }

    /**
     * PRD §4.7: Complaint triggers notification to admin
     */
    public function test_notification_sent_to_admin(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(KomplainIndex::class)
            ->call('openForm')
            ->set('type', 'Barang Rusak')
            ->set('resolution', 'refund')
            ->set('description', 'Barang saya rusak parah saat diterima, kemasan sudah hancur')
            ->call('submit');

        $complaint = Complain::first();
        $this->assertNotNull($complaint);

        $notif = Notification::where('type', NotificationService::TYPE_NEW_COMPLAINT)->first();
        $this->assertNotNull($notif);
        $this->assertEquals('Komplain Baru', $notif->data['title']);
    }
}
